<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateSqliteToMysql extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:migrate-sqlite-to-mysql';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate data from SQLite to MySQL';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting migration from SQLite to MySQL...');

        // 1. Configure SQLite connection on the fly
        config(['database.connections.sqlite_old' => [
            'driver' => 'sqlite',
            'database' => database_path('database.sqlite'),
            'prefix' => '',
            'foreign_key_constraints' => false,
        ]]);

        // Check if SQLite file exists
        if (!file_exists(database_path('database.sqlite'))) {
            $this->error('Access database.sqlite file not found!');
            return 1;
        }

        // 2. Fresh Migration on MySQL (Destination)
        if ($this->confirm('This will wipe the configured MySQL database. Continue?', true)) {
            $this->call('migrate:fresh', ['--force' => true]);
        } else {
            $this->info('Aborted.');
            return 0;
        }

        // 3. Disable Foreign Keys
        DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0;');

        // 4. Tables to copy (Order matters less with FK checks off, but good to be logical)
        $tables = [
            'users',
            'wilayahs',
            'areas',
            'roles',
            'permissions',
            'model_has_roles',
            'role_has_permissions',
            'model_has_permissions',
            'cm_data',
            'coins_data',
            'activity_log',
        ];

        foreach ($tables as $table) {
            $this->info("Copying table: {$table}");

            try {
                // Fetch from SQLite
                $rows = DB::connection('sqlite_old')->table($table)->get();

                if ($rows->isEmpty()) {
                    $this->warn(" - No data in {$table}");
                    continue;
                }

                // Insert chunked into MySQL
                $data = $rows->map(function ($row) {
                    return (array) $row;
                })->toArray();

                foreach (array_chunk($data, 100) as $chunk) {
                    DB::connection('mysql')->table($table)->insert($chunk);
                }

                $this->info(" - Copied " . count($rows) . " rows.");

            } catch (\Exception $e) {
                $this->error("Failed to copy {$table}: " . $e->getMessage());
            }
        }

        // 5. Re-enable Foreign Keys
        DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info('Migration completed successfully!');
    }
}
