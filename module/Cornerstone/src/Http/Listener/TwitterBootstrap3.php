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

class TwitterBootstrap3 extends AbstractListenerAggregate
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

        $application = $pEvent->getApplication();

        // Getting the view helper manager from the application service manager
        $view_helper_manager = $application->getServiceManager()->get('viewHelperManager');

        /**
         * Configure all the headScript items we want
         */
        $head_script = $view_helper_manager->get('headScript');

        $head_script->appendFile('/bootstrap/js/html5shiv.js', 'text/javascript', array (
            'conditional' => 'lt IE 9'
        ));
        $head_script->appendFile('/bootstrap/js/respond.min.js', 'text/javascript', array (
            'conditional' => 'lt IE 9'
        ));

        /**
         * Configure all the inlineScript items we want
         */
        $inline_script = $view_helper_manager->get('inlineScript');

        $inline_script->appendFile('//code.jquery.com/jquery.js');
        $inline_script->appendFile('//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js');

        /**
         * Configure all the headLink items we want
         */
        $head_link = $view_helper_manager->get('headLink');

        $link = array ();
        $link['rel'] = 'stylesheet';
        $link['href'] = '//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css';
        $link['type'] = 'text/css';
        $link['media'] = 'screen';
        $head_link($link, 'APPEND');

        $link = array ();
        $link['rel'] = 'stylesheet';
        $link['href'] = '//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-theme.min.css';
        $link['type'] = 'text/css';
        $link['media'] = 'screen';
        $head_link($link, 'APPEND');
    }
}