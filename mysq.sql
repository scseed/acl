-- -----------------------------------------------------
-- Table `roles`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `roles` (
  `id` TINYINT(3) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `parent_id` TINYINT(3) UNSIGNED NULL DEFAULT NULL ,
  `name` VARCHAR(32) NOT NULL ,
  `description` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `uniq_name` (`name` ASC) ,
  INDEX `fk_role_parent` (`parent_id` ASC) ,
  CONSTRAINT `fk_role_parent`
    FOREIGN KEY (`parent_id` )
    REFERENCES `roles` (`id` )
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `resources`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `resources` (
  `id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `parent_id` SMALLINT(5) UNSIGNED NULL DEFAULT NULL ,
  `route_name` VARCHAR(64) NOT NULL DEFAULT 'default' ,
  `directory` VARCHAR(64) NULL DEFAULT NULL ,
  `controller` VARCHAR(64) NULL DEFAULT NULL ,
  `action` VARCHAR(64) NULL DEFAULT NULL ,
  `params` VARCHAR(256) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_resource_parent` (`parent_id` ASC) ,
  CONSTRAINT `fk_resource_parent`
    FOREIGN KEY (`parent_id` )
    REFERENCES `resources` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `actions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `actions` (
  `id` TINYINT(3) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(20) NOT NULL ,
  `score` SMALLINT(2) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `acls`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `acls` (
  `id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `role_id` TINYINT(3) UNSIGNED NOT NULL ,
  `resource_id` SMALLINT(5) UNSIGNED NOT NULL ,
  `action_id` TINYINT(3) UNSIGNED NOT NULL ,
  `regulation` ENUM('allow','deny') NOT NULL DEFAULT 'allow' ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_acl_role` (`role_id` ASC) ,
  INDEX `fk_acl_resource` (`resource_id` ASC) ,
  INDEX `fk_acl_action` (`action_id` ASC) ,
  CONSTRAINT `fk_acl_action`
    FOREIGN KEY (`action_id` )
    REFERENCES `actions` (`id` )
    ON UPDATE CASCADE,
  CONSTRAINT `fk_acl_resource`
    FOREIGN KEY (`resource_id` )
    REFERENCES `resources` (`id` )
    ON UPDATE CASCADE,
  CONSTRAINT `fk_acl_role`
    FOREIGN KEY (`role_id` )
    REFERENCES `roles` (`id` )
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `assertion_types`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `assertion_types` (
  `id` SMALLINT(2) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `assertions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `assertions` (
  `id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(64) NOT NULL ,
  `acl_id` SMALLINT(5) UNSIGNED NOT NULL ,
  `params` VARCHAR(256) NULL DEFAULT NULL ,
  `condition` VARCHAR(45) NULL ,
  `type_id` SMALLINT(2) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_assertion_type` (`type_id` ASC) ,
  INDEX `fk_assertion_acl` (`acl_id` ASC) ,
  CONSTRAINT `fk_assertion_type`
    FOREIGN KEY (`type_id` )
    REFERENCES `assertion_types` (`id` )
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_assertion_acl`
    FOREIGN KEY (`acl_id` )
    REFERENCES `acls` (`id` )
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;