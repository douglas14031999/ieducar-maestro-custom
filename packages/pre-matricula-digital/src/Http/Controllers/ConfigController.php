<?php

namespace iEducar\Packages\PreMatricula\Http\Controllers;

use Illuminate\Http\Response;

/**
 * @codeCoverageIgnore
 */
class ConfigController
{
    /**
     * Return config JS.
     *
     * @return Response
     */
    public function config()
    {
        $config = [
            'city' => config('prematricula.city'),
            'state' => config('prematricula.state'),
            'ibge_codes' => config('prematricula.ibge_codes'),
            'map' => [
                'lat' => floatval(config('prematricula.map.lat')),
                'lng' => floatval(config('prematricula.map.lng')),
                'zoom' => intval(config('prematricula.map.zoom')),
            ],
            'token' => config('prematricula.token'),
            'logo' => config('prematricula.logo'),
            'slogan' => config('prematricula.slogan'),
            'allow_optional_address' => config('prematricula.allow_optional_address'),
            'show_how_to_do_video' => config('prematricula.show_how_to_do_video'),
            'video_intro_url' => config('prematricula.video_intro_url'),
            'link_to_restrict_area' => config('prematricula.link_to_restrict_area'),
            'features' => config('prematricula.features'),
        ];

        $config = json_encode($config);

        return new Response("window.config = {$config};", 200, [
            'content-type' => 'text/javascript',
        ]);
    }
}
