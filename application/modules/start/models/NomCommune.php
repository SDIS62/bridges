<?php

class Start_Model_NomCommune extends SDIS62_Model_Abstract
{
    /**
     * Nom de la commune
     *
     * @var string
     */
	protected $name;

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
     * @return Start_Model_NomCommune Interface fluide
     */
	public function setName($name)
    {
		$this->name = $name;
        return $this;
	}
}