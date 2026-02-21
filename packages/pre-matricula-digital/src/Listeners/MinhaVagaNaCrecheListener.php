<?php

namespace iEducar\Packages\PreMatricula\Listeners;

use iEducar\Packages\PreMatricula\Models\Process;
use Illuminate\Support\Facades\Http;

class MinhaVagaNaCrecheListener
{
    public function handle(): void
    {
        if (empty($this->getMinhaVagaNaCrecheUrl()) || empty($this->getUrl())) {
            return;
        }

        $exists = Process::query()
            ->where('active', true)
            ->count();

        if (empty($exists)) {
            return;
        }

        $publishWaitingList = Process::query()
            ->where('active', true)
            ->where('show_waiting_list', true)
            ->exists();

        $url = $this->getUrl();

        $payload = [
            'city' => $this->getCity(),
            'state' => $this->getState(),
            'availability' => 'online',
            'status' => $publishWaitingList ? 'available' : 'not-info',
            'application_url' => $url,
            'waiting_list_url' => $publishWaitingList ? $url : null,
        ];

        $response = Http::withToken($this->getToken())->put(
            url: $this->getMinhaVagaNaCrecheUrl(),
            data: $payload,
        );

        if ($response->failed()) {
            logger([
                'response' => $response->json(),
                'payload' => $payload,
            ]);
        }
    }

    private function getUrl(): string
    {
        return str(config('app.url'))->trim('/')->append('/pre-matricula-digital')->value();
    }

    private function getCity(): string
    {
        return config('prematricula.city');
    }

    private function getState(): string
    {
        return config('prematricula.state');
    }

    private function getToken(): ?string
    {
        return config('prematricula.minha_vaga_na_creche.token');
    }

    private function getMinhaVagaNaCrecheUrl(): ?string
    {
        return config('prematricula.minha_vaga_na_creche.url');
    }
}
