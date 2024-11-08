<?php

namespace Simflex\Core\DB\Cli;

use Exception;
use ReflectionClass;
use Simflex\Core\Console\Command;
use Simflex\Core\Console\Help;
use Simflex\Core\ConsoleBase;
use Simflex\Core\DB;
use Simflex\Core\DB\Migration as MigrationInterface;
use Simflex\Core\DB\Schema;
use Simflex\Core\Log;
use Simflex\Core\Models\Migration as Migration;

class Migrator extends ConsoleBase
{
    protected array $migrationFiles = [];

    public function __construct()
    {
        // populate migration files
        foreach (scandir('database/migrations') as $file) {
            $path = pathinfo($file);
            if (($path['extension'] ?? '') == 'php') {
                $this->migrationFiles[$path['filename']] = [
                    'file' => 'database/migrations/' . $file,
                    'name' => $path['filename']
                ];
            }
        }
    }

    /**
     * Get new migrations
     * @param array $list List of completed migrations
     * @return array List of new migrations
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
     * Attempts to get migration object
     * @param string $name Migration name
     * @return MigrationInterface|null
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
     * Ups the migrations
     * @param string $steps Amount of steps to go
     * @return void
     * @throws Exception
     */
    #[Command('Up migrations')]
    public function up(
        #[Help('"all" = run all migrations. Otherwise, amount of steps to go')] string $steps = 'all'
    ): void {
        // find migrations that were not processed yet
        $list = Migration::findAdv()
            ->asArray()
            ->all();

        $migrations = $this->getNewMigrations($list);
        if (empty($migrations)) {
            Log::notice('No new migrations');
            return;
        }

        // run migrations
        $counter = 0;
        foreach ($migrations as $migration) {
            if ($steps != 'all') {
                if ($counter++ >= (int)$steps) {
                    break;
                }
            }

            // run and remember the migration
            $class = $this->getMigrationObject($migration);
            if (!$class) {
                Log::error('Migration {migration} cannot be loaded', ['migration' => $migration]);
                return;
            }

            $schema = new Schema();
            $class->up($schema);
            if (!$schema->commit()) {
                Log::critical('Failed to up migration {migration}', ['migration' => $migration]);
                Log::critical('Query: {query}, error: {error}', ['query' => DB::getLastQuery(), 'error' => DB::error()]
                );

                $schema->rollback();
                return;
            }

            // force reload to update existing tables
            $schema->reload();

            $dbMigration = new Migration();
            if (!$dbMigration->insert(['file' => $migration])) {
                $class->down($schema);
                if (!$schema->commit()) {
                    Log::emergency('Failed to down migration {migration} after DB failure', ['migration' => $migration]
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
     * Downs the migrations
     * @param string $steps
     * @return void
     * @throws Exception
     */
    #[Command('Down migrations')]
    public function down(
        #[Help('"all" = run all migrations. Otherwise, amount of steps to go')] string $steps = '1'
    ): void {
        $list = Migration::findAdv()
            ->orderBy('`id` DESC');

        if ($steps != 'all') {
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
                Log::critical('Query: {query}, error: {error}', ['query' => DB::getLastQuery(), 'error' => DB::error()]
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
     * Refreshes the migrations
     * @param string $steps
     * @return void
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
     * Creates a new migration
     * @param string $name
     * @return void
     */
    #[Command('Create new migration')]
    public function create(#[Help('Migration name')] string $name): void
    {
        $fileName = date('Y_m_d') . '_' . $name . '.php';
        copy(__DIR__ . '/migration_template.php', SF_ROOT_PATH . '/database/migrations/' . $fileName);

        Log::notice('Created new migration {name}', ['name' => $fileName]);
    }
}