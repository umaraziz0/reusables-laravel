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

        Schema::connection($this->connection())->create($prefix . 'districts', function (Blueprint $table) use ($prefix): void {
            $table->bigIncrements('id');
            $table->char('code', 7)->unique();
            $table->char('city_code', 4)->index();
            $table->string('name', 255);
            $table->text('meta')->nullable();
            $table->timestamps();

            $table->foreign('city_code')
                ->references('code')
                ->on($prefix . 'cities')
                ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop(Config::string('laravolt.indonesia.table_prefix') . 'districts');
    }
};
