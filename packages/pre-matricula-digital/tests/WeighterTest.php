<?php

namespace iEducar\Packages\PreMatricula\Tests;

use iEducar\Packages\PreMatricula\Services\Weight\Weigher;
use PHPUnit\Framework\TestCase;

class WeighterTest extends TestCase
{
    protected $weigher;

    protected function setUp(): void
    {
        $this->weigher = new Weigher;
    }

    public function test_weight()
    {
        $this->assertEquals(10, $this->weigher->weight(2, 5));
        $this->assertEquals(10, $this->weigher->weight(2, -5));
        $this->assertEquals(10, $this->weigher->weight(-2, 5));
        $this->assertEquals(10, $this->weigher->weight(-2, -5));
    }

    public function test_filled_weight()
    {
        $this->assertEquals(0, $this->weigher->filled('', 10));
        $this->assertEquals(0, $this->weigher->filled(null, 10));
        $this->assertEquals(0, $this->weigher->filled([], 10));
        $this->assertEquals(0, $this->weigher->filled(false, 10));
        $this->assertEquals(0, $this->weigher->filled(0, 10));
        $this->assertEquals(0, $this->weigher->filled('0', 10));

        $this->assertEquals(10, $this->weigher->filled(' ', 10));
        $this->assertEquals(10, $this->weigher->filled('1', 10));
        $this->assertEquals(10, $this->weigher->filled(1, 10));
        $this->assertEquals(10, $this->weigher->filled(true, 10));
        $this->assertEquals(10, $this->weigher->filled([1], 10));
    }

    public function test_date_positive_weight()
    {
        // Mais velhos tem mais prioridade
        $younger = now()->day(11)->month(11)->year(2017); // 30000 to 2100-01-01
        $older = now()->day(12)->month(10)->year(2017); // 30030 to 2100-01-01

        $weight1 = $this->weigher->date($younger, 1);
        $weight2 = $this->weigher->date($older, 1);

        $this->assertEquals(30000, $weight1);
        $this->assertEquals(30030, $weight2);
    }

    public function test_date_negative_weight()
    {
        // Mais novos tem mais prioridade
        $younger = '2017-11-11'; // 70000 to 2100-01-01
        $older = '2017-10-12'; // 69070 to 2100-01-01

        $weight1 = $this->weigher->date($younger, -1);
        $weight2 = $this->weigher->date($older, -1);

        $this->assertEquals(70000, $weight1);
        $this->assertEquals(69970, $weight2);
    }
}
