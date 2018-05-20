-- phpMyAdmin SQL Dump
-- version 4.6.6deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Creato il: Set 13, 2017 alle 20:17
-- Versione del server: 5.7.17-1
-- Versione PHP: 7.0.16-3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `esamedb`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `annuncio`
--

CREATE TABLE `annuncio` (
  `idAnnuncio` int(11) NOT NULL,
  `titoloAnnuncio` varchar(45) NOT NULL,
  `descrizioneAnnuncio` varchar(250) NOT NULL,
  `idUtente` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `annuncio_has_utente`
--

CREATE TABLE `annuncio_has_utente` (
  `idAnnuncio` int(11) NOT NULL,
  `idUtente` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `caratteristicaComune`
--

CREATE TABLE `caratteristicaComune` (
  `idCarComune` int(11) NOT NULL,
  `nomeCarComune` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `carriera_has_competenza`
--

CREATE TABLE `carriera_has_competenza` (
  `idDatiCarriera` int(11) NOT NULL,
  `idCompetenza` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `carriera_has_lavoroPassato`
--

CREATE TABLE `carriera_has_lavoroPassato` (
  `idDatiCarriera` int(11) NOT NULL,
  `idLavoroPassato` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `carriera_has_skill`
--

CREATE TABLE `carriera_has_skill` (
  `idDatiCarriera` int(11) NOT NULL,
  `idSkill` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `carriera_has_titoloStudio`
--

CREATE TABLE `carriera_has_titoloStudio` (
  `idDatiCarriera` int(11) NOT NULL,
  `idTitoloStudio` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `competenza`
--

CREATE TABLE `competenza` (
  `idCompetenza` int(11) NOT NULL,
  `nomeCompetenza` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `datiAccount`
--

CREATE TABLE `datiAccount` (
  `idDatiAccount` int(11) NOT NULL,
  `email` varchar(45) NOT NULL,
  `password` varchar(20) NOT NULL,
  `tipoUtente` varchar(4) NOT NULL,
  `numCartaCredito` bigint(16) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `datiAnagrafici`
--

CREATE TABLE `datiAnagrafici` (
  `idDatiAnagrafici` int(11) NOT NULL,
  `nomeUtente` varchar(45) NOT NULL,
  `sesso` char(1) DEFAULT NULL,
  `dataNascita` date DEFAULT NULL,
  `luogoNascita` varchar(45) DEFAULT NULL,
  `luogoResidenza` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `datiCarriera`
--

CREATE TABLE `datiCarriera` (
  `idDatiCarriera` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `gruppo`
--

CREATE TABLE `gruppo` (
  `idGruppo` int(11) NOT NULL,
  `nomeGruppo` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `gruppo_has_carComune`
--

CREATE TABLE `gruppo_has_carComune` (
  `idGruppo` int(11) NOT NULL,
  `idCarComune` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `lavoroPassato`
--

CREATE TABLE `lavoroPassato` (
  `idLavoroPassato` int(11) NOT NULL,
  `nomeLavoroPassato` varchar(45) NOT NULL,
  `dataInizio` date DEFAULT NULL,
  `dataFine` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `richiestaConnessione`
--

CREATE TABLE `richiestaConnessione` (
  `idRichiesta` int(11) NOT NULL,
  `idUtenteRichiedente` int(11) NOT NULL,
  `commentoRichiesta` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `skill`
--

CREATE TABLE `skill` (
  `idSkill` int(11) NOT NULL,
  `nomeSkill` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `titoloStudio`
--

CREATE TABLE `titoloStudio` (
  `idTitoloStudio` int(11) NOT NULL,
  `nomeTitolo` varchar(45) NOT NULL,
  `valutazioneConseguita` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `utente`
--

CREATE TABLE `utente` (
  `idUtente` int(11) NOT NULL,
  `idDatiAnagrafici` int(11) NOT NULL,
  `idDatiCarriera` int(11) NOT NULL,
  `idDatiAccount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `utente_has_gruppo`
--

CREATE TABLE `utente_has_gruppo` (
  `idUtente` int(11) NOT NULL,
  `idGruppo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `utente_has_richiesta`
--

CREATE TABLE `utente_has_richiesta` (
  `idUtente` int(11) NOT NULL,
  `idRichiesta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `utente_has_utente`
--

CREATE TABLE `utente_has_utente` (
  `idUtente` int(11) NOT NULL,
  `idConnessione` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `utente_has_valutazione`
--

CREATE TABLE `utente_has_valutazione` (
  `idValutato` int(11) NOT NULL,
  `idValutazione` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `valutazione`
--

CREATE TABLE `valutazione` (
  `idValutazione` int(11) NOT NULL,
  `valutazione` varchar(250) NOT NULL,
  `idValutante` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `annuncio`
--
ALTER TABLE `annuncio`
  ADD PRIMARY KEY (`idAnnuncio`);

--
-- Indici per le tabelle `caratteristicaComune`
--
ALTER TABLE `caratteristicaComune`
  ADD PRIMARY KEY (`idCarComune`);

--
-- Indici per le tabelle `competenza`
--
ALTER TABLE `competenza`
  ADD PRIMARY KEY (`idCompetenza`);

--
-- Indici per le tabelle `datiAccount`
--
ALTER TABLE `datiAccount`
  ADD PRIMARY KEY (`idDatiAccount`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indici per le tabelle `datiAnagrafici`
--
ALTER TABLE `datiAnagrafici`
  ADD PRIMARY KEY (`idDatiAnagrafici`);

--
-- Indici per le tabelle `datiCarriera`
--
ALTER TABLE `datiCarriera`
  ADD PRIMARY KEY (`idDatiCarriera`);

--
-- Indici per le tabelle `gruppo`
--
ALTER TABLE `gruppo`
  ADD PRIMARY KEY (`idGruppo`);

--
-- Indici per le tabelle `lavoroPassato`
--
ALTER TABLE `lavoroPassato`
  ADD PRIMARY KEY (`idLavoroPassato`);

--
-- Indici per le tabelle `richiestaConnessione`
--
ALTER TABLE `richiestaConnessione`
  ADD PRIMARY KEY (`idRichiesta`);

--
-- Indici per le tabelle `skill`
--
ALTER TABLE `skill`
  ADD PRIMARY KEY (`idSkill`),
  ADD UNIQUE KEY `nomeSkill` (`nomeSkill`);

--
-- Indici per le tabelle `titoloStudio`
--
ALTER TABLE `titoloStudio`
  ADD PRIMARY KEY (`idTitoloStudio`);

--
-- Indici per le tabelle `utente`
--
ALTER TABLE `utente`
  ADD PRIMARY KEY (`idUtente`);

--
-- Indici per le tabelle `valutazione`
--
ALTER TABLE `valutazione`
  ADD PRIMARY KEY (`idValutazione`),
  ADD UNIQUE KEY `u_index` (`valutazione`,`idValutante`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `annuncio`
--
ALTER TABLE `annuncio`
  MODIFY `idAnnuncio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT per la tabella `caratteristicaComune`
--
ALTER TABLE `caratteristicaComune`
  MODIFY `idCarComune` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT per la tabella `competenza`
--
ALTER TABLE `competenza`
  MODIFY `idCompetenza` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT per la tabella `datiAccount`
--
ALTER TABLE `datiAccount`
  MODIFY `idDatiAccount` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT per la tabella `datiAnagrafici`
--
ALTER TABLE `datiAnagrafici`
  MODIFY `idDatiAnagrafici` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT per la tabella `datiCarriera`
--
ALTER TABLE `datiCarriera`
  MODIFY `idDatiCarriera` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT per la tabella `gruppo`
--
ALTER TABLE `gruppo`
  MODIFY `idGruppo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT per la tabella `lavoroPassato`
--
ALTER TABLE `lavoroPassato`
  MODIFY `idLavoroPassato` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT per la tabella `richiestaConnessione`
--
ALTER TABLE `richiestaConnessione`
  MODIFY `idRichiesta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT per la tabella `skill`
--
ALTER TABLE `skill`
  MODIFY `idSkill` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT per la tabella `titoloStudio`
--
ALTER TABLE `titoloStudio`
  MODIFY `idTitoloStudio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT per la tabella `utente`
--
ALTER TABLE `utente`
  MODIFY `idUtente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT per la tabella `valutazione`
--
ALTER TABLE `valutazione`
  MODIFY `idValutazione` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
