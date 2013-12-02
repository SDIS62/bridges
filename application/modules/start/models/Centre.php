<?php

class Start_Model_Centre extends SDIS62_Model_Abstract
{
    /**
     * Nom du centre
     *
     * @var string
     */
    protected $name;
    
    /**
     * Récupération du nom du centre
     *
     * @return string
     */ 
	public function getName()
    {
		return $this->name;
	}

    /**
     * Définition du nom du centre
     *
     * @param string $name
     * @return Start_Model_Centre Interface fluide
     */
	public function setName($name)
    {
		$this->name = $name;
        return $this;
	}
}