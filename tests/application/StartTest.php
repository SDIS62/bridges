<?php

class StartTest extends PHPUnit_Framework_TestCase
{
	public function setup()
    {
        $modelLoader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'Start_',
            'basePath' => APPLICATION_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'start'
            )
        );
	}
	
	public function testStartRecupIntervention()
    {
		$start = new Start_Service_Start;
		$nbPage = 2;
		$nbInter = 3;
		//Test pour récuperer 20 interventions d'une commune quelconque
		$listeInter = $start->getListeInterventions($nbPage,$nbInter);
		//Zend_Debug::dump($listeInter);

		//1 On verifie que l'élément retourné est bien de type array
		$this->assertInternalType('array', json_decode($listeInter));
		
		//2 On comptes le nombre d'éléments retournés = au param nbInter
		$this->assertCount($nbInter, json_decode($listeInter));
		
		//On retest avec un numéro de page non précisé
		$listeInter2 = $start->getListeInterventions(null,$nbInter);
		
		//3 On verifie que l'élément retourné est bien de type array
		$this->assertInternalType('array', json_decode($listeInter2));
		
		//4 On comptes le nombre d'éléments retournés = au param nbInter
		$this->assertCount($nbInter, json_decode($listeInter2));
		
		
	}
	
	public function testStartRecupInterventionDetail()
    {
		//$numInter = '00047267';
		$numInter = '00047270';
		//$numInter = '00047269';
		$start = new Start_Service_Start;
		$interEntity = $start->getInter($numInter);
		
		//Zend_Debug::dump($intervention);
		//5 On verifie que l'élément retourné est bien de type array
		$this->assertInternalType('array', json_decode($interEntity));
		//$this->assertInstanceOf('Start_Model_Entity_Inter', json_decode($interEntity));

		//6 On verifie que la valeur de l'interEntity est bien égale à celle passée en parametre
		//$this->assertEquals($numInter, $interEntity->getNumero());
		
		//On récupere la main courante
		$mainCourante = $start->getInterMainCourante($numInter);
		
		//Zend_Debug::dump($mainCourante);
		//7 On verifie que la main courante correspond bien à une entity de main courante
		//$this->assertInstanceOf('Start_Model_Entity_MainCourante', $mainCourante);
		//Zend_Debug::dump($mainCourante);
		//On affecte la main courante à l'entity de l'interevention
		//$interEntity->setMainCourante($mainCourante);
		
		/*
		//On récupere la chronologie
		$chronologie = $start->getChronologie($numInter);
		
		//8 On verifie que la chronologie correspond bien à une entity de chronologie
		$this->assertInstanceOf('Start_Model_Entity_Chronologie', $chronologie);
		//On affecte la chronologie de l'intervention à l'entity
		$interEntity->setChronologie($chronologie);
		
		
		//On récupere les centre engagés
		$centreEngage = $start->getCentreEngage($numInter,'13');
		Zend_Debug::dump($centreEngage);
		//9 On verifie que l'élément chronologie est bien un tableau
		$this->assertInternalType('array', $centreEngage);
		
		$interEntity->setCentreEngage($centreEngage);
		
		//12 On verifie l'entity
		$this->assertInstanceOf("Start_Model_Entity_Inter", $interEntity);
		//Zend_Debug::dump($interEntity);
		*/
	}
}