<?php

class DbEnviarDrex extends TWindow
{
    protected $form;
    private $formFields = [];
    private static $database = 'dbimovel';
    private static $activeRecord = 'Dbfinanciamentos';
    private static $primaryKey = 'id';
    private static $formName = 'form_DbAssumirFinanciador';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        parent::setSize(0.8, null);
        parent::setTitle("Envio de Drex");
        parent::setProperty('class', 'window_modal');

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Envio de Drex");


        $id = new TEntry('id');
        $dbcontrato_id = new TDBCombo('dbcontrato_id', 'dbimovel', 'Dbcontrato', 'id', '{cnome}','cnome asc'  );
        $dbfinanciador_id = new TDBCombo('dbfinanciador_id', 'dbimovel', 'Dbfinanciador', 'id', '{cnome}','cnome asc'  );
        $dbfiador_id = new TDBCombo('dbfiador_id', 'dbimovel', 'Dbfiador', 'id', '{cnome}','cnome asc'  );
        $dbcomprador_id = new TDBCombo('dbcomprador_id', 'dbimovel', 'Dbcomprador', 'id', '{cnome}','cnome asc'  );
        $nindex = new TEntry('nindex');
        $cnome = new TEntry('cnome');
        $nvalor = new TNumeric('nvalor', '2', ',', '.' );

        $dbfinanciador_id->addValidation("Financiador", new TRequiredValidator()); 

        $cnome->setMaxLength(200);
        $dbfiador_id->enableSearch();
        $dbcontrato_id->enableSearch();
        $dbcomprador_id->enableSearch();
        $dbfinanciador_id->enableSearch();

        $id->setSize(100);
        $cnome->setSize('100%');
        $nindex->setSize('100%');
        $nvalor->setSize('100%');
        $dbfiador_id->setSize('100%');
        $dbcontrato_id->setSize('100%');
        $dbcomprador_id->setSize('100%');
        $dbfinanciador_id->setSize('100%');

        $id->setEditable(false);
        $cnome->setEditable(false);
        $nindex->setEditable(false);
        $nvalor->setEditable(false);
        $dbfiador_id->setEditable(false);
        $dbcontrato_id->setEditable(false);
        $dbcomprador_id->setEditable(false);
        $dbfinanciador_id->setEditable(false);

        $row1 = $this->form->addFields([new TLabel("INFORMAÇÃO:", null, '14px', 'B')],[new TLabel("O DREX será enviado da carteira do FINANCIADOR para o COMPRADOR, gerando a divida em contrato que deve paga a posteriore.", null, '14px', null)]);
        $row2 = $this->form->addFields([new TLabel("Id:", null, '14px', null)],[$id],[new TLabel("Contrato:", null, '14px', null)],[$dbcontrato_id]);
        $row3 = $this->form->addFields([new TLabel("Financiador:", null, '14px', null)],[$dbfinanciador_id],[new TLabel("Fiador:", null, '14px', null)],[$dbfiador_id]);
        $row4 = $this->form->addFields([new TLabel("Comprador:", null, '14px', null)],[$dbcomprador_id],[new TLabel("Índice/NFT:", null, '14px', null)],[$nindex]);
        $row5 = $this->form->addFields([new TLabel("Nome do imóvel:", null, '14px', null)],[$cnome],[new TLabel("Valor:", null, '14px', null)],[$nvalor]);

        // create the form actions
        $btn_onsave = $this->form->addAction("Enviar Drex", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        parent::add($this->form);

    }

    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new Dbfinanciamentos(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object->store(); // save the object 

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $w3 = new C9Web3();

            $result=  $w3->EnviarDrex($object->Dbfinanciador->cchaveprivada,
                              $object->Dbfinanciador->cpublickey,
                              $object->nindex);
            if ($result===false)
            {
                new TMessage('error','Erro enviando DREX do financiador para o portador');
                $this->form->setData( $this->form->getData() ); // keep form data
                TTransaction::rollback(); // undo all pending operations
            }else{

            $object->store(); // save the object 

            $it = new Dbinteracoes();
            $it->dbfinanciamento_id = $object->id;
            $it->ctipo='EnvioDeDrex';
            $it->nbloco=$result[1];
            $it->cevento='Sem detalhes';
            $it->chash=$result[2];
            $it->store();

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            new TMessage('info', "Registro salvo", $messageAction); 

            }

                TWindow::closeWindow(parent::getId()); 

        }
        catch (Exception $e) // in case of exception
        {
            //</catchAutoCode> 

            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }

    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open(self::$database); // open a transaction

                $object = new Dbfinanciamentos($key); // instantiates the Active Record 

                $this->form->setData($object); // fill the form 

                TTransaction::close(); // close the transaction 
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(true);

    }

    public function onShow($param = null)
    {

    } 

    public static function getFormName()
    {
        return self::$formName;
    }

}

