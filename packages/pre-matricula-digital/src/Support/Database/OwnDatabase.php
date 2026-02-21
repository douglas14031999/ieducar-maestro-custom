<?php

namespace iEducar\Packages\PreMatricula\Support\Database;

/**
 * @codeCoverageIgnore
 */
trait OwnDatabase
{
    public function useOwnDatabase(): bool
    {
        return !$this->useThirdPartyDatabase();
    }

    public function useThirdPartyDatabase(): bool
    {
        return config('prematricula.legacy');
    }
}
