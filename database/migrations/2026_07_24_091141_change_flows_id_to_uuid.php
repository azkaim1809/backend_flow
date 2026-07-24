<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 0. Aktifkan extension buat generate uuid
        DB::statement('CREATE EXTENSION IF NOT EXISTS "pgcrypto"');

        // 1. Tambah kolom uuid baru di flows
        DB::statement('ALTER TABLE flows ADD COLUMN new_id uuid DEFAULT gen_random_uuid()');

        // 2. Tambah kolom uuid baru di 3 tabel anak (nullable dulu)
        DB::statement('ALTER TABLE flow_nodes ADD COLUMN new_flow_id uuid');
        DB::statement('ALTER TABLE flow_connections ADD COLUMN new_flow_id uuid');
        DB::statement('ALTER TABLE simulations ADD COLUMN new_flow_id uuid');

        // 3. Isi new_flow_id berdasarkan mapping id lama -> uuid baru
        DB::statement('
            UPDATE flow_nodes fn
            SET new_flow_id = f.new_id
            FROM flows f
            WHERE fn.flow_id = f.id
        ');
        DB::statement('
            UPDATE flow_connections fc
            SET new_flow_id = f.new_id
            FROM flows f
            WHERE fc.flow_id = f.id
        ');
        DB::statement('
            UPDATE simulations s
            SET new_flow_id = f.new_id
            FROM flows f
            WHERE s.flow_id = f.id
        ');

        // 4. Drop FK constraint lama
        DB::statement('ALTER TABLE flow_connections DROP CONSTRAINT fk_connection_flow');
        DB::statement('ALTER TABLE flow_nodes DROP CONSTRAINT fk_flow_nodes_flow');
        DB::statement('ALTER TABLE simulations DROP CONSTRAINT fk_simulation_flow');

        // 5. Drop kolom flow_id lama, rename new_flow_id -> flow_id
        foreach (['flow_nodes', 'flow_connections', 'simulations'] as $table) {
            DB::statement("ALTER TABLE {$table} DROP COLUMN flow_id");
            DB::statement("ALTER TABLE {$table} RENAME COLUMN new_flow_id TO flow_id");
            DB::statement("ALTER TABLE {$table} ALTER COLUMN flow_id SET NOT NULL");
        }

        // 6. Drop primary key lama di flows, drop kolom id lama, rename new_id -> id
        DB::statement('ALTER TABLE flows DROP CONSTRAINT flows_pkey');
        DB::statement('ALTER TABLE flows DROP COLUMN id');
        DB::statement('ALTER TABLE flows RENAME COLUMN new_id TO id');
        DB::statement('ALTER TABLE flows ADD PRIMARY KEY (id)');
        DB::statement('ALTER TABLE flows ALTER COLUMN id SET DEFAULT gen_random_uuid()');

        // 7. Pasang lagi FK di 3 tabel anak, sekarang ke flows.id yang sudah uuid
        DB::statement('ALTER TABLE flow_connections ADD CONSTRAINT fk_connection_flow FOREIGN KEY (flow_id) REFERENCES flows(id) ON DELETE CASCADE');
        DB::statement('ALTER TABLE flow_nodes ADD CONSTRAINT fk_flow_nodes_flow FOREIGN KEY (flow_id) REFERENCES flows(id) ON DELETE CASCADE');
        DB::statement('ALTER TABLE simulations ADD CONSTRAINT fk_simulation_flow FOREIGN KEY (flow_id) REFERENCES flows(id) ON DELETE CASCADE');
    }

    public function down(): void
    {
        throw new \Exception('Rollback manual — restore dari backup database.');
    }
};