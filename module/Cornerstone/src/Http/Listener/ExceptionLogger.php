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
use Locale;
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

        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, $options, 100);
    }

    public function onDispatchError (MvcEvent $pEvent)
    {
        $request = $pEvent->getRequest();

        // Make sure that we are not running in a console
        if ($request instanceof Console\Request)
        {
            return;
        }

        /** bail out if we're not processing an exception */
        if (\Zend\Mvc\Application::ERROR_EXCEPTION != $pEvent->getError())
        {
            return;
        }

        $e = $pEvent->getParam('exception');
        error_log($e->getMessage(), E_USER_ERROR);

        /** I need to set up a zend logger default strategy */
//         $service_manager = $pEvent->getApplication()->getServiceManager();
//         $logger = $service_manager->get('Logger\General');
//         $logger->LogException($e);
    }
}