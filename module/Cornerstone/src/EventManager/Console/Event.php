<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license http://framework.zend.com/license/new-bsd New BSD License
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
