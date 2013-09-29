<?php
/**
 *
 * @author Oakensoul (http://www.oakensoul.com/)
 * @link https://github.com/oakensoul/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package Cornerstone
 */
namespace Cornerstone\Console\Controller;

use Cornerstone\EventManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\ColorInterface;
use Zend\Console;
use Exception;
use Zend\Console\Response;

class ApplicationController extends AbstractActionController
{

    protected $mForce = false;

    protected $mVerbose = false;

    protected $mEnvironment = 'production';

    protected $mEvent;

    protected $mAction;

    /**
     * these should be treated as a bitmask, numbers should go in bit math sequence
     * i.e.
     * 1, 2, 4, 8, ...
     *
     * @todo However, now that i'm switching everything over to be event driven... this
     *       won't work as well, I need to figure out another solution...
     *
     *       My current thoughts are that when I have time I'll create an invokable
     *       service that will allow the various listeners to register the error
     *       states they care about and the service itself will handle taking
     *       those registered errors and assigning them an appropriate bitmask
     *       structure. I'm a bit worried that this will be rather dynamic and
     *       could cause issues... thus I haven't implemented it yet.
     *
     *       const ERROR_UNDEFINED = 1;
     *       const ERROR_LISTENER_OPTIONS_NOT_CONFIGURED = 2;
     *       const ERROR_FAILED_TO_CREATE_CACHE_FOLDER = 4;
     *       const ERROR_CACHE_FOLDER_NOT_WRITABLE = 8;
     */

    /**
     * This action handles console requests to the Application.
     * It is expected that
     * the route that matches this action will set an "event" that is derived from
     * the Cornerstone\EventManager\Service
     *
     * It will handle adding a bunch of verbose error logging and will trigger the
     * provided 'event' through the EventManager. It is expected that developers
     * will attach Listeners to these events to handle the various calls that get
     * implemented.
     *
     * It is recommended that this is done by creating strategies in the Module.php
     * file by defining them in the getServiceConfig function, and calling them
     * in the onBootstrap method. Please see the packaged Module.php file for
     * examples.
     *
     * @return \Zend\Console\Response
     */
    public function eventAction ()
    {
        /* This method makes sure we're in a console view, if not, tosses an exception */
        $this->RequireConsoleRequest();

        $this->mConsole = $this->getServiceLocator()->get('console');
        $this->mForce = $this->params('force', false);
        $this->mEvent = $this->params('event', false);
        $this->mVerbose = $this->params('verbose', false);
        $this->mEnvironment = $this->params()->fromRoute('env', $this->mEnvironment);

        /**
         * Output the standard Console Flag for verbose output
         */
        if (true == $this->mVerbose)
        {
            $this->mConsole->clearScreen();

            $this->mConsole->write("    [Controller] ", ColorInterface::LIGHT_GREEN);
            $this->mConsole->writeLine(__CLASS__, ColorInterface::YELLOW);

            $this->mConsole->write("        [Action] ", ColorInterface::LIGHT_GREEN);
            $this->mConsole->writeLine(__FUNCTION__, ColorInterface::YELLOW);

            $this->mConsole->write("       [Verbose] ", ColorInterface::LIGHT_GREEN);
            $this->mConsole->writeLine('true', ColorInterface::YELLOW);

            $this->mConsole->write("         [Force] ", ColorInterface::LIGHT_GREEN);
            $this->mConsole->writeLine(true === $this->mForce ? 'true' : 'false', ColorInterface::YELLOW);

            $this->mConsole->write(" [Event Manager] ", ColorInterface::LIGHT_GREEN);
            $this->mConsole->writeLine('Cornerstone\EventManager\Service', ColorInterface::YELLOW);

            $this->mConsole->write("       [Trigger] ", ColorInterface::LIGHT_GREEN);
            $this->mConsole->writeLine($this->mEvent, ColorInterface::YELLOW);
            $this->mConsole->writeLine();
        }

        $event = new EventManager\Console\Event();
        $event->setName($this->mEvent);
        $event->setTarget($this);
        $event->setForceFlag($this->mForce);
        $event->setVerboseFlag($this->mVerbose);
        $event->setEnvironment($this->mEnvironment);

        $event_result = $this->EventManager()->trigger($event);

        $errors_encountered = false;

        $response = new Response();
        $response->setErrorLevel(0);

        foreach ($event_result as $result)
        {
            if (is_object($result))
            {
                if (0 < $result->getErrorLevel())
                {
                    $this->mConsole->write("[Error] ", ColorInterface::RED);

                    /* we have to use error log here so that it will write to stderr instead of stdout */
                    error_log($result->getContent());

                    $error_level = $result->getErrorLevel() + $response->getErrorLevel();
                    $response->setErrorLevel($error_level);
                }
                else
                {
                    $this->mConsole->writeLine($result->getContent());
                }
            }
        }

        if (true == $this->mVerbose && 0 == $response->getErrorLevel())
        {
            $this->mConsole->write(' --------------- ', ColorInterface::LIGHT_GREEN);
            $this->mConsole->writeLine('-----------------------------------------------------------', ColorInterface::YELLOW);

            $this->mConsole->write("     [Completed] ", ColorInterface::LIGHT_GREEN);
            $this->mConsole->writeLine("Event processing", ColorInterface::YELLOW);
        }

        return $response;
    }

    /**
     * RequireConsoleRequest
     *
     * This method makes sure that we're in a console request, if we're not, it will
     * throw a RuntimeException. Technically Zend automatically protects against this
     * unless, but I've added it so that a route doesn't accidentally get added and
     * expose it.
     *
     * @throws \RuntimeException
     */
    protected function RequireConsoleRequest ()
    {
        $request = $this->getRequest();

        // Make sure that we are running in a console and that we have not somehow
        // accidentally exposed this route to http traffic
        if (! $request instanceof Console\Request)
        {
            throw new \RuntimeException('You can only use this action from a console!');
        }
    }

    /**
     * Returns the Cornerstone Event Manger Service for logging functionality
     *
     * @return Cornerstone\EventManager\Service
     */
    protected function EventManager ()
    {
        if (empty($this->mEventManager))
        {
            $this->mEventManager = $this->getServiceLocator()->get('Application\EventManager');
            $this->mEventManager->setEventClass('Zend\Mvc\MvcEvent');
        }

        return $this->mEventManager;
    }
}