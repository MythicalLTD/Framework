ALTER TABLE `framework_roles_permissions` ADD `status` ENUM('true','false') NOT NULL DEFAULT 'true' AFTER `permission`;
