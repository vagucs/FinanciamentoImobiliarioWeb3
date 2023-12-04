<?php

class DbinteracoesForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'dbimovel';
    private static $activeRecord = 'Dbinteracoes';
    private static $primaryKey = 'id';
    private static $formName = 'form_DbinteracoesForm';

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
        $this->form->setFormTitle("Interações");


        $id = new TEntry('id');
        $dbfinanciamento_id = new TDBCombo('dbfinanciamento_id', 'dbimovel', 'Dbfinanciamentos', 'id', '{cnome}','cnome asc'  );
        $ddatahora = new TDateTime('ddatahora');
        $ctipo = new TEntry('ctipo');
        $nbloco = new TEntry('nbloco');
        $button_ver_no_blockchain = new TButton('button_ver_no_blockchain');
        $chash = new TEntry('chash');
        $cevento = new TText('cevento');


        $dbfinanciamento_id->enableSearch();
        $ddatahora->setMask('dd/mm/yyyy hh:ii');
        $ddatahora->setValue('current_timestamp');
        $ddatahora->setDatabaseMask('yyyy-mm-dd hh:ii');
        $button_ver_no_blockchain->setAction(new TAction(['DbinteracoesForm', 'VerBC'],['chash' => 'chash']), "Ver no Blockchain");
        $button_ver_no_blockchain->addStyleClass('btn-default');
        $button_ver_no_blockchain->setImage('fas:search #000000');
        $id->setEditable(false);
        $ctipo->setEditable(false);
        $chash->setEditable(false);
        $nbloco->setEditable(false);
        $cevento->setEditable(false);
        $dbfinanciamento_id->setEditable(false);

        $id->setSize(100);
        $ctipo->setSize('100%');
        $chash->setSize('100%');
        $ddatahora->setSize(150);
        $nbloco->setSize('100%');
        $cevento->setSize('100%', 70);
        $dbfinanciamento_id->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null)],[$id],[],[]);
        $row2 = $this->form->addFields([new TLabel("Financiamento:", null, '14px', null)],[$dbfinanciamento_id],[new TLabel("Hora do evento:", null, '14px', null)],[$ddatahora]);
        $row3 = $this->form->addFields([new TLabel("Tipo:", null, '14px', null)],[$ctipo],[new TLabel("Bloco:", null, '14px', null)],[$nbloco]);
        $row4 = $this->form->addFields([new TLabel("Hash:", null, '14px', null)],[$button_ver_no_blockchain,$chash]);
        $row5 = $this->form->addFields([new TLabel("Log:", null, '14px', null)],[$cevento]);

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['DbinteracoesList', 'onShow']), 'fas:arrow-left #000000');
        $this->btn_onshow = $btn_onshow;

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Contratos","Interações"]));
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

            $object = new Dbinteracoes(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object->store(); // save the object 

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
            TApplication::loadPage('DbinteracoesList', 'onShow', $loadPageParam); 

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

                $object = new Dbinteracoes($key); // instantiates the Active Record 

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

    public static function VerBC($param)
    {

        $window = TWindow::create('Adianti', 0.8, 0.8);

        $iframe = new TElement('iframe');
        $iframe->id = "iframe_external";
        $iframe->src = "https://testnet.tomoscan.io/tx/" . $param['chash'];
        $iframe->frameborder = "0";
        $iframe->scrolling = "yes";
        $iframe->width = "100%";
        $iframe->height = "600px";

        $window->add($iframe);
        $window->show();

    }

}

