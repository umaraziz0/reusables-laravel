<?php

declare(strict_types=1);

use App\Database\Expressions\UserSearchVector;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\PostgresGrammar;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Fluent;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            Schema::ensureExtensionExists('citext');
            Schema::ensureExtensionExists('pg_trgm');

            if (! PostgresGrammar::hasMacro('typeCitext')) {
                PostgresGrammar::macro('typeCitext', fn (Fluent $column): string => 'citext');
            }
        }

        Schema::create('users', function (Blueprint $table) use ($driver): void {
            $table->id();

            if ($driver === 'pgsql') {
                $table->addColumn('citext', 'name');
                $table->addColumn('citext', 'email')->unique();
            } else {
                $table->string('name');
                $table->string('email')->unique();
            }

            $table->string('country_code', 6)->nullable();
            $table->string('phone_number', 15)->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            if ($driver === 'pgsql') {
                $table->rawIndex(
                    UserSearchVector::TRGM_INDEX,
                    'users_search_trgm_idx',
                )->algorithm('gin');
            }
        });

        Schema::create('password_reset_tokens', function (Blueprint $table): void {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
