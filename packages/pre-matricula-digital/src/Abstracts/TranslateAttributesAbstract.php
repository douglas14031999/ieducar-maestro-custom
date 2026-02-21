<?php

namespace iEducar\Packages\PreMatricula\Abstracts;

use iEducar\Packages\PreMatricula\Models\City;
use iEducar\Packages\PreMatricula\Services\TimelineService;

abstract class TranslateAttributesAbstract
{
    protected function translateGender(?int $gender): string
    {
        return match ($gender) {
            1 => 'Feminino',
            2 => 'Masculino',
            default => null
        };
    }

    protected function translateMaritalStatus(?int $status): string
    {
        return match ($status) {
            1 => 'Solteiro(a)',
            2 => 'Casado(a)',
            3 => 'Divorciado(a)',
            4 => 'Separado(a)',
            5 => 'Viúvo(a)',
            6 => 'Companheiro(a)',
            7 => 'Não informado',
            default => 'Não informado'
        };
    }

    protected function translatePlaceOfBirth(?int $cityId): ?string
    {
        if (!$cityId) {
            return null;
        }

        $city = City::with('state')->find($cityId);

        return $city ? "{$city->name}/{$city->state->abbreviation}" : 'Não informado';
    }

    protected function formatEventAttributes(object $event): void
    {
        if (isset($event->before['date_of_birth'])) {
            $event->before['date_of_birth'] = TimelineService::formatDate($event->before['date_of_birth']);
        }

        if (isset($event->before['date_of_birth'])) {
            $event->after['date_of_birth'] = TimelineService::formatDate($event->after['date_of_birth']);
        }

        if (isset($event->before['marital_status'])) {
            $event->before['marital_status'] = $this->translateMaritalStatus($event->before['marital_status']);
            $event->after['marital_status'] = $this->translateMaritalStatus($event->after['marital_status']);
        }

        if (isset($event->before['place_of_birth'])) {
            $event->before['place_of_birth'] = $this->translatePlaceOfBirth($event->before['place_of_birth']);
            $event->after['place_of_birth'] = $this->translatePlaceOfBirth($event->after['place_of_birth']);
        }

        if (isset($event->before['gender'])) {
            $event->before['gender'] = $this->translateGender($event->before['gender']);
        }

        if (isset($event->after['gender'])) {
            $event->after['gender'] = $this->translateGender($event->after['gender']);
        }
    }
}
