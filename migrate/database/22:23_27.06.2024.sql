CREATE TABLE `framework_users_activities` (
  `id` int(11) NOT NULL,
  `user_id` text NOT NULL,
  `username` text NOT NULL,
  `description` text NOT NULL,
  `action` text NOT NULL,
  `ip_address` text NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `framework_users_activities`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `framework_users_activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
