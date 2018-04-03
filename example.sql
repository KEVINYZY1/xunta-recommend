CREATE TABLE `user` (
  `uid` int(11) NOT NULL,
  `email` varchar(40) COLLATE utf8_bin NOT NULL,
  `nickname` varchar(40) COLLATE utf8_bin NOT NULL,
  `sex` tinyint(3) NOT NULL,
  `photo_id` int(11) NOT NULL,
  `birthday` date NOT NULL,
  `register_time` datetime NOT NULL,
  `last_login_time` datetime NOT NULL,
  `description` varchar(500) COLLATE utf8_bin NOT NULL,
  `real_name` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `email_prefix` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `verify` tinyint(4) DEFAULT NULL,
  `phone_num` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `disabled` tinyint(4) DEFAULT '0',
  `area` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `job_type` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `receive_mail` tinyint(4) DEFAULT '1',
  `receive_gsm` tinyint(4) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `recommend` (
  `rid` int(11) NOT NULL,
  `for_uid` int(11) NOT NULL,
  `target_uid` int(11) NOT NULL,
  `voted` tinyint(1) DEFAULT NULL,
  `seen_time` datetime DEFAULT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `matching` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `target_uid` int(11) NOT NULL,
  `action` tinyint(3) NOT NULL,
  `ts` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `matched` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `uid2` int(11) NOT NULL,
  `seen` tinyint(1) NOT NULL,
  `seen_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `photo` (
  `photo_id` int(11) NOT NULL,
  `url` varchar(300) COLLATE utf8_bin NOT NULL,
  `upload_time` datetime NOT NULL,
  `uid` int(10) DEFAULT NULL,
  `verify` tinyint(4) DEFAULT NULL,
  `deleted` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- TODO: insert some example data
