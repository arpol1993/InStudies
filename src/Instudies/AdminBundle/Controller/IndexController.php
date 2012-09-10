<?php

namespace Instudies\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/admin")
 */
class IndexController extends Controller
{
    /**
     * @Route("/", name="admin_index")
     * @Template()
     */
    public function indexAction()
    {
    	return array();
    }
    
    /**
     * @Route("/management", name="admin_management")
     */
    public function managementAction()
    {
    	return $this->forward('InstudiesAdminBundle:Management\User:index');
    }
    
    /**
     * @Route("/statistics", name="admin_statistics")
     */
    public function statisticsAction()
    {
    	return $this->forward('InstudiesAdminBundle:Statistics\Statistics:index');
    }
}
