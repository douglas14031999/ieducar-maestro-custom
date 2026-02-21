<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Mail\PreRegistrationProtocolMail;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;
use Illuminate\Support\Facades\Mail;

class SendProtocolByEmailTest extends GraphQLTestCase
{
    public function test_send_email(): void
    {
        $query = '
            mutation sendProtocolsByEmail(
                $preregistrations: [ID!]!
                $email: String!
            ) {
                sendProtocolsByEmail(
                    preregistrations: $preregistrations
                    email: $email
                )
            }
        ';

        Mail::fake();

        $this->graphQL($query, [
            'preregistrations' => [],
            'email' => 'contato@portabilis.com.br',
        ])->assertJson([
            'data' => [
                'sendProtocolsByEmail' => true,
            ],
        ]);

        Mail::assertSent(PreRegistrationProtocolMail::class, function ($mail) {
            return $mail->hasTo('contato@portabilis.com.br')
                && $mail->assertSeeInHtml('Sua inscrição foi realizada com sucesso.');
        });
    }

    public function test_wrong_email(): void
    {
        $query = '
            mutation sendProtocolsByEmail(
                $preregistrations: [ID!]!
                $email: String!
            ) {
                sendProtocolsByEmail(
                    preregistrations: $preregistrations
                    email: $email
                )
            }
        ';

        $this->graphQL($query, [
            'preregistrations' => [],
            'email' => 'contato@portabilis',
        ])->assertGraphQLErrorMessage('O e-mail [contato@portabilis] é inválido');
    }
}
