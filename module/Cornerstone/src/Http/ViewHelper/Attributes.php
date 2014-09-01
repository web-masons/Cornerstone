<?php

/**
 * View Helper - Adds attributes to specific tags so views can modify the attributes on tags in the layout
 *
 */
namespace Cornersteon\Http\ViewHelper;

use Zend\View\Helper\AbstractHelper;

class Attributes extends AbstractHelper
{
  protected $mServiceLocator;
  protected $mAttributes = array();

  public function __invoke ( $pTagName, $pKey = null, $pValue = null )
  {
    if(!empty($pKey) && !empty($pTagName) && is_string($pValue))
    {
      $this->mAttributes[$pTagName][$pKey] = $pValue;
    }
    else
    {
      $result = '';
      if(isset($this->mAttributes[$pTagName]) && is_array($this->mAttributes[$pTagName]))
      {
        foreach($this->mAttributes[$pTagName] as $key => $value)
        {
          $result .= ' ' . $key . '="'.$value.'"';
        }
      }

      return $result;
    }
  }
}