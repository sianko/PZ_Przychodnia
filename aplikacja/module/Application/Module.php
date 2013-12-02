<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Uzytkownik\Model\Uzytkownik;
use Uzytkownik\Model\UzytkownikTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;


class Module
{
    public function onBootstrap(MvcEvent $e)
    {
     
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {   
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                    'Uzytkownik' => __DIR__ . '/src/Uzytkownik',
                    'Lekarz' => __DIR__ . '/src/Lekarz',
                ),
            ),
        );
    }
    
    /*public function getServiceConfig()
    {
         return array(
             'factories' => array(
                 'Uzytkownik\Model\UzytkownikTable' =>  function($sm) {
                     $tableGateway = $sm->get('UzytkownikTableGateway');
                     $table = new UzytkownikTable($tableGateway);
                     return $table;
                 },
                 'UzytkownikTableGateway' => function ($sm) {
                     $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                     $resultSetPrototype = new ResultSet();
                     $resultSetPrototype->setArrayObjectPrototype(new Uzytkownik());
                     return new TableGateway('osoby', $dbAdapter, null, $resultSetPrototype);
                 },
             ),
         );
    }*/
}
