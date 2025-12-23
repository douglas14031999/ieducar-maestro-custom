<?php

class TabelaNormalizarMediaTest extends UnitBaseTest
{
    /**
     * Sem normalização: soma 75 não encontra range 1-25, retorna último
     */
    public function test_soma_sem_normalizacao_retorna_ultimo_conceito()
    {
        $tabela = $this->criarTabela(normalizarMedia: false);
        $resultado = $tabela->round(75, 2, 1, 4);

        $this->assertEquals('A', $resultado);
    }

    /**
     * Com normalização: soma 75 / 4 etapas = 18.75 → "C"
     */
    public function test_soma_com_normalizacao_encontra_conceito_correto()
    {
        $tabela = $this->criarTabela(normalizarMedia: true);
        $resultado = $tabela->round(75, 2, 1, 4);

        $this->assertEquals('C', $resultado);
    }

    /**
     * Nota de etapa não é afetada pela normalização (com checkbox)
     */
    public function test_nota_etapa_nao_normaliza_com_checkbox()
    {
        $tabela = $this->criarTabela(normalizarMedia: true);
        $resultado = $tabela->round(20, 1, 1, 4);

        $this->assertEquals('B', $resultado);
    }

    /**
     * Nota de etapa sem checkbox marcado
     */
    public function test_nota_etapa_sem_checkbox()
    {
        $tabela = $this->criarTabela(normalizarMedia: false);
        $resultado = $tabela->round(20, 1, 1, 4);

        $this->assertEquals('B', $resultado);
    }

    /**
     * Normalização com 2 etapas
     */
    public function test_normalizacao_com_2_etapas()
    {
        $tabela = $this->criarTabela(normalizarMedia: true);
        $resultado = $tabela->round(40, 2, 1, 2);

        $this->assertEquals('B', $resultado);
    }

    /**
     * Soma máxima: 100 / 4 = 25 → "A"
     */
    public function test_normalizacao_soma_maxima()
    {
        $tabela = $this->criarTabela(normalizarMedia: true);
        $resultado = $tabela->round(100, 2, 1, 4);

        $this->assertEquals('A', $resultado);
    }

    /**
     * Soma mínima: 20 / 4 = 5 → "D"
     */
    public function test_normalizacao_soma_minima()
    {
        $tabela = $this->criarTabela(normalizarMedia: true);
        $resultado = $tabela->round(20, 2, 1, 4);

        $this->assertEquals('D', $resultado);
    }

    /**
     * Sem qtdeEtapas não normaliza
     */
    public function test_sem_qtde_etapas_nao_normaliza()
    {
        $tabela = $this->criarTabela(normalizarMedia: true);
        $resultado = $tabela->round(20, 2, 1, null);

        $this->assertEquals('B', $resultado);
    }

    /**
     * qtdeEtapas = 0 não normaliza (evita divisão por zero)
     */
    public function test_qtde_etapas_zero_nao_normaliza()
    {
        $tabela = $this->criarTabela(normalizarMedia: true);
        $resultado = $tabela->round(20, 2, 1, 0);

        $this->assertEquals('B', $resultado);
    }

    private function criarTabela($normalizarMedia)
    {
        $tabela = new TabelaArredondamento_Model_Tabela([
            'tipoNota' => RegraAvaliacao_Model_Nota_TipoValor::CONCEITUAL,
            'normalizarMedia' => $normalizarMedia,
        ]);

        // Ranges: D(1-14.9), C(15-18.9), B(19-21.9), A(22-25)
        $valores = [
            $this->criarValor('D', 1, 14.9),
            $this->criarValor('C', 15, 18.9),
            $this->criarValor('B', 19, 21.9),
            $this->criarValor('A', 22, 25),
        ];

        $reflection = new ReflectionClass($tabela);
        $property = $reflection->getProperty('_tabelaValores');
        $property->setAccessible(true);
        $property->setValue($tabela, $valores);

        return $tabela;
    }

    private function criarValor($nome, $min, $max)
    {
        return new TabelaArredondamento_Model_TabelaValor([
            'nome' => $nome,
            'valorMinimo' => $min,
            'valorMaximo' => $max,
        ]);
    }
}
