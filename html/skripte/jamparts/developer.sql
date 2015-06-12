/*
Navicat MySQL Data Transfer

Source Server         : DMC
Source Server Version : 50537
Source Host           : design-motorcycles.de:3306
Source Database       : developer

Target Server Type    : MYSQL
Target Server Version : 50537
File Encoding         : 65001

Date: 2015-03-04 19:35:23
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for media
-- ----------------------------
DROP TABLE IF EXISTS `media`;
CREATE TABLE `media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prod_id` varchar(501) DEFAULT NULL,
  `image` text,
  `media_type` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=408 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for product
-- ----------------------------
DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prod_id` varchar(100) DEFAULT NULL,
  `url_id` int(11) DEFAULT NULL,
  `h1` text,
  `h2` text,
  `description` longtext,
  `price` text,
  `ek` text,
  `color` varchar(501) DEFAULT NULL,
  `artnr` varchar(501) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=793 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for tmp
-- ----------------------------
DROP TABLE IF EXISTS `tmp`;
CREATE TABLE `tmp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url_id` int(11) DEFAULT NULL,
  `html` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4630 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for urls
-- ----------------------------
DROP TABLE IF EXISTS `urls`;
CREATE TABLE `urls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(501) DEFAULT NULL,
  `provider` varchar(501) DEFAULT NULL,
  `brand` varchar(501) DEFAULT NULL,
  `model` varchar(501) DEFAULT NULL,
  `yearofbuild` varchar(501) DEFAULT NULL,
  `url` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43941 DEFAULT CHARSET=latin1;
