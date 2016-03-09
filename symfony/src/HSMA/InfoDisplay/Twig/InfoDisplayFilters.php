<?php
/* (c) 2014 Thomas Smits */

namespace HSMA\InfoDisplay\Twig;

/**
 * Class InfoDisplayFilters
 * @package HSMA\InfoDisplay\Twig
 *
 * Custom TWIG filter to decode several internal values of the application
 * into a user friendly format.
 */
class InfoDisplayFilters extends \Twig_Extension {

    /**
     * Called by the framework to ask for existing filters.
     *
     * @return array Twig_SimpleFilter[] filters
     */
    public function getFilters() {
        return array(
            new \Twig_SimpleFilter('day_of_week', array($this, 'dayOfWeek')),
            new \Twig_SimpleFilter('hyphen',      array($this, 'nonBreakingHyphen'),
                    array('is_safe' => array('html'))),
            new \Twig_SimpleFilter('space',      array($this, 'nonBreakingSpace'),
                    array('is_safe' => array('html'))),
        );
    }

    /**
     * Decode the semester id (131) into external representation.
     *
     * @param string $dayNumber the id
     *
     * @return string the long format
     */
    public function dayOfWeek($dayNumber) {

        switch ($dayNumber) {
            case 1: return 'Montag';
            case 2: return 'Dienstag';
            case 3: return 'Mittwoch';
            case 4: return 'Donnerstag';
            case 5: return 'Freitag';
            case 6: return 'Samstag';
            case 7: return 'Sonntag';
        }
    }

    /**
     * Replace hyphens with non-breaking variant
     *
     * @param string $string the text to be modified
     *
     * @return string the replaced string
     */
    public function nonBreakingHyphen($string) {
        return str_replace('-', '&nbsp;', $string);
    }

    /**
     * Replace spaces with non-breaking variant
     *
     * @param string $string the text to be modified
     *
     * @return string the replaced string
     */
    public function nonBreakingSpace($string) {
        return str_replace(' ', '&nbsp;', $string);
    }

    /**
     * Return the name of the filter. Called by the framework.
     *
     * @return string the name
     */
    public function getName() {
        return 'infodisplay_extension';
    }
}
