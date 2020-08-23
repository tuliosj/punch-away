-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema punch-away
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema punch-away
-- -----------------------------------------------------
DROP DATABASE IF EXISTS `punch-away` ;
CREATE DATABASE IF NOT EXISTS `punch-away` DEFAULT CHARACTER SET utf8 ;
USE `punch-away` ;

-- -----------------------------------------------------
-- Table `punch-away`.`companies`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `punch-away`.`companies` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `gmt_difference` INT NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `punch-away`.`employees`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `punch-away`.`employees` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(45) NOT NULL UNIQUE,
  `name` VARCHAR(45) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `24hclock` TINYINT(1) NOT NULL DEFAULT 1,
  `endianness` ENUM('L', 'M', 'B') NOT NULL DEFAULT 'L',
  `companies_id` INT UNSIGNED NOT NULL,
  `admin` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `fk_employees_companies_idx` (`companies_id` ASC),
  CONSTRAINT `fk_employees_companies`
    FOREIGN KEY (`companies_id`)
    REFERENCES `punch-away`.`companies` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `punch-away`.`work_days`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `punch-away`.`work_days` (
  `date` DATE NOT NULL,
  `employees_id` INT UNSIGNED NOT NULL,
  `month` VARCHAR(7) NOT NULL,
  `start` TIME NOT NULL DEFAULT '00:00:00',
  `lunch_start` TIME NOT NULL DEFAULT '00:00:00',
  `lunch_end` TIME NOT NULL DEFAULT '00:00:00',
  `end` TIME NOT NULL DEFAULT '00:00:00',
  `total` TIME NOT NULL DEFAULT '00:00:00',
  PRIMARY KEY (`date`, `employees_id`),
  INDEX `fk_work_days_employees1_idx` (`employees_id` ASC),
  CONSTRAINT `fk_work_days_employees1`
    FOREIGN KEY (`employees_id`)
    REFERENCES `punch-away`.`employees` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- Triggers

DELIMITER $$

CREATE TRIGGER `calculate_total`
BEFORE UPDATE ON `work_days`
FOR EACH ROW
BEGIN
  SET NEW.`total` = ((NEW.`end` - NEW.`lunch_end`) + (NEW.`lunch_start` - NEW.`start`));
END$$

DELIMITER ;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- Inserts

INSERT INTO `companies`(`name`, `gmt_difference`) VALUES ('Acme Inc.',-3);
INSERT INTO `companies`(`name`, `gmt_difference`) VALUES ('La Casa do Pastel',2);
INSERT INTO `companies`(`name`, `gmt_difference`) VALUES ('McRonald\'s',-3);

INSERT INTO `employees`(`email`, `name`, `password`, `24hclock`, `endianness`, `companies_id`) VALUES ("tuliosjardim@gmail.com", "Túlio Jardim", "5f6955d227a320c7f1f6c7da2a6d96a851a8118f", 1, 'L', 1);
INSERT INTO `employees`(`email`, `name`, `password`, `24hclock`, `endianness`, `companies_id`) VALUES ("edinho@gmail.com", "Edson Jardim", "5f6955d227a320c7f1f6c7da2a6d96a851a8118f", 0, 'M', 2);

INSERT INTO `work_days`(`date`, `employees_id`, `month`) VALUES ('2020-07-31', 1, '2020/07');