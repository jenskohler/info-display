<?php
/* (c) 2016 Thomas Smits */

namespace HSMA\InfoDisplay\Controller;

/**
 * Class Config
 * @package HSMA\InfoDisplay\Controller
 *
 * Basic configuration settings which are not worth to be
 * stored in an external config file.
 */
class Config {

    /**
     * Times for the blocks.
     */
    const TIMES_FOR_BLOCKS = [
        [  0,  0, 59,  6 ],
        [  1,  0, 59,  6 ],
        [  2,  0, 59,  6 ],
        [  3,  0, 59,  6 ],
        [  4,  0, 59,  6 ],
        [  5,  0, 59,  6 ],
        [  6,  0, 59,  6 ],
        [  7,  0, 59,  6 ],
        [  8,  0, 59,  0 ],
        [  9,  0, 30,  0 ],
        [  9, 31, 44, -1 ],
        [  9, 45, 59,  1 ],
        [ 10,  0, 59,  1 ],
        [ 11,  0, 15,  1 ],
        [ 11, 16, 29, -2 ],
        [ 11, 30, 59,  2 ],
        [ 12,  0, 59,  2 ],
        [ 13,  0, 30,  2 ],
        [ 13, 31, 39, -3 ],
        [ 13, 40, 59,  3 ],
        [ 14,  0, 59,  3 ],
        [ 15,  0, 10,  3 ],
        [ 15, 11, 19, -4 ],
        [ 15, 20, 59,  4 ],
        [ 16,  0, 50,  4 ],
        [ 16, 51, 59, -5 ],
        [ 17,  0, 59,  5 ],
        [ 18,  0, 30,  5 ],
        [ 18,  31, 59, 6 ],
        [ 19,  0, 59,  6 ],
        [ 20,  0, 59,  6 ],
        [ 21,  0, 59,  6 ],
        [ 22,  0, 59,  6 ],
        [ 23,  0, 59,  6 ],
    ];

    /**
     * Semesters.
     */
    const  ALL_SEMESTERS =
        [ '1IB',
          '2IB',
          '3IB',
          '4IB',
          '6IB',
          '7IB',
          '1UIB',
          '2UIB',
          '3UIB',
          '4UIB',
          '6UIB',
          '7UIB',
          '1IMB',
          '2IMB',
          '3IMB',
          '4IMB',
          '6IMB',
          '7IMB',
          '1IM',
          '2IM',
        ];

    /**
     * Reload interval for timetable pages.
     */
    const RELOAD_TIMETABLE_PAGE_AFTER = 20000;

    /**
     * Reload interval for timetable pages.
     */
    const RELOAD_PICTURE_PAGE_AFTER = 5000;

    /**
     * Reload interval for timetable pages.
     */
    const RELOAD_PLAKAT_PAGE_AFTER = 15000;

    /**
     * Reload interval for timetable pages.
     */
    const RELOAD_ROOM_PAGE_AFTER = 15000;

    /**
     * TTL for timetable cache
     */
    const TTL_CACHE_TIMETABLE = 60 * 10; // 10 Minutes

    /**
     * TTL for room cache
     */
    const TTL_CACHE_ROOMS = 60 * 10; // 10 Minutes

    /**
     * Rooms.
     */
    const ALL_ROOMS =
        [
          'A005',
          'A008',
          'A010',
          'A012',
          'A107',
          'A108',
          'A111',
          'L303',
        ];

    /**
     * Times for the different blocks in the winter.
     */
    const TIMES_FOR_BLOCKS_WINTER =
        [
            '8:00-9:30 Uhr',
            '9:45-11:15 Uhr',
            '12:00-13:30 Uhr',
            '13:40-15:10 Uhr',
            '15:20-16:50 Uhr',
            '17:00-18:30 Uhr',
            'ab 18:30 Uhr',
    ];

    /**
     * Times for the different blocks in the summer.
     */
    const TIMES_FOR_BLOCKS_SUMMER =
        [
            '8:00-9:30 Uhr',
            '9:45-11:15 Uhr',
            '11:30-13:00 Uhr',
            '13:40-15:10 Uhr',
            '15:20-16:50 Uhr',
            '17:00-18:30 Uhr',
            'ab 18:30 Uhr',
        ];
}