<?php
/**
 *
 * @author    Oakensoul (http://www.oakensoul.com/)
 * @link      https://github.com/web-masons/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013, github.com/web-masons Contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache-2.0-Clause
 * @package   Cornerstone
 */
namespace Cornerstone\EventManager\Console;

use Zend;

class Event extends Zend\EventManager\Event
{

    protected $mVerboseFlag;

    protected $mForceFlag;

    protected $mEnvironment;

    public function setVerboseFlag ($pVerbose)
    {
        $this->mVerboseFlag = true === $pVerbose ? true : false;
    }

    public function getVerboseFlag ()
    {
        return $this->mVerboseFlag;
    }

    public function setForceFlag ($pForce)
    {
        $this->mForceFlag = true === $pForce ? true : false;
    }

    public function getForceFlag ()
    {
        return $this->mForceFlag;
    }

    public function setEnvironment ($pEnvironment)
    {
        $this->mEnvironment = $pEnvironment;
    }

    public function getEnvironment ()
    {
        return $this->mEnvironment;
    }
}
