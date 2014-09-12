-- phpMyAdmin SQL Dump
-- version 3.3.4-rc1
-- http://www.phpmyadmin.net
--
-- Host: mysql.dev.sendlove.us
-- Generation Time: Sep 15, 2010 at 08:31 PM
-- Server version: 5.0.45
-- PHP Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `LM_logintest`
--

-- --------------------------------------------------------

--
-- Table structure for table `login_users`
--

CREATE TABLE IF NOT EXISTS `login_users` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `confirmed` tinyint(1) NOT NULL,
  `active` tinyint(4) default '1',
  `token` varchar(50) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  `nickname` varchar(255) NOT NULL,
  `admin` int(1) NOT NULL default '0',
  `removed` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `login_users`
--

INSERT INTO `login_users` (`id`, `username`, `password`, `confirmed`, `active`, `token`, `date_added`, `date_modified`, `nickname`, `admin`, `removed`) VALUES
(2, 'existingUser@domain.com', '{crypt}$1$A_C6u+tR$CPDUhJlbW6.u3KcuosGK81', 1, 1, '4c915a7f7e5bb', '2010-09-15 17:38:53', '0000-00-00 00:00:00', 'existingUser', 0, 0),
(3, 'notConfirmedUser@domain.com', '{crypt}$1$A_C6u+tR$CPDUhJlbW6.u3KcuosGK81', 0, 1, '2fbe21ab2b5febeae6e2342f5f51cb5e', '2010-09-15 17:38:53', '0000-00-00 00:00:00', 'notConfirmedUser', 0, 0),
(4, 'notActiveUser@domain.com', '{crypt}$1$A_C6u+tR$CPDUhJlbW6.u3KcuosGK81', 1, 0, '2fbe21ab2b5febeae6e2342f5f51cb5e', '2010-09-15 17:38:53', '0000-00-00 00:00:00', 'notActiveUser', 0, 0),
(5, 'RemovedUser@domain.com', '{crypt}$1$A_C6u+tR$CPDUhJlbW6.u3KcuosGK81', 1, 1, '2fbe21ab2b5febeae6e2342f5f51cb5e', '2010-09-15 17:38:53', '0000-00-00 00:00:00', 'RemovedUser', 0, 1),
(6, 'adminUser@domain.com', '{crypt}$1$A_C6u+tR$CPDUhJlbW6.u3KcuosGK81', 1, 1, '2fbe21ab2b5febeae6e2342f5f51cb5e', '2010-09-15 17:38:53', '0000-00-00 00:00:00', 'adminUser', 1, 0),
(9, 'testingUser@domain.com', '{crypt}$1${f:kj91|$t0q6rOil.Nrne7MxICQTL.', 1, 1, '4c915117ece77', '2010-09-15 17:38:53', '0000-00-00 00:00:00', 'testingUser', 0, 0),
(7, 'adminUserRemoved@domain.com', '{crypt}$1$A_C6u+tR$CPDUhJlbW6.u3KcuosGK81', 1, 1, '2fbe21ab2b5febeae6e2342f5f51cb5e', '2010-09-15 17:38:53', '0000-00-00 00:00:00', 'adminUser', 1, 1),
(8, 'adminUserDeactivated@domain.com', '{crypt}$1$A_C6u+tR$CPDUhJlbW6.u3KcuosGK81', 1, 0, '2fbe21ab2b5febeae6e2342f5f51cb5e', '2010-09-15 17:38:53', '0000-00-00 00:00:00', 'adminUser', 1, 0);
