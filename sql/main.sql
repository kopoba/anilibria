SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE `session` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `time` bigint(20) NOT NULL,
  `ip` varbinary(16) NOT NULL,
  `info` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `login` varchar(20) NOT NULL,
  `nickname` varchar(20) DEFAULT NULL,
  `passwd` varchar(255) NOT NULL,
  `mail` varchar(254) NOT NULL,
  `2fa` varchar(16) DEFAULT NULL,
  `access` int(1) NOT NULL DEFAULT 1,
  `user_values` varchar(1024) DEFAULT NULL,
  `register_date` bigint(20) DEFAULT NULL,
  `last_activity` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `xbt_config` (
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `xbt_files` (
  `fid` int(11) NOT NULL,
  `info_hash` binary(20) NOT NULL,
  `leechers` int(11) NOT NULL DEFAULT 0,
  `seeders` int(11) NOT NULL DEFAULT 0,
  `completed` int(11) NOT NULL DEFAULT 0,
  `flags` int(11) NOT NULL DEFAULT 0,
  `mtime` int(11) NOT NULL,
  `ctime` int(11) NOT NULL,
  `rid` int(11) NOT NULL,
  `info` varchar(1024) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `xbt_files_users` (
  `fid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `active` tinyint(4) NOT NULL,
  `announced` int(11) NOT NULL,
  `completed` int(11) NOT NULL,
  `downloaded` bigint(20) UNSIGNED NOT NULL,
  `left` bigint(20) UNSIGNED NOT NULL,
  `uploaded` bigint(20) UNSIGNED NOT NULL,
  `mtime` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `xbt_users` (
  `uid` int(11) NOT NULL,
  `torrent_pass_version` int(11) NOT NULL DEFAULT 0,
  `downloaded` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `uploaded` bigint(20) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `session`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `hash` (`hash`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`),
  ADD UNIQUE KEY `mail` (`mail`);

ALTER TABLE `xbt_files`
  ADD PRIMARY KEY (`fid`),
  ADD UNIQUE KEY `info_hash` (`info_hash`),
  ADD KEY `rid` (`rid`);

ALTER TABLE `xbt_files_users`
  ADD UNIQUE KEY `fid` (`fid`,`uid`),
  ADD KEY `uid` (`uid`);

ALTER TABLE `xbt_users`
  ADD PRIMARY KEY (`uid`);

ALTER TABLE `session`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `xbt_files`
  MODIFY `fid` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `xbt_users`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT;
