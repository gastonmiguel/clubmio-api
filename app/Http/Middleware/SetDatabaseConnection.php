<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SetDatabaseConnection
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        $dbConnection = env('DB_DATABASE');

        if ($user) {
            $organization = $user->organization;
            $dbConnection = $organization->database_name;

            if (!config('database.connections.' . $dbConnection)) {
                throw new \Exception('Database connection not found');
            }
        }

        config(['database.default' => $dbConnection]);
        DB::purge($dbConnection);

        return $next($request);
    }
}


