<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * Initialisation du cache APC
     */
    protected function _initCache()
    {
        // Import des paramètres de connexion à la base de données
        $dbConfig = new Zend_Config_Ini(APPLICATION_PATH . DS . 'configs' . DS . 'application.ini', APPLICATION_ENV);
            
        return Zend_Cache::factory('Core', 'APC', array(
            'lifetime' => $dbConfig->cache->lifetime,
            'cache_id_prefix' => 'bridges'
        ));
    }
}