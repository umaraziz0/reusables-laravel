<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $connection = Config::get('activitylog.database_connection');

        Schema::connection(is_string($connection) ? $connection : null)
            ->create(Config::string('activitylog.table_name'), function (Blueprint $table): void {
                $table->bigIncrements('id');
                $table->string('log_name')->nullable();
                $table->text('description');
                $table->nullableMorphs('subject', 'subject');
                $table->nullableMorphs('causer', 'causer');
                $table->json('properties')->nullable();
                $table->timestamps();
                $table->index('log_name');
            });
    }

    public function down(): void
    {
        $connection = Config::get('activitylog.database_connection');

        Schema::connection(is_string($connection) ? $connection : null)
            ->dropIfExists(Config::string('activitylog.table_name'));
    }
};
