-- MySQL dump 10.13  Distrib 5.5.36, for Win32 (x86)
--
-- Host: localhost    Database: ssi
-- ------------------------------------------------------
-- Server version	5.5.36

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `wms_letdown_details`
--

DROP TABLE IF EXISTS `wms_letdown_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wms_letdown_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `store_or_sku` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `move_doc_number` int(11) NOT NULL DEFAULT '0',
  `from_slot_code` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `to_slot_code` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `quantity_to_pick` int(11) NOT NULL DEFAULT '0',
  `moved_qty` int(11) NOT NULL DEFAULT '0',
  `move_to_picking_area` tinyint(4) NOT NULL DEFAULT '0',
  `store_code` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `so_no` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `letdown_details_store_or_sku_move_doc_number_so_no_unique` (`store_or_sku`,`move_doc_number`,`so_no`),
  KEY `letdown_details_from_slot_code_move_to_picking_area_index` (`from_slot_code`,`move_to_picking_area`),
  KEY `letdown_details_store_or_sku_index` (`store_or_sku`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wms_letdown_details`
--

LOCK TABLES `wms_letdown_details` WRITE;
/*!40000 ALTER TABLE `wms_letdown_details` DISABLE KEYS */;
INSERT INTO `wms_letdown_details` VALUES (1,'900496',210,'PCK00001','',2,0,0,'20','10472','2014-05-12 13:06:33','0000-00-00 00:00:00','0000-00-00 00:00:00'),(2,'900510',210,'PCK00001','',3,0,0,'20','10472','2014-05-12 13:06:33','0000-00-00 00:00:00','0000-00-00 00:00:00'),(3,'902370',210,'PCK00001','',4,0,0,'20','10472','2014-05-12 13:06:33','0000-00-00 00:00:00','0000-00-00 00:00:00'),(4,'900496',211,'PCK00001','',6,0,0,'26','10473','2014-05-12 13:06:33','0000-00-00 00:00:00','0000-00-00 00:00:00'),(5,'902370',211,'PCK00001','',2,0,0,'26','10473','2014-05-12 13:06:33','0000-00-00 00:00:00','0000-00-00 00:00:00'),(6,'902371',211,'PCK00001','',5,0,0,'26','10473','2014-05-12 13:06:33','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `wms_letdown_details` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-05-12 14:06:42
