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
use Adianti\Widget\Wrapper\TDBUniqueSearch;
use Adianti\Wrapper\BootstrapFormBuilder;

class AgendamentoForm extends TPage
{
    private $form;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_agendamento');
        $this->form->setFormTitle('Agendamento');

        $id = new TEntry('id');
        $data_agendamento = new TDateTime('data_agendamento');
        $data_viagem = new TDateTime('data_viagem');
        $destino = new TEntry('destino');
        $motivo = new TEntry('motivo');
        $status = new THidden('status');
        $system_user_id = new THidden('system_user_id');
        $veiculo_id = new TDBUniqueSearch('veiculo_id', 'banco', 'Veiculo', 'id', 'placa', 'placa');
        $motorista_id = new TDBUniqueSearch('motorista_id', 'banco', 'Motorista', 'id', 'nome', 'nome');
        $ramal = new TEntry('ramal');

        $id->setEditable(false);
        $data_agendamento->setEditable(false);
        $data_agendamento->setMask('dd/mm/yyyy hh:ii');
        $data_agendamento->setDatabaseMask('yyyy-mm-dd hh:ii');
        $data_viagem->setMask('dd/mm/yyyy hh:ii');
        $data_viagem->setDatabaseMask('yyyy-mm-dd hh:ii');
        $veiculo_id->setMinLength(0);
        $motorista_id->setMinLength(0);
        $veiculo_id->setMask('{modelo}-{placa}');
        $ramal->setMask('999');

        $data_agendamento->setValue(date('d/m/Y H:i'));
        // $data_viagem->setValue(date('d/m/Y H:i'));

        $data_viagem->addValidation('Data viagem', new TRequiredValidator);
        $destino->addValidation('Destino', new TRequiredValidator);
        $veiculo_id->addValidation('Veículo', new TRequiredValidator);
        $motorista_id->addValidation('Motorista', new TRequiredValidator);
        $motivo->addValidation('Motivo', new TRequiredValidator);
        $ramal->addValidation('Ramal', new TRequiredValidator);

        $this->form->addFields([$system_user_id, $status]);
        $this->form->addFields([new TLabel('ID')], [$id], [new TLabel('Data agendamento')], [$data_agendamento]);
        $this->form->addFields([new TLabel('Data viagem', 'red')], [$data_viagem], [new TLabel('Ramal', 'red')], [$ramal]);
        $this->form->addFields([new TLabel('Destino', 'red')], [$destino]);
        $this->form->addFields([new TLabel('Motivo', 'red')], [$motivo]);
        $this->form->addFields([new TLabel('Motorista', 'red')], [$motorista_id]);
        $this->form->addFields([new TLabel('Veículo', 'red')], [$veiculo_id]);

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
