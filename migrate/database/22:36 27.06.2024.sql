CREATE TABLE `framework_announcements` (
  `id` int(11) NOT NULL,
  `title` text NOT NULL,
  `text` text NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `framework_announcements`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `framework_announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
