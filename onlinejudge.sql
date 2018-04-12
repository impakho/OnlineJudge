-- phpMyAdmin SQL Dump
-- version 4.0.10.20
-- https://www.phpmyadmin.net
--
-- 主机: 
-- 生成日期: 2018-01-01 10:00:00
-- 服务器版本: 5.5.50-log
-- PHP 版本: 5.3.29p1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `onlinejudge`
--

-- --------------------------------------------------------

--
-- 表的结构 `achieve`
--

CREATE TABLE IF NOT EXISTS `achieve` (
  `userid` int(10) unsigned DEFAULT NULL,
  `problemid` int(10) unsigned DEFAULT NULL,
  `time` bigint(20) unsigned DEFAULT NULL,
  KEY `userid` (`userid`) USING BTREE,
  KEY `problemid` (`problemid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

-- --------------------------------------------------------

--
-- 表的结构 `catalog`
--

CREATE TABLE IF NOT EXISTS `catalog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=7 ;

--
-- 转存表中的数据 `catalog`
--

INSERT INTO `catalog` (`id`, `name`) VALUES
(1, '基础知识 Basic'),
(2, '网页攻防 Web'),
(3, '逆向工程 Reverse'),
(4, '二进制漏洞 Pwn'),
(5, '密码学 Crypto'),
(6, '安全杂项 Misc');

-- --------------------------------------------------------

--
-- 表的结构 `problem`
--

CREATE TABLE IF NOT EXISTS `problem` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(10) unsigned DEFAULT '0',
  `time` bigint(20) unsigned DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `url` varchar(255) DEFAULT NULL,
  `score` int(10) unsigned DEFAULT '0',
  `flag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `type` (`type`) USING BTREE,
  KEY `time` (`time`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `problem`
--

INSERT INTO `problem` (`id`, `type`, `time`, `title`, `content`, `url`, `score`, `flag`) VALUES
(1, 1, 1514764800, '基础知识点（1）', 'Basic 很简单，flag 就隐藏在其中', '//file.SITE_DOMAIN/basic_1.jpg', 100, 'flag{demo_basic_1}'),
(2, 2, 1514768400, '网页基础（1）', 'Web 不简单，去问大佬拿 flag', '//ctf.SITE_DOMAIN/web1/', 100, 'flag{demo_web_1}');

-- --------------------------------------------------------

--
-- 表的结构 `rank`
--

CREATE TABLE IF NOT EXISTS `rank` (
  `id` int(10) unsigned DEFAULT NULL,
  `score` int(10) unsigned DEFAULT '0',
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `score` (`score`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `regtime` bigint(20) unsigned DEFAULT NULL,
  `logtime` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
