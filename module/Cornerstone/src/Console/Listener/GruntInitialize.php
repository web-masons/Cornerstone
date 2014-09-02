<?php

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
use Zend\Config\Reader\Json;

class GruntInitialize extends EventManager\AbstractListenerAggregate implements ServiceManager\ServiceLocatorAwareInterface
{
  protected $mServiceLocator;

  /**
   * {@inheritDoc}
   */
  public function attach ( EventManager\EventManagerInterface $pEventManager )
  {
    $options = array ();
    $options[] = $this;
    $options[] = 'EventHandler';

    $this->listeners[] = $pEventManager->attach(Service::EVENT_APPLICATION_GRUNT_INIT, $options, 1);
  }

  public function EventHandler ( Console\Event $pEvent )
  {
    $this->mConsole = $this->getServiceLocator()->get('console');

    $config = $this->getServiceLocator()->get('Config');
    $this->mConfig = new Config($config);

    $this->mResolver = new TemplateMapResolver(array ( 'grunt/gruntfile' => $this->mConfig->view_manager->template_map->get('grunt/gruntfile'), 'grunt/package-json' => $this->mConfig->view_manager->template_map->get('grunt/package-json') ));

    $this->mForce = $pEvent->getForceFlag();
    $this->mVerbose = $pEvent->getVerboseFlag();

    try
    {
      $arguments = $pEvent->getArguments();
      $option = $arguments['option'];

      switch ( $option )
      {
        case 'gruntfile':
          $this->GenerateGruntFile();
          break;

        case 'package-json':
          $this->GeneratePackageJSON();
          break;

        case 'all':
          $this->GenerateGruntFile();
          $this->GeneratePackageJSON();
          break;
      }
    }
    catch ( Exception $e )
    {
      $this->mConsole->writeLine("Error using grunt to initialize files: " . $e->getMessage(), ColorInterface::RED);
      $this->mConsole->writeLine("Grunt initialize failed!", ColorInterface::RED);
    }
  }

  protected function GenerateGruntFile ()
  {
    // create the view model for the vhost template
    $view = new ViewModel();
    $view->setTemplate('grunt/gruntfile');

    $renderer = new PhpRenderer();
    $renderer->setResolver($this->mResolver);
    $txt = $renderer->render($view);

    // write the vhost file here....
    $gruntfile = getcwd() . '/Gruntfile.js';

    if ( false === file_exists($gruntfile) || true == $this->mForce )
    {
      if ( true == $this->mVerbose )
      {
        $this->mConsole->writeLine("Writing Gruntfile file to: $gruntfile", ColorInterface::GREEN);
      }

      $pointer = fopen($gruntfile, 'w');
      if ( $pointer === false )
      {
        $this->mConsole->writeLine("Error opening file for writing: $gruntfile", ColorInterface::RED);
        $this->mConsole->writeLine("Building Gruntfile failed!", ColorInterface::RED);
      }
      else
      {
        fwrite($pointer, $txt);
        fclose($pointer);
      }
    }
    else
    {
      if ( true == $this->mVerbose )
      {
        $this->mConsole->writeline("$gruntfile already exists. (Overwrite using --force)");
      }
    }
  }

  protected function GeneratePackageJSON ()
  {
    // create the view model for the vhost template
    $view = new ViewModel();

    $composer = new Json();
    $composer_config = $composer->fromFile(getcwd() . '/' . 'composer.json');

    $explode = explode('/', $composer_config['name']);
    $name = strtolower(implode('-', $explode));

    $view->setVariable('mName', $name);
    $view->setTemplate('grunt/package-json');

    $renderer = new PhpRenderer();
    $renderer->setResolver($this->mResolver);
    $txt = $renderer->render($view);

    // write the vhost file here....
    $gruntfile = getcwd() . '/package.json';

    if ( false === file_exists($gruntfile) || true == $this->mForce )
    {
      if ( true == $this->mVerbose )
      {
        $this->mConsole->writeLine("Writing Package json file to: $gruntfile", ColorInterface::GREEN);
      }

      $pointer = fopen($gruntfile, 'w');
      if ( $pointer === false )
      {
        $this->mConsole->writeLine("Error opening file for writing: $gruntfile", ColorInterface::RED);
        $this->mConsole->writeLine("Building Package JSON failed!", ColorInterface::RED);
      }
      else
      {
        fwrite($pointer, $txt);
        fclose($pointer);
      }
    }
    else
    {
      if ( true == $this->mVerbose )
      {
        $this->mConsole->writeline("$gruntfile already exists. (Overwrite using --force)");
      }
    }
  }

  /**
   * Set service locator
   *
   * @param ServiceLocatorInterface $serviceLocator
   */
  public function setServiceLocator ( ServiceManager\ServiceLocatorInterface $serviceLocator )
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