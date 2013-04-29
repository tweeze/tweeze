-- MySQL dump 10.13  Distrib 5.5.25, for FreeBSD9.0 (i386)
--
-- Host: localhost    Database: suma1
-- ------------------------------------------------------
-- Server version	5.5.25

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
-- Table structure for table `parser_errors`
--

DROP TABLE IF EXISTS `parser_errors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parser_errors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `chunk` text CHARACTER SET utf8 NOT NULL COMMENT 'Chunks, from the twitter stream that are identified as one msg.',
  `error_type` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT 'Type of error',
  `error_message` text CHARACTER SET utf8 NOT NULL COMMENT 'Text of the error message',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table that store the raw chunks of twitter information';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parser_errors`
--

LOCK TABLES `parser_errors` WRITE;
/*!40000 ALTER TABLE `parser_errors` DISABLE KEYS */;
/*!40000 ALTER TABLE `parser_errors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wut_contributors`
--

DROP TABLE IF EXISTS `wut_contributors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wut_contributors` (
  `tweet_id` bigint(20) unsigned NOT NULL COMMENT 'The integer representation of the unique identifier for a Tweet.',
  `user_id` bigint(20) unsigned NOT NULL COMMENT 'The integer representation of the ID of the user who contributed to this Tweet.',
  `screen_name` varchar(30) CHARACTER SET utf8 NOT NULL COMMENT 'The screen name of the user who contributed to this Tweet.',
  PRIMARY KEY (`tweet_id`,`user_id`),
  KEY `user_id` (`user_id`),
  KEY `screen_name` (`screen_name`(15))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='An collection of brief user objects (usually only one) indicating users who cont';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wut_contributors`
--

LOCK TABLES `wut_contributors` WRITE;
/*!40000 ALTER TABLE `wut_contributors` DISABLE KEYS */;
/*!40000 ALTER TABLE `wut_contributors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wut_delete`
--

DROP TABLE IF EXISTS `wut_delete`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wut_delete` (
  `id` bigint(20) unsigned NOT NULL COMMENT 'id of tweet to be deleted',
  `user_id` bigint(20) unsigned NOT NULL COMMENT 'user_id',
  `executed` enum('false','true') CHARACTER SET utf8 NOT NULL DEFAULT 'false' COMMENT 'Has this deletion been executed?',
  PRIMARY KEY (`id`),
  KEY `executed` (`executed`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Status deletion notices (delete)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wut_delete`
--

LOCK TABLES `wut_delete` WRITE;
/*!40000 ALTER TABLE `wut_delete` DISABLE KEYS */;
/*!40000 ALTER TABLE `wut_delete` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wut_follow`
--

DROP TABLE IF EXISTS `wut_follow`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wut_follow` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Track ID',
  `follow` bigint(20) unsigned NOT NULL COMMENT '5000 users can be active at the same time',
  `begin` datetime NOT NULL COMMENT 'Datetime when to start the search for the track',
  `end` datetime NOT NULL COMMENT 'Datetime when to end the search for the track',
  `authorized_by` int(10) unsigned NOT NULL DEFAULT '1' COMMENT 'Webuser, who entered the search',
  PRIMARY KEY (`ID`),
  KEY `track` (`follow`,`begin`,`end`,`authorized_by`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table that stores twitter users to be searched.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wut_follow`
--

LOCK TABLES `wut_follow` WRITE;
/*!40000 ALTER TABLE `wut_follow` DISABLE KEYS */;
/*!40000 ALTER TABLE `wut_follow` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wut_geo_objects`
--

DROP TABLE IF EXISTS `wut_geo_objects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wut_geo_objects` (
  `tweet_id` bigint(20) unsigned NOT NULL COMMENT 'Id of tweet',
  `type` enum('Point','MultiPoint','LineString','MultiLineString','Polygon','MultiPolygon','GeometryCollection') CHARACTER SET utf8 NOT NULL DEFAULT 'Point' COMMENT 'Type of geo object',
  PRIMARY KEY (`tweet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Represents the geographic location of a Tweet as reported';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wut_geo_objects`
--

LOCK TABLES `wut_geo_objects` WRITE;
/*!40000 ALTER TABLE `wut_geo_objects` DISABLE KEYS */;
/*!40000 ALTER TABLE `wut_geo_objects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wut_geo_objects_coordinates`
--

DROP TABLE IF EXISTS `wut_geo_objects_coordinates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wut_geo_objects_coordinates` (
  `tweet_id` bigint(20) unsigned NOT NULL COMMENT 'Id of tweet',
  `index_of` tinyint(3) unsigned NOT NULL COMMENT 'Index of the coordinates from the tweets coordinates array',
  `longitude` double NOT NULL COMMENT 'As Decimal Degree',
  `latitude` double NOT NULL COMMENT 'As Decimal Degree',
  PRIMARY KEY (`tweet_id`,`index_of`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='coordinates of a tweet object';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wut_geo_objects_coordinates`
--

LOCK TABLES `wut_geo_objects_coordinates` WRITE;
/*!40000 ALTER TABLE `wut_geo_objects_coordinates` DISABLE KEYS */;
/*!40000 ALTER TABLE `wut_geo_objects_coordinates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wut_hashtags`
--

DROP TABLE IF EXISTS `wut_hashtags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wut_hashtags` (
  `tweet_id` bigint(20) unsigned NOT NULL COMMENT 'Id of tweet',
  `index_of` tinyint(3) unsigned NOT NULL COMMENT 'Index of from the tweets hashtags array',
  `x1` tinyint(3) unsigned NOT NULL COMMENT 'represents the location of the # character in the Tweet text string',
  `x2` tinyint(3) unsigned NOT NULL COMMENT 'represents the location of the first character after the hashtag. Therefore the difference between the x1 and x2 will be the length of the hashtag name plus one (for the ''#'' character).',
  `text` varchar(139) CHARACTER SET utf8 NOT NULL COMMENT 'Name of the hashtag, minus the leading ''#'' character.',
  PRIMARY KEY (`tweet_id`,`index_of`),
  KEY `text` (`text`(20))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='hashtags found in a tweet';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wut_hashtags`
--

LOCK TABLES `wut_hashtags` WRITE;
/*!40000 ALTER TABLE `wut_hashtags` DISABLE KEYS */;
/*!40000 ALTER TABLE `wut_hashtags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wut_http_statuscodes`
--

DROP TABLE IF EXISTS `wut_http_statuscodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wut_http_statuscodes` (
  `value` smallint(5) unsigned NOT NULL,
  `description` varchar(255) CHARACTER SET utf8 NOT NULL,
  `reference` varchar(255) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Source: http://www.iana.org/assignments/http-status-codes/http-status-codes.xml';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wut_http_statuscodes`
--

LOCK TABLES `wut_http_statuscodes` WRITE;
/*!40000 ALTER TABLE `wut_http_statuscodes` DISABLE KEYS */;
/*!40000 ALTER TABLE `wut_http_statuscodes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wut_limit`
--

DROP TABLE IF EXISTS `wut_limit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wut_limit` (
  `time_of_limit_hit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp when the Limit occured',
  `track` int(10) unsigned NOT NULL COMMENT 'total count of the number of undelivered Tweets since the connection was opened',
  `processed` enum('false','true') CHARACTER SET utf8 NOT NULL DEFAULT 'false' COMMENT 'has this limit been processed',
  PRIMARY KEY (`time_of_limit_hit`),
  KEY `processed` (`processed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Limit notices, get a search on the search-API for that time';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wut_limit`
--

LOCK TABLES `wut_limit` WRITE;
/*!40000 ALTER TABLE `wut_limit` DISABLE KEYS */;
/*!40000 ALTER TABLE `wut_limit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wut_locations`
--

DROP TABLE IF EXISTS `wut_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wut_locations` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Track ID',
  `name` varchar(128) CHARACTER SET utf8 NOT NULL COMMENT 'Name unter dem die Boundingbox gespeichert wird',
  `SW` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT 'longitude,latitude pair / south-west corner of the Boundingbox',
  `NE` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT 'longitude,latitude pair / north-east corner of the BoundingBox',
  `begin` datetime NOT NULL COMMENT 'Datetime when to start the search for the track',
  `end` datetime NOT NULL COMMENT 'Datetime when to end the search for the track',
  `authorized_by` int(10) unsigned NOT NULL DEFAULT '1' COMMENT 'Webuser, who entered the search',
  PRIMARY KEY (`ID`),
  KEY `track` (`SW`,`begin`,`end`,`authorized_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table that stores bounding boxes to be searched.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wut_locations`
--

LOCK TABLES `wut_locations` WRITE;
/*!40000 ALTER TABLE `wut_locations` DISABLE KEYS */;
/*!40000 ALTER TABLE `wut_locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wut_media`
--

DROP TABLE IF EXISTS `wut_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wut_media` (
  `id` bigint(20) unsigned NOT NULL COMMENT 'ID of the media expressed as a 64-bit integer',
  `display_url` text CHARACTER SET utf8 NOT NULL COMMENT 'URL of the media to display to clients',
  `expanded_url` text CHARACTER SET utf8 NOT NULL COMMENT 'An expanded version of display_url. Links to the media display page',
  `x1` tinyint(3) unsigned NOT NULL COMMENT 'represents the location of the first character of the URL in the Tweet text',
  `x2` tinyint(3) unsigned NOT NULL COMMENT 'represents the location of the first non-URL character occurring after the URL (or the end of the string if the URL is the last part of the Tweet text)',
  `media_url` text CHARACTER SET utf8 NOT NULL COMMENT 'An http:// URL pointing directly to the uploaded media file.',
  `media_url_https` text CHARACTER SET utf8 NOT NULL COMMENT 'An https:// URL pointing directly to the uploaded media file, for embedding on https pages.',
  `source_status_id` bigint(20) unsigned DEFAULT NULL COMMENT 'For Tweets containing media that was originally associated with a different tweet, this ID points to the original Tweet.',
  `type` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT 'Type of uploaded media.',
  `url` text CHARACTER SET utf8 NOT NULL COMMENT 'Wrapped URL for the media link. This corresponds with the URL embedded directly into the raw Tweet text, and the values for the indices parameter.',
  PRIMARY KEY (`id`),
  KEY `source_status_id` (`source_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Represents media elements uploaded with the Tweet';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wut_media`
--

LOCK TABLES `wut_media` WRITE;
/*!40000 ALTER TABLE `wut_media` DISABLE KEYS */;
/*!40000 ALTER TABLE `wut_media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wut_media_sizes`
--

DROP TABLE IF EXISTS `wut_media_sizes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wut_media_sizes` (
  `media_id` bigint(20) unsigned NOT NULL COMMENT 'ID of the media expressed as a 64-bit integer',
  `size` enum('thumb','large','medium','small') CHARACTER SET utf8 NOT NULL COMMENT 'type of the size',
  `h` smallint(5) unsigned NOT NULL COMMENT 'Height in pixels of this size.',
  `w` smallint(5) unsigned NOT NULL COMMENT 'Width in pixels of this size.',
  `resize` varchar(15) CHARACTER SET utf8 NOT NULL COMMENT 'Resizing method used to obtain this size. A value of fit means that the media was resized to fit one dimension, keeping its native aspect ratio. A value of crop means that the media was cropped in order to fit a specific resolution.',
  PRIMARY KEY (`media_id`,`size`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='available sizes for the media file';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wut_media_sizes`
--

LOCK TABLES `wut_media_sizes` WRITE;
/*!40000 ALTER TABLE `wut_media_sizes` DISABLE KEYS */;
/*!40000 ALTER TABLE `wut_media_sizes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wut_parser_errors`
--

DROP TABLE IF EXISTS `wut_parser_errors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wut_parser_errors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `chunk` text CHARACTER SET utf8 NOT NULL COMMENT 'Chunks, from the twitter stream that are identified as one msg.',
  `error_type` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT 'Type of error',
  `error_message` text CHARACTER SET utf8 NOT NULL COMMENT 'Text of the error message',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table that store the raw chunks of twitter information';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wut_parser_errors`
--

LOCK TABLES `wut_parser_errors` WRITE;
/*!40000 ALTER TABLE `wut_parser_errors` DISABLE KEYS */;
/*!40000 ALTER TABLE `wut_parser_errors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wut_places`
--

DROP TABLE IF EXISTS `wut_places`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wut_places` (
  `id` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT 'ID representing this place. Note that this is represented as a string, not an integer.',
  `attributes` text CHARACTER SET utf8 COMMENT 'JSON object. Contains a hash of variant information about the place. See About Geo Place Attributes.',
  `bounding_box` enum('Point','MultiPoint','LineString','MultiLineString','Polygon','MultiPolygon','GeometryCollection') CHARACTER SET utf8 DEFAULT NULL COMMENT 'A bounding box of coordinates which encloses this place.',
  `country` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Name of the country containing this place.',
  `country_code` varchar(5) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Shortened country code representing the country containing this place.',
  `full_name` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Full human-readable representation of the place''s name.',
  `name` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Short human-readable representation of the place''s name.',
  `place_type` varchar(80) CHARACTER SET utf8 DEFAULT NULL COMMENT 'The type of location represented by this place.',
  `url` text CHARACTER SET utf8 COMMENT 'URL representing the location of additional place metadata for this place.',
  PRIMARY KEY (`id`),
  KEY `full_name` (`full_name`(20))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Places are specific, named locations with corresponding geo coordinates.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wut_places`
--

LOCK TABLES `wut_places` WRITE;
/*!40000 ALTER TABLE `wut_places` DISABLE KEYS */;
/*!40000 ALTER TABLE `wut_places` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wut_places_bounding_box_coordinates`
--

DROP TABLE IF EXISTS `wut_places_bounding_box_coordinates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wut_places_bounding_box_coordinates` (
  `place_id` varchar(32) NOT NULL COMMENT 'Id of place',
  `index_of` tinyint(3) unsigned NOT NULL COMMENT 'Index of the coordinates from the places.bounding_box coordinates array',
  `longitude` double NOT NULL COMMENT 'As Decimal Degree',
  `latitude` double NOT NULL COMMENT 'As Decimal Degree',
  PRIMARY KEY (`place_id`,`index_of`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='coordinates of a tweet object';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wut_places_bounding_box_coordinates`
--

LOCK TABLES `wut_places_bounding_box_coordinates` WRITE;
/*!40000 ALTER TABLE `wut_places_bounding_box_coordinates` DISABLE KEYS */;
/*!40000 ALTER TABLE `wut_places_bounding_box_coordinates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wut_scrub_geo`
--

DROP TABLE IF EXISTS `wut_scrub_geo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wut_scrub_geo` (
  `user_id` bigint(20) unsigned NOT NULL COMMENT 'user_id of the parrent user',
  `up_to_status_id` bigint(20) unsigned NOT NULL COMMENT 'latest tweet_id for which geo information must be stripped',
  `executed` enum('false','true') CHARACTER SET utf8 NOT NULL DEFAULT 'false' COMMENT 'has this notice been processed?',
  PRIMARY KEY (`user_id`,`up_to_status_id`),
  KEY `executed` (`executed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Location deletion notices';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wut_scrub_geo`
--

LOCK TABLES `wut_scrub_geo` WRITE;
/*!40000 ALTER TABLE `wut_scrub_geo` DISABLE KEYS */;
/*!40000 ALTER TABLE `wut_scrub_geo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wut_status_withheld`
--

DROP TABLE IF EXISTS `wut_status_withheld`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wut_status_withheld` (
  `tweet_id` bigint(20) unsigned NOT NULL COMMENT 'indicating the status ID',
  `user_id` bigint(20) unsigned NOT NULL COMMENT 'indicating the user',
  `withheld_in_countries` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT 'a collection of withheld_in_countries two-letter country codes',
  `processed` enum('false','true') CHARACTER SET utf8 NOT NULL DEFAULT 'false' COMMENT 'has this notice been processed',
  PRIMARY KEY (`tweet_id`,`user_id`),
  KEY `processed` (`processed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='These messages indicate that either the indicated tweet or indicated user has ha';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wut_status_withheld`
--

LOCK TABLES `wut_status_withheld` WRITE;
/*!40000 ALTER TABLE `wut_status_withheld` DISABLE KEYS */;
/*!40000 ALTER TABLE `wut_status_withheld` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wut_stream_statistics`
--

DROP TABLE IF EXISTS `wut_stream_statistics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wut_stream_statistics` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `starttime` datetime NOT NULL,
  `endtime` datetime NOT NULL,
  `affRows` int(10) unsigned NOT NULL,
  `deleteCount` int(10) unsigned NOT NULL,
  `emitted` int(10) unsigned NOT NULL,
  `errCount` int(10) unsigned NOT NULL,
  `limitCount` int(10) unsigned NOT NULL,
  `scrubGeoCount` int(10) unsigned NOT NULL,
  `statusWithheldCount` int(10) unsigned NOT NULL,
  `totalProcessed` int(10) unsigned NOT NULL,
  `tweetCount` int(10) unsigned NOT NULL,
  `userWithheldCount` int(10) unsigned NOT NULL,
  `warningCount` int(10) unsigned NOT NULL,
  `stuffToTrack` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Logs statistics for every run and keywordchange';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wut_stream_statistics`
--

LOCK TABLES `wut_stream_statistics` WRITE;
/*!40000 ALTER TABLE `wut_stream_statistics` DISABLE KEYS */;
/*!40000 ALTER TABLE `wut_stream_statistics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wut_symbols`
--

DROP TABLE IF EXISTS `wut_symbols`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wut_symbols` (
  `tweet_id` bigint(20) unsigned NOT NULL COMMENT 'Id of tweet',
  `index_of` tinyint(3) unsigned NOT NULL COMMENT 'Index of from the tweets symbols array',
  `x1` tinyint(3) unsigned NOT NULL COMMENT 'represents the location of the $ character in the Tweet text string',
  `x2` tinyint(3) unsigned NOT NULL COMMENT 'represents the location of the first character after the symbol. Therefore the difference between the x1 and x2 will be the length of the symbol name plus one (for the ''$'' character).',
  `text` varchar(139) CHARACTER SET utf8 NOT NULL COMMENT 'Name of the symbol, minus the leading ''$'' character. REGEX pattern is \\$[a-z]{1,6}([._][a-z]{1,2})? over the lower cased text of the Tweet',
  PRIMARY KEY (`tweet_id`,`index_of`),
  KEY `text` (`text`(20))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Twitter auto-links financial symbols (which look like $FOO)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wut_symbols`
--

LOCK TABLES `wut_symbols` WRITE;
/*!40000 ALTER TABLE `wut_symbols` DISABLE KEYS */;
/*!40000 ALTER TABLE `wut_symbols` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wut_track`
--

DROP TABLE IF EXISTS `wut_track`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wut_track` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Track ID',
  `track` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '400 tracks can be active at the same time',
  `begin` datetime NOT NULL COMMENT 'Datetime when to start the search for the track. Localtime on Client will be converted to ServerTime UTC.',
  `end` datetime NOT NULL COMMENT 'Datetime when to end the search for the track. Localtime on Client will be converted to ServerTime UTC.',
  `authorized_by` int(10) unsigned NOT NULL DEFAULT '1' COMMENT 'Webuser, who entered the search',
  PRIMARY KEY (`ID`),
  KEY `track` (`track`(191),`begin`,`end`,`authorized_by`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table that stores keywords and hashtags to be searched.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wut_track`
--

LOCK TABLES `wut_track` WRITE;
/*!40000 ALTER TABLE `wut_track` DISABLE KEYS */;
/*!40000 ALTER TABLE `wut_track` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wut_tweet_media`
--

DROP TABLE IF EXISTS `wut_tweet_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wut_tweet_media` (
  `tweet_id` bigint(20) unsigned NOT NULL COMMENT 'id of parrent tweet',
  `index_of` tinyint(3) unsigned NOT NULL COMMENT 'Index of from the tweets media array',
  `media_id` bigint(20) unsigned NOT NULL COMMENT 'ID of the media expressed as a 64-bit integer',
  PRIMARY KEY (`tweet_id`,`index_of`,`media_id`),
  KEY `media_id` (`media_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wut_tweet_media`
--

LOCK TABLES `wut_tweet_media` WRITE;
/*!40000 ALTER TABLE `wut_tweet_media` DISABLE KEYS */;
/*!40000 ALTER TABLE `wut_tweet_media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wut_tweets`
--

DROP TABLE IF EXISTS `wut_tweets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wut_tweets` (
  `id` bigint(20) unsigned NOT NULL COMMENT 'The integer representation of the unique identifier for this Tweet. This number is greater than 53 bits and some programming languages may have difficulty/silent defects in interpreting it.',
  `annotations` text CHARACTER SET utf8 COMMENT 'At time of development. This feature is not implemented by twitter. Will be stored as JSON-object here, if implemented',
  `created_at` datetime NOT NULL COMMENT 'UTC time when this Tweet was created.',
  `current_user_retweet` bigint(20) unsigned DEFAULT NULL COMMENT 'Perspectival. Only surfaces on methods supporting the include_my_retweet parameter, when set to true. Details the Tweet ID of the user''s own retweet (if existent) of this Tweet.',
  `favorite_count` int(10) unsigned DEFAULT NULL COMMENT 'Nullable. Indicates approximately how many times this Tweet has been "favorited" by Twitter users.',
  `favorited` enum('false','true') CHARACTER SET utf8 DEFAULT NULL COMMENT 'Nullable. Perspectival. Indicates whether this Tweet has been favorited by the authenticating user.',
  `filter_level` enum('none','low','medium','high') CHARACTER SET utf8 DEFAULT NULL COMMENT 'Indicates the maximum value of the filter_level parameter which may be used and still stream this Tweet. So a value of medium will be streamed on none, low, and medium streams. https://dev.twitter.com/docs/streaming-apis/parameters#filter_level  https://dev.twitter.com/blog/introducing-new-metadata-for-tweets',
  `geo` text CHARACTER SET utf8 COMMENT 'Deprecated. Nullable. Use the "coordinates" field instead. If still used will be JSON-object.',
  `in_reply_to_screen_name` varchar(60) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Nullable. If the represented Tweet is a reply, this field will contain the screen name of the original Tweet''s author.',
  `in_reply_to_status_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Nullable. If the represented Tweet is a reply, this field will contain the integer representation of the original Tweet''s ID.',
  `in_reply_to_user_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Nullable. If the represented Tweet is a reply, this field will contain the integer representation of the original Tweet''s author ID.',
  `lang` varchar(9) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Nullable. When present, indicates a BCP 47 language identifier corresponding to the machine-detected language of the Tweet text, or "und" if no language could be detected.',
  `place` varchar(32) CHARACTER SET utf8 DEFAULT NULL COMMENT 'string, not an integer! Nullable. When present, indicates that the tweet is associated (but not necessarily originating from) a Place.',
  `possibly_sensitive` enum('false','true') CHARACTER SET utf8 DEFAULT NULL COMMENT 'Nullable. This field only surfaces when a tweet contains a link. The meaning of the field doesn''t pertain to the tweet content itself, but instead it is an indicator that the URL contained in the tweet may contain content or media identified as sensitive content.',
  `possibly_sensitive_editable` enum('false','true') CHARACTER SET utf8 DEFAULT NULL,
  `scopes` text CHARACTER SET utf8 COMMENT 'JSON-object. A set of key-value pairs indicating the intended contextual delivery of the containing Tweet. Currently used by Twitter''s Promoted Products.',
  `retweet_count` int(10) unsigned DEFAULT NULL COMMENT 'Number of times this Tweet has been retweeted. This field is no longer capped at 99 and will not turn into a String for "100+"',
  `retweeted` enum('false','true') CHARACTER SET utf8 DEFAULT NULL COMMENT 'Perspectival. Indicates whether this Tweet has been retweeted by the authenticating user.',
  `source` text CHARACTER SET utf8 COMMENT 'Utility used to post the Tweet, as an HTML-formatted string. Tweets from the Twitter website have a source value of web.',
  `text` text CHARACTER SET utf8 NOT NULL COMMENT 'The actual UTF-8 text of the status update. See twitter-text for details on what is currently considered valid characters.',
  `truncated` enum('false','true') CHARACTER SET utf8 DEFAULT NULL COMMENT 'Indicates whether the value of the text parameter was truncated, for example, as a result of a retweet exceeding the 140 character Tweet length. Truncated text will end in ellipsis, like this ...',
  `user` bigint(20) unsigned NOT NULL COMMENT 'The user who posted this Tweet.',
  `withheld_copyright` enum('false','true') CHARACTER SET utf8 DEFAULT NULL COMMENT 'When present and set to "true", it indicates that this piece of content has been withheld due to a DMCA complaint',
  `withheld_in_countries` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT 'When present, indicates a textual representation of the two-letter country codes this content is withheld from.',
  `withheld_scope` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT 'When present, indicates whether the content being withheld is the "status" or a "user."',
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Here come the tweets';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wut_tweets`
--

LOCK TABLES `wut_tweets` WRITE;
/*!40000 ALTER TABLE `wut_tweets` DISABLE KEYS */;
/*!40000 ALTER TABLE `wut_tweets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wut_unknown_tweet_objs`
--

DROP TABLE IF EXISTS `wut_unknown_tweet_objs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wut_unknown_tweet_objs` (
  `tweet_id` bigint(20) unsigned NOT NULL COMMENT 'tweet_id of the parrent tweet',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'timestamp of the creation of the parrent tweet',
  `unknown` text CHARACTER SET utf8 NOT NULL COMMENT 'field with a JSON array of unknown tweet objects and attributes',
  PRIMARY KEY (`tweet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='table to store not recognized tweet attributes';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wut_unknown_tweet_objs`
--

LOCK TABLES `wut_unknown_tweet_objs` WRITE;
/*!40000 ALTER TABLE `wut_unknown_tweet_objs` DISABLE KEYS */;
/*!40000 ALTER TABLE `wut_unknown_tweet_objs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wut_unknown_user_objs`
--

DROP TABLE IF EXISTS `wut_unknown_user_objs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wut_unknown_user_objs` (
  `user_id` bigint(20) unsigned NOT NULL COMMENT 'user_id of the parrent user',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'timestamp of user creation',
  `unknown` text CHARACTER SET utf8 NOT NULL COMMENT 'field with a JSON array of unknown user objects and attributes',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='table to store not recognized user attributes, for future upgrades and debugging';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wut_unknown_user_objs`
--

LOCK TABLES `wut_unknown_user_objs` WRITE;
/*!40000 ALTER TABLE `wut_unknown_user_objs` DISABLE KEYS */;
/*!40000 ALTER TABLE `wut_unknown_user_objs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wut_urls`
--

DROP TABLE IF EXISTS `wut_urls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wut_urls` (
  `tweet_id` bigint(20) unsigned NOT NULL COMMENT 'Id of tweet',
  `index_of` tinyint(3) unsigned NOT NULL COMMENT 'Index of from the tweets url array',
  `display_url` text CHARACTER SET utf8 NOT NULL COMMENT 'Version of the URL to display to clients.',
  `expanded_url` text CHARACTER SET utf8 NOT NULL COMMENT 'Expanded version of display_url.',
  `x1` tinyint(3) unsigned NOT NULL COMMENT 'represents the location of the first character of the URL in the Tweet text',
  `x2` tinyint(3) unsigned NOT NULL COMMENT 'represents the location of the first non-URL character after the end of the URL',
  `url` text CHARACTER SET utf8 NOT NULL COMMENT 'The t.co wrapped URL, corresponding to the value embedded directly into the raw Tweet text',
  PRIMARY KEY (`tweet_id`,`index_of`),
  KEY `expanded_url` (`expanded_url`(25))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Represents URLs included in the text of the Tweet.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wut_urls`
--

LOCK TABLES `wut_urls` WRITE;
/*!40000 ALTER TABLE `wut_urls` DISABLE KEYS */;
/*!40000 ALTER TABLE `wut_urls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wut_urls_resolved`
--

DROP TABLE IF EXISTS `wut_urls_resolved`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wut_urls_resolved` (
  `tweet_id` bigint(20) unsigned NOT NULL COMMENT 'id des tweets',
  `index_of` tinyint(3) unsigned NOT NULL COMMENT 'nth URL in tweet',
  `resolve_index_of` tinyint(3) unsigned NOT NULL COMMENT 'nth resolve of resulting URL e.g. fb.me -> bit.ly -> tinyurl.com -> final Real URL',
  `expanded_url` text CHARACTER SET utf8 NOT NULL COMMENT 'url to resolve',
  `resolved` enum('false','true') CHARACTER SET utf8 NOT NULL DEFAULT 'false' COMMENT 'is this chain completely resolved?',
  `statuscode` smallint(5) unsigned DEFAULT NULL COMMENT 'HTTP status codes, when URI was processed',
  `httpVersion` varchar(5) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Server Protocol of the response',
  `contentType` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Content type of the response',
  `date` datetime DEFAULT NULL COMMENT 'Date the responding server send back',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Timestamp of this server',
  PRIMARY KEY (`tweet_id`,`index_of`,`resolve_index_of`),
  KEY `resolved` (`resolved`),
  KEY `date` (`date`),
  KEY `timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Storing expanded URLs after Twitters own expansion';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wut_urls_resolved`
--

LOCK TABLES `wut_urls_resolved` WRITE;
/*!40000 ALTER TABLE `wut_urls_resolved` DISABLE KEYS */;
/*!40000 ALTER TABLE `wut_urls_resolved` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wut_user_mentions`
--

DROP TABLE IF EXISTS `wut_user_mentions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wut_user_mentions` (
  `tweet_id` bigint(20) unsigned NOT NULL COMMENT 'Id of tweet',
  `index_of` tinyint(3) unsigned NOT NULL COMMENT 'Index of from the tweets user_mentions array',
  `id` bigint(20) unsigned NOT NULL COMMENT 'ID of the mentioned user, as an integer.',
  `x1` tinyint(4) unsigned NOT NULL COMMENT 'represents the location of the ''@'' character of the user mention',
  `x2` tinyint(4) unsigned NOT NULL COMMENT 'represents the location of the first non-screenname character following the user mention',
  `name` varchar(40) CHARACTER SET utf8 NOT NULL COMMENT 'Display name of the referenced user.',
  `screen_name` varchar(30) CHARACTER SET utf8 NOT NULL COMMENT 'Screen name of the referenced user.',
  PRIMARY KEY (`tweet_id`,`index_of`),
  KEY `id` (`id`),
  KEY `name` (`name`(20)),
  KEY `screen_name` (`screen_name`(15))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Represents other Twitter users mentioned in the text of the Tweet.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wut_user_mentions`
--

LOCK TABLES `wut_user_mentions` WRITE;
/*!40000 ALTER TABLE `wut_user_mentions` DISABLE KEYS */;
/*!40000 ALTER TABLE `wut_user_mentions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wut_user_withheld`
--

DROP TABLE IF EXISTS `wut_user_withheld`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wut_user_withheld` (
  `user_id` int(10) unsigned NOT NULL COMMENT 'indicating the user ID',
  `withheld_in_countries` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT 'a collection of withheld_in_countries two-letter country codes',
  `processed` enum('false','true') CHARACTER SET utf8 NOT NULL DEFAULT 'false' COMMENT 'has this notice been processed?',
  PRIMARY KEY (`user_id`),
  KEY `processed` (`processed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='These messages indicate that either the indicated tweet or indicated user has ha';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wut_user_withheld`
--

LOCK TABLES `wut_user_withheld` WRITE;
/*!40000 ALTER TABLE `wut_user_withheld` DISABLE KEYS */;
/*!40000 ALTER TABLE `wut_user_withheld` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wut_users`
--

DROP TABLE IF EXISTS `wut_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wut_users` (
  `id` bigint(20) unsigned NOT NULL COMMENT 'The integer representation of the unique identifier for this User. This number is greater than 53 bits and some programming languages may have difficulty/silent defects in interpreting it.',
  `contributors_enabled` enum('false','true') CHARACTER SET utf8 DEFAULT NULL COMMENT 'Indicates that the user has an account with "contributor mode" enabled, allowing for Tweets issued by the user to be co-authored by another account. Rarely true.',
  `created_at` datetime DEFAULT NULL COMMENT 'The UTC datetime that the user account was created on Twitter.',
  `default_profile` enum('false','true') CHARACTER SET utf8 DEFAULT NULL COMMENT 'When true, indicates that the user has not altered the theme or background of their user profile.',
  `default_profile_image` enum('false','true') CHARACTER SET utf8 DEFAULT NULL COMMENT 'When true, indicates that the user has not uploaded their own avatar and a default egg avatar is used instead.',
  `description` varchar(160) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Nullable. The user-defined UTF-8 string describing their account.',
  `favourites_count` int(10) unsigned DEFAULT NULL COMMENT 'The number of tweets this user has favorited in the account''s lifetime.',
  `follow_request_sent` enum('false','true') CHARACTER SET utf8 DEFAULT NULL COMMENT 'Nullable. Perspectival. When true, indicates that the authenticating user has issued a follow request to this protected user account.',
  `following` enum('false','true') CHARACTER SET utf8 DEFAULT NULL COMMENT 'Nullable. Perspectival. Deprecated. When true, indicates that the authenticating user is following this user. Some false negatives are possible when set to "false," but these false negatives are increasingly being represented as "null" instead.',
  `followers_count` int(10) unsigned DEFAULT NULL COMMENT 'The number of followers this account currently has. Under certain conditions of duress, this field will temporarily indicate "0."',
  `friends_count` int(10) unsigned DEFAULT NULL COMMENT 'The number of users this account is following (AKA their "followings"). Under certain conditions of duress, this field will temporarily indicate "0."',
  `geo_enabled` enum('false','true') CHARACTER SET utf8 DEFAULT NULL COMMENT 'When true, indicates that the user has enabled the possibility of geotagging their Tweets.',
  `is_translator` enum('false','true') CHARACTER SET utf8 DEFAULT NULL COMMENT 'When true, indicates that the user is a participant in Twitter''s translator community',
  `lang` varchar(2) CHARACTER SET utf8 DEFAULT NULL COMMENT 'The ISO 639-1 two-letter character code for the user''s self-declared user interface language. May or may not have anything to do with the content of their Tweets.',
  `listed_count` int(10) unsigned DEFAULT NULL COMMENT 'The number of public lists that this user is a member of.',
  `location` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Nullable. The user-defined location for this account''s profile. Not necessarily a location nor parseable. This field will occasionally be fuzzily interpreted by the Search service.',
  `name` varchar(40) CHARACTER SET utf8 NOT NULL COMMENT 'The name of the user, as they''ve defined it. Not necessarily a person''s name. Typically capped at 20 characters, but subject to change.',
  `notifications` enum('false','true') CHARACTER SET utf8 DEFAULT NULL COMMENT 'Nullable. Deprecated. May incorrectly report "false" at times. Indicates whether the authenticated user has chosen to receive this user''s tweets by SMS.',
  `profile_background_color` varchar(6) CHARACTER SET utf8 DEFAULT NULL COMMENT 'The hexadecimal color chosen by the user for their background.',
  `profile_background_image_url` text CHARACTER SET utf8 COMMENT 'A HTTP-based URL pointing to the background image the user has uploaded for their profile.',
  `profile_background_image_url_https` text CHARACTER SET utf8 COMMENT 'A HTTPS-based URL pointing to the background image the user has uploaded for their profile.',
  `profile_background_tile` enum('false','true') CHARACTER SET utf8 DEFAULT NULL COMMENT 'When true, indicates that the user''s profile_background_image_url should be tiled when displayed.',
  `profile_banner_url` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Undocumented field in Twitter API',
  `profile_image_url` text CHARACTER SET utf8 COMMENT 'A HTTP-based URL pointing to the user''s avatar image.',
  `profile_image_url_https` text CHARACTER SET utf8 COMMENT 'A HTTPS-based URL pointing to the user''s avatar image.',
  `profile_link_color` varchar(6) CHARACTER SET utf8 DEFAULT NULL COMMENT 'The hexadecimal color the user has chosen to display links with in their Twitter UI.',
  `profile_sidebar_border_color` varchar(6) CHARACTER SET utf8 DEFAULT NULL COMMENT 'The hexadecimal color the user has chosen to display sidebar borders with in their Twitter UI.',
  `profile_sidebar_fill_color` varchar(6) CHARACTER SET utf8 DEFAULT NULL COMMENT 'The hexadecimal color the user has chosen to display sidebar backgrounds with in their Twitter UI.',
  `profile_text_color` varchar(6) CHARACTER SET utf8 DEFAULT NULL COMMENT 'The hexadecimal color the user has chosen to display text with in their Twitter UI.',
  `profile_use_background_image` enum('false','true') CHARACTER SET utf8 DEFAULT NULL COMMENT 'When true, indicates the user wants their uploaded background image to be used.',
  `protected` enum('false','true') CHARACTER SET utf8 DEFAULT NULL COMMENT 'When true, indicates that this user has chosen to protect their Tweets.',
  `screen_name` varchar(30) CHARACTER SET utf8 NOT NULL COMMENT 'The screen name, handle, or alias that this user identifies themselves with. screen_names are unique but subject to change. Use id_str as a user identifier whenever possible. Typically a maximum of 15 characters long, but some historical accounts may exist with longer names.',
  `show_all_inline_media` enum('false','true') CHARACTER SET utf8 DEFAULT NULL COMMENT 'Indicates that the user would like to see media inline. Somewhat disused.',
  `status` bigint(20) unsigned DEFAULT NULL COMMENT 'Nullable. Last Tweet_id. If possible, the user''s most recent tweet or retweet. In some circumstances, this data cannot be provided and this field will be omitted, null, or empty.',
  `statuses_count` int(10) unsigned DEFAULT NULL COMMENT 'The number of tweets (including retweets) issued by the user.',
  `time_zone` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Nullable. A string describing the Time Zone this user declares themselves within.',
  `url` text CHARACTER SET utf8 COMMENT 'Nullable. A URL provided by the user in association with their profile.',
  `utc_offset` int(10) unsigned DEFAULT NULL COMMENT 'Nullable. The offset from GMT/UTC in seconds.',
  `verified` enum('false','true') CHARACTER SET utf8 DEFAULT NULL COMMENT 'When true, indicates that the user has a verified account.',
  `withheld_in_countries` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT 'When present, indicates a textual representation of the two-letter country codes this user is withheld from.',
  `withheld_scope` enum('status','user') CHARACTER SET utf8 DEFAULT NULL COMMENT 'When present, indicates whether the content being withheld is the "status" or a "user." ',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Twitter user data';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wut_users`
--

LOCK TABLES `wut_users` WRITE;
/*!40000 ALTER TABLE `wut_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `wut_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wut_users_backlog`
--

DROP TABLE IF EXISTS `wut_users_backlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wut_users_backlog` (
  `back_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID of the loging',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'timestamp of the loging',
  `id` bigint(20) unsigned NOT NULL COMMENT 'The integer representation of the unique identifier for this User. This number is greater than 53 bits and some programming languages may have difficulty/silent defects in interpreting it.',
  `contributors_enabled` enum('false','true') CHARACTER SET utf8 DEFAULT NULL COMMENT 'Indicates that the user has an account with "contributor mode" enabled, allowing for Tweets issued by the user to be co-authored by another account. Rarely true.',
  `created_at` datetime DEFAULT NULL COMMENT 'The UTC datetime that the user account was created on Twitter.',
  `default_profile` enum('false','true') CHARACTER SET utf8 DEFAULT NULL COMMENT 'When true, indicates that the user has not altered the theme or background of their user profile.',
  `default_profile_image` enum('false','true') CHARACTER SET utf8 DEFAULT NULL COMMENT 'When true, indicates that the user has not uploaded their own avatar and a default egg avatar is used instead.',
  `description` varchar(160) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Nullable. The user-defined UTF-8 string describing their account.',
  `favourites_count` int(10) unsigned DEFAULT NULL COMMENT 'The number of tweets this user has favorited in the account''s lifetime.',
  `follow_request_sent` enum('false','true') CHARACTER SET utf8 DEFAULT NULL COMMENT 'Nullable. Perspectival. When true, indicates that the authenticating user has issued a follow request to this protected user account.',
  `following` enum('false','true') CHARACTER SET utf8 DEFAULT NULL COMMENT 'Nullable. Perspectival. Deprecated. When true, indicates that the authenticating user is following this user. Some false negatives are possible when set to "false," but these false negatives are increasingly being represented as "null" instead.',
  `followers_count` int(10) unsigned DEFAULT NULL COMMENT 'The number of followers this account currently has. Under certain conditions of duress, this field will temporarily indicate "0."',
  `friends_count` int(10) unsigned DEFAULT NULL COMMENT 'The number of users this account is following (AKA their "followings"). Under certain conditions of duress, this field will temporarily indicate "0."',
  `geo_enabled` enum('false','true') CHARACTER SET utf8 DEFAULT NULL COMMENT 'When true, indicates that the user has enabled the possibility of geotagging their Tweets.',
  `is_translator` enum('false','true') CHARACTER SET utf8 DEFAULT NULL COMMENT 'When true, indicates that the user is a participant in Twitter''s translator community',
  `lang` varchar(2) CHARACTER SET utf8 DEFAULT NULL COMMENT 'The ISO 639-1 two-letter character code for the user''s self-declared user interface language. May or may not have anything to do with the content of their Tweets.',
  `listed_count` int(11) unsigned DEFAULT NULL COMMENT 'The number of public lists that this user is a member of.',
  `location` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Nullable. The user-defined location for this account''s profile. Not necessarily a location nor parseable. This field will occasionally be fuzzily interpreted by the Search service.',
  `name` varchar(40) CHARACTER SET utf8 NOT NULL COMMENT 'The name of the user, as they''ve defined it. Not necessarily a person''s name. Typically capped at 20 characters, but subject to change.',
  `notifications` enum('false','true') CHARACTER SET utf8 DEFAULT NULL COMMENT 'Nullable. Deprecated. May incorrectly report "false" at times. Indicates whether the authenticated user has chosen to receive this user''s tweets by SMS.',
  `profile_background_color` varchar(6) CHARACTER SET utf8 DEFAULT NULL COMMENT 'The hexadecimal color chosen by the user for their background.',
  `profile_background_image_url` text CHARACTER SET utf8 COMMENT 'A HTTP-based URL pointing to the background image the user has uploaded for their profile.',
  `profile_background_image_url_https` text CHARACTER SET utf8 COMMENT 'A HTTPS-based URL pointing to the background image the user has uploaded for their profile.',
  `profile_background_tile` enum('false','true') CHARACTER SET utf8 DEFAULT NULL COMMENT 'When true, indicates that the user''s profile_background_image_url should be tiled when displayed.',
  `profile_banner_url` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Undocumented field in Twitter API',
  `profile_image_url` text CHARACTER SET utf8 COMMENT 'A HTTP-based URL pointing to the user''s avatar image.',
  `profile_image_url_https` text CHARACTER SET utf8 COMMENT 'A HTTPS-based URL pointing to the user''s avatar image.',
  `profile_link_color` varchar(6) CHARACTER SET utf8 DEFAULT NULL COMMENT 'The hexadecimal color the user has chosen to display links with in their Twitter UI.',
  `profile_sidebar_border_color` varchar(6) CHARACTER SET utf8 DEFAULT NULL COMMENT 'The hexadecimal color the user has chosen to display sidebar borders with in their Twitter UI.',
  `profile_sidebar_fill_color` varchar(6) CHARACTER SET utf8 DEFAULT NULL COMMENT 'The hexadecimal color the user has chosen to display sidebar backgrounds with in their Twitter UI.',
  `profile_text_color` varchar(6) CHARACTER SET utf8 DEFAULT NULL COMMENT 'The hexadecimal color the user has chosen to display text with in their Twitter UI.',
  `profile_use_background_image` enum('false','true') CHARACTER SET utf8 DEFAULT NULL COMMENT 'When true, indicates the user wants their uploaded background image to be used.',
  `protected` enum('false','true') CHARACTER SET utf8 DEFAULT NULL COMMENT 'When true, indicates that this user has chosen to protect their Tweets.',
  `screen_name` varchar(30) CHARACTER SET utf8 NOT NULL COMMENT 'The screen name, handle, or alias that this user identifies themselves with. screen_names are unique but subject to change. Use id_str as a user identifier whenever possible. Typically a maximum of 15 characters long, but some historical accounts may exist with longer names.',
  `show_all_inline_media` enum('false','true') CHARACTER SET utf8 DEFAULT NULL COMMENT 'Indicates that the user would like to see media inline. Somewhat disused.',
  `status` bigint(20) unsigned DEFAULT NULL COMMENT 'Nullable. Last Tweet_id. If possible, the user''s most recent tweet or retweet. In some circumstances, this data cannot be provided and this field will be omitted, null, or empty.',
  `statuses_count` int(10) unsigned DEFAULT NULL COMMENT 'The number of tweets (including retweets) issued by the user.',
  `time_zone` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Nullable. A string describing the Time Zone this user declares themselves within.',
  `url` text CHARACTER SET utf8 COMMENT 'Nullable. A URL provided by the user in association with their profile.',
  `utc_offset` int(10) unsigned DEFAULT NULL COMMENT 'Nullable. The offset from GMT/UTC in seconds.',
  `verified` enum('false','true') CHARACTER SET utf8 DEFAULT NULL COMMENT 'When true, indicates that the user has a verified account.',
  `withheld_in_countries` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT 'When present, indicates a textual representation of the two-letter country codes this user is withheld from.',
  `withheld_scope` enum('status','user') CHARACTER SET utf8 DEFAULT NULL COMMENT 'When present, indicates whether the content being withheld is the "status" or a "user." ',
  PRIMARY KEY (`back_id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47872 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Twitter user data';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wut_users_backlog`
--

LOCK TABLES `wut_users_backlog` WRITE;
/*!40000 ALTER TABLE `wut_users_backlog` DISABLE KEYS */;
/*!40000 ALTER TABLE `wut_users_backlog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wut_warnings`
--

DROP TABLE IF EXISTS `wut_warnings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wut_warnings` (
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Timestmp of warning',
  `code` varchar(80) CHARACTER SET utf8 NOT NULL COMMENT 'Warning Code',
  `message` text CHARACTER SET utf8 NOT NULL COMMENT 'Warning message',
  `percent_full` double DEFAULT NULL COMMENT 'Percentage of backfill messages in queue',
  PRIMARY KEY (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Warnings from the Twitter API';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wut_warnings`
--

LOCK TABLES `wut_warnings` WRITE;
/*!40000 ALTER TABLE `wut_warnings` DISABLE KEYS */;
/*!40000 ALTER TABLE `wut_warnings` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-04-28 17:35:35
