-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Czas wygenerowania: 21 Gru 2012, 18:54
-- Wersja serwera: 5.5.27
-- Wersja PHP: 5.4.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Baza danych: `livesportcms`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `lsc_articles`
--

CREATE TABLE IF NOT EXISTS `lsc_articles` (
  `Article_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `User_ID` int(10) unsigned NOT NULL,
  `Subcategory_ID` int(10) unsigned DEFAULT NULL,
  `Commentary_ID` int(10) unsigned DEFAULT NULL,
  `Live_ID` int(10) unsigned DEFAULT NULL,
  `Article_Title` varchar(48) NOT NULL,
  `Article_Creation_Date` datetime NOT NULL,
  `Article_Brief` varchar(512) NOT NULL,
  `Article_Text` text NOT NULL,
  `Article_Image` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`Article_ID`),
  KEY `User_ID` (`User_ID`),
  KEY `Commentary_ID` (`Commentary_ID`),
  KEY `Live_ID` (`Live_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Zrzut danych tabeli `lsc_articles`
--

INSERT INTO `lsc_articles` (`Article_ID`, `User_ID`, `Subcategory_ID`, `Commentary_ID`, `Live_ID`, `Article_Title`, `Article_Creation_Date`, `Article_Brief`, `Article_Text`, `Article_Image`) VALUES
(9, 1, 1, 0, 0, 'Lewandowski do MU', '2012-12-08 15:16:06', 'Robert Lewandowski od lipca przyszłego roku zasiądzie na ławce Czerwonych Diabłów. $100 000.', 'Robert Lewandowski od lipca przyszłego roku zasiądzie na ławce Czerwonych Diabłów. $100 000.\r\nCały Artykuł.', ''),
(10, 1, 2, 0, 0, 'Błaszczykowski do Lecha', '2012-12-08 15:45:11', 'Jakub Błaszczykowski według serwisu Bild miałby grać dla Lecha Poznań od przyszłej Jesieni. Potwierdzona informacja. 100000zł', 'Jakub Błaszczykowski według serwisu Bild miałby grać dla Lecha Poznań od przyszłej Jesieni. Potwierdzona informacja. 100000zł\r\nCały Artykuł.', ''),
(11, 1, 2, 0, 0, 'Goal.com ocenia piłkarzy Borussii.', '2012-12-20 12:00:17', 'Serwis goal.com ocenił występ piłkarzy w środowym meczu Pucharu Niemiec, w którym Borussia Dortmund pokonała Hannover 5:1. Na najwyższą notę zasłużył strzelec trzech bramek Mario Goetze. Występy Łukasza Piszczka, Kuby Błaszczykowskiego i Roberta Lewandowskiego wycenione zostały na cztery w pięciogwiazdkowej skali.', 'Serwis goal.com ocenił występ piłkarzy w środowym meczu Pucharu Niemiec, w którym Borussia Dortmund pokonała Hannover 5:1. Na najwyższą notę zasłużył strzelec trzech bramek Mario Goetze. Występy Łukasza Piszczka, Kuby Błaszczykowskiego i Roberta Lewandowskiego wycenione zostały na cztery w pięciogwiazdkowej skali.\r\nTrzej Polacy pojawili się na boisku od pierwszej minuty spotkania. Błaszczykowski i Lewandowski po razie pokonali bramkarza rywali. Trzy bramki dodał Goetze.\r\n\r\n\r\n\r\n"Biegał do przodu, gdy tylko było to możliwe, a także sprawiał, że lewoskrzydłowy Hannoveru Christian Pander miał mały wkład w akcje ofensywne swojego zespołu. Imponująca postawa" - uzasadnił serwis notę dla Piszczka. Pochwały należały się także Błaszczykowskiemu, który po raz kolejny zagrał na lewej obronie. "Przejawiał niewyobrażalną ilość energii do dręczenia rywala od początku do końca. Rewelacyjny z piłką przy nodze" - zachwalał kapitana reprezentacji Polski serwis.', '');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `lsc_categories`
--

CREATE TABLE IF NOT EXISTS `lsc_categories` (
  `Categorie_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Categorie_Name` varchar(15) NOT NULL,
  `Categorie_Live_Template` varchar(64) NOT NULL,
  PRIMARY KEY (`Categorie_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Zrzut danych tabeli `lsc_categories`
--

INSERT INTO `lsc_categories` (`Categorie_ID`, `Categorie_Name`, `Categorie_Live_Template`) VALUES
(1, 'Football', 'Football'),
(2, 'Piłka Nożna', 'Football');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `lsc_commentaries`
--

CREATE TABLE IF NOT EXISTS `lsc_commentaries` (
  `Commentary_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`Commentary_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `lsc_commentary_messages`
--

CREATE TABLE IF NOT EXISTS `lsc_commentary_messages` (
  `Message_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Commentary_ID` int(10) unsigned NOT NULL,
  `User_ID` int(10) unsigned NOT NULL,
  `Message_Text` varchar(60) NOT NULL,
  `Message_Post_Date` date NOT NULL,
  PRIMARY KEY (`Message_ID`),
  KEY `Commentary_ID` (`Commentary_ID`),
  KEY `User_ID` (`User_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `lsc_live_commentary`
--

CREATE TABLE IF NOT EXISTS `lsc_live_commentary` (
  `Live_Commentary_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `User_ID` int(10) unsigned NOT NULL,
  `Score_ID` int(10) unsigned NOT NULL,
  `Live_Commentary_Live` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`Live_Commentary_ID`),
  KEY `User_ID` (`User_ID`),
  KEY `Score_ID` (`Score_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `lsc_live_messages`
--

CREATE TABLE IF NOT EXISTS `lsc_live_messages` (
  `Live_Message_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Live_Commentary_ID` int(10) unsigned NOT NULL,
  `Live_Message_Update` datetime NOT NULL,
  `Live_Message_Title` varchar(64) NOT NULL DEFAULT '""',
  `Live_Message_Text` text NOT NULL,
  `Live_Message_Order` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`Live_Message_ID`),
  KEY `Live_Stream_ID` (`Live_Commentary_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `lsc_live_streams`
--

CREATE TABLE IF NOT EXISTS `lsc_live_streams` (
  `Live_Stream_ID` int(10) unsigned NOT NULL,
  `User_ID` int(10) unsigned NOT NULL,
  `Score_ID` int(10) unsigned NOT NULL,
  `Commentary_ID` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`Live_Stream_ID`),
  KEY `User_ID` (`User_ID`),
  KEY `Score_ID` (`Score_ID`),
  KEY `Commentary_ID` (`Commentary_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `lsc_modules`
--

CREATE TABLE IF NOT EXISTS `lsc_modules` (
  `Modules_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Modules_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `Modules_class` varchar(64) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `Modules_panel` int(1) NOT NULL DEFAULT '0',
  `Modules_hierarchy` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Modules_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Zrzut danych tabeli `lsc_modules`
--

INSERT INTO `lsc_modules` (`Modules_ID`, `Modules_name`, `Modules_class`, `Modules_panel`, `Modules_hierarchy`) VALUES
(1, 'Modul 1', 'Modul1', 1, 1),
(2, 'Modul 2', 'Modul2', 2, 1),
(3, 'Modul 3', 'Modul3', 1, 3),
(4, 'Modul 4', 'Modul4', 1, 2),
(5, 'Modul 5', 'Modul5', 0, 0),
(8, 'Testowy Moduł', 'Modul4', 2, 2),
(9, 'Mój Moduł', 'Modul4', 0, 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `lsc_scores`
--

CREATE TABLE IF NOT EXISTS `lsc_scores` (
  `Score_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Subcategory_ID` int(10) unsigned NOT NULL,
  `Score_Home_Name` varchar(128) NOT NULL,
  `Score_Away_Name` varchar(128) NOT NULL,
  `Score_Home_Score` mediumint(9) NOT NULL DEFAULT '0',
  `Score_Away_Score` mediumint(9) NOT NULL DEFAULT '0',
  `Score_Event_Datetime` datetime NOT NULL,
  PRIMARY KEY (`Score_ID`),
  KEY `Category_ID` (`Subcategory_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Zrzut danych tabeli `lsc_scores`
--

INSERT INTO `lsc_scores` (`Score_ID`, `Subcategory_ID`, `Score_Home_Name`, `Score_Away_Name`, `Score_Home_Score`, `Score_Away_Score`, `Score_Event_Datetime`) VALUES
(1, 1, 'Polonia Warszawa', 'PogoĹ„ Szczecin', 2, 0, '2012-12-10 18:30:00'),
(2, 1, 'Korona Kielce', 'Lech PoznaĹ„', 0, 1, '2012-12-09 17:00:00'),
(3, 1, 'Podbeskidzie Bielsko-BiaĹ‚a', 'Widzew Ĺ�ĂłdĹş', 2, 2, '2012-12-09 14:30:00'),
(4, 1, 'ZagĹ‚Ä™bie Lublin', 'WisĹ‚a KrakĂłw', 4, 1, '2012-12-08 15:45:00'),
(5, 1, 'GĂłrnik Zabrze', 'Lechia GdaĹ„sk', 2, 0, '2012-12-08 18:00:00');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

--
-- Struktura tabeli dla tabeli `lsc_session_store`
--

CREATE TABLE IF NOT EXISTS `lsc_session_store` (
  `Session_Id` varchar(32) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `User_Id` int(10) unsigned NOT NULL,
  `Session_Expire` int(20) unsigned NOT NULL,
  `Session_Lastaction` int(20) unsigned NOT NULL,
  `Session_Ip` varchar(45) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL DEFAULT '0.0.0.0',
  `Session_Actioncount` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`Session_Id`),
  KEY `User_Id` (`User_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `lsc_subcategories`
--

CREATE TABLE IF NOT EXISTS `lsc_subcategories` (
  `Subcategory_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Categorie_ID` int(10) NOT NULL,
  `Subcategory_Name` varchar(128) NOT NULL,
  PRIMARY KEY (`Subcategory_ID`),
  KEY `Categorie_ID` (`Categorie_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Zrzut danych tabeli `lsc_subcategories`
--

INSERT INTO `lsc_subcategories` (`Subcategory_ID`, `Categorie_ID`, `Subcategory_Name`) VALUES
(1, 2, 'Puchar Anglii'),
(2, 2, 'Liga Polska'),
(3, 2, 'Liga Angielska');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `lsc_subscores`
--

CREATE TABLE IF NOT EXISTS `lsc_subscores` (
  `Subscore_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Score_ID` int(10) unsigned NOT NULL,
  `Subscore_Home_Subscore` smallint(5) unsigned NOT NULL DEFAULT '0',
  `Subscore_Away_Subscore` smallint(5) unsigned NOT NULL DEFAULT '0',
  `Subscore_Event_Order` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`Subscore_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `lsc_users`
--

CREATE TABLE IF NOT EXISTS `lsc_users` (
  `User_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `User_Login` varchar(32) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL DEFAULT '',
  `User_Salt` varchar(32) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `User_Password` varchar(128) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `User_Name` varchar(64) CHARACTER SET utf8 COLLATE utf8_polish_ci DEFAULT '',
  `User_Surname` varchar(64) CHARACTER SET utf8 COLLATE utf8_polish_ci DEFAULT '',
  `User_Email` varchar(100) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL DEFAULT '',
  `User_Level` tinyint(3) NOT NULL DEFAULT '0',
  `User_Status` tinyint(1) NOT NULL DEFAULT '0',
  `User_Avatar` varchar(100) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL DEFAULT '',
  `User_Lastvisit` datetime NOT NULL,
  `User_Registered` datetime NOT NULL,
  PRIMARY KEY (`User_Id`),
  UNIQUE KEY `User_Login` (`User_Login`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Zrzut danych tabeli `lsc_users`
--

INSERT INTO `lsc_users` (`User_Id`, `User_Login`, `User_Salt`, `User_Password`, `User_Name`, `User_Surname`, `User_Email`, `User_Level`, `User_Status`, `User_Avatar`, `User_Lastvisit`, `User_Registered`) VALUES
(1, 'Test1', '$2a$10$QsWxzuMZfGXgxpJjBHRIRNB', '$2a$10$QsWxzuMZfGXgxpJjBHRIR.hGdmNFmtBmkOQ.0ttdv8eduK5chIH/q', 'Jan', 'Kowalski', 'user1@user1.com', 3, 1, 'images/avatars/default_user.png', '2012-12-21 18:01:32', '2012-11-28 15:28:03'),
(2, 'Test2', '$2a$10$81X0lz1BAUhKSJfsBMJnjlg', '$2a$10$81X0lz1BAUhKSJfsBMJnjeH.C9Mnb8uh52fryTXXOWx4hGgH.zxSi', '', '', 'user2@user2.com', 2, 1, 'images/avatars/default_user.png', '2012-11-28 15:29:07', '2012-11-28 15:29:07'),
(3, 'Test3', '$2a$10$FD6y9w5wUlyzTH0uf1jhr06', '$2a$10$FD6y9w5wUlyzTH0uf1jhruEwAagi0X1./c3EKZXu6gqeiu.pKkz9y', '', '', 'user3@user3.com', 1, 1, 'images/avatars/default_user.png', '2012-11-28 15:29:26', '2012-11-28 15:29:26'),
(4, 'rufus92', '$2a$10$RDRcCn35vALMWGqfDauW5A5', '$2a$10$RDRcCn35vALMWGqfDauW5.XkRDFqFC2RgKIwnRhK7o5XwJRRktRKy', '', '', 'r4@gmail.com', 1, 1, 'images/avatars/default_user.png', '2012-12-08 16:16:39', '2012-12-08 16:16:39'),
(5, 'r21', '$2a$10$f7MBBFasFhaNxvxfjdI7Tk4', '$2a$10$f7MBBFasFhaNxvxfjdI7Tel3x9Lt/L5Q1CN22zNq51aBeqCe7LFSW', '', '', 'r21@gmail.com', 1, 1, 'images/avatars/default_user.png', '2012-12-08 16:18:01', '2012-12-08 16:18:01'),
(6, 'Rufus90', '$2a$10$ZYMs8PgQDhUszk9X35qHB9g', '$2a$10$ZYMs8PgQDhUszk9X35qHBuuz8IReXYxFHyELhRMOdqi78XJbjCXAK', 'Andrzej', 'Rufus', 'Rufus90@gmail.com', 1, 1, 'images/avatars/default_user.png', '2012-12-08 16:18:51', '2012-12-08 16:18:51');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `lsc_settings`
--

CREATE TABLE IF NOT EXISTS `lsc_settings` (
  `Setting_Name` varchar(128) NOT NULL,
  `Setting_Value` varchar(256) NOT NULL,
  PRIMARY KEY (`Setting_Name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `lsc_settings`
--

INSERT INTO `lsc_settings` (`Setting_Name`, `Setting_Value`) VALUES
('hash_algo', 'Blowfish'),
('hash_str', '10'),
('last_activity_limit', '900'),
('live_messages_limit', '10'),
('page_title', 'LiveSportCMS'),
('script_host', 'localhost'),
('script_path', '/~boruc-san/ProjektSI/'),
('session_expire', '43200'),
('theme', 'default');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;