<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class MigrateAllTenants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:tenants';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations on all tenant databases';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $organizations = DB::table('organizations')->get();
        foreach ($organizations as $organization) {
            $dbName = $organization->database_name;
            $this->info("Migrating database: {$dbName}");

            Config::set('database.default', $dbName);
            DB::purge($dbName);

            try {
                $this->call('migrate', ['--force' => true]);
            } catch (\Exception $e) {
                $this->error("Failed to migrate database {$dbName}: " . $e->getMessage());
            }
        }

        // Restaurar la conexión por defecto
        Config::set('database.default', env('DB_CONNECTION', 'clubmio'));
        DB::purge(env('DB_CONNECTION', 'clubmio'));

        $this->info('Migrations completed for all tenant databases.');
        return 0;
    }
}

