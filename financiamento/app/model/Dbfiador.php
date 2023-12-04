<?php

class Dbfiador extends TRecord
{
    const TABLENAME  = 'dbfiador';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('cnome');
        parent::addAttribute('cpublickey');
        parent::addAttribute('cchaveprivada');
            
    }

    /**
     * Method getDbfinanciamentoss
     */
    public function getDbfinanciamentoss()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('dbfiador_id', '=', $this->id));
        return Dbfinanciamentos::getObjects( $criteria );
    }

    public function set_dbfinanciamentos_dbcontrato_to_string($dbfinanciamentos_dbcontrato_to_string)
    {
        if(is_array($dbfinanciamentos_dbcontrato_to_string))
        {
            $values = Dbcontrato::where('id', 'in', $dbfinanciamentos_dbcontrato_to_string)->getIndexedArray('cnome', 'cnome');
            $this->dbfinanciamentos_dbcontrato_to_string = implode(', ', $values);
        }
        else
        {
            $this->dbfinanciamentos_dbcontrato_to_string = $dbfinanciamentos_dbcontrato_to_string;
        }

        $this->vdata['dbfinanciamentos_dbcontrato_to_string'] = $this->dbfinanciamentos_dbcontrato_to_string;
    }

    public function get_dbfinanciamentos_dbcontrato_to_string()
    {
        if(!empty($this->dbfinanciamentos_dbcontrato_to_string))
        {
            return $this->dbfinanciamentos_dbcontrato_to_string;
        }
    
        $values = Dbfinanciamentos::where('dbfiador_id', '=', $this->id)->getIndexedArray('dbcontrato_id','{dbcontrato->cnome}');
        return implode(', ', $values);
    }

    public function set_dbfinanciamentos_dbfinanciador_to_string($dbfinanciamentos_dbfinanciador_to_string)
    {
        if(is_array($dbfinanciamentos_dbfinanciador_to_string))
        {
            $values = Dbfinanciador::where('id', 'in', $dbfinanciamentos_dbfinanciador_to_string)->getIndexedArray('cnome', 'cnome');
            $this->dbfinanciamentos_dbfinanciador_to_string = implode(', ', $values);
        }
        else
        {
            $this->dbfinanciamentos_dbfinanciador_to_string = $dbfinanciamentos_dbfinanciador_to_string;
        }

        $this->vdata['dbfinanciamentos_dbfinanciador_to_string'] = $this->dbfinanciamentos_dbfinanciador_to_string;
    }

    public function get_dbfinanciamentos_dbfinanciador_to_string()
    {
        if(!empty($this->dbfinanciamentos_dbfinanciador_to_string))
        {
            return $this->dbfinanciamentos_dbfinanciador_to_string;
        }
    
        $values = Dbfinanciamentos::where('dbfiador_id', '=', $this->id)->getIndexedArray('dbfinanciador_id','{dbfinanciador->cnome}');
        return implode(', ', $values);
    }

    public function set_dbfinanciamentos_dbfiador_to_string($dbfinanciamentos_dbfiador_to_string)
    {
        if(is_array($dbfinanciamentos_dbfiador_to_string))
        {
            $values = Dbfiador::where('id', 'in', $dbfinanciamentos_dbfiador_to_string)->getIndexedArray('cnome', 'cnome');
            $this->dbfinanciamentos_dbfiador_to_string = implode(', ', $values);
        }
        else
        {
            $this->dbfinanciamentos_dbfiador_to_string = $dbfinanciamentos_dbfiador_to_string;
        }

        $this->vdata['dbfinanciamentos_dbfiador_to_string'] = $this->dbfinanciamentos_dbfiador_to_string;
    }

    public function get_dbfinanciamentos_dbfiador_to_string()
    {
        if(!empty($this->dbfinanciamentos_dbfiador_to_string))
        {
            return $this->dbfinanciamentos_dbfiador_to_string;
        }
    
        $values = Dbfinanciamentos::where('dbfiador_id', '=', $this->id)->getIndexedArray('dbfiador_id','{dbfiador->cnome}');
        return implode(', ', $values);
    }

    public function set_dbfinanciamentos_dbcomprador_to_string($dbfinanciamentos_dbcomprador_to_string)
    {
        if(is_array($dbfinanciamentos_dbcomprador_to_string))
        {
            $values = Dbcomprador::where('id', 'in', $dbfinanciamentos_dbcomprador_to_string)->getIndexedArray('cnome', 'cnome');
            $this->dbfinanciamentos_dbcomprador_to_string = implode(', ', $values);
        }
        else
        {
            $this->dbfinanciamentos_dbcomprador_to_string = $dbfinanciamentos_dbcomprador_to_string;
        }

        $this->vdata['dbfinanciamentos_dbcomprador_to_string'] = $this->dbfinanciamentos_dbcomprador_to_string;
    }

    public function get_dbfinanciamentos_dbcomprador_to_string()
    {
        if(!empty($this->dbfinanciamentos_dbcomprador_to_string))
        {
            return $this->dbfinanciamentos_dbcomprador_to_string;
        }
    
        $values = Dbfinanciamentos::where('dbfiador_id', '=', $this->id)->getIndexedArray('dbcomprador_id','{dbcomprador->cnome}');
        return implode(', ', $values);
    }

    
}

