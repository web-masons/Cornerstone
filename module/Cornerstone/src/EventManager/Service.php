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
    const EVENT_APPLICATION_CHECK_CONFIGURATION = 'ApplicationCheckConfiguration';
    const EVENT_APPLICATION_CHECK_INTEGRATION = 'ApplicationCheckIntegration';
    const EVENT_APPLICATION_INITIALIZE = 'ApplicationInitialize';
    const EVENT_APPLICATION_CACHE_INIT = 'ApplicationCacheInit';
    const EVENT_APPLICATION_CACHE_EMPTY = 'ApplicationCacheEmpty';
    const EVENT_APPLICATION_BUILD_VHOST = 'ApplicationBuildVhost';
    const EVENT_APPLICATION_CASPER = 'ApplicationCasper';

    const EVENT_TRACKING_CONFIGURATION_NOT_SET = 'TrackingConfigurationNotSet';
    const EVENT_GA_CONFIGURATION_NOT_SET = 'GoogleAnalyticsConfigurationNotSet';
    const EVENT_GA_CODE_CONFIGURATION_NOT_SET = 'GoogleAnalyticsCodeConfigurationNotSet';
    const EVENT_GA_DOMAIN_CONFIGURATION_NOT_SET = 'GoogleAnalyticsDomainConfigurationNotSet';
}