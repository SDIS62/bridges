<?php

class Start_ApiController extends Zend_Controller_Action
{
    public function indexAction()
    {
        // On annule le rendu de la vue
        $this->_helper->viewRenderer->setNoRender(true);

        // On configure le serveur du Web Service
        $server = new SDIS62_Rest_Server;
        $server->setClass("Start_Service_Api");
        $server->handle();
    }
}