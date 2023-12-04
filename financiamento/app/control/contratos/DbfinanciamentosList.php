<?php

class DbfinanciamentosList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'dbimovel';
    private static $activeRecord = 'Dbfinanciamentos';
    private static $primaryKey = 'id';
    private static $formName = 'form_DbfinanciamentosList';
    private $showMethods = ['onReload', 'onSearch', 'onRefresh', 'onClearFilters'];
    private $limit = 20;

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct($param = null)
    {
        parent::__construct();

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);

        // define the form title
        $this->form->setFormTitle("Listagem de financiamentos");
        $this->limit = 20;

        $dbcontrato_id = new TDBCombo('dbcontrato_id', 'dbimovel', 'Dbcontrato', 'id', '{cnome}','cnome asc'  );
        $dbfinanciador_id = new TDBCombo('dbfinanciador_id', 'dbimovel', 'Dbfinanciador', 'id', '{cnome}','cnome asc'  );
        $dbfiador_id = new TDBCombo('dbfiador_id', 'dbimovel', 'Dbfiador', 'id', '{cnome}','cnome asc'  );
        $dbcomprador_id = new TDBCombo('dbcomprador_id', 'dbimovel', 'Dbcomprador', 'id', '{cnome}','cnome asc'  );


        $dbfiador_id->setSize('100%');
        $dbcontrato_id->setSize('100%');
        $dbcomprador_id->setSize('100%');
        $dbfinanciador_id->setSize('100%');

        $dbfiador_id->enableSearch();
        $dbcontrato_id->enableSearch();
        $dbcomprador_id->enableSearch();
        $dbfinanciador_id->enableSearch();

        $row1 = $this->form->addFields([new TLabel("Contrato:", null, '14px', null)],[$dbcontrato_id],[new TLabel("Financiador:", null, '14px', null)],[$dbfinanciador_id]);
        $row2 = $this->form->addFields([new TLabel("Fiador:", null, '14px', null)],[$dbfiador_id],[new TLabel("Comprador:", null, '14px', null)],[$dbcomprador_id]);

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsearch = $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $this->btn_onsearch = $btn_onsearch;
        $btn_onsearch->addStyleClass('btn-primary'); 

        $btn_onshow = $this->form->addAction("Cadastrar", new TAction(['DbfinanciamentosForm', 'onShow']), 'fas:plus #69aa46');
        $this->btn_onshow = $btn_onshow;

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->disableHtmlConversion();

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(250);

        $column_dbcontrato_cnome = new TDataGridColumn('dbcontrato->cnome', "Contrato", 'left');
        $column_dbfinanciador_cnome = new TDataGridColumn('dbfinanciador->cnome', "Financiador", 'left');
        $column_dbfiador_cnome = new TDataGridColumn('dbfiador->cnome', "Fiador", 'left');
        $column_dbcomprador_cnome = new TDataGridColumn('dbcomprador->cnome', "Comprador", 'left');
        $column_nindex = new TDataGridColumn('nindex', "Índice/NFT", 'left');
        $column_cnome = new TDataGridColumn('cnome', "Nome do imóvel", 'left');
        $column_nvalor_transformed = new TDataGridColumn('nvalor', "Valor", 'left');

        $column_nvalor_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!empty(trim($value)))
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y H:i');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
        });        

        $order_nindex = new TAction(array($this, 'onReload'));
        $order_nindex->setParameter('order', 'nindex');
        $column_nindex->setAction($order_nindex);
        $order_nvalor_transformed = new TAction(array($this, 'onReload'));
        $order_nvalor_transformed->setParameter('order', 'nvalor');
        $column_nvalor_transformed->setAction($order_nvalor_transformed);

        $this->datagrid->addColumn($column_dbcontrato_cnome);
        $this->datagrid->addColumn($column_dbfinanciador_cnome);
        $this->datagrid->addColumn($column_dbfiador_cnome);
        $this->datagrid->addColumn($column_dbcomprador_cnome);
        $this->datagrid->addColumn($column_nindex);
        $this->datagrid->addColumn($column_cnome);
        $this->datagrid->addColumn($column_nvalor_transformed);

        $action_onEdit = new TDataGridAction(array('DbfinanciamentosForm', 'onEdit'));
        $action_onEdit->setUseButton(false);
        $action_onEdit->setButtonClass('btn btn-default btn-sm');
        $action_onEdit->setLabel("Editar");
        $action_onEdit->setImage('far:edit #478fca');
        $action_onEdit->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onEdit);

        $action_DadosNFT = new TDataGridAction(array('DbfinanciamentosList', 'DadosNFT'));
        $action_DadosNFT->setUseButton(false);
        $action_DadosNFT->setButtonClass('btn btn-default btn-sm');
        $action_DadosNFT->setLabel("Dados (NFT)");
        $action_DadosNFT->setImage('fas:info #000000');
        $action_DadosNFT->setField(self::$primaryKey);

        $action_DadosNFT->setParameter('id', '{id}');

        $this->datagrid->addAction($action_DadosNFT);

        $action_DbAssumirFinanciador_onEdit = new TDataGridAction(array('DbAssumirFinanciador', 'onEdit'));
        $action_DbAssumirFinanciador_onEdit->setUseButton(false);
        $action_DbAssumirFinanciador_onEdit->setButtonClass('btn btn-default btn-sm');
        $action_DbAssumirFinanciador_onEdit->setLabel("Assumir Finaciador");
        $action_DbAssumirFinanciador_onEdit->setImage('far:building #000000');
        $action_DbAssumirFinanciador_onEdit->setField(self::$primaryKey);

        $action_DbAssumirFinanciador_onEdit->setParameter('key', '{id}');

        $this->datagrid->addAction($action_DbAssumirFinanciador_onEdit);

        $action_DbEnviarDrexTokenizado_onEdit = new TDataGridAction(array('DbEnviarDrexTokenizado', 'onEdit'));
        $action_DbEnviarDrexTokenizado_onEdit->setUseButton(false);
        $action_DbEnviarDrexTokenizado_onEdit->setButtonClass('btn btn-default btn-sm');
        $action_DbEnviarDrexTokenizado_onEdit->setLabel("Dep. Drex T.");
        $action_DbEnviarDrexTokenizado_onEdit->setImage('fas:money-check-alt #000000');
        $action_DbEnviarDrexTokenizado_onEdit->setField(self::$primaryKey);

        $action_DbEnviarDrexTokenizado_onEdit->setParameter('key', '{id}');

        $this->datagrid->addAction($action_DbEnviarDrexTokenizado_onEdit);

        $action_DbEnviarDrex_onEdit = new TDataGridAction(array('DbEnviarDrex', 'onEdit'));
        $action_DbEnviarDrex_onEdit->setUseButton(false);
        $action_DbEnviarDrex_onEdit->setButtonClass('btn btn-default btn-sm');
        $action_DbEnviarDrex_onEdit->setLabel("Dep. Drex");
        $action_DbEnviarDrex_onEdit->setImage('fas:money-bill #000000');
        $action_DbEnviarDrex_onEdit->setField(self::$primaryKey);

        $action_DbEnviarDrex_onEdit->setParameter('key', '{id}');

        $this->datagrid->addAction($action_DbEnviarDrex_onEdit);

        $action_DbPargarParcial_onEdit = new TDataGridAction(array('DbPargarParcial', 'onEdit'));
        $action_DbPargarParcial_onEdit->setUseButton(false);
        $action_DbPargarParcial_onEdit->setButtonClass('btn btn-default btn-sm');
        $action_DbPargarParcial_onEdit->setLabel("Pagar parcela");
        $action_DbPargarParcial_onEdit->setImage('fas:comment-dollar #000000');
        $action_DbPargarParcial_onEdit->setField(self::$primaryKey);

        $action_DbPargarParcial_onEdit->setParameter('key', '{id}');

        $this->datagrid->addAction($action_DbPargarParcial_onEdit);

        $action_DbQuitarContrato_onEdit = new TDataGridAction(array('DbQuitarContrato', 'onEdit'));
        $action_DbQuitarContrato_onEdit->setUseButton(false);
        $action_DbQuitarContrato_onEdit->setButtonClass('btn btn-default btn-sm');
        $action_DbQuitarContrato_onEdit->setLabel("Liquidar");
        $action_DbQuitarContrato_onEdit->setImage('fas:calendar-check #000000');
        $action_DbQuitarContrato_onEdit->setField(self::$primaryKey);

        $action_DbQuitarContrato_onEdit->setParameter('key', '{id}');

        $this->datagrid->addAction($action_DbQuitarContrato_onEdit);

        // create the datagrid model
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup();
        $panel->datagrid = 'datagrid-container';
        $this->datagridPanel = $panel;
        $this->datagrid_form->add($this->datagrid);
        $panel->add($this->datagrid_form);

        $panel->getBody()->class .= ' table-responsive';

        $panel->addFooter($this->pageNavigation);

        $headerActions = new TElement('div');
        $headerActions->class = ' datagrid-header-actions ';
        $headerActions->style = 'justify-content: space-between;';

        $head_left_actions = new TElement('div');
        $head_left_actions->class = ' datagrid-header-actions-left-actions ';

        $head_right_actions = new TElement('div');
        $head_right_actions->class = ' datagrid-header-actions-left-actions ';

        $headerActions->add($head_left_actions);
        $headerActions->add($head_right_actions);

        $panel->getBody()->insert(0, $headerActions);

        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['DbfinanciamentosList', 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['DbfinanciamentosList', 'onExportXls'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['DbfinanciamentosList', 'onExportPdf'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['DbfinanciamentosList', 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );

        $head_right_actions->add($dropdown_button_exportar);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Contratos","Financiamentos"]));
        }
        $container->add($this->form);
        $container->add($panel);

        parent::add($container);

    }

    public function onExportCsv($param = null) 
    {
        try
        {
            $output = 'app/output/'.uniqid().'.csv';

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $this->limit = 0;
                $objects = $this->onReload();

                if ($objects)
                {
                    $handler = fopen($output, 'w');
                    TTransaction::open(self::$database);

                    foreach ($objects as $object)
                    {
                        $row = [];
                        foreach ($this->datagrid->getColumns() as $column)
                        {
                            $column_name = $column->getName();

                            if (isset($object->$column_name))
                            {
                                $row[] = is_scalar($object->$column_name) ? $object->$column_name : '';
                            }
                            else if (method_exists($object, 'render'))
                            {
                                $column_name = (strpos((string)$column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
                                $row[] = $object->render($column_name);
                            }
                        }

                        fputcsv($handler, $row);
                    }

                    fclose($handler);
                    TTransaction::close();
                }
                else
                {
                    throw new Exception(_t('No records found'));
                }

                TPage::openFile($output);
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }
    public function onExportXls($param = null) 
    {
        try
        {
            $output = 'app/output/'.uniqid().'.xls';

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $widths = [];
                $titles = [];

                foreach ($this->datagrid->getColumns() as $column)
                {
                    $titles[] = $column->getLabel();
                    $width    = 100;

                    if (is_null($column->getWidth()))
                    {
                        $width = 100;
                    }
                    else if (strpos((string)$column->getWidth(), '%') !== false)
                    {
                        $width = ((int) $column->getWidth()) * 5;
                    }
                    else if (is_numeric($column->getWidth()))
                    {
                        $width = $column->getWidth();
                    }

                    $widths[] = $width;
                }

                $table = new \TTableWriterXLS($widths);
                $table->addStyle('title',  'Helvetica', '10', 'B', '#ffffff', '#617FC3');
                $table->addStyle('data',   'Helvetica', '10', '',  '#000000', '#FFFFFF', 'LR');

                $table->addRow();

                foreach ($titles as $title)
                {
                    $table->addCell($title, 'center', 'title');
                }

                $this->limit = 0;
                $objects = $this->onReload();

                TTransaction::open(self::$database);
                if ($objects)
                {
                    foreach ($objects as $object)
                    {
                        $table->addRow();
                        foreach ($this->datagrid->getColumns() as $column)
                        {
                            $column_name = $column->getName();
                            $value = '';
                            if (isset($object->$column_name))
                            {
                                $value = is_scalar($object->$column_name) ? $object->$column_name : '';
                            }
                            else if (method_exists($object, 'render'))
                            {
                                $column_name = (strpos((string)$column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
                                $value = $object->render($column_name);
                            }

                            $transformer = $column->getTransformer();
                            if ($transformer)
                            {
                                $value = strip_tags(call_user_func($transformer, $value, $object, null));
                            }

                            $table->addCell($value, 'center', 'data');
                        }
                    }
                }
                $table->save($output);
                TTransaction::close();

                TPage::openFile($output);
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }
    public function onExportPdf($param = null) 
    {
        try
        {
            $output = 'app/output/'.uniqid().'.pdf';

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $this->limit = 0;
                $this->datagrid->prepareForPrinting();
                $this->onReload();

                $html = clone $this->datagrid;
                $contents = file_get_contents('app/resources/styles-print.html') . $html->getContents();

                $dompdf = new \Dompdf\Dompdf;
                $dompdf->loadHtml($contents);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();

                file_put_contents($output, $dompdf->output());

                $window = TWindow::create('PDF', 0.8, 0.8);
                $object = new TElement('iframe');
                $object->src  = $output;
                $object->type  = 'application/pdf';
                $object->style = "width: 100%; height:calc(100% - 10px)";

                $window->add($object);
                $window->show();
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }
    public function onExportXml($param = null) 
    {
        try
        {
            $output = 'app/output/'.uniqid().'.xml';

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $this->limit = 0;
                $objects = $this->onReload();

                if ($objects)
                {
                    TTransaction::open(self::$database);

                    $dom = new DOMDocument('1.0', 'UTF-8');
                    $dom->{'formatOutput'} = true;
                    $dataset = $dom->appendChild( $dom->createElement('dataset') );

                    foreach ($objects as $object)
                    {
                        $row = $dataset->appendChild( $dom->createElement( self::$activeRecord ) );

                        foreach ($this->datagrid->getColumns() as $column)
                        {
                            $column_name = $column->getName();
                            $column_name_raw = str_replace(['(','{','->', '-','>','}',')', ' '], ['','','_','','','','','_'], $column_name);

                            if (isset($object->$column_name))
                            {
                                $value = is_scalar($object->$column_name) ? $object->$column_name : '';
                                $row->appendChild($dom->createElement($column_name_raw, $value)); 
                            }
                            else if (method_exists($object, 'render'))
                            {
                                $column_name = (strpos((string)$column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
                                $value = $object->render($column_name);
                                $row->appendChild($dom->createElement($column_name_raw, $value));
                            }
                        }
                    }

                    $dom->save($output);

                    TTransaction::close();
                }
                else
                {
                    throw new Exception(_t('No records found'));
                }

                TPage::openFile($output);
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

    /**
     * Register the filter in the session
     */
    public function onSearch($param = null)
    {
        $data = $this->form->getData();
        $filters = [];

        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        if (isset($data->dbcontrato_id) AND ( (is_scalar($data->dbcontrato_id) AND $data->dbcontrato_id !== '') OR (is_array($data->dbcontrato_id) AND (!empty($data->dbcontrato_id)) )) )
        {

            $filters[] = new TFilter('dbcontrato_id', '=', $data->dbcontrato_id);// create the filter 
        }

        if (isset($data->dbfinanciador_id) AND ( (is_scalar($data->dbfinanciador_id) AND $data->dbfinanciador_id !== '') OR (is_array($data->dbfinanciador_id) AND (!empty($data->dbfinanciador_id)) )) )
        {

            $filters[] = new TFilter('dbfinanciador_id', '=', $data->dbfinanciador_id);// create the filter 
        }

        if (isset($data->dbfiador_id) AND ( (is_scalar($data->dbfiador_id) AND $data->dbfiador_id !== '') OR (is_array($data->dbfiador_id) AND (!empty($data->dbfiador_id)) )) )
        {

            $filters[] = new TFilter('dbfiador_id', '=', $data->dbfiador_id);// create the filter 
        }

        if (isset($data->dbcomprador_id) AND ( (is_scalar($data->dbcomprador_id) AND $data->dbcomprador_id !== '') OR (is_array($data->dbcomprador_id) AND (!empty($data->dbcomprador_id)) )) )
        {

            $filters[] = new TFilter('dbcomprador_id', '=', $data->dbcomprador_id);// create the filter 
        }

        // fill the form with data again
        $this->form->setData($data);

        // keep the search data in the session
        TSession::setValue(__CLASS__.'_filter_data', $data);
        TSession::setValue(__CLASS__.'_filters', $filters);

        $this->onReload(['offset' => 0, 'first_page' => 1]);
    }

    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'dbimovel'
            TTransaction::open(self::$database);

            // creates a repository for Dbfinanciamentos
            $repository = new TRepository(self::$activeRecord);

            $criteria = clone $this->filter_criteria;

            if (empty($param['order']))
            {
                $param['order'] = 'nindex';    
            }

            if (empty($param['direction']))
            {
                $param['direction'] = 'desc';
            }

            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $this->limit);

            if($filters = TSession::getValue(__CLASS__.'_filters'))
            {
                foreach ($filters as $filter) 
                {
                    $criteria->add($filter);       
                }
            }

            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);

            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {

                    $row = $this->datagrid->addItem($object);
                    $row->id = "row_{$object->id}";

                }
            }

            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);

            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($this->limit); // limit

            // close the transaction
            TTransaction::close();
            $this->loaded = true;

            return $objects;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }

    public function onShow($param = null)
    {

    }

    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  $this->showMethods))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }

    public static function DadosNFT($param)
    {
        TTransaction::open(self::$database);
        $fin = Dbfinanciamentos::find( $param['id']);

        $w3 = new C9Web3();

        $fiador = $w3->LerFinanciador($fin->nindex);
        $garantidor = $w3->LerGarantidor($fin->nindex);
        $comprador = $w3->LerComprador($fin->nindex);
        $valor =  number_format($w3->valorDoBem($fin->nindex),2,",",".");
        $valoratual =  number_format($w3->valorDoDebitoAtual($fin->nindex),2,",",".");
        $valordrex =  number_format($w3->valorSaldoDrex($fin->nindex),2,",",".");
        $valordrext =  number_format($w3->valorSaldoDrexTokenizado($fin->nindex),2,",",".");

        $valorcontrato =  number_format($w3->LerValorTotalEmContrato(),2,",",".");
        $valorapagar =  number_format($w3->ValorTotalAbertoEmContrato(),2,",",".");
        $valordivida =  number_format($w3->ValorTotalDividaEmContrato(),2,",",".");

        $message='Financiador:' . $fiador . '<br>' . 
                 'Garantidor:' . $garantidor . '<br>' . 
                 'Comprador:' . $comprador . '<hr>' .
                 'Valor do bem: ' .  $valor . '<br>' .
                 'Valor atual: ' . $valoratual . '<br>' .
                 'Valor Drex: ' . $valordrex . '<br>' . 
                 'Drex Tokenizado: ' . $valordrext . '<br>' . 
                 'Valor do contrato: ' . $valorcontrato . '<br>' .
                 'Valor a cobrir(Financiador): ' . $valorapagar . '<br>' .
                 'Valor da divida: ' . $valordivida;

        $object = new TLabel($message);

        $window = TWindow::create('Dados obtidos do blockchain', 0.5, 300);
        $window->add($object);
        $window->show();

        //new TMessage('info',$message);

    }

}

