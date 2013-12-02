<?php

class Start_Service_Api
{
    /**
     * Récupération de l'entity manager
     *
     * @return Doctrine\ORM\EntityManager
     */
    private function getEntityManager()
    {
        $modules_bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('modules'); 
        return $modules_bootstrap['start']->getResource('db');
    }
    
    /**
     * Récupération de toutes les interventions (plus récentes aux plus anciennes)
     *
     * @param int $count
     * @return string
     */
	public function interventions($count = 50)
	{
        $interventions = $this->getEntityManager()->getRepository("Start_Model_Intervention")->findBy(array(), array('date_declenchement' => 'DESC'), $count, 0);
        $data = array();
        
        foreach($interventions as $intervention)
        {
            $data[] = $intervention->extract();
        }
        
        return $data;
    }
    
    /**
     * Récupération d'une intervention
     *
     * @param int $id
     * @param int $year Optionnel, par défaut, l'année en cours (ex : 13)
     * @return string
     */
	public function intervention($id, $year = null)
	{
        if($year === null)
        {
            $year = date('y');
        }
        
        return $this->getEntityManager()->getRepository("Start_Model_Intervention")->find(array('id' => $id, 'year' => $year))->extract();
    }
}