<?php
/**
 *
 * @author Oakensoul (http://www.oakensoul.com/)
 * @link https://github.com/oakensoul/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package Cornerstone
 */
namespace Cornerstone;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Console\Adapter\AdapterInterface as ConsoleAdapterInterface;

class Module implements ConsoleUsageProviderInterface, AutoloaderProviderInterface, ConfigProviderInterface
{

    public function onBootstrap (MvcEvent $e)
    {
        $application = $e->getApplication();
        $service_manager = $application->getServiceManager();
        $event_manager = $application->getEventManager();
        $shared_manager = $event_manager->getSharedManager();

        /* allow controller short names in routing */
        $module_route_listener = new ModuleRouteListener();
        $module_route_listener->attach($event_manager);

        /* set sup our localization strategy so that we enforce localization routes */
        $service_manager->get('Http\LocalizationStrategy')->attach($event_manager);

        /* the scheme strategy handles processing based on (http|https) scheme */
        $service_manager->get('Http\SchemeStrategy')->attach($event_manager);

        /* the bootstrap strategy handles setting up bootstrap and its requirements into the view renderer */
        $service_manager->get('Http\BootstrapStrategy')->attach($event_manager);

        /* the theme strategy handles setting up basic head scripts and style sheets to the layout */
        $service_manager->get('Http\ThemeStrategy')->attach($event_manager);

        /* Cornerstone Application Event Manager */
        $cornerstone_event_manager = $service_manager->get('Application\EventManager');

        /* attach our CLI strategies */
        // $service_manager->get('Console\ApplicationCacheInit')->attach($cornerstone_event_manager);

        $service_manager->get('Console\InitializeApplicationStrategy')->attach($cornerstone_event_manager);
        $service_manager->get('Console\CheckApplicationConfigurationStrategy')->attach($cornerstone_event_manager);
        $service_manager->get('Console\CheckApplicationIntegrationStrategy')->attach($cornerstone_event_manager);
    }

    public function getConfig ()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig ()
    {
        return array (
            'Zend\Loader\StandardAutoloader' => array (
                'namespaces' => array (
                    __NAMESPACE__ => __DIR__ . '/src/'
                )
            )
        );
    }

    public function getConsoleUsage (ConsoleAdapterInterface $console)
    {
        return array (
            'Build Commands:',
            'application initialize --env=<environment>' => "Initialize the application.",
            'application check-config --env=<environment>' => "Check configuration for the application.",
            'application check-integration --env=<environment>' => "Check service integrations for the application.",
            'application build-vhost --env=<environment>' => 'Generates a vhost file for the project.',
            'application cache-init' => "Initialize and empty the application cache folder.",

            'Optional Parameters:',
            array (
                '--force',
                'Force command to go through'
            ),
            array (
                '--verbose',
                'Verbose output'
            )
        );
    }

    public function getServiceConfig ()
    {
        $factories = array ();

        $factories['translator'] = 'Zend\I18n\Translator\TranslatorServiceFactory';
        $factories['Site\Navigation'] = 'Zend\Navigation\Service\DefaultNavigationFactory';

        $invokables = array ();

        $invokables['Http\LocalizationStrategy'] = 'Cornerstone\Http\Listener\LocalizationStrategy';
        $invokables['Http\SchemeStrategy'] = 'Cornerstone\Http\Listener\SchemeStrategy';
        $invokables['Http\BootstrapStrategy'] = 'Cornerstone\Http\Listener\TwitterBootstrap3Strategy';
        $invokables['Http\ThemeStrategy'] = 'Cornerstone\Http\Listener\ThemeStrategy';

        $invokables['Application\EventManager'] = 'Cornerstone\EventManager\Service';

        $invokables['Console\InitializeApplicationStrategy'] = 'Cornerstone\Console\Listener\InitializeApplicationStrategy';
        $invokables['Console\CheckApplicationConfigurationStrategy'] = 'Cornerstone\Console\Listener\CheckApplicationConfigurationStrategy';
        $invokables['Console\CheckApplicationIntegrationStrategy'] = 'Cornerstone\Console\Listener\CheckApplicationIntegrationStrategy';

        $service_config = array (
            'factories' => $factories,
            'invokables' => $invokables
        );

        return $service_config;
    }
}
