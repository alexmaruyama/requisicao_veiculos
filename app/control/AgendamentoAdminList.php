<?php

use Adianti\Base\AdiantiStandardListTrait;
use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Registry\TSession;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Form\TDateTime;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;

class AgendamentoAdminList extends TPage
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

        $this->form = new BootstrapFormBuilder('form_agendamento_admin_search');
        $this->form->setFormTitle('Agendamento - Administrar');

        $id = new TEntry('id');

        $this->form->addFields([new TLabel('ID')], [$id]);

        $this->form->setData(TSession::getValue(__CLASS__ . '_filter_data'));

        $this->form->addAction('Procurar', new TAction([$this, 'onSearch']), 'fa:search green');
        $this->form->addActionLink('Cadastrar', new TAction(['AgendamentoAdminForm', 'onEdit']), 'fa:plus blue');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width:100%;overflow-x:auto';

        $col_id = new TDataGridColumn('id', 'ID', 'left');
        $col_data_viagem = new TDataGridColumn('data_viagem', 'Data viagem', 'left');
        $col_destino = new TDataGridColumn('destino', 'Destino', 'left');
        $col_motivo = new TDataGridColumn('motivo', 'Motivo', 'left');
        $col_motorista_id = new TDataGridColumn('{motorista->nome}', 'Motorista', 'left');
        $col_veiculo_id = new TDataGridColumn('{veiculo->modelo}({veiculo->placa})', 'Veículo', 'left');
        $col_status = new TDataGridColumn('status', 'Status', 'left');
        $col_system_user_id = new TDataGridColumn('{system_user->name}', 'Usuário', 'left');

        $col_status->setTransformer(function ($valor) {
            $status = [0 => 'AGUARDANDO', 1 => 'LIBERADO', 2 => 'NEGADO', 3 => 'FINALIZADO', 4 => 'TRAVADO'];
            $cores = [0 => 'yellow', 'green', 'red', 'white', 'orange'];
            return '<span style="background-color:' . $cores[$valor] . ';font-weight:bold">' . $status[$valor] . '</span>';
        });

        $col_data_viagem->setTransformer(function ($valor) {
            return TDateTime::convertToMask($valor, 'yyyy-mm-dd hh:ii', 'dd/mm/yyyy hh:ii');
        });

        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_system_user_id);
        $this->datagrid->addColumn($col_data_viagem);
        $this->datagrid->addColumn($col_destino);
        $this->datagrid->addColumn($col_motivo);
        $this->datagrid->addColumn($col_motorista_id);
        $this->datagrid->addColumn($col_veiculo_id);
        $this->datagrid->addColumn($col_status);

        $acao_editar = new TDataGridAction(['AgendamentoAdminForm', 'onEdit'], ['id' => '{id}']);
        $acao_visualizar = new TDataGridAction(['AgendamentoFormView', 'onLoad'], ['id' => '{id}']);

        $acao_editar->setDisplayCondition(function ($objeto) {
            return $objeto->status > 0 ? false : true;
        });

        $acao_visualizar->setDisplayCondition(function ($objeto) {
            return $objeto->status;
        });

        $acao_visualizar->setDisplayCondition(function ($objeto) {
            return $objeto->status > 1 ? true : false;
        });

        $this->datagrid->addAction($acao_editar, 'Editar', 'fa:edit blue');
        $this->datagrid->addAction($acao_visualizar, 'Visualizar', 'fa:search green');

        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->enableCounters();

        $panel = TPanelGroup::pack('', $this->datagrid, $this->pageNavigation);

        $vbox = new TVBox;
        $vbox->style = 'width:100%;overflow-x:auto';
        $vbox->add($this->form);
        $vbox->add($panel);

        parent::add($vbox);
    }
}
