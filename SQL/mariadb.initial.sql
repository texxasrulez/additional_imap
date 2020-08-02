/*
 * Roundcube Additional IMAP Schema
 *
 * @author Gene Hawkins <texxasrulez@yahoo.com>
 *
 * @licence GNU AGPL
 */

CREATE TABLE IF NOT EXISTS additional_imap (
  `id` int check (`id` > 0) NOT NULL AUTO_INCREMENT,
  `user_id` int check (`user_id` > 0) NOT NULL,
  `iid` int check (`iid` > 0) NOT NULL,
  `username` varchar(256) DEFAULT NULL,
  `password` text,
  `server` varchar(256) DEFAULT NULL,
  `enabled` int check (`enabled` > 0) NOT NULL DEFAULT '0',
  `label` text,
  `preferences` text,
  PRIMARY KEY (`id`)
) /*!40000 ENGINE=INNODB */ /*!40101 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci AUTO_INCREMENT=1 */;

CREATE INDEX `user_id` ON additional_imap (`user_id`);
CREATE INDEX `iid` ON additional_imap (`iid`);

ALTER TABLE `additional_imap`
  ADD CONSTRAINT `additional_imap_ibfk_2` FOREIGN KEY (`iid`) REFERENCES identities (`identity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `additional_imap_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS additional_imap_hosts (
  `id` int NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) NOT NULL,
  `host` varchar(255) DEFAULT NULL,
  `ts` datetime NOT NULL,
  PRIMARY KEY (`id`)
) /*!40000 ENGINE=INNODB */ /*!40101 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci AUTO_INCREMENT=1 */;

CREATE TABLE IF NOT EXISTS cache_tables (
  `id` int NOT NULL AUTO_INCREMENT,
  `suffix` varchar(255) NOT NULL,
  `ts` int NOT NULL,
  PRIMARY KEY (`id`)
) /*!40000 ENGINE=INNODB */ /*!40101 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci AUTO_INCREMENT=1 */;

REPLACE INTO `system` (`name`, `value`) SELECT ('tx-additional-imap-version', '2020080200');
