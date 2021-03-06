<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TDateTime;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\THidden;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TTime;
use Adianti\Wrapper\BootstrapFormBuilder;

class AgendamentoEncerrarForm extends TPage
{
    private $form;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_agendamento_encerrar');
        $this->form->setFormTitle('Agendamento - Encerrar');

        $id = new TEntry('id');
        $data_agendamento = new TDateTime('data_agendamento');
        $data_viagem = new TDateTime('data_viagem');
        $destino = new TEntry('destino');
        $motivo = new TEntry('motivo');
        $status = new THidden('status');
        $system_user_id = new THidden('system_user_id');
        $veiculo_id = new THidden('veiculo_id');
        $motorista_id = new THidden('motorista_id');
        $system_user_name = new TEntry('system_user_name');
        $veiculo_nome = new TEntry('veiculo_nome');
        $motorista_nome = new TEntry('motorista_nome');
        $km_saida = new TEntry('km_saida');
        $km_chegada = new TEntry('km_chegada');
        $hora_saida = new TTime('hora_saida');
        $hora_chegada = new TTime('hora_chegada');

        $id->setEditable(false);
        $data_viagem->setEditable(false);
        $destino->setEditable(false);
        $motivo->setEditable(false);
        $data_agendamento->setEditable(false);
        $motorista_nome->setEditable(false);
        $veiculo_nome->setEditable(false);
        $system_user_name->setEditable(false);

        $km_chegada->addValidation('Km chegada', new TRequiredValidator);
        $km_saida->addValidation('Km saída', new TRequiredValidator);
        $hora_chegada->addValidation('Hora chegada', new TRequiredValidator);
        $hora_saida->addValidation('Hora saída', new TRequiredValidator);

        $data_agendamento->setMask('dd/mm/yyyy hh:ii');
        $data_agendamento->setDatabaseMask('yyyy-mm-dd hh:ii');
        $data_viagem->setMask('dd/mm/yyyy hh:ii');
        $data_viagem->setDatabaseMask('yyyy-mm-dd hh:ii');

        $this->form->addFields([$status, $system_user_id, $motorista_id, $veiculo_id]);
        $this->form->addFields([new TLabel('ID')], [$id], [new TLabel('Data agendamento')], [$data_agendamento]);
        $this->form->addFields([new TLabel('Data viagem')], [$data_viagem]);
        $this->form->addFields([new TLabel('Destino')], [$destino]);
        $this->form->addFields([new TLabel('Motivo')], [$motivo]);
        $this->form->addFields([new TLabel('Motorista')], [$motorista_nome]);
        $this->form->addFields([new TLabel('Veículo')], [$veiculo_nome]);
        $this->form->addFields([new TLabel('Usuário')], [$system_user_name]);
        $this->form->addFields([new TLabel('Km saída')], [$km_saida], [new TLabel('Hora saída')], [$hora_saida]);
        $this->form->addFields([new TLabel('Km chegada')], [$km_chegada], [new TLabel('Hora chegada')], [$hora_chegada]);

        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        $this->form->addAction('Limpar', new TAction([$this, 'onClear']), 'fa:eraser red');

        parent::add($this->form);
    }

    public function onClear()
    {
        $this->form->clear(true);
        $objeto = new stdClass;
        $objeto->system_user_id = TSession::getValue('userid');
        $objeto->status = 0;
        TForm::sendData('form_agendamento', $objeto);
    }

    public function onSave($param)
    {
        try {
            $this->form->validate();
            $data = $this->form->getData();
            TTransaction::open('banco');

            $agendamento = new Agendamento();
            $agendamento->fromArray((array) $data);
            $agendamento->status = 3;
            $agendamento->store();

            TTransaction::close();
            $acao = new TAction(['AgendamentoList', 'onReload']);
            new TMessage('info', 'Registro salvo com sucesso', $acao);
        } catch (Exception $ex) {
            new TMessage('error', $ex->getMessage());
            TTransaction::rollback();
        }
    }

    public function onEdit($param)
    {
        if (isset($param['id'])) {
            try {
                TTransaction::open('banco');

                $agendamento = new Agendamento($param['id']);
                $agendamento->veiculo_nome = $agendamento->veiculo->modelo . '(' . $agendamento->veiculo->placa . ')';
                $agendamento->motorista_nome = $agendamento->motorista->nome;
                $agendamento->system_user_name = $agendamento->system_user->name;
                $this->form->setData($agendamento);

                TTransaction::close();
            } catch (Exception $ex) {
                new TMessage('error', $ex->getMessage());
            }
        } else {
            $this->onClear();
        }
    }
}
