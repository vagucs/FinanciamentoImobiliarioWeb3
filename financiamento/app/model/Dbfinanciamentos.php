<?php

class Dbfinanciamentos extends TRecord
{
    const TABLENAME  = 'dbfinanciamentos';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private $dbcontrato;
    private $dbfinanciador;
    private $dbfiador;
    private $dbcomprador;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('dbcontrato_id');
        parent::addAttribute('dbfinanciador_id');
        parent::addAttribute('dbfiador_id');
        parent::addAttribute('dbcomprador_id');
        parent::addAttribute('nindex');
        parent::addAttribute('cnome');
        parent::addAttribute('nvalor');
        parent::addAttribute('ccep');
        parent::addAttribute('cestado');
        parent::addAttribute('ccidade');
        parent::addAttribute('cendereco');
        parent::addAttribute('cnumero');
        parent::addAttribute('cbairro');
            
    }

    /**
     * Method set_dbcontrato
     * Sample of usage: $var->dbcontrato = $object;
     * @param $object Instance of Dbcontrato
     */
    public function set_dbcontrato(Dbcontrato $object)
    {
        $this->dbcontrato = $object;
        $this->dbcontrato_id = $object->id;
    }

    /**
     * Method get_dbcontrato
     * Sample of usage: $var->dbcontrato->attribute;
     * @returns Dbcontrato instance
     */
    public function get_dbcontrato()
    {
    
        // loads the associated object
        if (empty($this->dbcontrato))
            $this->dbcontrato = new Dbcontrato($this->dbcontrato_id);
    
        // returns the associated object
        return $this->dbcontrato;
    }
    /**
     * Method set_dbfinanciador
     * Sample of usage: $var->dbfinanciador = $object;
     * @param $object Instance of Dbfinanciador
     */
    public function set_dbfinanciador(Dbfinanciador $object)
    {
        $this->dbfinanciador = $object;
        $this->dbfinanciador_id = $object->id;
    }

    /**
     * Method get_dbfinanciador
     * Sample of usage: $var->dbfinanciador->attribute;
     * @returns Dbfinanciador instance
     */
    public function get_dbfinanciador()
    {
    
        // loads the associated object
        if (empty($this->dbfinanciador))
            $this->dbfinanciador = new Dbfinanciador($this->dbfinanciador_id);
    
        // returns the associated object
        return $this->dbfinanciador;
    }
    /**
     * Method set_dbfiador
     * Sample of usage: $var->dbfiador = $object;
     * @param $object Instance of Dbfiador
     */
    public function set_dbfiador(Dbfiador $object)
    {
        $this->dbfiador = $object;
        $this->dbfiador_id = $object->id;
    }

    /**
     * Method get_dbfiador
     * Sample of usage: $var->dbfiador->attribute;
     * @returns Dbfiador instance
     */
    public function get_dbfiador()
    {
    
        // loads the associated object
        if (empty($this->dbfiador))
            $this->dbfiador = new Dbfiador($this->dbfiador_id);
    
        // returns the associated object
        return $this->dbfiador;
    }
    /**
     * Method set_dbcomprador
     * Sample of usage: $var->dbcomprador = $object;
     * @param $object Instance of Dbcomprador
     */
    public function set_dbcomprador(Dbcomprador $object)
    {
        $this->dbcomprador = $object;
        $this->dbcomprador_id = $object->id;
    }

    /**
     * Method get_dbcomprador
     * Sample of usage: $var->dbcomprador->attribute;
     * @returns Dbcomprador instance
     */
    public function get_dbcomprador()
    {
    
        // loads the associated object
        if (empty($this->dbcomprador))
            $this->dbcomprador = new Dbcomprador($this->dbcomprador_id);
    
        // returns the associated object
        return $this->dbcomprador;
    }

    /**
     * Method getDbinteracoess
     */
    public function getDbinteracoess()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('dbfinanciamento_id', '=', $this->id));
        return Dbinteracoes::getObjects( $criteria );
    }

    public function set_dbinteracoes_dbfinanciamento_to_string($dbinteracoes_dbfinanciamento_to_string)
    {
        if(is_array($dbinteracoes_dbfinanciamento_to_string))
        {
            $values = Dbfinanciamentos::where('id', 'in', $dbinteracoes_dbfinanciamento_to_string)->getIndexedArray('cnome', 'cnome');
            $this->dbinteracoes_dbfinanciamento_to_string = implode(', ', $values);
        }
        else
        {
            $this->dbinteracoes_dbfinanciamento_to_string = $dbinteracoes_dbfinanciamento_to_string;
        }

        $this->vdata['dbinteracoes_dbfinanciamento_to_string'] = $this->dbinteracoes_dbfinanciamento_to_string;
    }

    public function get_dbinteracoes_dbfinanciamento_to_string()
    {
        if(!empty($this->dbinteracoes_dbfinanciamento_to_string))
        {
            return $this->dbinteracoes_dbfinanciamento_to_string;
        }
    
        $values = Dbinteracoes::where('dbfinanciamento_id', '=', $this->id)->getIndexedArray('dbfinanciamento_id','{dbfinanciamento->cnome}');
        return implode(', ', $values);
    }

    
}

