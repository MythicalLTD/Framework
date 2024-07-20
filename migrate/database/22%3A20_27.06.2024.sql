CREATE TABLE `framework_users_userids` (
  `id` int(11) NOT NULL,
  `uid` text NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
ALTER TABLE `framework_users_userids`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `framework_users_userids`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;