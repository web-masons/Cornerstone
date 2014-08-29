<?php
/**
 * Returns URL to alternate locale, or the home page if it does not exist
 *
 * @author    Oakensoul (http://www.oakensoul.com/)
 * @link      https://github.com/web-masons/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013, github.com/web-masons Contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache-2.0-Clause
 * @package   Cornerstone
 */
namespace Cornerstone\Http\ViewHelper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ChangeLocale extends AbstractHelper implements ServiceLocatorAwareInterface
{

    protected $mServiceLocator;

    public function __invoke ($pLocale)
    {
        $sm = $this->getServiceLocator()->getServiceLocator();

        $router = $sm->get('router');
        $request = $sm->get('request');

        $match = $router->match($request);

        if (is_null($match))
        {
            /* we hit a 404 */
            $params['lang'] = $pLocale;
            $options['name'] = 'home';
        }
        else
        {
            $match->setParam('lang', $pLocale);
            $params = $match->getParams();
            $options['name'] = $match->getMatchedRouteName();
        }

        $url = $router->assemble($params, $options);

        return $url;
    }

    public function setServiceLocator (ServiceLocatorInterface $serviceLocator)
    {
        $this->mServiceLocator = $serviceLocator;
    }

    public function getServiceLocator ()
    {
        return $this->mServiceLocator;
    }
}
