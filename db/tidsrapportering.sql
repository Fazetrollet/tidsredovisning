/*
SQLyog Community
MySQL - 8.0.31 : Database - tidrapportering
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `aktiviteter` */

DROP TABLE IF EXISTS `aktiviteter`;

CREATE TABLE `aktiviteter` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Namn` varchar(20) COLLATE utf8mb3_swedish_ci NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `UIX_namn` (`Namn`)
) ENGINE=InnoDB AUTO_INCREMENT=842 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_swedish_ci;

/*Data for the table `aktiviteter` */

insert  into `aktiviteter`(`ID`,`Namn`) values 
(3,'Någon annat'),
(352,'qwertyuio0'),
(2,'qwertyuiuytrew'),
(1,'slötitta på netflix'),
(7,'uWESRDGTFHJY');

/*Table structure for table `uppgifter` */

DROP TABLE IF EXISTS `uppgifter`;

CREATE TABLE `uppgifter` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Datum` date NOT NULL,
  `tid` time NOT NULL,
  `beskrivning` varchar(100) COLLATE utf8mb3_swedish_ci DEFAULT NULL,
  `AktivitetID` int NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `AktivitetID` (`AktivitetID`),
  CONSTRAINT `uppgifter_ibfk_1` FOREIGN KEY (`AktivitetID`) REFERENCES `aktiviteter` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=164 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_swedish_ci;

/*Data for the table `uppgifter` */

insert  into `uppgifter`(`ID`,`Datum`,`tid`,`beskrivning`,`AktivitetID`) values 
(6,'2024-01-11','05:00:00','lewpfhwiuhfdjwiodjfewo',2),
(17,'2024-01-03','07:00:00','wkwkwkwkwwkw',7),
(18,'2024-01-29','08:00:00','edfrtyukio',352),
(19,'2024-01-01','01:00:00','1',1);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
