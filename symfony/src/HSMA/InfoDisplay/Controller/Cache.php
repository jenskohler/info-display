<?php
/* (c) 2016 Thomas Smits */

namespace HSMA\InfoDisplay\Controller;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class Cache
 * @package HSMA\InfoDisplay\Controller
 *
 * Base class for caches.
 */
abstract class Cache {

    /**
     * @var SessionInterface the current session
     */
    private $session;

    /**
     * @var String prefix for variables stored in the session.
     */
    private $prefix;

    /**
     * @var int TTL for cache entries
     */
    private $ttl;

    /**
     * Create a new instance, using the session for storage.
     *
     * @param String $prefix for variables stored in the session.
     * @param int $ttl TTL for session entries
     * @param SessionInterface $session the current session
     */
    public function __construct($prefix, $ttl, SessionInterface $session) {
        $this->session = $session;
        $this->prefix = $prefix;
        $this->ttl = $ttl;
    }

    /**
     * Retrieve data from the cache.
     *
     * @param \DateTime $date the date
     * @param bool $reload if set to true, the cache will not be used.
     *
     * @return mixed the data from the cache.
     */
    public function getData(\DateTime $date = null, $reload = false) {

        $timeStamp = time();

        $this->session->start();
        $lastUpdate = $this->session->get($this->prefix . '_last_update', 0);

        if ($timeStamp > $lastUpdate + $this->ttl || $reload) {

            // cache invalid; retrieve fresh data
            $this->session->set($this->prefix . '_last_update', $timeStamp);

            $data = $this->retrieveData($date);

            $this->session->set($this->prefix . '_data', $data);
        }
        else {
            // cache valid, retrieve data
            $data = $this->session->get($this->prefix . '_data');
        }

        return $data;
    }

    /**
     * Retrieve data from the cache.
     *
     * @param \DateTime $date the date
     * @param bool $reload if set to true, the cache will not be used.
     *
     * @return mixed the data from the cache.
     */
    public function getDataForDate(\DateTime $date, $reload = false) {

        if ($date == null) {
            $date = new \DateTime();
        }

        $result = $this->getData($date, $reload);

        return $this->filterData($result, $date);
    }

    /**
     * Subclasses overwrite this method to retrieve fresh data.
     *
     * @param \DateTime $date the date
     * @return mixed the data retrieved
     */
    protected abstract function retrieveData($date);

    /**
     * Filter the data for the given date.
     *
     * @param mixed $data data to be filtered
     * @param \DateTime $date Date to filter data for
     *
     * @return mixed the data
     */
    protected abstract function filterData($data, \DateTime $date);

}