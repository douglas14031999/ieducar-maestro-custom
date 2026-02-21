<?php

namespace iEducar\Packages\PreMatricula\Exceptions;

use App\Models\LegacyRegistration;
use Exception;
use GraphQL\Error\ClientAware;
use GraphQL\Error\ProvidesExtensions;

class TransferValidationException extends Exception implements ClientAware, ProvidesExtensions
{
    const ERROR_TRANSFER_EXISTS = 1;

    private $registration;

    private $customMessage;

    public function __construct(LegacyRegistration $registration, $message, $code = 0)
    {
        $this->registration = $registration;
        $this->customMessage = $message;

        parent::__construct('Não foi possível realizar o deferimento', $code);
    }

    public function isClientSafe(): bool
    {
        return true;
    }

    /**
     * @codeCoverageIgnore'
     */
    public function getExtensions(): ?array
    {
        return [
            'registration' => $this->registration,
            'message' => $this->customMessage,
        ];
    }

    public static function activeTransferRequest(LegacyRegistration $registration)
    {
        $message = sprintf('O(a) aluno(a) <strong>%s</strong> já possuí uma solicitação de transferência ativa para o ano letivo no i-Educar', $registration->name);

        return new static($registration, $message, self::ERROR_TRANSFER_EXISTS);
    }
}
