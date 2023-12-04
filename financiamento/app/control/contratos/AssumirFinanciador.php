<?php

class AssumirFinanciador extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = '';
    private static $activeRecord = '';
    private static $primaryKey = '';
    private static $formName = 'form_AssumirFinanciador';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param = null)
    {
        parent::__construct();

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("AssumirFinanciador");


        $ccontrato = new TEntry('ccontrato');


        $ccontrato->setSize('100%');


        $row1 = $this->form->addFields([new TLabel("Rótulo:", null, '14px', null, '100%')],[$ccontrato]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        // create the form actions
        $btn_onaction = $this->form->addAction("Ação", new TAction([$this, 'onAction']), 'fas:rocket #ffffff');
        $this->btn_onaction = $btn_onaction;
        $btn_onaction->addStyleClass('btn-primary'); 

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Contratos","AssumirFinanciador"]));
        }
        $container->add($this->form);

        parent::add($container);

    }

    public function onAction($param = null) 
    {
        try
        {

        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    public function onShow($param = null)
    {               

        new TMessage('info',$param);
    } 

}

