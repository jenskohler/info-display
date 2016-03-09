<?php
/* (c) 2015 Thomas Smits */
namespace HSMA\InfoDisplay\Controller\API;

use HSMA\InfoDisplay\Entity\Domain\Lecturer;
use HSMA\InfoDisplay\Entity\Domain\TimeTableEntry;
use HSMA\InfoDisplay\Entity\Domain\Room;
use HSMA\InfoDisplay\Entity\Domain\Booking;
use HSMA\InfoDisplay\Persistency\RESTClient;
use HSMA\InfoDisplay\Persistency\RoomCache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class NewsController
 * @package HSMA\InfoDisplay\Controller\API
 *
 * Post news to the info-display.
 */
class ApiController extends Controller {

    /**
     * Constructor.
     */
    public function __construct() {
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function retrieveLecturesForLecturerAction($lecturerShortname, Request $request) {

        // Open repositories
        $repoLecturer = $this->getDoctrine()->getRepository('InfoDisplayBundle:Domain\Lecturer');
        $repoTimeTable = $this->getDoctrine()->getRepository('InfoDisplayBundle:Domain\TimeTableEntry');

        /** @var Lecturer $lecturer */
        $lecturer = $repoLecturer->findOneBy(['shortName' => $lecturerShortname]);

        /** @var TimeTableEntry[] $timeTable */
        $timeTable = $repoTimeTable->findBy(['lecturer' => $lecturerShortname]);

        $result = [];

        foreach ($timeTable as $entry) {
            $result[] = $entry->toJSON();
        }

        $json = [
            'lecturer' => $lecturer->toJSON(),
            'lectures' => $result,
        ];

        $response = new StreamedResponse();
        $response->headers->add([
            'Content-type' => 'application/json; charset=utf-8'
        ]);


        $response->setCallback(function () use ($json) {
            print(json_encode($json));
            flush();
            ob_flush();
        });

        return $response;
    }


    /**
     * Wrapper for the REST API for the internal room management solution.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function retrieveAllRoomsAction(Request $request) {

        $cache = new RoomCache(100);

        $response = new StreamedResponse();
        $response->headers->add([
            'Content-type' => 'application/json; charset=utf-8'
        ]);

        $response->setCallback(function () use ($cache) {
            print_r($cache->getAllRooms());
            flush();
            ob_flush();
        });

        return $response;
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function synchronizeAllRoomsAction(Request $request) {

        $dbh = $this->getDoctrine()->getConnection();
        $dbh->query('DELETE FROM room');
        unset($dbh);

        $client = new RESTClient('https://services.informatik.hs-mannheim.de');

        $rooms = $client->readAllRooms();

        $manager = $this->getDoctrine()->getManager();

        foreach ($rooms as $room) {
            $manager->persist($room);
        }

        $manager->flush();

        return $this->redirectToRoute('all_rooms');
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function synchronizeBookingsAction($room, Request $request) {

        $dbh = $this->getDoctrine()->getConnection();
        $pstm = $dbh->prepare('DELETE FROM booking WHERE room=?');
        $pstm->bindValue(1, $room);
        $pstm->execute();
        unset($dbh);

        $manager = $this->getDoctrine()->getManager();

        $client = new RESTClient('https://services.informatik.hs-mannheim.de');
        $bookings = $client->readRoomBookingsForTimeSpan($room, new \DateTime('2015-10-19'), new \DateTime('2015-10-30'));

        print_r($bookings);

        foreach ($bookings as $booking) {
            $manager->persist($booking);
        }

        $manager->flush();

        return null;//$this->redirectToRoute('all_rooms');
    }
}
