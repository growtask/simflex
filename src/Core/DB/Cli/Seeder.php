<?php

namespace Simflex\Core\DB\Cli;

use Exception;
use ReflectionClass;
use Simflex\Core\Console\Command;
use Simflex\Core\Console\Help;
use Simflex\Core\ConsoleBase;
use Simflex\Core\DB\Attrib\Manual;
use Simflex\Core\DB\Seeder as SeederInterface;
use Simflex\Core\Log;
use Simflex\Core\ModelBase;
use Simflex\Core\Models\Seeder as SeederModel;

class Seeder extends ConsoleBase
{
    protected array $seederFiles = [];

    public function __construct()
    {
        // populate seeder files
        foreach (scandir('database/seeders') as $file) {
            $path = pathinfo($file);
            if (($path['extension'] ?? '') == 'php') {
                $this->seederFiles[$path['filename']] = [
                    'file' => 'database/seeders/' . $file,
                    'name' => $path['filename']
                ];
            }
        }
    }

    /**
     * Get new seeders
     * @param array $list List of completed seeders
     * @return array List of new seeders
     */
    protected function getNewSeeders(array $list): array
    {
        $files = [];
        foreach ($this->seederFiles as $file) {
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
     * @return SeederInterface|null
     */
    protected function getSeederObject(string $name): ?SeederInterface
    {
        try {
            $class = include $this->seederFiles[$name]['file'];

            $reflection = new ReflectionClass($class);
            if (!$reflection->implementsInterface(SeederInterface::class)) {
                Log::error('Seeder {name} should implement Seeder interface', ['name' => $name]);
                return null;
            }

            return $class;
        } catch (\Throwable $ex) {
            Log::critical(
                'Failed to load seeder {name} ({exception})',
                ['name' => $name, 'exception' => $ex->getMessage()]
            );
            return null;
        }
    }

    /**
     * Executes seeders
     * @param string $only Specific seeder to run
     * @return void
     * @throws Exception
     */
    #[Command('Execute seeders')]
    public function run(#[Help('run only a specific seeder. Required for manual-only seeders')] string $only = ''): void
    {
        $list = SeederModel::all();
        $seeders = $this->getNewSeeders($list);
        if (empty($seeders)) {
            Log::notice('No new seeders');
            return;
        }

        if ($only) {
            if (!in_array($only, $seeders)) {
                Log::error('Seeder {seeder} not found in the list (does not exist or already ran)', ['seeder' => $only]
                );
                return;
            }

            $seeders = [$only];
        }

        foreach ($seeders as $seeder) {
            $class = $this->getSeederObject($seeder);
            if (!$class) {
                Log::error('Skipped {seeder} - load failed', ['seeder' => $seeder]);
                continue;
            }

            $ref = new ReflectionClass($class);
            if ($ref->getAttributes(Manual::class) && !$only) {
                Log::warning('Skipped {seeder} - can only be ran manually (--only={seeder})', ['seeder' => $seeder]);
                continue;
            }

            // reset stats
            ModelBase::$stats = ['inserted' => 0, 'updated' => 0, 'deleted' => 0];

            try {
                $class->seed();
            } catch (Exception $ex) {
                Log::error('Failed to run {seeder} - {what}', ['seeder' => $seeder, 'what' => $ex->getMessage()]);
                Log::error('Data may be corrupted, please revise the table(s)');
                continue;
            }

            if (!SeederModel::insertStatic(['file' => $seeder])) {
                Log::error('Failed to remember {seeder}, be careful running it again!', ['seeder' => $seeder]);
                continue;
            }

            $s = ModelBase::$stats;
            if (!$s['inserted'] && !$s['updated'] && !$s['deleted']) {
                Log::warning('Nothing changed after running {seeder}, is this intended?', ['seeder' => $seeder]);
                continue;
            }

            Log::notice(
                'Ran {seeder} - {inserted} inserted, {updated} updated, {deleted} deleted',
                array_merge(['seeder' => $seeder], $s)
            );
        }
    }
}