<?php
/**
 *
 * @author    Oakensoul (http://www.oakensoul.com/)
 * @link      https://github.com/web-masons/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013, github.com/web-masons Contributors
 * @license   http://opensource.org/licenses/Apache-2.0 Apache-2.0-Clause
 * @package   Cornerstone
 */
namespace Cornerstone\Http\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{

    public function indexAction ()
    {
        $view = new ViewModel();
        $view->setTemplate('application/index/index');

        return $view;
    }
}
