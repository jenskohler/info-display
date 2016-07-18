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

        $controller = [
            'InfoDisplayBundle:View\Timetable:timetable',
//            'InfoDisplayBundle:View\News:news',
            'InfoDisplayBundle:View\Rooms:rooms',
        ];

        if (PlakatController::ready()) {
            $controller[] = 'InfoDisplayBundle:View\Plakat:plakat';
        }

        if (PictureController::ready()) {
            $controller[] = 'InfoDisplayBundle:View\Picture:picture';
        }

        if ($page >= count($controller)) {
            $page = 0;
        }

        $result = $this->forward($controller[$page], [
            'request' => $request,
            'reload'=> true ]);

        $page++;

        $session->set('page', $page);
        return $result;
    }
}
