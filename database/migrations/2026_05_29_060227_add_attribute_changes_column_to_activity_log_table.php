<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $connection = Config::get('activitylog.database_connection');

        // Example upgrade migration
        Schema::connection(is_string($connection) ? $connection : null)
            ->table(Config::string('activitylog.table_name'), function (Blueprint $table): void {
                $table->json('attribute_changes')->nullable()->after('causer_id');
                $table->dropColumn('batch_uuid');
            });

        // Then migrate existing data: move 'attributes' and 'old' from properties to attribute_changes
        DB::table('activity_log')
            ->where(function ($query): void {
                $query->whereNotNull('properties->attributes')
                    ->orWhereNotNull('properties->old');
            })
            ->eachById(function ($row): void {
                $properties = json_decode((string) $row->properties, true);
                $changes    = array_intersect_key($properties, array_flip(['attributes', 'old']));
                $remaining  = array_diff_key($properties, array_flip(['attributes', 'old']));

                DB::table('activity_log')->where('id', $row->id)->update([
                    'attribute_changes' => $changes === [] ? null : json_encode($changes),
                    'properties'        => $remaining === [] ? null : json_encode($remaining),
                ]);
            });
    }
};
