<?php

namespace iEducar\Packages\PreMatricula\Services\Weight;

use Carbon\Carbon;

class Weigher
{
    public function date($value, $weight)
    {
        $date = Carbon::hasFormat($value, 'Y-m-d')
            ? Carbon::createFromFormat('Y-m-d', $value)
            : Carbon::parse($value);

        $value = (int) $date->diffInDays(Carbon::create(2100, 01, 01));

        if ($weight < 0) {
            $value -= 100000;
        }

        return $this->weight($value, $weight);
    }

    public function filled($value, $weight)
    {
        $value = $value ? 1 : 0;

        return $this->weight($value, $weight);
    }

    public function weight($value, $weight)
    {
        return abs($value * $weight);
    }
}
