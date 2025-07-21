<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    protected $viewName = 'users_administrateurs_view';


    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
         CREATE VIEW {$this->viewName} AS
            SELECT
                users.id AS user_id,
                CONCAT(users.first_name, ' ', users.last_name) AS name,
                users.email AS email,
                users_profile.mobile AS mobile
            FROM
                users
            JOIN users_profile ON users.id = users_profile.user_id        
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS {$this->viewName}");
    }
};
