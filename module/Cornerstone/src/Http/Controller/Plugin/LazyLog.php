<?php
/**
 *
 * @author Oakensoul (http://www.oakensoul.com/)
 * @link https://github.com/oakensoul/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package Cornerstone
 */
namespace Cornerstone\Http\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Cornerstone;

class LazyLog extends AbstractPlugin
{

    /**
     * Allows a Lazy log item to be passed, generally good for debugging.
     * This
     * really shouldn't be used throughout your application. If you want module
     * or application specific logging, you should add a logging strategy.
     *
     * Pass in the array of items you would like passed into the logs. General
     * should be used for logging general information that is not related to an
     * event, for event logging use LogEvent
     *
     * It's generally good practice to log items to the general log if they're
     * going to stay on your web server and aren't used for calculating metrics.
     *
     * @param array $pParams            
     */
    public function LogGeneral ($pParams = array())
    {
        return $this->getController()
            ->getServiceLocator()
            ->get('application')
            ->getEventManager()
            ->trigger(Cornerstone\Module::LOG_GENERAL, NULL, $pParams);
    }

    /**
     * Allows a Lazy log item to be passed, generally good for event debugging.
     * This really shouldn't be used throughout your application. If you want
     * module or application specific logging, you should add a logging strategy.
     *
     * Pass in the array of items you would like passed into the logs. Event
     * should be used for logging events that occur within the application, such
     * as a user logging in, failing to log in, registering etc.
     *
     * It's generally good practice to keep event logs free of PII so that they
     * can be sent to an aggregation system, etc.
     *
     * @param array $pParams            
     */
    public function LogEvent ($pParams = array())
    {
        return $this->getController()
            ->getServiceLocator()
            ->get('application')
            ->getEventManager()
            ->trigger(Cornerstone\Module::LOG_EVENT, NULL, $pParams);
    }
}


