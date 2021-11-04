/*
SQLyog Community v12.1 (32 bit)
MySQL - 10.5.8-MariaDB-1:10.5.8+maria~focal : Database - db_marketing
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`db_marketing` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

USE `db_marketing`;

/*Table structure for table `app_array` */

DROP TABLE IF EXISTS `app_array`;

CREATE TABLE `app_array` (
  `processflag` varchar(5) DEFAULT NULL,
  `insert_platform` varchar(3) DEFAULT '1',
  `insert_user` varchar(15) DEFAULT NULL,
  `insert_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `update_platform` varchar(3) DEFAULT NULL,
  `update_user` varchar(15) DEFAULT NULL,
  `update_date` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `delete_platform` varchar(3) DEFAULT NULL,
  `delete_user` varchar(15) DEFAULT NULL,
  `delete_date` timestamp NULL DEFAULT NULL,
  `cru_csvnote` varchar(500) DEFAULT NULL,
  `is_erpsent` varchar(3) DEFAULT '0',
  `is_enabled` varchar(3) DEFAULT '1',
  `i` int(11) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_erp` varchar(25) DEFAULT NULL,
  `type` varchar(15) DEFAULT NULL,
  `id_tosave` varchar(25) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `id_owner` int(11) DEFAULT NULL COMMENT 'propietario del tipo o categoria',
  `order_by` int(5) NOT NULL DEFAULT 100,
  `uuid` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

/*Data for the table `app_array` */

insert  into `app_array`(`processflag`,`insert_platform`,`insert_user`,`insert_date`,`update_platform`,`update_user`,`update_date`,`delete_platform`,`delete_user`,`delete_date`,`cru_csvnote`,`is_erpsent`,`is_enabled`,`i`,`id`,`code_erp`,`type`,`id_tosave`,`description`,`id_owner`,`order_by`,`uuid`) values (NULL,'1',NULL,'2021-11-01 19:25:04',NULL,NULL,'2021-11-01 19:26:18',NULL,NULL,NULL,NULL,'0','1',NULL,1,'en','language',NULL,'english',-1,100,NULL),(NULL,'1',NULL,'2021-11-01 19:26:29',NULL,NULL,'2021-11-01 19:26:33',NULL,NULL,NULL,NULL,'0','1',NULL,2,'es','language',NULL,'spanish',-1,100,NULL),(NULL,'1',NULL,'2021-11-01 19:27:00',NULL,NULL,'2021-11-01 19:27:26',NULL,NULL,NULL,NULL,'0','1',NULL,3,'nl','language',NULL,'dutch',-1,100,NULL);

/*Table structure for table `app_business_data` */

DROP TABLE IF EXISTS `app_business_data`;

CREATE TABLE `app_business_data` (
  `processflag` varchar(5) DEFAULT NULL,
  `insert_platform` varchar(3) DEFAULT '1',
  `insert_user` varchar(15) DEFAULT NULL,
  `insert_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `update_platform` varchar(3) DEFAULT NULL,
  `update_user` varchar(15) DEFAULT NULL,
  `update_date` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `delete_platform` varchar(3) DEFAULT NULL,
  `delete_user` varchar(15) DEFAULT NULL,
  `delete_date` timestamp NULL DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL COMMENT 'base_user.id permissions',
  `url_logo_1` varchar(100) DEFAULT NULL COMMENT 'logo horizontal',
  `url_logo_2` varchar(100) DEFAULT NULL COMMENT 'logo cuadrado',
  `url_logo_3` varchar(100) DEFAULT NULL COMMENT 'logo gigante',
  `url_favicon` varchar(100) DEFAULT NULL COMMENT 'favicon',
  `head_bgcolor` varchar(10) DEFAULT NULL,
  `head_color` varchar(10) DEFAULT NULL,
  `head_bgimage` varchar(10) DEFAULT NULL,
  `body_bgcolor` varchar(10) DEFAULT NULL,
  `body_color` varchar(10) DEFAULT NULL,
  `body_bgimage` varchar(100) DEFAULT NULL COMMENT 'imagen de fondo por defecto',
  `site` varchar(100) DEFAULT NULL,
  `url_social_fb` varchar(100) DEFAULT NULL,
  `url_social_ig` varchar(100) DEFAULT NULL,
  `url_social_twitter` varchar(100) DEFAULT NULL,
  `url_social_tiktok` varchar(100) DEFAULT NULL,
  `uuid` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `base_user_permissions_id_user_uindex` (`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

/*Data for the table `app_business_data` */

/*Table structure for table `app_ip_untracked` */

DROP TABLE IF EXISTS `app_ip_untracked`;

CREATE TABLE `app_ip_untracked` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `insert_date` timestamp NULL DEFAULT current_timestamp(),
  `update_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `remote_ip` varchar(100) NOT NULL,
  `country` varchar(50) DEFAULT NULL,
  `whois` varchar(200) DEFAULT NULL,
  `comment` varchar(200) DEFAULT NULL,
  `is_enabled` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `remote_ip` (`remote_ip`),
  KEY `is_enabled` (`is_enabled`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

/*Data for the table `app_ip_untracked` */

/*Table structure for table `app_promotion` */

DROP TABLE IF EXISTS `app_promotion`;

CREATE TABLE `app_promotion` (
  `processflag` varchar(5) DEFAULT NULL,
  `insert_platform` varchar(3) DEFAULT '1',
  `insert_user` varchar(15) DEFAULT NULL,
  `insert_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `update_platform` varchar(3) DEFAULT NULL,
  `update_user` varchar(15) DEFAULT NULL,
  `update_date` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `delete_platform` varchar(3) DEFAULT NULL,
  `delete_user` varchar(15) DEFAULT NULL,
  `delete_date` timestamp NULL DEFAULT NULL,
  `cru_csvnote` varchar(500) DEFAULT NULL,
  `is_erpsent` varchar(3) DEFAULT '0',
  `is_enabled` varchar(3) DEFAULT '1',
  `i` int(11) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_erp` varchar(25) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `slug` varchar(250) DEFAULT NULL,
  `content` varchar(2000) DEFAULT NULL,
  `id_owner` int(11) DEFAULT NULL COMMENT 'a quien pertenece la promo',
  `id_type` int(11) DEFAULT NULL,
  `date_from` datetime DEFAULT NULL,
  `date_to` datetime DEFAULT NULL,
  `url_social` varchar(250) DEFAULT NULL COMMENT 'url en la red social',
  `url_design` varchar(250) DEFAULT NULL COMMENT 'diseño por defecto',
  `is_active` tinyint(4) DEFAULT 0,
  `invested` decimal(10,3) DEFAULT 0.000,
  `returned` decimal(10,3) DEFAULT 0.000,
  `notes` varchar(300) DEFAULT NULL,
  `uuid` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

/*Data for the table `app_promotion` */

/*Table structure for table `app_promotion_array` */

DROP TABLE IF EXISTS `app_promotion_array`;

CREATE TABLE `app_promotion_array` (
  `processflag` varchar(5) DEFAULT NULL,
  `insert_platform` varchar(3) DEFAULT '1',
  `insert_user` varchar(15) DEFAULT NULL,
  `insert_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `update_platform` varchar(3) DEFAULT NULL,
  `update_user` varchar(15) DEFAULT NULL,
  `update_date` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `delete_platform` varchar(3) DEFAULT NULL,
  `delete_user` varchar(15) DEFAULT NULL,
  `delete_date` timestamp NULL DEFAULT NULL,
  `cru_csvnote` varchar(500) DEFAULT NULL,
  `is_erpsent` varchar(3) DEFAULT '0',
  `is_enabled` varchar(3) DEFAULT '1',
  `i` int(11) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_erp` varchar(25) DEFAULT NULL,
  `type` varchar(15) DEFAULT NULL,
  `id_tosave` varchar(25) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `id_owner` int(11) DEFAULT NULL COMMENT 'propietario del tipo o categoria',
  `order_by` int(5) NOT NULL DEFAULT 100,
  `uuid` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

/*Data for the table `app_promotion_array` */

insert  into `app_promotion_array`(`processflag`,`insert_platform`,`insert_user`,`insert_date`,`update_platform`,`update_user`,`update_date`,`delete_platform`,`delete_user`,`delete_date`,`cru_csvnote`,`is_erpsent`,`is_enabled`,`i`,`id`,`code_erp`,`type`,`id_tosave`,`description`,`id_owner`,`order_by`,`uuid`) values (NULL,'1',NULL,'2020-07-01 07:35:58',NULL,NULL,'2020-07-01 07:35:58',NULL,NULL,NULL,NULL,'0','1',NULL,1,NULL,'generic',NULL,'Generic',1,100,NULL);

/*Table structure for table `app_promotion_urls` */

DROP TABLE IF EXISTS `app_promotion_urls`;

CREATE TABLE `app_promotion_urls` (
  `processflag` varchar(5) DEFAULT NULL,
  `insert_platform` varchar(3) DEFAULT '1',
  `insert_user` varchar(15) DEFAULT NULL,
  `insert_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `update_platform` varchar(3) DEFAULT NULL,
  `update_user` varchar(15) DEFAULT NULL,
  `update_date` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `delete_platform` varchar(3) DEFAULT NULL,
  `delete_user` varchar(15) DEFAULT NULL,
  `delete_date` timestamp NULL DEFAULT NULL,
  `cru_csvnote` varchar(500) DEFAULT NULL,
  `is_erpsent` varchar(3) DEFAULT '0',
  `is_enabled` varchar(3) DEFAULT '1',
  `i` int(11) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_erp` varchar(25) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `id_promotion` int(11) NOT NULL,
  `id_type` int(11) NOT NULL DEFAULT 1 COMMENT 'promotion_array: fb|web|youtube,...',
  `design` varchar(250) DEFAULT NULL COMMENT 'url de la creatividad',
  `notes` varchar(300) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT 1,
  `uuid` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

/*Data for the table `app_promotion_urls` */

/*Table structure for table `app_promotion_user` */

DROP TABLE IF EXISTS `app_promotion_user`;

CREATE TABLE `app_promotion_user` (
  `processflag` varchar(5) DEFAULT NULL,
  `insert_platform` varchar(3) DEFAULT '1',
  `insert_user` varchar(15) DEFAULT NULL,
  `insert_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `update_platform` varchar(3) DEFAULT NULL,
  `update_user` varchar(15) DEFAULT NULL,
  `update_date` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `delete_platform` varchar(3) DEFAULT NULL,
  `delete_user` varchar(15) DEFAULT NULL,
  `delete_date` timestamp NULL DEFAULT NULL,
  `cru_csvnote` varchar(500) DEFAULT NULL,
  `is_erpsent` varchar(3) DEFAULT '0',
  `is_enabled` varchar(3) DEFAULT '1',
  `i` int(11) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_erp` varchar(25) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `id_language` int(11) DEFAULT 1 COMMENT 'por defecto ingles',
  `id_country` int(11) DEFAULT NULL,
  `phone1` varchar(20) NOT NULL COMMENT 'telefono',
  `email` varchar(100) NOT NULL,
  `birthdate` datetime DEFAULT NULL COMMENT 'comprobar mayoria de edad',
  `name1` varchar(15) NOT NULL,
  `name2` varchar(15) DEFAULT NULL,
  `id_gender` varchar(5) DEFAULT NULL COMMENT 'app_array.type=gender',
  `m1` int(5) DEFAULT NULL,
  `m2` int(5) DEFAULT NULL,
  `m3` int(5) DEFAULT NULL,
  `m4` int(5) DEFAULT NULL,
  `m5` int(5) DEFAULT NULL,
  `uuid` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

/*Data for the table `app_promotion_user` */

/*Table structure for table `app_promotions_subscriptions` */

DROP TABLE IF EXISTS `app_promotions_subscriptions`;

CREATE TABLE `app_promotions_subscriptions` (
  `processflag` varchar(5) DEFAULT NULL,
  `insert_platform` varchar(3) DEFAULT '1',
  `insert_user` varchar(15) DEFAULT NULL,
  `insert_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `update_platform` varchar(3) DEFAULT NULL,
  `update_user` varchar(15) DEFAULT NULL,
  `update_date` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `delete_platform` varchar(3) DEFAULT NULL,
  `delete_user` varchar(15) DEFAULT NULL,
  `delete_date` timestamp NULL DEFAULT NULL,
  `cru_csvnote` varchar(500) DEFAULT NULL,
  `is_erpsent` varchar(3) DEFAULT '0',
  `is_enabled` varchar(3) DEFAULT '1',
  `i` int(11) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_erp` varchar(25) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `id_promotion` int(11) NOT NULL,
  `id_promouser` int(11) NOT NULL COMMENT 'app_promotion_user.id',
  `date_subs` datetime DEFAULT NULL COMMENT 'cuando se inscribe',
  `url_ref` varchar(250) DEFAULT NULL COMMENT 'de donde llega',
  `code1` varchar(15) DEFAULT NULL COMMENT 'codigo unico de inscripción',
  `date_confirm` timestamp NULL DEFAULT NULL,
  `is_confirmed` tinyint(4) DEFAULT 0,
  `date_exec` datetime DEFAULT NULL,
  `notes` varchar(300) DEFAULT NULL,
  `uuid` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

/*Data for the table `app_promotions_subscriptions` */

/*Table structure for table `base_array` */

DROP TABLE IF EXISTS `base_array`;

CREATE TABLE `base_array` (
  `processflag` varchar(5) DEFAULT NULL,
  `insert_platform` varchar(3) DEFAULT '1',
  `insert_user` varchar(15) DEFAULT NULL,
  `insert_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `update_platform` varchar(3) DEFAULT NULL,
  `update_user` varchar(15) DEFAULT NULL,
  `update_date` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `delete_platform` varchar(3) DEFAULT NULL,
  `delete_user` varchar(15) DEFAULT NULL,
  `delete_date` timestamp NULL DEFAULT NULL,
  `cru_csvnote` varchar(500) DEFAULT NULL,
  `is_erpsent` varchar(3) DEFAULT '0',
  `is_enabled` varchar(3) DEFAULT '1',
  `i` int(11) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_erp` varchar(25) DEFAULT NULL,
  `type` varchar(15) DEFAULT NULL,
  `id_tosave` varchar(25) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `order_by` int(5) NOT NULL DEFAULT 100,
  `uuid` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

/*Data for the table `base_array` */

insert  into `base_array`(`processflag`,`insert_platform`,`insert_user`,`insert_date`,`update_platform`,`update_user`,`update_date`,`delete_platform`,`delete_user`,`delete_date`,`cru_csvnote`,`is_erpsent`,`is_enabled`,`i`,`id`,`code_erp`,`type`,`id_tosave`,`description`,`order_by`,`uuid`) values (NULL,'1',NULL,'2021-11-02 18:53:35',NULL,NULL,'2021-11-02 18:53:35',NULL,NULL,NULL,NULL,'0','1',NULL,1,NULL,'profile',NULL,'root',100,NULL),(NULL,'1',NULL,'2021-11-02 18:53:45',NULL,NULL,'2021-11-02 18:53:45',NULL,NULL,NULL,NULL,'0','1',NULL,2,NULL,'profile',NULL,'admin',100,NULL),(NULL,'1',NULL,'2021-11-02 18:55:42',NULL,NULL,'2021-11-02 19:05:30',NULL,NULL,NULL,NULL,'0','1',NULL,3,NULL,'business',NULL,'business',100,NULL);

/*Table structure for table `base_user` */

DROP TABLE IF EXISTS `base_user`;

CREATE TABLE `base_user` (
  `processflag` varchar(5) DEFAULT NULL,
  `insert_platform` varchar(3) DEFAULT '1',
  `insert_user` varchar(15) DEFAULT NULL,
  `insert_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `update_platform` varchar(3) DEFAULT NULL,
  `update_user` varchar(15) DEFAULT NULL,
  `update_date` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `delete_platform` varchar(3) DEFAULT NULL,
  `delete_user` varchar(15) DEFAULT NULL,
  `delete_date` timestamp NULL DEFAULT NULL,
  `cru_csvnote` varchar(500) DEFAULT NULL,
  `is_erpsent` varchar(3) DEFAULT '0',
  `is_enabled` varchar(3) DEFAULT '1',
  `i` int(11) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_erp` varchar(25) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `secret` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `address` varchar(250) DEFAULT NULL,
  `birthdate` datetime DEFAULT NULL COMMENT 'comprobar mayoria de edad',
  `geo_location` varchar(500) DEFAULT NULL,
  `id_parent` int(11) DEFAULT NULL COMMENT 'quien es su superior',
  `id_gender` int(11) DEFAULT NULL COMMENT 'app_array.type=gender',
  `id_nationality` int(11) DEFAULT NULL,
  `id_country` int(11) DEFAULT NULL COMMENT 'app_array.type=country',
  `id_language` int(11) DEFAULT 1 COMMENT 'su idioma de preferencia',
  `path_picture` varchar(100) DEFAULT NULL,
  `id_profile` int(11) DEFAULT NULL COMMENT 'base_array.type=profile: user,maintenaince,system, enterprise, individual, client',
  `tokenreset` varchar(250) DEFAULT NULL,
  `log_attempts` int(5) DEFAULT 0,
  `date_validated` varchar(14) DEFAULT NULL COMMENT 'cuando valido su cuenta por email',
  `is_notificable` tinyint(4) DEFAULT 0 COMMENT 'para enviar notificaciones push',
  `uuid` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `base_user_email_uindex` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

/*Data for the table `base_user` */

insert  into `base_user`(`processflag`,`insert_platform`,`insert_user`,`insert_date`,`update_platform`,`update_user`,`update_date`,`delete_platform`,`delete_user`,`delete_date`,`cru_csvnote`,`is_erpsent`,`is_enabled`,`i`,`id`,`code_erp`,`description`,`email`,`secret`,`phone`,`fullname`,`address`,`birthdate`,`geo_location`,`id_parent`,`id_gender`,`id_nationality`,`id_country`,`id_language`,`path_picture`,`id_profile`,`tokenreset`,`log_attempts`,`date_validated`,`is_notificable`,`uuid`) values (NULL,'1','1','2021-11-01 18:11:04',NULL,NULL,'2021-11-02 19:10:21',NULL,NULL,NULL,NULL,'0','1',NULL,1,'aa','desc','eaf@eaf.com','$2y$10$./yY7fyGD2xx/qxLva1jfu7MbbOrkwtpTRJ4ZGx7OG59KOgd/UPK6','629196076','Ell Eduuu Rdo','c/ alba 125','1976-01-10 00:00:00',NULL,NULL,1,1,1,2,NULL,2,NULL,0,NULL,1,'U00001');

/*Table structure for table `base_user_permissions` */

DROP TABLE IF EXISTS `base_user_permissions`;

CREATE TABLE `base_user_permissions` (
  `processflag` varchar(5) DEFAULT NULL,
  `insert_platform` varchar(3) DEFAULT '1',
  `insert_user` varchar(15) DEFAULT NULL,
  `insert_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `update_platform` varchar(3) DEFAULT NULL,
  `update_user` varchar(15) DEFAULT NULL,
  `update_date` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `delete_platform` varchar(3) DEFAULT NULL,
  `delete_user` varchar(15) DEFAULT NULL,
  `delete_date` timestamp NULL DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL COMMENT 'base_user.id permissions',
  `json_rw` varchar(2000) DEFAULT NULL COMMENT 'json con permisos R,W',
  `uuid` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `base_user_permissions_id_user_uindex` (`id_user`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

/*Data for the table `base_user_permissions` */

insert  into `base_user_permissions`(`processflag`,`insert_platform`,`insert_user`,`insert_date`,`update_platform`,`update_user`,`update_date`,`delete_platform`,`delete_user`,`delete_date`,`id`,`id_user`,`json_rw`,`uuid`) values (NULL,'1',NULL,'2021-11-02 19:44:49',NULL,NULL,'2021-11-02 19:45:26',NULL,NULL,NULL,1,1,'[\r\n\"dashboard:read\",\r\n\"dashboard:write\",\r\n\"users:read\",\r\n\"users:write\",\r\n\"promotions:read\",\r\n\"promotions:write\"]',NULL);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
