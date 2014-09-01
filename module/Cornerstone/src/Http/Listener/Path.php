<?php
namespace Cornerstone\Http\Listener;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\RouteMatch;

/**
 *
 * This listener fires before the view manager's bootstrap and modifies the template path stack to include the current
 * module's view folder.  This allows site modules (not composed in modules) to override partials and layouts for their
 * specific use (i.e. only for requests handled by that module)
 *
 */
class Path extends AbstractListenerAggregate
{
  /**
   * {@inheritDoc}
   */
  public function attach(EventManagerInterface $events)
  {
    $this->listeners[] = $events->attach(MvcEvent::EVENT_BOOTSTRAP, array($this, 'onBootstrap'), 10001); //must be called before the ViewManager's onBootstrap (10000)
  }

  /**
   * Detach aggregate listeners from the specified event manager
   *
   * @param  EventManagerInterface $events
   * @return void
   */
  public function detach(EventManagerInterface $events)
  {
    foreach ($this->listeners as $index => $listener)
    {
      if ($events->detach($listener))
      {
        unset($this->listeners[$index]);
      }
    }
  }

  public function onBootstrap($event)
  {
    $this->ModifyTemplatePathStack($event);
  }

  /**
   * This dynamically adds the module's view directory to the the template path stack based on the matched controller's namespace
   * @param MvcEvent $pEvent
   */
  public function ModifyTemplatePathStack (MvcEvent $pEvent)
  {
    $application = $pEvent->getApplication();
    $service_manager = $application->getServiceManager();

    $router = $service_manager->get('router');
    $request = $service_manager->get('request');

    $match = $router->match($request);

    if ( $match instanceof RouteMatch )
    {
      //We need to find the module name - to do that we must get it from the controller's namespace
      $config = $service_manager->get('config');
      $controller_key = $match->getParam('controller');
      $controller = '';

      //look up controller key in the controller config
      if( !empty($config['controllers']['invokables'][$controller_key]) ) //check invokables
      {
        $controller = $config['controllers']['invokables'][$controller_key];
      }
      else if( !empty($config['controllers']['factories'][$controller_key]) ) //check factories
      {
        $controller = $config['controllers']['factories'][$controller_key];
      }

      //if we found a controller pull out the first part of the namespace to get the module name
      if ( !empty($controller) && stristr($controller, '\\') !== false)
      {
        $parts = explode('\\', $controller);
        $module = $parts[0];

        //if we have a template path stack - which we always should - push a new directory on it
        if(isset($config['view_manager']['template_path_stack']) && is_array($config['view_manager']['template_path_stack']))
        {
          //add the module to the config path - only check the site's module directory - not composed in modules - this way we can still override composed in modules per site.
          $module_view_path = getcwd().'/module/'.$module.'/view';
          array_push($config['view_manager']['template_path_stack'], $module_view_path);
          $service_manager->setAllowOverride(true);
          $service_manager->setService('config', $config);
          $service_manager->setAllowOverride(false);
        }
      }
    }

  }


}