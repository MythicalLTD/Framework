
    SET FOREIGN_KEY_CHECKS=0;
    SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
    START TRANSACTION;
    SET time_zone = "+00:20";

    DROP TABLE IF EXISTS `framework_announcements`;
    CREATE TABLE `framework_announcements` (
    `id` int(11) NOT NULL,
    `title` text NOT NULL,
    `text` text NOT NULL,
    `date` datetime NOT NULL DEFAULT current_timestamp()
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


    TRUNCATE TABLE `framework_announcements`;

    DROP TABLE IF EXISTS `framework_logs`;
    CREATE TABLE `framework_logs` (
    `id` int(11) NOT NULL,
    `l_type` enum('OTHER','CORE','DATABASE','PLUGIN','LOG','LANGUAGE') NOT NULL DEFAULT 'OTHER',
    `levels` enum('INFO','WARNING','ERROR','CRITICAL','OTHER') NOT NULL DEFAULT 'INFO',
    `message` text DEFAULT NULL,
    `formatted` text DEFAULT NULL,
    `date` datetime NOT NULL DEFAULT current_timestamp()
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

    TRUNCATE TABLE `framework_logs`;

    DROP TABLE IF EXISTS `framework_roles`;
    CREATE TABLE `framework_roles` (
    `id` int(11) NOT NULL,
    `name` text NOT NULL,
    `weight` int(11) NOT NULL DEFAULT 1,
    `date` datetime NOT NULL DEFAULT current_timestamp()
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


    TRUNCATE TABLE `framework_roles`;

    INSERT INTO `framework_roles` (`id`, `name`, `weight`, `date`) VALUES
    (1, 'Default', 1, '2024-07-20 06:52:48'),
    (2, 'Admin', 2, '2024-07-20 06:52:48'),
    (3, 'Administrator', 3, '2024-07-20 06:52:48');

    DROP TABLE IF EXISTS `framework_roles_permissions`;
    CREATE TABLE `framework_roles_permissions` (
    `id` int(11) NOT NULL,
    `role_id` int(11) NOT NULL DEFAULT 1,
    `permission` text NOT NULL,
    `date` datetime NOT NULL DEFAULT current_timestamp()
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


    TRUNCATE TABLE `framework_roles_permissions`;

    INSERT INTO `framework_roles_permissions` (`id`, `role_id`, `permission`, `date`) VALUES
    (1, 1, 'mythicalframework.default', '2024-07-20 06:52:48'),
    (2, 2, 'mythicalframework.admin', '2024-07-20 06:52:48'),
    (3, 3, 'mythicalframework.administrator', '2024-07-20 06:52:48');

    DROP TABLE IF EXISTS `framework_settings`;
    CREATE TABLE `framework_settings` (
    `id` int(11) NOT NULL,
    `scategory` text NOT NULL,
    `skey` text NOT NULL,
    `svalue` text NOT NULL,
    `last_modified` datetime NOT NULL DEFAULT current_timestamp()
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

    TRUNCATE TABLE `framework_settings`;

    DROP TABLE IF EXISTS `framework_users`;
    CREATE TABLE `framework_users` (
    `id` int(11) NOT NULL,
    `username` text NOT NULL,
    `first_name` text NOT NULL,
    `last_name` text NOT NULL,
    `email` text NOT NULL,
    `password` text NOT NULL,
    `avatar` text DEFAULT 'https://www.gravatar.com/avatar',
    `uuid` text NOT NULL,
    `token` text NOT NULL,
    `role` int(11) NOT NULL DEFAULT 1,
    `first_ip` text NOT NULL,
    `last_ip` text NOT NULL,
    `banned` text DEFAULT 'NO',
    `deleted` enum('false','true') NOT NULL DEFAULT 'false',
    `verified` enum('false','true') NOT NULL DEFAULT 'false',
    `last_seen` datetime NOT NULL DEFAULT current_timestamp(),
    `first_seen` datetime NOT NULL DEFAULT current_timestamp()
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

    TRUNCATE TABLE `framework_users`;

    DROP TABLE IF EXISTS `framework_users_activities`;
    CREATE TABLE `framework_users_activities` (
    `id` int(11) NOT NULL,
    `user_id` text NOT NULL,
    `username` text NOT NULL,
    `description` text NOT NULL,
    `action` text NOT NULL,
    `ip_address` text NOT NULL,
    `date` datetime NOT NULL DEFAULT current_timestamp()
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

    TRUNCATE TABLE `framework_users_activities`;

    DROP TABLE IF EXISTS `framework_users_notifications`;
    CREATE TABLE `framework_users_notifications` (
    `id` int(11) NOT NULL,
    `user_id` text NOT NULL,
    `name` text NOT NULL,
    `description` text NOT NULL,
    `date` datetime NOT NULL DEFAULT current_timestamp()
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

    TRUNCATE TABLE `framework_users_notifications`;

    DROP TABLE IF EXISTS `framework_users_userids`;
    CREATE TABLE `framework_users_userids` (
    `id` int(11) NOT NULL,
    `uid` text NOT NULL,
    `date` datetime NOT NULL DEFAULT current_timestamp()
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

    TRUNCATE TABLE `framework_users_userids`;

    ALTER TABLE `framework_announcements`
    ADD PRIMARY KEY (`id`);

    ALTER TABLE `framework_logs`
    ADD PRIMARY KEY (`id`);

    ALTER TABLE `framework_roles`
    ADD PRIMARY KEY (`id`);

    ALTER TABLE `framework_roles_permissions`
    ADD PRIMARY KEY (`id`);

    ALTER TABLE `framework_settings`
    ADD PRIMARY KEY (`id`);

    ALTER TABLE `framework_users`
    ADD PRIMARY KEY (`id`);

    ALTER TABLE `framework_users_activities`
    ADD PRIMARY KEY (`id`);

    ALTER TABLE `framework_users_notifications`
    ADD PRIMARY KEY (`id`);

    ALTER TABLE `framework_users_userids`
    ADD PRIMARY KEY (`id`);

    ALTER TABLE `framework_announcements`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

    ALTER TABLE `framework_logs`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

    ALTER TABLE `framework_roles`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

    ALTER TABLE `framework_roles_permissions`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

    ALTER TABLE `framework_settings`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

    ALTER TABLE `framework_users`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

    ALTER TABLE `framework_users_activities`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

    ALTER TABLE `framework_users_notifications`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

    ALTER TABLE `framework_users_userids`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
    SET FOREIGN_KEY_CHECKS=1;
    CREATE TABLE IF NOT EXISTS `framework_settings_migrations` (`id` INT NOT NULL AUTO_INCREMENT , `script` TEXT NOT NULL , `executed_at` DATETIME NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;             ALTER TABLE `framework_settings_migrations` CHANGE `executed_at` `executed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
    COMMIT;