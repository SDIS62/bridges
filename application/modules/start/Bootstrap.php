<?php

class Start_Bootstrap extends Zend_Application_Module_Bootstrap
{
    /**
     * Initialisation et configuration de Doctrine 2
     */
    protected function _initDb()
    {
        // Import des paramètres de connexion à la base de données
        $dbConfig =  new Zend_Config_Ini(APPLICATION_PATH . DS . 'modules' . DS . 'start' . DS . 'configs' . DS . 'secret.ini', APPLICATION_ENV, true);
        $dbConfig->merge(new Zend_Config_Ini(APPLICATION_PATH . DS . 'modules' . DS . 'start' . DS . 'configs' . DS . 'db.ini', APPLICATION_ENV));

        // Exemple de configuration du cache
        if (APPLICATION_ENV == "development" || APPLICATION_ENV == "testing")
        {
            $cache = new \Doctrine\Common\Cache\ArrayCache();
        }
        else
        {
            $cacheOptions = $options['cache']['backendOptions'];
            $cache = new \Doctrine\Common\Cache\MemcacheCache();
            $memcache = new Memcache;
            $memcache->connect($cacheOptions['servers']['host'], $cacheOptions['servers']['port']);
            $cache->setMemcache($memcache);
        }
        
        // Configuration des entités
        $config = Doctrine\ORM\Tools\Setup::createXMLMetadataConfiguration(
            array(APPLICATION_PATH . DS .  'modules' . DS . 'start' . DS . 'models' . DS . 'DataMapper'),
            APPLICATION_ENV === "development"
        );

        // Utiliser les annotations pour la description du modèle
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);
        $config->setProxyDir(APPLICATION_PATH . DS . 'modules' . DS . 'start' . DS . 'models' . DS . 'Proxy');

        // Ne générer les classes proxy qu'en developpement
        $config->setAutoGenerateProxyClasses(APPLICATION_ENV == "development");

        // Configuration des paramètres de connexion
        $connectionParams  = array(
            'driver' => $dbConfig->resources->db->start->adapter,
            'host' => $dbConfig->resources->db->start->params->host,
            'port' => $dbConfig->resources->db->start->params->port,
            'user'  => $dbConfig->resources->db->start->params->username,
            'password' => $dbConfig->resources->db->start->params->password,
            'dbname' => $dbConfig->resources->db->start->params->dbname,
        );

        // On obtient l'entityManager
        return Doctrine\ORM\EntityManager::create($connectionParams, $config);
    }
}