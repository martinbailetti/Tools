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

-- Volcando estructura para tabla hardwarelink.factory_dispatch_orders
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
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
