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

class YouTubeIframe extends AbstractHelper implements ServiceLocatorAwareInterface
{
  protected $mServiceLocator;

  protected $mVideoId;
  protected $mWidth = '100%';
  protected $mHeight = '';
  protected $mYoutubeUrl;
  protected $mCssClasses;
  protected $mQueryString = '';

  public function __invoke ($pData)
  {
    $this->ConfigureIframe($pData);

    return $this->GetResponse();
  }

  /**
   * Helper -- Returns the markup for YouTube iframe embed
   * @return string HTML markup to display
   */
  protected function GetResponse ()
  {
    $data = array ();
    $data['height'] = $this->mHeight;
    $data['width'] = $this->mWidth;
    $data['youtube_url'] = $this->mYoutubeUrl;
    $data['css_classes'] = $this->mCssClasses;
    $data['query_string'] = $this->mQueryString;
    $data['video_id'] = $this->mVideoId;

    return $this->getView()->partial('youtube/iframe', $data);
  }

  /**
   * Helper -- Set the various options based on configuration
   * @param array $pData
   */
  protected function ConfigureIframe ($pData)
  {
    /**
     * The double getServiceLocator() is required because the initial call returns a
     * Zend\View\HelperPluginManager. From that, you can get the application wide service
     * locator via the second call.
     *
     * c.f., http://robertbasic.com/blog/working-with-custom-view-helpers-in-zend-framework-2
     */
    $config = new Config($this->getServiceLocator()->getServiceLocator()->get('Config'));

    if(!isset($pData['language']) || empty($pData['language']))
    {
      $pData['language'] = Locale::getDefault();
      if( 2 !== strlen($pData['language']) )
      {
        $pData['language'] = substr($pData['language'], 0, 2);
      }
    }

    // Use the current timestamp as a default ID attribute value if no URL given
    $this->mVideoId = time();
    if(!empty($pData['youtubeUrl']))
    {
      $this->mYoutubeUrl = $pData['youtubeUrl'];

      // Parse the video ID out of the URL
      $matches = array ();
      $match = preg_match('/.*(embed\/|watch?v=)([^&?]+)/', $pData['youtubeUrl'], $matches);
      if(1 === $match)
      {
        $this->mVideoId = $matches[2];
      }
    }

    $this->mCssClasses = $pData['cssClasses'];
    if(is_array($this->mCssClasses) && !empty($this->mCssClasses))
    {
      $this->mCssClasses = implode(' ', $this->mCssClasses);
    }

    if(isset($pData['width']) && !empty($pData['width']))
    {
      $this->mWidth = $pData['width'];
    }

    if(isset($pData['height']) && !empty($pData['height']))
    {
      $this->mHeight = $pData['height'];
    }

    $query_parameters = array ();

    $html5 = (isset($pData['html5'])) ? $pData['relatedVideos'] : $config->youtube->html5;
    if(!empty($html5))
    {
      $query_parameters[] = 'html5=1';
    }

    $enableJsApi = (isset($pData['enableJsApi'])) ? $pData['enableJsApi'] : $config->youtube->enableJsApi;
    if(!empty($enableJsApi))
    {
      $query_parameters[] = 'enablejsapi=1';
    }

    $relatedVideos = (isset($pData['relatedVideos'])) ? $pData['relatedVideos'] : $config->youtube->relatedVideos;
    if( isset($relatedVideos) && false === $relatedVideos)
    {
      $query_parameters[] = 'rel=0';
    }
    else if( true === $relatedVideos )
    {
      $query_parameters[] = 'rel=1';
    }

    $showVideoTitle = (isset($pData['showVideoTitle'])) ? $pData['showVideoTitle'] : $config->youtube->showVideoTitle;
    if( false === $showVideoTitle)
    {
      $query_parameters[] = 'showinfo=0';
    }
    else if( true === $showVideoTitle )
    {
      $query_parameters[] = 'showinfo=1';
    }

    $annotations = (isset($pData['annotations'])) ? $pData['annotations'] : $config->youtube->annotations;
    if(!empty($annotations))
    {
      $query_parameters[] = 'iv_load_policy=1';
    }

    $closedCaption = false;
    if( isset($config->youtube->closedCaption->{$pData['language']}) && !empty($config->youtube->closedCaption->{$pData['language']}) )
    {
      $closedCaption = true;
    }

    if($closedCaption)
    {
      $query_parameters[] = 'cc_load_policy=1';
    }

    $modestBranding = (isset($pData['modestBranding'])) ? $pData['modestBranding'] : $config->youtube->modestBranding;
    if(!empty($modestBranding))
    {
      $query_parameters[] = 'modestbranding=1';
    }

    $protocol = $_SERVER['HTTPS'] ? 'https' : 'http';
    $query_parameters[] = 'origin=' . $protocol . '://' . $_SERVER['SERVER_NAME'];

    // NOTE: This parameter must be the last in the list; YouTube doesn't seem to respect it otherwise
    if($closedCaption)
    {
      $query_parameters[] = 'cc_lang_pref=' . $pData['language'];
    }

    if(!empty($query_parameters))
    {
      $this->mQueryString = implode('&', $query_parameters);
    }
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