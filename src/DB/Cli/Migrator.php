<?php

namespace Simflex\Core\DB\Cli;

use Simflex\Core\System\Container;
use Exception;
use ReflectionClass;
use Simflex\Core\Console\Command;
use Simflex\Core\Console\Help;
use Simflex\Core\Console\ConsoleBase;
use Simflex\Core\DB\DB;
use Simflex\Core\DB\Migration as MigrationInterface;
use Simflex\Core\DB\Schema;
use Simflex\Core\Logger\Log;
use Simflex\Core\Models\Migration as Migration;

class Migrator extends ConsoleBase
{
    /**
     * Миграция, создающая таблицу migration. Не откатывается: без неё
     * migrate/up не сможет запомнить выполненные миграции.
     */
    protected const BOOTSTRAP_MIGRATION = '00_migration';

    protected array $migrationFiles = [];

    public function __construct()
    {
        // собираем файлы миграций: пронумерованные, в естественном порядке (9_x раньше 10_x)
        $files = scandir('database/migrations');
        natsort($files);
        foreach ($files as $file) {
            $path = pathinfo($file);
            if (($path['extension'] ?? '') === 'php') {
                $this->migrationFiles[$path['filename']] = [
                    'file' => 'database/migrations/' . $file,
                    'name' => $path['filename'],
                ];
            }
        }
    }

    /**
     * Новые миграции
     * @param array $list Список выполненных миграций
     * @return array Список новых миграций
     */
    protected function getNewMigrations(array $list): array
    {
        $files = [];
        foreach ($this->migrationFiles as $file) {
            $files[] = $file['name'];
        }

        if (empty($list)) {
            return $files;
        }

        $migrations = [];
        foreach ($list as $item) {
            $migrations[] = $item['file'];
        }

        return array_diff($files, $migrations);
    }

    /**
     * Пытается получить объект миграции
     * @param string $name Имя миграции
     */
    protected function getMigrationObject(string $name): ?MigrationInterface
    {
        try {
            $class = include $this->migrationFiles[$name]['file'];

            $reflection = new ReflectionClass($class);
            if (!$reflection->implementsInterface(MigrationInterface::class)) {
                Log::error('Migration {name} should implement Migration interface', ['name' => $name]);
                return null;
            }

            return $class;
        } catch (\Throwable $ex) {
            Log::critical(
                'Failed to load migration {name} ({exception})',
                ['name' => $name, 'exception' => $ex->getMessage()]
            );
            return null;
        }
    }

    /**
     * Применяет миграции
     * @param string $steps Количество шагов
     * @throws Exception
     */
    #[Command('Up migrations')]
    public function up(
        #[Help('"all" = run all migrations. Otherwise, amount of steps to go')] string $steps = 'all'
    ): void {
        // ищем ещё не выполненные миграции
        // на пустой базе таблицы migration ещё нет: её создаст миграция 00_migration
        $list = DB::hasTable('migration')
            ? Migration::findAdv()->asArray()->all()
            : [];

        $migrations = $this->getNewMigrations($list);
        if (empty($migrations)) {
            Log::notice('No new migrations');
            return;
        }

        // выполняем миграции
        $counter = 0;
        foreach ($migrations as $migration) {
            if ($steps !== 'all') {
                if ($counter++ >= (int)$steps) {
                    break;
                }
            }

            // выполняем и запоминаем миграцию
            $class = $this->getMigrationObject($migration);
            if (!$class) {
                Log::error('Migration {migration} cannot be loaded', ['migration' => $migration]);
                return;
            }

            $schema = new Schema();
            $class->up($schema);
            if (!$schema->commit()) {
                Log::critical('Failed to up migration {migration}', ['migration' => $migration]);
                Log::critical(
                    'Query: {query}, error: {error}',
                    ['query' => DB::getLastQuery(), 'error' => DB::error()]
                );

                $schema->rollback();
                return;
            }

            // принудительно перезагружаем, чтобы обновить существующие таблицы
            $schema->reload();

            $dbMigration = new Migration();
            if (!$dbMigration->save(['file' => $migration])) {
                $class->down($schema);
                if (!$schema->commit()) {
                    Log::emergency(
                        'Failed to down migration {migration} after DB failure',
                        ['migration' => $migration]
                    );
                    return;
                }

                Log::critical('Failed to remember migration {migration}', ['migration' => $migration]);
                return;
            }

            Log::notice('Migration {migration} is up', ['migration' => $migration]);
        }
    }

    /**
     * Откатывает миграции
     * @throws Exception
     */
    #[Command('Down migrations')]
    public function down(
        #[Help('"all" = run all migrations. Otherwise, amount of steps to go')] string $steps = '1'
    ): void {
        $list = Migration::findAdv()
            ->where("`file` <> '" . static::BOOTSTRAP_MIGRATION . "'")
            ->orderBy('`id` DESC');

        if ($steps !== 'all') {
            $list->limit((int)$steps);
        }

        $list = $list->all();

        /** @var Migration $migration */
        foreach ($list as $migration) {
            $class = $this->getMigrationObject($migration->file);
            if (!$class) {
                return;
            }

            $schema = new Schema();
            $class->down($schema);
            if (!$schema->commit()) {
                Log::critical('Failed to down migration {migration}', ['migration' => $migration->file]);
                Log::critical(
                    'Query: {query}, error: {error}',
                    ['query' => DB::getLastQuery(), 'error' => DB::error()]
                );
                return;
            }

            if (!$migration->delete()) {
                $class->up($schema);
                if (!$schema->commit()) {
                    $schema->rollback();

                    Log::emergency('Failed to up migration {name} after DB failure', ['name' => $migration->file]);
                    return;
                }

                Log::critical('Failed to forget migration {migration}', ['migration' => $migration->file]);
                return;
            }

            Log::notice('Migration {migration} is down', ['migration' => $migration->file]);
        }
    }

    /**
     * Перевыполняет миграции
     * @throws Exception
     */
    #[Command('Refresh migrations')]
    public function refresh(
        #[Help('"all" = run all migrations. Otherwise, amount of steps to go')] string $steps = '1'
    ): void {
        $this->down($steps);
        $this->up($steps);
    }

    /**
     * Создаёт новую миграцию
     */
    #[Command('Create new migration')]
    public function create(#[Help('Migration name')] string $name): void
    {
        $last = 0;
        foreach ($this->migrationFiles as $file) {
            $last = max($last, (int)$file['name']);
        }
        $fileName = sprintf('%02d', $last + 1) . '_' . $name . '.php';
        copy(__DIR__ . '/../migration_template.php', Container::getPaths()->getRoot('database/migrations/' . $fileName));

        Log::notice('Created new migration {name}', ['name' => $fileName]);
    }
}
