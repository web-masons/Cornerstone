<?php
/**
 *
 * @author    Oakensoul (http://www.oakensoul.com/)
 * @link      https://github.com/web-masons/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013, github.com/web-masons Contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache-2.0-Clause
 * @package   Cornerstone
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
}
