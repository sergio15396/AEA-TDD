<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompteCorrent extends Model
{
    protected $fillable = ['saldo', 'transferit_avui'];

    private const MAX_OPERACIO = 6000.00;
    private const LIMIT_TRANSFERENCIA_DIARIA = 3000.00;

    public function getSaldo()
    {
        return $this->arredonir($this->saldo);
    }

    public function ingressar($quantitat)
    {
        if (!$this->esQuantitatValida($quantitat)) return false;
        if ($quantitat > self::MAX_OPERACIO) return false;

        $this->saldo += $quantitat;
        $this->saldo = $this->arredonir($this->saldo);
        $this->save();
        return true;
    }

    public function retirar($quantitat)
    {
        if (!$this->esQuantitatValida($quantitat)) return false;
        if ($quantitat > $this->saldo) return false;
        if ($quantitat > self::MAX_OPERACIO) return false;

        $this->saldo -= $quantitat;
        $this->saldo = $this->arredonir($this->saldo);
        $this->save();
        return true;
    }

    public function transferir(CompteCorrent $desti, $quantitat)
    {
        if (!$this->esQuantitatValida($quantitat)) return false;
        if ($quantitat > $this->saldo) return false;
        if ($this->transferit_avui + $quantitat > self::LIMIT_TRANSFERENCIA_DIARIA) return false;

        $this->saldo -= $quantitat;
        $this->saldo = $this->arredonir($this->saldo);
        $desti->saldo += $quantitat;
        $desti->saldo = $this->arredonir($desti->saldo);
        $this->transferit_avui += $quantitat;
        $this->transferit_avui = $this->arredonir($this->transferit_avui);
        $this->save();
        $desti->save();
        return true;
    }

    public function reiniciarTransferenciaDiaria()
    {
        $this->transferit_avui = 0.0;
        $this->save();
    }

    private function esQuantitatValida($quantitat)
    {
        if ($quantitat <= 0) return false;

        $parts = explode('.', (string)$quantitat);
        if (count($parts) == 2 && strlen($parts[1]) > 2) return false;

        return true;
    }

    private function arredonir($valor)
    {
        return round($valor, 2);
    }
}
