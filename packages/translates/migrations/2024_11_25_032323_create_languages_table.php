<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use DreamTeam\Base\Enums\BaseStatusEnum;
use DreamTeam\Base\Supports\Language;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id('id');
            $table->string('name', 120);
            $table->string('locale', 20)->index();
            $table->string('code', 20)->index();
            $table->string('flag', 20)->nullable();
            $table->tinyInteger('is_default')->unsigned()->default(0)->index();
            $table->integer('order')->default(0);
            $table->integer('status')->default(BaseStatusEnum::ACTIVE);
            $table->tinyInteger('is_rtl')->unsigned()->default(0);
            $table->timestamps();
        });

        $listLangs = Language::getListLanguages();
        $langs = [
            [
                'code' => $listLangs['vi'][1],
                'locale' => $listLangs['vi'][0],
                'name' => $listLangs['vi'][2],
                'flag' => $listLangs['vi'][4],
                'order' => 0,
                'status' => BaseStatusEnum::ACTIVE
            ],
            [
                'code' => $listLangs['en_US'][1],
                'locale' => $listLangs['en_US'][0],
                'name' => $listLangs['en_US'][2],
                'flag' => $listLangs['en_US'][4],
                'order' => 1,
                'status' => BaseStatusEnum::ACTIVE
            ],
            [
                'code' => $listLangs['de_DE'][1],
                'locale' => $listLangs['de_DE'][0],
                'name' => $listLangs['de_DE'][2],
                'flag' => $listLangs['de_DE'][4],
                'order' => 2,
                'status' => BaseStatusEnum::ACTIVE
            ],
            [
                'code' => $listLangs['fr_FR'][1],
                'locale' => $listLangs['fr_FR'][0],
                'name' => $listLangs['fr_FR'][2],
                'flag' => $listLangs['fr_FR'][4],
                'order' => 3,
                'status' => BaseStatusEnum::ACTIVE
            ]
        ];
        DB::table('languages')->insert($langs);
    }

    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
