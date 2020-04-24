<?php

use Adianti\Base\AdiantiStandardFormTrait;
use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Wrapper\BootstrapFormBuilder;

class MotoristaForm extends TPage
{
    protected $form;

    use AdiantiStandardFormTrait;

    public function __construct()
    {
        parent::__construct();
        $this->setDatabase('banco');
        $this->setActiveRecord('Motorista');
        $this->setAfterSaveAction(new TAction(['MotoristaList', 'onReload']));

        $this->form = new BootstrapFormBuilder('form_motorista');
        $this->form->setFormTitle('Motorista');

        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $matricula = new TEntry('matricula');
        $cnh = new TEntry('cnh');
        $categoria_cnh = new TEntry('categoria_cnh');
        $portaria_autorizacao = new TEntry('portaria_autorizacao');
        $data_vencimento_cnh = new TDate('data_vencimento_cnh');

        $id->setEditable(false);
        $data_vencimento_cnh->setMask('dd/mm/yyyy');
        $data_vencimento_cnh->setDatabaseMask('yyyy-mm-dd');
        $categoria_cnh->setMask('A');
        $categoria_cnh->forceUpperCase();

        $nome->addValidation('Nome', new TRequiredValidator);

        $this->form->addFields([new TLabel('ID')], [$id], [new TLabel('Matrícula')], [$matricula]);
        $this->form->addFields([new TLabel('Nome', 'red')], [$nome]);
        $this->form->addFields([new TLabel('Portaria autorização')], [$portaria_autorizacao]);
        $this->form->addFields([new TLabel('CNH')], [$cnh], [new TLabel('Categoria CNH')], [$categoria_cnh]);
        $this->form->addFields([new TLabel('Data vencimento CNH')], [$data_vencimento_cnh]);

        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        $this->form->addAction('Limpar', new TAction([$this, 'onClear']), 'fa:eraser red');

        parent::add($this->form);
    }
}
