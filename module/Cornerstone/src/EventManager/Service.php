<?php
/**
 *
 * @author Oakensoul (http://www.oakensoul.com/)
 * @link https://github.com/oakensoul/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package Cornerstone
 */
namespace Cornerstone\EventManager;

use Zend\EventManager;

class Service extends EventManager\EventManager
{
    const EVENT_TRACKING_CONFIGURATION_NOT_SET = 'TrackingConfigurationNotSet';
    const EVENT_CHECK_APPLICATION_CONFIGURATION = 'CheckApplicationConfiguration';
    const EVENT_CHECK_APPLICATION_INTEGRATION = 'CheckApplicationIntegration';
    const EVENT_APPLICATION_INITIALIZE = 'ApplicationInitialize';
    const EVENT_APPLICATION_CACHE_INIT = 'ApplicationCacheInit';
}