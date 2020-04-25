<?php

use Adianti\Base\AdiantiStandardListTrait;
use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Wrapper\BootstrapFormBuilder;

class VeiculoList extends TPage
{
    protected $form, $datagrid, $pageNavigation;

    use AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();
        $this->setDatabase('banco');
        $this->setActiveRecord('Veiculo');
        $this->setLimit(5);
        $this->setDefaultOrder('id', 'desc');
        $this->addFilterField('placa', 'like', 'placa');

        $this->form = new BootstrapFormBuilder('form_veiculo_search');
        $this->form->setFormTitle('Veículo');

        $placa = new TEntry('placa');

        $this->form->addFields([new TLabel('Placa')], [$placa]);

        $this->form->addAction('Procurar', new TAction([$this, 'onSearch']), 'fa:search green');
        $this->form->addAction('Cadastrar', new TAction(['VeiculoForm', 'onEdit']), 'fa:plus blue');

        $this->datagrid = new TDataGrid;
        $this->datagrid->style = 'width:100%';

        $col_id = new TDataGridColumn('id', 'ID', 'left');
        $col_modelo = new TDataGridColumn('modelo', 'Modelo', 'left');
        $col_placa = new TDataGridColumn('placa', 'Placa', 'left');

        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_modelo);
        $this->datagrid->addColumn($col_placa);

        $acao_editar = new TDataGridAction(['VeiculoForm', 'onEdit'], ['id' => '{id}']);
        $acao_excluir = new TDataGridAction([$this, 'onDelete'], ['id' => '{id}']);

        $this->datagrid->addAction($acao_editar, 'Editar', 'fa:edit blue');
        $this->datagrid->addAction($acao_excluir, 'Excluir', 'fa:trash red');

        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->enableCounters();

        $panel = TPanelGroup::pack('', $this->datagrid, $this->pageNavigation);

        $vbox = new TVBox;
        $vbox->style = 'width:100%';
        $vbox->add($this->form);
        $vbox->add($panel);

        parent::add($vbox);
    }
}
