<?php
/* (c) 2016 Thomas Smits */
namespace HSMA\InfoDisplay\Controller\View;

use HSMA\InfoDisplay\Controller\Config;
use HSMA\InfoDisplay\Controller\RoomCache;
use HSMA\InfoDisplay\Entity\Domain\Utility;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class RoomsController
 * @package HSMA\InfoDisplay\Controller\View
 *
 * Display information on rooms that are currently free.
 */
class RoomsController extends Controller {

    /**
     * Constructor.
     */
    public function __construct() {
    }

    /**
     * Action for the rooms.
     *
     * @param Request $request the request
     * @param bool $reload if set to true, the page will reload after a timeout.
     *
     * @return Response the response of the action
     */
    public function roomsAction(Request $request, $reload = false) {

        $date = new \DateTime();

        // for testing purposes allow forcing the date
        if ($request->get('date') != null) {
            $date = new \DateTime($request->get('date'));
        }

        // current block we are in
        $block = abs(Utility::hourToBlock($date));

        // Mark first block at the beginning of the day
        if ($block == 6 && $date->format('h') < 8) {
            $block = 0;
        }

        $dayOfWeek = $date->format('N');

        $cache = new RoomCache($request->getSession());
        $plan = $cache->getDataForDate($date, $request->get('reload') != null);

        return $this->render('InfoDisplayBundle:Info:rooms.html.twig', [
            'bookings'  => $plan,
            'rooms' => Config::ALL_ROOMS,
            'block' => $block,
            'time'  => $date->format('d.m.Y G:i'), // $this->getTime(),
            'timeout' => Config::RELOAD_ROOM_PAGE_AFTER,
            'reload'  => $reload,
            'day'     => Utility::dayInGerman($dayOfWeek),
            'times'   => Utility::summerTerm($date) ? Config::TIMES_FOR_BLOCKS_SUMMER : Config::TIMES_FOR_BLOCKS_WINTER,
        ]);
    }
}
