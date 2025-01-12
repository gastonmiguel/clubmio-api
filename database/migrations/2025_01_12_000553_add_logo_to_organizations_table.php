<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            Schema::table('organizations', function (Blueprint $table) {
                $table->string('logo')->nullable()->after('name'); // Añade el campo 'logo' como un string (puede ser nulo si no se proporciona un valor)
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn('logo');
        });
    }
};
