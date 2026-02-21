<?php

namespace iEducar\Packages\PreMatricula\GraphQL\Mutations;

use iEducar\Packages\PreMatricula\Exceptions\InvalidEmailException;
use iEducar\Packages\PreMatricula\Mail\PreRegistrationProtocolMail;
use Illuminate\Support\Facades\Mail;
use Nuwave\Lighthouse\Execution\ErrorPool;
use Throwable;

class SendProtocolsByEmail
{
    public function __construct(
        private ErrorPool $errors
    ) {}

    private function validateEmail($email): void
    {
        $validate = filter_var($email, FILTER_VALIDATE_EMAIL);

        if (empty($validate)) {
            throw new InvalidEmailException($email);
        }
    }

    private function sendEmail(array $preregistrations, string $email): void
    {
        Mail::send(
            new PreRegistrationProtocolMail($preregistrations, $email)
        );
    }

    public function __invoke($_, array $args): bool
    {
        try {
            $this->validateEmail($args['email']);
            $this->sendEmail($args['preregistrations'], $args['email']);
        } catch (Throwable $throwable) {
            $this->errors->record($throwable);

            return false;
        }

        return true;
    }
}
