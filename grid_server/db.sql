/*
MySQL Data Transfer
Source Host: localhost
Source Database: sampledb
Target Host: localhost
Target Database: sampledb
Date: 18/12/2013 16:04:00
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for books
-- ----------------------------
DROP TABLE IF EXISTS `books`;
CREATE TABLE `books` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(11) NOT NULL,
  `author` varchar(30) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` varchar(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records 
-- ----------------------------
INSERT INTO `books` VALUES ('1', 'book 1', 'Pushkin', '300', '50');
INSERT INTO `books` VALUES ('2', 'book 2', 'Blok', '2147', '43');
INSERT INTO `books` VALUES ('3', 'book 3', 'Tolstoy', '7488', '100');
INSERT INTO `books` VALUES ('4', 'book 4', 'df', '80', '68');
INSERT INTO `books` VALUES ('6', 'book 5', 'sdf', '547', '82');
INSERT INTO `books` VALUES ('7', 'book 6', 'Tolstoy', '93', '15');
INSERT INTO `books` VALUES ('8', 'book 7', 'sdf', '36', '35');
INSERT INTO `books` VALUES ('9', 'book 8', 'sdf', '22', '9');
INSERT INTO `books` VALUES ('10', 'book 9', 'tolkien', '44', '21');
INSERT INTO `books` VALUES ('24', 'book 10', 'werwe', '74', '100');
INSERT INTO `books` VALUES ('21', 'book 11', 'werwerwer', '32', '57');
INSERT INTO `books` VALUES ('38', 'book 12', '', '58', '74');
