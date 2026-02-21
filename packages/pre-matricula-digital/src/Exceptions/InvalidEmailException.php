<?php

namespace iEducar\Packages\PreMatricula\Exceptions;

use Exception;
use GraphQL\Error\ClientAware;
use GraphQL\Error\ProvidesExtensions;

class InvalidEmailException extends Exception implements ClientAware, ProvidesExtensions
{
    public function __construct(
        private string $email
    ) {
        parent::__construct("O e-mail [$email] é inválido");
    }

    public function isClientSafe(): bool
    {
        return true;
    }

    public function getExtensions(): ?array
    {
        return [
            'email' => $this->email,
            'message' => $this->getMessage(),
        ];
    }
}
