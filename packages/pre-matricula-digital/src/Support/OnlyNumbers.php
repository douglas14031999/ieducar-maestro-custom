<?php

namespace iEducar\Packages\PreMatricula\Support;

trait OnlyNumbers
{
    public function onlyNumbers(string $formattedNumber): int
    {
        return preg_replace('/[^0-9]/', '', $formattedNumber);
    }

    public function getDdd(string $phone): int
    {
        [$ddd] = str_replace(['(', ' ', '-'], '', explode(')', $phone));

        return $ddd;
    }

    public function getPhoneNumber(string $phone): int
    {
        [$_, $phone] = str_replace(['(', ' ', '-'], '', explode(')', $phone));

        return $phone;
    }
}
