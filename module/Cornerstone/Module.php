<?php
/**
 *
 * @author    Oakensoul (http://www.oakensoul.com/)
 * @link      https://github.com/web-masons/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013, github.com/web-masons Contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache-2.0-Clause
 * @package   Cornerstone
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

        /* the theme strategy handles setting up basic head scripts and style sheets to the layout */
        $service_manager->get('Http\ThemeStrategy')->attach($event_manager);

        /* the exception logger strategy dumps exception messages to the log before ZF2 gets it and swallows it */
        $service_manager->get('Http\ExceptionLoggerStrategy')->attach($event_manager);

        /* the layout strategy handles overriding the layout for specific routes */
        $service_manager->get('Http\LayoutStrategy')->attach($event_manager);

        /* Cornerstone Application Event Manager */
        $cornerstone_event_manager = $service_manager->get('Application\EventManager');

        /* attach our CLI strategies */
        $service_manager->get('Console\InitializeApplicationStrategy')->attach($cornerstone_event_manager);
        $service_manager->get('Console\BuildVirtualHostStrategy')->attach($cornerstone_event_manager);
        $service_manager->get('Console\ApplicationCacheInitStrategy')->attach($cornerstone_event_manager);
        $service_manager->get('Console\ApplicationCacheEmptyStrategy')->attach($cornerstone_event_manager);
        $service_manager->get('Console\CasperConfigurationStrategy')->attach($cornerstone_event_manager);
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
            'application cache-init' => "Initialize the application cache folder.",
            'application cache-empty' => "Empty the application cache folder.",

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
        /**
         * Factories should be used when you have logic required to create the
         * requested service or object. If it's a simple instantiation with no
         * dependencies, use an invokable
         */
        $factories = array ();

        $factories['translator'] = 'Zend\I18n\Translator\TranslatorServiceFactory';
        $factories['Site\Navigation'] = 'Zend\Navigation\Service\DefaultNavigationFactory';

        /**
         * Invokables should be used for a simple instantiation with no
         * dependencies. If you have logic required to create the requested
         * service or object, use a factory.
         *
         * Generally, invokables are great for strategy objects / Listeners
         */
        $invokables = array ();

        $invokables['Http\LocalizationStrategy'] = 'Cornerstone\Http\Listener\Localization';
        $invokables['Http\SchemeStrategy'] = 'Cornerstone\Http\Listener\Scheme';
        $invokables['Http\ThemeStrategy'] = 'Cornerstone\Http\Listener\Theme';
        $invokables['Http\ExceptionLoggerStrategy'] = 'Cornerstone\Http\Listener\ExceptionLogger';
        $invokables['Http\LayoutStrategy'] = 'Cornerstone\Http\Listener\Layout';

        $invokables['Application\EventManager'] = 'Cornerstone\EventManager\Service';

        $invokables['Console\BuildVirtualHostStrategy'] = 'Cornerstone\Console\Listener\BuildVirtualHost';
        $invokables['Console\ApplicationCacheInitStrategy'] = 'Cornerstone\Console\Listener\ApplicationCacheInit';
        $invokables['Console\ApplicationCacheEmptyStrategy'] = 'Cornerstone\Console\Listener\ApplicationCacheEmpty';
        $invokables['Console\InitializeApplicationStrategy'] = 'Cornerstone\Console\Listener\InitializeApplication';
        $invokables['Console\CasperConfigurationStrategy'] = 'Cornerstone\Console\Listener\CasperConfiguration';

        $service_config = array (
            'factories' => $factories,
            'invokables' => $invokables
        );

        return $service_config;
    }
}
