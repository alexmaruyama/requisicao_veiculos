<?php

use Adianti\Base\AdiantiStandardFormTrait;
use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Wrapper\BootstrapFormBuilder;

class FeriadoForm extends TPage
{
    protected $form;

    use AdiantiStandardFormTrait;

    public function __construct()
    {
        parent::__construct();
        $this->setDatabase('banco');
        $this->setActiveRecord('Feriado');
        $this->setAfterSaveAction(new TAction(['FeriadoList', 'onReload']));

        $this->form = new BootstrapFormBuilder('form_feriado');
        $this->form->setFormTitle('Feriado');

        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $data_feriado = new TDate('data_feriado');

        $id->setEditable(false);
        $data_feriado->setMask('dd/mm/yyyy');
        $data_feriado->setDatabaseMask('yyyy-mm-dd');

        $nome->addValidation('Nome', new TRequiredValidator);

        $this->form->addFields([new TLabel('ID')], [$id]);
        $this->form->addFields([new TLabel('Nome', 'red')], [$nome]);
        $this->form->addFields([new TLabel('Data feriado')], [$data_feriado]);

        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        $this->form->addAction('Limpar', new TAction([$this, 'onClear']), 'fa:eraser red');

        parent::add($this->form);
    }
}
