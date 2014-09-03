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

class BuildVirtualHost extends EventManager\AbstractListenerAggregate implements ServiceManager\ServiceLocatorAwareInterface
{

    protected $mTemplateKey = 'application/vhost';
    protected $mRewriteRulesPreTemplateKey = 'application/vhost/rewrite/rules/pre';
    protected $mRewriteRulesPostTemplateKey = 'application/vhost/rewrite/rules/post';
    protected $mModsecTemplateKey = 'application/vhost/modsec';

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

            $vhost_config = isset($config['Installation']['Vhost']) ? $config['Installation']['Vhost'] : array();
            $server = $vhost_config['Server'];
            $prefix = $server['Prefix'];
            $region = $server['Region'];
            $domain = $server['Domain'];
            $suffix = $server['Suffix'];
            $view->setVariable('ServerName', $prefix . $region . $domain . $suffix);

            $public = $server['PublicFolder'];

            $view->setVariable('DocumentRoot', getcwd() . '/' . $public);
            $view->setVariable('ApplicationEnv', $pEvent->getEnvironment());
            $view->setVariable('Config', $vhost_config);

            if (true == $pEvent->getVerboseFlag())
            {
                $console->write("   [Server Name] ");
                $console->writeLine($view->getVariable('ServerName'), ColorInterface::YELLOW);

                $console->write(" [Document Root] ");
                $console->writeLine($view->getVariable('DocumentRoot'), ColorInterface::YELLOW);

                $console->write("   [Environment] ");
                $console->writeLine($view->getVariable('ApplicationEnv'), ColorInterface::YELLOW);
            }

            // setup specific configurations
            $view->setVariable('ApacheLogDir', $vhost_config['ApacheLog']);
            $view->setVariable('UseSyslog', $vhost_config['UseSysLog']);
            $view->setVariable('Ports', $vhost_config['Ports']);
            $view->setVariable('CorsOrigin', false);

            if ( array_key_exists('CorsOrigin', $config['Installation'] ) )
            {
                $cors = $config['Installation']['CorsOrigin'];

                if ( false === is_array($cors))
                {
                    throw new Exception('CorsOrigin configuration must be an array.');
                }

                $origin_list = implode('|', $cors);
                $view->setVariable('CorsOrigin', 'http(s)?://(' . $origin_list . ')');
            }

            /**
             * Create a template map resolver from the template map in the config
             * file.
             * Using that we create a renderer that will parse the zf2 view
             * template like an ordinary template, so that we can get back its
             * contents
             */
            $map = new TemplateMapResolver($config['view_manager']['template_map']);
            $renderer = new PhpRenderer();
            $renderer->setResolver($map);

            // setup view partials for rewrites and modsec
            $rewritePreView = new ViewModel();
            $rewritePreView->setTemplate($this->mRewriteRulesPreTemplateKey);
            $rewritePreView->setVariable('Config', $vhost_config);
            $view->setVariable('RewritePreRules', $renderer->render($rewritePreView));

            $rewritePostView = new ViewModel();
            $rewritePostView->setTemplate($this->mRewriteRulesPostTemplateKey);
            $rewritePostView->setVariable('Config', $vhost_config);
            $view->setVariable('RewritePostRules', $renderer->render($rewritePostView));

            $modSec = new ViewModel();
            $modSec->setTemplate($this->mModsecTemplateKey);
            $modSec->setVariable('Config', $vhost_config);
            $view->setVariable('ModSecRules', $renderer->render($modSec));

            if (true == $pEvent->getVerboseFlag())
            {
                $console->write(" [View Template] ");
                $console->writeLine(realpath($map->get($this->mTemplateKey)), ColorInterface::YELLOW);
            }

            // write the vhost file here....
            $vhost_extension = $server['Extension'];
            $vhost_path = $server['Path'];

            $vhost_filename = $view->getVariable('ServerName') . '.' . $vhost_extension;
            $vhost_file = $vhost_path . $vhost_filename;

            if (true == $pEvent->getVerboseFlag())
            {
                $console->write("  [Apache VHost] ");
                $console->writeLine($vhost_file, ColorInterface::YELLOW);
            }

            if (false === is_dir($vhost_path))
            {
                $result = mkdir($vhost_path, 0775, true);

                if (true === $result)
                {
                    if (true == $pEvent->getVerboseFlag())
                    {
                        $console->write("        [NOTICE] ", ColorInterface::LIGHT_CYAN);
                        $console->writeLine("Vhost directory has been created.", ColorInterface::CYAN);
                    }
                }
                else
                {
                    if (true == $pEvent->getVerboseFlag())
                    {
                        $console->write("       [Failure] ", ColorInterface::RED);
                        $console->writeLine('Failed to create vhost directory, ' . $vhost_path . PHP_EOL, ColorInterface::RED);
                    }

                    throw new Exception('Failed to create vhost directory, ' . $vhost_path);
                }
            }

            if (false === is_writable($vhost_path))
            {
                if (true == $pEvent->getVerboseFlag())
                {
                    $console->write("       [Failure] ", ColorInterface::RED);
                    $console->writeLine("Vhost directory ($vhost_path) is not writable by web server." . PHP_EOL, ColorInterface::RED);
                }

                throw new Exception("Vhost directory ($vhost_path) is not writable by web server.");
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
            else if (file_exists($vhost_file) && false === is_writable($vhost_file))
            {
                if (true == $pEvent->getVerboseFlag())
                {
                    $console->write("       [Failure] ", ColorInterface::RED);
                    $console->writeLine('Apache VHost Not Writable!' . PHP_EOL, ColorInterface::RED);
                }

                throw new Exception(sprintf('Virtual host file %s is not writable.', $vhost_file));
            }
            else
            {

                $pointer = fopen($vhost_file, 'w+');
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
                        $console->write('       [Success] ', ColorInterface::LIGHT_GREEN);
                        $console->writeLine('VHost File Update Complete' . PHP_EOL, ColorInterface::YELLOW);

                        /* adding some extra spacing for the notice outside of this if block */
                        $console->write('        ');
                    }

                    $notice = $console->colorize('[NOTICE] ', ColorInterface::LIGHT_CYAN);

                    $response = new Response();
                    $response->setContent($notice . 'Virtual Host file has been updated, you may need to restart/reload your web server.');
                    return $response;
                }
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