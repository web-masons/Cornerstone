<?php
/**
 *
 * @author    Oakensoul (http://www.oakensoul.com/)
 * @link      https://github.com/web-masons/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013, github.com/web-masons Contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache-2.0-Clause
 * @package   Cornerstone
 */

/*
 * use paths relative to the application root, that way getcwd is reliable.
 */
chdir(dirname(__DIR__));

// Setup autoloading
if (file_exists('vendor/autoload.php'))
{
    $loader = include 'vendor/autoload.php';
}

if (! class_exists('Zend\Loader\AutoloaderFactory'))
{
    throw new RuntimeException('Unable to load ZF2. Run `php composer.phar install`.');
}

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
