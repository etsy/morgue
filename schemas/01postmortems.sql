--
-- Table structure for table `postmortems`
--

DROP TABLE IF EXISTS `postmortems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `postmortems` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `summary` longtext NOT NULL,
  `why_surprised` longtext NOT NULL,
  `tldr` longtext NOT NULL,
  `meeting_notes_link` varchar(255) NOT NULL DEFAULT '',
  `starttime` int(11) UNSIGNED NOT NULL,
  `endtime` int(11) UNSIGNED NOT NULL,
  `statustime` int(11) UNSIGNED NOT NULL,
  `detecttime` int(11) UNSIGNED NOT NULL,
  `severity` int(1) UNSIGNED NOT NULL,
  `gcal` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `contact` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT "",
  `facilitator` varchar(255) NOT NULL DEFAULT '',
  `facilitator_email` varchar(255) NOT NULL DEFAULT '',
  `subsystem` varchar(50) NOT NULL DEFAULT '',
  `owner_team` varchar(50) NOT NULL DEFAULT '',
  `problem_type` varchar(50) NOT NULL DEFAULT '',
  `impact_type` varchar(50) NOT NULL DEFAULT '',
  `incident_cause` varchar(50) NOT NULL DEFAULT '',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created` int(11) UNSIGNED NOT NULL,
  `modified` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `modifier` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `tags`;
DROP TABLE IF EXISTS `postmortem_referenced_tags`;
CREATE TABLE `tags` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
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
  `summary` longtext,
  `why_surprised` longtext,
  `create_date` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `postmortem_id` (`postmortem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
