SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE `session` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `time` bigint(20) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `info` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `login` varchar(20) NOT NULL,
  `passwd` varchar(255) NOT NULL,
  `mail` varchar(254) NOT NULL,
  `2fa` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `session`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `hash` (`hash`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`),
  ADD UNIQUE KEY `mail` (`mail`);
