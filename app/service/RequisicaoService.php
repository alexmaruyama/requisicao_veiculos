<?php

use Adianti\Database\TTransaction;

class RequisicaoService {

    public function checarRequisicoesAbertas($param) {
        TTransaction::open('requisicao');
        $requisicoes = Agendamento::where('status', '=', 2)->load();
        if ($requisicoes) {
            $data_atual = date('Y-m-d');
            $hora_atual = date('H:i');
            foreach ($requisicoes as $requisicao) {
                $data_limite = $this->obter_data_limite($requisicao->data_viagem);
                if (($data_atual > $data_limite) || ($data_atual == $data_limite && $hora_atual > '17:00')) {
                    $requisicao->status = 5;
                    $requisicao->store();
                }
            }
        }
        TTransaction::close();
    }

    public static function obter_data_limite($data) {
        $flag = true;
        $data2 = $data;
        while ($flag) {
            $data_aux = date('Y-m-d', strtotime($data2 . '+1 day'));
            $dia_semana = date('w', strtotime($data_aux));
            #0 = domingo 6=sábado
            if ($dia_semana != 0 && $dia_semana != 6) {
                $feriados = Feriado::where('data_feriado', '=', $data_aux)->load();
                if (count($feriados) == 0) {
                    $flag = false;
                }
            }
            $data2 = $data_aux;
        }
        return $data_aux;
    }

}
