<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Add the stored generated column for email indexing
            $table->string('email_indexed')->storedAs('CASE WHEN deleted_at IS NULL THEN email ELSE NULL END');

            // Drop the original unique index on the `email` column
            $table->dropUnique('users_email_unique');

            // Add the unique index on the stored generated column
            $table->unique('email_indexed', 'email_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the unique index on the `email_indexed` column
            $table->dropUnique('email_unique');

            // Drop the generated column
            $table->dropColumn('email_indexed');

            // Restore the original unique index on the `email` column
            $table->unique('email', 'users_email_unique');
        });
    }
};
