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

use Exception;
use Zend\EventManager;
use Zend\ServiceManager;
use Cornerstone\EventManager\Service;
use Cornerstone\EventManager\Console;
use Zend\View\Resolver\TemplateMapResolver;
use Zend\View\Renderer\PhpRenderer;
use Zend\Console\ColorInterface;
use Zend\View\Model\ViewModel;
use Zend\Console\Response;
use Zend\Config\Config;

class BuildVirtualHost extends EventManager\AbstractListenerAggregate implements ServiceManager\ServiceLocatorAwareInterface
{

    protected $mTemplateKey = 'application/vhost';

    protected $mServiceLocator;

    /**
     * {@inheritDoc}
     */
    public function attach (EventManager\EventManagerInterface $pEventManager)
    {
        $options = array ();
        $options[] = $this;
        $options[] = 'EventHandler';

        $this->listeners[] = $pEventManager->attach(Service::EVENT_APPLICATION_INITIALIZE, $options, 1);
        $this->listeners[] = $pEventManager->attach(Service::EVENT_APPLICATION_BUILD_VHOST, $options, 1);
    }

    public function EventHandler (Console\Event $pEvent)
    {
        try
        {
            $console = $this->getServiceLocator()->get('console');

            $config = $this->getServiceLocator()->get('Config');
            $config = new Config($config);

            if (true == $pEvent->getVerboseFlag())
            {
                $console->write(' --------------- ', ColorInterface::LIGHT_GREEN);
                $console->writeLine('-----------------------------------------------------------', ColorInterface::YELLOW);

                $console->write("      [Listener] ", ColorInterface::LIGHT_GREEN);
                $console->writeLine(__CLASS__, ColorInterface::YELLOW);

                $console->write(' --------------- ', ColorInterface::LIGHT_GREEN);
                $console->writeLine('-----------------------------------------------------------', ColorInterface::YELLOW);
            }

            if (true == $pEvent->getVerboseFlag())
            {
                $console->write("  [Template Key] ");
                $console->writeLine($this->mTemplateKey, ColorInterface::YELLOW);
            }

            // create the view model for the vhost template
            $view = new ViewModel();
            $view->setTemplate($this->mTemplateKey);

            $prefix = $config->Installation->Vhost->Server->get('Prefix', '');
            $region = $config->Installation->Vhost->Server->get('Region', 'www.');
            $domain = $config->Installation->Vhost->Server->get('Domain', 'mogwai-zf2');
            $suffix = $config->Installation->Vhost->Server->get('Suffix', '.com');
            $view->ServerName = $prefix . $region . $domain . $suffix;

            $public = $config->Installation->Vhost->Server->get('PublicFolder', 'public');
            $view->DocumentRoot = getcwd() . "/$public/";
            $view->ApplicationEnv = $pEvent->getEnvironment();
            $view->Config = $config->Installation->get('Vhost', array ());

            if (true == $pEvent->getVerboseFlag())
            {
                $console->write("   [Server Name] ");
                $console->writeLine($view->ServerName, ColorInterface::YELLOW);

                $console->write(" [Document Root] ");
                $console->writeLine($view->DocumentRoot, ColorInterface::YELLOW);

                $console->write("   [Environment] ");
                $console->writeLine($view->ApplicationEnv, ColorInterface::YELLOW);
            }

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

            /**
             * Create a template map resolver from the template map in the config
             * file.
             * Using that we create a renderer that will parse the zf2 view
             * template like an ordinary template, so that we can get back its
             * contents
             */
            $map = new TemplateMapResolver($config->view_manager->template_map);
            $renderer = new PhpRenderer();
            $renderer->setResolver($map);

            if (true == $pEvent->getVerboseFlag())
            {
                $console->write(" [View Template] ");
                $console->writeLine(realpath($map->get($this->mTemplateKey)), ColorInterface::YELLOW);
            }

            // write the vhost file here....
            $vhost_extension = $config->Installation->Vhost->get('Extension', 'vhost');
            $vhost_path = $config->Installation->Vhost->get('Path', '/etc/apache2/sites-available/');

            $vhost_filename = $region . $domain . '.com' . '.' . $vhost_extension;
            $vhost_file = $vhost_path . $vhost_filename;

            if (true == $pEvent->getVerboseFlag())
            {
                $console->write("  [Apache VHost] ");
                $console->writeLine($vhost_file, ColorInterface::YELLOW);
            }

            if (file_exists($vhost_file) && false === $pEvent->getForceFlag())
            {
                if (true == $pEvent->getVerboseFlag())
                {
                    $console->write("        [NOTICE] ", ColorInterface::LIGHT_CYAN);
                    $console->writeLine('Apache VHost file already exists, skipping creation.', ColorInterface::CYAN);

                    $console->write("          [INFO] ", ColorInterface::LIGHT_CYAN);
                    $console->writeLine('To overwrite the existing file, use --force' . PHP_EOL, ColorInterface::CYAN);
                }
            }
            else
            {
                if (false === is_writable($vhost_file))
                {
                    if (true == $pEvent->getVerboseFlag())
                    {
                        $console->write("       [Failure] ", ColorInterface::RED);
                        $console->writeLine('Apache VHost Not Writable!' . PHP_EOL, ColorInterface::RED);
                    }

                    throw new Exception(sprintf('Virtual host file %s is not writable.', $vhost_file));
                }

                $pointer = fopen($vhost_file, 'w');
                if ($pointer === false)
                {
                    if (true == $pEvent->getVerboseFlag())
                    {
                        $console->write("       [Failure] ", ColorInterface::RED);
                        $console->writeLine('Failed to open Apache VHost for writing!' . PHP_EOL, ColorInterface::RED);
                    }

                    throw new Exception(sprintf('Failed to open Virtual host file %s for writing.', $vhost_file));
                }
                else
                {
                    fwrite($pointer, $renderer->render($view));
                    fclose($pointer);

                    if (true == $pEvent->getVerboseFlag())
                    {
                        $console->write("       [Success] ", ColorInterface::LIGHT_GREEN);
                        $console->writeLine('VHost File Update Complete' . PHP_EOL, ColorInterface::YELLOW);
                    }

                    $notice = $console->colorize('[NOTICE] ', ColorInterface::LIGHT_CYAN);

                    $response = new Response();
                    $response->setContent($notice . ' Virtual Host file has been updated, you may need to restart/reload your web server.');
                    return $response;
                }
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