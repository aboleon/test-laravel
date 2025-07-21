<?php

use App\Services\Pec\PecType;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE pec_distribution MODIFY COLUMN type ENUM('" . implode("', '", PecType::keys()) . "') DEFAULT '" . PecType::default() . "'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        $array = PecType::keys();
        $index = array_search(PecType::TAXROOM->value, $array);

        if ($index !== false) {
            unset($array[$index]);
            $array = array_values($array);
        }
        DB::statement("ALTER TABLE pec_distribution MODIFY COLUMN type ENUM('" . implode("', '", $array) . "') DEFAULT '" . PecType::default() . "'");
    }
};
