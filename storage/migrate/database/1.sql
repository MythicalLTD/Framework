SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+02:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `framework`
--

-- --------------------------------------------------------

--
-- Table structure for table `framework_announcements`
--

CREATE TABLE `framework_announcements` (
  `id` int(11) NOT NULL,
  `title` text NOT NULL,
  `text` text NOT NULL,
  `deleted` enum('false','true') NOT NULL DEFAULT 'false',
  `locked` enum('false','true') NOT NULL DEFAULT 'false',
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `framework_announcements_social`
--

CREATE TABLE `framework_announcements_social` (
  `id` int(11) NOT NULL,
  `announcement_id` int(11) NOT NULL,
  `user_uuid` text NOT NULL,
  `type` enum('read','like','dislike') NOT NULL DEFAULT 'read',
  `deleted` enum('false','true') NOT NULL DEFAULT 'false',
  `locked` enum('false','true') NOT NULL DEFAULT 'false',
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `framework_backups`
--

CREATE TABLE `framework_backups` (
  `id` int(11) NOT NULL,
  `backup_path` text NOT NULL,
  `backup_status` enum('IN_PROGRESS','DONE','FAILED') NOT NULL DEFAULT 'IN_PROGRESS',
  `deleted` enum('false','true') NOT NULL DEFAULT 'false',
  `locked` enum('false','true') NOT NULL DEFAULT 'false',
  `backup_date_start` datetime NOT NULL DEFAULT current_timestamp(),
  `backup_date_end` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `framework_firewall`
--

CREATE TABLE `framework_firewall` (
  `id` int(11) NOT NULL,
  `ip` text NOT NULL,
  `uuid` text DEFAULT NULL,
  `action` enum('drop','allow','none') NOT NULL DEFAULT 'none',
  `deleted` enum('false','true') NOT NULL DEFAULT 'false',
  `locked` enum('false','true') NOT NULL DEFAULT 'false',
  `date_first_seen` datetime NOT NULL DEFAULT current_timestamp(),
  `date_last_seen` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `framework_logs`
--

CREATE TABLE `framework_logs` (
  `id` int(11) NOT NULL,
  `l_type` enum('OTHER','CORE','DATABASE','PLUGIN','LOG','LANGUAGE','BACKUP') NOT NULL DEFAULT 'OTHER',
  `levels` enum('INFO','WARNING','ERROR','CRITICAL','OTHER') NOT NULL DEFAULT 'INFO',
  `message` text DEFAULT NULL,
  `formatted` text DEFAULT NULL,
  `deleted` enum('false','true') NOT NULL DEFAULT 'false',
  `locked` enum('false','true') NOT NULL DEFAULT 'false',
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `framework_plugins`
--

CREATE TABLE `framework_plugins` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `homepage` text DEFAULT NULL,
  `require` text DEFAULT NULL,
  `license` text DEFAULT NULL,
  `stability` enum('alpha','beta','dev','rc','stable') NOT NULL DEFAULT 'dev',
  `authors` text NOT NULL,
  `support` text DEFAULT NULL,
  `funding` text DEFAULT NULL,
  `version` text NOT NULL,
  `enabled` enum('false','true') NOT NULL DEFAULT 'false',
  `isInstalled` enum('false','true') NOT NULL DEFAULT 'false',
  `deleted` enum('false','true') NOT NULL DEFAULT 'false',
  `locked` enum('false','true') NOT NULL DEFAULT 'false',
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `framework_roles`
--

CREATE TABLE `framework_roles` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `weight` int(11) NOT NULL DEFAULT 1,
  `deleted` enum('false','true') NOT NULL DEFAULT 'false',
  `locked` enum('false','true') NOT NULL DEFAULT 'false',
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `framework_roles`
--

INSERT INTO `framework_roles` (`id`, `name`, `weight`, `deleted`, `locked`, `date`) VALUES
(1, 'Default', 1, 'false', 'false', '2024-07-20 06:52:48'),
(2, 'Admin', 2, 'false', 'false', '2024-07-20 06:52:48'),
(3, 'Administrator', 3, 'false', 'false', '2024-07-20 06:52:48');

-- --------------------------------------------------------

--
-- Table structure for table `framework_roles_permissions`
--

CREATE TABLE `framework_roles_permissions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL DEFAULT 1,
  `permission` text NOT NULL,
  `deleted` enum('false','true') NOT NULL DEFAULT 'false',
  `locked` enum('false','true') NOT NULL DEFAULT 'false',
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `framework_roles_permissions`
--

INSERT INTO `framework_roles_permissions` (`id`, `role_id`, `permission`, `deleted`, `locked`, `date`) VALUES
(1, 1, 'mythicalframework.default', 'false', 'false', '2024-07-20 06:52:48'),
(2, 2, 'mythicalframework.admin', 'false', 'false', '2024-07-20 06:52:48'),
(3, 3, 'mythicalframework.administrator', 'false', 'false', '2024-07-20 06:52:48');

-- --------------------------------------------------------

--
-- Table structure for table `framework_settings`
--

CREATE TABLE `framework_settings` (
  `id` int(11) NOT NULL,
  `scategory` text NOT NULL,
  `skey` text NOT NULL,
  `svalue` text NOT NULL,
  `deleted` enum('false','true') NOT NULL DEFAULT 'false',
  `locked` enum('false','true') NOT NULL DEFAULT 'false',
  `last_modified` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `framework_settings_migrations`
--

CREATE TABLE `framework_settings_migrations` (
  `id` int(11) NOT NULL,
  `script` text NOT NULL,
  `deleted` enum('false','true') NOT NULL DEFAULT 'false',
  `locked` enum('false','true') NOT NULL DEFAULT 'false',
  `executed_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `framework_users`
--

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
  `verified` enum('false','true') NOT NULL DEFAULT 'false',
  `deleted` enum('false','true') NOT NULL DEFAULT 'false',
  `locked` enum('false','true') NOT NULL DEFAULT 'false',
  `last_seen` datetime NOT NULL DEFAULT current_timestamp(),
  `first_seen` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `framework_users_activities`
--

CREATE TABLE `framework_users_activities` (
  `id` int(11) NOT NULL,
  `user_id` text NOT NULL,
  `description` text NOT NULL,
  `action` text NOT NULL,
  `ip_address` text NOT NULL,
  `deleted` enum('false','true') NOT NULL DEFAULT 'false',
  `locked` enum('false','true') NOT NULL DEFAULT 'false',
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `framework_users_notifications`
--

CREATE TABLE `framework_users_notifications` (
  `id` int(11) NOT NULL,
  `user_id` text NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `deleted` enum('false','true') NOT NULL DEFAULT 'false',
  `locked` enum('false','true') NOT NULL DEFAULT 'false',
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `framework_users_notifications_reads`
--

CREATE TABLE `framework_users_notifications_reads` (
  `id` int(11) NOT NULL,
  `user_uuid` text NOT NULL,
  `notification_id` int(11) NOT NULL,
  `deleted` enum('false','true') NOT NULL DEFAULT 'false',
  `locked` enum('false','true') NOT NULL DEFAULT 'false',
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `framework_users_userids`
--

CREATE TABLE `framework_users_userids` (
  `id` int(11) NOT NULL,
  `uid` text NOT NULL,
  `deleted` enum('false','true') NOT NULL DEFAULT 'false',
  `locked` enum('false','true') NOT NULL DEFAULT 'false',
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `framework_announcements`
--
ALTER TABLE `framework_announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `framework_announcements_social`
--
ALTER TABLE `framework_announcements_social`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `framework_backups`
--
ALTER TABLE `framework_backups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `framework_firewall`
--
ALTER TABLE `framework_firewall`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `framework_logs`
--
ALTER TABLE `framework_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `framework_plugins`
--
ALTER TABLE `framework_plugins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `framework_roles`
--
ALTER TABLE `framework_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `framework_roles_permissions`
--
ALTER TABLE `framework_roles_permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `framework_settings`
--
ALTER TABLE `framework_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `framework_settings_migrations`
--
ALTER TABLE `framework_settings_migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `framework_users`
--
ALTER TABLE `framework_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `framework_users_activities`
--
ALTER TABLE `framework_users_activities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `framework_users_notifications`
--
ALTER TABLE `framework_users_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `framework_users_notifications_reads`
--
ALTER TABLE `framework_users_notifications_reads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `framework_users_userids`
--
ALTER TABLE `framework_users_userids`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `framework_announcements`
--
ALTER TABLE `framework_announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `framework_announcements_social`
--
ALTER TABLE `framework_announcements_social`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `framework_backups`
--
ALTER TABLE `framework_backups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `framework_firewall`
--
ALTER TABLE `framework_firewall`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `framework_logs`
--
ALTER TABLE `framework_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `framework_plugins`
--
ALTER TABLE `framework_plugins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `framework_roles`
--
ALTER TABLE `framework_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `framework_roles_permissions`
--
ALTER TABLE `framework_roles_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `framework_settings`
--
ALTER TABLE `framework_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `framework_settings_migrations`
--
ALTER TABLE `framework_settings_migrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `framework_users`
--
ALTER TABLE `framework_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `framework_users_activities`
--
ALTER TABLE `framework_users_activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `framework_users_notifications`
--
ALTER TABLE `framework_users_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `framework_users_notifications_reads`
--
ALTER TABLE `framework_users_notifications_reads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `framework_users_userids`
--
ALTER TABLE `framework_users_userids`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;