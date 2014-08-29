#!/usr/bin/env php
<?php
/**
 *
 * @author    Oakensoul (http://www.oakensoul.com/)
 * @link      https://github.com/web-masons/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013, github.com/web-masons Contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache-2.0-Clause
 * @package   Cornerstone
 */

/**
 * Set application env from the command line parameter.
 *
 * This allows the application to read in an appropriate config file
 * during the application bootstrap.
 */
if (! defined('APPLICATION_ENV'))
{
    $env = '';
    foreach ($argv as $cur)
    {
        if (stristr($cur, '--env=') !== false)
        {
            $parts = explode('=', $cur);
            $env = $parts[1];
        }
    }
    define('APPLICATION_ENV', $env);
}

require 'public/index.php';
