
create table users (
  id int not null auto_increment primary key,
  email varchar(255) unique,
  username varchar(255),
  password varchar(255),
  created datetime DEFAULT CURRENT_TIMESTAMP,
  modified datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  role tinyint(3) unsigned DEFAULT 0 COMMENT 'ユーザ権限：0/無料会員 1/有料会員',
);

CREATE TABLE `enq1` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `gender` tinyint(3) unsigned DEFAULT NULL,
 `old` tinyint(3) unsigned DEFAULT NULL,
 `taste` tinyint(3) unsigned DEFAULT NULL,
 `opinion` varchar(255) DEFAULT NULL,
 `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8

CREATE TABLE `admins` (
 `admin` varchar(255) DEFAULT NULL,
 `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8

CREATE TABLE `reminder_token` (
 `token` varbinary(255) NOT NULL COMMENT '識別するためのID',
 `user_id` int(11) unsigned NOT NULL COMMENT 'ユーザID',
 `created` datetime NOT NULL COMMENT '作成日時',
 PRIMARY KEY (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8

CREATE TABLE `user_login_lock` (
 `user_id` int(11) unsigned NOT NULL COMMENT '識別するためのID',
 `error_count` tinyint(3) unsigned NOT NULL COMMENT 'ログインエラーの回数(ログイン成功したら一度リセット)',
 `lock_time` datetime NOT NULL COMMENT 'ロック時間。0000-00-00 00:00:00 なら「ロックされてない」',
 PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
