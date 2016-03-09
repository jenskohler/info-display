-- phpMyAdmin SQL Dump
-- version 4.5.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 15. Feb 2016 um 17:54
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

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `booking`
--

CREATE TABLE `booking` (
  `id` int(11) NOT NULL,
  `room` varchar(10) COLLATE utf8_bin NOT NULL,
  `lecture` varchar(10) COLLATE utf8_bin NOT NULL,
  `description` varchar(255) COLLATE utf8_bin NOT NULL,
  `responsible` varchar(10) COLLATE utf8_bin NOT NULL,
  `responsible_long` varchar(255) COLLATE utf8_bin NOT NULL,
  `semester` varchar(10) COLLATE utf8_bin NOT NULL,
  `faculty` varchar(10) COLLATE utf8_bin NOT NULL,
  `block` int(11) NOT NULL,
  `dayofweek` int(11) NOT NULL,
  `date` date NOT NULL,
  `start` time NOT NULL,
  `end` time NOT NULL,
  `type` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `cancelled`
--

CREATE TABLE `cancelled` (
  `id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL,
  `posted` date NOT NULL,
  `valid` date NOT NULL,
  `timetable_key` varchar(100) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `lecture`
--

CREATE TABLE `lecture` (
  `shortname` varchar(10) COLLATE utf8_bin NOT NULL,
  `langname` varchar(255) COLLATE utf8_bin NOT NULL,
  `owner` varchar(10) COLLATE utf8_bin NOT NULL,
  `faculty` varchar(5) COLLATE utf8_bin NOT NULL,
  `exam` varchar(10) COLLATE utf8_bin NOT NULL,
  `area` varchar(5) COLLATE utf8_bin NOT NULL,
  `maxstudents` int(11) NOT NULL,
  `module` varchar(10) COLLATE utf8_bin NOT NULL,
  `moduleexam` varchar(10) COLLATE utf8_bin NOT NULL,
  `examplan` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `lecturer`
--

CREATE TABLE `lecturer` (
  `id` int(11) NOT NULL COMMENT 'Primärschlüssel von der Datenbank generiert',
  `shortname` char(5) COLLATE utf8_bin NOT NULL COMMENT 'Von der Verwaltung vergebenes Kürzel',
  `cis_id` int(11) DEFAULT NULL COMMENT 'Von der Verwaltung für Lehrbeauftragte vergebene ID der CIS-Datenbank',
  `surname` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'Nachname des Dozenten',
  `firstname` varchar(100) COLLATE utf8_bin NOT NULL COMMENT 'Vorname des Dozenten',
  `title` varchar(20) COLLATE utf8_bin NOT NULL COMMENT 'Akademischer Titel z.B. Prof. oder Prof. Dr.',
  `email` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'E-Mail-Adresse',
  `status` enum('LB','PROF','MA','IMP') COLLATE utf8_bin NOT NULL COMMENT 'Status: (LB) = Lehrbeauftragter, (PROF) = Professor, (MA) = Mitarbeiter, (IMP) = Import von anderer Fakultät',
  `achievements` tinyint(1) NOT NULL COMMENT 'Indikator, ob der Dozent auch Studienleistungen abnehmen wird'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `posted` datetime NOT NULL,
  `valid` datetime NOT NULL,
  `text` varchar(255) COLLATE utf8_bin NOT NULL,
  `semester` varchar(10) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `program`
--

CREATE TABLE `program` (
  `id` char(5) COLLATE utf8_bin NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `role`
--

CREATE TABLE `role` (
  `id` int(11) NOT NULL COMMENT 'Primärschlüssel',
  `rolename` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'Name der Rolle'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `room`
--

CREATE TABLE `room` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `description` varchar(255) COLLATE utf8_bin NOT NULL,
  `capacity` int(11) NOT NULL,
  `usesblocks` tinyint(1) NOT NULL,
  `link` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `timetable`
--

CREATE TABLE `timetable` (
  `id` int(11) NOT NULL,
  `semester` varchar(10) COLLATE utf8_bin NOT NULL,
  `dayofweek` int(11) NOT NULL,
  `block` int(11) NOT NULL,
  `lecture` varchar(10) COLLATE utf8_bin NOT NULL,
  `room` varchar(20) COLLATE utf8_bin NOT NULL,
  `lecturer` varchar(10) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL COMMENT 'Eindeutige ID des Benutzers',
  `lecturer_id` int(11) DEFAULT NULL COMMENT 'Verknüpfung mit der Tabelle der Lehrenden',
  `username` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'Benutzername (entspricht LDAP-User)',
  `password` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'Temporärer Eintrag, bis LDAP-Anbindung implementiert ist',
  `key` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'API key für den Zugriff ohne Benutzername und Passwort.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_role`
--

CREATE TABLE `user_role` (
  `user_id` int(11) NOT NULL COMMENT 'ID des Users, dem die Rolle zugewiesen wird',
  `role_id` int(11) NOT NULL COMMENT 'ID der zugewiesenen Rolle'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room` (`room`),
  ADD KEY `responsible` (`responsible`);

--
-- Indizes für die Tabelle `cancelled`
--
ALTER TABLE `cancelled`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `lecturer`
--
ALTER TABLE `lecturer`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `id_2` (`id`);

--
-- Indizes für die Tabelle `program`
--
ALTER TABLE `program`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`);

--
-- Indizes für die Tabelle `timetable`
--
ALTER TABLE `timetable`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indizes für die Tabelle `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`user_id`,`role_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `booking`
--
ALTER TABLE `booking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=206;
--
-- AUTO_INCREMENT für Tabelle `cancelled`
--
ALTER TABLE `cancelled`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT für Tabelle `lecturer`
--
ALTER TABLE `lecturer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primärschlüssel von der Datenbank generiert', AUTO_INCREMENT=261;
--
-- AUTO_INCREMENT für Tabelle `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
--
-- AUTO_INCREMENT für Tabelle `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primärschlüssel', AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT für Tabelle `room`
--
ALTER TABLE `room`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;
--
-- AUTO_INCREMENT für Tabelle `timetable`
--
ALTER TABLE `timetable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=277;
--
-- AUTO_INCREMENT für Tabelle `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Eindeutige ID des Benutzers', AUTO_INCREMENT=44;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
