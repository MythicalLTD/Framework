INSERT INTO `framework_roles` (`id`, `name`, `weight`, `date`) VALUES ('1', 'Default', '1', current_timestamp());
INSERT INTO `framework_roles` (`id`, `name`, `weight`, `date`) VALUES ('2', 'Admin', '2', current_timestamp());
INSERT INTO `framework_roles` (`id`, `name`, `weight`, `date`) VALUES ('3', 'Administrator', '3', current_timestamp());
INSERT INTO `framework_roles_permissions` (`id`, `role_id`, `permission`, `date`) VALUES ('1', '1', 'mythicalframework.default', current_timestamp());
INSERT INTO `framework_roles_permissions` (`id`, `role_id`, `permission`, `date`) VALUES ('2', '2', 'mythicalframework.admin', current_timestamp());
INSERT INTO `framework_roles_permissions` (`id`, `role_id`, `permission`, `date`) VALUES ('3', '3', 'mythicalframework.administrator', current_timestamp());