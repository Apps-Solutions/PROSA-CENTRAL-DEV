-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 12-05-2015 a las 10:10:19
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
) ENGINE=InnoDB AUTO_INCREMENT=213 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `PRA_ALERT`
--

INSERT INTO `PRA_ALERT` (`ID_ALERT`, `AL_CL_ID_CLIENT`, `AL_TIMESTAMP`, `AL_SE_ID_SERVICE`, `AL_TEXT`, `AL_STATUS`) VALUES
(180, 1, 1413821194, 7, 'Test notificacion', 1),
(181, 1, 1413821196, 7, 'Test notificacion', 1),
(182, 1, 1413821199, 7, 'Test notificacion', 1),
(185, 0, 1413821197, 7, 'kgdlfkgjdfljl', 1),
(207, 1, 1413823766, 7, 'Prueba: Servicio ATM fuera - Claudio', 1),
(208, 1, 1413823769, 7, 'Prueba: Servicio ATM fuera - Claudio', 1),
(209, 1, 1413823769, 7, 'Prueba: Servicio', 1),
(210, 1, 1413823769, 7, 'Prueba: Servicio', 1),
(211, 1, 1413823769, 7, 'Prueba: Servicio', 1),
(212, 1, 1413823769, 7, 'Prueba: Servicio', 1);

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `PRA_CLIENT`
--

INSERT INTO `PRA_CLIENT` (`ID_CLIENT`, `CL_CLIENT`, `CL_CODE`, `CL_STATUS`, `CL_TIMESTAMP`) VALUES
(1, 'SANTANDER', 'B003', 1, 1431368241),
(2, 'HSBC', 'B021', 1, 1431368241),
(3, 'SCOTIABANK', 'B044', 1, 1431368241),
(4, 'BANORTE', 'B072', 1, 1413743865),
(5, 'PROSA', 'B999', 1, 1413743865);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `PRA_LAST_TOTAL`
--

CREATE TABLE IF NOT EXISTS `PRA_LAST_TOTAL` (
  `LT_SE_ID_SERVICE` int(11) NOT NULL,
  `LT_TOTAL` int(11) NOT NULL,
  `LT_TIMESTAMP` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `PRA_LAST_TOTAL`
--

INSERT INTO `PRA_LAST_TOTAL` (`LT_SE_ID_SERVICE`, `LT_TOTAL`, `LT_TIMESTAMP`) VALUES
(0, 341898, 1431363586),
(1, 10458, 1431100304),
(2, 765119, 1431100445),
(3, 38388, 1431100311),
(4, 1270, 1431100567),
(5, 560, 1431097518),
(7, 2677252, 1431383701),
(8, 192608, 1431096527),
(9, 2270, 1431100291);

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

--
-- Volcado de datos para la tabla `PRA_MAINTENANCE`
--

INSERT INTO `PRA_MAINTENANCE` (`ID_MAINTENANCE`, `MA_SE_ID_SERVICE`, `MA_START`, `MA_END`, `MA_TIMESTAMP`, `MA_STATUS`) VALUES
(1, 10, 1413726840, 1413727200, 1413726845, 1),
(2, 10, 1413727500, 1413727800, 1413726916, 1),
(3, 8, 1414801020, 1414808220, 1414627653, 1),
(4, 7, 1425653460, 1425658260, 1425616004, 1),
(5, 7, 1430860740, 1430864340, 1430860023, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `PRA_NETWORK`
--

CREATE TABLE IF NOT EXISTS `PRA_NETWORK` (
  `ID_NETWORK` varchar(5) COLLATE utf8_spanish_ci NOT NULL,
  `NETWORK` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `EMITTER` varchar(5) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `PRA_NETWORK`
--

INSERT INTO `PRA_NETWORK` (`ID_NETWORK`, `NETWORK`, `EMITTER`) VALUES
('ARTL', 'ARTELI', 'ADQ'),
('BENA', 'FARMACIAS BENAVIDES', 'ADQ'),
('BIC2', 'WALMART (TIENDA)', 'ADQ'),
('BNET', 'POS MASTERCARD', 'AMBAS'),
('CICR', 'CIMACO', 'AMBAS'),
('PALH', 'PALACIO DEL HIERRO', 'ADQ'),
('PAYW', 'PAYWORKS', 'ADQ'),
('PCHR', '-', 'ADQ'),
('PCLV', 'PUNTO CLAVE', 'ADQ'),
('PEGA', 'PEGASO', 'ADQ'),
('PLNT', 'PLANET PAYMENT', 'ADQ'),
('PRO1', 'PROSA FRONT END', 'AMBAS'),
('PROC', 'AMBAS', 'ADQ'),
('PROE', 'PROCOM', 'AMBAS'),
('PROI', 'MULTISERV', 'ADQ'),
('PROV', 'AUTORIZACIONES VOZ', 'ADQ'),
('PRSA', 'SWITCH ABIERTO: General', 'ADQ'),
('PRSH', 'PRICE SHOWS', 'ADQ'),
('RESC', 'RESORTCOM', 'ADQ'),
('RSHK', 'RADIO SHACK', 'ADQ'),
('SAHS', 'SWITCH ABIERTO: Hoteles Santander', 'ADQ'),
('SMBT', 'SMART BT', 'ADQ'),
('SMFL', '-', 'ADQ'),
('SNBR', 'SANBORNS', 'ADQ'),
('SORI', 'SORIANA', 'ADQ'),
('STUS', '-', 'ADQ'),
('SWDL', 'SWITCH ABIERTO A TRAVES DE DELL', 'ADQ'),
('TCP1', 'VPNS POS', 'ADQ'),
('TELC', 'TELCEL', 'ADQ'),
('UNEF', 'UNEFON', 'ADQ'),
('VISA', 'VISA', 'AMBOS');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `PRA_RESPONSE_CODE`
--

CREATE TABLE IF NOT EXISTS `PRA_RESPONSE_CODE` (
  `ID_RESPONSE_CODE` int(11) NOT NULL,
  `RC_RESPONSE_CODE` varchar(45) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `PRA_RESPONSE_CODE`
--

INSERT INTO `PRA_RESPONSE_CODE` (`ID_RESPONSE_CODE`, `RC_RESPONSE_CODE`) VALUES
(50, 'Declinada'),
(51, 'Tarjeta Expirada'),
(52, 'Intentos de PIN excedidos'),
(53, 'Uso compartido no soportado'),
(54, 'Modulo de seguridad incorrecto'),
(55, 'Transaccion Invalida'),
(56, 'Transaccion no soportada'),
(57, 'Tarjeta Reportada'),
(58, 'Tarjeta Invalida'),
(59, 'Tarjeta Restringida'),
(60, 'Cuenta sin CAF'),
(61, 'Cuenta sin PBF'),
(62, 'PBF erroneo'),
(64, 'Track Data Erroneo'),
(67, 'Fecha de Transacción Invalida'),
(68, 'Error en PTLF'),
(69, 'Revisión de Mensaje Incorrecta'),
(70, 'No IDF'),
(73, 'Ruteo Incorrecto'),
(74, 'Inhabilitado para Autoriza'),
(76, 'Fondos Insuficientes'),
(77, 'Limite de autorización rechazado'),
(78, 'Transacción duplicada'),
(81, 'Monto de devolución mayor al credito'),
(82, 'Intentos de devolución excedidos'),
(84, 'Cliente sin NEG'),
(85, 'Consulta no Permitida'),
(86, 'Limite de piso Excedido'),
(87, 'Intentos de devolución excedidos'),
(88, 'Transacción Referida'),
(89, 'Tarjeta Inactiva'),
(92, 'Monto de disposición menor'),
(94, 'Limite excedido'),
(95, 'Monto maximo excedido'),
(97, 'MOD 10 CHECK'),
(99, 'PBF erroneo'),
(100, 'Inhabilitado para procesar'),
(101, 'Llame para autorizar'),
(105, 'Tarjeta no soportada'),
(107, 'Limite de devoliciones excedido'),
(113, 'TIMEOUT'),
(150, 'No existe el Comercio'),
(200, 'Cuenta Invalida'),
(201, 'PIN incorrecto'),
(204, 'Ingrese un monto menor'),
(205, 'Monto de disposiciÃ³n invalido'),
(206, 'CAF no presente'),
(208, 'Fecha de expiraciÃ³n Invalida'),
(251, 'CASH BACK excede limite diario'),
(300, 'Comercio Fraudulento'),
(302, 'Promocion Inexistente'),
(303, 'Promocion Invalida'),
(400, 'ARQC Invalido'),
(406, 'NULL'),
(901, 'Tarjeta Expirada'),
(902, 'NEG CAPTURE CARD'),
(903, 'CAF STATUS 3'),
(906, 'Captura Tarjeta Pago'),
(908, 'NULL'),
(909, 'Capturada');

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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `PRA_SERVICE`
--

INSERT INTO `PRA_SERVICE` (`ID_SERVICE`, `SE_SERVICE`, `SE_COMMAND`, `SE_STATUS`, `SE_TIMESTAMP`, `SE_ORDER`) VALUES
(1, 'Pagos Diferidos', 's1', 1, 1431376470, 5),
(2, 'Preautorizador (PREA)', 's2', 1, 1431376470, 7),
(3, 'Payware Online', 's3', 1, 1431376470, 6),
(4, 'Switch Abierto', 's4', 1, 1431376470, 10),
(5, 'PROCOM', 's5', 1, 1431376470, 8),
(6, 'Cargos Automaticos', 's6', 1, 1431376470, 3),
(7, 'POS', 's7', 1, 1431376470, 1),
(8, 'ATM', 's8', 1, 1431376470, 2),
(9, 'Multiserv', 's9', 1, 1431376470, 4),
(10, 'SMS', 's10', 0, 1431376470, 9);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `PRA_SERVICE_CLIENT`
--

CREATE TABLE IF NOT EXISTS `PRA_SERVICE_CLIENT` (
  `SC_SE_ID_SERVICE` int(11) NOT NULL,
  `SC_CL_ID_CLIENT` int(11) NOT NULL,
  `SC_TIMESTAMP` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `PRA_SERVICE_CLIENT`
--

INSERT INTO `PRA_SERVICE_CLIENT` (`SC_SE_ID_SERVICE`, `SC_CL_ID_CLIENT`, `SC_TIMESTAMP`) VALUES
(7, 4, 1415233651),
(9, 4, 1415233652),
(8, 7, 1429196877),
(5, 4, 1415233654),
(7, 2, 1418689328),
(9, 2, 1418689331),
(6, 2, 1418689333),
(7, 7, 1427329905),
(6, 7, 1430848035),
(7, 8, 1430853116);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `PRA_SERVICE_USER`
--

CREATE TABLE IF NOT EXISTS `PRA_SERVICE_USER` (
  `SU_SE_ID_SERVICE` int(11) NOT NULL,
  `SU_USER` varchar(45) COLLATE utf8_spanish_ci NOT NULL,
  `SU_TIMESTAMP` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `PRA_SERVICE_USER`
--

INSERT INTO `PRA_SERVICE_USER` (`SU_SE_ID_SERVICE`, `SU_USER`, `SU_TIMESTAMP`) VALUES
(7, 'mxbdcal1', 1420670024),
(6, 'mxbdcal1', 1420670025),
(8, 'mxbdcal1', 1420670171),
(9, 'mxbdcal1', 1420670172),
(3, 'mxbdcal1', 1420670174),
(2, 'mxbdcal1', 1420670175),
(5, 'mxbdcal1', 1420670177),
(10, 'mxbdcal1', 1420670178),
(4, 'mxbdcal1', 1420670180),
(1, 'mxbdcal1', 1413827612);

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

--
-- Volcado de datos para la tabla `PRA_TBL_APP_PREAUTORIZADOR`
--

INSERT INTO `PRA_TBL_APP_PREAUTORIZADOR` (`ID`, `FIID_TARJ`, `PREFIJO`, `PREA`) VALUES
(1, 'B003', 272800, '2'),
(2, 'B003', 454747, '2'),
(3, 'B003', 456300, '2'),
(4, 'B003', 491327, '2'),
(5, 'B003', 491405, '2'),
(6, 'B003', 491418, '2'),
(7, 'B003', 491512, '2'),
(8, 'B003', 491572, '2'),
(9, 'B003', 491573, '2'),
(10, 'B003', 493135, '2');

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

--
-- Volcado de datos para la tabla `PRA_THRESHOLD`
--

INSERT INTO `PRA_THRESHOLD` (`TH_SE_ID_SERVICE`, `TH_THRESHOLD`, `TH_TIME_PROSA`, `TH_TIME_CLIENT`, `TH_TIMESTAMP`) VALUES
(1, 10.00, 2, 0, 1430860250),
(2, 10.00, 2, 0, 1430860250),
(3, 14.00, 1, 0, 1430860250),
(4, 10.00, 2, 0, 1430860250),
(5, 0.00, 0, 0, 1430860250),
(6, 50.00, 0, 0, 1430860250),
(7, 20.00, 2, 0, 1430860250),
(8, 20.00, 1, 0, 1430860250),
(9, 15.00, 1, 0, 1430860250),
(10, 0.00, 1, 0, 1430860250);

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
-- Volcado de datos para la tabla `PRA_TOKEN`
--

INSERT INTO `PRA_TOKEN` (`TK_USER`, `TK_TOKEN_PROSA`, `TK_TOKEN_APPLE`, `TK_TIMESTAMP`) VALUES
('mxbdcal1', 'f18dcbcd8bcddc3c591c553ba6bb6a53', '7ae841bee0a9eb356bd318fdf93f8b08b247a2b5c9f88dc7b9e420a01445d10c', 1422398129),
('sug', '620e7794b8453c600d468cb2b3c441c5', '92c5346b951f43221ada2a6a63dcd7b9ab55668262be96cf2afbbcd4fb87285a', 0),
('dguztysrtsgyf', '9cf2a0657b6141d93e3b3d939c294e1e', 'f4a62ec42ea4fe72e3c5938fee6fd59f9b469dfa31a7f4db11ef8d627adab2bf', 1424727362),
('dev_admin', '07f4718a0767f89f1729aac9dd00f634', '68979265dd1345341464585196725bc4c56cb2daf51b8e6f81b7cfc6ec914dda', 1416425713),
('aontiver', 'd1ae1ae0faf20559f05a05015c8cada0', '9f3775e2433a048dbb1f2555b04327604a89e779d9528dadbe089f87a1b746d4', 1431608828),
('abansb', '3bed60fd27d44b9d2ade8dd624fd8cb6', '1033c9b4c83de3fc6d7e1bec3a452944838b4ac4a84bd07f10ca1bb1cd073f14', 1417048799),
('mono', 'a05761c84237270085df15806bed7023', '', 0),
('nfaktalgaly', '7c2a95374c1d2de0e00b83f04af3bc9a', '834baddfa637de54923d01e0fed2658efd87d4c47850984de4fd86db0a448bb6', 1428362208),
('hbguevar', '4c01c3edc93cbcb98e7cad0aa9e42025', 'ebd345021f5ef663b58d7cd05ee6c92dd2646a02042df5eded4167e4eccf0ccd', 0),
('cavila', 'e0b92984d4db0ca9d9ed1865b5a6c24e', '6606a2aae5699e71615fe2688eb56c45e2005c904cd8d3e918593d0e941508c6', 1432074226);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `PRA_ALERT`
--
ALTER TABLE `PRA_ALERT`
 ADD PRIMARY KEY (`ID_ALERT`);

--
-- Indices de la tabla `PRA_CLIENT`
--
ALTER TABLE `PRA_CLIENT`
 ADD PRIMARY KEY (`ID_CLIENT`);

--
-- Indices de la tabla `PRA_LAST_TOTAL`
--
ALTER TABLE `PRA_LAST_TOTAL`
 ADD PRIMARY KEY (`LT_SE_ID_SERVICE`);

--
-- Indices de la tabla `PRA_MAINTENANCE`
--
ALTER TABLE `PRA_MAINTENANCE`
 ADD PRIMARY KEY (`ID_MAINTENANCE`);

--
-- Indices de la tabla `PRA_NETWORK`
--
ALTER TABLE `PRA_NETWORK`
 ADD PRIMARY KEY (`ID_NETWORK`);

--
-- Indices de la tabla `PRA_RESPONSE_CODE`
--
ALTER TABLE `PRA_RESPONSE_CODE`
 ADD PRIMARY KEY (`ID_RESPONSE_CODE`);

--
-- Indices de la tabla `PRA_SERVICE`
--
ALTER TABLE `PRA_SERVICE`
 ADD PRIMARY KEY (`ID_SERVICE`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `PRA_ALERT`
--
ALTER TABLE `PRA_ALERT`
MODIFY `ID_ALERT` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=213;
--
-- AUTO_INCREMENT de la tabla `PRA_CLIENT`
--
ALTER TABLE `PRA_CLIENT`
MODIFY `ID_CLIENT` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT de la tabla `PRA_SERVICE`
--
ALTER TABLE `PRA_SERVICE`
MODIFY `ID_SERVICE` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
