-- MySQL dump 10.13  Distrib 5.7.19, for Linux (x86_64)
--
-- Host: localhost    Database: wag_db
-- ------------------------------------------------------
-- Server version	5.7.19-0ubuntu0.16.04.1


--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `user_score` bigint(20) DEFAULT NULL,
  `posted` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_userid` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
