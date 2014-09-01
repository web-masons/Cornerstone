<?php

/**
 * View Helper - Handles returning the markup for a YouTube iframe embed.
 *
 */
namespace Cornerstone\Http\ViewHelper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Config\Config;
use \Locale;

class Variable extends AbstractHelper
{
  protected $mVar = array();

  public function __invoke ($pKey, $pValue = null)
  {
    if(!empty($pValue))
    {
      $this->mVar[$pKey] = $pValue;
    }

    return $this->mVar[$pKey];
  }
}