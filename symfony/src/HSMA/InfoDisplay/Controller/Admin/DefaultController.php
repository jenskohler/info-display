<?php
/* (c) 2015 Thomas Smits */
namespace HSMA\InfoDisplay\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class DefaultController
 * @package HSMA\InfoDisplay\Controller\Admin
 *
 * Index/default page.
 */
class DefaultController extends Controller {

    /**
     * Constructor.
     */
    public function __construct() {
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction() {

        return $this->render('InfoDisplayBundle:Admin:index.html.twig', array(
            'admin' => $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'),
            'user'  => $this->getUser(),
        ));
    }
}
