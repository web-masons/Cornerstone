<?php
/**
 *
 * @author Oakensoul (http://www.oakensoul.com/)
 * @link https://github.com/oakensoul/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package Cornerstone
 */
namespace Cornerstone\Console\Listener;

use Zend\EventManager;
use Zend\ServiceManager;
use Cornerstone\EventManager\Service;
use Cornerstone\EventManager\Console;

class InitializeApplication extends EventManager\AbstractListenerAggregate implements ServiceManager\ServiceLocatorAwareInterface
{

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
    }

    public function EventHandler (Console\Event $pEvent)
    {
        // process event
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