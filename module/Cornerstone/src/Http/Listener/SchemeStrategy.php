<?php
/**
 *
 * @author Oakensoul (http://www.oakensoul.com/)
 * @link https://github.com/oakensoul/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package Cornerstone
 */
namespace Cornerstone\Http\Listener;

use Exception;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\Console;
use Zend\Mvc\Router\Console\RouteMatch;
use Zend\Http\PhpEnvironment\Response;

class SchemeStrategy extends AbstractListenerAggregate
{

    protected $mServiceLocator;

    /**
     * {@inheritDoc}
     */
    public function attach (EventManagerInterface $events)
    {
        /**
         * add onDispatch event to Dispatcher
         */
        $options = array ();
        $options[] = $this;
        $options[] = 'onDispatch';
        
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, $options, 100);
    }

    public function onDispatch (MvcEvent $pEvent)
    {
        $request = $pEvent->getRequest();
        
        // Make sure that we are not running in a console
        if ($request instanceof Console\Request)
        {
            return;
        }
        
        /* @var $match RouteMatch */
        $match = $pEvent->getRouteMatch();
        
        /**
         * This code basically just makes sure that when we dispatch
         * a route the user is forced to SSL if the route is configured
         * to enable the feature
         */
        if (true === $match->getParam('force_https_scheme', false))
        {
            $uri = $request->getUri();
            
            if ($uri->getScheme() !== "https")
            {
                $uri->setScheme('https');
                
                /* @var $response Response */
                $response = $pEvent->getResponse();
                
                $response->setStatusCode(302);
                $response->getHeaders()->addHeaderLine('Location', $uri);
                $response->sendHeaders();
                return $response;
            }
        }
    }
}