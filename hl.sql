-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         10.5.10-MariaDB-log - mariadb.org binary distribution
-- SO del servidor:              Win64
-- HeidiSQL Versión:             9.3.0.4984
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Volcando estructura para tabla hardwarelink.factory_clients
DROP TABLE IF EXISTS `factory_clients`;
CREATE TABLE IF NOT EXISTS `factory_clients` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=latin1;

-- Volcando datos para la tabla hardwarelink.factory_clients: ~25 rows (aproximadamente)
DELETE FROM `factory_clients`;
/*!40000 ALTER TABLE `factory_clients` DISABLE KEYS */;
INSERT INTO `factory_clients` (`id`, `name`) VALUES
	(6, 'EGASA DISTRIBUCIÓN, S.L.'),
	(7, 'ARISTOCRAT TECHNOLOGIES SPAIN, S.L.'),
	(8, 'EGS - Electronic Gaming Systems AS'),
	(10, 'EBS DIGITAL AS'),
	(11, 'ELAUT NV'),
	(12, 'GISTRA, S.L.'),
	(13, 'INDIGLOBAL PANAMA, S.A.'),
	(14, 'COMATEL (INVERSIONES COMATEL, S.L.)'),
	(15, 'OLIVA TORRAS, S.A.'),
	(16, 'LUCKIA GAMES, S.A.'),
	(17, 'MEYPAR, S.L.'),
	(18, 'MGA-MACHINES GAMES AUTOMATICS, S.A. (Unipersonal)'),
	(19, 'SERDISGA 2000, S.L.'),
	(20, 'SAS FRANCIA (SOCIETÉ FRANCAISE COMMERCIALISATION APPAREILS AUTO)'),
	(21, 'VIDEOB OÜ'),
	(22, 'VID (VISION INNOVATION DIVERSION, S.L.)'),
	(24, 'SMI2000'),
	(25, 'SPV'),
	(26, 'JUEGOS DE CENTROAMERICA'),
	(27, 'GAMBEE'),
	(28, 'Honduras, cliente no determinado.'),
	(29, 'CIRSA'),
	(30, 'VALISA'),
	(31, 'AINSWORTH'),
	(32, 'new client');
/*!40000 ALTER TABLE `factory_clients` ENABLE KEYS */;


-- Volcando estructura para tabla hardwarelink.factory_dispatch_orders
DROP TABLE IF EXISTS `factory_dispatch_orders`;
CREATE TABLE IF NOT EXISTS `factory_dispatch_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `factory_client_id` int(11) NOT NULL DEFAULT 0,
  `detail` text NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Volcando datos para la tabla hardwarelink.factory_dispatch_orders: ~0 rows (aproximadamente)
DELETE FROM `factory_dispatch_orders`;
/*!40000 ALTER TABLE `factory_dispatch_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `factory_dispatch_orders` ENABLE KEYS */;


-- Volcando estructura para tabla hardwarelink.factory_dispatch_orders_licenses
DROP TABLE IF EXISTS `factory_dispatch_orders_licenses`;
CREATE TABLE IF NOT EXISTS `factory_dispatch_orders_licenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `factory_dispatch_orders_id` int(11) NOT NULL,
  `group_id` varchar(50) NOT NULL,
  `machine_id` varchar(50) NOT NULL,
  `device_id` varchar(100) NOT NULL,
  `type` varchar(100) NOT NULL,
  `type_info` varchar(100) NOT NULL,
  `manufacturer_serial_number` varchar(100) NOT NULL,
  `rpi` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Volcando datos para la tabla hardwarelink.factory_dispatch_orders_licenses: ~0 rows (aproximadamente)
DELETE FROM `factory_dispatch_orders_licenses`;
/*!40000 ALTER TABLE `factory_dispatch_orders_licenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `factory_dispatch_orders_licenses` ENABLE KEYS */;


-- Volcando estructura para tabla hardwarelink.spv_clients
DROP TABLE IF EXISTS `spv_clients`;
CREATE TABLE IF NOT EXISTS `spv_clients` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;

-- Volcando datos para la tabla hardwarelink.spv_clients: ~25 rows (aproximadamente)
DELETE FROM `spv_clients`;
/*!40000 ALTER TABLE `spv_clients` DISABLE KEYS */;
INSERT INTO `spv_clients` (`id`, `name`) VALUES
	(6, 'EGASA DISTRIBUCIÓN, S.L.'),
	(7, 'ARISTOCRAT TECHNOLOGIES SPAIN, S.L.'),
	(8, 'EGS - Electronic Gaming Systems AS'),
	(10, 'EBS DIGITAL AS'),
	(11, 'ELAUT NV'),
	(12, 'GISTRA, S.L.'),
	(13, 'INDIGLOBAL PANAMA, S.A.'),
	(14, 'COMATEL (INVERSIONES COMATEL, S.L.)'),
	(15, 'OLIVA TORRAS, S.A.'),
	(16, 'LUCKIA GAMES, S.A.'),
	(17, 'MEYPAR, S.L.'),
	(18, 'MGA-MACHINES GAMES AUTOMATICS, S.A. (Unipersonal)'),
	(19, 'SERDISGA 2000, S.L.'),
	(20, 'SAS FRANCIA (SOCIETÉ FRANCAISE COMMERCIALISATION APPAREILS AUTO)'),
	(21, 'VIDEOB OÜ'),
	(22, 'VID (VISION INNOVATION DIVERSION, S.L.)'),
	(24, 'SMI2000'),
	(25, 'SPV'),
	(26, 'JUEGOS DE CENTROAMERICA'),
	(27, 'GAMBEE'),
	(28, 'Honduras, cliente no determinado.'),
	(29, 'CIRSA'),
	(30, 'VALISA'),
	(31, 'AINSWORTH'),
	(32, 'new client');
/*!40000 ALTER TABLE `spv_clients` ENABLE KEYS */;


-- Volcando estructura para tabla hardwarelink.spv_dispatch_orders
DROP TABLE IF EXISTS `spv_dispatch_orders`;
CREATE TABLE IF NOT EXISTS `spv_dispatch_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `spv_client_id` int(11) NOT NULL DEFAULT 0,
  `detail` text NOT NULL DEFAULT '0',
  `group_id` varchar(50) DEFAULT NULL,
  `machine_id` varchar(50) DEFAULT NULL,
  `device_id` varchar(200) DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `type_info` varchar(100) DEFAULT NULL,
  `machine_serial_number` varchar(50) NOT NULL DEFAULT '0',
  `rpi` varchar(50) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Volcando datos para la tabla hardwarelink.spv_dispatch_orders: ~0 rows (aproximadamente)
DELETE FROM `spv_dispatch_orders`;
/*!40000 ALTER TABLE `spv_dispatch_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `spv_dispatch_orders` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
