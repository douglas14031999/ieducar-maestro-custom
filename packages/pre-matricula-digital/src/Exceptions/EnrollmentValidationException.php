<?php

namespace iEducar\Packages\PreMatricula\Exceptions;

use Exception;
use GraphQL\Error\ClientAware;
use GraphQL\Error\ProvidesExtensions;
use iEducar\Packages\PreMatricula\Models\PreRegistration;

class EnrollmentValidationException extends Exception implements ClientAware, ProvidesExtensions
{
    const ERROR_MISSING_STUDENT_INEP = 1;

    const ERROR_NO_VACANCY = 2;

    const ERROR_EXISTING_ENROLLMENT = 3;

    private $preregistration;

    private $customMessage;

    public function __construct($preregistration, $message, $code = 0)
    {
        $this->preregistration = $preregistration;
        $this->customMessage = $message;

        parent::__construct('Não foi possível realizar o deferimento', $code);
    }

    /**
     * @codeCoverageIgnore
     */
    public function isClientSafe(): bool
    {
        return true;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getExtensions(): ?array
    {
        return [
            'preregistration' => $this->preregistration,
            'message' => $this->customMessage,
        ];
    }

    public static function missingStudentInep($preregistration)
    {
        $message = sprintf('O(a) aluno(a) <strong>%s</strong>  não possui código INEP cadastrado', $preregistration->student->name);

        return new static($preregistration, $message, self::ERROR_MISSING_STUDENT_INEP);
    }

    public static function noVacancy(PreRegistration $preregistration)
    {
        $message = sprintf('Não existe vaga disponível para matricular o(a) aluno(a) <strong>%s</strong> na turma selecionada', $preregistration->student->name);

        return new static($preregistration, $message, self::ERROR_NO_VACANCY);
    }

    public static function existingEnrollment(PreRegistration $preregistration)
    {
        $message = sprintf('O(a) aluno(a) <strong>%s</strong> já possuí uma enturmação ativa para o ano letivo no i-Educar', $preregistration->student->name);

        return new static($preregistration, $message, self::ERROR_EXISTING_ENROLLMENT);
    }
}
