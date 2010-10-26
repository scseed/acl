CREATE TABLE IF NOT EXISTS `acls` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `role_id` int(11) unsigned NOT NULL,
  `resource_id` smallint(5) unsigned NOT NULL,
  `action_id` tinyint(3) unsigned NOT NULL,
  `regulation` enum('allow','deny') NOT NULL default 'allow',
  PRIMARY KEY  (`id`),
  KEY `fk_acl_role` (`role_id`),
  KEY `fk_acl_resource` (`resource_id`),
  KEY `fk_acl_action` (`action_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `actions` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `resources` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `parent_id` smallint(5) unsigned default NULL,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `fk_resource_parent` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- If your `roles` table looks different - try to merge them or drop yours and use this one

CREATE TABLE IF NOT EXISTS `roles` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `parent_id` smallint(5) unsigned default NULL,
  `name` varchar(64) NOT NULL,
  `description` varchar(128) default NULL,
  PRIMARY KEY  (`id`),
  KEY `fk_role_parent` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

ALTER TABLE `acls`
  ADD CONSTRAINT `fk_acl_action` FOREIGN KEY (`action_id`) REFERENCES `actions` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_acl_resource` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_acl_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;

ALTER TABLE `resources`
  ADD CONSTRAINT `fk_resource_parent` FOREIGN KEY (`parent_id`) REFERENCES `resources` (`id`) ON UPDATE CASCADE;

ALTER TABLE `roles`
  ADD column `parent_id` int(11) UNSIGNED default NULL AFTER `id`,
  ADD CONSTRAINT `fk_role_parent` FOREIGN KEY (`parent_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;