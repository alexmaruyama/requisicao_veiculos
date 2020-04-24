<?php

use Adianti\Database\TRecord;

class Feriado extends TRecord
{
    const TABLENAME = 'feriado';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial';

    public function __construct($id = null)
    {
        parent::__construct($id);

        parent::addAttribute('nome');
        parent::addAttribute('data_feriado');
    }
}
