<?php
namespace Cornerstone\Http\Listener;

use Exception;
use Zend\Mvc\Application;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\Config\Config;
use Zend\Http\Request;

class RedirectNotFound extends AbstractListenerAggregate
{

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        /**
         * add onDispatch event to Dispatcher
         */
        $options = array();
        $options[] = $this;
        $options[] = 'onNotFound';

        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, $options, 100);
    }

    public function onNotFound(MvcEvent $pEvent)
    {
        if($pEvent->getRequest() instanceof Request)
        {
            if (Application::ERROR_ROUTER_NO_MATCH != $pEvent->getError())
            {
                return;
            }

            $service_manager = $pEvent->getApplication()->getServiceManager();
            $config = $service_manager->get('Config');

            $requestUri = $pEvent->getRequest()->getRequestUri();

            if( array_key_exists('UrlRedirects', $config) && !empty($config['UrlRedirects']) ) {
                $redirects = $config['UrlRedirects'];
                if( array_key_exists($requestUri, $redirects) && !empty($redirects[ $requestUri ]) ) {
                    $response = $pEvent->getResponse();
                    $response->setStatusCode(301);
                    $response->getHeaders()->addHeaderLine('Location', $redirects[ $requestUri ]);
                    $response->sendHeaders();
                    exit();
                }
            }
        }
    }
}