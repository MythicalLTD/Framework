CREATE TABLE `framework_logs` (
  `id` int(11) NOT NULL,
  `type` enum('OTHER','CORE','DATABASE','PLUGIN','LOG') NOT NULL DEFAULT 'OTHER',
  `levels` enum('INFO','WARNING','ERROR','CRITICAL','OTHER') NOT NULL DEFAULT 'INFO',
  `message` text DEFAULT NULL,
  `formatted` text DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


ALTER TABLE `framework_logs`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `framework_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;