<?php

use Adianti\Database\TRecord;

class Agendamento extends TRecord
{
    const TABLENAME = 'agendamento';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial';

    public function __construct($id = null)
    {
        parent::__construct($id);

        parent::addAttribute('data_agendamento');
        parent::addAttribute('data_viagem');
        parent::addAttribute('destino');
        parent::addAttribute('motivo');
        parent::addAttribute('status');
        parent::addAttribute('system_user_id');
        parent::addAttribute('km_saida');
        parent::addAttribute('km_chegada');
        parent::addAttribute('hora_saida');
        parent::addAttribute('hora_chegada');
        parent::addAttribute('motivo_transporte');
        parent::addAttribute('veiculo_id');
        parent::addAttribute('motorista_id');
        parent::addAttribute('ramal');
    }

    public function get_motorista()
    {
        return Motorista::find($this->motorista_id);
    }

    public function get_veiculo()
    {
        return Veiculo::find($this->veiculo_id);
    }

    public function get_system_user()
    {
        return SystemUser::find($this->system_user_id);
    }
}
