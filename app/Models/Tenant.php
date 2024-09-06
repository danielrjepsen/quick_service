<?php

namespace App\Models;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;
use Spatie\Multitenancy\Models\Tenant as OrgTenantClass;

class Tenant extends OrgTenantClass
{
    use UsesLandlordConnection;
    public static function booted()
    {
        static::created(fn(Tenant $model) => $model->createDatabase());
    }

    public function createDatabase()
    {
        $schemaName = $this::getDatabaseName() ?: config("database.connections.tenant.database");
        $charset = config("database.connections.tenant.charset",'utf8mb4');
        $collation = config("database.connections.tenant.collation",'utf8mb4_unicode_ci');

        config(["database.connections.tenant.database" => null]);

        DB::reconnect('tenant');

        $query = "CREATE DATABASE IF NOT EXISTS $schemaName CHARACTER SET $charset COLLATE $collation;";

        DB::statement($query);

        config(["database.connections.tenant.database" => $schemaName]);

        DB::reconnect('tenant');

        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--force' => true
        ]);
    }
}
