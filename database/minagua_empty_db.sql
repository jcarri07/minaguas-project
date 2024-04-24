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
  Tipo enum('SuperAdmin','Admin', 'User') DEFAULT NULL,
  estatus enum('activo', 'inactivo') DEFAULT NULL
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
  municipio VARCHAR(100) NOT NULL -- FOREIGN KEY (id_estado) REFERENCES estados (id_estado)
);

--
-- Estructura de tabla para la tabla `parroquias`
--
CREATE TABLE `parroquias` (
  id_parroquia INT(11) NOT NULL,
  id_municipio INT(11) NOT NULL,
  parroquia VARCHAR(250) NOT NULL -- FOREIGN KEY (id_municipio) REFERENCES municipios (id_municipio)
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
  region VARCHAR(255) DEFAULT "",
  id_encargado VARCHAR(11) DEFAULT "",
  estatus enum('activo', 'inactivo') DEFAULT NULL -- FOREIGN KEY (id_estado) REFERENCES estados (id_estado),
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
  estatus ENUM('activo', 'inactivo') DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS `codigo_extraccion`(
  id INT(11) AUTO_INCREMENT PRIMARY KEY NOT NULL,
  codigo VARCHAR(20) NOT NULL UNIQUE,
  leyenda_sistema VARCHAR(255) NOT NULL,
  concepto VARCHAR(100) NOT NULL,
  uso VARCHAR(1000) NOT NULL,
  id_tipo_codigo_extraccion INT,
  estatus ENUM('activo', 'inactivo') DEFAULT NULL
);

CREATE TABLE datos_embalse (
  id_registro INT AUTO_INCREMENT PRIMARY KEY,
  id_embalse INT,
  fecha DATE,
  hora TIME,
  cota_actual FLOAT,
  id_encargado INT(11) NOT NULL,
  archivo_importacion VARCHAR(1000) NOT NULL,
  fecha_importacion DATE,
  estatus VARCHAR(20) NOT NULL,
  FOREIGN KEY (id_embalse) REFERENCES embalses (id_embalse),
  FOREIGN KEY (id_encargado) REFERENCES usuarios (Id_usuario)
);

CREATE TABLE detalles_extraccion (
  id_detalles_extraccion INT AUTO_INCREMENT PRIMARY KEY,
  /*tipo_extraccion VARCHAR(50),*/
  id_codigo_extraccion INT(11) NOT NULL,
  extraccion VARCHAR (255),
  id_registro INT,
  estatus VARCHAR(20) NOT NULL,
  FOREIGN KEY (id_registro) REFERENCES datos_embalse (id_registro)
  /*FOREIGN KEY (id_codigo_extraccion) REFERENCES codigo_extraccion (id)*/
);

CREATE TABLE `configuraciones` (
  `id_config` int(11) AUTO_INCREMENT PRIMARY KEY,
  `nombre_config` varchar(50) DEFAULT NULL,
  `configuracion` text
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

CREATE TABLE propositos (
  id_proposito INT AUTO_INCREMENT PRIMARY KEY,
  proposito VARCHAR(50),
  estatus varchar(20) NOT NULL
);

CREATE TABLE operadores (
  id_operador INT AUTO_INCREMENT PRIMARY KEY,
  operador VARCHAR(50),
  estatus varchar(20) NOT NULL
);

CREATE TABLE regiones (
  id_region INT AUTO_INCREMENT PRIMARY KEY,
  region VARCHAR(50),
  estatus varchar(20) NOT NULL
);

insert into
  `usuarios`(
    `Id_usuario`,
    `Contrasena`,
    `P_Nombre`,
    `S_Nombre`,
    `P_Apellido`,
    `S_Apellido`,
    `Cedula`,
    `Correo`,
    `Telefono`,
    `Tipo`,
    `estatus`
  )
values
  (
    1,
    '$2y$05$drIpNODWj6463HT6/68CMeojRcps3aFrd3ejsroKklrSorYD4INkG',
    'Admin',
    'Admin',
    'Admin',
    'Admin',
    '00000000',
    'admin@gmail.com',
    '00000000000',
    'Admin',
    'activo'
  ),
  (
    3,
    '$2y$05$lOy35Mzm/nB40AwP7GwYL.PPoHNyVBdNYK.QWdRTBTp.Cbmk.Ei9m',
    'Pedro',
    'Antonio',
    'Rodrigues',
    'Vargas',
    '12345678',
    'pedro@gmail.com',
    '04121234567',
    'User',
    'activo'
  ),
  (
    1,
    '$2y$05$lOy35Mzm/nB40AwP7GwYL.PPoHNyVBdNYK.QWdRTBTp.Cbmk.Ei9m',
    'Admin',
    'Admin',
    'Admin',
    'Admin',
    '99999999',
    'superadmin@gmail.com',
    '04121234567',
    'SuperAdmin',
    'activo'
  );

SET
  GLOBAL sql_mode =(
    SELECT
      REPLACE(@ @sql_mode, 'ONLY_FULL_GROUP_BY', '')
  );