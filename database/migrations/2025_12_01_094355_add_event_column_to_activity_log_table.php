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
            ->table(Config::string('activitylog.table_name'), function (Blueprint $table): void {
                $table->string('event')->nullable()->after('subject_type');
            });
    }

    public function down(): void
    {
        $connection = Config::get('activitylog.database_connection');

        Schema::connection(is_string($connection) ? $connection : null)
            ->table(Config::string('activitylog.table_name'), function (Blueprint $table): void {
                $table->dropColumn('event');
            });
    }
};
