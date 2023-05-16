-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Creato il: Mag 16, 2023 alle 16:37
-- Versione del server: 10.1.13-MariaDB
-- Versione PHP: 5.6.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `testregesta`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `fornitore`
--

CREATE TABLE `fornitore` (
  `id` int(11) NOT NULL,
  `nome` varchar(40) NOT NULL,
  `citta` varchar(40) NOT NULL,
  `indirizzo` varchar(40) NOT NULL,
  `giorniSpedizione` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `fornitore`
--

INSERT INTO `fornitore` (`id`, `nome`, `citta`, `indirizzo`, `giorniSpedizione`) VALUES
(1, 'Fornitore 1', 'Pontoglio', 'Via Aldo Moro 24', '5'),
(2, 'Fornitore 2', 'Brescia', 'Via Dante 10', '7'),
(3, 'Fornitore 3', 'Bergamo', 'Via Leopardi 1', '3');

-- --------------------------------------------------------

--
-- Struttura della tabella `prodotto`
--

CREATE TABLE `prodotto` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `prodotto`
--

INSERT INTO `prodotto` (`id`, `nome`) VALUES
(1, 'Philips monitor 17'),
(2, 'Tastiera Logitech'),
(3, 'PC HP'),
(4, 'Mouse Logitech'),
(5, 'Controller XBOX ONE');

-- --------------------------------------------------------

--
-- Struttura della tabella `sconto`
--

CREATE TABLE `sconto` (
  `id` int(11) NOT NULL,
  `tipo` enum('quantita','totale','data') NOT NULL,
  `quando` int(11) NOT NULL,
  `percentuale` int(11) NOT NULL,
  `idFornitore` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `sconto`
--

INSERT INTO `sconto` (`id`, `tipo`, `quando`, `percentuale`, `idFornitore`) VALUES
(1, 'totale', 1000, 5, 1),
(2, 'quantita', 5, 3, 2),
(3, 'quantita', 10, 5, 2),
(4, 'totale', 1000, 5, 3),
(5, 'data', 5, 2, 3);

-- --------------------------------------------------------

--
-- Struttura della tabella `stock`
--

CREATE TABLE `stock` (
  `idFornitore` int(11) NOT NULL,
  `idProdotto` int(11) NOT NULL,
  `quantita` int(11) NOT NULL,
  `prezzo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `stock`
--

INSERT INTO `stock` (`idFornitore`, `idProdotto`, `quantita`, `prezzo`) VALUES
(1, 1, 8, 120),
(1, 4, 0, 5),
(2, 1, 15, 128),
(2, 5, 3, 30),
(3, 1, 23, 129);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `fornitore`
--
ALTER TABLE `fornitore`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `prodotto`
--
ALTER TABLE `prodotto`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `sconto`
--
ALTER TABLE `sconto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idFornitore` (`idFornitore`);

--
-- Indici per le tabelle `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`idFornitore`,`idProdotto`),
  ADD KEY `idProdotto` (`idProdotto`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `fornitore`
--
ALTER TABLE `fornitore`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT per la tabella `prodotto`
--
ALTER TABLE `prodotto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT per la tabella `sconto`
--
ALTER TABLE `sconto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `sconto`
--
ALTER TABLE `sconto`
  ADD CONSTRAINT `sconto_ibfk_1` FOREIGN KEY (`idFornitore`) REFERENCES `fornitore` (`id`);

--
-- Limiti per la tabella `stock`
--
ALTER TABLE `stock`
  ADD CONSTRAINT `stock_ibfk_1` FOREIGN KEY (`idFornitore`) REFERENCES `fornitore` (`id`),
  ADD CONSTRAINT `stock_ibfk_2` FOREIGN KEY (`idProdotto`) REFERENCES `prodotto` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
