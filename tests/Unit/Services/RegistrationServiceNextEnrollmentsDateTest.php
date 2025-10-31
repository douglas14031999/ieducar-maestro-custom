<?php

namespace Tests\Unit\Services;

use App\Services\RegistrationService;
use DateTime;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use PHPUnit\Framework\TestCase;


class RegistrationServiceNextEnrollmentsDateTest extends TestCase
{
    private function makeServiceWithoutConstructor(): RegistrationService
    {
        $ref = new \ReflectionClass(RegistrationService::class);
        return $ref->newInstanceWithoutConstructor();
    }

    private function callUpdateNext(EloquentCollection $coll, string $date): void
    {
        $service = $this->makeServiceWithoutConstructor();
        $ref = new \ReflectionMethod($service, 'updateNextEnrollmentsRegistrationDate');
        $ref->setAccessible(true);
        $ref->invoke($service, $coll, $date);
    }

    private static function asYmd($value): ?string
    {
        if ($value === null) {
            return null;
        }
        if ($value instanceof DateTime) {
            return $value->format('Y-m-d');
        }
        if (is_string($value)) {
            return (new DateTime($value))->format('Y-m-d');
        }
        return null;
    }

    /**
     * CT1: Entradas válidas
     * Entrada: enturmacao=2024-02-01, exclusao=2024-02-01, date=2024-03-01
     * Saída esperada: enturmacao=2024-03-01, exclusao=2024-03-01
     */
    public function test_ct1_entradas_validas(): void
    {
        $enrollment = new EnrollmentStub('2024-02-01', '2024-02-01');

        $this->callUpdateNext(new EloquentCollection([$enrollment]), '2024-03-01');

        $this->assertSame('2024-03-01', self::asYmd($enrollment->data_enturmacao));
        $this->assertSame('2024-03-01', self::asYmd($enrollment->data_exclusao));
    }

    /**
     * CT2: Enturmação anterior e exclusão nula 
     * Entrada: enturmacao=2024-02-01, exclusao=null, date=2024-03-01
     * Saída esperada: enturmacao=2024-03-01, exclusao permanece null
     */
    public function test_ct2_enturmacao_anterior_exc_nula(): void
    {
        $enrollment = new EnrollmentStub('2024-02-01', null);

        $this->callUpdateNext(new EloquentCollection([$enrollment]), '2024-03-01');

        $this->assertSame('2024-03-01', self::asYmd($enrollment->data_enturmacao));
        $this->assertNull(self::asYmd($enrollment->data_exclusao));
    }

    /**
     * CT3: Exclusão anterior e enturmação posterior 
     * Entrada: enturmacao=2024-04-01, exclusao=2024-02-01, date=2024-03-01
     * Saída esperada: enturmacao permanece 2024-04-01; exclusao=2024-03-01
     */
    public function test_ct3_exclusao_anterior_enturmacao_posterior(): void
    {
        $enrollment = new EnrollmentStub('2024-04-01', '2024-02-01');

        $this->callUpdateNext(new EloquentCollection([$enrollment]), '2024-03-01');

        $this->assertSame('2024-04-01', self::asYmd($enrollment->data_enturmacao)); 
        $this->assertSame('2024-03-01', self::asYmd($enrollment->data_exclusao));   
    }
}

class EnrollmentStub
{
    /** @var DateTime|null */
    public $data_enturmacao;

    /** @var DateTime|null */
    public $data_exclusao;

    public function __construct(?string $enturmacao, ?string $exclusao)
    {
        $this->data_enturmacao = $enturmacao ? new DateTime($enturmacao) : null;
        $this->data_exclusao   = $exclusao ? new DateTime($exclusao) : null;
    }

    public function __set(string $name, $value): void
    {
        if (in_array($name, ['data_enturmacao', 'data_exclusao'], true)) {
            $this->$name = $value instanceof DateTime ? $value : new DateTime($value);
            return;
        }
        $this->$name = $value;
    }

    public function save(): void
    {
    }
}
