SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

DROP SCHEMA IF EXISTS `projectsforecast` ;
CREATE SCHEMA IF NOT EXISTS `projectsforecast` DEFAULT CHARACTER SET utf8 COLLATE utf8_swedish_ci ;
USE `projectsforecast` ;

-- -----------------------------------------------------
-- Table `projectsforecast`.`Users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `projectsforecast`.`Users` ;

CREATE  TABLE IF NOT EXISTS `projectsforecast`.`Users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `username` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `username_canonical` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `email` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `email_canonical` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `enabled` TINYINT(1) NOT NULL ,
  `salt` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `password` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `last_login` DATETIME NULL DEFAULT NULL ,
  `locked` TINYINT(1) NOT NULL ,
  `expired` TINYINT(1) NOT NULL ,
  `expires_at` DATETIME NULL DEFAULT NULL ,
  `confirmation_token` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL ,
  `password_requested_at` DATETIME NULL DEFAULT NULL ,
  `roles` LONGTEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT '(DC2Type:array)' ,
  `credentials_expired` TINYINT(1) NOT NULL ,
  `credentials_expire_at` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

CREATE UNIQUE INDEX `UNIQ_D5428AED92FC23A8` ON `projectsforecast`.`Users` (`username_canonical` ASC) ;

CREATE UNIQUE INDEX `UNIQ_D5428AEDA0D96FBF` ON `projectsforecast`.`Users` (`email_canonical` ASC) ;


-- -----------------------------------------------------
-- Table `projectsforecast`.`Agencies`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `projectsforecast`.`Agencies` ;

CREATE  TABLE IF NOT EXISTS `projectsforecast`.`Agencies` (
  `Id` INT NOT NULL AUTO_INCREMENT ,
  `AgenciesCreatedBy` INT(11) NOT NULL ,
  `AgenciesName` VARCHAR(45) NOT NULL ,
  `AgenciesIsTrashed` TINYINT(1) NOT NULL DEFAULT 0 ,
  `AgenciesCreated` DATETIME NOT NULL ,
  `AgenciesUpdated` DATETIME NULL ,
  PRIMARY KEY (`Id`) ,
  CONSTRAINT `fk_Agencies_Users1`
    FOREIGN KEY (`AgenciesCreatedBy` )
    REFERENCES `projectsforecast`.`Users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_Agencies_Users1_idx` ON `projectsforecast`.`Agencies` (`AgenciesCreatedBy` ASC) ;


-- -----------------------------------------------------
-- Table `projectsforecast`.`Customers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `projectsforecast`.`Customers` ;

CREATE  TABLE IF NOT EXISTS `projectsforecast`.`Customers` (
  `Id` INT NOT NULL AUTO_INCREMENT ,
  `AgenciesIdAgencies` INT NULL ,
  `CustomersCreatedBy` INT(11) NOT NULL ,
  `CustomersName` VARCHAR(50) NULL ,
  `CustomersIsTrashed` TINYINT(1) NOT NULL DEFAULT 0 ,
  `CustomersCreated` DATETIME NOT NULL ,
  `CustomersUpdated` DATETIME NULL ,
  PRIMARY KEY (`Id`) ,
  CONSTRAINT `fk_Customers_Agencies`
    FOREIGN KEY (`AgenciesIdAgencies` )
    REFERENCES `projectsforecast`.`Agencies` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Customers_Users1`
    FOREIGN KEY (`CustomersCreatedBy` )
    REFERENCES `projectsforecast`.`Users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_Customers_Agencies_idx` ON `projectsforecast`.`Customers` (`AgenciesIdAgencies` ASC) ;

CREATE INDEX `fk_Customers_Users1_idx` ON `projectsforecast`.`Customers` (`CustomersCreatedBy` ASC) ;


-- -----------------------------------------------------
-- Table `projectsforecast`.`Projects`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `projectsforecast`.`Projects` ;

CREATE  TABLE IF NOT EXISTS `projectsforecast`.`Projects` (
  `Id` INT NOT NULL AUTO_INCREMENT ,
  `CustomersIdCustomers` INT NOT NULL ,
  `ProjectsCreatedBy` INT(11) NOT NULL ,
  `ProjectsName` VARCHAR(250) NULL ,
  `ProjectsComments` LONGTEXT NULL ,
  `ProjectsIsTrashed` TINYINT(1) NOT NULL DEFAULT 0 ,
  `ProjectsCreated` DATETIME NOT NULL ,
  `ProjectsUpdated` DATETIME NULL ,
  PRIMARY KEY (`Id`) ,
  CONSTRAINT `fk_Projects_Customers1`
    FOREIGN KEY (`CustomersIdCustomers` )
    REFERENCES `projectsforecast`.`Customers` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Projects_Users1`
    FOREIGN KEY (`ProjectsCreatedBy` )
    REFERENCES `projectsforecast`.`Users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_Projects_Customers1_idx` ON `projectsforecast`.`Projects` (`CustomersIdCustomers` ASC) ;

CREATE INDEX `fk_Projects_Users1_idx` ON `projectsforecast`.`Projects` (`ProjectsCreatedBy` ASC) ;


-- -----------------------------------------------------
-- Table `projectsforecast`.`Lots`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `projectsforecast`.`Lots` ;

CREATE  TABLE IF NOT EXISTS `projectsforecast`.`Lots` (
  `Id` INT NOT NULL AUTO_INCREMENT ,
  `ProjectsIdProjects` INT NOT NULL ,
  `LotsCreatedBy` INT(11) NOT NULL ,
  `LotsIsTrashed` TINYINT(1) NOT NULL DEFAULT 0 ,
  `LotsCreated` DATETIME NOT NULL ,
  `LotsUpdated` VARCHAR(45) NULL ,
  PRIMARY KEY (`Id`) ,
  CONSTRAINT `fk_Lots_Projects1`
    FOREIGN KEY (`ProjectsIdProjects` )
    REFERENCES `projectsforecast`.`Projects` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Lots_Users1`
    FOREIGN KEY (`LotsCreatedBy` )
    REFERENCES `projectsforecast`.`Users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_Lots_Projects1_idx` ON `projectsforecast`.`Lots` (`ProjectsIdProjects` ASC) ;

CREATE INDEX `fk_Lots_Users1_idx` ON `projectsforecast`.`Lots` (`LotsCreatedBy` ASC) ;


-- -----------------------------------------------------
-- Table `projectsforecast`.`States`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `projectsforecast`.`States` ;

CREATE  TABLE IF NOT EXISTS `projectsforecast`.`States` (
  `Id` INT NOT NULL AUTO_INCREMENT ,
  `StatesLabel` VARCHAR(45) NULL ,
  `StatesIsTrashed` TINYINT(1) NOT NULL DEFAULT 0 ,
  `StatesCreated` DATETIME NOT NULL ,
  `StatesUpdated` DATETIME NULL ,
  `StatesIsDefault` TINYINT(1) NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`Id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `projectsforecast`.`Tasks`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `projectsforecast`.`Tasks` ;

CREATE  TABLE IF NOT EXISTS `projectsforecast`.`Tasks` (
  `Id` INT NOT NULL AUTO_INCREMENT ,
  `ProjectsIdProjects` INT NOT NULL ,
  `LotsIdLots` INT NULL ,
  `StatesIdStates` INT NOT NULL ,
  `TasksCreatedBy` INT(11) NOT NULL ,
  `TasksName` VARCHAR(45) NOT NULL ,
  `TasksBeginAt` DATE NOT NULL ,
  `TasksEndAt` DATE NOT NULL ,
  `TasksOrder` SMALLINT NOT NULL DEFAULT 1 ,
  `TasksIsTrashed` TINYINT(1) NOT NULL DEFAULT 0 ,
  `TasksCreated` DATETIME NOT NULL ,
  `TasksUpdated` DATETIME NULL ,
  PRIMARY KEY (`Id`) ,
  CONSTRAINT `fk_Tasks_Lots1`
    FOREIGN KEY (`LotsIdLots` )
    REFERENCES `projectsforecast`.`Lots` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Tasks_Projects1`
    FOREIGN KEY (`ProjectsIdProjects` )
    REFERENCES `projectsforecast`.`Projects` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Tasks_States1`
    FOREIGN KEY (`StatesIdStates` )
    REFERENCES `projectsforecast`.`States` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Tasks_Users1`
    FOREIGN KEY (`TasksCreatedBy` )
    REFERENCES `projectsforecast`.`Users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_Tasks_Lots1_idx` ON `projectsforecast`.`Tasks` (`LotsIdLots` ASC) ;

CREATE INDEX `fk_Tasks_Projects1_idx` ON `projectsforecast`.`Tasks` (`ProjectsIdProjects` ASC) ;

CREATE INDEX `fk_Tasks_States1_idx` ON `projectsforecast`.`Tasks` (`StatesIdStates` ASC) ;

CREATE INDEX `fk_Tasks_Users1_idx` ON `projectsforecast`.`Tasks` (`TasksCreatedBy` ASC) ;


-- -----------------------------------------------------
-- Table `projectsforecast`.`Providers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `projectsforecast`.`Providers` ;

CREATE  TABLE IF NOT EXISTS `projectsforecast`.`Providers` (
  `Id` INT NOT NULL AUTO_INCREMENT ,
  `ProvidersName` VARCHAR(50) NOT NULL ,
  `ProvidersIsTrashed` TINYINT(1) NOT NULL DEFAULT 0 ,
  `ProvidersCreated` DATETIME NOT NULL ,
  `ProvidersUpdated` VARCHAR(45) NULL ,
  PRIMARY KEY (`Id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `projectsforecast`.`Jobs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `projectsforecast`.`Jobs` ;

CREATE  TABLE IF NOT EXISTS `projectsforecast`.`Jobs` (
  `Id` INT NOT NULL AUTO_INCREMENT ,
  `JobsLabel` VARCHAR(45) NULL ,
  `JobsIsTrashed` TINYINT(1) NOT NULL DEFAULT 0 ,
  `JobsCreated` DATETIME NOT NULL ,
  `JobsUpdated` DATETIME NULL ,
  PRIMARY KEY (`Id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `projectsforecast`.`TasksLoads`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `projectsforecast`.`TasksLoads` ;

CREATE  TABLE IF NOT EXISTS `projectsforecast`.`TasksLoads` (
  `Id` INT NOT NULL AUTO_INCREMENT ,
  `TasksIdTasks` INT NOT NULL ,
  `JobsIdJobs` INT NOT NULL ,
  `TasksLoadsCreatedBy` INT(11) NOT NULL ,
  `ProvidersIdProviders` INT NULL ,
  `TasksLoadsVolume` SMALLINT NOT NULL ,
  `isTrashed` TINYINT(1) NOT NULL DEFAULT 0 ,
  `TasksLoadsCreated` DATETIME NOT NULL ,
  `TasksLoadsUpdated` DATETIME NULL ,
  PRIMARY KEY (`Id`) ,
  CONSTRAINT `fk_Load_Tasks1`
    FOREIGN KEY (`TasksIdTasks` )
    REFERENCES `projectsforecast`.`Tasks` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Load_Providers1`
    FOREIGN KEY (`ProvidersIdProviders` )
    REFERENCES `projectsforecast`.`Providers` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Load_Jobs1`
    FOREIGN KEY (`JobsIdJobs` )
    REFERENCES `projectsforecast`.`Jobs` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_TasksLoads_Users1`
    FOREIGN KEY (`TasksLoadsCreatedBy` )
    REFERENCES `projectsforecast`.`Users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_Load_Tasks1_idx` ON `projectsforecast`.`TasksLoads` (`TasksIdTasks` ASC) ;

CREATE INDEX `fk_Load_Providers1_idx` ON `projectsforecast`.`TasksLoads` (`ProvidersIdProviders` ASC) ;

CREATE INDEX `fk_Load_Jobs1_idx` ON `projectsforecast`.`TasksLoads` (`JobsIdJobs` ASC) ;

CREATE INDEX `fk_TasksLoads_Users1_idx` ON `projectsforecast`.`TasksLoads` (`TasksLoadsCreatedBy` ASC) ;


-- -----------------------------------------------------
-- Table `projectsforecast`.`Groups`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `projectsforecast`.`Groups` ;

CREATE  TABLE IF NOT EXISTS `projectsforecast`.`Groups` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `roles` LONGTEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT '(DC2Type:array)' ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

CREATE UNIQUE INDEX `UNIQ_F7C13C465E237E06` ON `projectsforecast`.`Groups` (`name` ASC) ;


-- -----------------------------------------------------
-- Table `projectsforecast`.`UsersHasGroups`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `projectsforecast`.`UsersHasGroups` ;

CREATE  TABLE IF NOT EXISTS `projectsforecast`.`UsersHasGroups` (
  `Usersid` INT(11) NOT NULL ,
  `Groupsid` INT(11) NOT NULL ,
  PRIMARY KEY (`Usersid`, `Groupsid`) ,
  CONSTRAINT `fk_UsersHasGroups_Users1`
    FOREIGN KEY (`Usersid` )
    REFERENCES `projectsforecast`.`Users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_UsersHasGroups_Groups1`
    FOREIGN KEY (`Groupsid` )
    REFERENCES `projectsforecast`.`Groups` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

CREATE INDEX `fk_UsersHasGroups_Groups1_idx` ON `projectsforecast`.`UsersHasGroups` (`Groupsid` ASC) ;

CREATE INDEX `fk_UsersHasGroups_Users1_idx` ON `projectsforecast`.`UsersHasGroups` (`Usersid` ASC) ;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
