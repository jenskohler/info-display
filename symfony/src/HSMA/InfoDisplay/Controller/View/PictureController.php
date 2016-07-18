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
class PictureController extends Controller {

    /**
     * Path to the pictures, relative to the location of this class in the file
     * system.
     */
    const PATH_TO_PICTURES = '/../../../../../web/images';

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
        return count(self::scanForFiles('pic*.jpg')) != 0;
    }

    /**
     * Action for the picture gallery.
     *
     * @param Request $request the request
     * @param bool $reload if set to true, the page will reload after a timeout.
     *
     * @return Response the response of the action
     */
    public function pictureAction(Request $request, $reload = false) {

        $date = new \DateTime();

        $files = self::scanForFiles('pic*.jpg');

        $session = $request->getSession();
        $session->start();
        $pictureIndex = $session->get('picture', 0);

        if ($pictureIndex >= count($files)) {
            $pictureIndex = 0;
        }

        $file = "images/$files[$pictureIndex]";

        $session->set('picture', ++$pictureIndex);

        return $this->render('InfoDisplayBundle:Info:picture.html.twig', array(
            'time'  => Utility::getGermanDateAndTime($date),
            'timeout' => Config::RELOAD_PICTURE_PAGE_AFTER,
            'picture' => $file,
            'reload'  => $reload,
        ));
    }

    /**
     * Can picture directory for files matching the given pattern.
     *
     * @param string $pattern pattern for file name
     * @return string[] names of found files
     */
    protected static function scanForFiles($pattern) {
        // scan for existing pictures
        $finder = new Finder();
        $finder->ignoreUnreadableDirs()
            ->in(__DIR__ . self::PATH_TO_PICTURES)
            ->files()
            ->name($pattern);

        $files = [ ];

        // get the names of the files
        foreach ($finder as $file) {
            $files[] = pathinfo($file)['basename'];
        }

        return $files;
    }
}
