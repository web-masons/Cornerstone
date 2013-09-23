<?php
namespace ApplicationTest;

error_reporting(E_ALL | E_STRICT);
chdir(__DIR__);

use Zend\Loader\AutoloaderFactory;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;
use RuntimeException;

/**
 * Test bootstrap, for setting up autoloading
 *
 * @subpackage UnitTest
 *            
 */
class Bootstrap
{

    protected static $serviceManager;

    protected static $config;

    protected static $bootstrap;

    public static function init ()
    {
        $test_config = include (is_readable('TestConfig.php')) ? 'TestConfig.php' : 'TestConfig.php.dist';
        
        $test_config['module_listener_options']['config_cache_enabled'] = false;
        $test_config['module_listener_options']['module_map_cache_enabled'] = false;
        
        $zf2ModulePaths = array ();
        
        if (isset($test_config['module_listener_options']['module_paths']))
        {
            $modulePaths = $test_config['module_listener_options']['module_paths'];
            foreach ($modulePaths as $modulePath)
            {
                $path = static::findParentPath($modulePath);
                
                if ((false !== $path))
                {
                    $zf2ModulePaths[] = $path;
                }
            }
        }
        
        $zf2ModulePaths = implode(PATH_SEPARATOR, $zf2ModulePaths) . PATH_SEPARATOR;
        $zf2ModulePaths .= getenv('ZF2_MODULES_TEST_PATHS') ?  : (defined('ZF2_MODULES_TEST_PATHS') ? ZF2_MODULES_TEST_PATHS : '');
        
        static::initAutoloader();
        
        // use ModuleManager to load this module and it's dependencies
        $baseConfig = array (
            'module_listener_options' => array (
                'module_paths' => explode(PATH_SEPARATOR, $zf2ModulePaths)
            )
        );
        
        $config = ArrayUtils::merge($baseConfig, $test_config);
        
        $serviceManager = new ServiceManager(new ServiceManagerConfig());
        $serviceManager->setService('ApplicationConfig', $config);
        $serviceManager->get('ModuleManager')->loadModules();
        
        static::$serviceManager = $serviceManager;
        static::$config = $config;
    }

    protected static function findParentPath ($path)
    {
        $dir = __DIR__;
        $previousDir = '.';
        while (! is_dir($dir . '/' . $path))
        {
            $dir = dirname($dir);
            if ($previousDir === $dir)
                return false;
            $previousDir = $dir;
        }
        return $dir . '/' . $path;
    }

    public static function getServiceManager ()
    {
        return static::$serviceManager;
    }

    public static function getConfig ()
    {
        return static::$config;
    }

    protected static function initAutoloader ()
    {
        $vendorPath = static::findParentPath('vendor');
        
        if (is_readable($vendorPath . '/autoload.php'))
        {
            $loader = include $vendorPath . '/autoload.php';
        }
        else
        {
            throw new RuntimeException('Unable to load ZF2. Run `php composer.phar install`.');
        }
        
        AutoloaderFactory::factory(array (
            'Zend\Loader\StandardAutoloader' => array (
                'autoregister_zf' => true,
                'namespaces' => array (
                    __NAMESPACE__ => __DIR__ . '/' . __NAMESPACE__
                )
            )
        ));
    }
}

Bootstrap::init();