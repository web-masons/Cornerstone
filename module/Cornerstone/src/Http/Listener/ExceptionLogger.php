<?php
/**
 *
 * @author    Oakensoul (http://www.oakensoul.com/)
 * @link      https://github.com/web-masons/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013, github.com/web-masons Contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache-2.0-Clause
 * @package   Cornerstone
 */
namespace Cornerstone\Http\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\Mvc;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\Console;

class ExceptionLogger extends AbstractListenerAggregate
{

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
        $options[] = 'onDispatchError';

        $this->listeners[] = $events->attach(Mvc\MvcEvent::EVENT_DISPATCH_ERROR, $options, 100);
    }

    /**
     * When ZF2 encounters an unhandled exception, it will return a 500
     * and potentially write it to the screen. However, it never makes
     * it into the apache error logs (and it really should).
     *
     * @param Mvc\MvcEvent $pEvent
     * @return null
     */
    public function onDispatchError (Mvc\MvcEvent $pEvent)
    {
        $request = $pEvent->getRequest();

        // Make sure that we are not running in a console
        if ($request instanceof Console\Request)
        {
            return NULL;
        }

        /** bail out if we're not processing an exception */
        if (Mvc\Application::ERROR_EXCEPTION != $pEvent->getError())
        {
            return NULL;
        }

        $e = $pEvent->getParam('exception');
        error_log($e->getMessage(), E_USER_ERROR);
    }
}
