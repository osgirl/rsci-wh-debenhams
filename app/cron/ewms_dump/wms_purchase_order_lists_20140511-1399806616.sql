-- MySQL dump 10.13  Distrib 5.5.36, for Win32 (x86)
--
-- Host: localhost    Database: test_ssi
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
-- Table structure for table `wms_purchase_order_lists`
--

DROP TABLE IF EXISTS `wms_purchase_order_lists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wms_purchase_order_lists` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `assigned_by` int(11) NOT NULL,
  `assigned_to_user_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `receiver_no` int(11) NOT NULL,
  `purchase_order_no` int(11) NOT NULL,
  `total_qty` int(11) NOT NULL,
  `po_status` tinyint(4) NOT NULL,
  `shipment_reference_no` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `latest_jda_sync_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `latest_mobile_sync_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `receiver_no` (`receiver_no`),
  KEY `purchase_order_lists_assigned_to_user_id_po_status_index` (`assigned_to_user_id`,`po_status`),
  KEY `purchase_order_lists_vendor_id_index` (`vendor_id`),
  KEY `purchase_order_lists_purchase_order_no_index` (`purchase_order_no`),
  KEY `purchase_order_lists_po_status_index` (`po_status`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wms_purchase_order_lists`
--

LOCK TABLES `wms_purchase_order_lists` WRITE;
/*!40000 ALTER TABLE `wms_purchase_order_lists` DISABLE KEYS */;
INSERT INTO `wms_purchase_order_lists` VALUES (1,0,0,30105,20110,10151,'9005',3,'0','0','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','2014-05-11 10:40:45','0000-00-00 00:00:00','0000-00-00 00:00:00'),(2,0,0,50015,20114,10155,'9005',3,'0','0','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','2014-05-11 10:40:45','0000-00-00 00:00:00','0000-00-00 00:00:00'),(3,0,0,90026,20146,10168,'9005',3,'0','0','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','2014-05-11 10:40:45','0000-00-00 00:00:00','0000-00-00 00:00:00'),(4,0,0,30030,20200,10176,'9005',3,'0','0','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','2014-05-11 10:40:45','0000-00-00 00:00:00','0000-00-00 00:00:00'),(5,0,0,30076,20145,10250,'9005',3,'0','0','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','2014-05-11 10:40:45','0000-00-00 00:00:00','0000-00-00 00:00:00'),(6,0,0,30014,20198,10383,'9005',3,'0','0','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','2014-05-11 10:40:45','0000-00-00 00:00:00','0000-00-00 00:00:00'),(7,0,0,30014,20199,10404,'9005',3,'0','0','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','2014-05-11 10:40:45','0000-00-00 00:00:00','0000-00-00 00:00:00'),(8,0,0,30039,20205,10464,'9005',3,'0','0','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','2014-05-11 10:40:45','0000-00-00 00:00:00','0000-00-00 00:00:00'),(9,0,0,1496,37069,31764,'9005',3,'0','0','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','2014-05-11 10:40:45','0000-00-00 00:00:00','0000-00-00 00:00:00'),(10,0,0,1496,37070,31765,'9005',3,'0','0','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','2014-05-11 10:40:45','0000-00-00 00:00:00','0000-00-00 00:00:00'),(11,0,0,1496,37071,31765,'9005',3,'0','0','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00','2014-05-11 10:40:45','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `wms_purchase_order_lists` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-05-11 12:10:17
