-- phpMyAdmin SQL Dump
-- version 4.5.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 02. Mrz 2016 um 14:08
-- Server-Version: 10.1.9-MariaDB
-- PHP-Version: 5.6.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `infodisplay`
--

--
-- Daten für Tabelle `program`
--

INSERT INTO `program` (`id`, `name`) VALUES
('EMLB', 'Elektro- und  Informationstechnik – Ingenieurpädagogik'),
('IB', 'Informatik Bachelor'),
('IM', 'Informatik Master'),
('IMB', 'Medizinische Informatik Bachelor'),
('MEB', 'Mechatronik Bachelor'),
('TSIT', 'Translation Studies in IT'),
('UIB', 'Unternehmens- und Wirtschaftsinformatik Bachelor'),
('WI', 'Wirtschaftsingenieurwesen Bachelor');

--
-- Daten für Tabelle `role`
--

INSERT INTO `role` (`id`, `rolename`) VALUES
(1, 'ROLE_ADMIN'),
(2, 'ROLE_WRITER'),
(3, 'ROLE_READER');

--
-- Daten für Tabelle `room`
--

INSERT INTO `room` (`id`, `name`, `description`, `capacity`, `usesblocks`, `link`) VALUES
(91, 'A005', 'A005 - Raskin', 0, 1, 'http://services.informatik.hs-mannheim.de/rooms/api/booking/A005'),
(92, 'A008', 'A008 - Dijkstra', 0, 1, 'http://services.informatik.hs-mannheim.de/rooms/api/booking/A008'),
(93, 'A010', 'A010 - Noether', 24, 1, 'http://services.informatik.hs-mannheim.de/rooms/api/booking/A010'),
(94, 'A012', 'A012 - Gray', 16, 1, 'http://services.informatik.hs-mannheim.de/rooms/api/booking/A012'),
(95, 'A105a', 'A105a - Jobs', 0, 0, 'http://services.informatik.hs-mannheim.de/rooms/api/booking/A105a'),
(96, 'A108', 'A108 - Nygaard', 0, 1, 'http://services.informatik.hs-mannheim.de/rooms/api/booking/A108'),
(97, 'A206', 'A206', 90, 1, 'http://services.informatik.hs-mannheim.de/rooms/api/booking/A206'),
(98, 'A208', 'A208', 30, 1, 'http://services.informatik.hs-mannheim.de/rooms/api/booking/A208'),
(99, 'A210', 'A210', 48, 1, 'http://services.informatik.hs-mannheim.de/rooms/api/booking/A210'),
(100, 'A211', 'A211', 48, 1, 'http://services.informatik.hs-mannheim.de/rooms/api/booking/A211'),
(101, 'A212', 'A212', 30, 1, 'http://services.informatik.hs-mannheim.de/rooms/api/booking/A212'),
(102, 'A212a', 'A212a - von Neumann', 0, 0, 'http://services.informatik.hs-mannheim.de/rooms/api/booking/A212a'),
(103, 'A305', 'A305', 126, 1, 'http://services.informatik.hs-mannheim.de/rooms/api/booking/A305'),
(104, 'A307', 'A307', 30, 1, 'http://services.informatik.hs-mannheim.de/rooms/api/booking/A307'),
(105, 'A309', 'A309', 72, 1, 'http://services.informatik.hs-mannheim.de/rooms/api/booking/A309'),
(106, 'A311', 'A311', 72, 1, 'http://services.informatik.hs-mannheim.de/rooms/api/booking/A311'),
(107, 'A409b', 'A409b', 6, 1, 'http://services.informatik.hs-mannheim.de/rooms/api/booking/A409b'),
(108, 'L303', 'L303 - Zuse', 0, 1, 'http://services.informatik.hs-mannheim.de/rooms/api/booking/L303');


--
-- Daten für Tabelle `user`
--

INSERT INTO `user` (`id`, `lecturer_id`, `username`, `password`, `key`) VALUES
(39, 17, 't.smits', '$2y$12$DCrTZ5bql6N/fWAw/itnQe9tnw8M43k01dYUCGHodsI3VaDYsv9uu', ''),
(43, 11, 'api_key', '', 'abbacap');

--
-- Daten für Tabelle `user_role`
--

INSERT INTO `user_role` (`user_id`, `role_id`) VALUES
(39, 3),
(43, 3);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
