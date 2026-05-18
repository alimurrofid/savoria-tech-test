<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            CREATE OR REPLACE VIEW view_user_application_access AS
            SELECT DISTINCT access_list.user_id, a.id AS application_id, a.name, a.url, a.icon, a.category
            FROM (
                SELECT u.id AS user_id, ad.application_id FROM users u JOIN application_department ad ON u.department_id = ad.department_id
                UNION
                SELECT u.id AS user_id, ar.application_id FROM users u JOIN application_role ar ON u.role_id = ar.role_id
                UNION
                SELECT au.user_id, au.application_id FROM application_user au
            ) AS access_list
            JOIN applications a ON access_list.application_id = a.id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS view_user_application_access');
    }
};
