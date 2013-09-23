<?php
/**
 *
 * @author Oakensoul (http://www.oakensoul.com/)
 * @link https://github.com/oakensoul/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package Cornerstone
 */
namespace Cornerstone\Console\Controller;

use Cornerstone\EventManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Console\ColorInterface;
use Zend\Console;
use Exception;
use Zend\Config\Config;
use Zend\View\Resolver\TemplateMapResolver;
use Zend\View\Renderer\PhpRenderer;
 
class ApplicationController extends AbstractActionController
{

    protected $mForce;

    protected $mVerbose;
    
    /**
     * these should be treated as a bitmask, numbers should go in bit math sequence
     * i.e.
     * 1, 2, 4, 8, ...
     */
    const ERROR_UNDEFINED = 1;
    const ERROR_LISTENER_OPTIONS_NOT_CONFIGURED = 2;
    const ERROR_FAILED_TO_CREATE_CACHE_FOLDER = 4;
    const ERROR_CACHE_FOLDER_NOT_WRITABLE = 8;

    /**
     * RequireConsoleRequest
     *
     * This method makes sure that we're in a console request, if we're not, it will
     * throw a RuntimeException. Technically Zend automatically protects against this
     * unless, but I've added it so that a route doesn't accidentally get added and
     * expose it.
     *
     * @throws \RuntimeException
     */
    protected function RequireConsoleRequest ()
    {
        $request = $this->getRequest();
        
        // Make sure that we are running in a console and that we have not somehow
        // accidentally exposed this route to http traffic
        if (! $request instanceof Console\Request)
        {
            throw new \RuntimeException('You can only use this action from a console!');
        }
    }

    public function initializeAction ()
    {
        /* This method makes sure we're in a console view, if not, tosses an exception */
        $this->RequireConsoleRequest();
        
        $this->mConsole = $this->getServiceLocator()->get('console');
        
        $config = $this->getServiceLocator()->get('Config');
        $config = new Config($config);
        
        $this->mForce = $this->params('force', false);
        $this->mVerbose = $this->params('verbose', false);
        
        try
        {
            $environment = $this->params()->fromRoute('env', 'production');
            
            if (true == $this->mVerbose)
            {
                $this->mConsole->writeLine("Initializing Mogwai application for environment: $environment", ColorInterface::GREEN);
            }
            
            $details = array ();
            $details['env'] = $environment;
            $details['force'] = $this->mForce;
            $details['verbose'] = $this->mVerbose;
            
            $this->EventManager()->trigger(EventManager\Service::EVENT_INITIALIZE_APPLICATION, $this, $details);
            
            if (true == $this->mVerbose)
            {
                $this->mConsole->writeLine("Application Initialization completed.", ColorInterface::GREEN);
            }
        }
        catch (Exception $e)
        {
            /* Add alternate exception catches if you want to catch errors that this controller doesn't know about */
            
            $error = isset($error) ? $error : static::ERROR_UNDEFINED;
            
            $this->mConsole->write("  [Error] ", ColorInterface::RED);
            
            /* we have to use error log here so that it will write to stderr instead of stdout */
            error_log('Exception Encountered: ' . $e->getMessage());
            
            $response = new Console\Response();
            $response->setErrorLevel($error);
            return $response;
        }
    }

    public function checkConfigAction ()
    {
        /* This method makes sure we're in a console view, if not, tosses an exception */
        $this->RequireConsoleRequest();
        
        $this->mConsole = $this->getServiceLocator()->get('console');
        
        $config = $this->getServiceLocator()->get('Config');
        $config = new Config($config);
        
        $this->mForce = $this->params('force', false);
        $this->mVerbose = $this->params('verbose', false);
        
        try
        {
            $environment = $this->params()->fromRoute('env', 'production');
            
            if (true == $this->mVerbose)
            {
                $this->mConsole->writeLine("Checking application configuration for environment: $environment", ColorInterface::GREEN);
            }
            
            $details = array ();
            $details['env'] = $environment;
            $details['force'] = $this->mForce;
            $details['verbose'] = $this->mVerbose;
            
            $this->EventManager()->trigger(EventManager\Service::EVENT_CHECK_APPLICATION_CONFIGURATION, $this, $details);
            
            /*
             * basic concept is that each module will register listeners if they have any items that require
             * configuration to function
             */
            
            if (true == $this->mVerbose)
            {
                $this->mConsole->writeLine("Configuration check completed.", ColorInterface::GREEN);
            }
        }
        catch (Exception $e)
        {
            /* Add alternate exception catches if you want to catch errors that this controller doesn't know about */
            
            $error = isset($error) ? $error : static::ERROR_UNDEFINED;
            
            $this->mConsole->write("  [Error] ", ColorInterface::RED);
            
            /* we have to use error log here so that it will write to stderr instead of stdout */
            error_log('Exception Encountered: ' . $e->getMessage());
            
            $response = new Console\Response();
            $response->setErrorLevel($error);
            return $response;
        }
    }

    public function checkIntegrationAction ()
    {
        /* This method makes sure we're in a console view, if not, tosses an exception */
        $this->RequireConsoleRequest();
        
        $this->mConsole = $this->getServiceLocator()->get('console');
        
        $config = $this->getServiceLocator()->get('Config');
        $config = new Config($config);
        
        $this->mForce = $this->params('force', false);
        $this->mVerbose = $this->params('verbose', false);
        
        try
        {
            $environment = $this->params()->fromRoute('env', 'production');
            
            if (true == $this->mVerbose)
            {
                $this->mConsole->writeLine("Performing Integration check for Mogwai application on environment: $environment", ColorInterface::GREEN);
            }
            
            $details = array ();
            $details['env'] = $environment;
            $details['force'] = $this->mForce;
            $details['verbose'] = $this->mVerbose;
            
            $this->EventManager()->trigger(EventManager\Service::EVENT_CHECK_APPLICATION_INTEGRATION, $this, $details);
            
            if (true == $this->mVerbose)
            {
                $this->mConsole->writeLine("Integration check completed.", ColorInterface::GREEN);
            }
        }
        catch (Exception $e)
        {
            /* Add alternate exception catches if you want to catch errors that this controller doesn't know about */
            
            $error = isset($error) ? $error : static::ERROR_UNDEFINED;
            
            $this->mConsole->write("  [Error] ", ColorInterface::RED);
            
            /* we have to use error log here so that it will write to stderr instead of stdout */
            error_log('Exception Encountered: ' . $e->getMessage());
            
            $response = new Console\Response();
            $response->setErrorLevel($error);
            return $response;
        }
    }

    
    
    
    public function buildVhostAction ()
    {
        /* This method makes sure we're in a console view, if not, tosses an exception */
        $this->RequireConsoleRequest();
    
        $this->mConsole = $this->getServiceLocator()->get('console');
    
        $config = $this->getServiceLocator()->get('Config');
        $config = new Config($config);
    
        $this->mForce = $this->params('force', false);
        $this->mVerbose = $this->params('verbose', false);
    
        try
        {
            $environment = $this->params()->fromRoute('env', 'production');
    
            if (true == $this->mVerbose)
            {
                $this->mConsole->writeLine("Generating vhost file for environment: $environment", ColorInterface::GREEN);
            }
    
            // create the view model for the vhost template
            $view = new ViewModel();
            $view->setTemplate('application/vhost');
    
            $prefix = $config->Installation->Vhost->Server->get('Prefix', '');
            $region = $config->Installation->Vhost->Server->get('Region', 'www.');
            $domain = $config->Installation->Vhost->Server->get('Domain', 'mogwai-zf2');
            $suffix = $config->Installation->Vhost->Server->get('Suffix', '.com');
            $view->ServerName = $prefix . $region . $domain . $suffix;
    
            $public = $config->Installation->Vhost->Server->get('PublicFolder', 'public');
            $view->DocumentRoot = getcwd() . "/$public/";
            $view->ApplicationEnv = $environment;
            $view->Config = $config->Installation->get('Vhost', array ());
    
            // setup specific configurations
            $view->ApacheLogDir = $config->Installation->Vhost->get('ApacheLog', '${APACHE_LOG_DIR}');
            $view->UseSyslog = $config->Installation->Vhost->get('UseSyslog', true);
    
            $view->Ports = $config->Installation->Vhost->get('Ports', array ());
    
            $view->CorsOrigin = false;
            if ($config->Installation->get('CorsOrigin', false))
            {
                $cors = $config->Installation->get('CorsOrigin', false);
    
                if (is_object($cors))
                {
                    $origin_list = implode('|', $cors->toArray());
                    $view->CorsOrigin = 'http(s)?://(' . $origin_list . ')';
                }
                else
                {
                    throw new Exception('CorsOrigin configuration must be an array.');
                }
            }
            elseif ($config->Installation->get('CorsHeader', false))
            {
                throw new Exception('CorsHeader has been deprecated. Please use a CorsOrigin array if you are setting up a new config.');
            }
    
            // render template
            // setup the tempate map
            $map = new TemplateMapResolver(array (
                'application/vhost' => $config->view_manager->template_map->get('application/vhost')
            ));
    
            $renderer = new PhpRenderer();
            $renderer->setResolver($map);
            $txt = $renderer->render($view);
    
            // write the vhost file here....
            $vhost_extension = $config->Installation->Vhost->get('Extension', 'vhost');
            $vhost_path = $config->Installation->Vhost->get('Path', '/etc/apache2/sites-available/');
    
            $vhost_filename = $region . $domain . '.com' . '.' . $vhost_extension;
            $vhost_file = $vhost_path . $vhost_filename;
    
            if (true == $this->mVerbose)
            {
                $this->mConsole->writeLine("Writing vhost file to: $vhost_file", ColorInterface::GREEN);
            }
    
            $pointer = fopen($vhost_file, 'w');
            if ($pointer === false)
            {
                $this->mConsole->writeLine("Error opening file for writing: $vhost_file", ColorInterface::RED);
                $this->mConsole->writeLine("Building vhost failed!", ColorInterface::RED);
            }
            else
            {
                fwrite($pointer, $txt);
                fclose($pointer);
            }
        }
        catch (Exception $e)
        {
            $this->mConsole->writeLine("Error building vhost: " . $e->getMessage(), ColorInterface::RED);
            $this->mConsole->writeLine("Building vhost failed!", ColorInterface::RED);
        }
    }    
    
    public function cacheInitAction ()
    {
        /* This method makes sure we're in a console view, if not, tosses an exception */
        $this->RequireConsoleRequest();
    
        /* @var $console Console\Request */
        $console = $this->getServiceLocator()->get('console');
    
        $config = $this->getServiceLocator()->get('ApplicationConfig');
    
        $this->mForce = $this->params('force', false);
        $this->mVerbose = $this->params('verbose', false);
    
        $already_exists = false;
    
        if (true == $this->mVerbose)
        {
            $console->writeLine("Initializing application-cache folder...", ColorInterface::GREEN);
        }
    
        try
        {
            if (false === array_key_exists('module_listener_options', $config))
            {
                $error = static::ERROR_LISTENER_OPTIONS_NOT_CONFIGURED;
                throw new \RuntimeException('Configuration key "module_listener_options" not set in application config.');
            }
            elseif (false === array_key_exists('cache_dir', $config['module_listener_options']))
            {
                $error = static::ERROR_LISTENER_OPTIONS_NOT_CONFIGURED;
                throw new \RuntimeException('Configuration key "module_listener_options[cache_dir]" not set in application config.');
            }
    
            $folder = $config['module_listener_options']['cache_dir'];
    
            if (true == $this->mVerbose)
            {
                $console->write("  [Info] ");
                $console->writeLine('$configuration[module_listener_options][cache_dir] = ' . $folder);
            }
    
            if (false === is_dir($folder))
            {
                $console->write("  [Info] ");
                $console->writeLine('Attempting to create ' . $folder);
                mkdir($folder, 0775, true);
            }
            elseif (true == $this->mVerbose)
            {
                $console->write("  [Notice] ", ColorInterface::LIGHT_GREEN);
                $console->writeLine(" $folder already exists.");
                $already_exists = true;
            }
    
            if (false === is_dir($folder))
            {
                $error = static::ERROR_FAILED_TO_CREATE_CACHE_FOLDER;
                throw new \RuntimeException("Failed to create cache folder ($folder).");
            }
    
            if (false === is_writable($folder))
            {
                $error = static::ERROR_CACHE_FOLDER_NOT_WRITABLE;
                throw new \RuntimeException("Cache Folder ($folder) not writable.");
            }
    
            if (true == $this->mVerbose && false == $already_exists)
            {
                $console->write("  [Success] ", ColorInterface::GREEN);
                $console->writeLine("Created folder $folder");
            }
        }
        catch (Exception $e)
        {
            $error = isset($error) ? $error : static::ERROR_UNDEFINED;
    
            $console->write("  [Error] ", ColorInterface::RED);
    
            /* we have to use error log here so that it will write to stderr instead of stdout */
            error_log('Exception Encountered: ' . $e->getMessage());
    
            $response = new Console\Response();
            $response->setErrorLevel($error);
            return $response;
        }
    }
    
    
    /**
     * Returns the Cornerstone Event Manger Service for logging functionality
     * 
     * @return Cornerstone\EventManager\Service
     */
    protected function EventManager ()
    {
        if (empty($this->mEventManager))
        {
            $this->mEventManager = $this->getServiceLocator()->get('Cornerstone\EventManager');
        }
        
        return $this->mEventManager;
    }
}