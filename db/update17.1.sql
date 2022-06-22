CREATE TABLE `zt_riskissue` (
  `risk` mediumint(8) unsigned NOT NULL,
  `issue` mediumint(8) unsigned NOT NULL,
  UNIQUE KEY `risk_issue` (`risk`,`issue`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `zt_projectadmin` (
  `group` smallint(6) NOT NULL,
  `account` char(30) NOT NULL,
  `programs` text NOT NULL,
  `projects` text NOT NULL,
  `products` text NOT NULL,
  `executions` text NOT NULL,
  UNIQUE KEY `group_account` (`group`, `account`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
