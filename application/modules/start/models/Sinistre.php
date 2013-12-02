<?php

class Start_Model_Sinistre extends SDIS62_Model_Abstract
{
    /**
     * Libellé du sinistre
     *
     * @var string
     */
	protected $label;

    /**
     * Récupération de l'intitulé du sinistre
     *
     * @return string
     */ 
	public function getLabel()
    {
		return $this->label;
	}

    /**
     * Définition de l'intitulé du sinistre
     *
     * @param string $label
     * @return Start_Model_Sinistre Interface fluide
     */
	public function setLabel($label)
    {
		$this->label = $label;
        return $this;
	}
}