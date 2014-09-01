<?php

/**
 * View Helper - Handles returning the markup for Google Analytics.
 *
 * Specifically it checks for a configuration variable containing the GA property ID for site.
 * If present and not empty, it will place the GA JavaScript on the page.
 */
namespace Cornerstone\Http\ViewHelper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Config\Config;
use Cornerstone\EventManager;

class GoogleAnalytics extends AbstractHelper implements ServiceLocatorAwareInterface
{
  protected $mServiceLocator;
  protected $mPropertyId = '';
  protected $mDomain = '';

  public function __invoke ()
  {
    $this->ConfigureTrackingData();

    return $this->GetResponse();
  }

  /**
   * Helper -- Returns the markup for Google Analytics based on the site configuration
   * @return string HTML markup to display
   */
  protected function GetResponse ()
  {
    $response = '';

    if ( $this->ValidateTrackingData() )
    {
      $data = array ( 'mPropertyId' => $this->mPropertyId, 'mDomain' => $this->mDomain );

      $response = $this->getView()->partial('layout/partials/google-analytics', $data);
    }

    return $response;
  }

  /**
   * Helper -- Set the GA Property ID from the site configuration
   */
  protected function ConfigureTrackingData ()
  {
    /**
     * The double getServiceLocator() is required because the initial call returns a
     * Zend\View\HelperPluginManager. From that, you can get the application wide service
     * locator via the second call.
     *
     * c.f., http://robertbasic.com/blog/working-with-custom-view-helpers-in-zend-framework-2
     */
    $config = new Config($this->getServiceLocator()->getServiceLocator()->get('Config'));

    $tracking = $config->get('Tracking', '');
    if ( !empty($tracking) )
    {
      $google = $tracking->get('GA', '');
      if ( !empty($google) )
      {
        $this->mPropertyId = $google->get('Code', '');
        if ( empty($this->mPropertyId) )
        {
          $em = $this->getServiceLocator()->getServiceLocator()->get('Application\EventManager');

          $details = array ();
          $details['message'] = '"Code" value for "GA" (Google Analytics) configuration block was missing';

          $em->trigger(EventManager\Service::EVENT_GA_CODE_CONFIGURATION_NOT_SET, $this, $details);
        }

        $this->mDomain = $google->get('Domain', '');
        if ( empty($this->mDomain) )
        {
          $em = $this->getServiceLocator()->getServiceLocator()->get('Application\EventManager');

          $details = array ();
          $details['message'] = '"Domain" value for "GA" (Google Analytics) configuration block was missing';

          $em->trigger(EventManager\Service::EVENT_GA_DOMAIN_CONFIGURATION_NOT_SET, $this, $details);
        }

        $this->mTriggerPageView = $google->get('TriggerPageView', 'true');
      }
      else
      {
        $em = $this->getServiceLocator()->getServiceLocator()->get('Application\EventManager');

        $details = array ();
        $details['message'] = '"Tracking -> GA" (Google Analytics) configuration block was missing';

        $em->trigger(EventManager\Service::EVENT_GA_CONFIGURATION_NOT_SET, $this, $details);
      }
    }
    else
    {
      $em = $this->getServiceLocator()->getServiceLocator()->get('Application\EventManager');

      $details = array ();
      $details['message'] = '"Tracking" configuration block was missing';

      $em->trigger(EventManager\Service::EVENT_TRACKING_CONFIGURATION_NOT_SET, $this, $details);
    }
  }

  public function ValidateTrackingData ()
  {
    $result = false;

    if ( !empty($this->mPropertyId) && !empty($this->mDomain) )
    {
      $result = true;
    }

    return $result;
  }

  public function setServiceLocator ( ServiceLocatorInterface $serviceLocator )
  {
    $this->mServiceLocator = $serviceLocator;
  }

  public function getServiceLocator ()
  {
    return $this->mServiceLocator;
  }
}