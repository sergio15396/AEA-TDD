<?php

namespace Tests\Unit;

use App\Models\CompteCorrent;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompteCorrentTest extends TestCase
{
    use RefreshDatabase;

    public function test_saldo_inicial()
    {
        $compte = new CompteCorrent();
        $this->assertEquals(0.0, $compte->getSaldo());
    }

    public function test_ingressar()
    {
        $compte = new CompteCorrent();
        $this->assertTrue($compte->ingressar(100.45));
        $this->assertEquals(100.45, $compte->getSaldo());
    }

    public function test_retirar()
    {
        $compte = new CompteCorrent();
        $compte->ingressar(100.45);
        $this->assertTrue($compte->retirar(50.00));
        $this->assertEquals(50.45, $compte->getSaldo());
    }

    public function test_transferir()
    {
        $compte1 = new CompteCorrent();
        $compte2 = new CompteCorrent();
        $compte1->ingressar(100.45);
        $this->assertTrue($compte1->transferir($compte2, 30.00));
        $this->assertEquals(70.45, $compte1->getSaldo());
        $this->assertEquals(30.00, $compte2->getSaldo());
    }

    public function test_quantitat_invalida()
    {
        $compte = new CompteCorrent();
        $this->assertFalse($compte->ingressar(-100.00));
        $this->assertFalse($compte->ingressar(0.00));
        $this->assertFalse($compte->ingressar(100.123));
    }
} 