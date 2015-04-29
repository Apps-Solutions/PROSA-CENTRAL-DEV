-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 29-04-2015 a las 16:57:10
-- Versión del servidor: 5.6.21
-- Versión de PHP: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `prosa`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `PRA_ALERT`
--

CREATE TABLE IF NOT EXISTS `PRA_ALERT` (
  `ID_ALERT` int(11) NOT NULL,
  `AL_CL_ID_CLIENT` int(11) NOT NULL,
  `AL_TIMESTAMP` int(11) NOT NULL,
  `AL_SE_ID_SERVICE` int(11) DEFAULT NULL,
  `AL_TEXT` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL,
  `AL_STATUS` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `PRA_CLIENT`
--

CREATE TABLE IF NOT EXISTS `PRA_CLIENT` (
`ID_CLIENT` int(11) NOT NULL,
  `CL_CLIENT` varchar(64) COLLATE utf8_spanish_ci NOT NULL,
  `CL_CODE` varchar(10) COLLATE utf8_spanish_ci NOT NULL,
  `CL_STATUS` int(11) NOT NULL,
  `CL_TIMESTAMP` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `PRA_LAST_TOTAL`
--

CREATE TABLE IF NOT EXISTS `PRA_LAST_TOTAL` (
  `LT_SE_ID_SERVICE` int(11) NOT NULL,
  `LT_TOTAL` int(11) NOT NULL,
  `LT_TIMESTAMP` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `PRA_MAINTENANCE`
--

CREATE TABLE IF NOT EXISTS `PRA_MAINTENANCE` (
  `ID_MAINTENANCE` int(11) NOT NULL,
  `MA_SE_ID_SERVICE` int(11) NOT NULL,
  `MA_START` int(11) NOT NULL,
  `MA_END` int(11) NOT NULL,
  `MA_TIMESTAMP` int(11) NOT NULL,
  `MA_STATUS` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `PRA_NETWORK`
--

CREATE TABLE IF NOT EXISTS `PRA_NETWORK` (
  `ID_NETWORK` varchar(5) COLLATE utf8_spanish_ci NOT NULL,
  `NETWORK` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `EMITTER` varchar(5) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `PRA_RESPONSE_CODE`
--

CREATE TABLE IF NOT EXISTS `PRA_RESPONSE_CODE` (
  `ID_RESPONSE_CODE` int(11) NOT NULL,
  `RC_RESPONSE_CODE` varchar(45) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `PRA_SERVICE`
--

CREATE TABLE IF NOT EXISTS `PRA_SERVICE` (
  `ID_SERVICE` int(11) NOT NULL,
  `SE_SERVICE` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `SE_COMMAND` varchar(10) COLLATE utf8_spanish_ci NOT NULL,
  `SE_STATUS` int(11) NOT NULL,
  `SE_TIMESTAMP` int(11) NOT NULL,
  `SE_ORDER` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `PRA_SERVICE_CLIENT`
--

CREATE TABLE IF NOT EXISTS `PRA_SERVICE_CLIENT` (
  `SC_SE_ID_SERVICE` int(11) NOT NULL,
  `SC_CL_ID_CLIENT` int(11) NOT NULL,
  `SC_TIMESTAMP` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `PRA_SERVICE_USER`
--

CREATE TABLE IF NOT EXISTS `PRA_SERVICE_USER` (
  `SU_SE_ID_SERVICE` int(11) NOT NULL,
  `SU_USER` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `SU_TIMESTAMP` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `PRA_TBL_APP_PREAUTORIZADOR`
--

CREATE TABLE IF NOT EXISTS `PRA_TBL_APP_PREAUTORIZADOR` (
  `ID` int(4) NOT NULL,
  `FIID_TARJ` varchar(4) COLLATE utf8_spanish_ci NOT NULL,
  `PREFIJO` int(6) NOT NULL,
  `PREA` varchar(1) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `PRA_TBL_SMS_LOG`
--

CREATE TABLE IF NOT EXISTS `PRA_TBL_SMS_LOG` (
  `PAN` varchar(19) COLLATE utf8_spanish_ci NOT NULL,
  `PHONE` varchar(15) COLLATE utf8_spanish_ci NOT NULL,
  `PHONE_NAME` varchar(25) COLLATE utf8_spanish_ci NOT NULL,
  `SKY` varchar(15) COLLATE utf8_spanish_ci NOT NULL,
  `EMAIL` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `AMOUNT` float(26,8) NOT NULL,
  `PCODE` varchar(6) COLLATE utf8_spanish_ci NOT NULL,
  `TXN_TYPE` varchar(1) COLLATE utf8_spanish_ci NOT NULL,
  `LOCAL_DATE` date NOT NULL,
  `LOCAL_TIME` int(11) NOT NULL,
  `TRACE` int(11) NOT NULL,
  `REFNUM` varchar(12) COLLATE utf8_spanish_ci NOT NULL,
  `AUTHNUM` varchar(6) COLLATE utf8_spanish_ci NOT NULL,
  `TERMID` varchar(8) COLLATE utf8_spanish_ci NOT NULL,
  `ACCEPTORNAME` varchar(42) COLLATE utf8_spanish_ci NOT NULL,
  `STATUS` varchar(5) COLLATE utf8_spanish_ci NOT NULL,
  `IDMSG` varchar(16) COLLATE utf8_spanish_ci NOT NULL,
  `TIPOMSG` varchar(1) COLLATE utf8_spanish_ci NOT NULL,
  `FIID` varchar(4) COLLATE utf8_spanish_ci NOT NULL,
  `FECH_REG` date NOT NULL,
  `ID_BROKER` varchar(1) COLLATE utf8_spanish_ci NOT NULL,
  `TMSG_ENV` varchar(1) COLLATE utf8_spanish_ci NOT NULL,
  `NMSGS` int(25) NOT NULL,
  `CVE_TRX` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `NAT_CONT` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `PREFIJO` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `TIPO_TH` varchar(4) COLLATE utf8_spanish_ci NOT NULL,
  `USER_ID` varchar(10) COLLATE utf8_spanish_ci NOT NULL,
  `NOMBRE_USUARIO` varchar(45) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `PRA_THRESHOLD`
--

CREATE TABLE IF NOT EXISTS `PRA_THRESHOLD` (
  `TH_SE_ID_SERVICE` int(11) NOT NULL,
  `TH_THRESHOLD` float(8,2) NOT NULL,
  `TH_TIME_PROSA` int(11) NOT NULL,
  `TH_TIME_CLIENT` int(11) NOT NULL,
  `TH_TIMESTAMP` int(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `PRA_TOKEN`
--

CREATE TABLE IF NOT EXISTS `PRA_TOKEN` (
  `TK_USER` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `TK_TOKEN_PROSA` varchar(128) COLLATE utf8_spanish_ci NOT NULL,
  `TK_TOKEN_APPLE` varchar(128) COLLATE utf8_spanish_ci NOT NULL,
  `TK_TIMESTAMP` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `PRA_CLIENT`
--
ALTER TABLE `PRA_CLIENT`
 ADD PRIMARY KEY (`ID_CLIENT`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `PRA_CLIENT`
--
ALTER TABLE `PRA_CLIENT`
MODIFY `ID_CLIENT` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
