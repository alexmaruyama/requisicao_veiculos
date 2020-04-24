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

class FeriadoList extends TPage
{
    protected $form, $datagrid, $pageNavigation;

    use AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();
        $this->setDatabase('banco');
        $this->setActiveRecord('Feriado');
        $this->setLimit(5);
        $this->setDefaultOrder('id', 'desc');
        $this->addFilterField('nome', 'like', 'nome');

        $this->form = new BootstrapFormBuilder('form_feriado_search');
        $this->form->setFormTitle('Feriado');

        $nome = new TEntry('nome');

        $this->form->addFields([new TLabel('Nome')], [$nome]);

        $this->form->addAction('Procurar', new TAction([$this, 'onSearch']), 'fa:search green');
        $this->form->addAction('Cadastrar', new TAction(['FeriadoForm', 'onEdit']), 'fa:plus blue');

        $this->datagrid = new TDataGrid;
        $this->datagrid->style = 'width:100%';

        $col_id = new TDataGridColumn('id', 'ID', 'left');
        $col_nome = new TDataGridColumn('nome', 'Nome', 'left');
        $col_data_feriado = new TDataGridColumn('data_feriado', 'Data feriado', 'left');

        $col_data_feriado->setTransformer(function ($valor) {
            return TDate::convertToMask($valor, 'yyyy-mm-dd', 'dd/mm/yyyy');
        });

        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_nome);
        $this->datagrid->addColumn($col_data_feriado);

        $acao_editar = new TDataGridAction(['FeriadoForm', 'onEdit'], ['id' => '{id}']);
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
