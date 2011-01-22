SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT=0;
START TRANSACTION;

CREATE TABLE IF NOT EXISTS `acls` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` tinyint(3) unsigned NOT NULL,
  `resource_id` smallint(5) unsigned NOT NULL,
  `action_id` tinyint(3) unsigned NOT NULL,
  `regulation` enum('allow','deny') NOT NULL DEFAULT 'allow',
  PRIMARY KEY (`id`),
  KEY `fk_acl_role` (`role_id`),
  KEY `fk_acl_resource` (`resource_id`),
  KEY `fk_acl_action` (`action_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `actions` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `resources` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` smallint(5) unsigned DEFAULT NULL,
  `route_name` varchar(64) NOT NULL DEFAULT 'default',
  `directory` varchar(64) DEFAULT NULL,
  `controller` varchar(64) DEFAULT NULL,
  `action` varchar(64) DEFAULT NULL,
  `object_id` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_resource_parent` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- If your `roles` table looks different - try to merge them or drop yours and use this one


CREATE TABLE IF NOT EXISTS `roles` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` tinyint(3) unsigned DEFAULT NULL,
  `name` varchar(32) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_name` (`name`),
  KEY `fk_role_parent` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


ALTER TABLE `acls`
  ADD CONSTRAINT `fk_acl_action` FOREIGN KEY (`action_id`) REFERENCES `actions` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_acl_resource` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_acl_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;

ALTER TABLE `resources`
  ADD CONSTRAINT `fk_resource_parent` FOREIGN KEY (`parent_id`) REFERENCES `resources` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `roles`
  ADD CONSTRAINT `fk_role_parent` FOREIGN KEY (`parent_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;

COMMIT;