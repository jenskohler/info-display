<?php
/* (c) 2016 Thomas Smits */
namespace HSMA\InfoDisplay\Controller\View;

use HSMA\InfoDisplay\Controller\Config;
use HSMA\InfoDisplay\Entity\Domain\NewsEntry;
use HSMA\InfoDisplay\Entity\Domain\Utility;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class NewsController
 * @package HSMA\InfoDisplay\Controller\View
 *
 * Page displaying the news.
 */
class NewsController extends Controller {

    /**
     * Constructor.
     */
    public function __construct() {
    }

    /**
     * Action for the news of the day.
     *
     * @param Request $request the request
     * @param bool $reload if set to true, the page will reload after a timeout.
     *
     * @return Response the response of the action
     */
    public function newsAction(Request $request, $reload = false) {
        $news = $this->readNews();
        $date = new \DateTime();

        return $this->render('InfoDisplayBundle:Info:news.html.twig', array(
            'time'  => Utility::getGermanDateAndTime($date),
            'timeout' => Config::RELOAD_TIMETABLE_PAGE_AFTER,
            'news' => $news,
            'reload'  => $reload,
        ));
    }

    /**
     * Read the news from teh database.
     *
     * @return NewsEntry[] the news
     */
    private function readNews() {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT u ' .
            '   FROM InfoDisplayBundle:Domain\NewsEntry u ' .
            '      WHERE u.valid >= CURRENT_DATE()' .
            '   ORDER BY u.posted ASC');
        $news = $query->getResult();

        return $news;
    }
}
