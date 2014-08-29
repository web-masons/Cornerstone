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

class NotFoundController extends AbstractActionController
{

    public function indexAction ()
    {
        /* @var \Zend\Http\Response $response */
        $response = $this->getResponse();
        $response->setStatusCode(404);

        return $response;
    }
}
