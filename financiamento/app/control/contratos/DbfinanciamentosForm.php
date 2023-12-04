<?php

class DbfinanciamentosForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'dbimovel';
    private static $activeRecord = 'Dbfinanciamentos';
    private static $primaryKey = 'id';
    private static $formName = 'form_DbfinanciamentosForm';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Financiamentos");


        $id = new TEntry('id');
        $nindex = new TEntry('nindex');
        $dbcontrato_id = new TDBCombo('dbcontrato_id', 'dbimovel', 'Dbcontrato', 'id', '{cnome}','cnome asc'  );
        $dbcomprador_id = new TDBCombo('dbcomprador_id', 'dbimovel', 'Dbcomprador', 'id', '{cnome}','cnome asc'  );
        $dbfiador_id = new TDBCombo('dbfiador_id', 'dbimovel', 'Dbfiador', 'id', '{cnome}','cnome asc'  );
        $cnome = new TEntry('cnome');
        $nvalor = new TNumeric('nvalor', '2', ',', '.' );
        $ccep = new TEntry('ccep');
        $cestado = new TEntry('cestado');
        $ccidade = new TEntry('ccidade');
        $cendereco = new TEntry('cendereco');
        $cnumero = new TEntry('cnumero');
        $cbairro = new TEntry('cbairro');

        $dbcontrato_id->addValidation("Contrato", new TRequiredValidator()); 
        $dbcomprador_id->addValidation("Comprador", new TRequiredValidator()); 
        $dbfiador_id->addValidation("Fiador", new TRequiredValidator()); 
        $cnome->addValidation("Nome do imóvel", new TRequiredValidator()); 
        $nvalor->addValidation("Valor do imóvel", new TRequiredValidator()); 

        $id->setEditable(false);
        $nindex->setEditable(false);

        $dbfiador_id->setDefaultOption(false);
        $dbcontrato_id->setDefaultOption(false);
        $dbcomprador_id->setDefaultOption(false);

        $dbfiador_id->enableSearch();
        $dbcontrato_id->enableSearch();
        $dbcomprador_id->enableSearch();

        $ccep->setMaxLength(20);
        $cnome->setMaxLength(200);
        $cestado->setMaxLength(2);
        $cnumero->setMaxLength(30);
        $ccidade->setMaxLength(200);
        $cbairro->setMaxLength(200);
        $cendereco->setMaxLength(200);

        $id->setSize(100);
        $ccep->setSize('100%');
        $cnome->setSize('100%');
        $nindex->setSize('100%');
        $nvalor->setSize('100%');
        $cestado->setSize('100%');
        $ccidade->setSize('100%');
        $cnumero->setSize('100%');
        $cbairro->setSize('100%');
        $cendereco->setSize('100%');
        $dbfiador_id->setSize('100%');
        $dbcontrato_id->setSize('100%');
        $dbcomprador_id->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null)],[$id],[new TLabel("Índice/NFT:", null, '14px', null)],[$nindex]);
        $row2 = $this->form->addFields([new TLabel("Contrato:", null, '14px', null)],[$dbcontrato_id],[],[]);
        $row3 = $this->form->addFields([new TLabel("Comprador:", null, '14px', null)],[$dbcomprador_id],[new TLabel("Fiador:", null, '14px', null)],[$dbfiador_id]);
        $row4 = $this->form->addFields([new TLabel("Nome do imóvel:", null, '14px', null)],[$cnome],[new TLabel("Valor:", null, '14px', null)],[$nvalor]);
        $row5 = $this->form->addFields([new TLabel("Cep:", null, '14px', null)],[$ccep],[new TLabel("Estado:", null, '14px', null)],[$cestado]);
        $row6 = $this->form->addFields([new TLabel("Cidade:", null, '14px', null)],[$ccidade],[new TLabel("Endereço:", null, '14px', null)],[$cendereco]);
        $row7 = $this->form->addFields([new TLabel("Número:", null, '14px', null)],[$cnumero],[new TLabel("Bairro:", null, '14px', null)],[$cbairro]);

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['DbfinanciamentosList', 'onShow']), 'fas:arrow-left #000000');
        $this->btn_onshow = $btn_onshow;

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Contratos","Financiamentos"]));
        }
        $container->add($this->form);

        parent::add($container);

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

            /*new TMessage('info',$object->Dbfiador->cnome);
            new TMessage('info',$object->Dbcomprador->cnome);
            new TMessage('info',$object->Dbfinanciador->cnome);

            new TMessage('info',$object->Dbcontrato->cnome);
            new TMessage('info',$object->Dbcontrato->cchaveprivada);
            new TMessage('info',$object->Dbcontrato->cpublickey);
            */
            $result=false;

            if (empty($object->id)) // Se está cadastrando um novo então o ID estará vazio
            {
                $w3 = new C9Web3();
                //$tx = $w3->CriarNFT($privateKey,$fromAddress,$agente_comprador,$agente_garantidor,$valorDoBem);
                $result=  $w3->CriarNFT($object->Dbcontrato->cchaveprivada,
                                  $object->Dbcontrato->cpublickey,
                                  $object->Dbcomprador->cpublickey,
                                  $object->Dbfiador->cpublickey,
                                  $object->nvalor);
                if ($result===false)
                {
                    new TMessage('error','Erro criando NFT');
                    $this->form->setData( $this->form->getData() ); // keep form data
                    TTransaction::rollback(); // undo all pending operations
                }else{
                    $object->nindex=$result[0];
                }
            }

            $object->store(); // save the object 

            $it = new Dbinteracoes();
            $it->dbfinanciamento_id = $object->id;
            $it->ctipo='Mint';
            $it->nbloco=$result[1];
            $it->cevento='Sem detalhes';
            $it->chash=$result[2];
            $it->store();

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('DbfinanciamentosList', 'onShow', $loadPageParam); 

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

