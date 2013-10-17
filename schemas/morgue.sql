--
-- Table structure for table `postmortems`
--

DROP TABLE IF EXISTS `postmortems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `postmortems` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `summary` text NOT NULL,
  `starttime` int(11) UNSIGNED NOT NULL,
  `endtime` int(11) UNSIGNED NOT NULL,
  `etsystatustime` int(11) UNSIGNED NOT NULL,
  `detecttime` int(11) UNSIGNED NOT NULL,
  `severity` int(1) UNSIGNED NOT NULL,
  `gcal` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `contact` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT "",
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `tags`;
DROP TABLE IF EXISTS `postmortem_referenced_tags`;
CREATE TABLE `tags` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `postmortem_referenced_tags` (
  `postmortem_id` bigint(20) NOT NULL,
  `tag_id` bigint(20) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  KEY `postmortem_id` (`postmortem_id`),
  KEY `tag_id` (`tag_id`),
  PRIMARY KEY (`postmortem_id`, `tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `postmortem_history`;
CREATE TABLE `postmortem_history` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `postmortem_id` bigint(20) NOT NULL,
  `auth_username` varchar(128) NOT NULL,
  `action` VARCHAR(32) NOT NULL,
  `create_date` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `postmortem_id` (`postmortem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
