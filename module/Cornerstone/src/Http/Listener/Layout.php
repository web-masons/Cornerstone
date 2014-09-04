<?php
/**
 * A Listener that ties into the rendering process and switches layouts based on
 * route configuration
 *
 * @author    divideandconquer (https://github.com/divideandconquer)
 * @link      https://github.com/web-masons/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013, github.com/web-masons Contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache-2.0-Clause
 * @package   Cornerstone
 */
namespace Cornerstone\Http\Listener;

use Exception;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\Console;
use Zend\Mvc\Router\Console\RouteMatch;

class Layout extends AbstractListenerAggregate
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
        $options[] = 'onRender';

        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, $options, 100);
    }

    public function onRender (MvcEvent $pEvent)
    {
        $request = $pEvent->getRequest();

        // Make sure that we are not running in a console
        if ($request instanceof Console\Request)
        {
            return;
        }

        /* @var $match RouteMatch */
        $match = $pEvent->getRouteMatch();

        if ( false === is_object($match))
        {
            /** if there's no route match, we're in a 404 state, abort */
            return;
        }

        $layout = $match->getParam('layout');

        /**
         * This code allows for route configuration based layout overrides
         */
        if (false === empty($layout))
        {
            $pEvent->getViewModel()->setTemplate($layout);
        }
    }
}
