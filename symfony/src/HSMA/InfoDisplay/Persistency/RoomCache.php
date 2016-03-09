<?php
namespace HSMA\InfoDisplay\Persistency;

use HSMA\InfoDisplay\Entity\Domain\Room;

class RoomCache {

    public $rooms = [];
    public $lastAccess;
    public $ttl;

    public function __construct($ttl) {
        $this->lastAccess = 0;
        $this->ttl = $ttl;
    }

    public function getAllRooms() {
        $this->refreshData();
        return $this->rooms;
    }

    public function invalidate() {
        $this->lastAccess = 0;
    }

    private function refreshData() {
        if ($this->lastAccess + $this->ttl >= time()) {
            return;
        }

        $url = "https://services.informatik.hs-mannheim.de/rooms/api/room";

        $restResponse = \Httpful\Request::get($url)
            ->expectsJson()
            ->send();

        $json = $restResponse->body;
        $rooms = [];

        foreach ($json->rooms as $room) {
            $roomObject = new Room(
                $room->id,
                $room->name,
                $room->description,
                $room->capacity,
                $room->usesblock == 1,
                $room->link);

            $rooms[$room->name] = $roomObject;
        }

        $this->rooms = $rooms;
    }
}