CREATE TABLE IF NOT EXISTS `#__cwmprayer` (
  id               INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  requesterid      INT(11)          NOT NULL DEFAULT '0',
  requester        VARCHAR(50)      NOT NULL DEFAULT '',
  request          TEXT             NOT NULL,
  date             DATE             NOT NULL DEFAULT '0000-00-00',
  time             TIME             NOT NULL DEFAULT '00:00:00',
  publishstate     SMALLINT(1)      NOT NULL DEFAULT '0',
  archivestate     SMALLINT(1)      NOT NULL DEFAULT '0',
  displaystate     SMALLINT(1)      NOT NULL DEFAULT '0',
  sendto           DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00',
  email            VARCHAR(50)      NOT NULL DEFAULT '',
  adminsendto      DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00',
  checked_out_time DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00',
  checked_out      INT(11)          NOT NULL DEFAULT '0',
  sessionid        VARCHAR(50)      NOT NULL DEFAULT '',
  title            VARCHAR(100)     NOT NULL DEFAULT '',
  topic            INT(11)          NOT NULL DEFAULT '0',
  hits             INT(11)          NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  KEY `idx_published`  ('publishstate'),
  KEY `idx_requesterid`  ('requesterid'),
  KEY `idx_checkedout`  ('checked_out')
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `#__cwmprayer_subscribe` (
  id        INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  email     VARCHAR(50)      NOT NULL DEFAULT '',
  date      DATE             NOT NULL DEFAULT '0000-00-00',
  approved  SMALLINT(1)      NOT NULL DEFAULT '0',
  sessionid VARCHAR(50)      NOT NULL DEFAULT '',
  PRIMARY KEY (id)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `#__cwmprayer_devotions` (
  id               INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  name             VARCHAR(200)     NOT NULL DEFAULT '',
  feed             VARCHAR(200)     NOT NULL DEFAULT '',
  published        SMALLINT(1)      NOT NULL DEFAULT '0',
  catid            INT(11)          NOT NULL DEFAULT '0',
  created_by       INT(10)          NOT NULL DEFAULT '0',
  publish_up       DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00',
  publish_down     DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00',
  checked_out_time DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00',
  checked_out      INT(11)          NOT NULL DEFAULT '0',
  ordering         INT(11)          NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  KEY `idx_published`  ('published'),
  KEY `idx_catid`  ('catid'),
  KEY `idx_createdby`  ('created_by'),
  KEY `idx_checkedout`  ('checked_out')
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

INSERT IGNORE INTO `#__cwmprayer_devotions` (`id`, `name`, `feed`, `published`, `catid`, `created_by`, `publish_up`, `publish_down`, `checked_out`, `checked_out_time`, `ordering`)
VALUES
  (1, 'Our Daily Bread Daily Devotional', 'http://www.rbc.org/rss.ashx?id=50398', 1, 0, 0, '0000-00-00 00:00:00',
      '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 1),
  (2, 'My Utmost for His Highest Daily Devotional', 'http://www.rbc.org/myUtmost.rss', 1, 0, 0, '0000-00-00 00:00:00',
      '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00',
      2);


CREATE TABLE IF NOT EXISTS `#__cwmprayer_links` (
  id               INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  name             VARCHAR(200)     NOT NULL DEFAULT '',
  url              VARCHAR(200)     NOT NULL DEFAULT '',
  alias            VARCHAR(200)     NOT NULL DEFAULT '',
  descrip          TEXT             NOT NULL,
  published        SMALLINT(1)      NOT NULL DEFAULT '0',
  catid            INT(11)          NOT NULL,
  checked_out_time DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00',
  checked_out      INT(11)          NOT NULL DEFAULT '0',
  ordering         INT(11)          NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  KEY `idx_catid`  ('catid'),
  KEY `idx_published`  ('published'),
  KEY `idx_checkedout`  ('checked_out')
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

INSERT IGNORE INTO `#__cwmprayer_links` (`id`, `name`, `url`, `alias`, `descrip`, `published`, `catid`, `checked_out`, `checked_out_time`, `ordering`)
VALUES
  (1, 'Max Lucado', 'http://www.maxlucado.com', 'Max Lucado', 'UpWords: The Teaching Ministry of Max Lucado', 1, 0, 0,
   '0000-00-00 00:00:00', 1),
  (2, 'Upper Room', 'http://www.upperroom.org', 'Upper Room', 'Upper Room Ministries', 1, 0, 0, '0000-00-00 00:00:00',
   2),
  (3, 'Samaritan\'s Purse', 'http://www.samaritanspurse.org', 'Samaritan\'s Purse',
   'Samaritan\'s Purse International Relief', 1, 0, 0, '0000-00-00 00:00:00', 3),
  (4, 'Heifer International', 'http://www.heifer.org', 'Heifer International',
   'Heifer International, Ending Hunger; Caring for the Earth', 1, 0, 0, '0000-00-00 00:00:00', 4);