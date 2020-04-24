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
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Wrapper\BootstrapFormBuilder;

class AgendamentoList extends TPage
{
    protected $form, $datagrid, $pageNavigation;

    use AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();
        $this->setDatabase('banco');
        $this->setActiveRecord('Agendamento');
        $this->setLimit(5);
        $this->setDefaultOrder('id', 'desc');
        $this->addFilterField('id', 'like', 'id');

        $this->form = new BootstrapFormBuilder('form_agendamento_search');
        $this->form->setFormTitle('Agendamento');

        $id = new TEntry('id');

        $this->form->addFields([new TLabel('ID')], [$id]);

        $this->form->addAction('Procurar', new TAction([$this, 'onSearch']), 'fa:search green');
        $this->form->addAction('Cadastrar', new TAction(['AgendamentoForm', 'onEdit']), 'fa:plus blue');

        $this->datagrid = new TDataGrid;
        $this->datagrid->style = 'width:100%;overflow-x:auto';

        $col_id = new TDataGridColumn('id', 'ID', 'left');
        $col_data_viagem = new TDataGridColumn('data_viagem', 'Data viagem', 'left');
        $col_destino = new TDataGridColumn('destino', 'Destino', 'left');
        $col_motivo = new TDataGridColumn('motivo', 'Motivo', 'left');
        $col_motorista_id = new TDataGridColumn('{motorista_nome}', 'Motorista', 'left');
        $col_veiculo_id = new TDataGridColumn('{veiculo->modelo}-{veiculo-placa}', 'Veículo', 'left');
        $col_status = new TDataGridColumn('status', 'Status', 'left');

        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_data_viagem);
        $this->datagrid->addColumn($col_destino);
        $this->datagrid->addColumn($col_motivo);
        $this->datagrid->addColumn($col_motorista_id);
        $this->datagrid->addColumn($col_veiculo_id);
        $this->datagrid->addColumn($col_status);

        $acao_editar = new TDataGridAction(['AgendamentoForm', 'onEdit'], ['id' => '{id}']);
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
