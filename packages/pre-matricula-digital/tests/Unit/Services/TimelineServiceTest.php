<?php

namespace iEducar\Packages\PreMatricula\Tests\Unit\Services;

use iEducar\Packages\PreMatricula\Database\Factories\PreRegistrationFactory;
use iEducar\Packages\PreMatricula\Models\Field;
use iEducar\Packages\PreMatricula\Models\PreRegistration;
use iEducar\Packages\PreMatricula\Models\Timeline;
use iEducar\Packages\PreMatricula\Services\TimelineService;
use iEducar\Packages\PreMatricula\Tests\TestCase;
use Illuminate\Support\Facades\Lang;

class TimelineServiceTest extends TestCase
{
    private TimelineService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TimelineService;
    }

    public function test_translate_fields(): void
    {
        Lang::shouldReceive('get')
            ->with('prematricula::pre-registration-timeline.fields', [], 'pt_BR')
            ->andReturn([
                'status' => 'Status',
                'name' => 'Nome',
                'email' => 'E-mail',
            ]);

        $data = [
            'status' => 'waiting',
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ];

        $translated = $this->service->translateFields($data);

        $this->assertEquals([
            'Status' => 'waiting',
            'Nome' => 'John Doe',
            'E-mail' => 'john@example.com',
        ], $translated);
    }

    public function test_translate_fields_with_unknown_key(): void
    {
        Lang::shouldReceive('get')
            ->with('prematricula::pre-registration-timeline.fields', [], 'pt_BR')
            ->andReturn([
                'status' => 'Status',
            ]);

        $data = [
            'status' => 'waiting',
            'unknown' => 'value',
        ];

        $translated = $this->service->translateFields($data);

        $this->assertEquals([
            'Status' => 'waiting',
            'unknown' => 'value',
        ], $translated);
    }

    public function test_translate_payload(): void
    {
        $payload = [
            'before' => [
                'status' => 'waiting',
                'name' => 'John Doe',
                'email' => 'john@example.com',
            ],
            'after' => [
                'status' => 'accepted',
                'name' => 'John Doe',
                'email' => 'john@example.com',
            ],
        ];

        $translated = $this->service->translatePayload($payload);

        $this->assertArrayHasKey('before', $translated);
        $this->assertArrayHasKey('after', $translated);
        $this->assertIsArray($translated['before']);
        $this->assertIsArray($translated['after']);
    }

    public function test_translate_fields_with_options(): void
    {
        $fields = collect([
            (object) [
                'field' => (object) [
                    'name' => 'status',
                    'field_type_id' => Field::TYPE_SELECT,
                    'options' => collect([
                        (object) ['id' => 'waiting', 'name' => 'Aguardando'],
                        (object) ['id' => 'accepted', 'name' => 'Aceito'],
                    ]),
                ],
                'value' => 'waiting',
            ],
        ]);

        $processed = $this->service->processFieldsWithOptions($fields);

        $this->assertArrayHasKey('status', $processed);
        $this->assertEquals('Aguardando', $processed['status']);
    }

    public function test_translate_fields_without_options(): void
    {
        $fields = collect([
            (object) [
                'field' => (object) [
                    'name' => 'name',
                    'field_type_id' => Field::TYPE_TEXT,
                    'options' => collect([]),
                ],
                'value' => 'John Doe',
            ],
        ]);

        $processed = $this->service->processFieldsWithOptions($fields);

        $this->assertArrayHasKey('name', $processed);
        $this->assertEquals('John Doe', $processed['name']);
    }

    public function test_translate_fields_with_null_value(): void
    {
        $fields = collect([
            (object) [
                'field' => (object) [
                    'name' => 'name',
                    'field_type_id' => Field::TYPE_TEXT,
                    'options' => collect([]),
                ],
                'value' => null,
            ],
        ]);

        $processed = $this->service->processFieldsWithOptions($fields);

        $this->assertArrayHasKey('name', $processed);
        $this->assertEquals('Não informado', $processed['name']);
    }

    public function test_create_timeline(): void
    {
        $preRegistration = PreRegistrationFactory::new()->create();
        $payload = [
            'before' => ['status' => 'waiting'],
            'after' => ['status' => 'accepted'],
        ];

        $timeline = TimelineService::create(
            type: 'test-type',
            model: $preRegistration,
            payload: $payload
        );

        $this->assertInstanceOf(Timeline::class, $timeline);
        $this->assertEquals('test-type', $timeline->type);
        $this->assertEquals($preRegistration->id, $timeline->model_id);
        $this->assertEquals(PreRegistration::class, $timeline->model_type);
        $this->assertEquals($payload, $timeline->payload);
    }

    public function test_create_timeline_with_empty_payload(): void
    {
        $preRegistration = PreRegistrationFactory::new()->create();
        $payload = [];

        $timeline = TimelineService::create(
            type: 'test-type',
            model: $preRegistration,
            payload: $payload
        );

        $this->assertNull($timeline);
    }

    public function test_clear_payload(): void
    {
        $payload = [
            'before' => [
                'status' => 'waiting',
                'created_at' => '2024-01-01',
                'updated_at' => '2024-01-01',
            ],
            'after' => [
                'status' => 'accepted',
                'created_at' => '2024-01-02',
                'updated_at' => '2024-01-02',
            ],
        ];

        $cleared = TimelineService::clearPayload($payload);

        $this->assertEquals(['status' => 'waiting'], $cleared['before']);
        $this->assertEquals(['status' => 'accepted'], $cleared['after']);
    }

    public function test_format_date(): void
    {
        $date = '2024-01-01';
        $formatted = TimelineService::formatDate($date);

        $this->assertEquals('01/01/2024', $formatted);
    }

    public function test_format_date_with_invalid_date(): void
    {
        $date = 'invalid-date';
        $formatted = TimelineService::formatDate($date);

        $this->assertEquals('invalid-date', $formatted);
    }

    public function test_format_date_with_null(): void
    {
        $formatted = TimelineService::formatDate(null);

        $this->assertNull($formatted);
    }
}
