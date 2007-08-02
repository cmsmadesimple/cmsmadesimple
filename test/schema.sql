-- MySQL dump 10.9
--
-- Host: localhost    Database: cms3
-- ------------------------------------------------------
-- Server version	4.1.22-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cms2_additional_htmlblob_users`
--

DROP TABLE IF EXISTS `cms2_additional_htmlblob_users`;
CREATE TABLE `cms2_additional_htmlblob_users` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `htmlblob_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_additional_htmlblob_users`
--

LOCK TABLES `cms2_additional_htmlblob_users` WRITE;
/*!40000 ALTER TABLE `cms2_additional_htmlblob_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms2_additional_htmlblob_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_additional_users`
--

DROP TABLE IF EXISTS `cms2_additional_users`;
CREATE TABLE `cms2_additional_users` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `page_id` int(11) default NULL,
  `content_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_additional_users`
--

LOCK TABLES `cms2_additional_users` WRITE;
/*!40000 ALTER TABLE `cms2_additional_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms2_additional_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_admin_bookmarks`
--

DROP TABLE IF EXISTS `cms2_admin_bookmarks`;
CREATE TABLE `cms2_admin_bookmarks` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `title` varchar(255) default NULL,
  `url` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_admin_bookmarks`
--

LOCK TABLES `cms2_admin_bookmarks` WRITE;
/*!40000 ALTER TABLE `cms2_admin_bookmarks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms2_admin_bookmarks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_admin_recent_pages`
--

DROP TABLE IF EXISTS `cms2_admin_recent_pages`;
CREATE TABLE `cms2_admin_recent_pages` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `title` varchar(255) default NULL,
  `url` varchar(255) default NULL,
  `access_time` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_admin_recent_pages`
--

LOCK TABLES `cms2_admin_recent_pages` WRITE;
/*!40000 ALTER TABLE `cms2_admin_recent_pages` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms2_admin_recent_pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_adminlog`
--

DROP TABLE IF EXISTS `cms2_adminlog`;
CREATE TABLE `cms2_adminlog` (
  `timestamp` int(11) default NULL,
  `user_id` int(11) default NULL,
  `username` varchar(25) default NULL,
  `item_id` int(11) default NULL,
  `item_name` varchar(50) default NULL,
  `action` varchar(255) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_adminlog`
--

LOCK TABLES `cms2_adminlog` WRITE;
/*!40000 ALTER TABLE `cms2_adminlog` DISABLE KEYS */;
INSERT INTO `cms2_adminlog` VALUES (1181419145,1,'',1,'admin','User Login'),(1181695156,1,'',1,'admin','User Login'),(1181695198,1,'',1,'Test','Added Content'),(1181696251,1,'',2,'Test','Added Content'),(1181696359,1,'',3,'Test 2','Added Content'),(1181696801,1,'',4,'Test','Added Content'),(1181696817,1,'',5,'Test 2','Added Content'),(1181698196,1,'',6,'Test','Added Content'),(1181698336,1,'',7,'Test 2','Added Content'),(1181698376,1,'',8,'Test 3','Added Content'),(1181698799,1,'',9,'Test','Added Content'),(1181698809,1,'',10,'Test 2','Added Content'),(1181698879,1,'',11,'Test','Added Content'),(1181698888,1,'',12,'Test 2','Added Content'),(1181699277,1,'',13,'Test','Added Content'),(1181699293,1,'',14,'Test 2','Added Content'),(1181699533,1,'',15,'Test','Added Content'),(1181699581,1,'',16,'Test','Added Content'),(1181699907,1,'',16,'Test 2','Added Content'),(1181702287,1,'',16,'Test','Added Content'),(1181702295,1,'',16,'Test 2','Added Content'),(1181702320,1,'',17,'Test','Added Content'),(1181702337,1,'',18,'Test 2','Added Content'),(1181702374,1,'',19,'Test 1.1','Added Content'),(1181702514,1,'',20,'Test 1.1 2','Added Content'),(1181702552,1,'',21,'Test 2.1','Added Content'),(1181703334,1,'',22,'Test','Added Content'),(1181703408,1,'',23,'Test','Added Content'),(1181703427,1,'',24,'Test','Added Content'),(1181704047,1,'',25,'Test 2.1','Added Content'),(1181705155,1,'',26,'Test','Added Content'),(1181705174,1,'',27,'Test 2','Added Content'),(1181705204,1,'',28,'Test 2.1','Added Content'),(1181705704,1,'',29,'Test 2.1.1','Added Content'),(1181705891,1,'',30,'Test','Added Content'),(1181705978,1,'',31,'Test','Added Content'),(1181706093,1,'',32,'Test','Added Content'),(1181706239,1,'',33,'Test','Added Content'),(1181706305,1,'',34,'Test','Added Content'),(1181706444,1,'',35,'Test','Added Content'),(1181706480,1,'',36,'Test','Added Content'),(1181706559,1,'',37,'Test','Added Content'),(1181706652,1,'',37,'Test','Added Content'),(1181706667,1,'',38,'Test','Added Content'),(1181706710,1,'',39,'Test','Added Content'),(1181706770,1,'',40,'Test','Added Content'),(1181706792,1,'',41,'Test','Added Content'),(1181706818,1,'',42,'Test','Added Content'),(1181728438,1,'',1,'Test','Edited Template'),(1183141629,1,'',1,'admin','User Login'),(1183322968,1,'',1,'Test','Edited Template'),(1183323816,1,'',1,'Test','Edited Template'),(1183323913,1,'',1,'Test','Added CSS'),(1183324188,1,'',1,'Test','Added Stylesheet Association'),(1183926387,1,'',1,'admin','User Login'),(1183926639,1,'',43,'Testing Again','Added Content'),(1183926737,1,'',43,'Testing Again','Edited Content'),(1183926774,1,'',43,'Testing Again','Edited Content'),(1183927092,1,'',43,'Testing Again','Edited Content'),(1183927418,1,'',43,'Testing Again','Edited Content'),(1183927436,1,'',43,'Testing Again','Edited Content'),(1183927456,1,'',43,'Testing Again','Edited Content'),(1183928326,1,'',27,'Test 2','Edited Content'),(1183928380,1,'',27,'Test 2','Edited Content'),(1183946249,1,'',28,'Test 2.1','Edited Content'),(1183946470,1,'',28,'Test 2.1','Edited Content'),(1183946596,1,'',28,'Test 2.1','Edited Content'),(1183946709,1,'',28,'Test 2.1','Edited Content'),(1183946744,1,'',27,'Test 2','Edited Content'),(1184029279,1,'',28,'Test 2.1','Edited Content'),(1184069263,1,'',1,'admin','User Login'),(1185058118,1,'',1,'admin','User Login'),(1185058997,1,'',1,'Test','Edited Template'),(1185062394,1,'',1,'admin','User Login'),(1185238159,1,'',1,'admin','User Login'),(1185270421,1,'',44,'Yet Another Test','Added Content'),(1185270467,1,'',44,'Yet Another Test','Edited Content'),(1185760350,1,'',1,'admin','User Login'),(1185798171,1,'',1,'','User Logout'),(1185798179,1,'',1,'admin','User Login'),(1185798200,1,'',1,'','User Logout'),(1185798543,1,'',1,'admin','User Login'),(1185798615,1,'',1,'','User Logout'),(1185798622,1,'',1,'admin','User Login'),(1185798650,1,'',1,'','User Logout'),(1185798677,1,'',1,'admin','User Login'),(1185799504,1,'',1,'','User Logout'),(1185806971,1,'',1,'admin','User Login'),(1185807323,1,'',1,'','User Logout'),(1185808557,1,'',1,'admin','User Login'),(1185812006,1,'',1,'','User Logout'),(1185886374,1,'',1,'admin','User Login'),(1185886640,1,'',2,'Test','Added Content'),(1185887165,1,'',2,'Test','Edited Content'),(1185887270,1,'',3,'Test 2','Added Content'),(1185887344,1,'',3,'Test 2','Edited Content'),(1185887553,1,'',1,'','User Logout'),(1185888560,1,'',1,'admin','User Login'),(1186073410,1,'',1,'admin','User Login'),(1186073457,1,'',4,'Test 1.2','Added Content'),(1186073484,1,'',5,'Test 1.2.1','Added Content'),(1186073509,1,'',6,'Test 1.2.2','Added Content'),(1186073543,1,'',3,'Test 1.1','Edited Content'),(1186073564,1,'',2,'Test 1','Edited Content'),(1186073615,1,'',7,'Test 2','Added Content'),(1186073640,1,'',8,'Test 2.1','Added Content'),(1186073743,1,'',9,'Test 2.2','Added Content'),(1186073761,1,'',10,'Test 3','Added Content'),(1186073785,1,'',11,'Test 4','Added Content'),(1186073835,1,'',12,'Test 4.1','Added Content'),(1186073871,1,'',13,'Test 4.2','Added Content'),(1186073926,1,'',14,'Test 4.2.1','Added Content'),(1186073951,1,'',15,'Test 4.2.2','Added Content'),(1186073977,1,'',16,'Test 4.2.2.1','Added Content'),(1186074510,1,'',1,'','User Logout'),(1186079258,1,'',1,'admin','User Login'),(1186079374,1,'',1,'','User Logout');
/*!40000 ALTER TABLE `cms2_adminlog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_content`
--

DROP TABLE IF EXISTS `cms2_content`;
CREATE TABLE `cms2_content` (
  `id` int(11) NOT NULL auto_increment,
  `content_name` varchar(255) default NULL,
  `type` varchar(25) default NULL,
  `owner_id` int(11) default NULL,
  `parent_id` int(11) default NULL,
  `template_id` int(11) default NULL,
  `item_order` int(11) default NULL,
  `hierarchy` varchar(255) default NULL,
  `default_content` tinyint(4) default NULL,
  `menu_text` varchar(255) default NULL,
  `content_alias` varchar(255) default NULL,
  `show_in_menu` tinyint(4) default NULL,
  `collapsed` tinyint(4) default NULL,
  `markup` varchar(25) default NULL,
  `active` tinyint(4) default NULL,
  `cachable` tinyint(4) default NULL,
  `id_hierarchy` varchar(255) default NULL,
  `hierarchy_path` text,
  `prop_names` text,
  `metadata` text,
  `titleattribute` varchar(255) default NULL,
  `tabindex` varchar(10) default NULL,
  `accesskey` varchar(5) default NULL,
  `last_modified_by` int(11) default NULL,
  `create_date` datetime default NULL,
  `modified_date` datetime default NULL,
  `lft` int(11) default NULL,
  `rgt` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `alias_and_active` (`content_alias`,`active`),
  KEY `default_content` (`default_content`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_content`
--

LOCK TABLES `cms2_content` WRITE;
/*!40000 ALTER TABLE `cms2_content` DISABLE KEYS */;
INSERT INTO `cms2_content` VALUES (1,'__root_node__','root',1,-1,-1,1,'',0,'__root_node__','__root_node__',0,0,'none',1,0,'','__root_node__','','','','','',1,NULL,NULL,1,32),(2,'Test 1','content',1,1,1,1,'1',1,'Test 1','test',1,0,'',1,0,'2','test','default-block-type,default-content,content_block_2-block-type,content_block_2-content','','','','',1,'2007-07-31 12:57:20','2007-08-02 16:52:44',2,11),(3,'Test 1.1','content',1,2,1,1,'1.1',0,'Test 1.1','test-2',1,0,'',1,0,'2.3','test/test-2','default-block-type,default-content,content_block_2-block-type,content_block_2-content','','','','',1,'2007-07-31 13:07:50','2007-08-02 16:52:23',3,4),(4,'Test 1.2','content',1,2,1,2,'1.2',0,'Test 1.2','test-1-2',1,0,'',1,0,'2.4','test/test-1-2','default-block-type,default-content,content_block_2-block-type,content_block_2-content','','','','',1,'2007-08-02 16:50:57','2007-08-02 16:50:57',5,10),(5,'Test 1.2.1','content',1,4,1,1,'1.2.1',0,'Test 1.2.1','test-1-2-1',1,0,'',1,0,'2.4.5','test/test-1-2/test-1-2-1','default-block-type,default-content,content_block_2-block-type,content_block_2-content','','','','',1,'2007-08-02 16:51:24','2007-08-02 16:51:24',6,7),(6,'Test 1.2.2','content',1,4,1,2,'1.2.2',0,'Test 1.2.2','test-1-2-2',1,0,'',1,0,'2.4.6','test/test-1-2/test-1-2-2','default-block-type,default-content,content_block_2-block-type,content_block_2-content','','','','',1,'2007-08-02 16:51:49','2007-08-02 16:51:49',8,9),(7,'Test 2','content',1,1,1,2,'2',0,'Test 2','test-2-2',1,0,'',1,0,'7','test-2-2','default-block-type,default-content,content_block_2-block-type,content_block_2-content','','','','',1,'2007-08-02 16:53:35','2007-08-02 16:53:35',12,17),(8,'Test 2.1','content',1,7,1,1,'2.1',0,'Test 2.1','test-2-1',1,0,'',1,0,'7.8','test-2-2/test-2-1','default-block-type,default-content,content_block_2-block-type,content_block_2-content','','','','',1,'2007-08-02 16:54:00','2007-08-02 16:54:00',13,14),(9,'Test 2.2','content',1,7,1,2,'2.2',0,'Test 2.2','test-2-2-2',1,0,'',1,0,'7.9','test-2-2/test-2-2-2','default-block-type,default-content,content_block_2-block-type,content_block_2-content','','','','',1,'2007-08-02 16:55:43','2007-08-02 16:55:43',15,16),(10,'Test 3','content',1,1,1,3,'3',0,'Test 3','test-3',1,0,'',1,0,'10','test-3','default-block-type,default-content,content_block_2-block-type,content_block_2-content','','','','',1,'2007-08-02 16:56:01','2007-08-02 16:56:01',18,19),(11,'Test 4','content',1,1,1,4,'4',0,'Test 4','test-4',1,0,'',1,0,'11','test-4','default-block-type,default-content,content_block_2-block-type,content_block_2-content','','','','',1,'2007-08-02 16:56:25','2007-08-02 16:56:25',20,31),(12,'Test 4.1','content',1,11,1,1,'4.1',0,'Test 4.1','test-4-1',1,0,'',1,0,'11.12','test-4/test-4-1','default-block-type,default-content,content_block_2-block-type,content_block_2-content','','','','',1,'2007-08-02 16:57:15','2007-08-02 16:57:15',21,22),(13,'Test 4.2','content',1,11,1,2,'4.2',0,'Test 4.2','test-4-2',1,0,'',1,0,'11.13','test-4/test-4-2','default-block-type,default-content,content_block_2-block-type,content_block_2-content','','','','',1,'2007-08-02 16:57:51','2007-08-02 16:57:51',23,30),(14,'Test 4.2.1','content',1,13,1,1,'4.2.1',0,'Test 4.2.1','test-4-2-1',1,0,'',1,0,'11.13.14','test-4/test-4-2/test-4-2-1','default-block-type,default-content,content_block_2-block-type,content_block_2-content','','','','',1,'2007-08-02 16:58:46','2007-08-02 16:58:46',24,25),(15,'Test 4.2.2','content',1,13,1,2,'4.2.2',0,'Test 4.2.2','test-4-2-2',1,0,'',1,0,'11.13.15','test-4/test-4-2/test-4-2-2','default-block-type,default-content,content_block_2-block-type,content_block_2-content','','','','',1,'2007-08-02 16:59:11','2007-08-02 16:59:11',26,29),(16,'Test 4.2.2.1','content',1,15,1,1,'4.2.2.1',0,'Test 4.2.2.1','test-4-2-2-1',1,0,'',1,0,'11.13.15.16','test-4/test-4-2/test-4-2-2/test-4-2-2-1','default-block-type,default-content,content_block_2-block-type,content_block_2-content','','','','',1,'2007-08-02 16:59:37','2007-08-02 16:59:37',27,28);
/*!40000 ALTER TABLE `cms2_content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_content_props`
--

DROP TABLE IF EXISTS `cms2_content_props`;
CREATE TABLE `cms2_content_props` (
  `id` int(11) NOT NULL auto_increment,
  `content_id` int(11) default NULL,
  `type` varchar(25) default NULL,
  `prop_name` varchar(255) default NULL,
  `param1` varchar(255) default NULL,
  `param2` varchar(255) default NULL,
  `param3` varchar(255) default NULL,
  `content` longtext,
  `create_date` datetime default NULL,
  `modified_date` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `content_id` (`content_id`)
) ENGINE=MyISAM AUTO_INCREMENT=61 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_content_props`
--

LOCK TABLES `cms2_content_props` WRITE;
/*!40000 ALTER TABLE `cms2_content_props` DISABLE KEYS */;
INSERT INTO `cms2_content_props` VALUES (1,2,'cmscontentproperty','default-block-type','','','','html','2007-07-31 12:57:20','2007-08-02 16:52:44'),(2,2,'cmscontentproperty','default-content','','','','Test','2007-07-31 12:57:20','2007-08-02 16:52:44'),(3,2,'cmscontentproperty','content_block_2-block-type','','','','html','2007-07-31 12:57:20','2007-08-02 16:52:44'),(4,2,'cmscontentproperty','content_block_2-content','','','','Test','2007-07-31 12:57:20','2007-08-02 16:52:44'),(5,3,'cmscontentproperty','default-block-type','','','','html','2007-07-31 13:07:50','2007-08-02 16:52:23'),(6,3,'cmscontentproperty','default-content','','','','Testing','2007-07-31 13:07:50','2007-08-02 16:52:23'),(7,3,'cmscontentproperty','content_block_2-block-type','','','','html','2007-07-31 13:07:50','2007-08-02 16:52:23'),(8,3,'cmscontentproperty','content_block_2-content','','','','Testing','2007-07-31 13:07:50','2007-08-02 16:52:23'),(9,4,'cmscontentproperty','default-block-type','','','','html','2007-08-02 16:50:57','2007-08-02 16:50:57'),(10,4,'cmscontentproperty','default-content','','','','Test','2007-08-02 16:50:57','2007-08-02 16:50:57'),(11,4,'cmscontentproperty','content_block_2-block-type','','','','html','2007-08-02 16:50:57','2007-08-02 16:50:57'),(12,4,'cmscontentproperty','content_block_2-content','','','','Test','2007-08-02 16:50:57','2007-08-02 16:50:57'),(13,5,'cmscontentproperty','default-block-type','','','','html','2007-08-02 16:51:24','2007-08-02 16:51:24'),(14,5,'cmscontentproperty','default-content','','','','Test','2007-08-02 16:51:24','2007-08-02 16:51:24'),(15,5,'cmscontentproperty','content_block_2-block-type','','','','html','2007-08-02 16:51:24','2007-08-02 16:51:24'),(16,5,'cmscontentproperty','content_block_2-content','','','','Test','2007-08-02 16:51:24','2007-08-02 16:51:24'),(17,6,'cmscontentproperty','default-block-type','','','','html','2007-08-02 16:51:49','2007-08-02 16:51:49'),(18,6,'cmscontentproperty','default-content','','','','Test','2007-08-02 16:51:49','2007-08-02 16:51:49'),(19,6,'cmscontentproperty','content_block_2-block-type','','','','html','2007-08-02 16:51:49','2007-08-02 16:51:49'),(20,6,'cmscontentproperty','content_block_2-content','','','','Test','2007-08-02 16:51:49','2007-08-02 16:51:49'),(21,7,'cmscontentproperty','default-block-type','','','','html','2007-08-02 16:53:35','2007-08-02 16:53:35'),(22,7,'cmscontentproperty','default-content','','','','Test','2007-08-02 16:53:35','2007-08-02 16:53:35'),(23,7,'cmscontentproperty','content_block_2-block-type','','','','html','2007-08-02 16:53:35','2007-08-02 16:53:35'),(24,7,'cmscontentproperty','content_block_2-content','','','','Test','2007-08-02 16:53:35','2007-08-02 16:53:35'),(25,8,'cmscontentproperty','default-block-type','','','','html','2007-08-02 16:54:00','2007-08-02 16:54:00'),(26,8,'cmscontentproperty','default-content','','','','Test','2007-08-02 16:54:00','2007-08-02 16:54:00'),(27,8,'cmscontentproperty','content_block_2-block-type','','','','html','2007-08-02 16:54:00','2007-08-02 16:54:00'),(28,8,'cmscontentproperty','content_block_2-content','','','','Test','2007-08-02 16:54:00','2007-08-02 16:54:00'),(29,9,'cmscontentproperty','default-block-type','','','','html','2007-08-02 16:55:43','2007-08-02 16:55:43'),(30,9,'cmscontentproperty','default-content','','','','Test','2007-08-02 16:55:43','2007-08-02 16:55:43'),(31,9,'cmscontentproperty','content_block_2-block-type','','','','html','2007-08-02 16:55:43','2007-08-02 16:55:43'),(32,9,'cmscontentproperty','content_block_2-content','','','','Test','2007-08-02 16:55:43','2007-08-02 16:55:43'),(33,10,'cmscontentproperty','default-block-type','','','','html','2007-08-02 16:56:01','2007-08-02 16:56:01'),(34,10,'cmscontentproperty','default-content','','','','Test','2007-08-02 16:56:01','2007-08-02 16:56:01'),(35,10,'cmscontentproperty','content_block_2-block-type','','','','html','2007-08-02 16:56:01','2007-08-02 16:56:01'),(36,10,'cmscontentproperty','content_block_2-content','','','','Test','2007-08-02 16:56:01','2007-08-02 16:56:01'),(37,11,'cmscontentproperty','default-block-type','','','','html','2007-08-02 16:56:25','2007-08-02 16:56:25'),(38,11,'cmscontentproperty','default-content','','','','Test','2007-08-02 16:56:25','2007-08-02 16:56:25'),(39,11,'cmscontentproperty','content_block_2-block-type','','','','html','2007-08-02 16:56:25','2007-08-02 16:56:25'),(40,11,'cmscontentproperty','content_block_2-content','','','','Test','2007-08-02 16:56:25','2007-08-02 16:56:25'),(41,12,'cmscontentproperty','default-block-type','','','','html','2007-08-02 16:57:15','2007-08-02 16:57:15'),(42,12,'cmscontentproperty','default-content','','','','Test','2007-08-02 16:57:15','2007-08-02 16:57:15'),(43,12,'cmscontentproperty','content_block_2-block-type','','','','html','2007-08-02 16:57:15','2007-08-02 16:57:15'),(44,12,'cmscontentproperty','content_block_2-content','','','','Test','2007-08-02 16:57:15','2007-08-02 16:57:15'),(45,13,'cmscontentproperty','default-block-type','','','','html','2007-08-02 16:57:51','2007-08-02 16:57:51'),(46,13,'cmscontentproperty','default-content','','','','Test','2007-08-02 16:57:51','2007-08-02 16:57:51'),(47,13,'cmscontentproperty','content_block_2-block-type','','','','html','2007-08-02 16:57:51','2007-08-02 16:57:51'),(48,13,'cmscontentproperty','content_block_2-content','','','','Test','2007-08-02 16:57:51','2007-08-02 16:57:51'),(49,14,'cmscontentproperty','default-block-type','','','','html','2007-08-02 16:58:46','2007-08-02 16:58:46'),(50,14,'cmscontentproperty','default-content','','','','Test','2007-08-02 16:58:46','2007-08-02 16:58:46'),(51,14,'cmscontentproperty','content_block_2-block-type','','','','html','2007-08-02 16:58:46','2007-08-02 16:58:46'),(52,14,'cmscontentproperty','content_block_2-content','','','','Test','2007-08-02 16:58:46','2007-08-02 16:58:46'),(53,15,'cmscontentproperty','default-block-type','','','','html','2007-08-02 16:59:11','2007-08-02 16:59:11'),(54,15,'cmscontentproperty','default-content','','','','Test','2007-08-02 16:59:11','2007-08-02 16:59:11'),(55,15,'cmscontentproperty','content_block_2-block-type','','','','html','2007-08-02 16:59:11','2007-08-02 16:59:11'),(56,15,'cmscontentproperty','content_block_2-content','','','','Test','2007-08-02 16:59:11','2007-08-02 16:59:11'),(57,16,'cmscontentproperty','default-block-type','','','','html','2007-08-02 16:59:37','2007-08-02 16:59:37'),(58,16,'cmscontentproperty','default-content','','','','Test','2007-08-02 16:59:37','2007-08-02 16:59:37'),(59,16,'cmscontentproperty','content_block_2-block-type','','','','html','2007-08-02 16:59:37','2007-08-02 16:59:37'),(60,16,'cmscontentproperty','content_block_2-content','','','','Test','2007-08-02 16:59:37','2007-08-02 16:59:37');
/*!40000 ALTER TABLE `cms2_content_props` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_crossref`
--

DROP TABLE IF EXISTS `cms2_crossref`;
CREATE TABLE `cms2_crossref` (
  `child_type` varchar(100) default NULL,
  `child_id` int(11) default NULL,
  `parent_type` varchar(100) default NULL,
  `parent_id` int(11) default NULL,
  `create_date` datetime default NULL,
  `modified_date` datetime default NULL,
  KEY `child_type_and_id` (`child_type`,`child_id`),
  KEY `parent_type_and_id` (`parent_type`,`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_crossref`
--

LOCK TABLES `cms2_crossref` WRITE;
/*!40000 ALTER TABLE `cms2_crossref` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms2_crossref` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_css`
--

DROP TABLE IF EXISTS `cms2_css`;
CREATE TABLE `cms2_css` (
  `id` int(11) NOT NULL auto_increment,
  `css_name` varchar(255) default NULL,
  `css_text` longtext,
  `media_type` varchar(255) default NULL,
  `create_date` datetime default NULL,
  `modified_date` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `css_name` (`css_name`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_css`
--

LOCK TABLES `cms2_css` WRITE;
/*!40000 ALTER TABLE `cms2_css` DISABLE KEYS */;
INSERT INTO `cms2_css` VALUES (1,'Test','body {\r\n  font-size: 12px;\r\n  font-family: Verdana;\r\n}','screen','2007-07-01 21:05:13','2007-07-01 21:05:13');
/*!40000 ALTER TABLE `cms2_css` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_css_assoc`
--

DROP TABLE IF EXISTS `cms2_css_assoc`;
CREATE TABLE `cms2_css_assoc` (
  `assoc_to_id` int(11) default NULL,
  `assoc_css_id` int(11) default NULL,
  `assoc_type` varchar(80) default NULL,
  `create_date` datetime default NULL,
  `modified_date` datetime default NULL,
  KEY `assoc_to_id` (`assoc_to_id`),
  KEY `assoc_css_id` (`assoc_css_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_css_assoc`
--

LOCK TABLES `cms2_css_assoc` WRITE;
/*!40000 ALTER TABLE `cms2_css_assoc` DISABLE KEYS */;
INSERT INTO `cms2_css_assoc` VALUES (1,1,'template','2007-07-01 21:09:48','2007-07-01 21:09:48');
/*!40000 ALTER TABLE `cms2_css_assoc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_css_seq`
--

DROP TABLE IF EXISTS `cms2_css_seq`;
CREATE TABLE `cms2_css_seq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_css_seq`
--

LOCK TABLES `cms2_css_seq` WRITE;
/*!40000 ALTER TABLE `cms2_css_seq` DISABLE KEYS */;
INSERT INTO `cms2_css_seq` VALUES (1);
/*!40000 ALTER TABLE `cms2_css_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_event_handlers`
--

DROP TABLE IF EXISTS `cms2_event_handlers`;
CREATE TABLE `cms2_event_handlers` (
  `event_id` int(11) default NULL,
  `tag_name` varchar(255) default NULL,
  `module_name` varchar(255) default NULL,
  `removable` int(11) default NULL,
  `handler_order` int(11) default NULL,
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_event_handlers`
--

LOCK TABLES `cms2_event_handlers` WRITE;
/*!40000 ALTER TABLE `cms2_event_handlers` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms2_event_handlers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_events`
--

DROP TABLE IF EXISTS `cms2_events`;
CREATE TABLE `cms2_events` (
  `originator` varchar(200) NOT NULL default '',
  `event_name` varchar(200) NOT NULL default '',
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `originator` (`originator`),
  KEY `event_name` (`event_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_events`
--

LOCK TABLES `cms2_events` WRITE;
/*!40000 ALTER TABLE `cms2_events` DISABLE KEYS */;
INSERT INTO `cms2_events` VALUES ('News','NewsArticleAdded',1),('News','NewsArticleEdited',2),('News','NewsArticleDeleted',3),('News','NewsCategoryAdded',4),('News','NewsCategoryEdited',5),('News','NewsCategoryDeleted',6);
/*!40000 ALTER TABLE `cms2_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_events_seq`
--

DROP TABLE IF EXISTS `cms2_events_seq`;
CREATE TABLE `cms2_events_seq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_events_seq`
--

LOCK TABLES `cms2_events_seq` WRITE;
/*!40000 ALTER TABLE `cms2_events_seq` DISABLE KEYS */;
INSERT INTO `cms2_events_seq` VALUES (6);
/*!40000 ALTER TABLE `cms2_events_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_group_permissions`
--

DROP TABLE IF EXISTS `cms2_group_permissions`;
CREATE TABLE `cms2_group_permissions` (
  `id` int(11) NOT NULL auto_increment,
  `permission_defn_id` int(11) default NULL,
  `group_id` int(11) default NULL,
  `object_id` int(11) default NULL,
  `has_access` tinyint(4) default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=95 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_group_permissions`
--

LOCK TABLES `cms2_group_permissions` WRITE;
/*!40000 ALTER TABLE `cms2_group_permissions` DISABLE KEYS */;
INSERT INTO `cms2_group_permissions` VALUES (93,3,-1,1,0),(94,3,1,1,1);
/*!40000 ALTER TABLE `cms2_group_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_group_perms`
--

DROP TABLE IF EXISTS `cms2_group_perms`;
CREATE TABLE `cms2_group_perms` (
  `id` int(11) NOT NULL auto_increment,
  `group_id` int(11) default NULL,
  `permission_id` int(11) default NULL,
  `create_date` datetime default NULL,
  `modified_date` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `group_and_permission` (`group_id`,`permission_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_group_perms`
--

LOCK TABLES `cms2_group_perms` WRITE;
/*!40000 ALTER TABLE `cms2_group_perms` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms2_group_perms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_group_perms_seq`
--

DROP TABLE IF EXISTS `cms2_group_perms_seq`;
CREATE TABLE `cms2_group_perms_seq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_group_perms_seq`
--

LOCK TABLES `cms2_group_perms_seq` WRITE;
/*!40000 ALTER TABLE `cms2_group_perms_seq` DISABLE KEYS */;
INSERT INTO `cms2_group_perms_seq` VALUES (4);
/*!40000 ALTER TABLE `cms2_group_perms_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_groups`
--

DROP TABLE IF EXISTS `cms2_groups`;
CREATE TABLE `cms2_groups` (
  `id` int(11) NOT NULL auto_increment,
  `group_name` varchar(25) default NULL,
  `active` tinyint(4) default NULL,
  `create_date` datetime default NULL,
  `modified_date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_groups`
--

LOCK TABLES `cms2_groups` WRITE;
/*!40000 ALTER TABLE `cms2_groups` DISABLE KEYS */;
INSERT INTO `cms2_groups` VALUES (1,'Anonymous',1,'2007-07-30 03:56:29','2007-07-30 03:56:29'),(2,'Admin',1,'2007-07-30 04:02:26','2007-07-30 04:02:26');
/*!40000 ALTER TABLE `cms2_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_htmlblobs`
--

DROP TABLE IF EXISTS `cms2_htmlblobs`;
CREATE TABLE `cms2_htmlblobs` (
  `id` int(11) NOT NULL auto_increment,
  `htmlblob_name` varchar(255) default NULL,
  `html` longtext,
  `owner` int(11) default NULL,
  `version` int(11) default NULL,
  `create_date` datetime default NULL,
  `modified_date` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `htmlblob_name` (`htmlblob_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_htmlblobs`
--

LOCK TABLES `cms2_htmlblobs` WRITE;
/*!40000 ALTER TABLE `cms2_htmlblobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms2_htmlblobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_module_deps`
--

DROP TABLE IF EXISTS `cms2_module_deps`;
CREATE TABLE `cms2_module_deps` (
  `parent_module` varchar(25) default NULL,
  `child_module` varchar(25) default NULL,
  `minimum_version` varchar(25) default NULL,
  `create_date` datetime default NULL,
  `modified_date` datetime default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_module_deps`
--

LOCK TABLES `cms2_module_deps` WRITE;
/*!40000 ALTER TABLE `cms2_module_deps` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms2_module_deps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_module_news`
--

DROP TABLE IF EXISTS `cms2_module_news`;
CREATE TABLE `cms2_module_news` (
  `news_id` int(11) NOT NULL default '0',
  `news_category_id` int(11) default NULL,
  `news_title` varchar(255) default NULL,
  `news_data` text,
  `news_date` datetime default NULL,
  `summary` text,
  `start_time` datetime default NULL,
  `end_time` datetime default NULL,
  `status` varchar(25) default NULL,
  `icon` varchar(255) default NULL,
  `create_date` datetime default NULL,
  `modified_date` datetime default NULL,
  `use_expiration` int(1) default NULL,
  `author_id` int(11) default NULL,
  PRIMARY KEY  (`news_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_module_news`
--

LOCK TABLES `cms2_module_news` WRITE;
/*!40000 ALTER TABLE `cms2_module_news` DISABLE KEYS */;
INSERT INTO `cms2_module_news` VALUES (1,1,'News Module Installed','The news module was installed.  Exciting. This news article is not using the Summary field and therefore there is no link to read more. But you can click on the news heading to read only this article.','2007-07-23 16:02:51',NULL,NULL,NULL,'published',NULL,'2007-07-23 16:02:51','2007-07-23 16:02:51',0,1);
/*!40000 ALTER TABLE `cms2_module_news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_module_news_categories`
--

DROP TABLE IF EXISTS `cms2_module_news_categories`;
CREATE TABLE `cms2_module_news_categories` (
  `news_category_id` int(11) NOT NULL default '0',
  `news_category_name` varchar(255) default NULL,
  `parent_id` int(11) default NULL,
  `hierarchy` varchar(255) default NULL,
  `long_name` text,
  `create_date` datetime default NULL,
  `modified_date` datetime default NULL,
  PRIMARY KEY  (`news_category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_module_news_categories`
--

LOCK TABLES `cms2_module_news_categories` WRITE;
/*!40000 ALTER TABLE `cms2_module_news_categories` DISABLE KEYS */;
INSERT INTO `cms2_module_news_categories` VALUES (1,'General',-1,'00001','General','2007-07-23 16:02:51','2007-07-23 16:02:51');
/*!40000 ALTER TABLE `cms2_module_news_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_module_news_categories_seq`
--

DROP TABLE IF EXISTS `cms2_module_news_categories_seq`;
CREATE TABLE `cms2_module_news_categories_seq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_module_news_categories_seq`
--

LOCK TABLES `cms2_module_news_categories_seq` WRITE;
/*!40000 ALTER TABLE `cms2_module_news_categories_seq` DISABLE KEYS */;
INSERT INTO `cms2_module_news_categories_seq` VALUES (1);
/*!40000 ALTER TABLE `cms2_module_news_categories_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_module_news_seq`
--

DROP TABLE IF EXISTS `cms2_module_news_seq`;
CREATE TABLE `cms2_module_news_seq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_module_news_seq`
--

LOCK TABLES `cms2_module_news_seq` WRITE;
/*!40000 ALTER TABLE `cms2_module_news_seq` DISABLE KEYS */;
INSERT INTO `cms2_module_news_seq` VALUES (1);
/*!40000 ALTER TABLE `cms2_module_news_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_module_templates`
--

DROP TABLE IF EXISTS `cms2_module_templates`;
CREATE TABLE `cms2_module_templates` (
  `module_name` varchar(200) default NULL,
  `template_name` varchar(200) default NULL,
  `content` longtext,
  `create_date` datetime default NULL,
  `modified_date` datetime default NULL,
  KEY `module_and_template` (`module_name`,`template_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_module_templates`
--

LOCK TABLES `cms2_module_templates` WRITE;
/*!40000 ALTER TABLE `cms2_module_templates` DISABLE KEYS */;
INSERT INTO `cms2_module_templates` VALUES ('News','displaysummary','<!-- Start News Display Template -->\n{foreach from=$items item=entry}\n<div class=\"NewsSummary\">\n\n{if $entry->formatpostdate}\n	<div class=\"NewsSummaryPostdate\">\n		{$entry->formatpostdate}\n	</div>\n{/if}\n\n<div class=\"NewsSummaryLink\">\n	{$entry->titlelink}\n</div>\n\n<div class=\"NewsSummaryCategory\">\n	{mod_lang string=\'category_label\'} {$entry->category}\n</div>\n\n{if $entry->author}\n	<div class=\"NewsSummaryAuthor\">\n		{mod_lang string=\'author_label\'} {$entry->author}\n	</div>\n{/if}\n\n{if $entry->summary}\n	<div class=\"NewsSummarySummary\">\n		{eval var=$entry->summary}\n	</div>\n\n	<div class=\"NewsSummaryMorelink\">\n		[{$entry->morelink}]\n	</div>\n\n{else if $entry->content}\n\n	<div class=\"NewsSummaryContent\">\n		{eval var=$entry->content}\n	</div>\n{/if}\n\n</div>\n{/foreach}\n<!-- End News Display Template -->','2007-07-23 16:02:51','2007-07-23 16:02:51'),('News','displaydetail','{if $entry->formatpostdate}\n	<div id=\"NewsPostDetailDate\">\n		{$entry->formatpostdate}\n	</div>\n{/if}\n<h3 id=\"NewsPostDetailTitle\">{$entry->title}</h3>\n\n<hr id=\"NewsPostDetailHorizRule\" />\n\n{if $entry->summary}\n	<div id=\"NewsPostDetailSummary\">\n		<strong>\n			{eval var=$entry->summary}\n		</strong>\n	</div>\n{/if}\n\n{if $entry->category}\n	<div id=\"NewsPostDetailCategory\">\n		{$category_label} {$entry->category}\n	</div>\n{/if}\n{if $entry->author}\n	<div id=\"NewsPostDetailAuthor\">\n		{$author_label} {$entry->author}\n	</div>\n{/if}\n\n<div id=\"NewsPostDetailContent\">\n	{eval var=$entry->content}\n</div>\n\n<div id=\"NewsPostDetailPrintLink\">\n	{$entry->printlink}\n</div>\n{if $return_url != \"\"}\n<div id=\"NewsPostDetailReturnLink\">{$return_url}</div>\n{/if}','2007-07-23 16:02:51','2007-07-23 16:02:51');
/*!40000 ALTER TABLE `cms2_module_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_modules`
--

DROP TABLE IF EXISTS `cms2_modules`;
CREATE TABLE `cms2_modules` (
  `module_name` varchar(255) default NULL,
  `status` varchar(255) default NULL,
  `version` varchar(255) default NULL,
  `admin_only` tinyint(4) default '0',
  `active` tinyint(4) default NULL,
  KEY `module_name` (`module_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_modules`
--

LOCK TABLES `cms2_modules` WRITE;
/*!40000 ALTER TABLE `cms2_modules` DISABLE KEYS */;
INSERT INTO `cms2_modules` VALUES ('MenuManager','installed','1.2',0,1),('News','installed','3.0',0,1);
/*!40000 ALTER TABLE `cms2_modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_multilanguage`
--

DROP TABLE IF EXISTS `cms2_multilanguage`;
CREATE TABLE `cms2_multilanguage` (
  `id` int(11) NOT NULL auto_increment,
  `module_name` varchar(25) default NULL,
  `content_type` varchar(25) default NULL,
  `object_id` int(11) default NULL,
  `property_name` varchar(100) default NULL,
  `language` varchar(5) default NULL,
  `content` longtext,
  `create_date` datetime default NULL,
  `modified_date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_multilanguage`
--

LOCK TABLES `cms2_multilanguage` WRITE;
/*!40000 ALTER TABLE `cms2_multilanguage` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms2_multilanguage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_permission_defns`
--

DROP TABLE IF EXISTS `cms2_permission_defns`;
CREATE TABLE `cms2_permission_defns` (
  `id` int(11) NOT NULL auto_increment,
  `module` varchar(50) default NULL,
  `extra_attr` varchar(50) default NULL,
  `name` varchar(50) default NULL,
  `hierarchical` tinyint(4) NOT NULL default '0',
  `table` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_permission_defns`
--

LOCK TABLES `cms2_permission_defns` WRITE;
/*!40000 ALTER TABLE `cms2_permission_defns` DISABLE KEYS */;
INSERT INTO `cms2_permission_defns` VALUES (3,'Core','Page','View',1,'content');
/*!40000 ALTER TABLE `cms2_permission_defns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_permissions`
--

DROP TABLE IF EXISTS `cms2_permissions`;
CREATE TABLE `cms2_permissions` (
  `id` int(11) NOT NULL auto_increment,
  `permission_name` varchar(255) default NULL,
  `permission_text` varchar(255) default NULL,
  `create_date` datetime default NULL,
  `modified_date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_permissions`
--

LOCK TABLES `cms2_permissions` WRITE;
/*!40000 ALTER TABLE `cms2_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms2_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_permissions_seq`
--

DROP TABLE IF EXISTS `cms2_permissions_seq`;
CREATE TABLE `cms2_permissions_seq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_permissions_seq`
--

LOCK TABLES `cms2_permissions_seq` WRITE;
/*!40000 ALTER TABLE `cms2_permissions_seq` DISABLE KEYS */;
INSERT INTO `cms2_permissions_seq` VALUES (2);
/*!40000 ALTER TABLE `cms2_permissions_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_serialized_versions`
--

DROP TABLE IF EXISTS `cms2_serialized_versions`;
CREATE TABLE `cms2_serialized_versions` (
  `id` int(11) NOT NULL auto_increment,
  `version` int(11) default NULL,
  `object_id` int(11) default NULL,
  `data` longblob,
  `type` varchar(255) default NULL,
  `create_date` datetime default NULL,
  `modified_date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_serialized_versions`
--

LOCK TABLES `cms2_serialized_versions` WRITE;
/*!40000 ALTER TABLE `cms2_serialized_versions` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms2_serialized_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_siteprefs`
--

DROP TABLE IF EXISTS `cms2_siteprefs`;
CREATE TABLE `cms2_siteprefs` (
  `sitepref_name` varchar(255) NOT NULL default '',
  `sitepref_value` text,
  `create_date` datetime default NULL,
  `modified_date` datetime default NULL,
  PRIMARY KEY  (`sitepref_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_siteprefs`
--

LOCK TABLES `cms2_siteprefs` WRITE;
/*!40000 ALTER TABLE `cms2_siteprefs` DISABLE KEYS */;
INSERT INTO `cms2_siteprefs` VALUES ('enablecustom404','0','2006-07-25 21:22:33','2006-07-25 21:22:33'),('custom404','<p>Page could not be found.</p>','2006-07-25 21:22:33','2006-07-25 21:22:33'),('custom404template','-1','2006-07-25 21:22:33','2006-07-25 21:22:33'),('enablesitedownmessage','0','2006-07-25 21:22:33','2006-07-25 21:22:33'),('sitedownmessage','<p>Site is currently down for maintenance.</p>','2006-07-25 21:22:33','2006-07-25 21:22:33'),('sitedownmessagetemplate','-1','2006-07-25 21:22:33','2006-07-25 21:22:33'),('useadvancedcss','1','2006-07-25 21:22:33','2006-07-25 21:22:33'),('metadata','<meta name=\"Generator\" content=\"CMS Made Simple - Copyright (C) 2004-6 Ted Kulp. All rights reserved.\" />\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\r\n ','2006-07-25 21:22:33','2006-07-25 21:22:33'),('xmlmodulerepository',NULL,'2006-07-25 21:22:33','2006-07-25 21:22:33'),('logintheme','default','2006-07-25 21:22:33','2006-07-25 21:22:33');
/*!40000 ALTER TABLE `cms2_siteprefs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_templates`
--

DROP TABLE IF EXISTS `cms2_templates`;
CREATE TABLE `cms2_templates` (
  `id` int(11) NOT NULL auto_increment,
  `template_name` varchar(255) default NULL,
  `template_content` longtext,
  `encoding` varchar(25) default NULL,
  `active` tinyint(4) default NULL,
  `default_template` tinyint(4) default NULL,
  `create_date` datetime default NULL,
  `modified_date` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `template_name` (`template_name`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_templates`
--

LOCK TABLES `cms2_templates` WRITE;
/*!40000 ALTER TABLE `cms2_templates` DISABLE KEYS */;
INSERT INTO `cms2_templates` VALUES (1,'Test','<html>\r\n<head>\r\n{header}\r\n</head>\r\n<body>\r\n<p>\r\n{menu collapse=1}\r\n</p>\r\n\r\n</p>\r\n{content}\r\n</p>\r\n\r\n<p>\r\n{content name=\"Content Block 2\"}\r\n</p>\r\n\r\n</body>\r\n</html>\r\n','',1,1,'2007-06-13 00:47:11','2007-07-21 23:03:16');
/*!40000 ALTER TABLE `cms2_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_user_groups`
--

DROP TABLE IF EXISTS `cms2_user_groups`;
CREATE TABLE `cms2_user_groups` (
  `group_id` int(11) default NULL,
  `user_id` int(11) default NULL,
  `create_date` datetime default NULL,
  `modified_date` datetime default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_user_groups`
--

LOCK TABLES `cms2_user_groups` WRITE;
/*!40000 ALTER TABLE `cms2_user_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms2_user_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_userplugins`
--

DROP TABLE IF EXISTS `cms2_userplugins`;
CREATE TABLE `cms2_userplugins` (
  `id` int(11) NOT NULL auto_increment,
  `userplugin_name` varchar(255) default NULL,
  `code` text,
  `create_date` datetime default NULL,
  `modified_date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_userplugins`
--

LOCK TABLES `cms2_userplugins` WRITE;
/*!40000 ALTER TABLE `cms2_userplugins` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms2_userplugins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_userprefs`
--

DROP TABLE IF EXISTS `cms2_userprefs`;
CREATE TABLE `cms2_userprefs` (
  `user_id` int(11) default NULL,
  `preference` varchar(50) default NULL,
  `value` text,
  `type` varchar(25) default NULL,
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_userprefs`
--

LOCK TABLES `cms2_userprefs` WRITE;
/*!40000 ALTER TABLE `cms2_userprefs` DISABLE KEYS */;
INSERT INTO `cms2_userprefs` VALUES (1,'collapse','1=1.2=1.4=1.7=1.13=1.15=1.0=1.11=1.',NULL);
/*!40000 ALTER TABLE `cms2_userprefs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_users`
--

DROP TABLE IF EXISTS `cms2_users`;
CREATE TABLE `cms2_users` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(25) default NULL,
  `password` varchar(40) default NULL,
  `admin_access` tinyint(4) default NULL,
  `first_name` varchar(50) default NULL,
  `last_name` varchar(50) default NULL,
  `email` varchar(255) default NULL,
  `active` tinyint(4) default NULL,
  `create_date` datetime default NULL,
  `modified_date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_users`
--

LOCK TABLES `cms2_users` WRITE;
/*!40000 ALTER TABLE `cms2_users` DISABLE KEYS */;
INSERT INTO `cms2_users` VALUES (1,'admin','21232f297a57a5a743894a0e4a801fc3',1,'','','',1,'2007-06-09 15:56:33','2007-06-09 15:56:33');
/*!40000 ALTER TABLE `cms2_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms2_version`
--

DROP TABLE IF EXISTS `cms2_version`;
CREATE TABLE `cms2_version` (
  `version` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cms2_version`
--

LOCK TABLES `cms2_version` WRITE;
/*!40000 ALTER TABLE `cms2_version` DISABLE KEYS */;
INSERT INTO `cms2_version` VALUES (27);
/*!40000 ALTER TABLE `cms2_version` ENABLE KEYS */;
UNLOCK TABLES;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

