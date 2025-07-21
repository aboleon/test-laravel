<?php

use App\Enum\ParticipantType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('participation_types', function (Blueprint $table) {
            $table->id();
            $table->enum('group', ParticipantType::keys())->default(ParticipantType::default())->index();
            $table->longText('name');
        });

//        $sql = "INSERT INTO `participation_types` (`id`, `group`, `name`) VALUES
//	(2, 'orator', '{\"fr\":\"Comit\\u00e9 scientifique\",\"en\":\"Scientific cometee\"}'),
//	(3, 'orator', '{\"fr\":\"Fondateurs\",\"en\":\"Founders\"}'),
//	(4, 'congress', '{\"fr\":\"Participant\",\"en\":\"Participant\"}'),
//	(5, 'congress', '{\"fr\":\"eposter presenter\",\"en\":\"eposter presenter\"}'),
//	(6, 'industry', '{\"fr\":\"Industriel\",\"en\":\"Industrial\"}'),
//	(7, 'industry', '{\"fr\":\"Industriel non sponsor\",\"en\":\"Industrial non sponsor\"}');";
//
//        DB::statement($sql);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participation_types');
    }
};
