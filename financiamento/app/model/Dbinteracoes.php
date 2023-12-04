<?php

class Dbinteracoes extends TRecord
{
    const TABLENAME  = 'dbinteracoes';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private $dbfinanciamento;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('dbfinanciamento_id');
        parent::addAttribute('ddatahora');
        parent::addAttribute('ctipo');
        parent::addAttribute('nbloco');
        parent::addAttribute('cevento');
        parent::addAttribute('chash');
    
    }

    /**
     * Method set_dbfinanciamentos
     * Sample of usage: $var->dbfinanciamentos = $object;
     * @param $object Instance of Dbfinanciamentos
     */
    public function set_dbfinanciamento(Dbfinanciamentos $object)
    {
        $this->dbfinanciamento = $object;
        $this->dbfinanciamento_id = $object->id;
    }

    /**
     * Method get_dbfinanciamento
     * Sample of usage: $var->dbfinanciamento->attribute;
     * @returns Dbfinanciamentos instance
     */
    public function get_dbfinanciamento()
    {
    
        // loads the associated object
        if (empty($this->dbfinanciamento))
            $this->dbfinanciamento = new Dbfinanciamentos($this->dbfinanciamento_id);
    
        // returns the associated object
        return $this->dbfinanciamento;
    }

}

