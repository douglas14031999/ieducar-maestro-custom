<?php

namespace iEducar\Packages\PreMatricula\Exceptions;

use Exception;
use GraphQL\Error\ClientAware;
use GraphQL\Error\ProvidesExtensions;
use iEducar\Packages\PreMatricula\Models\Classroom;

class EnrollmentRelocationValidationException extends Exception implements ClientAware, ProvidesExtensions
{
    public const ERROR_EXISTS_ACTIVE_ENROLLMENT_SAME_TIME = 1;

    public const ERROR_MULTIPLE_ACTIVE_ENROLLMENTS = 2;

    public const ERROR_CANCELLATION_DATE_AFTER_ACADEMIC_YEAR = 3;

    public const ERROR_PREVIOUS_CANCELLATION_DATE = 4;

    public const ERROR_PREVIOUS_ENROLL_CANCELLATION_DATE = 5;

    public const ERROR_ENROLLMENT_SAME_CLASSROOM = 6;

    public const ERROR_ENROLL_DATE_AFTER_ACADEMIC_YEAR = 7;

    public const ERROR_PREVIOUS_ENROLL_DATE = 8;

    public const ERROR_PREVIOUS_ENROLL_REGISTRATION_DATE = 9;

    private $customMessage;

    public function __construct($message, $code = 0)
    {
        $fullMessage = 'Não foi possível realizar o <strong>remanejamento</strong>. ' . $message;
        $this->customMessage = $fullMessage;

        parent::__construct('Não foi possível realizar o deferimento', $code);
    }

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
            'message' => $this->customMessage,
        ];
    }

    public static function existsActiveEnrollmentSameTime(string $originalMessage)
    {
        return new static($originalMessage, self::ERROR_EXISTS_ACTIVE_ENROLLMENT_SAME_TIME);
    }

    public static function multipleActiveEnrollments()
    {
        $message = 'Existe mais de uma enturmação ativa.';

        return new static($message, self::ERROR_MULTIPLE_ACTIVE_ENROLLMENTS);
    }

    public static function cancellationDateAfterAcademicYear(string $originalMessage)
    {
        return new static($originalMessage, self::ERROR_CANCELLATION_DATE_AFTER_ACADEMIC_YEAR);
    }

    public static function previousCancellationDate(string $originalMessage)
    {
        return new static($originalMessage, self::ERROR_PREVIOUS_CANCELLATION_DATE);
    }

    public static function previousEnrollCancellationDate(string $originalMessage)
    {
        return new static($originalMessage, self::ERROR_PREVIOUS_ENROLL_CANCELLATION_DATE);
    }

    public static function enrollmentSameClassroom(Classroom $classroom)
    {
        $message = 'O(a) aluno(a) já possui enturmação na turma <strong>%s</strong>';
        $message = sprintf($message, $classroom->name);

        return new static($message, self::ERROR_ENROLLMENT_SAME_CLASSROOM);
    }

    public static function enrollDateAfterAcademicYear(string $originalMessage)
    {
        return new static($originalMessage, self::ERROR_ENROLL_DATE_AFTER_ACADEMIC_YEAR);
    }

    public static function previousEnrollDate(string $originalMessage)
    {
        return new static($originalMessage, self::ERROR_PREVIOUS_ENROLL_DATE);
    }

    public static function previousEnrollRegistrationDate(string $originalMessage)
    {
        return new static($originalMessage, self::ERROR_PREVIOUS_ENROLL_REGISTRATION_DATE);
    }
}
