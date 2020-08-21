-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `mydb` DEFAULT CHARACTER SET utf8 ;
USE `mydb` ;

-- -----------------------------------------------------
-- Table `mydb`.`companies`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`companies` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`employees`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`employees` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(45) NOT NULL,
  `name` VARCHAR(45) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `24hclock` TINYINT(1) NOT NULL DEFAULT 1,
  `endianness` ENUM('L', 'M', 'B') NOT NULL DEFAULT 'L',
  `companies_id` INT UNSIGNED NOT NULL,
  `daily_minutes` TIME NOT NULL DEFAULT 06:00,
  `admin` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `fk_employees_companies_idx` (`companies_id` ASC) VISIBLE,
  CONSTRAINT `fk_employees_companies`
    FOREIGN KEY (`companies_id`)
    REFERENCES `mydb`.`companies` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`work_months`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`work_months` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `employees_id` INT UNSIGNED NOT NULL,
  `month` INT NOT NULL,
  `year` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_work_months_employees1_idx` (`employees_id` ASC) VISIBLE,
  CONSTRAINT `fk_work_months_employees1`
    FOREIGN KEY (`employees_id`)
    REFERENCES `mydb`.`employees` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`work_days`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`work_days` (
  `day` DATE NOT NULL,
  `work_months_id` INT UNSIGNED NOT NULL,
  `start` TIME NOT NULL,
  `lunch_start` TIME GENERATED ALWAYS AS () VIRTUAL,
  `lunch_end` TIME NOT NULL,
  `end` TIME NOT NULL,
  `total` TIME NULL,
  INDEX `fk_employees_has_months_months1_idx` (`work_months_id` ASC) VISIBLE,
  PRIMARY KEY (`day`),
  CONSTRAINT `fk_employees_has_months_months1`
    FOREIGN KEY (`work_months_id`)
    REFERENCES `mydb`.`work_months` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
