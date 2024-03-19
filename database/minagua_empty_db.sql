CREATE DATABASE minagua_db;

USE minagua_db;

CREATE TABLE `usuarios` (
  Id_usuario int(11) AUTO_INCREMENT PRIMARY KEY NOT NULL,
  Contrasena varchar(255) DEFAULT NULL,
  P_Nombre varchar(255) DEFAULT NULL,
  S_Nombre varchar(255) DEFAULT NULL,
  P_Apellido varchar(255) DEFAULT NULL,
  S_Apellido varchar(255) DEFAULT NULL,
  Cedula varchar(255) DEFAULT NULL,
  Correo varchar(255) DEFAULT NULL,
  Telefono varchar(255) DEFAULT NULL,
  Tipo enum('Admin','User') DEFAULT NULL,
  estatus enum('activo','inactivo') DEFAULT NULL
);

-- INSERT INTO `usuarios` (`Contrasena`, `P_Nombre`,`S_Nombre`, `P_Apellido`, `S_Apellido`,`Cedula`, `Correo`, `Telefono`, `Tipo`) VALUES
-- ( '1234', 'Admin','Admin', 'Admin','Admin', '00000000', 'admin@gmail.com','00000000000', 'Admin');
--
-- Estructura de tabla para la tabla `estados`
--

CREATE TABLE `estados` (
  id_estado INT(11) NOT NULL,
  estado VARCHAR(250) NOT NULL,
  iso VARCHAR(4) NOT NULL
);

--
-- Estructura de tabla para la tabla `municipios`
--

CREATE TABLE `municipios` (
  id_municipio INT(11) NOT NULL,
  id_estado INT(11) NOT NULL,
  municipio VARCHAR(100) NOT NULL
  -- FOREIGN KEY (id_estado) REFERENCES estados (id_estado)
);


--
-- Estructura de tabla para la tabla `parroquias`
--

CREATE TABLE `parroquias` (
  id_parroquia INT(11) NOT NULL,
  id_municipio INT(11) NOT NULL,
  parroquia VARCHAR(250) NOT NULL
  -- FOREIGN KEY (id_municipio) REFERENCES municipios (id_municipio)
);

--
-- Estructura de tabla para la tabla `embalses`
--

CREATE TABLE embalses (
  id_embalse INT AUTO_INCREMENT PRIMARY KEY,
  nombre_embalse VARCHAR(255) DEFAULT "",
  nombre_presa VARCHAR(255) DEFAULT "",
  id_estado VARCHAR(255) DEFAULT "",
  id_municipio VARCHAR(255) DEFAULT "",
  id_parroquia VARCHAR(255) DEFAULT "",
  este VARCHAR(100) DEFAULT "",
  norte VARCHAR(100) DEFAULT "",
  huso VARCHAR(100) DEFAULT "",
  cuenca_principal VARCHAR(255) DEFAULT "",
  afluentes_principales VARCHAR(255) DEFAULT "",
  area_cuenca VARCHAR(100) DEFAULT "",
  escurrimiento_medio VARCHAR(100) DEFAULT "",
  ubicacion_embalse TEXT,
  organo_rector VARCHAR(255) DEFAULT "",
  personal_encargado VARCHAR(255) DEFAULT "",
  operador VARCHAR(255) DEFAULT "",
  autoridad_responsable VARCHAR(255) DEFAULT "",
  proyectista VARCHAR(255) DEFAULT "",
  constructor VARCHAR(255) DEFAULT "",
  inicio_construccion VARCHAR(50) DEFAULT "", 
  duracion_de_construccion VARCHAR(50) DEFAULT "",
  inicio_de_operacion VARCHAR(50) DEFAULT "",
  monitoreo_del_embalse VARCHAR(255) DEFAULT "",
  batimetria MEDIUMTEXT DEFAULT "",
  vida_util VARCHAR(50) DEFAULT "",
  cota_min VARCHAR(50) DEFAULT "",
  cota_nor VARCHAR(50) DEFAULT "",
  cota_max VARCHAR(50) DEFAULT "",
  vol_min VARCHAR(50) DEFAULT "",
  vol_nor VARCHAR(50) DEFAULT "",
  vol_max VARCHAR(50) DEFAULT "",
  sup_min VARCHAR(50) DEFAULT "",
  sup_nor VARCHAR(50) DEFAULT "",
  sup_max VARCHAR(50) DEFAULT "",
  numero_de_presas VARCHAR(10) DEFAULT "",
  tipo_de_presa VARCHAR(255) DEFAULT "",
  altura VARCHAR(50) DEFAULT "",
  talud_aguas_arriba VARCHAR(50) DEFAULT "",
  talud_aguas_abajo VARCHAR(50) DEFAULT "",
  longitud_cresta VARCHAR(50) DEFAULT "",
  cota_cresta VARCHAR(50) DEFAULT "",
  ancho_cresta VARCHAR(50) DEFAULT "",
  volumen_terraplen VARCHAR(50) DEFAULT "",
  ancho_base VARCHAR(50) DEFAULT "",
  ubicacion_aliviadero VARCHAR(255) DEFAULT "",
  tipo_aliviadero VARCHAR(255) DEFAULT "",
  numero_compuertas_aliviadero VARCHAR(50) DEFAULT "",
  carga_vertedero VARCHAR(50) DEFAULT "",
  descarga_maxima VARCHAR(50) DEFAULT "",
  longitud_aliviadero VARCHAR(50) DEFAULT "",
  ubicacion_toma VARCHAR(255) DEFAULT "",
  tipo_toma VARCHAR(255) DEFAULT "",
  numero_compuertas_toma VARCHAR(50) DEFAULT "",
  mecanismos_de_emergencia VARCHAR(255) DEFAULT "",
  mecanismos_de_regulacion VARCHAR(255) DEFAULT "",
  gasto_maximo VARCHAR(50) DEFAULT "",
  descarga_de_fondo VARCHAR(50) DEFAULT "",
  posee_obra VARCHAR(255) DEFAULT "",
  tipo_de_obra VARCHAR(255) DEFAULT "",
  accion_requerida VARCHAR(255) DEFAULT "",
  proposito VARCHAR(255) DEFAULT "",
  uso_actual VARCHAR(255) DEFAULT "",
  sectores_estado VARCHAR(255) DEFAULT "",
  sectores_municipio VARCHAR(255) DEFAULT "",
  sectores_parroquia VARCHAR(255) DEFAULT "",
  poblacion_beneficiada VARCHAR(255) DEFAULT "",
  area_de_riego_beneficiada VARCHAR(50) DEFAULT "",
  area_protegida VARCHAR(50) DEFAULT "",
  poblacion_protegida VARCHAR(50) DEFAULT "",
  produccion_hidro VARCHAR(50) DEFAULT "",
  f_cargo VARCHAR(100) DEFAULT "",
  f_cedula VARCHAR(100) DEFAULT "",
  f_nombres VARCHAR(100) DEFAULT "",
  f_apellidos VARCHAR(100) DEFAULT "",
  f_telefono VARCHAR(100) DEFAULT "",
  f_correo VARCHAR(100) DEFAULT "",
  imagen_uno TEXT DEFAULT "",
  imagen_dos TEXT DEFAULT "",
  imagen_tres TEXT DEFAULT "",
  id_encargado VARCHAR(11) DEFAULT "",
  estatus enum('activo','inactivo') DEFAULT NULL
  -- FOREIGN KEY (id_estado) REFERENCES estados (id_estado),
  -- FOREIGN KEY (id_municipio) REFERENCES municipios (id_municipio),
  -- FOREIGN KEY (id_parroquia) REFERENCES parroquias (id_parroquia)
);

/*DROP TABLE IF EXISTS `codigo_extraccion`;
DROP TABLE IF EXISTS `tipo_codigo_extraccion`;*/
CREATE TABLE IF NOT EXISTS `tipo_codigo_extraccion`(
	id INT(11) AUTO_INCREMENT PRIMARY KEY NOT NULL,
	nombre VARCHAR(255) NULL,
	cantidad_primaria INT NOT NULL,
	unidad VARCHAR(100) NOT NULL,
	estatus ENUM('activo','inactivo') DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS `codigo_extraccion`(
	id INT(11) AUTO_INCREMENT PRIMARY KEY NOT NULL,
	codigo VARCHAR(20) NOT NULL UNIQUE,
	leyenda_sistema VARCHAR(255) NOT NULL,
	concepto VARCHAR(100) NOT NULL,
	uso VARCHAR(1000) NOT NULL,
	id_tipo_codigo_extraccion INT,
	estatus ENUM('activo','inactivo') DEFAULT NULL
);

CREATE TABLE datos_embalse (
  id_registro INT AUTO_INCREMENT PRIMARY KEY,
  id_embalse INT,
  fecha DATE,
  hora TIME,
  cota_actual FLOAT,
  id_encargado INT(11) NOT NULL,
  estatus VARCHAR(20) NOT NULL,
  FOREIGN KEY (id_embalse) REFERENCES embalses (id_embalse),
  FOREIGN KEY (id_encargado) REFERENCES usuarios (Id_usuario)
);

CREATE TABLE detalles_extraccion (
  id_detalles_extraccion INT AUTO_INCREMENT PRIMARY KEY,
  /*tipo_extraccion VARCHAR(50),*/
  id_codigo_extraccion INT(11) NOT NULL,
  extraccion FLOAT,
  id_registro INT,
  estatus VARCHAR(20) NOT NULL,
  FOREIGN KEY (id_registro) REFERENCES datos_embalse (id_registro),
  /*FOREIGN KEY (id_codigo_extraccion) REFERENCES codigo_extraccion (id)*/
);

CREATE TABLE configuraciones (
  id_config INT AUTO_INCREMENT PRIMARY KEY,
  nombre_config VARCHAR(50),
  configuracion TEXT
);

CREATE TABLE propositos (
  id_proposito INT AUTO_INCREMENT PRIMARY KEY,
  proposito VARCHAR(50),
  estatus varchar(20) NOT NULL
);

insert  into `usuarios`(`Id_usuario`,`Contrasena`,`P_Nombre`,`S_Nombre`,`P_Apellido`,`S_Apellido`,`Cedula`,`Correo`,`Telefono`,`Tipo`,`estatus`) values 
(1,'1234','Admin','Admin','Admin','Admin','00000000','admin@gmail.com','00000000000','Admin','activo'),
(2,'1234','Pedro','Antonio','Rodrigues','Vargas','12345678','pedro@gmail.com','04121234567','User','activo');



INSERT INTO `tipo_codigo_extraccion` (`id`, `nombre`, `cantidad_primaria`, `unidad`, `estatus`) 
VALUES ('1', 'Descarga por Aliviadero', '1000', 'm3', 'activo'),
	('2', 'Descarga por Connducto de Toma', '1000', 'm3', 'activo'),
	('3', 'Entrega Individualizada', '1000', 'm3', 'activo'),
	('4', 'Descarga No Controlada', '1000', 'm3', 'activo'),
	('5', 'Abertura de Valvula o Compuerta', '', '', 'activo'),
	('6', 'Climatología', '', '', 'activo'),
	('7', 'Caudal Afluente', '', '', 'activo');

INSERT INTO `codigo_extraccion` (`id`, `codigo`, `leyenda_sistema`, `concepto`, `uso`, `id_tipo_codigo_extraccion`, `estatus`) 
VALUES ('1', '00', '', 'Día', 'Día del cual se transmite la información', '', 'activo'),
	('2', '01', '', 'Cota', 'Cota o nivel del embalse en metros sobre el nivel de mar, leída a las 8:00 am para el día indicado en la columna 00', '', 'activo'),
	('3', '02', 'Volumen', 'Volumen', 'Volumen en hectómetros cúbicos almacenado en el embalse correspondiente a la cota indicada en la columna 01. Se obtiene de la Tabla Área-Capacidad del embalse', '', 'activo'),
	('4', '03', 'Area', 'Área', 'Area o superficie del vaso del embalse en hectáreas que corresponde a la cota indicada en la columna 01. Se obtiene de la Tabla Area- Capacidad del embalse.', '', 'activo'),
	('5', '04', 'ARiego', 'Riego', 'Descargas controladas por el aliviadero específicamente para el riego.', '1', 'activo'),
	('6', '05', 'ARio', 'Río', 'Descargas controladas por el aliviadero para el uso de ribereños.', '1', 'activo'),
	('7', '06', 'AControl', 'Control de Inundaciones', 'Descargas controladas efectuadas por el aliviadero con el propósito de controlar crecientes. ', '1', 'activo'),
	('8', '07', 'AOtros', 'Otros', 'Se refiere a aquellas descargas por el aliviadero para otros usos no incluidos en lo códigos 04,05 y 06, como por ejemplo: fugas a través de las compuertas de aliviadero, descargas al cauce con fines recreacionales, etc.', '1', 'activo'),
	('9', '08', '', 'Subtotal', 'Suma de los códigos 04, 05, 06, y 07', '1', 'activo'),
	('10', '09', 'TRiego', 'Riego', 'Descargas controladas por conducto de toma con fines de riego. Incluye sistemas de riego y ribereños', '2', 'activo'),
	('11', '10', 'TRio', 'Río', 'Descarga única y exclusivamente para gasto ecológico efectuadas por el conducto de toma.', '2', 'activo'),
	('12', '11', 'TControl', 'Control de Inundaciones', 'Descargas controladas por conducto de toma para controlar crecientes.', '2', 'activo'),
	('13', '12', 'TOtros', 'Otros', 'Incluye cualquier descarga diferente a la de los códigos 09,10 y 11, ejemplo: descargas de sedimentos, fugas por válvulas, compuertas u otros mecanismos, descargas al cauce para consumo humano, descarga al cauce con fines recreacionales, descargas para evitar daños en las obras inconclusas o que presenten alguna condición especial que no permita que el embalse alcance ciertos niveles o alivie.', '2', 'activo'),
	('14', '13', '', 'Subtotal', 'Suma de los códigos 09, 10, 11 y 12', '2', 'activo'),
	('15', '14', 'EAcueducto', 'Consumo Humano', 'Entregas efectuadas por tomas individualizadas única y exclusivamente para abastecimiento de poblaciones.', '3', 'activo'),
	('16', '15', 'ERiego', 'Riego', 'Entregas efectuadas por tomas individualizadas específicamente para sistemas de riego.', '3', 'activo'),
	('17', '16', 'EIndustria', 'Industria', 'Entregas por tomas individualizadas a diferentes industrias: CADAFE, Centros Agroindustriales, etc.', '3', 'activo'),
	('18', '17', 'EOtros', 'Otros', 'Incluye cualquier otra entrega que cumpla con algún objetivo diferente al de los códigos 14,15, y 16; ejemplo: toma dentro del embalse para actividad "No tipificada o regularizada".', '3', 'activo'),
	('19', '18', '', 'Subtotal', 'Suma de los códigos 14, 15, 16, y 17.', '3', 'activo'),
	('20', '19', 'Aliviadero', 'Aliviadero', 'Descarga libre por el aliviadero de los volúmenes excedentes en el embalse. Esta descarga se efectúa a partir del nivel normal. Cuando el embalse está aliviando, los códigos 09 y 10 no deben transmitirse porque en la mayoría de los casos el caudal aliviado es suficiente para satisfacer estos dos códigos.', '4', 'activo'),
	('21', '20', 'Conductos', 'Conductos', 'Descargas no controladas generalmente a través de sifones.', '4', 'activo'),
	('22', '21', 'Otros', 'Otros', 'Otras salidas diferentes a las de los códigos 19 y 20', '4', 'activo'),
	('23', '22', '', 'Subtotal', 'Suma de los códigos 19, 20 y 21.', '4', 'activo'),
	('24', '23', '', 'Total Descargas', 'Suma de los subtotales correspondientes a los códigos 08, 13, 18, y 22.', '', 'activo'),
	('25', '24', '', 'Lluvia', 'Lluvia en mm caída sobre el espejo de agua del vaso y medida en la estación climatológica.', '6', 'activo'),
	('26', '25', '', 'Evaporación', 'Evaporación en mm medida en la estación climatológica.', '6', 'activo'),
	('27', '26', '', 'Q m3/seg', 'Aporte tomado en el afluente del embalse en m3/seg medido en la estación fluviométrica (limnígrafo o mira).', '7', 'activo'),
	('28', '27', '', 'Volumen Total', 'Volumen correspondiente al caudal indicado en el código 26.', '7', 'activo'),
	('29', '28', '', 'Observaciones', 'Observaciones varias relacionadas con abertura de válvulas, hectáreas regadas y/o cualquier novedad.', '7', 'activo'),
	('30', '29', '', 'Abertura de Válvula o Compuerta (cm)', 'En este código se indica la abertura en cms de la(s) válvula(s) o compuerta(s) que este en ese momento en operación', '5', 'activo'),
	('31', '30', '', 'Caudal (m3/s)', 'Se refiere al caudal que corresponde a la abertura indicada en el código 29.', '5', 'activo'),
	('32', '31', '', 'Hectáreas Regadas', 'Se refiere al área bajo riego expresada en hectáreas cubierta por el volumen acusado en los códigos 04, 09 y 15 definidos anteriormente, es importante el conocimiento de esta información la cual puede provenir de los funcionamientos de los sistemas de riego del MPC o cualquier otro organismo o en su defecto en forma estimativa por el funcionario encargado de la operación del embalse.', '', 'activo');


SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));