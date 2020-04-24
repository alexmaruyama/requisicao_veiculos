<?php

use Adianti\Database\TRecord;

class Motorista extends TRecord
{
    const TABLENAME = 'motorista';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial';

    public function __construct($id = null)
    {
        parent::__construct($id);

        parent::addAttribute('nome');
        parent::addAttribute('matricula');
        parent::addAttribute('cnh');
        parent::addAttribute('categoria_cnh');
        parent::addAttribute('portaria_autorizacao');
        parent::addAttribute('data_vencimento_cnh');
    }
}
