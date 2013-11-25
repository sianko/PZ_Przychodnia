-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Czas wygenerowania: 21 Lis 2013, 11:11
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
  `grafik` varchar(500) COLLATE utf8_polish_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_lekarze_spec` (`spec_id`),
  KEY `fk_lekarze_os` (`os_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `osoby`
--

CREATE TABLE IF NOT EXISTS `osoby` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `imie` varchar(20) COLLATE utf8_polish_ci NOT NULL,
  `nazwisko` varchar(40) COLLATE utf8_polish_ci NOT NULL,
  `pesel` char(11) COLLATE utf8_polish_ci NOT NULL,
	`haslo` char(60) COLLATE utf8_polish_ci NOT NULL,
	`sol` varchar(16) COLLATE utf8_polish_ci NOT NULL,
  `adres` varchar(200) COLLATE utf8_polish_ci NOT NULL,
  `telefon` varchar(11) COLLATE utf8_polish_ci DEFAULT NULL,
  `email` varchar(50) COLLATE utf8_polish_ci NOT NULL,
  `data_ur` date NOT NULL,
  `plec` char(1) COLLATE utf8_polish_ci NOT NULL,
  `poziom` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `pesel` (`pesel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `specjalnosci`
--

CREATE TABLE IF NOT EXISTS `specjalnosci` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazwa` varchar(30) COLLATE utf8_polish_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nazwa` (`nazwa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;

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
