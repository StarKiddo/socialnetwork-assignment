-- --------------------------------------------------------
-- Hostitel:                     127.0.0.1
-- Verze serveru:                10.1.26-MariaDB - mariadb.org binary distribution
-- OS serveru:                   Win32
-- HeidiSQL Verze:               9.4.0.5125
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Exportování struktury pro tabulka social-network.friends
CREATE TABLE IF NOT EXISTS `friends` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idUser1` int(11) NOT NULL DEFAULT '0',
  `idUser2` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin2 COLLATE=latin2_czech_cs;

-- Export dat nebyl vybrán.
-- Exportování struktury pro tabulka social-network.groups
CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE latin2_czech_cs NOT NULL DEFAULT '0',
  `picPath` varchar(300) COLLATE latin2_czech_cs DEFAULT NULL,
  `description` varchar(500) COLLATE latin2_czech_cs DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin2 COLLATE=latin2_czech_cs;

-- Export dat nebyl vybrán.
-- Exportování struktury pro tabulka social-network.likes
CREATE TABLE IF NOT EXISTS `likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stuffId` int(11) NOT NULL DEFAULT '0',
  `userId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin2 COLLATE=latin2_czech_cs;

-- Export dat nebyl vybrán.
-- Exportování struktury pro tabulka social-network.members
CREATE TABLE IF NOT EXISTS `members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupId` int(11) NOT NULL DEFAULT '0',
  `memberId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin2 COLLATE=latin2_czech_cs;

-- Export dat nebyl vybrán.
-- Exportování struktury pro tabulka social-network.stuff
CREATE TABLE IF NOT EXISTS `stuff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `addAt` datetime NOT NULL,
  `content` varchar(1000) COLLATE latin2_czech_cs DEFAULT NULL,
  `shareId` int(11) DEFAULT '0',
  `userId` int(11) DEFAULT NULL,
  `pic` varchar(500) COLLATE latin2_czech_cs DEFAULT '0',
  `groupId` int(11) DEFAULT '-1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin2 COLLATE=latin2_czech_cs;

-- Export dat nebyl vybrán.
-- Exportování struktury pro tabulka social-network.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) COLLATE latin2_czech_cs DEFAULT NULL,
  `password` varchar(1000) COLLATE latin2_czech_cs DEFAULT NULL,
  `name` varchar(150) COLLATE latin2_czech_cs DEFAULT '0',
  `lastname` varchar(150) COLLATE latin2_czech_cs DEFAULT '0',
  `about` varchar(1000) COLLATE latin2_czech_cs DEFAULT 'Něco o mě...',
  `profPicPath` varchar(1000) COLLATE latin2_czech_cs DEFAULT 'img/defProfPic.png',
  `headerPicPath` varchar(1000) COLLATE latin2_czech_cs DEFAULT NULL,
  `role` varchar(1000) COLLATE latin2_czech_cs DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin2 COLLATE=latin2_czech_cs;

-- Export dat nebyl vybrán.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
