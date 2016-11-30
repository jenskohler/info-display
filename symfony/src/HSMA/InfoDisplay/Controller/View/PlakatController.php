<?php
/* (c) 2016 Thomas Smits */
namespace HSMA\InfoDisplay\Controller\View;

use HSMA\InfoDisplay\Controller\Config;
use HSMA\InfoDisplay\Entity\Domain\Utility;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class PictureController
 * @package HSMA\InfoDisplay\Controller\View
 *
 * Page for the pictures..
 */
class PlakatController extends PictureController {

    /**
     * Constructor.
     */
    public function __construct() {
    }

    /**
     * Check if there is work to be done for the controller.
     *
     * @return bool true if the controller has work to do, otherwise false
     */
    public static function ready() {
        return count(self::scanForFiles('plakat*.jpg')) != 0;
    }

    /**
     * Action for the picture gallery.
     *
     * @param Request $request the request
     * @param bool $reload if set to true, the page will reload after a timeout.
     *
     * @return Response the response of the action
     */
    public function plakatAction(Request $request, $reload = false) {

        $date = new \DateTime();

        $files = self::scanForFiles('plakat*.*');

        $session = $request->getSession();
        $session->start();
        $pictureIndex = $session->get('plakat', 0);

        if ($pictureIndex >= count($files)) {
            $pictureIndex = 0;
        }

        $file = "images/$files[$pictureIndex]";

        $session->set('plakat', ++$pictureIndex);

        return $this->render('InfoDisplayBundle:Info:plakat.html.twig', array(
            'time'  => Utility::getGermanDateAndTime($date),
            'timeout' => Config::RELOAD_PLAKAT_PAGE_AFTER,
            'picture' => $file,
            'reload'  => $reload,
        ));
    }
}
