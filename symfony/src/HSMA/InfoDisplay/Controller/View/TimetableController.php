<?php
/* (c) 2016 Thomas Smits */
namespace HSMA\InfoDisplay\Controller\View;

use HSMA\InfoDisplay\Controller\Config;
use HSMA\InfoDisplay\Controller\TimetableCache;
use HSMA\InfoDisplay\Controller\Viewdata\Timetable;
use HSMA\InfoDisplay\Entity\Domain\Utility;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class TimetableController
 * @package HSMA\InfoDisplay\Controller\View
 *
 * Display timetable for all programs at the faculty.
 */
class TimetableController extends Controller {

    /**
     * Constructor.
     */
    public function __construct() {
    }

    /**
     * Action displaying the timetable.
     *
     * @param Request $request the request
     * @param bool $reload if set to true, the page will reload after a timeout.
     *
     * @return Response the response of the action
     */
    public function timetableAction(Request $request, $reload = false) {

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

        $cache = new TimetableCache($request->getSession());
        $plan = $cache->getDataForDate($date, $request->get('reload') != null);

        $this->readCancellations($plan);

        return $this->render('InfoDisplayBundle:Info:timetable.html.twig', [
            'plan'    => $plan,
            'block'   => $block,
            'time'    => Utility::getGermanDateAndTime($date),
            'timeout' => Config::RELOAD_TIMETABLE_PAGE_AFTER,
            'reload'  => $reload,
            'day'     => Utility::dayInGerman($dayOfWeek),
            'times'   => Utility::summerTerm($date) ? Config::TIMES_FOR_BLOCKS_SUMMER : Config::TIMES_FOR_BLOCKS_WINTER,
        ]);
    }

    /**
     * Read the cancellations and updates the timetable with the information.
     *
     * @param Timetable $timetable the timetable to add cancellations to.
     */
    private function readCancellations(Timetable $timetable) {

        // read cancellations from database
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository('InfoDisplayBundle:Domain\NewsEntry');

        $query = $em->createQuery(
            'SELECT u ' .
            '   FROM InfoDisplayBundle:Domain\CancelledEvent u ' .
            '      WHERE u.valid >= CURRENT_DATE()');
        $cancellations = $query->getResult();
        $cancelledIds= [ ];

        // map to array
        foreach ($cancellations as $c) {
            $cancelledIds[$c->timetableKey] = $c->newsId;
        }

        $timetable->for_each(function($value) use ($cancelledIds, $repo, $timetable) {
            if (isset($cancelledIds[$value->getKey()])) {
                $value->cancelled = true;
                $value->news = $repo->find($cancelledIds[$value->id]);
                $timetable->add($value);
            }
        });
    }
}
