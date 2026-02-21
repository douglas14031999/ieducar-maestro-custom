<?php

use App\Setting;
use App\Support\Database\SettingCategoryTrait;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use SettingCategoryTrait;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Setting::query()->updateOrCreate([
            'key' => 'prematricula.video_intro_url',
        ], [
            'type' => 'string',
            'description' => 'URL referente ao vídeo "Como efetuar sua Pré-matrícula"',
            'value' => 'https://www.youtube.com/embed/ltXDgjS-XpA?html5=1',
            'hint' => 'O vídeo deve estar em endereço público (Youtube, Facebook, Vimeo, etc...). O URL do vídeo deve ser um URL atribuído válido (exemplo.: https://www.youtube.com/embed/ltXDgjS-XpA?html5=1).',
            'setting_category_id' => $this->getSettingCategoryIdByName('Pré-matrícula Digital'),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Setting::query()->where('key', 'prematricula.video_intro_url')->delete();
    }
};
