<?php
if (!isset($_GET['page']) || $_GET['page'] == "") {
    include "pages/dashboard-principal.php";
} else {
    if ($_GET['page'] == "usuarios") {
        include "pages/usuarios.php";
    }
    if ($_GET['page'] == "embalses") {
        include "pages/embalses.php";
    }
    if ($_GET['page'] == "crear_embalse") {
        include "pages/crear_embalse.php";
    }
    if ($_GET['page'] == "editar_embalse") {
        include "pages/editar_embalse.php";
    }
    if ($_GET['page'] == "datos") {
        include "pages/datos.php";
    }
    if ($_GET['page'] == "reportes") {
        include "pages/reportes.php";
    }
    if ($_GET['page'] == "perfil") {
        include "pages/perfil.php";
    }
    if ($_GET['page'] == "configuraciones") {
        include "pages/configuraciones.php";
    }
    if ($_GET['page'] == "cerrar_sesion") {
        include "php/login/logout.php";
    }
    if ($_GET['page'] == "grafica_embalse") {
        include "php/Graficas/grafica_embalse.php";
    }
    if ($_GET['page'] == "show") {
        include "pages/show.php";
    }
}
