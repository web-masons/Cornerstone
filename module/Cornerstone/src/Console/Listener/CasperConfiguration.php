<?php

namespace Cornerstone\Console\Listener;

use Exception;
use Zend\Console\ColorInterface;
use Zend\EventManager;
use Zend\ServiceManager;
use Cornerstone\EventManager\Service;
use Cornerstone\EventManager\Console;
use Zend\View\Resolver\TemplateMapResolver;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Model\ViewModel;
use Zend\Config\Config;

/**
 * Creates a casper configuration file for the environment
 */
class CasperConfiguration extends EventManager\AbstractListenerAggregate implements ServiceManager\ServiceLocatorAwareInterface
{
  const TEMPLATE_NAME = 'casper/casper';
  const FILE_NAME = '.casperjsrc.js';
  
  const DEFAULT_PREFIX = '';
  const DEFAULT_REGION = 'www.';
  const DEFAULT_DOMAIN = 'cornerstone';
  const DEFAULT_SUFFIX = '.com';
  const DEFAULT_PROTOCOL = 'http://';
  
  protected $mServiceLocator;
  protected $mConsole;
  protected $mConfig;
  protected $mVerbose;
  protected $mForce;

  /**
   * {@inheritDoc}
   */
  public function attach (EventManager\EventManagerInterface $pEventManager)
  {
    $options = array ();
    $options[] = $this;
    $options[] = 'EventHandler';

    $this->listeners[] = $pEventManager->attach(Service::EVENT_APPLICATION_INITIALIZE, $options, 1);
    $this->listeners[] = $pEventManager->attach(Service::EVENT_APPLICATION_CASPER, $options, 1);
  }

  public function EventHandler (Console\Event $pEvent)
  {
    try
    {
      /* set console, config, etc */
      $console = $this->getServiceLocator()->get('console');
      $this->mConsole = $console;
      $config = $this->getServiceLocator()->get('Config');
      $config = new Config($config);
      $this->mConfig = $config;
      $this->mVerbose = $pEvent->getVerboseFlag();
      $this->mForce = $pEvent->getForceFlag();

      if (true == $this->mVerbose)
      {
        $console->write(PHP_EOL .' --------------- ', ColorInterface::LIGHT_GREEN);
        $console->writeLine('-----------------------------------------------------------', ColorInterface::YELLOW);

        $console->write("      [Listener] ", ColorInterface::LIGHT_GREEN);
        $console->writeLine(__CLASS__, ColorInterface::YELLOW);

        $console->write(' --------------- ', ColorInterface::LIGHT_GREEN);
        $console->writeLine('-----------------------------------------------------------', ColorInterface::YELLOW);
      }
            
      /* create the view model using the casper template */
      $view = new ViewModel();
      $view->setTemplate(static::TEMPLATE_NAME);
      
      /* get the full url parameter to pass to the template */
      $view->fullUrl = $this->GetFullUrl();
      
      /* write out the file */
      $this->WriteFile($view, $pEvent);
    }
    catch (Exception $e)
    {
      $this->mConsole->writeLine(' [ERROR] failed to generate casper configuration', ColorInterface::RED);
    }
  }
  
  /**
   * Gets the full domain string from config
   * @return string domain from config
   */
  private function GetFullUrl ()
  {
    $fullUrlPath = '';
    
    $prefix = $this->mConfig->Installation->Vhost->Server->get('Prefix', static::DEFAULT_PREFIX);
    $region = $this->mConfig->Installation->Vhost->Server->get('Region', static::DEFAULT_REGION);
    $domain = $this->mConfig->Installation->Vhost->Server->get('Domain', static::DEFAULT_DOMAIN);
    $suffix = $this->mConfig->Installation->Vhost->Server->get('Suffix', static::DEFAULT_SUFFIX);
    $fullUrlPath = static::DEFAULT_PROTOCOL . $prefix . $region . $domain . $suffix . '/';
    
    return $fullUrlPath;
  }
  
  /**
   * Write the configuration file
   * @param ViewModel $pView
   * @param Console\Event $pEvent
   */
  private function WriteFile(ViewModel $pView, Console\Event $pEvent)
  {
    $map = new TemplateMapResolver($this->mConfig->view_manager->template_map);
    $renderer = new PhpRenderer();
    $renderer->setResolver($map);
    
    // write the vhost file here....
    $casperFilePath = getcwd();
    $casperFile = $casperFilePath . '/' . static::FILE_NAME;
    
    if (file_exists($casperFile) && false === $this->mForce)
    {
      /* file already exists, don't do anything */
      if($this->mVerbose === true)
      {
        $this->mConsole->write('       [Info] ', ColorInterface::LIGHT_CYAN);
        $this->mConsole->writeLine('Casper configuration file already exists. Use --force to overwrite' . PHP_EOL, ColorInterface::YELLOW);
      }
    }
    else
    {
      if (false === is_writable($casperFilePath))
      {
        /* file path isn't writable */
        $this->mConsole->write('       [Error] ', ColorInterface::RED);
        $this->mConsole->writeLine('Casper configuration file path not writable' . PHP_EOL, ColorInterface::YELLOW);
      }
    
      $pointer = fopen($casperFile, 'w');
      if ($pointer === false)
      {
        /* fopen failed to open the file to write */
        $this->mConsole->write('       [Error] ', ColorInterface::RED);
        $this->mConsole->writeLine('Could not open casper configuration file for writing' . PHP_EOL, ColorInterface::YELLOW);
      }
      else
      {
        /* write the file out */
        fwrite($pointer, $renderer->render($pView));
        fclose($pointer);
    
        if($this->mVerbose === true)
        {
          $this->mConsole->write('       [Success] ', ColorInterface::LIGHT_GREEN);
          $this->mConsole->writeLine('Created casper configuration' . PHP_EOL, ColorInterface::YELLOW);
        }
      }
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
