<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `events` ADD COLUMN `code` VARCHAR(255) NULL DEFAULT NULL");

        $events = \App\Models\Event::withTrashed()->pluck('created_at', 'id');
        DB::beginTransaction();

        try {
            foreach ($events as $id => $date) {
                DB::statement("UPDATE `events` SET `code`='".Str::upper(Str::random(4)).$date->format('Y')."' where `id`=$id");
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
        }
        DB::statement("ALTER TABLE `events` ADD UNIQUE INDEX `code` (`code`);");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
};
