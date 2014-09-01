<?php

/**
 * View Helper - Retrieves the Module handling the request
 */
namespace Cornerstone\Http\ViewHelper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\Router\Http\RouteMatch;

class ModuleName extends AbstractHelper implements ServiceLocatorAwareInterface
{
  protected $mServiceLocator;

  public function __invoke ( )
  {
    $result = '';
    $sm = $this->getServiceLocator()->getServiceLocator();

    $router = $sm->get('router');
    $request = $sm->get('request');

    $match = $router->match($request);

    if ( $match instanceof RouteMatch )
    {
      $controller = $match->getParam('controller');
      if ( !empty($controller) && stristr($controller, '\\') !== false)
      {
        $parts = explode('\\', $controller);
        $result = $parts[0];
      }
    }
    return $result;
  }

  public function setServiceLocator ( ServiceLocatorInterface $serviceLocator )
  {
    $this->mServiceLocator = $serviceLocator;
  }

  public function getServiceLocator ()
  {
    return $this->mServiceLocator;
  }
}