<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected function connection(): string
    {
        $connection = Config::get('laravolt.indonesia.database.connection');

        return is_string($connection) ? $connection : Config::string('database.default');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $prefix = Config::string('laravolt.indonesia.table_prefix');

        Schema::connection($this->connection())->create($prefix . 'provinces', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->char('code', 2)->unique();
            $table->string('name', 255);
            $table->text('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop(Config::string('laravolt.indonesia.table_prefix') . 'provinces');
    }
};
