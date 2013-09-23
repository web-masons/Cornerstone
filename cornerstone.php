#!/usr/bin/env php
<?php
/**
 * @link      https://github.com/oakensoul/application-skeleton for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
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