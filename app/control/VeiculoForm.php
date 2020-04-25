<?php

use Adianti\Base\AdiantiStandardFormTrait;
use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Wrapper\BootstrapFormBuilder;

class VeiculoForm extends TPage
{
    protected $form;

    use AdiantiStandardFormTrait;

    public function __construct()
    {
        parent::__construct();
        $this->setDatabase('banco');
        $this->setActiveRecord('Veiculo');
        $this->setAfterSaveAction(new TAction(['VeiculoList', 'onReload']));

        $this->form = new BootstrapFormBuilder('form_veiculo');
        $this->form->setFormTitle('Veículo');

        $id = new TEntry('id');
        $cor = new TEntry('cor');
        $marca = new TEntry('marca');
        $modelo = new TEntry('modelo');
        $placa = new TEntry('placa');

        $id->setEditable(false);
        $placa->setMask('SSS 9999');
        $placa->forceUpperCase();

        $this->form->addFields([new TLabel('ID')], [$id]);
        $this->form->addFields([new TLabel('Modelo')], [$modelo], [new TLabel('Marca')], [$marca]);
        $this->form->addFields([new TLabel('Placa')], [$placa], [new TLabel('Cor')], [$cor]);

        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        $this->form->addAction('Limpar', new TAction([$this, 'onClear']), 'fa:eraser red');

        parent::add($this->form);
    }
}
