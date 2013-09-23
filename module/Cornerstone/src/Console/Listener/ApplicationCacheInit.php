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

use Exception;
use Zend\EventManager;
use Zend\ServiceManager;
use Cornerstone\EventManager\Service;

class ApplicationCacheInit extends EventManager\AbstractListenerAggregate implements ServiceManager\ServiceLocatorAwareInterface
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
        $this->listeners[] = $pEventManager->attach(Service::EVENT_APPLICATION_CACHE_INIT, $options, 1);
    }

    public function EventHandler (EventManager\Event $pEvent)
    {
        $params = $pEvent->getParams();
        
        /**
         * vhost generation
         */
        
        /**
         * application cache init
         */
        /**
         * check config
         */
        /**
         * check integration
         */
        \Zend\Debug\Debug::dump('TEST');
    
    /**
     * attach the "application cache init" strategy to the InitializeApplication event and the ApplicationCacheInit"
     * event
     */
        
        // $logger = $this->getServiceLocator()->get('Logger\Event');
        // $message = (array_key_exists('message', $params)) ? $params['message'] : 'Storefront\Event';
        // $logger->info($message, $params);
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