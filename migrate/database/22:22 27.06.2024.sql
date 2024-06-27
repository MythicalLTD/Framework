CREATE TABLE `framework_users_notifications` (
  `id` int(11) NOT NULL,
  `user_id` text NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `framework_users_notifications`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `framework_users_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;