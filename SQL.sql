-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Czas wygenerowania: 18 Gru 2013, 10:43
-- Wersja serwera: 5.5.24-log
-- Wersja PHP: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Baza danych: `przychodnia`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `lekarze`
--

CREATE TABLE IF NOT EXISTS `lekarze` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `os_id` int(11) NOT NULL,
  `spec_id` int(11) NOT NULL,
  `tytul_naukowy` varchar(15) DEFAULT NULL,
  `grafik` varchar(500) DEFAULT NULL,
  `minut_na_pacjenta` int(4) DEFAULT '30',
  PRIMARY KEY (`id`),
  KEY `fk_lekarze_spec` (`spec_id`),
  KEY `fk_lekarze_os` (`os_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Zrzut danych tabeli `lekarze`
--

INSERT INTO `lekarze` (`id`, `os_id`, `spec_id`, `tytul_naukowy`, `grafik`, `minut_na_pacjenta`) VALUES
(2, 5, 100, 'mgr', '8:00-16:00%8:00-16:00%8:00-16:00%8:00-16:00%8:00-16:00%%', 20),
(3, 6, 83, 'prof. dr hab.', '%14:00-17:30%%%%%', 60),
(6, 3, 70, 'prof.', '8:00-12:00%%8:00-10:00%8:00-10:00%%%', 35),
(7, 6, 76, 'prof.', '%%%%%%', 30),
(9, 8, 71, 'prof.', '%%7:00-15:00%%%%', 40),
(10, 9, 100, 'mgr', '8:00-18:00%13:00-18:00%13:00-18:00%13:00-18:00%13:00-18:00%%', 20);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `osoby`
--

CREATE TABLE IF NOT EXISTS `osoby` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `imie` varchar(20) NOT NULL,
  `nazwisko` varchar(40) NOT NULL,
  `pesel` char(11) NOT NULL,
  `adres` varchar(200) NOT NULL,
  `telefon` varchar(11) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `data_ur` date NOT NULL,
  `plec` char(1) NOT NULL,
  `poziom` tinyint(1) NOT NULL DEFAULT '0',
  `haslo` varchar(60) NOT NULL,
  `sol` varchar(16) NOT NULL,
  `aktywny` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `pesel` (`pesel`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Zrzut danych tabeli `osoby`
--

INSERT INTO `osoby` (`id`, `imie`, `nazwisko`, `pesel`, `adres`, `telefon`, `email`, `data_ur`, `plec`, `poziom`, `haslo`, `sol`, `aktywny`) VALUES
(1, 'Anonim', 'Noname', '00000000000', 'nie dotyczy', NULL, 'no-reply@przychodnia.url.ph', '1960-01-01', 'M', 0, '$2y$06$54fsd4654df8ewr54wr864f6sdf841ds154f648444646611055df', 'f8ao3G@L3gKeE0t6', 1),
(2, 'Jan', 'Kowalski', '99999999999', '99-999 Warszawa, ul. Inna 215', '48123456789', 'siankotm+jk@gmail.com', '1995-02-26', 'M', 0, '$2y$06$ZjhhbzNHQEwzZ0tlRTB0NebSkSatM/2AeppP5kBYV0v/zl8DVkMdO', 'f8ao3G@L3gKeE0t6', 1),
(3, 'Agata', 'Pogodna', '11111111111', '99-999 Warszawa, ul. Wysoka 23', '', 'siankotm+ap@gmail.com', '1995-02-26', 'K', 1, '$2y$06$ZjhhbzNHQEwzZ0tlRTB0NebSkSatM/2AeppP5kBYV0v/zl8DVkMdO', 'f8ao3G@L3gKeE0t6', 1),
(4, 'Grzegorz', 'Szamburski', '22222222222', '99-999 Miasto, ul. Nowa 1', NULL, 'siankotm@gmail.com', '1992-02-01', 'M', 2, '$2y$06$ZjhhbzNHQEwzZ0tlRTB0NebSkSatM/2AeppP5kBYV0v/zl8DVkMdO', 'f8ao3G@L3gKeE0t6', 1),
(5, 'Julia', 'Nowakowska', '33333333333', '99-999 Warszawa, ul. Wysoka 23', '', 'siankotm+jn@gmail.com', '1984-08-15', 'K', 1, '$2y$06$OXQjYllabj9iZEM/IUhpde8Z4IwB.YQwyRNMfmkf9v6BHvosPJgh6', '9t#bYZn?bdC?!Hiv', 1),
(6, 'Tomasz', 'Okulski', '55555555555', '99-999 Warszawa, ul. Wysoka 1', '', 'siankotm+to@gmail.com', '1953-12-01', 'M', 1, '$2y$06$TzlGVHBAdlpARkhyJFlASOfRjqkXlZQDTXEeer9K057nxZKkM.gyq', 'O9FTp@vZ@FHr$Y@I', 1),
(7, 'Julia', 'Adamska', '44444444444', '99-999 Warszawa, ul. Wysoka 23', '', 'siankotm+jn@gmail.com', '1984-08-15', 'K', 0, '$2y$06$OXQjYllabj9iZEM/IUhpde8Z4IwB.YQwyRNMfmkf9v6BHvosPJgh6', '9t#bYZn?bdC?!Hiv', 1),
(8, 'Karolina', 'Mocna', '45120874614', '99-999 Łódź, ul. Wysoka 23', '', 'siankotm+km@gmail.com', '1945-12-08', 'K', 0, '$2y$06$aEBvVDlZaFZFSW13MzlGZud1v6S91Mi8P63.bwRfsb8UAsJ/3M2fO', 'h@oT9YhVEImw39Fg', 1),
(9, 'Marta', 'Adamska', '85070512345', '99-999 Łódź, ul. Inna 2', '', 'siankotm+ma@gmail.com', '1985-07-05', 'K', 1, '$2y$06$I2RweDdGQHpzdER2R2dSVuxsGvrixtqCM1BT6PovRzyii699G/yOy', '#dpx7F@zstDvGgRW', 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `specjalnosci`
--

CREATE TABLE IF NOT EXISTS `specjalnosci` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazwa` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nazwa` (`nazwa`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=101 ;

--
-- Zrzut danych tabeli `specjalnosci`
--

INSERT INTO `specjalnosci` (`id`, `nazwa`) VALUES
(66, 'Alergologia'),
(67, 'Anestezjologia'),
(68, 'Balneologia'),
(69, 'Bariatria'),
(70, 'Chirurgia'),
(100, 'Choroby wew. (internista)'),
(71, 'Dermatologia'),
(72, 'Epidemiologia'),
(74, 'Genetyka kliniczna'),
(75, 'Geriatria'),
(76, 'Ginekologia'),
(77, 'Immunologia'),
(73, 'Medycyna estetyczna'),
(78, 'Medycyna paliatywna'),
(79, 'Medycyna pracy'),
(80, 'Medycyna sądowa'),
(81, 'Medycyna wojskowa'),
(82, 'Neurologia'),
(83, 'Okulistyka'),
(84, 'Onkologia'),
(85, 'Ortopedia'),
(86, 'Otorynolaryngologia'),
(87, 'Patologia'),
(88, 'Pediatria'),
(89, 'Położnictwo'),
(90, 'Psychiatria'),
(91, 'Radiologia'),
(92, 'Rehabilitacja'),
(93, 'Seksuologia'),
(94, 'Stomatologia'),
(95, 'Telerehabilitacja'),
(96, 'Toksykologia'),
(97, 'Transfuzjologia'),
(98, 'Urologia'),
(99, 'Wenerologia');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `wizyty`
--

CREATE TABLE IF NOT EXISTS `wizyty` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lekarz_id` int(11) NOT NULL,
  `pacjent_id` int(11) NOT NULL,
  `data` datetime NOT NULL,
  `data_koniec` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_wizyty_pacjent` (`pacjent_id`),
  KEY `fk_wizyty_lekarz` (`lekarz_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=79 ;

--
-- Zrzut danych tabeli `wizyty`
--

INSERT INTO `wizyty` (`id`, `lekarz_id`, `pacjent_id`, `data`, `data_koniec`, `status`) VALUES
(61, 6, 2, '2013-12-14 23:00:00', '2013-12-14 23:35:00', 0),
(62, 6, 2, '2013-12-14 12:00:00', '2013-12-14 12:35:00', 0),
(65, 6, 2, '2013-12-21 12:00:00', '2013-12-21 12:35:00', 0),
(66, 6, 7, '2013-12-19 08:00:00', '2013-12-19 08:35:00', 1),
(69, 3, 1, '2013-12-16 08:44:00', '2013-12-16 23:59:59', 0),
(70, 3, 1, '2013-12-17 00:00:00', '2013-12-17 23:44:00', 0),
(71, 6, 1, '2013-12-16 07:44:00', '2013-12-16 23:59:59', 0),
(72, 6, 1, '2013-12-17 00:00:00', '2013-12-17 23:44:00', 0),
(73, 3, 2, '2013-12-24 15:00:00', '2013-12-24 16:00:00', 1),
(74, 3, 2, '2013-12-24 17:00:00', '2013-12-24 18:00:00', 0),
(75, 3, 2, '2013-12-24 14:00:00', '2013-12-24 15:00:00', 0),
(76, 3, 2, '2013-12-24 16:00:00', '2013-12-24 17:00:00', 0),
(77, 2, 7, '2013-12-25 08:40:00', '2013-12-25 09:00:00', 0),
(78, 2, 2, '2013-12-17 12:00:00', '2013-12-17 12:20:00', 0);

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `lekarze`
--
ALTER TABLE `lekarze`
  ADD CONSTRAINT `fk_lekarze_os` FOREIGN KEY (`os_id`) REFERENCES `osoby` (`id`),
  ADD CONSTRAINT `fk_lekarze_spec` FOREIGN KEY (`spec_id`) REFERENCES `specjalnosci` (`id`);

--
-- Ograniczenia dla tabeli `wizyty`
--
ALTER TABLE `wizyty`
  ADD CONSTRAINT `fk_wizyty_lekarz` FOREIGN KEY (`lekarz_id`) REFERENCES `lekarze` (`id`),
  ADD CONSTRAINT `fk_wizyty_pacjent` FOREIGN KEY (`pacjent_id`) REFERENCES `osoby` (`id`);
