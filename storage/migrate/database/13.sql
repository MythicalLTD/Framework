CREATE TABLE `framework_roles_permissions_list` (`id` INT NOT NULL AUTO_INCREMENT , `permission` TEXT NOT NULL , `owned_by` ENUM('plugin') NOT NULL DEFAULT 'plugin' , `owned_by_id` INT NOT NULL , `deleted` ENUM('false','true') NOT NULL DEFAULT 'false' , `locked` ENUM('false','true') NOT NULL DEFAULT 'false' , `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = InnoDB;