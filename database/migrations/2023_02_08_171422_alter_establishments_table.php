<?php

use App\Enum\EstablishmentType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('establishments', function (Blueprint $table) {
            $table->dropColumn('visibility');
            $table->enum('type', EstablishmentType::keys())->after('name')->default(EstablishmentType::default());
            $table->string('street_number')->after('type')->nullable();
            $table->string('route')->after('street_number')->nullable();
            $table->string('postal_code')->after('route')->nullable();
            $table->string('administrative_area_level_1')->after('country')->nullable();
            $table->string('administrative_area_level_2')->after('administrative_area_level_1')->nullable();
            $table->text('text_address')->after('administrative_area_level_2')->nullable();
            $table->decimal('lat', 16, 13, true)->after('text_address')->nullable();
            $table->decimal('lon', 16, 13, true)->after('lat')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('establishments', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->enum('visibility', EstablishmentType::keys())->after('name')->default(EstablishmentType::default());
            $table->dropColumn('street_number');
            $table->dropColumn('route');
            $table->dropColumn('postal_code');
            $table->dropColumn('administrative_area_level_1');
            $table->dropColumn('administrative_area_level_2');
            $table->dropColumn('text_address');
            $table->dropColumn('lat');
            $table->dropColumn('lon');
        });
    }
};
