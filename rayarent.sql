-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 24, 2026 at 02:28 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.35

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rayarent`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-356a192b7913b04c54574d18c28d46e6395428ab', 'i:1;', 1795525730),
('laravel-cache-356a192b7913b04c54574d18c28d46e6395428ab:timer', 'i:1795525730;', 1795525730),
('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba', 'i:1;', 1795530046),
('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba:timer', 'i:1795530046;', 1795530046),
('laravel-cache-roster:project:v3:edf43ca38896a4cf0c2a67c15a2bace0', 'O:26:\"Laravel\\Roster\\ProjectScan\":8:{s:8:\"basePath\";s:24:\"C:\\laragon\\www\\rayarent\\\";s:3:\"php\";O:35:\"Laravel\\Roster\\Ecosystems\\Ecosystem\":2:{s:9:\"\0*\0byName\";a:116:{s:10:\"brick/math\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"brick/math\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";E:43:\"Laravel\\Roster\\Enums\\PackageSource:Composer\";s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\laragon\\www\\rayarent\\vendor\\brick\\math\";}s:31:\"carbonphp/carbon-doctrine-types\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"carbonphp/carbon-doctrine-types\";s:10:\"\0*\0version\";s:5:\"3.2.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:62:\"C:\\laragon\\www\\rayarent\\vendor\\carbonphp\\carbon-doctrine-types\";}s:23:\"dflydev/dot-access-data\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"dflydev/dot-access-data\";s:10:\"\0*\0version\";s:5:\"3.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"C:\\laragon\\www\\rayarent\\vendor\\dflydev\\dot-access-data\";}s:18:\"doctrine/inflector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"doctrine/inflector\";s:10:\"\0*\0version\";s:5:\"2.1.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"C:\\laragon\\www\\rayarent\\vendor\\doctrine\\inflector\";}s:14:\"doctrine/lexer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"doctrine/lexer\";s:10:\"\0*\0version\";s:5:\"3.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\laragon\\www\\rayarent\\vendor\\doctrine\\lexer\";}s:29:\"dragonmantank/cron-expression\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"dragonmantank/cron-expression\";s:10:\"\0*\0version\";s:5:\"3.6.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:60:\"C:\\laragon\\www\\rayarent\\vendor\\dragonmantank\\cron-expression\";}s:23:\"egulias/email-validator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"egulias/email-validator\";s:10:\"\0*\0version\";s:5:\"4.0.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"C:\\laragon\\www\\rayarent\\vendor\\egulias\\email-validator\";}s:18:\"fruitcake/php-cors\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"fruitcake/php-cors\";s:10:\"\0*\0version\";s:5:\"1.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"C:\\laragon\\www\\rayarent\\vendor\\fruitcake\\php-cors\";}s:27:\"graham-campbell/result-type\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"graham-campbell/result-type\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"C:\\laragon\\www\\rayarent\\vendor\\graham-campbell\\result-type\";}s:17:\"guzzlehttp/guzzle\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"guzzlehttp/guzzle\";s:10:\"\0*\0version\";s:5:\"8.3.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\laragon\\www\\rayarent\\vendor\\guzzlehttp\\guzzle\";}s:19:\"guzzlehttp/promises\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"guzzlehttp/promises\";s:10:\"\0*\0version\";s:5:\"3.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\laragon\\www\\rayarent\\vendor\\guzzlehttp\\promises\";}s:15:\"guzzlehttp/psr7\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"guzzlehttp/psr7\";s:10:\"\0*\0version\";s:5:\"3.2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\laragon\\www\\rayarent\\vendor\\guzzlehttp\\psr7\";}s:23:\"guzzlehttp/uri-template\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"guzzlehttp/uri-template\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"C:\\laragon\\www\\rayarent\\vendor\\guzzlehttp\\uri-template\";}s:17:\"laravel/framework\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"laravel/framework\";s:10:\"\0*\0version\";s:7:\"13.40.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^13.17\";s:7:\"\0*\0path\";s:48:\"C:\\laragon\\www\\rayarent\\vendor\\laravel\\framework\";}s:15:\"laravel/prompts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"laravel/prompts\";s:10:\"\0*\0version\";s:6:\"0.3.26\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\laragon\\www\\rayarent\\vendor\\laravel\\prompts\";}s:28:\"laravel/serializable-closure\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"laravel/serializable-closure\";s:10:\"\0*\0version\";s:5:\"2.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"C:\\laragon\\www\\rayarent\\vendor\\laravel\\serializable-closure\";}s:14:\"laravel/tinker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"laravel/tinker\";s:10:\"\0*\0version\";s:5:\"3.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^3.0\";s:7:\"\0*\0path\";s:45:\"C:\\laragon\\www\\rayarent\\vendor\\laravel\\tinker\";}s:17:\"league/commonmark\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"league/commonmark\";s:10:\"\0*\0version\";s:6:\"2.10.7\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\laragon\\www\\rayarent\\vendor\\league\\commonmark\";}s:13:\"league/config\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"league/config\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\laragon\\www\\rayarent\\vendor\\league\\config\";}s:16:\"league/flysystem\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"league/flysystem\";s:10:\"\0*\0version\";s:6:\"3.36.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\laragon\\www\\rayarent\\vendor\\league\\flysystem\";}s:22:\"league/flysystem-local\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"league/flysystem-local\";s:10:\"\0*\0version\";s:6:\"3.35.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\laragon\\www\\rayarent\\vendor\\league\\flysystem-local\";}s:26:\"league/mime-type-detection\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"league/mime-type-detection\";s:10:\"\0*\0version\";s:6:\"1.17.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"C:\\laragon\\www\\rayarent\\vendor\\league\\mime-type-detection\";}s:10:\"league/uri\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"league/uri\";s:10:\"\0*\0version\";s:5:\"7.9.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\laragon\\www\\rayarent\\vendor\\league\\uri\";}s:21:\"league/uri-interfaces\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"league/uri-interfaces\";s:10:\"\0*\0version\";s:5:\"7.9.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"C:\\laragon\\www\\rayarent\\vendor\\league\\uri-interfaces\";}s:15:\"monolog/monolog\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"monolog/monolog\";s:10:\"\0*\0version\";s:6:\"3.12.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\laragon\\www\\rayarent\\vendor\\monolog\\monolog\";}s:13:\"nesbot/carbon\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"nesbot/carbon\";s:10:\"\0*\0version\";s:6:\"3.15.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\laragon\\www\\rayarent\\vendor\\nesbot\\carbon\";}s:12:\"nette/schema\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"nette/schema\";s:10:\"\0*\0version\";s:5:\"1.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\laragon\\www\\rayarent\\vendor\\nette\\schema\";}s:11:\"nette/utils\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"nette/utils\";s:10:\"\0*\0version\";s:5:\"4.1.6\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\laragon\\www\\rayarent\\vendor\\nette\\utils\";}s:16:\"nikic/php-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"nikic/php-parser\";s:10:\"\0*\0version\";s:5:\"5.9.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\laragon\\www\\rayarent\\vendor\\nikic\\php-parser\";}s:19:\"nunomaduro/termwind\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"nunomaduro/termwind\";s:10:\"\0*\0version\";s:5:\"2.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\laragon\\www\\rayarent\\vendor\\nunomaduro\\termwind\";}s:19:\"phpoption/phpoption\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"phpoption/phpoption\";s:10:\"\0*\0version\";s:6:\"1.10.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\laragon\\www\\rayarent\\vendor\\phpoption\\phpoption\";}s:9:\"psr/clock\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"psr/clock\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"C:\\laragon\\www\\rayarent\\vendor\\psr\\clock\";}s:13:\"psr/container\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"psr/container\";s:10:\"\0*\0version\";s:5:\"2.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\laragon\\www\\rayarent\\vendor\\psr\\container\";}s:20:\"psr/event-dispatcher\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"psr/event-dispatcher\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\laragon\\www\\rayarent\\vendor\\psr\\event-dispatcher\";}s:15:\"psr/http-client\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"psr/http-client\";s:10:\"\0*\0version\";s:5:\"1.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\laragon\\www\\rayarent\\vendor\\psr\\http-client\";}s:16:\"psr/http-factory\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"psr/http-factory\";s:10:\"\0*\0version\";s:5:\"1.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\laragon\\www\\rayarent\\vendor\\psr\\http-factory\";}s:16:\"psr/http-message\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"psr/http-message\";s:10:\"\0*\0version\";s:3:\"2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\laragon\\www\\rayarent\\vendor\\psr\\http-message\";}s:7:\"psr/log\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"psr/log\";s:10:\"\0*\0version\";s:5:\"3.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:38:\"C:\\laragon\\www\\rayarent\\vendor\\psr\\log\";}s:16:\"psr/simple-cache\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"psr/simple-cache\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\laragon\\www\\rayarent\\vendor\\psr\\simple-cache\";}s:9:\"psy/psysh\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"psy/psysh\";s:10:\"\0*\0version\";s:7:\"0.12.24\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"C:\\laragon\\www\\rayarent\\vendor\\psy\\psysh\";}s:17:\"ramsey/collection\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"ramsey/collection\";s:10:\"\0*\0version\";s:5:\"2.1.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\laragon\\www\\rayarent\\vendor\\ramsey\\collection\";}s:11:\"ramsey/uuid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"ramsey/uuid\";s:10:\"\0*\0version\";s:5:\"4.9.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\laragon\\www\\rayarent\\vendor\\ramsey\\uuid\";}s:13:\"symfony/clock\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"symfony/clock\";s:10:\"\0*\0version\";s:6:\"7.4.21\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\clock\";}s:15:\"symfony/console\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"symfony/console\";s:10:\"\0*\0version\";s:6:\"7.4.23\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\console\";}s:20:\"symfony/css-selector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"symfony/css-selector\";s:10:\"\0*\0version\";s:6:\"7.4.22\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\css-selector\";}s:29:\"symfony/deprecation-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"symfony/deprecation-contracts\";s:10:\"\0*\0version\";s:5:\"3.8.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:60:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\deprecation-contracts\";}s:21:\"symfony/error-handler\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"symfony/error-handler\";s:10:\"\0*\0version\";s:6:\"7.4.22\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\error-handler\";}s:24:\"symfony/event-dispatcher\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"symfony/event-dispatcher\";s:10:\"\0*\0version\";s:6:\"7.4.17\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\event-dispatcher\";}s:34:\"symfony/event-dispatcher-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"symfony/event-dispatcher-contracts\";s:10:\"\0*\0version\";s:5:\"3.8.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\event-dispatcher-contracts\";}s:14:\"symfony/finder\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"symfony/finder\";s:10:\"\0*\0version\";s:6:\"7.4.23\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\finder\";}s:23:\"symfony/http-foundation\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"symfony/http-foundation\";s:10:\"\0*\0version\";s:6:\"7.4.23\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\http-foundation\";}s:19:\"symfony/http-kernel\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"symfony/http-kernel\";s:10:\"\0*\0version\";s:6:\"7.4.23\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\http-kernel\";}s:14:\"symfony/mailer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"symfony/mailer\";s:10:\"\0*\0version\";s:6:\"7.4.22\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\mailer\";}s:12:\"symfony/mime\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"symfony/mime\";s:10:\"\0*\0version\";s:6:\"7.4.22\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\mime\";}s:22:\"symfony/polyfill-ctype\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-ctype\";s:10:\"\0*\0version\";s:6:\"1.37.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\polyfill-ctype\";}s:30:\"symfony/polyfill-intl-grapheme\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"symfony/polyfill-intl-grapheme\";s:10:\"\0*\0version\";s:6:\"1.43.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\polyfill-intl-grapheme\";}s:25:\"symfony/polyfill-intl-idn\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"symfony/polyfill-intl-idn\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\polyfill-intl-idn\";}s:32:\"symfony/polyfill-intl-normalizer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"symfony/polyfill-intl-normalizer\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\polyfill-intl-normalizer\";}s:25:\"symfony/polyfill-mbstring\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"symfony/polyfill-mbstring\";s:10:\"\0*\0version\";s:6:\"1.43.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\polyfill-mbstring\";}s:22:\"symfony/polyfill-php80\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php80\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\polyfill-php80\";}s:22:\"symfony/polyfill-php82\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php82\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\polyfill-php82\";}s:22:\"symfony/polyfill-php83\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php83\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\polyfill-php83\";}s:22:\"symfony/polyfill-php84\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php84\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\polyfill-php84\";}s:22:\"symfony/polyfill-php85\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php85\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\polyfill-php85\";}s:22:\"symfony/polyfill-php86\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php86\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\polyfill-php86\";}s:21:\"symfony/polyfill-time\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"symfony/polyfill-time\";s:10:\"\0*\0version\";s:6:\"1.42.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\polyfill-time\";}s:21:\"symfony/polyfill-uuid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"symfony/polyfill-uuid\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\polyfill-uuid\";}s:15:\"symfony/process\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"symfony/process\";s:10:\"\0*\0version\";s:6:\"7.4.22\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\process\";}s:15:\"symfony/routing\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"symfony/routing\";s:10:\"\0*\0version\";s:6:\"7.4.23\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\routing\";}s:25:\"symfony/service-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"symfony/service-contracts\";s:10:\"\0*\0version\";s:5:\"3.8.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\service-contracts\";}s:14:\"symfony/string\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"symfony/string\";s:10:\"\0*\0version\";s:6:\"7.4.22\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\string\";}s:19:\"symfony/translation\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"symfony/translation\";s:10:\"\0*\0version\";s:6:\"7.4.23\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\translation\";}s:29:\"symfony/translation-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"symfony/translation-contracts\";s:10:\"\0*\0version\";s:5:\"3.8.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:60:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\translation-contracts\";}s:11:\"symfony/uid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"symfony/uid\";s:10:\"\0*\0version\";s:6:\"7.4.21\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\uid\";}s:18:\"symfony/var-dumper\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"symfony/var-dumper\";s:10:\"\0*\0version\";s:6:\"7.4.22\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\var-dumper\";}s:33:\"tijsverkoyen/css-to-inline-styles\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"tijsverkoyen/css-to-inline-styles\";s:10:\"\0*\0version\";s:5:\"2.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"C:\\laragon\\www\\rayarent\\vendor\\tijsverkoyen\\css-to-inline-styles\";}s:16:\"vlucas/phpdotenv\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"vlucas/phpdotenv\";s:10:\"\0*\0version\";s:5:\"5.8.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\laragon\\www\\rayarent\\vendor\\vlucas\\phpdotenv\";}s:19:\"voku/portable-ascii\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"voku/portable-ascii\";s:10:\"\0*\0version\";s:5:\"2.1.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\laragon\\www\\rayarent\\vendor\\voku\\portable-ascii\";}s:15:\"composer/semver\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"composer/semver\";s:10:\"\0*\0version\";s:5:\"3.6.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\laragon\\www\\rayarent\\vendor\\composer\\semver\";}s:14:\"fakerphp/faker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"fakerphp/faker\";s:10:\"\0*\0version\";s:6:\"1.24.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^1.23\";s:7:\"\0*\0path\";s:45:\"C:\\laragon\\www\\rayarent\\vendor\\fakerphp\\faker\";}s:11:\"filp/whoops\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"filp/whoops\";s:10:\"\0*\0version\";s:6:\"2.19.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\laragon\\www\\rayarent\\vendor\\filp\\whoops\";}s:21:\"hamcrest/hamcrest-php\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"hamcrest/hamcrest-php\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"C:\\laragon\\www\\rayarent\\vendor\\hamcrest\\hamcrest-php\";}s:22:\"laravel/agent-detector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"laravel/agent-detector\";s:10:\"\0*\0version\";s:5:\"2.0.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\laragon\\www\\rayarent\\vendor\\laravel\\agent-detector\";}s:13:\"laravel/boost\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"laravel/boost\";s:10:\"\0*\0version\";s:6:\"2.10.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^2.10\";s:7:\"\0*\0path\";s:44:\"C:\\laragon\\www\\rayarent\\vendor\\laravel\\boost\";}s:11:\"laravel/mcp\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"laravel/mcp\";s:10:\"\0*\0version\";s:5:\"1.1.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\laragon\\www\\rayarent\\vendor\\laravel\\mcp\";}s:12:\"laravel/pail\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"laravel/pail\";s:10:\"\0*\0version\";s:5:\"1.2.7\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^1.2.5\";s:7:\"\0*\0path\";s:43:\"C:\\laragon\\www\\rayarent\\vendor\\laravel\\pail\";}s:11:\"laravel/pao\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"laravel/pao\";s:10:\"\0*\0version\";s:5:\"1.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^1.0.6\";s:7:\"\0*\0path\";s:42:\"C:\\laragon\\www\\rayarent\\vendor\\laravel\\pao\";}s:12:\"laravel/pint\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"laravel/pint\";s:10:\"\0*\0version\";s:6:\"1.32.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^1.27\";s:7:\"\0*\0path\";s:43:\"C:\\laragon\\www\\rayarent\\vendor\\laravel\\pint\";}s:14:\"laravel/roster\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"laravel/roster\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\laragon\\www\\rayarent\\vendor\\laravel\\roster\";}s:15:\"mockery/mockery\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"mockery/mockery\";s:10:\"\0*\0version\";s:6:\"1.6.15\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^1.6\";s:7:\"\0*\0path\";s:46:\"C:\\laragon\\www\\rayarent\\vendor\\mockery\\mockery\";}s:17:\"myclabs/deep-copy\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"myclabs/deep-copy\";s:10:\"\0*\0version\";s:6:\"1.14.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\laragon\\www\\rayarent\\vendor\\myclabs\\deep-copy\";}s:20:\"nunomaduro/collision\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"nunomaduro/collision\";s:10:\"\0*\0version\";s:5:\"8.9.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^8.6\";s:7:\"\0*\0path\";s:51:\"C:\\laragon\\www\\rayarent\\vendor\\nunomaduro\\collision\";}s:16:\"phar-io/manifest\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"phar-io/manifest\";s:10:\"\0*\0version\";s:5:\"2.0.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\laragon\\www\\rayarent\\vendor\\phar-io\\manifest\";}s:15:\"phar-io/version\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"phar-io/version\";s:10:\"\0*\0version\";s:5:\"3.2.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\laragon\\www\\rayarent\\vendor\\phar-io\\version\";}s:25:\"phpunit/php-code-coverage\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"phpunit/php-code-coverage\";s:10:\"\0*\0version\";s:6:\"12.5.8\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"C:\\laragon\\www\\rayarent\\vendor\\phpunit\\php-code-coverage\";}s:25:\"phpunit/php-file-iterator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"phpunit/php-file-iterator\";s:10:\"\0*\0version\";s:5:\"6.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"C:\\laragon\\www\\rayarent\\vendor\\phpunit\\php-file-iterator\";}s:19:\"phpunit/php-invoker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"phpunit/php-invoker\";s:10:\"\0*\0version\";s:5:\"6.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\laragon\\www\\rayarent\\vendor\\phpunit\\php-invoker\";}s:25:\"phpunit/php-text-template\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"phpunit/php-text-template\";s:10:\"\0*\0version\";s:5:\"5.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"C:\\laragon\\www\\rayarent\\vendor\\phpunit\\php-text-template\";}s:17:\"phpunit/php-timer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"phpunit/php-timer\";s:10:\"\0*\0version\";s:5:\"8.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\laragon\\www\\rayarent\\vendor\\phpunit\\php-timer\";}s:15:\"phpunit/phpunit\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"phpunit/phpunit\";s:10:\"\0*\0version\";s:7:\"12.5.43\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:8:\"^12.5.12\";s:7:\"\0*\0path\";s:46:\"C:\\laragon\\www\\rayarent\\vendor\\phpunit\\phpunit\";}s:20:\"sebastian/cli-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"sebastian/cli-parser\";s:10:\"\0*\0version\";s:5:\"4.2.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\laragon\\www\\rayarent\\vendor\\sebastian\\cli-parser\";}s:20:\"sebastian/comparator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"sebastian/comparator\";s:10:\"\0*\0version\";s:5:\"7.1.8\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\laragon\\www\\rayarent\\vendor\\sebastian\\comparator\";}s:20:\"sebastian/complexity\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"sebastian/complexity\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\laragon\\www\\rayarent\\vendor\\sebastian\\complexity\";}s:14:\"sebastian/diff\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"sebastian/diff\";s:10:\"\0*\0version\";s:5:\"7.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\laragon\\www\\rayarent\\vendor\\sebastian\\diff\";}s:21:\"sebastian/environment\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"sebastian/environment\";s:10:\"\0*\0version\";s:5:\"8.1.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"C:\\laragon\\www\\rayarent\\vendor\\sebastian\\environment\";}s:18:\"sebastian/exporter\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"sebastian/exporter\";s:10:\"\0*\0version\";s:5:\"7.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"C:\\laragon\\www\\rayarent\\vendor\\sebastian\\exporter\";}s:22:\"sebastian/global-state\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"sebastian/global-state\";s:10:\"\0*\0version\";s:5:\"8.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\laragon\\www\\rayarent\\vendor\\sebastian\\global-state\";}s:23:\"sebastian/lines-of-code\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"sebastian/lines-of-code\";s:10:\"\0*\0version\";s:5:\"4.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"C:\\laragon\\www\\rayarent\\vendor\\sebastian\\lines-of-code\";}s:27:\"sebastian/object-enumerator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"sebastian/object-enumerator\";s:10:\"\0*\0version\";s:5:\"7.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"C:\\laragon\\www\\rayarent\\vendor\\sebastian\\object-enumerator\";}s:26:\"sebastian/object-reflector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"sebastian/object-reflector\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"C:\\laragon\\www\\rayarent\\vendor\\sebastian\\object-reflector\";}s:27:\"sebastian/recursion-context\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"sebastian/recursion-context\";s:10:\"\0*\0version\";s:5:\"7.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"C:\\laragon\\www\\rayarent\\vendor\\sebastian\\recursion-context\";}s:14:\"sebastian/type\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"sebastian/type\";s:10:\"\0*\0version\";s:5:\"6.0.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\laragon\\www\\rayarent\\vendor\\sebastian\\type\";}s:17:\"sebastian/version\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"sebastian/version\";s:10:\"\0*\0version\";s:5:\"6.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\laragon\\www\\rayarent\\vendor\\sebastian\\version\";}s:28:\"staabm/side-effects-detector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"staabm/side-effects-detector\";s:10:\"\0*\0version\";s:5:\"1.0.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"C:\\laragon\\www\\rayarent\\vendor\\staabm\\side-effects-detector\";}s:12:\"symfony/yaml\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"symfony/yaml\";s:10:\"\0*\0version\";s:6:\"7.4.23\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\yaml\";}s:17:\"theseer/tokenizer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"theseer/tokenizer\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\laragon\\www\\rayarent\\vendor\\theseer\\tokenizer\";}}s:11:\"\0*\0packages\";O:32:\"Laravel\\Roster\\PackageCollection\":2:{s:8:\"\0*\0items\";a:116:{i:0;r:5;i:1;r:13;i:2;r:21;i:3;r:29;i:4;r:37;i:5;r:45;i:6;r:53;i:7;r:61;i:8;r:69;i:9;r:77;i:10;r:85;i:11;r:93;i:12;r:101;i:13;r:109;i:14;r:117;i:15;r:125;i:16;r:133;i:17;r:141;i:18;r:149;i:19;r:157;i:20;r:165;i:21;r:173;i:22;r:181;i:23;r:189;i:24;r:197;i:25;r:205;i:26;r:213;i:27;r:221;i:28;r:229;i:29;r:237;i:30;r:245;i:31;r:253;i:32;r:261;i:33;r:269;i:34;r:277;i:35;r:285;i:36;r:293;i:37;r:301;i:38;r:309;i:39;r:317;i:40;r:325;i:41;r:333;i:42;r:341;i:43;r:349;i:44;r:357;i:45;r:365;i:46;r:373;i:47;r:381;i:48;r:389;i:49;r:397;i:50;r:405;i:51;r:413;i:52;r:421;i:53;r:429;i:54;r:437;i:55;r:445;i:56;r:453;i:57;r:461;i:58;r:469;i:59;r:477;i:60;r:485;i:61;r:493;i:62;r:501;i:63;r:509;i:64;r:517;i:65;r:525;i:66;r:533;i:67;r:541;i:68;r:549;i:69;r:557;i:70;r:565;i:71;r:573;i:72;r:581;i:73;r:589;i:74;r:597;i:75;r:605;i:76;r:613;i:77;r:621;i:78;r:629;i:79;r:637;i:80;r:645;i:81;r:653;i:82;r:661;i:83;r:669;i:84;r:677;i:85;r:685;i:86;r:693;i:87;r:701;i:88;r:709;i:89;r:717;i:90;r:725;i:91;r:733;i:92;r:741;i:93;r:749;i:94;r:757;i:95;r:765;i:96;r:773;i:97;r:781;i:98;r:789;i:99;r:797;i:100;r:805;i:101;r:813;i:102;r:821;i:103;r:829;i:104;r:837;i:105;r:845;i:106;r:853;i:107;r:861;i:108;r:869;i:109;r:877;i:110;r:885;i:111;r:893;i:112;r:901;i:113;r:909;i:114;r:917;i:115;r:925;}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:2:\"js\";O:37:\"Laravel\\Roster\\Ecosystems\\JsEcosystem\":3:{s:9:\"\0*\0byName\";a:6:{s:17:\"@tailwindcss/vite\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"@tailwindcss/vite\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";E:38:\"Laravel\\Roster\\Enums\\PackageSource:Npm\";s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^4.0.0\";s:7:\"\0*\0path\";s:54:\"C:\\laragon\\www\\rayarent\\node_modules\\@tailwindcss\\vite\";}s:12:\"concurrently\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"concurrently\";s:10:\"\0*\0version\";s:6:\"10.0.3\";s:9:\"\0*\0source\";r:1057;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:7:\"^10.0.3\";s:7:\"\0*\0path\";s:49:\"C:\\laragon\\www\\rayarent\\node_modules\\concurrently\";}s:19:\"laravel-vite-plugin\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"laravel-vite-plugin\";s:10:\"\0*\0version\";s:3:\"3.1\";s:9:\"\0*\0source\";r:1057;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^3.1\";s:7:\"\0*\0path\";s:56:\"C:\\laragon\\www\\rayarent\\node_modules\\laravel-vite-plugin\";}s:11:\"tailwindcss\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"tailwindcss\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1057;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^4.0.0\";s:7:\"\0*\0path\";s:48:\"C:\\laragon\\www\\rayarent\\node_modules\\tailwindcss\";}s:4:\"vite\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"vite\";s:10:\"\0*\0version\";s:5:\"8.0.0\";s:9:\"\0*\0source\";r:1057;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^8.0.0\";s:7:\"\0*\0path\";s:41:\"C:\\laragon\\www\\rayarent\\node_modules\\vite\";}s:18:\"@laravel/multiplex\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"@laravel/multiplex\";s:10:\"\0*\0version\";s:5:\"0.4.1\";s:9:\"\0*\0source\";r:1057;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^0.4.1\";s:7:\"\0*\0path\";s:55:\"C:\\laragon\\www\\rayarent\\node_modules\\@laravel\\multiplex\";}}s:11:\"\0*\0packages\";O:32:\"Laravel\\Roster\\PackageCollection\":2:{s:8:\"\0*\0items\";a:6:{i:0;r:1054;i:1;r:1062;i:2;r:1070;i:3;r:1078;i:4;r:1086;i:5;r:1094;}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:17:\"\0*\0packageManager\";N;}s:6:\"stacks\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:1:{i:0;E:32:\"Laravel\\Roster\\Enums\\Stack:Blade\";}}s:21:\"browserTestFrameworks\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:0:{}}s:9:\"frontends\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:0:{}}s:6:\"agents\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:2:{i:0;E:37:\"Laravel\\Roster\\Enums\\Agent:ClaudeCode\";i:1;E:32:\"Laravel\\Roster\\Enums\\Agent:Codex\";}}s:7:\"editors\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:0:{}}}', 1793803672);
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-roster:project:v3:f1aee0dde1e5ff23af83cd755942d266', 'O:26:\"Laravel\\Roster\\ProjectScan\":8:{s:8:\"basePath\";s:24:\"C:\\laragon\\www\\rayarent\\\";s:3:\"php\";O:35:\"Laravel\\Roster\\Ecosystems\\Ecosystem\":2:{s:9:\"\0*\0byName\";a:116:{s:10:\"brick/math\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"brick/math\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";E:43:\"Laravel\\Roster\\Enums\\PackageSource:Composer\";s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\laragon\\www\\rayarent\\vendor\\brick\\math\";}s:31:\"carbonphp/carbon-doctrine-types\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"carbonphp/carbon-doctrine-types\";s:10:\"\0*\0version\";s:5:\"3.2.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:62:\"C:\\laragon\\www\\rayarent\\vendor\\carbonphp\\carbon-doctrine-types\";}s:23:\"dflydev/dot-access-data\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"dflydev/dot-access-data\";s:10:\"\0*\0version\";s:5:\"3.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"C:\\laragon\\www\\rayarent\\vendor\\dflydev\\dot-access-data\";}s:18:\"doctrine/inflector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"doctrine/inflector\";s:10:\"\0*\0version\";s:5:\"2.1.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"C:\\laragon\\www\\rayarent\\vendor\\doctrine\\inflector\";}s:14:\"doctrine/lexer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"doctrine/lexer\";s:10:\"\0*\0version\";s:5:\"3.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\laragon\\www\\rayarent\\vendor\\doctrine\\lexer\";}s:29:\"dragonmantank/cron-expression\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"dragonmantank/cron-expression\";s:10:\"\0*\0version\";s:5:\"3.6.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:60:\"C:\\laragon\\www\\rayarent\\vendor\\dragonmantank\\cron-expression\";}s:23:\"egulias/email-validator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"egulias/email-validator\";s:10:\"\0*\0version\";s:5:\"4.0.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"C:\\laragon\\www\\rayarent\\vendor\\egulias\\email-validator\";}s:18:\"fruitcake/php-cors\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"fruitcake/php-cors\";s:10:\"\0*\0version\";s:5:\"1.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"C:\\laragon\\www\\rayarent\\vendor\\fruitcake\\php-cors\";}s:27:\"graham-campbell/result-type\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"graham-campbell/result-type\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"C:\\laragon\\www\\rayarent\\vendor\\graham-campbell\\result-type\";}s:17:\"guzzlehttp/guzzle\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"guzzlehttp/guzzle\";s:10:\"\0*\0version\";s:5:\"8.3.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\laragon\\www\\rayarent\\vendor\\guzzlehttp\\guzzle\";}s:19:\"guzzlehttp/promises\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"guzzlehttp/promises\";s:10:\"\0*\0version\";s:5:\"3.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\laragon\\www\\rayarent\\vendor\\guzzlehttp\\promises\";}s:15:\"guzzlehttp/psr7\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"guzzlehttp/psr7\";s:10:\"\0*\0version\";s:5:\"3.2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\laragon\\www\\rayarent\\vendor\\guzzlehttp\\psr7\";}s:23:\"guzzlehttp/uri-template\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"guzzlehttp/uri-template\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"C:\\laragon\\www\\rayarent\\vendor\\guzzlehttp\\uri-template\";}s:17:\"laravel/framework\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"laravel/framework\";s:10:\"\0*\0version\";s:7:\"13.40.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^13.17\";s:7:\"\0*\0path\";s:48:\"C:\\laragon\\www\\rayarent\\vendor\\laravel\\framework\";}s:15:\"laravel/prompts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"laravel/prompts\";s:10:\"\0*\0version\";s:6:\"0.3.26\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\laragon\\www\\rayarent\\vendor\\laravel\\prompts\";}s:28:\"laravel/serializable-closure\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"laravel/serializable-closure\";s:10:\"\0*\0version\";s:5:\"2.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"C:\\laragon\\www\\rayarent\\vendor\\laravel\\serializable-closure\";}s:14:\"laravel/tinker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"laravel/tinker\";s:10:\"\0*\0version\";s:5:\"3.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^3.0\";s:7:\"\0*\0path\";s:45:\"C:\\laragon\\www\\rayarent\\vendor\\laravel\\tinker\";}s:17:\"league/commonmark\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"league/commonmark\";s:10:\"\0*\0version\";s:6:\"2.10.7\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\laragon\\www\\rayarent\\vendor\\league\\commonmark\";}s:13:\"league/config\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"league/config\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\laragon\\www\\rayarent\\vendor\\league\\config\";}s:16:\"league/flysystem\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"league/flysystem\";s:10:\"\0*\0version\";s:6:\"3.36.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\laragon\\www\\rayarent\\vendor\\league\\flysystem\";}s:22:\"league/flysystem-local\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"league/flysystem-local\";s:10:\"\0*\0version\";s:6:\"3.35.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\laragon\\www\\rayarent\\vendor\\league\\flysystem-local\";}s:26:\"league/mime-type-detection\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"league/mime-type-detection\";s:10:\"\0*\0version\";s:6:\"1.17.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"C:\\laragon\\www\\rayarent\\vendor\\league\\mime-type-detection\";}s:10:\"league/uri\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"league/uri\";s:10:\"\0*\0version\";s:5:\"7.9.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\laragon\\www\\rayarent\\vendor\\league\\uri\";}s:21:\"league/uri-interfaces\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"league/uri-interfaces\";s:10:\"\0*\0version\";s:5:\"7.9.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"C:\\laragon\\www\\rayarent\\vendor\\league\\uri-interfaces\";}s:15:\"monolog/monolog\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"monolog/monolog\";s:10:\"\0*\0version\";s:6:\"3.12.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\laragon\\www\\rayarent\\vendor\\monolog\\monolog\";}s:13:\"nesbot/carbon\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"nesbot/carbon\";s:10:\"\0*\0version\";s:6:\"3.15.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\laragon\\www\\rayarent\\vendor\\nesbot\\carbon\";}s:12:\"nette/schema\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"nette/schema\";s:10:\"\0*\0version\";s:5:\"1.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\laragon\\www\\rayarent\\vendor\\nette\\schema\";}s:11:\"nette/utils\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"nette/utils\";s:10:\"\0*\0version\";s:5:\"4.1.6\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\laragon\\www\\rayarent\\vendor\\nette\\utils\";}s:16:\"nikic/php-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"nikic/php-parser\";s:10:\"\0*\0version\";s:5:\"5.9.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\laragon\\www\\rayarent\\vendor\\nikic\\php-parser\";}s:19:\"nunomaduro/termwind\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"nunomaduro/termwind\";s:10:\"\0*\0version\";s:5:\"2.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\laragon\\www\\rayarent\\vendor\\nunomaduro\\termwind\";}s:19:\"phpoption/phpoption\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"phpoption/phpoption\";s:10:\"\0*\0version\";s:6:\"1.10.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\laragon\\www\\rayarent\\vendor\\phpoption\\phpoption\";}s:9:\"psr/clock\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"psr/clock\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"C:\\laragon\\www\\rayarent\\vendor\\psr\\clock\";}s:13:\"psr/container\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"psr/container\";s:10:\"\0*\0version\";s:5:\"2.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\laragon\\www\\rayarent\\vendor\\psr\\container\";}s:20:\"psr/event-dispatcher\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"psr/event-dispatcher\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\laragon\\www\\rayarent\\vendor\\psr\\event-dispatcher\";}s:15:\"psr/http-client\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"psr/http-client\";s:10:\"\0*\0version\";s:5:\"1.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\laragon\\www\\rayarent\\vendor\\psr\\http-client\";}s:16:\"psr/http-factory\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"psr/http-factory\";s:10:\"\0*\0version\";s:5:\"1.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\laragon\\www\\rayarent\\vendor\\psr\\http-factory\";}s:16:\"psr/http-message\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"psr/http-message\";s:10:\"\0*\0version\";s:3:\"2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\laragon\\www\\rayarent\\vendor\\psr\\http-message\";}s:7:\"psr/log\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"psr/log\";s:10:\"\0*\0version\";s:5:\"3.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:38:\"C:\\laragon\\www\\rayarent\\vendor\\psr\\log\";}s:16:\"psr/simple-cache\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"psr/simple-cache\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\laragon\\www\\rayarent\\vendor\\psr\\simple-cache\";}s:9:\"psy/psysh\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"psy/psysh\";s:10:\"\0*\0version\";s:7:\"0.12.24\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"C:\\laragon\\www\\rayarent\\vendor\\psy\\psysh\";}s:17:\"ramsey/collection\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"ramsey/collection\";s:10:\"\0*\0version\";s:5:\"2.1.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\laragon\\www\\rayarent\\vendor\\ramsey\\collection\";}s:11:\"ramsey/uuid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"ramsey/uuid\";s:10:\"\0*\0version\";s:5:\"4.9.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\laragon\\www\\rayarent\\vendor\\ramsey\\uuid\";}s:13:\"symfony/clock\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"symfony/clock\";s:10:\"\0*\0version\";s:6:\"7.4.21\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\clock\";}s:15:\"symfony/console\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"symfony/console\";s:10:\"\0*\0version\";s:6:\"7.4.23\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\console\";}s:20:\"symfony/css-selector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"symfony/css-selector\";s:10:\"\0*\0version\";s:6:\"7.4.22\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\css-selector\";}s:29:\"symfony/deprecation-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"symfony/deprecation-contracts\";s:10:\"\0*\0version\";s:5:\"3.8.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:60:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\deprecation-contracts\";}s:21:\"symfony/error-handler\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"symfony/error-handler\";s:10:\"\0*\0version\";s:6:\"7.4.22\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\error-handler\";}s:24:\"symfony/event-dispatcher\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"symfony/event-dispatcher\";s:10:\"\0*\0version\";s:6:\"7.4.17\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\event-dispatcher\";}s:34:\"symfony/event-dispatcher-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"symfony/event-dispatcher-contracts\";s:10:\"\0*\0version\";s:5:\"3.8.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\event-dispatcher-contracts\";}s:14:\"symfony/finder\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"symfony/finder\";s:10:\"\0*\0version\";s:6:\"7.4.23\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\finder\";}s:23:\"symfony/http-foundation\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"symfony/http-foundation\";s:10:\"\0*\0version\";s:6:\"7.4.23\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\http-foundation\";}s:19:\"symfony/http-kernel\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"symfony/http-kernel\";s:10:\"\0*\0version\";s:6:\"7.4.23\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\http-kernel\";}s:14:\"symfony/mailer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"symfony/mailer\";s:10:\"\0*\0version\";s:6:\"7.4.22\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\mailer\";}s:12:\"symfony/mime\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"symfony/mime\";s:10:\"\0*\0version\";s:6:\"7.4.22\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\mime\";}s:22:\"symfony/polyfill-ctype\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-ctype\";s:10:\"\0*\0version\";s:6:\"1.37.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\polyfill-ctype\";}s:30:\"symfony/polyfill-intl-grapheme\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"symfony/polyfill-intl-grapheme\";s:10:\"\0*\0version\";s:6:\"1.43.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\polyfill-intl-grapheme\";}s:25:\"symfony/polyfill-intl-idn\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"symfony/polyfill-intl-idn\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\polyfill-intl-idn\";}s:32:\"symfony/polyfill-intl-normalizer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"symfony/polyfill-intl-normalizer\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\polyfill-intl-normalizer\";}s:25:\"symfony/polyfill-mbstring\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"symfony/polyfill-mbstring\";s:10:\"\0*\0version\";s:6:\"1.43.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\polyfill-mbstring\";}s:22:\"symfony/polyfill-php80\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php80\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\polyfill-php80\";}s:22:\"symfony/polyfill-php82\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php82\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\polyfill-php82\";}s:22:\"symfony/polyfill-php83\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php83\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\polyfill-php83\";}s:22:\"symfony/polyfill-php84\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php84\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\polyfill-php84\";}s:22:\"symfony/polyfill-php85\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php85\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\polyfill-php85\";}s:22:\"symfony/polyfill-php86\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php86\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\polyfill-php86\";}s:21:\"symfony/polyfill-time\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"symfony/polyfill-time\";s:10:\"\0*\0version\";s:6:\"1.42.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\polyfill-time\";}s:21:\"symfony/polyfill-uuid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"symfony/polyfill-uuid\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\polyfill-uuid\";}s:15:\"symfony/process\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"symfony/process\";s:10:\"\0*\0version\";s:6:\"7.4.22\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\process\";}s:15:\"symfony/routing\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"symfony/routing\";s:10:\"\0*\0version\";s:6:\"7.4.23\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\routing\";}s:25:\"symfony/service-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"symfony/service-contracts\";s:10:\"\0*\0version\";s:5:\"3.8.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\service-contracts\";}s:14:\"symfony/string\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"symfony/string\";s:10:\"\0*\0version\";s:6:\"7.4.22\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\string\";}s:19:\"symfony/translation\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"symfony/translation\";s:10:\"\0*\0version\";s:6:\"7.4.23\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\translation\";}s:29:\"symfony/translation-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"symfony/translation-contracts\";s:10:\"\0*\0version\";s:5:\"3.8.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:60:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\translation-contracts\";}s:11:\"symfony/uid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"symfony/uid\";s:10:\"\0*\0version\";s:6:\"7.4.21\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\uid\";}s:18:\"symfony/var-dumper\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"symfony/var-dumper\";s:10:\"\0*\0version\";s:6:\"7.4.22\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\var-dumper\";}s:33:\"tijsverkoyen/css-to-inline-styles\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"tijsverkoyen/css-to-inline-styles\";s:10:\"\0*\0version\";s:5:\"2.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"C:\\laragon\\www\\rayarent\\vendor\\tijsverkoyen\\css-to-inline-styles\";}s:16:\"vlucas/phpdotenv\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"vlucas/phpdotenv\";s:10:\"\0*\0version\";s:5:\"5.8.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\laragon\\www\\rayarent\\vendor\\vlucas\\phpdotenv\";}s:19:\"voku/portable-ascii\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"voku/portable-ascii\";s:10:\"\0*\0version\";s:5:\"2.1.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\laragon\\www\\rayarent\\vendor\\voku\\portable-ascii\";}s:15:\"composer/semver\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"composer/semver\";s:10:\"\0*\0version\";s:5:\"3.6.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\laragon\\www\\rayarent\\vendor\\composer\\semver\";}s:14:\"fakerphp/faker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"fakerphp/faker\";s:10:\"\0*\0version\";s:6:\"1.24.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^1.23\";s:7:\"\0*\0path\";s:45:\"C:\\laragon\\www\\rayarent\\vendor\\fakerphp\\faker\";}s:11:\"filp/whoops\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"filp/whoops\";s:10:\"\0*\0version\";s:6:\"2.19.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\laragon\\www\\rayarent\\vendor\\filp\\whoops\";}s:21:\"hamcrest/hamcrest-php\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"hamcrest/hamcrest-php\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"C:\\laragon\\www\\rayarent\\vendor\\hamcrest\\hamcrest-php\";}s:22:\"laravel/agent-detector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"laravel/agent-detector\";s:10:\"\0*\0version\";s:5:\"2.0.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\laragon\\www\\rayarent\\vendor\\laravel\\agent-detector\";}s:13:\"laravel/boost\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"laravel/boost\";s:10:\"\0*\0version\";s:6:\"2.10.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^2.10\";s:7:\"\0*\0path\";s:44:\"C:\\laragon\\www\\rayarent\\vendor\\laravel\\boost\";}s:11:\"laravel/mcp\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"laravel/mcp\";s:10:\"\0*\0version\";s:5:\"1.1.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\laragon\\www\\rayarent\\vendor\\laravel\\mcp\";}s:12:\"laravel/pail\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"laravel/pail\";s:10:\"\0*\0version\";s:5:\"1.2.7\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^1.2.5\";s:7:\"\0*\0path\";s:43:\"C:\\laragon\\www\\rayarent\\vendor\\laravel\\pail\";}s:11:\"laravel/pao\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"laravel/pao\";s:10:\"\0*\0version\";s:5:\"1.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^1.0.6\";s:7:\"\0*\0path\";s:42:\"C:\\laragon\\www\\rayarent\\vendor\\laravel\\pao\";}s:12:\"laravel/pint\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"laravel/pint\";s:10:\"\0*\0version\";s:6:\"1.32.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^1.27\";s:7:\"\0*\0path\";s:43:\"C:\\laragon\\www\\rayarent\\vendor\\laravel\\pint\";}s:14:\"laravel/roster\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"laravel/roster\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\laragon\\www\\rayarent\\vendor\\laravel\\roster\";}s:15:\"mockery/mockery\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"mockery/mockery\";s:10:\"\0*\0version\";s:6:\"1.6.15\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^1.6\";s:7:\"\0*\0path\";s:46:\"C:\\laragon\\www\\rayarent\\vendor\\mockery\\mockery\";}s:17:\"myclabs/deep-copy\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"myclabs/deep-copy\";s:10:\"\0*\0version\";s:6:\"1.14.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\laragon\\www\\rayarent\\vendor\\myclabs\\deep-copy\";}s:20:\"nunomaduro/collision\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"nunomaduro/collision\";s:10:\"\0*\0version\";s:5:\"8.9.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^8.6\";s:7:\"\0*\0path\";s:51:\"C:\\laragon\\www\\rayarent\\vendor\\nunomaduro\\collision\";}s:16:\"phar-io/manifest\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"phar-io/manifest\";s:10:\"\0*\0version\";s:5:\"2.0.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\laragon\\www\\rayarent\\vendor\\phar-io\\manifest\";}s:15:\"phar-io/version\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"phar-io/version\";s:10:\"\0*\0version\";s:5:\"3.2.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\laragon\\www\\rayarent\\vendor\\phar-io\\version\";}s:25:\"phpunit/php-code-coverage\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"phpunit/php-code-coverage\";s:10:\"\0*\0version\";s:6:\"12.5.8\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"C:\\laragon\\www\\rayarent\\vendor\\phpunit\\php-code-coverage\";}s:25:\"phpunit/php-file-iterator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"phpunit/php-file-iterator\";s:10:\"\0*\0version\";s:5:\"6.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"C:\\laragon\\www\\rayarent\\vendor\\phpunit\\php-file-iterator\";}s:19:\"phpunit/php-invoker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"phpunit/php-invoker\";s:10:\"\0*\0version\";s:5:\"6.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\laragon\\www\\rayarent\\vendor\\phpunit\\php-invoker\";}s:25:\"phpunit/php-text-template\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"phpunit/php-text-template\";s:10:\"\0*\0version\";s:5:\"5.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"C:\\laragon\\www\\rayarent\\vendor\\phpunit\\php-text-template\";}s:17:\"phpunit/php-timer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"phpunit/php-timer\";s:10:\"\0*\0version\";s:5:\"8.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\laragon\\www\\rayarent\\vendor\\phpunit\\php-timer\";}s:15:\"phpunit/phpunit\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"phpunit/phpunit\";s:10:\"\0*\0version\";s:7:\"12.5.43\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:8:\"^12.5.12\";s:7:\"\0*\0path\";s:46:\"C:\\laragon\\www\\rayarent\\vendor\\phpunit\\phpunit\";}s:20:\"sebastian/cli-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"sebastian/cli-parser\";s:10:\"\0*\0version\";s:5:\"4.2.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\laragon\\www\\rayarent\\vendor\\sebastian\\cli-parser\";}s:20:\"sebastian/comparator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"sebastian/comparator\";s:10:\"\0*\0version\";s:5:\"7.1.8\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\laragon\\www\\rayarent\\vendor\\sebastian\\comparator\";}s:20:\"sebastian/complexity\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"sebastian/complexity\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\laragon\\www\\rayarent\\vendor\\sebastian\\complexity\";}s:14:\"sebastian/diff\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"sebastian/diff\";s:10:\"\0*\0version\";s:5:\"7.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\laragon\\www\\rayarent\\vendor\\sebastian\\diff\";}s:21:\"sebastian/environment\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"sebastian/environment\";s:10:\"\0*\0version\";s:5:\"8.1.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"C:\\laragon\\www\\rayarent\\vendor\\sebastian\\environment\";}s:18:\"sebastian/exporter\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"sebastian/exporter\";s:10:\"\0*\0version\";s:5:\"7.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"C:\\laragon\\www\\rayarent\\vendor\\sebastian\\exporter\";}s:22:\"sebastian/global-state\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"sebastian/global-state\";s:10:\"\0*\0version\";s:5:\"8.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\laragon\\www\\rayarent\\vendor\\sebastian\\global-state\";}s:23:\"sebastian/lines-of-code\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"sebastian/lines-of-code\";s:10:\"\0*\0version\";s:5:\"4.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"C:\\laragon\\www\\rayarent\\vendor\\sebastian\\lines-of-code\";}s:27:\"sebastian/object-enumerator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"sebastian/object-enumerator\";s:10:\"\0*\0version\";s:5:\"7.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"C:\\laragon\\www\\rayarent\\vendor\\sebastian\\object-enumerator\";}s:26:\"sebastian/object-reflector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"sebastian/object-reflector\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"C:\\laragon\\www\\rayarent\\vendor\\sebastian\\object-reflector\";}s:27:\"sebastian/recursion-context\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"sebastian/recursion-context\";s:10:\"\0*\0version\";s:5:\"7.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"C:\\laragon\\www\\rayarent\\vendor\\sebastian\\recursion-context\";}s:14:\"sebastian/type\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"sebastian/type\";s:10:\"\0*\0version\";s:5:\"6.0.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\laragon\\www\\rayarent\\vendor\\sebastian\\type\";}s:17:\"sebastian/version\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"sebastian/version\";s:10:\"\0*\0version\";s:5:\"6.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\laragon\\www\\rayarent\\vendor\\sebastian\\version\";}s:28:\"staabm/side-effects-detector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"staabm/side-effects-detector\";s:10:\"\0*\0version\";s:5:\"1.0.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"C:\\laragon\\www\\rayarent\\vendor\\staabm\\side-effects-detector\";}s:12:\"symfony/yaml\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"symfony/yaml\";s:10:\"\0*\0version\";s:6:\"7.4.23\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\laragon\\www\\rayarent\\vendor\\symfony\\yaml\";}s:17:\"theseer/tokenizer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"theseer/tokenizer\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\laragon\\www\\rayarent\\vendor\\theseer\\tokenizer\";}}s:11:\"\0*\0packages\";O:32:\"Laravel\\Roster\\PackageCollection\":2:{s:8:\"\0*\0items\";a:116:{i:0;r:5;i:1;r:13;i:2;r:21;i:3;r:29;i:4;r:37;i:5;r:45;i:6;r:53;i:7;r:61;i:8;r:69;i:9;r:77;i:10;r:85;i:11;r:93;i:12;r:101;i:13;r:109;i:14;r:117;i:15;r:125;i:16;r:133;i:17;r:141;i:18;r:149;i:19;r:157;i:20;r:165;i:21;r:173;i:22;r:181;i:23;r:189;i:24;r:197;i:25;r:205;i:26;r:213;i:27;r:221;i:28;r:229;i:29;r:237;i:30;r:245;i:31;r:253;i:32;r:261;i:33;r:269;i:34;r:277;i:35;r:285;i:36;r:293;i:37;r:301;i:38;r:309;i:39;r:317;i:40;r:325;i:41;r:333;i:42;r:341;i:43;r:349;i:44;r:357;i:45;r:365;i:46;r:373;i:47;r:381;i:48;r:389;i:49;r:397;i:50;r:405;i:51;r:413;i:52;r:421;i:53;r:429;i:54;r:437;i:55;r:445;i:56;r:453;i:57;r:461;i:58;r:469;i:59;r:477;i:60;r:485;i:61;r:493;i:62;r:501;i:63;r:509;i:64;r:517;i:65;r:525;i:66;r:533;i:67;r:541;i:68;r:549;i:69;r:557;i:70;r:565;i:71;r:573;i:72;r:581;i:73;r:589;i:74;r:597;i:75;r:605;i:76;r:613;i:77;r:621;i:78;r:629;i:79;r:637;i:80;r:645;i:81;r:653;i:82;r:661;i:83;r:669;i:84;r:677;i:85;r:685;i:86;r:693;i:87;r:701;i:88;r:709;i:89;r:717;i:90;r:725;i:91;r:733;i:92;r:741;i:93;r:749;i:94;r:757;i:95;r:765;i:96;r:773;i:97;r:781;i:98;r:789;i:99;r:797;i:100;r:805;i:101;r:813;i:102;r:821;i:103;r:829;i:104;r:837;i:105;r:845;i:106;r:853;i:107;r:861;i:108;r:869;i:109;r:877;i:110;r:885;i:111;r:893;i:112;r:901;i:113;r:909;i:114;r:917;i:115;r:925;}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:2:\"js\";O:37:\"Laravel\\Roster\\Ecosystems\\JsEcosystem\":3:{s:9:\"\0*\0byName\";a:6:{s:17:\"@tailwindcss/vite\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"@tailwindcss/vite\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";E:38:\"Laravel\\Roster\\Enums\\PackageSource:Npm\";s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^4.0.0\";s:7:\"\0*\0path\";s:54:\"C:\\laragon\\www\\rayarent\\node_modules\\@tailwindcss\\vite\";}s:12:\"concurrently\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"concurrently\";s:10:\"\0*\0version\";s:6:\"10.0.3\";s:9:\"\0*\0source\";r:1057;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:7:\"^10.0.3\";s:7:\"\0*\0path\";s:49:\"C:\\laragon\\www\\rayarent\\node_modules\\concurrently\";}s:19:\"laravel-vite-plugin\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"laravel-vite-plugin\";s:10:\"\0*\0version\";s:3:\"3.1\";s:9:\"\0*\0source\";r:1057;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^3.1\";s:7:\"\0*\0path\";s:56:\"C:\\laragon\\www\\rayarent\\node_modules\\laravel-vite-plugin\";}s:11:\"tailwindcss\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"tailwindcss\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1057;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^4.0.0\";s:7:\"\0*\0path\";s:48:\"C:\\laragon\\www\\rayarent\\node_modules\\tailwindcss\";}s:4:\"vite\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"vite\";s:10:\"\0*\0version\";s:5:\"8.0.0\";s:9:\"\0*\0source\";r:1057;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^8.0.0\";s:7:\"\0*\0path\";s:41:\"C:\\laragon\\www\\rayarent\\node_modules\\vite\";}s:18:\"@laravel/multiplex\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"@laravel/multiplex\";s:10:\"\0*\0version\";s:5:\"0.4.1\";s:9:\"\0*\0source\";r:1057;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^0.4.1\";s:7:\"\0*\0path\";s:55:\"C:\\laragon\\www\\rayarent\\node_modules\\@laravel\\multiplex\";}}s:11:\"\0*\0packages\";O:32:\"Laravel\\Roster\\PackageCollection\":2:{s:8:\"\0*\0items\";a:6:{i:0;r:1054;i:1;r:1062;i:2;r:1070;i:3;r:1078;i:4;r:1086;i:5;r:1094;}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:17:\"\0*\0packageManager\";N;}s:6:\"stacks\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:1:{i:0;E:32:\"Laravel\\Roster\\Enums\\Stack:Blade\";}}s:21:\"browserTestFrameworks\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:0:{}}s:9:\"frontends\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:0:{}}s:6:\"agents\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:2:{i:0;E:37:\"Laravel\\Roster\\Enums\\Agent:ClaudeCode\";i:1;E:32:\"Laravel\\Roster\\Enums\\Agent:Codex\";}}s:7:\"editors\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:0:{}}}', 1793890002);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_11_04_000001_add_role_phone_to_users_table', 1),
(5, '2026_11_04_000002_create_vehicles_table', 1),
(6, '2026_11_04_000003_create_vehicle_units_table', 1),
(7, '2026_11_04_000004_create_rental_packages_table', 1),
(8, '2026_11_04_000005_create_orders_table', 1),
(9, '2026_11_04_000006_create_payments_table', 1),
(10, '2026_11_04_000007_create_settings_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `vehicle_unit_id` bigint UNSIGNED NOT NULL,
  `rental_package_id` bigint UNSIGNED NOT NULL,
  `quantity` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `start_at` datetime NOT NULL,
  `end_at` datetime NOT NULL,
  `pickup_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_price` int UNSIGNED NOT NULL,
  `pay_mode` enum('dp','lunas') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('menunggu_konfirmasi','dikonfirmasi','sedang_disewa','selesai','dibatalkan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu_konfirmasi',
  `returned_at` datetime DEFAULT NULL,
  `return_condition` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `late_fee` int UNSIGNED NOT NULL DEFAULT '0',
  `damage_fee` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `code`, `user_id`, `vehicle_unit_id`, `rental_package_id`, `quantity`, `start_at`, `end_at`, `pickup_location`, `total_price`, `pay_mode`, `status`, `returned_at`, `return_condition`, `late_fee`, `damage_fee`, `created_at`, `updated_at`) VALUES
(1, 'RR-0001', 2, 101, 16, 2, '2026-11-03 08:00:00', '2026-11-05 08:00:00', 'Bandara Adi Soemarmo', 1500000, 'lunas', 'selesai', '2026-11-05 08:00:00', 'Baik', 0, 0, '2026-11-01 01:00:00', '2026-11-05 23:52:59'),
(2, 'RR-0002', 3, 110, 21, 1, '2026-11-01 08:00:00', '2026-11-02 08:00:00', 'Stasiun Solo Balapan', 750000, 'dp', 'selesai', '2026-11-02 10:00:00', 'Baik', 100000, 0, '2026-10-30 01:00:00', '2026-11-05 23:52:59'),
(3, 'RR-0003', 4, 137, 40, 1, '2026-10-30 08:00:00', '2026-10-31 08:00:00', 'Stasiun Solo Balapan', 350000, 'lunas', 'selesai', '2026-10-31 08:00:00', 'Baret di bumper belakang', 0, 250000, '2026-10-28 01:00:00', '2026-11-05 23:52:59'),
(4, 'RR-0004', 5, 117, 25, 2, '2026-10-28 08:00:00', '2026-10-29 08:00:00', 'Bandara Adi Soemarmo', 700000, 'dp', 'selesai', '2026-10-29 08:00:00', 'Baik', 0, 0, '2026-10-26 01:00:00', '2026-11-05 23:52:59'),
(5, 'RR-0005', 6, 92, 9, 1, '2026-10-26 08:00:00', '2026-10-27 08:00:00', 'Stasiun Solo Balapan', 1200000, 'lunas', 'selesai', '2026-10-27 08:00:00', 'Baik', 0, 0, '2026-10-24 01:00:00', '2026-11-05 23:52:59'),
(6, 'RR-0006', 2, 151, 51, 1, '2026-10-24 08:00:00', '2026-10-24 20:00:00', 'Stasiun Solo Balapan', 300000, 'dp', 'selesai', '2026-10-24 22:00:00', 'Baik', 100000, 0, '2026-10-22 01:00:00', '2026-11-05 23:52:59'),
(7, 'RR-0007', 3, 90, 7, 2, '2026-10-22 08:00:00', '2026-10-24 08:00:00', 'Bandara Adi Soemarmo', 6000000, 'lunas', 'selesai', '2026-10-24 08:00:00', 'Baik', 0, 0, '2026-10-20 01:00:00', '2026-11-05 23:52:59'),
(8, 'RR-0008', 4, 87, 6, 1, '2026-10-20 08:00:00', '2026-10-21 08:00:00', 'Stasiun Solo Balapan', 1400000, 'dp', 'selesai', '2026-10-21 08:00:00', 'Baret di bumper belakang', 0, 250000, '2026-10-18 01:00:00', '2026-11-05 23:52:59'),
(9, 'RR-0009', 5, 121, 28, 1, '2026-10-18 08:00:00', '2026-10-19 08:00:00', 'Stasiun Solo Balapan', 350000, 'lunas', 'selesai', '2026-10-19 08:00:00', 'Baik', 0, 0, '2026-10-16 01:00:00', '2026-11-05 23:52:59'),
(10, 'RR-0010', 6, 97, 14, 2, '2026-10-16 08:00:00', '2026-10-18 08:00:00', 'Bandara Adi Soemarmo', 2000000, 'dp', 'selesai', '2026-10-18 10:00:00', 'Baik', 100000, 0, '2026-10-14 01:00:00', '2026-11-05 23:52:59'),
(11, 'RR-0011', 2, 114, 22, 1, '2026-10-14 08:00:00', '2026-10-15 08:00:00', 'Stasiun Solo Balapan', 500000, 'lunas', 'selesai', '2026-10-15 08:00:00', 'Baik', 0, 0, '2026-10-12 01:00:00', '2026-11-05 23:52:59'),
(12, 'RR-0012', 3, 126, 33, 1, '2026-10-12 08:00:00', '2026-10-12 20:00:00', 'Stasiun Solo Balapan', 300000, 'dp', 'selesai', '2026-10-12 20:00:00', 'Baik', 0, 0, '2026-10-10 01:00:00', '2026-11-05 23:52:59'),
(13, 'RR-0013', 4, 105, 18, 2, '2026-10-10 08:00:00', '2026-10-12 08:00:00', 'Bandara Adi Soemarmo', 1100000, 'lunas', 'selesai', '2026-10-12 08:00:00', 'Baret di bumper belakang', 0, 250000, '2026-10-08 01:00:00', '2026-11-05 23:52:59'),
(14, 'RR-0014', 5, 139, 43, 1, '2026-10-08 08:00:00', '2026-10-08 20:00:00', 'Stasiun Solo Balapan', 250000, 'dp', 'selesai', '2026-10-08 22:00:00', 'Baik', 100000, 0, '2026-10-06 01:00:00', '2026-11-05 23:52:59'),
(15, 'RR-0015', 6, 134, 38, 1, '2026-10-06 08:00:00', '2026-10-07 08:00:00', 'Stasiun Solo Balapan', 300000, 'lunas', 'selesai', '2026-10-07 08:00:00', 'Baik', 0, 0, '2026-10-04 01:00:00', '2026-11-05 23:52:59'),
(16, 'RR-0016', 2, 83, 2, 2, '2026-10-04 08:00:00', '2026-10-06 08:00:00', 'Bandara Adi Soemarmo', 3200000, 'dp', 'selesai', '2026-10-06 08:00:00', 'Baik', 0, 0, '2026-10-02 01:00:00', '2026-11-05 23:52:59'),
(17, 'RR-0017', 3, 97, 14, 2, '2026-11-05 09:00:00', '2026-11-07 09:00:00', 'Stasiun Solo Balapan', 2000000, 'dp', 'sedang_disewa', NULL, NULL, 0, 0, '2026-11-03 02:00:00', '2026-11-05 23:52:59'),
(18, 'RR-0018', 4, 137, 41, 1, '2026-11-06 05:00:00', '2026-11-06 17:00:00', 'Stasiun Solo Balapan', 300000, 'lunas', 'sedang_disewa', NULL, NULL, 0, 0, '2026-11-03 22:00:00', '2026-11-05 23:52:59'),
(19, 'RR-0019', 5, 117, 24, 1, '2026-11-04 08:00:00', '2026-11-05 08:00:00', 'Stasiun Solo Balapan', 400000, 'dp', 'sedang_disewa', NULL, NULL, 0, 0, '2026-11-02 01:00:00', '2026-11-05 23:52:59'),
(20, 'RR-0020', 5, 92, 10, 2, '2026-11-07 08:00:00', '2026-11-09 08:00:00', 'Stasiun Solo Balapan', 2900000, 'dp', 'dikonfirmasi', NULL, NULL, 0, 0, '2026-11-05 01:00:00', '2026-11-05 23:52:59'),
(21, 'RR-0021', 6, 90, 7, 1, '2026-11-09 08:00:00', '2026-11-10 08:00:00', 'Stasiun Solo Balapan', 3000000, 'lunas', 'dikonfirmasi', NULL, NULL, 0, 0, '2026-11-07 01:00:00', '2026-11-05 23:52:59'),
(22, 'RR-0022', 3, 146, 46, 1, '2026-11-08 09:00:00', '2026-11-09 09:00:00', 'Stasiun Solo Balapan', 400000, 'dp', 'menunggu_konfirmasi', NULL, NULL, 0, 0, '2026-11-06 02:00:00', '2026-11-05 23:52:59'),
(23, 'RR-0023', 4, 87, 6, 1, '2026-11-11 07:00:00', '2026-11-12 07:00:00', 'Stasiun Solo Balapan', 1400000, 'lunas', 'menunggu_konfirmasi', NULL, NULL, 0, 0, '2026-11-09 00:00:00', '2026-11-05 23:52:59'),
(24, 'RR-0024', 2, 156, 54, 1, '2026-11-10 10:00:00', '2026-11-11 10:00:00', 'Stasiun Solo Balapan', 350000, 'dp', 'menunggu_konfirmasi', NULL, NULL, 0, 0, '2026-11-08 03:00:00', '2026-11-05 23:52:59'),
(25, 'RR-0025', 6, 132, 36, 1, '2026-11-12 08:00:00', '2026-11-13 08:00:00', 'Stasiun Solo Balapan', 300000, 'dp', 'dibatalkan', NULL, NULL, 0, 0, '2026-11-10 01:00:00', '2026-11-05 23:52:59');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `amount` int UNSIGNED NOT NULL,
  `status` enum('menunggu','diterima','ditolak') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu',
  `proof_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verified_by` bigint UNSIGNED DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `amount`, `status`, `proof_path`, `verified_by`, `verified_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1500000, 'diterima', NULL, 1, '2026-11-02 08:00:00', '2026-11-05 23:52:59', '2026-11-05 23:52:59'),
(2, 2, 225000, 'diterima', NULL, 1, '2026-10-31 08:00:00', '2026-11-05 23:52:59', '2026-11-05 23:52:59'),
(3, 2, 625000, 'diterima', NULL, 1, '2026-11-02 10:00:00', '2026-11-05 23:52:59', '2026-11-05 23:52:59'),
(4, 3, 350000, 'diterima', NULL, 1, '2026-10-29 08:00:00', '2026-11-05 23:52:59', '2026-11-05 23:52:59'),
(5, 3, 250000, 'diterima', NULL, 1, '2026-10-31 08:00:00', '2026-11-05 23:52:59', '2026-11-05 23:52:59'),
(6, 4, 210000, 'diterima', NULL, 1, '2026-10-27 08:00:00', '2026-11-05 23:52:59', '2026-11-05 23:52:59'),
(7, 4, 490000, 'diterima', NULL, 1, '2026-10-29 08:00:00', '2026-11-05 23:52:59', '2026-11-05 23:52:59'),
(8, 5, 1200000, 'diterima', NULL, 1, '2026-10-25 08:00:00', '2026-11-05 23:52:59', '2026-11-05 23:52:59'),
(9, 6, 90000, 'diterima', NULL, 1, '2026-10-23 08:00:00', '2026-11-05 23:52:59', '2026-11-05 23:52:59'),
(10, 6, 310000, 'diterima', NULL, 1, '2026-10-24 22:00:00', '2026-11-05 23:52:59', '2026-11-05 23:52:59'),
(11, 7, 6000000, 'diterima', NULL, 1, '2026-10-21 08:00:00', '2026-11-05 23:52:59', '2026-11-05 23:52:59'),
(12, 8, 420000, 'diterima', NULL, 1, '2026-10-19 08:00:00', '2026-11-05 23:52:59', '2026-11-05 23:52:59'),
(13, 8, 1230000, 'diterima', NULL, 1, '2026-10-21 08:00:00', '2026-11-05 23:52:59', '2026-11-05 23:52:59'),
(14, 9, 350000, 'diterima', NULL, 1, '2026-10-17 08:00:00', '2026-11-05 23:52:59', '2026-11-05 23:52:59'),
(15, 10, 600000, 'diterima', NULL, 1, '2026-10-15 08:00:00', '2026-11-05 23:52:59', '2026-11-05 23:52:59'),
(16, 10, 1500000, 'diterima', NULL, 1, '2026-10-18 10:00:00', '2026-11-05 23:52:59', '2026-11-05 23:52:59'),
(17, 11, 500000, 'diterima', NULL, 1, '2026-10-13 08:00:00', '2026-11-05 23:52:59', '2026-11-05 23:52:59'),
(18, 12, 90000, 'diterima', NULL, 1, '2026-10-11 08:00:00', '2026-11-05 23:52:59', '2026-11-05 23:52:59'),
(19, 12, 210000, 'diterima', NULL, 1, '2026-10-12 20:00:00', '2026-11-05 23:52:59', '2026-11-05 23:52:59'),
(20, 13, 1100000, 'diterima', NULL, 1, '2026-10-09 08:00:00', '2026-11-05 23:52:59', '2026-11-05 23:52:59'),
(21, 13, 250000, 'diterima', NULL, 1, '2026-10-12 08:00:00', '2026-11-05 23:52:59', '2026-11-05 23:52:59'),
(22, 14, 75000, 'diterima', NULL, 1, '2026-10-07 08:00:00', '2026-11-05 23:52:59', '2026-11-05 23:52:59'),
(23, 14, 275000, 'diterima', NULL, 1, '2026-10-08 22:00:00', '2026-11-05 23:52:59', '2026-11-05 23:52:59'),
(24, 15, 300000, 'diterima', NULL, 1, '2026-10-05 08:00:00', '2026-11-05 23:52:59', '2026-11-05 23:52:59'),
(25, 16, 960000, 'diterima', NULL, 1, '2026-10-03 08:00:00', '2026-11-05 23:52:59', '2026-11-05 23:52:59'),
(26, 16, 2240000, 'diterima', NULL, 1, '2026-10-06 08:00:00', '2026-11-05 23:52:59', '2026-11-05 23:52:59'),
(27, 17, 600000, 'diterima', NULL, 1, '2026-11-04 09:00:00', '2026-11-05 23:52:59', '2026-11-05 23:52:59'),
(28, 18, 300000, 'diterima', NULL, 1, '2026-11-05 05:00:00', '2026-11-05 23:52:59', '2026-11-05 23:52:59'),
(29, 19, 120000, 'diterima', NULL, 1, '2026-11-03 08:00:00', '2026-11-05 23:52:59', '2026-11-05 23:52:59'),
(30, 20, 870000, 'diterima', NULL, 1, '2026-11-05 06:52:59', '2026-11-05 23:52:59', '2026-11-05 23:52:59'),
(31, 21, 3000000, 'diterima', NULL, 1, '2026-11-05 06:52:59', '2026-11-05 23:52:59', '2026-11-05 23:52:59'),
(32, 22, 120000, 'menunggu', NULL, NULL, NULL, '2026-11-05 23:52:59', '2026-11-05 23:52:59'),
(33, 23, 1400000, 'ditolak', NULL, 1, '2026-11-06 01:52:59', '2026-11-05 23:52:59', '2026-11-05 23:52:59');

-- --------------------------------------------------------

--
-- Table structure for table `rental_packages`
--

CREATE TABLE `rental_packages` (
  `id` bigint UNSIGNED NOT NULL,
  `vehicle_id` bigint UNSIGNED NOT NULL,
  `service` enum('lepas_kunci','driver','driver_bbm') COLLATE utf8mb4_unicode_ci NOT NULL,
  `duration_hours` tinyint UNSIGNED NOT NULL,
  `price` int UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rental_packages`
--

INSERT INTO `rental_packages` (`id`, `vehicle_id`, `service`, `duration_hours`, `price`, `created_at`, `updated_at`) VALUES
(1, 1, 'driver', 24, 1400000, '2026-11-04 06:28:42', '2026-11-04 06:28:42'),
(2, 1, 'driver_bbm', 24, 1600000, '2026-11-04 06:28:42', '2026-11-04 06:28:42'),
(3, 2, 'driver', 24, 1350000, '2026-11-04 06:28:42', '2026-11-04 06:28:42'),
(4, 2, 'driver_bbm', 24, 1500000, '2026-11-04 06:28:42', '2026-11-04 06:28:42'),
(5, 3, 'driver', 24, 1200000, '2026-11-04 06:28:42', '2026-11-04 06:28:42'),
(6, 3, 'driver_bbm', 24, 1400000, '2026-11-04 06:28:42', '2026-11-04 06:28:42'),
(7, 4, 'driver', 24, 3000000, '2026-11-04 06:28:42', '2026-11-04 06:28:42'),
(8, 4, 'driver_bbm', 24, 3500000, '2026-11-04 06:28:42', '2026-11-04 06:28:42'),
(9, 5, 'lepas_kunci', 24, 1200000, '2026-11-04 06:28:42', '2026-11-04 06:28:42'),
(10, 5, 'driver', 24, 1450000, '2026-11-04 06:28:42', '2026-11-04 06:28:42'),
(11, 5, 'driver_bbm', 24, 1600000, '2026-11-04 06:28:42', '2026-11-04 06:28:42'),
(12, 6, 'lepas_kunci', 24, 1000000, '2026-11-04 06:28:42', '2026-11-04 06:28:42'),
(13, 7, 'lepas_kunci', 24, 900000, '2026-11-04 06:28:42', '2026-11-04 06:28:42'),
(14, 7, 'driver', 24, 1000000, '2026-11-04 06:28:42', '2026-11-04 06:28:42'),
(15, 7, 'driver_bbm', 24, 1150000, '2026-11-04 06:28:42', '2026-11-04 06:28:42'),
(16, 8, 'driver', 24, 750000, '2026-11-04 06:28:42', '2026-11-04 06:28:42'),
(17, 8, 'driver_bbm', 24, 900000, '2026-11-04 06:28:42', '2026-11-04 06:28:42'),
(18, 9, 'lepas_kunci', 24, 550000, '2026-11-04 06:28:42', '2026-11-04 06:28:42'),
(19, 10, 'lepas_kunci', 24, 500000, '2026-11-04 06:28:42', '2026-11-04 06:28:42'),
(20, 11, 'driver', 24, 550000, '2026-11-04 06:28:42', '2026-11-04 06:28:42'),
(21, 11, 'driver_bbm', 24, 750000, '2026-11-04 06:28:42', '2026-11-04 06:28:42'),
(22, 12, 'driver', 24, 500000, '2026-11-04 06:28:42', '2026-11-04 06:28:42'),
(23, 12, 'driver_bbm', 24, 650000, '2026-11-04 06:28:42', '2026-11-04 06:28:42'),
(24, 13, 'lepas_kunci', 24, 400000, '2026-11-04 06:28:42', '2026-11-04 06:28:42'),
(25, 13, 'lepas_kunci', 12, 350000, '2026-11-04 06:28:42', '2026-11-04 06:28:42'),
(26, 14, 'lepas_kunci', 24, 350000, '2026-11-04 06:28:42', '2026-11-04 06:28:42'),
(27, 14, 'lepas_kunci', 12, 300000, '2026-11-04 06:28:42', '2026-11-04 06:28:42'),
(28, 15, 'lepas_kunci', 24, 350000, '2026-11-04 06:28:42', '2026-11-04 06:28:42'),
(29, 15, 'lepas_kunci', 12, 300000, '2026-11-04 06:28:42', '2026-11-04 06:28:42'),
(30, 16, 'lepas_kunci', 24, 300000, '2026-11-04 06:28:42', '2026-11-04 06:28:42'),
(31, 16, 'lepas_kunci', 12, 250000, '2026-11-04 06:28:42', '2026-11-04 06:28:42'),
(32, 17, 'lepas_kunci', 24, 350000, '2026-11-04 06:28:43', '2026-11-04 06:28:43'),
(33, 17, 'lepas_kunci', 12, 300000, '2026-11-04 06:28:43', '2026-11-04 06:28:43'),
(34, 18, 'lepas_kunci', 24, 350000, '2026-11-04 06:28:43', '2026-11-04 06:28:43'),
(35, 18, 'lepas_kunci', 12, 300000, '2026-11-04 06:28:43', '2026-11-04 06:28:43'),
(36, 19, 'lepas_kunci', 24, 300000, '2026-11-04 06:28:43', '2026-11-04 06:28:43'),
(37, 19, 'lepas_kunci', 12, 250000, '2026-11-04 06:28:43', '2026-11-04 06:28:43'),
(38, 20, 'lepas_kunci', 24, 300000, '2026-11-04 06:28:43', '2026-11-04 06:28:43'),
(39, 20, 'lepas_kunci', 12, 250000, '2026-11-04 06:28:43', '2026-11-04 06:28:43'),
(40, 21, 'lepas_kunci', 24, 350000, '2026-11-04 06:28:43', '2026-11-04 06:28:43'),
(41, 21, 'lepas_kunci', 12, 300000, '2026-11-04 06:28:43', '2026-11-04 06:28:43'),
(42, 22, 'lepas_kunci', 24, 300000, '2026-11-04 06:28:43', '2026-11-04 06:28:43'),
(43, 22, 'lepas_kunci', 12, 250000, '2026-11-04 06:28:43', '2026-11-04 06:28:43'),
(44, 23, 'lepas_kunci', 24, 400000, '2026-11-04 06:28:43', '2026-11-04 06:28:43'),
(45, 23, 'lepas_kunci', 12, 350000, '2026-11-04 06:28:43', '2026-11-04 06:28:43'),
(46, 24, 'lepas_kunci', 24, 400000, '2026-11-04 06:28:43', '2026-11-04 06:28:43'),
(47, 24, 'lepas_kunci', 12, 350000, '2026-11-04 06:28:43', '2026-11-04 06:28:43'),
(48, 25, 'lepas_kunci', 24, 350000, '2026-11-04 06:28:43', '2026-11-04 06:28:43'),
(49, 25, 'lepas_kunci', 12, 300000, '2026-11-04 06:28:43', '2026-11-04 06:28:43'),
(50, 26, 'lepas_kunci', 24, 350000, '2026-11-04 06:28:43', '2026-11-04 06:28:43'),
(51, 26, 'lepas_kunci', 12, 300000, '2026-11-04 06:28:43', '2026-11-04 06:28:43'),
(52, 27, 'lepas_kunci', 24, 350000, '2026-11-04 06:28:43', '2026-11-04 06:28:43'),
(53, 27, 'lepas_kunci', 12, 300000, '2026-11-04 06:28:43', '2026-11-04 06:28:43'),
(54, 28, 'lepas_kunci', 24, 350000, '2026-11-04 06:28:43', '2026-11-04 06:28:43'),
(55, 28, 'lepas_kunci', 12, 250000, '2026-11-04 06:28:43', '2026-11-04 06:28:43');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('G9OPGrN7PAYCy3KkRgmc1WF9AqQMARYkhRIIrInJ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/158.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJCT0gyQzlaVnBCN2xvR3B5Zk5tRFpadXNMZDBEZUFoMzNLakhKTWVlIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAiLCJyb3V0ZSI6ImhvbWUifX0=', 1795530137),
('ma1IDNOodbab2bR3KAewVz6CSsMuh1cxLvTzLrQ6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.147.0 Chrome/150.0.7871.250 Electron/43.8.9 Safari/537.36', 'eyJfdG9rZW4iOiIyM1VEWnBLcGxPYUpSamtTTHBEdWRMWmt2SnhnQW1XUE1ySFpRMEhjIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1795521788),
('stxXt6DEZGcHNSZqmUEIsPvwfIAvTyZ9ku1Xo1Zx', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.147.0 Chrome/150.0.7871.250 Electron/43.8.9 Safari/537.36', 'eyJfdG9rZW4iOiJ1R1lYSUhPODNKUHBGUzhFNHlyUU5nWDRvUnl5b1JTVTVycjk1Z28yIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9rb250YWsiLCJyb3V0ZSI6ImtvbnRhayJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1795528282);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`key`, `value`) VALUES
('dp_percent', '30'),
('late_fee_per_hour', '50000');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('admin','pelanggan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pelanggan',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `role`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin RAYARENT', 'admin@rayarent.id', '0271000000', 'admin', NULL, '$2y$12$n50/sT.hm1mpOX8wRW6B2OnBNKjs7mg5Zrz8RjyIpde2jQaKExaAe', 'vExiHmgMjTR3CsPRBWFCsyovoBHhhdX8TyEFWSfhyjW09f9k05x8WNm6T47H', '2026-11-04 06:28:42', '2026-11-04 06:28:42'),
(2, 'Budi Santoso', 'budi@mail.com', '08123456789', 'pelanggan', NULL, '$2y$12$3rtMlLjpov6YdCRw20e6.OgE45jSxHxOjaJWoWifFqClud5ZqlA7m', NULL, '2026-11-04 06:28:42', '2026-11-04 06:28:42'),
(3, 'Siti Rahayu', 'siti@mail.com', '081111111111', 'pelanggan', NULL, '$2y$12$0hX9sY9Buh89XQunS0CU6.WMY9Nb3Gu7NvNE4mR6iWwLZRzYd9Nei', NULL, '2026-11-05 23:52:58', '2026-11-05 23:52:58'),
(4, 'Andi Wijaya', 'andi@mail.com', '082222222222', 'pelanggan', NULL, '$2y$12$Vppb7d1fMBX8S7te/VgzduAJlaP5IFrFHPVFODnTnUU2kLCXJ/gJS', NULL, '2026-11-05 23:52:58', '2026-11-05 23:52:58'),
(5, 'Dewi Lestari', 'dewi@mail.com', '083333333333', 'pelanggan', NULL, '$2y$12$mnudo5lZjbeSg9PabsGYeuF8hjjKBfrpWxyY4aKmlcVUqI32.u8PK', NULL, '2026-11-05 23:52:59', '2026-11-05 23:52:59'),
(6, 'Rizky Pratama', 'rizky@mail.com', '084444444444', 'pelanggan', NULL, '$2y$12$0Rkhsy/K2v85ijooqLyKCu4pQrMfBuDIbR/NbZmtonjiBJqUCa2H6', NULL, '2026-11-05 23:52:59', '2026-11-05 23:52:59');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `seat_label` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacity` tinyint UNSIGNED NOT NULL,
  `transmission` enum('Matic','Manual') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `name`, `seat_label`, `capacity`, `transmission`, `photo`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'ELF Long', '19', 19, NULL, 'vehicles/bce9c948-5097-4192-b861-36eb23cd7916.jpg', 1, '2026-11-04 06:28:42', '2026-11-24 13:32:05'),
(2, 'Hiace Premio', '14', 14, NULL, 'vehicles/ff0893fc-9f15-4775-a8f5-d303336f8757.jpg', 1, '2026-11-04 06:28:42', '2026-11-24 13:37:26'),
(3, 'Hiace Commuter', '14', 14, NULL, 'vehicles/7fa86b53-bad7-4e8a-b8e2-46495fafc94e.jpg', 1, '2026-11-04 06:28:42', '2026-11-24 13:37:06'),
(4, 'Alphard', '6', 6, NULL, 'vehicles/bbb870c2-f755-4d80-b5f0-c943af16993f.jpg', 1, '2026-11-04 06:28:42', '2026-11-24 13:23:57'),
(5, 'Fortuner GR', '6-8', 8, NULL, 'vehicles/f2ec88b7-1401-4233-a837-b7c3008e3e5c.jpg', 1, '2026-11-04 06:28:42', '2026-11-24 13:33:24'),
(6, 'Fortuner TRD', '7-8', 8, NULL, 'vehicles/ee0e1c06-5252-4763-bf86-a820cf004ad5.jpg', 1, '2026-11-04 06:28:42', '2026-11-24 13:34:45'),
(7, 'Innova Zenix', '7', 7, NULL, 'vehicles/5b36c74a-41fa-4849-a0d2-6095f9e23681.jpg', 1, '2026-11-04 06:28:42', '2026-11-24 13:38:54'),
(8, 'Innova Reborn', '7', 7, NULL, 'vehicles/bdcaf79a-3d10-4f29-a776-ac46e417cf55.jpg', 1, '2026-11-04 06:28:42', '2026-11-24 13:38:26'),
(9, 'Innova Reborn Matic', '7-8', 8, 'Matic', 'vehicles/d226639b-ddb7-4e72-950e-dfb08f55dd80.jpg', 1, '2026-11-04 06:28:42', '2026-11-24 13:38:26'),
(10, 'Innova Reborn Manual', '7-8', 8, 'Manual', 'vehicles/acc3d3c5-f8ab-4b49-89b2-6456c72941a5.jpg', 1, '2026-11-04 06:28:42', '2026-11-24 13:38:26'),
(11, 'Avanza New', '7', 7, NULL, 'vehicles/f1b5c8b5-1586-4c01-bea5-372bfdc4e09f.jpg', 1, '2026-11-04 06:28:42', '2026-11-24 13:26:09'),
(12, 'Grand Avanza', '7', 7, NULL, 'vehicles/067aaec6-538b-4e86-bb14-4a47188261d7.jpg', 1, '2026-11-04 06:28:42', '2026-11-24 13:36:39'),
(13, 'Jazz RS Matic', '3-4', 4, 'Matic', 'vehicles/5204291f-5516-4653-b3ce-6759e6f167bc.jpg', 1, '2026-11-04 06:28:42', '2026-11-24 13:40:01'),
(14, 'Jazz RS Manual', '3-4', 4, 'Manual', 'vehicles/5002c4ef-58be-4a85-9593-52dfa651ee81.jpg', 1, '2026-11-04 06:28:42', '2026-11-24 13:40:01'),
(15, 'Yaris Matic', '3-4', 4, 'Matic', 'vehicles/11e17315-934d-4b38-9eb3-9006e99f04ed.jpg', 1, '2026-11-04 06:28:42', '2026-11-24 13:08:08'),
(16, 'Yaris Manual', '3-4', 4, 'Manual', 'vehicles/abd71106-9f6a-48a1-8f92-2840d915b0a3.jpg', 1, '2026-11-04 06:28:42', '2026-11-24 13:08:08'),
(17, 'Agya Matic', '3-4', 4, 'Matic', 'vehicles/5e99c3d2-6242-4646-bf7e-38204e9e63ec.jpg', 1, '2026-11-04 06:28:43', '2026-11-24 13:18:15'),
(18, 'Ayla Matic', '3-4', 4, 'Matic', 'vehicles/0ec57b45-c74c-4a15-8f88-a05f0a445b76.jpg', 1, '2026-11-04 06:28:43', '2026-11-24 13:24:45'),
(19, 'Agya Manual', '3-4', 4, 'Manual', 'vehicles/f2efaa0e-14bd-4e2a-b0ec-c37517506fba.jpg', 1, '2026-11-04 06:28:43', '2026-11-24 13:18:15'),
(20, 'Ayla Manual', '3-4', 4, 'Manual', 'vehicles/74119918-d90f-4304-bf83-247e02d03c26.jpg', 1, '2026-11-04 06:28:43', '2026-11-24 13:24:45'),
(21, 'Brio Matic', '3-4', 4, 'Matic', 'vehicles/e71aa0d7-bd32-42cd-a05c-778e5e99a88f.jpg', 1, '2026-11-04 06:28:43', '2026-11-24 13:42:58'),
(22, 'Brio Manual', '3-4', 4, 'Manual', 'vehicles/610e4bc6-6c6a-4df1-bbe6-48fda653a4af.jpg', 1, '2026-11-04 06:28:43', '2026-11-24 13:42:58'),
(23, 'New Avanza Matic', '3-4', 4, 'Matic', 'vehicles/f1a3a37b-5d49-4923-9fae-1b171cd98d79.jpg', 1, '2026-11-04 06:28:43', '2026-11-24 13:27:29'),
(24, 'Xenia Matic', '3-4', 4, 'Matic', 'vehicles/06fc06a7-757e-46d8-9eb2-3ce6a7083cb8.jpg', 1, '2026-11-04 06:28:43', '2026-11-24 13:40:59'),
(25, 'New Avanza Manual', '3-4', 4, 'Manual', 'vehicles/0d54687d-297f-4d28-958f-42f179fbfbf2.jpg', 1, '2026-11-04 06:28:43', '2026-11-24 13:27:29'),
(26, 'Xenia Manual', '3-4', 4, 'Manual', 'vehicles/272afa00-5bb9-4ef2-bc84-6bf20dced852.jpg', 1, '2026-11-04 06:28:43', '2026-11-24 13:40:59'),
(27, 'Grand Avanza Matic', '3-4', 4, 'Matic', 'vehicles/e7919739-6a20-4fd6-b068-a5d9170ea73d.jpg', 1, '2026-11-04 06:28:43', '2026-11-24 13:36:39'),
(28, 'Grand Avanza Manual', '3-4', 4, 'Manual', 'vehicles/070edfe1-c075-414b-83ed-0a9d28ab3a5e.jpg', 1, '2026-11-04 06:28:43', '2026-11-24 13:36:39');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_units`
--

CREATE TABLE `vehicle_units` (
  `id` bigint UNSIGNED NOT NULL,
  `vehicle_id` bigint UNSIGNED NOT NULL,
  `plate_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('tersedia','disewa','perawatan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tersedia',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicle_units`
--

INSERT INTO `vehicle_units` (`id`, `vehicle_id`, `plate_number`, `status`, `created_at`, `updated_at`) VALUES
(83, 1, 'AD 9618 LC', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(84, 1, 'AD 8366 WD', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(85, 2, 'AD 1964 MK', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(86, 2, 'AD 5927 DP', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(87, 3, 'AD 3968 CB', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(88, 3, 'AD 9679 WF', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(89, 3, 'AD 1625 DJ', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(90, 4, 'AD 7147 TF', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(91, 4, 'AD 7640 DG', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(92, 5, 'AD 7514 YD', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(93, 5, 'AD 4971 MT', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(94, 6, 'AD 5098 DG', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(95, 6, 'AD 9778 KH', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(96, 6, 'AD 8167 FD', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(97, 7, 'AD 6359 UC', 'disewa', '2026-11-05 23:50:32', '2026-11-05 23:52:59'),
(98, 7, 'AD 1721 MP', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(99, 7, 'AD 6890 YL', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(100, 7, 'AD 9373 DU', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(101, 8, 'AD 3661 HW', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(102, 8, 'AD 6763 AR', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(103, 8, 'AD 8632 FF', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(104, 8, 'AD 7177 UE', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(105, 9, 'AD 7361 KJ', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(106, 9, 'AD 7765 TF', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(107, 9, 'AD 4565 FM', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(108, 10, 'AD 9598 EC', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(109, 10, 'AD 7665 HA', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(110, 11, 'AD 8837 AR', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(111, 11, 'AD 2075 TG', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(112, 11, 'AD 5524 FL', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(113, 11, 'AD 4873 PD', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(114, 12, 'AD 9465 KR', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(115, 12, 'AD 8223 SY', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(116, 12, 'AD 3540 GF', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(117, 13, 'AD 1205 HW', 'disewa', '2026-11-05 23:50:32', '2026-11-05 23:52:59'),
(118, 13, 'AD 3521 LC', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(119, 13, 'AD 8081 JF', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(120, 14, 'AD 3287 NJ', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(121, 15, 'AD 2194 HP', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(122, 15, 'AD 9144 RD', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(123, 16, 'AD 9099 YN', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(124, 16, 'AD 9411 ML', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(125, 16, 'AD 9970 TH', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(126, 17, 'AD 4201 DA', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(127, 17, 'AD 9996 PJ', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(128, 17, 'AD 5294 FD', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(129, 17, 'AD 8012 ER', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(130, 18, 'AD 7261 AE', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(131, 18, 'AD 3709 NM', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(132, 19, 'AD 6363 CB', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(133, 19, 'AD 6035 FG', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(134, 20, 'AD 6997 EB', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(135, 20, 'AD 4919 FR', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(136, 20, 'AD 3661 FM', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(137, 21, 'AD 9060 YG', 'disewa', '2026-11-05 23:50:32', '2026-11-05 23:52:59'),
(138, 21, 'AD 3054 SW', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(139, 22, 'AD 5595 YP', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(140, 22, 'AD 2570 KF', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(141, 22, 'AD 2817 ME', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(142, 22, 'AD 6229 MH', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(143, 23, 'AD 6168 RJ', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(144, 23, 'AD 6451 RC', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(145, 23, 'AD 9705 GS', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(146, 24, 'AD 9568 BH', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(147, 24, 'AD 5031 UH', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(148, 24, 'AD 4253 RE', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(149, 24, 'AD 9340 YH', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(150, 25, 'AD 4287 YH', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(151, 26, 'AD 1954 YG', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(152, 26, 'AD 8654 DF', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(153, 26, 'AD 4777 LM', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(154, 26, 'AD 8543 MK', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(155, 27, 'AD 1128 LS', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32'),
(156, 28, 'AD 7268 NM', 'tersedia', '2026-11-05 23:50:32', '2026-11-05 23:50:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_code_unique` (`code`),
  ADD KEY `orders_user_id_foreign` (`user_id`),
  ADD KEY `orders_rental_package_id_foreign` (`rental_package_id`),
  ADD KEY `orders_vehicle_unit_id_start_at_end_at_index` (`vehicle_unit_id`,`start_at`,`end_at`),
  ADD KEY `orders_status_index` (`status`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_order_id_foreign` (`order_id`),
  ADD KEY `payments_verified_by_foreign` (`verified_by`);

--
-- Indexes for table `rental_packages`
--
ALTER TABLE `rental_packages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rental_packages_vehicle_id_service_duration_hours_unique` (`vehicle_id`,`service`,`duration_hours`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vehicle_units`
--
ALTER TABLE `vehicle_units`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehicle_units_plate_number_unique` (`plate_number`),
  ADD KEY `vehicle_units_vehicle_id_foreign` (`vehicle_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `rental_packages`
--
ALTER TABLE `rental_packages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `vehicle_units`
--
ALTER TABLE `vehicle_units`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_rental_package_id_foreign` FOREIGN KEY (`rental_package_id`) REFERENCES `rental_packages` (`id`),
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_vehicle_unit_id_foreign` FOREIGN KEY (`vehicle_unit_id`) REFERENCES `vehicle_units` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `rental_packages`
--
ALTER TABLE `rental_packages`
  ADD CONSTRAINT `rental_packages_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicle_units`
--
ALTER TABLE `vehicle_units`
  ADD CONSTRAINT `vehicle_units_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
