<?php

namespace Simflex\Core\DB;

use ReflectionClass;
use Simflex\Core\Alert\Console\Alert;
use Simflex\Core\ConsoleBase;
use Simflex\Core\DB;
use Simflex\Core\DB\Migration as MigrationInterface;
use Simflex\Core\Models\Migration as Migration;
use Simflex\Core\DB\Schema;

class Migrator extends ConsoleBase
{
    protected $migrationFiles = [];

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

    protected function getMigrationObject(string $name): ?MigrationInterface
    {
        try {
            $class = include $this->migrationFiles[$name]['file'];

            $reflection = new ReflectionClass($class);
            if (!$reflection->implementsInterface(MigrationInterface::class)) {
                Alert::error('Migration ' . $name . ' should implement Migration interface');
                return null;
            }

            return $class;
        } catch (\Throwable $ex) {
            Alert::error('FATAL: ' . $ex->getMessage());
            return null;
        }
    }

    /**
     * @param int|string $steps
     * @throws \Exception
     */
    public function up($steps = 'all')
    {
        // find migrations that were not processed yet
        $list = Migration::findAdv()
            ->asArray()
            ->all();

        $migrations = $this->getNewMigrations($list);
        if (empty($migrations)) {
            Alert::success('No new migrations');
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
                return;
            }

            $schema = new Schema();
            $class->up($schema);
            if (!$schema->commit()) {
                Alert::error('Failed to up migration ' . $migration);
                Alert::text(DB::error());

                $schema->rollback();
                return;
            }

            // force reload to update existing tables
            $schema->reload();

            $dbMigration = new Migration();
            if (!$dbMigration->insert(['file' => $migration])) {
                $class->down($schema);
                if (!$schema->commit()) {
                    Alert::error('FATAL: failed to down migration ' . $migration . ' after DB failure');
                    Alert::text(DB::error());
                    return;
                }

                Alert::error('Failed to remember migration ' . $migration);
                return;
            }

            Alert::text('Migration ' . $migration . ' is up!');
        }

        Alert::success('Up is done!');
    }

    /**
     * @param int|string $steps
     * @throws \Exception
     */
    public function down($steps = 1)
    {
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
                Alert::error('Failed to down migration ' . $migration['file']);
                Alert::text(DB::error());
                return;
            }

            if (!$migration->delete()) {
                $class->up($schema);
                if (!$schema->commit()) {
                    $dbError = DB::error();
                    $schema->rollback();

                    Alert::error('FATAL: Failed to up migration ' . $migration['file'] . 'after DB failure');
                    Alert::text($dbError);
                    return;
                }

                Alert::error('Failed to forget migration ' . $migration['file']);
                return;
            }

            Alert::text('Migration ' . $migration['file'] . ' is down!');
        }

        Alert::success('Down is done!');
    }

    /**
     * @param int|string $steps
     * @throws \Exception
     */
    public function refresh($steps = 1)
    {
        $this->down($steps);
        $this->up($steps);
    }

    public function create($name)
    {
        $fileName = date('Y_m_d') . '_' . $name . '.php';
        copy(__DIR__ . '/migration_template.php', $_SERVER['DOCUMENT_ROOT'] . '/database/migrations/' . $fileName);

        Alert::success('Created new migration ' . $fileName);
    }
}