-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Czas wygenerowania: 28 Lis 2013, 22:04
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
  `tytul_naukowy` int(11) DEFAULT NULL,
  `grafik` varchar(500) DEFAULT NULL,
  `minut_na_pacjenta` int(4) DEFAULT '30',
  PRIMARY KEY (`id`),
  KEY `fk_lekarze_spec` (`spec_id`),
  KEY `fk_lekarze_os` (`os_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
  PRIMARY KEY (`id`),
  UNIQUE KEY `pesel` (`pesel`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Zrzut danych tabeli `osoby`
--

INSERT INTO `osoby` (`id`, `imie`, `nazwisko`, `pesel`, `adres`, `telefon`, `email`, `data_ur`, `plec`, `poziom`, `haslo`, `sol`) VALUES
(1, 'Grzegorz', 'Szamburski', '00000000000', '99-999 Miasto, ul. Nowa 1', NULL, 'siankotm@gmail.com', '1992-02-01', 'M', 2, '$2y$06$ZjhhbzNHQEwzZ0tlRTB0NebSkSatM/2AeppP5kBYV0v/zl8DVkMdO', 'f8ao3G@L3gKeE0t6'),
(2, 'Jan', 'Kowalski', '99999999999', '99-999 Warszawa, ul. Inna 215', '48123456789', 'siankotm@gmail.com', '1995-02-26', 'M', 0, '$2y$06$ZjhhbzNHQEwzZ0tlRTB0NebSkSatM/2AeppP5kBYV0v/zl8DVkMdO', 'f8ao3G@L3gKeE0t6');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `specjalnosci`
--

CREATE TABLE IF NOT EXISTS `specjalnosci` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazwa` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nazwa` (`nazwa`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=100 ;

--
-- Zrzut danych tabeli `specjalnosci`
--

INSERT INTO `specjalnosci` (`id`, `nazwa`) VALUES
(66, 'Alergologia'),
(67, 'Anestezjologia'),
(68, 'Balneologia'),
(69, 'Bariatria'),
(70, 'Chirurgia'),
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
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_wizyty_pacjent` (`pacjent_id`),
  KEY `fk_wizyty_lekarz` (`lekarz_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
