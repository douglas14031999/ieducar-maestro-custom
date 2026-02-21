<?php

namespace iEducar\Packages\PreMatricula\Support;

trait InitialsFromName
{
    public function initials(string $name): string
    {
        return str($name)
            ->explode(' ')
            ->reject(fn ($partial) => strlen($partial) <= 2)
            ->map(fn ($partial) => mb_substr(trim($partial), 0, 1) . '.')
            ->implode('');
    }
}
