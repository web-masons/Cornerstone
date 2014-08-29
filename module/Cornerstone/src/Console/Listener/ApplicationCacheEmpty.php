<?php
/**
 *
 * @author Oakensoul (http://www.oakensoul.com/)
 * @link      https://github.com/web-masons/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013, github.com/web-masons Contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache-2.0-Clause
 * @package   Cornerstone
 */
namespace Cornerstone\Console\Listener;

use Cornerstone\EventManager\Console;
use Cornerstone\EventManager\Service;
use Exception;
use Zend\EventManager;
use Zend\Console\ColorInterface;
use Zend\Console\Response;
use Zend\ServiceManager;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use FilesystemIterator;

class ApplicationCacheEmpty extends EventManager\AbstractListenerAggregate implements ServiceManager\ServiceLocatorAwareInterface
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

        $this->listeners[] = $pEventManager->attach(Service::EVENT_APPLICATION_CACHE_EMPTY, $options, 1);
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
                    $console->write("        [Failure] ", ColorInterface::RED);
                    $console->writeLine("Application config does not contain an entry for 'module_listener_options'.", ColorInterface::RED);
                    $console->write("                 ", ColorInterface::LIGHT_CYAN);
                    $console->writeLine("Skipping emptying of application cache folder." . PHP_EOL, ColorInterface::CYAN);
                }

                throw new Exception('Application config error, module_listener_options, does not exist. Directory empty failed!');
            }

            if (false === array_key_exists('cache_dir', $config['module_listener_options']))
            {
                if (true == $pEvent->getVerboseFlag())
                {
                    $console->write("       [Failure] ", ColorInterface::RED);
                    $console->writeLine('Application config error, module_listener_options, does not contain a "cache_dir" entry!' . PHP_EOL, ColorInterface::RED);
                }

                throw new Exception('Application config error, module_listener_options, does not contain a "cache_dir" entry!');
            }

            $cache_dir = $config['module_listener_options']['cache_dir'];

            if (true == $pEvent->getVerboseFlag())
            {
                $console->write("     [Cache Dir] ");
                $console->writeLine($cache_dir, ColorInterface::YELLOW);
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
                $console->write("        [NOTICE] ", ColorInterface::LIGHT_CYAN);
                $console->writeLine("Emptying cache directory of any existing files/folders.", ColorInterface::CYAN);
            }

            $iterator = new RecursiveDirectoryIterator($cache_dir, FilesystemIterator::SKIP_DOTS);

            foreach (new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST) as $path)
            {
                /* @var RecursiveDirectoryIterator $path */
                if (true == $pEvent->getVerboseFlag())
                {
                    $console->write("        [NOTICE] ", ColorInterface::LIGHT_CYAN);
                    $console->writeLine("Deleting " . $path->getPathname(), ColorInterface::CYAN);
                }

                $path->isFile() ? unlink($path->getPathname()) : rmdir($path->getPathname());
            }

            if (true == $pEvent->getVerboseFlag())
            {
                $console->write("       [Success] ", ColorInterface::GREEN);
                $console->writeLine("Application cache folder ($cache_dir) has been emptied." . PHP_EOL);
            }

            return NULL;
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
     * @param ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator (ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $this->mServiceLocator = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator ()
    {
        return $this->mServiceLocator;
    }
}
