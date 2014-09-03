<?php
/**
 * @link      https://github.com/web-masons/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013, github.com/web-masons Contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache-2.0-Clause
 * @package   Cornerstone
 */

/**
 * To add a new route...
 *
 * http://framework.zend.com/manual/2.2/en/modules/zend.mvc.routing.html
 *
 * Since all configs are merged...
 *
 * To override allowable languages...
 * :: return $config['router']['routes']['home']['options']['constraints']['lang'] = '(en|nv|it|es)';
 *
 * To change default language...
 * :: return $config['router']['routes']['home']['options']['defaults']['lang'] = '(en|nv|it|es)';
 *
 * Routes that extend from 'home' will require localized routing, unless turned off by setting the
 * 'force_localized_route' parameter to default in the route configuration
 */
$router = array(
    'routes' => array(
        'home' => array(
            'type' => 'Segment',
            'may_terminate' => true,
            'options' => array(
                'route' => '[/:lang][/]',
                'defaults' => array(
                    'controller' => 'Application\Controller\Index',
                    'action' => 'index',
                    'force_localized_route' => false,
                    'force_https_scheme' => false,
                    'theme' => 'default'
                ),
                'constraints' => array(
                    'lang' => '(en|fr|it|de|es)'
                )
            ),
            'child_routes' => array(
                'not-found' => array(
                    'type' => 'Segment',
                    'priority' => 9001,
                    'may_terminate' => true,
                    'options' => array(
                        'route' => 'not-found[/]',
                        'defaults' => array(
                            'controller' => 'Application\Controller\NotFound',
                            'action' => 'index',
                            'force_localized_route' => false,
                            'force_https_scheme' => false
                        )
                    )
                ),
                'system-error' => array(
                    'type' => 'Segment',
                    'priority' => 9002,
                    'may_terminate' => true,
                    'options' => array(
                        'route' => 'system-error[/]',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Exception',
                            'action' => 'index',
                            'force_localized_route' => false,
                            'force_https_scheme' => false
                        )
                    )
                )
            )
        )
    )
);

/**
 * To add a new controller...
 *
 * http://framework.zend.com/manual/2.2/en/user-guide/routing-and-controllers.html
 */
$controllers = array(
    'invokables' => array(
        'Application\Controller\Index' => 'Cornerstone\Http\Controller\IndexController',
        'Application\Controller\NotFound' => 'Cornerstone\Http\Controller\NotFoundController',
        'Application\Controller\Exception' => 'Cornerstone\Http\Controller\ExceptionController',
        'Console\Controller\Application' => 'Cornerstone\Console\Controller\ApplicationController'
    )
);

/**
 * Controller Plugins
 *
 * http://lab.empirio.no/custom-controller-plugin-in-zf2.html
 */
$controller_plugins = array(
    'invokables' => array(
    )
);

/**
 * Working with Views
 *
 * http://framework.zend.com/manual/2.2/en/modules/zend.view.quick-start.html
 */
$view_manager = array(
    'display_not_found_reason' => false,
    'display_exceptions' => false,
    'doctype' => 'HTML5',
    'not_found_template' => 'error/404',
    'exception_template' => 'error/index',
    'template_map' => array(
        'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
        'layout/navigation' => __DIR__ . '/../view/layout/partials/site/navigation.phtml',
        'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
        'error/404' => __DIR__ . '/../view/error/404.phtml',
        'error/index' => __DIR__ . '/../view/error/index.phtml',
        'casper/casper' => __DIR__ . '/../view/console/casper/casper.tpl',
        'application/vhost' => __DIR__ . '/../view/console/application/vhost.tpl'
    ),
    'template_path_stack' => array(
        __DIR__ . '/../view'
    )
);

$console = array(
    'router' => array(
        'routes' => array(
            'cli-build-vhost' => array(
                'options' => array(
                    'route' => 'application build-vhost --env= [--force] [--verbose]',
                    'defaults' => array(
                        'controller' => 'Console\Controller\Application',
                        'action' => 'event',
                        'event' => Cornerstone\EventManager\Service::EVENT_APPLICATION_BUILD_VHOST,
                        'installer-route' => true
                    )
                )
            ),
            'cli-application-cache-init' => array(
                'options' => array(
                    'route' => 'application cache-init [--verbose]',
                    'defaults' => array(
                        'controller' => 'Console\Controller\Application',
                        'action' => 'event',
                        'event' => Cornerstone\EventManager\Service::EVENT_APPLICATION_CACHE_INIT,
                        'installer-route' => true
                    )
                )
            ),
            'cli-application-cache-empty' => array(
                'options' => array(
                    'route' => 'application cache-empty [--verbose]',
                    'defaults' => array(
                        'controller' => 'Console\Controller\Application',
                        'action' => 'event',
                        'event' => Cornerstone\EventManager\Service::EVENT_APPLICATION_CACHE_EMPTY,
                        'installer-route' => true
                    )
                )
            ),
            'application-initialization' => array(
                'options' => array(
                    'route' => 'application initialize --env= [--force] [--verbose]',
                    'defaults' => array(
                        'controller' => 'Console\Controller\Application',
                        'action' => 'event',
                        'event' => Cornerstone\EventManager\Service::EVENT_APPLICATION_INITIALIZE,
                        'installer-route' => true
                    )
                )
            ),

            'application-configuration-check' => array(
                'options' => array(
                    'route' => 'application check-config --env= [--force] [--verbose]',
                    'defaults' => array(
                        'controller' => 'Console\Controller\Application',
                        'action' => 'event',
                        'event' => Cornerstone\EventManager\Service::EVENT_APPLICATION_CHECK_CONFIGURATION,
                        'installer-route' => true
                    )
                )
            ),

            'application-integration-check' => array(
                'options' => array(
                    'route' => 'application check-integration --env= [--force] [--verbose]',
                    'defaults' => array(
                        'controller' => 'Console\Controller\Application',
                        'action' => 'event',
                        'event' => Cornerstone\EventManager\Service::EVENT_APPLICATION_CHECK_INTEGRATION,
                        'installer-route' => true
                    )
                )
            ),

            'application-casper-configuration' => array(
                'options' => array(
                    'route' => 'application casper-configuration --env= [--force] [--verbose]',
                    'defaults' => array(
                        'controller' => 'Console\Controller\Application',
                        'action' => 'event',
                        'event' => Cornerstone\EventManager\Service::EVENT_APPLICATION_CASPER,
                        'installer-route' => true
                    )
                )
            ),
        )
    )
);

$translator = array(
    'translation_file_patterns' => array(
        array(
            'type' => 'gettext',
            'base_dir' => realpath(__DIR__ . '/../language'),
            'pattern' => 'Cornerstone_%s.mo'
        )
    )
);

$navigation = array(
    'default' => array(
        'Application' => array(
            'label' => 'Home',
            'route' => 'home',
            'order' => 1
        )
    )
);

$installation = array();

$installation['Vhost'] = array (
    'ApacheLog' => '${APACHE_LOG_DIR}',
    'UseSysLog' => true
);

$installation['Vhost']['Server'] = array(
    'Domain' => 'cornerstone',
    'Prefix' => '',
    'Suffix' => '.com',
    'Region' => 'www.',
    'Extension' => 'vhost',
    'PublicFolder' => 'public/',
    'Path' => '/etc/apache2/sites-available/'
);

/**
 * Set up to use ubuntu self-signed certs by default,
 * set up your real certs in your global, local or
 * environment configs
 */
$installation['Vhost']['Ports'] = array(
    'port-80' => array(
        'Port' => 80,
        'Scheme' => 'http'
    ),
    'port-443' => array(
        'Port' => 443,
        'Scheme' => 'https',
        'SSLCert' => '/etc/ssl/certs/ssl-cert-snakeoil.pem',
        'SSLKey' => '/etc/ssl/private/ssl-cert-snakeoil.key'
    )
);

$view_helpers = array(
    'invokables' => array(
        'ChangeLocale' => 'Cornerstone\Http\ViewHelper\ChangeLocale'
    )
);

$cornerstone = array(
    'Application' => array(
        'Log' => array(
            'Event' => array(
                'Facility' => NULL,
                'ApplicationName' => NULL
            ),
            'General' => array(
                'Facility' => NULL,
                'ApplicationName' => NULL
            )
        )
    )
);

$config = array();
$config['router'] = $router;
$config['console'] = $console;
$config['translator'] = $translator;
$config['controllers'] = $controllers;
$config['controller_plugins'] = $controller_plugins;
$config['navigation'] = $navigation;
$config['view_manager'] = $view_manager;
$config['view_helpers'] = $view_helpers;
$config['Installation'] = $installation;
$config['Cornerstone'] = $cornerstone;

return $config;
