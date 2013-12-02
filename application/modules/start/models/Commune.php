<?php

class Start_Model_Commune extends SDIS62_Model_Abstract
{
    /**
     * Nom de la commune
     *
     * @var Start_Model_NomCommune
     */
	protected $name;
    
    /**
     * Numéro insee de la commune
     *
     * @var int
     */
	protected $code_insee;
    
    /**
     * Récupération du nom de la commune
     *
     * @return string
     */ 
    public function getName()
    {
		return $this->name;
	}

    /**
     * Définition du nom de la commune
     *
     * @param string $name
     * @return Start_Model_Commune Interface fluide
     */
	public function setName($name)
    {
		$this->name = $name;
        return $this;
	}

    /**
     * Récupération du code insee
     *
     * @return int
     */ 
    public function getCodeInsee()
    {
		return $this->code_insee;
	}

    /**
     * Définition du code insee
     *
     * @param int $code_insee
     * @return Start_Model_Commune Interface fluide
     */
	public function setSetInsee($code_insee)
    {
		$this->code_insee = $code_insee;
        return $this;
	}
    
    /**
     * @inheritdoc
     */
    public function extract()
    {
        $data = parent::extract();
        
        $data['name'] = $this->getName()->getName();
        
        return $data;
    }
}