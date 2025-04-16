-- Schema for streaming_list

DROP TABLE IF EXISTS `media`;

CREATE TABLE `media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `type` enum('movie','tv_show','franchise','series') NOT NULL,
  `streaming_platform` varchar(255) DEFAULT NULL,
  `date_added` timestamp NULL DEFAULT current_timestamp(),
  `watched` tinyint(1) DEFAULT 0,
  `rating` int(11) DEFAULT 0,
  `currently_watching` tinyint(1) DEFAULT 0,
  `notes` text DEFAULT NULL,
  `next_airing` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
