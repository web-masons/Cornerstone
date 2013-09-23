<?php
/**
 * @link      https://github.com/oakensoul/application-skeleton for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package   Cornerstone
 */

$environment = (getenv('APPLICATION_ENV') ?  : 'www');
if (defined('APPLICATION_ENV'))
{
    $environment = APPLICATION_ENV;
}

/**
 * This array is responsible for returning the base application configuration.
 * This will be used to find all of the modules that ZF2 needs to load and boostrap.
 *
 * @var array $application_config
 */
$application_config = array ();

$modules = array (
    'Cornerstone'
);
$application_config['modules'] = $modules;

/**
 * Module Listener Options :: Module Paths
 *
 * @var array $module_paths
 */
$module_paths = array (
    './module',
    './vendor'
);
$application_config['module_listener_options']['module_paths'] = $module_paths;

/**
 * Module Listener Options :: Config Paths
 *
 * @var array $config_paths
 */
$config_paths = array (
    'config/autoload/{,*.}global.php',
    'config/autoload/{,*.}' . $environment . '.json',
    'config/autoload/{,*.}local.php'
);

$application_config['module_listener_options']['config_glob_paths'] = $config_paths;

/**
 * Module Listener Options :: Config Cache
 *
 * Whether or not to enable a configuration cache. If enabled, the merged
 * configuration will be cached and used in subsequent requests.
 *
 * @var bool $config_cache_enabled
 */
$config_cache_enabled = 'www' == $environment ? true : false;
$application_config['module_listener_options']['config_cache_enabled'] = $config_cache_enabled;

/**
 * Module Listener Options :: Config Cache Key
 *
 * The key used to create the configuration cache file name.
 *
 * @var string $application_config['module_listener_options']['config_cache_key']
 */
$application_config['module_listener_options']['config_cache_key'] = 'application_config_cache';

/**
 * Module Listener Options :: Module Map Cache Enabled
 *
 * Whether or not to enable a module class map cache. If enabled, creates a module
 * class map cache which will be used by in future requests, to reduce the autoloading process.
 *
 * @var bool $module_map_cache_enabled
 */
$module_map_cache_enabled = 'www' == $environment ? true : false;
$application_config['module_listener_options']['module_map_cache_enabled'] = $module_map_cache_enabled;

/**
 * Module Listener Options :: Module Map Cache Key
 *
 * The key used to create the class map cache file name.
 *
 * @var string $application_config['module_listener_options']['module_map_cache_key']
 */
$application_config['module_listener_options']['module_map_cache_key'] = 'application_modulemap_cache';

/**
 * Module Listener Options :: Cache Directory
 *
 * The path in which to cache merged configuration.
 *
 * @var string $application_config['module_listener_options']['cache_dir']
 */
$application_config['module_listener_options']['cache_dir'] = './data/cache/application';

/**
 * Module Listener Options :: Check Dependencies
 *
 * Whether or not to enable modules dependency checking. Enabled by default, prevents usage
 * of modules that depend on other modules that weren't loaded.
 *
 * Note: This is set to true in development, and false in production in expectation that
 * any dependencies should be found during development, testing and staging... not in production.
 * Not that it should be much overhead, but no need to add more.
 *
 * @var string $application_config['module_listener_options']['cache_dir']
 */
$check_dependencies = 'www' == $environment ? false : true;
$application_config['module_listener_options']['check_dependencies'] = $check_dependencies;

/**
 * Initial configuration settings to alter ServiceManager behavior.
 * May contain one or more child arrays.
 *
 * array(
 * 'service_manager' => $stringServiceManagerName,
 * 'config_key' => $stringConfigKey,
 * 'interface' => $stringOptionalInterface,
 * 'method' => $stringRequiredMethodName,
 * ),
 *
 * @var array $service_manager
 */
$application_config['service_manager'] = array ();

/**
 * Initial configuration with which to seed the ServiceManager.
 * Should be compatible with Zend\ServiceManager\Config.
 *
 * @var array $service_listener_options
 */
$application_config['service_listener_options'] = array ();

return $application_config;
