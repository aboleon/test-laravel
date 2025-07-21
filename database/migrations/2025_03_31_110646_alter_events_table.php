<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->json('transport')->nullable();
            $table->json('transfert')->nullable();
        });

        DB::table('events')->get()->each(function ($event) {
            $transport = [];
            if ($event->transport_speakers == 1) {
                $transport[] = 'orator';
            }
            if ($event->transport_participants == 1) {
                $transport[] = 'congress';
            }
            if ($event->transport_grant_pax == 1) {
                $transport[] = 'pec';
            }

            DB::table('events')->where('id', $event->id)->update(['transport' => empty($transport) ? null : json_encode($transport)]);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['transport_speakers', 'transport_participants', 'transport_grant_pax','has_transferts']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {Schema::table('events', function (Blueprint $table) {
        $table->boolean('transport_speakers')->nullable();
        $table->boolean('transport_participants')->nullable();
        $table->boolean('transport_grant_pax')->nullable();
    });

        DB::table('events')->get()->each(function ($event) {
            $transport = json_decode($event->transport, true) ?? [];
            DB::table('events')->where('id', $event->id)->update([
                'transport_speakers' => in_array('orator', $transport) ? 1 : null,
                'transport_participants' => in_array('congress', $transport) ? 1 : null,
                'transport_grant_pax' => in_array('pec', $transport) ? 1 : null,
            ]);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('transport');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('transport');
            $table->dropColumn('transfert');
        });

    }
};
