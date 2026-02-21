<?php

namespace iEducar\Packages\PreMatricula\Exceptions;

use Exception;
use GraphQL\Error\ClientAware;
use GraphQL\Error\ProvidesExtensions;

class PreRegistrationValidationException extends Exception implements ClientAware, ProvidesExtensions
{
    private string $customMessage;

    public function __construct(string $message)
    {
        $this->customMessage = $message;

        parent::__construct('Ocorreu um erro ao finalizar a inscrição');
    }

    public function isClientSafe(): bool
    {
        return true;
    }

    public function getExtensions(): ?array
    {
        // Foi preciso customizar o retorno devido ao método `assertGraphQLValidationError` só fazer a asserção se a
        // estrutura do framework for obedecida.

        return [
            'message' => $this->customMessage,
            'validation' => [
                'message' => [$this->customMessage],
            ],
        ];
    }

    public static function duplicatedRegistration(): static
    {
        return new static('Já existe uma inscrição para este(a) aluno(a)');
    }

    public static function waitingListLimit(): static
    {
        return new static('O limite de inscrição em lista de espera já foi atingido');
    }

    public static function nothingRegistered(): static
    {
        return new static('Nenhuma pré-matrícula realizada');
    }

    public static function noVacancy(): static
    {
        return new static('Não há vagas para a série e turno selecionados na escola');
    }

    public static function stageIsNotOpen(): static
    {
        return new static('O período de inscrição não está aberto');
    }

    public static function isNotInConfirmation(): static
    {
        return new static('A inscrição não está em confirmação');
    }

    public static function gradeIsRequired(): static
    {
        return new static('A série é obrigatória');
    }
}
