<?php

use Adianti\Database\TRecord;

class Veiculo extends TRecord
{
    const TABLENAME = 'veiculo';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial';

    public function __construct($id = null)
    {
        parent::__construct($id);

        parent::addAttribute('cor');
        parent::addAttribute('marca');
        parent::addAttribute('modelo');
        parent::addAttribute('placa');
    }
}
