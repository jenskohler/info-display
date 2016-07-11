<?php
/* (c) 2015 Thomas Smits */
namespace HSMA\InfoDisplay\Controller\View;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class DisplayController
 * @package HSMA\InfoDisplay\Controller\View
 *
 * Index/default page, cycling through the other views of the display.
 */
class DisplayController extends Controller {

    /**
     * Constructor.
     */
    public function __construct() {
    }

    /**
     * Action called when no path is given. It cycles through the
     * different pages.
     *
     * @param Request $request The current request
     *
     * @return Response the response of the action
     */
    public function indexAction(Request $request) {

        $session = $request->getSession();
        $session->start();
        $page = $session->get('page', 0);

        switch ($page++) {
            case 0: $result = $this->forward('InfoDisplayBundle:View\Timetable:timetable', [
                    'request' => $request,
                    'reload'=> true ]);
                    break;
// TODO: Enable news
//            case 1: $result = $this->forward('InfoDisplayBundle:View\News:news', [
//                    'request' => $request,
//                    'reload' => true ]);
//                    break;
            case 1: $result = $this->forward('InfoDisplayBundle:View\Rooms:rooms', [
                    'request' => $request,
                    'reload'=> true ]);
                    break;

            case 2: $result = $this->forward('InfoDisplayBundle:View\Plakat:plakat', [
                'request' => $request,
                'reload'=> true ]);
                break;

            default: $result = $this->forward('InfoDisplayBundle:View\Picture:picture', [
                    'request' => $request,
                    'reload' => true ]);
                    $page = 0;
        }

        $session->set('page', $page);
        return $result;
    }
}
