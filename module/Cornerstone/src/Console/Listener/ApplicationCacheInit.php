<?php
/**
 *
 * @author Oakensoul (http://www.oakensoul.com/)
 * @link https://github.com/oakensoul/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package Cornerstone
 */
namespace Cornerstone\Console\Listener;

use Cornerstone\EventManager\Console;
use Cornerstone\EventManager\Service;
use Exception;
use Zend\EventManager;
use Zend\Console\ColorInterface;
use Zend\Console\Response;
use Zend\ServiceManager;

class ApplicationCacheInit extends EventManager\AbstractListenerAggregate implements ServiceManager\ServiceLocatorAwareInterface
{

    protected $mServiceLocator;

    /**
     * {@inheritDoc}
     */
    public function attach (EventManager\EventManagerInterface $pEventManager)
    {
        $options = array ();
        $options[] = $this;
        $options[] = 'EventHandler';

        $this->listeners[] = $pEventManager->attach(Service::EVENT_APPLICATION_INITIALIZE, $options, 10);
        $this->listeners[] = $pEventManager->attach(Service::EVENT_APPLICATION_CACHE_INIT, $options, 1);
    }

    public function EventHandler (Console\Event $pEvent)
    {
        try
        {
            $console = $this->getServiceLocator()->get('console');

            $config = $this->getServiceLocator()->get('ApplicationConfig');

            if (true == $pEvent->getVerboseFlag())
            {
                $console->write(' --------------- ', ColorInterface::LIGHT_GREEN);
                $console->writeLine('-----------------------------------------------------------', ColorInterface::YELLOW);

                $console->write("      [Listener] ", ColorInterface::LIGHT_GREEN);
                $console->writeLine(__CLASS__, ColorInterface::YELLOW);

                $console->write(' --------------- ', ColorInterface::LIGHT_GREEN);
                $console->writeLine('-----------------------------------------------------------', ColorInterface::YELLOW);
            }

            if (false === array_key_exists('module_listener_options', $config))
            {
                if (true == $pEvent->getVerboseFlag())
                {
                    $console->write("        [NOTICE] ", ColorInterface::LIGHT_CYAN);
                    $console->writeLine("Application config does not contain an entry for 'module_listener_options'.", ColorInterface::CYAN);
                    $console->write("                 ", ColorInterface::LIGHT_CYAN);
                    $console->writeLine("Skipping creation of module map cache folder." . PHP_EOL, ColorInterface::CYAN);
                }

                return;
            }

            if (false === array_key_exists('cache_dir', $config['module_listener_options']))
            {
                if (true == $pEvent->getVerboseFlag())
                {
                    $console->write("       [Failure] ", ColorInterface::RED);
                    $console->writeLine('Application config error, module_listener_options, does not contain a "cache_dir" entry!' . PHP_EOL, ColorInterface::RED);
                }

                throw new Exception('Application config error, module_listener_options, does not contain a "cache_dir" entry!');
                return;
            }

            $cache_dir = $config['module_listener_options']['cache_dir'];

            if (true == $pEvent->getVerboseFlag())
            {
                $console->write("     [Cache Dir] ");
                $console->writeLine($cache_dir, ColorInterface::YELLOW);
            }

            if (false === is_dir($cache_dir))
            {
                $result = mkdir($cache_dir, 0775, true);

                if (true === $result)
                {
                    if (true == $pEvent->getVerboseFlag())
                    {
                        $console->write("        [NOTICE] ", ColorInterface::LIGHT_CYAN);
                        $console->writeLine("Cache directory has been created.", ColorInterface::CYAN);
                    }
                }
                else
                {
                    if (true == $pEvent->getVerboseFlag())
                    {
                        $console->write("       [Failure] ", ColorInterface::RED);
                        $console->writeLine('Failed to create cache directory, ' . $cache_dir . PHP_EOL, ColorInterface::RED);
                    }

                    throw new Exception('Failed to create cache directory, ' . $cache_dir);
                }
            }

            if (false === is_writable($cache_dir))
            {
                if (true == $pEvent->getVerboseFlag())
                {
                    $console->write("       [Failure] ", ColorInterface::RED);
                    $console->writeLine("Cache directory ($cache_dir) is not writable by web server." . PHP_EOL, ColorInterface::RED);
                }

                throw new Exception("Cache directory ($cache_dir) is not writable by web server.");
            }

            if (true == $pEvent->getVerboseFlag())
            {
                $console->write("       [Success] ", ColorInterface::GREEN);
                $console->writeLine("Application cache folder ($cache_dir) exists and is writable." . PHP_EOL);
            }
        }
        catch (Exception $e)
        {
            $response = new Response();
            $response->setErrorLevel(1);
            $response->setContent('Exception Encountered: ' . $e->getMessage());
            return $response;
        }
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator (ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $this->mServiceLocator = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator ()
    {
        return $this->mServiceLocator;
    }
}