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

use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\Mvc\Router\Http\TreeRouteStack;
use Locale;
use Zend\Console;

class Localization extends AbstractListenerAggregate
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
        $options[] = 'onDispatch';

        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, $options, 100);
    }

    public function onDispatch (MvcEvent $pEvent)
    {
        $request = $pEvent->getRequest();

        // Make sure that we are not running in a console
        if ($request instanceof Console\Request)
        {
            return NULL;
        }

        /* @var \Zend\Mvc\Router\RouteMatch $match */
        $match = $pEvent->getRouteMatch();

        /* locale selected */
        $lang_selected = $match->getParam('lang');

        $default_language = 'en';

        /**
         * This code basically just makes sure that when we dispatch
         * a route the user is forced to use a localized route if the
         * system is configured to enable the feature
         */
        if (is_null($lang_selected) && true === $match->getParam('force_localized_route', false))
        {
            /* @var $router TreeRouteStack */
            $router = $pEvent->getRouter();

            /* @todo remove hard coded 'en' default language */
            $params = $match->getParams();
            $params['lang'] = $default_language;

            $options = array ();
            $options['name'] = $match->getMatchedRouteName();

            $url = $router->assemble($params, $options);

            /* @var \Zend\Http\PhpEnvironment\Response $response */
            $response = $pEvent->getResponse();

            $response->setStatusCode(302);
            $response->getHeaders()->addHeaderLine('Location', $url);
            $response->sendHeaders();
            return $response;
        }

        if (empty($lang_selected))
        {
            $lang_selected = $default_language;
        }

        Locale::setDefault($lang_selected);

        return NULL;
    }
}