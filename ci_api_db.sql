-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le :  mer. 19 fév. 2020 à 14:35
-- Version du serveur :  10.1.38-MariaDB
-- Version de PHP :  7.2.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `ci_api_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `ci_capabilities`
--

CREATE TABLE `ci_capabilities` (
  `capability_id` int(11) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `label` varchar(255) NOT NULL,
  `deleted` tinyint(4) NOT NULL,
  `deleted_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `ci_capabilities`
--

INSERT INTO `ci_capabilities` (`capability_id`, `slug`, `label`, `deleted`, `deleted_at`, `created_at`, `updated_at`) VALUES
(5, 'have_all_capabilities', 'Avoir Toutes Les Capabilités', 0, '0000-00-00 00:00:00', '2019-12-07 08:11:58', '0000-00-00 00:00:00'),
(6, 'create_capability', 'Créer Des Capabilités', 0, '0000-00-00 00:00:00', '2019-12-28 20:53:13', '0000-00-00 00:00:00'),
(7, 'create_capabilities', 'Créer Des Capabilités', 0, '0000-00-00 00:00:00', '2020-01-04 10:49:23', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `ci_options`
--

CREATE TABLE `ci_options` (
  `oname` varchar(55) NOT NULL,
  `ovalue` text NOT NULL,
  `autoload` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `ci_options`
--

INSERT INTO `ci_options` (`oname`, `ovalue`, `autoload`) VALUES
('plugins', 'a:1:{s:3:\\\"cms\\\";s:7:\\\"enabled\\\";}', 'yes');

-- --------------------------------------------------------

--
-- Structure de la table `ci_posts`
--

CREATE TABLE `ci_posts` (
  `post_id` bigint(20) UNSIGNED NOT NULL,
  `post_author` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_excerpt` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_title` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'publish',
  `comment_status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `post_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `post_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content_filtered` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_parent` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `post_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'post',
  `comment_count` bigint(20) NOT NULL DEFAULT '0',
  `post_deleted` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ci_roles`
--

CREATE TABLE `ci_roles` (
  `role_id` bigint(20) NOT NULL,
  `slug` varchar(55) NOT NULL,
  `label` varchar(200) NOT NULL,
  `is_default` tinyint(4) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL,
  `deleted` tinyint(4) NOT NULL,
  `deleted_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `ci_roles`
--

INSERT INTO `ci_roles` (`role_id`, `slug`, `label`, `is_default`, `created_at`, `updated_at`, `deleted`, `deleted_at`) VALUES
(1, 'developer', 'Développeur', 0, '2020-01-05 09:58:56', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `ci_role_capabilities`
--

CREATE TABLE `ci_role_capabilities` (
  `role_slug` varchar(55) NOT NULL,
  `capability_slug` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `ci_role_capabilities`
--

INSERT INTO `ci_role_capabilities` (`role_slug`, `capability_slug`) VALUES
('developer', 'have_all_capabilities');

-- --------------------------------------------------------

--
-- Structure de la table `ci_users`
--

CREATE TABLE `ci_users` (
  `user_id` bigint(20) NOT NULL,
  `uname` varchar(55) NOT NULL,
  `fname` varchar(200) NOT NULL,
  `lname` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `psswd` varchar(255) NOT NULL,
  `reset_psswd_token` varchar(255) NOT NULL,
  `reset_psswd_validity` datetime NOT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL,
  `deleted` tinyint(4) NOT NULL,
  `deleted_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `ci_users`
--

INSERT INTO `ci_users` (`user_id`, `uname`, `fname`, `lname`, `email`, `psswd`, `reset_psswd_token`, `reset_psswd_validity`, `status`, `created_at`, `updated_at`, `deleted`, `deleted_at`) VALUES
(1, 'root', 'Syga', 'Label', 'r.aroandrialova@gmail.com', '$2y$10$eq4PfWNb3.5rYUarL.duCewWmwSCrpfeB0R0SjAD6POxKhxfXTmvm', '', '0000-00-00 00:00:00', 'active', '2020-01-04 19:12:51', '2020-01-04 19:13:52', 0, '0000-00-00 00:00:00'),
(4, 'test', 'Syga', 'Test', 'r.aroandrialov@gmail.com', '$2y$10$9O.WThKgOFB3D/WKTdaa2eydfj44.hUd3kLwOYvDB2QIWfWJxLYFW', 'QEAyMDIwLTAxLTIxIDEyOjMwOjU2', '2020-01-21 12:30:56', 'pending', '2020-01-19 12:30:56', '2020-01-19 12:30:56', 0, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `ci_user_roles`
--

CREATE TABLE `ci_user_roles` (
  `user_id` bigint(20) NOT NULL,
  `role_slug` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `ci_user_roles`
--

INSERT INTO `ci_user_roles` (`user_id`, `role_slug`) VALUES
(1, 'developer'),
(4, 'editor');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `ci_capabilities`
--
ALTER TABLE `ci_capabilities`
  ADD PRIMARY KEY (`capability_id`),
  ADD UNIQUE KEY `capability_slug` (`slug`);

--
-- Index pour la table `ci_options`
--
ALTER TABLE `ci_options`
  ADD UNIQUE KEY `option name` (`oname`);

--
-- Index pour la table `ci_posts`
--
ALTER TABLE `ci_posts`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `post_name` (`post_name`(191)),
  ADD KEY `type_status_date` (`post_type`,`post_status`,`post_date`,`post_id`),
  ADD KEY `post_parent` (`post_parent`),
  ADD KEY `post_author` (`post_author`);

--
-- Index pour la table `ci_roles`
--
ALTER TABLE `ci_roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Index pour la table `ci_users`
--
ALTER TABLE `ci_users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `ci_capabilities`
--
ALTER TABLE `ci_capabilities`
  MODIFY `capability_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `ci_posts`
--
ALTER TABLE `ci_posts`
  MODIFY `post_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `ci_roles`
--
ALTER TABLE `ci_roles`
  MODIFY `role_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `ci_users`
--
ALTER TABLE `ci_users`
  MODIFY `user_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
