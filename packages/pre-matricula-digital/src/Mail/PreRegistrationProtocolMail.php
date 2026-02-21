<?php

namespace iEducar\Packages\PreMatricula\Mail;

use iEducar\Packages\PreMatricula\Models\PreRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PreRegistrationProtocolMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @var array
     */
    private $ids;

    /**
     * Create a new message instance.
     */
    public function __construct(array $ids, string $email)
    {
        $this->ids = $ids;

        $this->subject('Confirmação de inscrição');
        $this->to($email);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('prematricula::protocol-mail', [
            'preregistrations' => PreRegistration::query()->findMany($this->ids),
        ]);
    }
}
