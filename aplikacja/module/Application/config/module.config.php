<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */


/* Własna konfiguracja Doctrine */
$MY_NAMESPACES_ENTITY = array(
    'Application'
);

$array_injected_into_doctrine = array();

foreach($MY_NAMESPACES_ENTITY as $source)
{
    $array_injected_into_doctrine[$source . '_entities'] = array(
                                                                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                                                                'cache' => 'array',
                                                                'paths' => array(__DIR__ . '/../src/'. $source . '/Entity')
                                                            );
    $array_injected_into_doctrine['orm_default']['drivers'][$source . '\Entity'] = $source . '_entities';
}

/* END Własna konfiguracja */


/* Dynamiczne tworzenie invokables 
    SCHEMAT: 'Uzytkownik\Controller\Logowanie' => 'Uzytkownik\Controller\LogowanieController',
*/
    $dir = __DIR__ . "/../src";
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
            if($file !== '.' && $file !== '..'){
               
               if ($subdir = opendir($dir.'/'.$file.'/Controller/')) {
                    while (($controllerFile = readdir($subdir)) !== false) {
                        $substring = substr($controllerFile, 0, -14);
                        if(!empty($substring)){
                            $tablica_invokables[$file.'\\Controller\\'. $substring] = $file . '\\Controller\\'. $substring .'Controller';
                        }
                    }
                    closedir($subdir);
               }
            }
        }
        closedir($dh);
    }
/* END Dynamiczne tworzenie */



return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'kontakt' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/kontakt',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Kontakt',
                        'action'        => 'index',
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type'    => 'Segment',
                    'options' => array(
                        'route'    => '/application[/:controller[/:action]]',
                        'defaults' => array(
                            //'__NAMESPACE__' => 'Application\Controller',
                            'controller'    => 'Application\Controller\Index',
                            'action'        => 'index',
                        ),
                    ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
            'uzytkownik' => array(
                'type'    => 'Segment',
                    'options' => array(
                        'route'    => '/uzytkownik[/:controller[/:action[/:id]]][/s:page][/]',
                        'constraints' => array(
                                'controller' => '[a-zA-Z]{2}[a-zA-Z0-9_\-]*',
                                'action'     => '[a-zA-Z]{2}[a-zA-Z0-9_\-]*',
                                'id'     => '[0-9]*',
                                'page'     => '[0-9]*',
                            ),
                        'defaults' => array(
                            '__NAMESPACE__' => 'Uzytkownik\Controller',
                            'controller'    => 'logowanie',
                            'action'        => 'index',
                            'id' => 0,
                            'page' => 1
                        ),
                    ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/uzytkownik[/:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
            'lekarz' => array(
                'type'    => 'Segment',
                    'options' => array(
                        'route'    => '/lekarze[/:controller[/:action[/:id]]][/spec/:category][/s:page][/]',
                        'constraints' => array(
                                'controller' => '[a-zA-Z]{2}[a-zA-Z0-9_\-]*',
                                'action'     => '[a-zA-Z]{2}[a-zA-Z0-9_\-]*',
                                'id'     => '[0-9]*',
                                'page'     => '[0-9]*',
                                'category' => '[0-9]*',
                            ),
                        'defaults' => array(
                            '__NAMESPACE__' => 'Lekarz\Controller',
                            'controller'    => 'profil',
                            'action'        => 'index',
                            'id' => 0,
                            'page' => 1,
                            'category' => 0
                        ),
                    ),
            ),
            'wizyta' => array(
                'type'    => 'Segment',
                    'options' => array(
                        'route'    => '/wizyta[/:controller[/:action[/:id]]][/os/:oid][/t/:category][/sort/:sort/:met][/s:page][/]',
                        'constraints' => array(
                                'controller' => '[a-zA-Z]{2}[a-zA-Z0-9_\-]*',
                                'action'     => '[a-zA-Z]{2}[a-zA-Z0-9_\-]*',
                                'id'     => '[0-9]*',
                                'page'     => '[0-9]*',
                                'category' => '[0-9]*',
                                'oid' => '[0-9]*',
                                'sort' => '[a-zA-Z]*',
                                'met' => '[a-zA-Z]*'
                            ),
                        'defaults' => array(
                            '__NAMESPACE__' => 'Wizyta\Controller',
                            'controller'    => 'Index',
                            'action'        => 'index',
                            'id' => 0,
                            'page' => 1,
                            'category' => 0,
                            'oid' => 0,
                            'sort' => 'data',
                            'met' => 'desc'
                        ),
                    ),
            ),
            
        ),
        
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
            'Zend\Authentication\AuthenticationService' => 'my_auth_service',
        ),
        'invokables' => array(
			'my_auth_service' => 'Zend\Authentication\AuthenticationService',
		),
    ),
    'translator' => array(
        'locale' => 'pl_PL',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
            array(
                'type'        => 'phparray',
                'base_dir'    => __DIR__ . '/../../../../resources/languages',
                'pattern'     => '/pl/Zend_Validate.php',
                'text_domain' => 'default'
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => $tablica_invokables,
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'partial/paginacja_part' => __DIR__ . '/../view/partial/paginacja_partial.phtml',
            'partial/grafik_lekarza' => __DIR__ . '/../view/partial/grafik_lekarza.phtml',
            'partial/lista_lekarzy' => __DIR__ . '/../view/partial/lista_lekarzy.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
    
    // Doctrine config
    'doctrine' => array(
        'driver' => $array_injected_into_doctrine
    )
);
