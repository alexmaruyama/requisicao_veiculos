<?php

use Adianti\Control\TAction;
use Adianti\Control\TWindow;
use Adianti\Database\TTransaction;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TDateTime;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TTime;
use Adianti\Wrapper\BootstrapFormBuilder;

class AgendamentoFormView extends TWindow
{
    private $form;

    public function __construct()
    {
        parent::__construct();
        parent::setSize(.8, null);
        parent::setTitle('Agendamento - Visualizar');

        $this->form = new BootstrapFormBuilder('form_agendamento_form_view');

        $id = new TEntry('id');
        $data_agendamento = new TDateTime('data_agendamento');
        $data_viagem = new TDateTime('data_viagem');
        $destino = new TEntry('destino');
        $motivo = new TEntry('motivo');
        $system_user_name = new TEntry('system_user_name');
        $veiculo_nome = new TEntry('veiculo_nome');
        $motorista_nome = new TEntry('motorista_nome');
        $km_saida = new TEntry('km_saida');
        $km_chegada = new TEntry('km_chegada');
        $hora_saida = new TTime('hora_saida');
        $hora_chegada = new TTime('hora_chegada');
        $motivo_transporte = new TEntry('motivo_transporte');

        $id->setEditable(false);
        $data_viagem->setEditable(false);
        $destino->setEditable(false);
        $motivo->setEditable(false);
        $data_agendamento->setEditable(false);
        $motorista_nome->setEditable(false);
        $veiculo_nome->setEditable(false);
        $system_user_name->setEditable(false);
        $km_saida->setEditable(false);
        $km_chegada->setEditable(false);
        $hora_chegada->setEditable(false);
        $hora_saida->setEditable(false);

        $this->form->addFields([new TLabel('ID')], [$id], [new TLabel('Data agendamento')], [$data_agendamento]);
        $this->form->addFields([new TLabel('Data viagem')], [$data_viagem]);
        $this->form->addFields([new TLabel('Destino')], [$destino]);
        $this->form->addFields([new TLabel('Motivo')], [$motivo]);
        $this->form->addFields([new TLabel('Motorista')], [$motorista_nome]);
        $this->form->addFields([new TLabel('Veículo')], [$veiculo_nome]);
        $this->form->addFields([new TLabel('Usuário')], [$system_user_name]);
        $this->form->addFields([new TLabel('Km saída')], [$km_saida], [new TLabel('Hora saída')], [$hora_saida]);
        $this->form->addFields([new TLabel('Km chegada')], [$km_chegada], [new TLabel('Hora chegada')], [$hora_chegada]);
        $this->form->addFields([new TLabel('Motivo transporte')], [$motivo_transporte]);

        parent::add($this->form);
    }

    public function onLoad($param)
    {
        if (isset($param['id'])) {
            try {
                TTransaction::open('banco');

                $agendamento = new Agendamento($param['id']);
                $agendamento->veiculo_nome = $agendamento->veiculo->modelo . '(' . $agendamento->veiculo->placa . ')';
                $agendamento->motorista_nome = $agendamento->motorista->nome;
                $agendamento->system_user_name = $agendamento->system_user->name;
                $agendamento->data_agendamento = TDateTime::convertToMask($agendamento->data_agendamento, 'yyyy-mm-dd hh:ii', 'dd/mm/yyyy hh:ii');
                $agendamento->data_viagem = TDateTime::convertToMask($agendamento->data_viagem, 'yyyy-mm-dd hh:ii', 'dd/mm/yyyy hh:ii');
                $this->form->setData($agendamento);

                TTransaction::close();
            } catch (Exception $ex) {
                new TMessage('error', $ex->getMessage());
            }
        }
    }
}
