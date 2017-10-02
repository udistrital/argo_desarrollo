<?php

namespace gestionPolizas\gestionPolizas\funcion;

use gestionPolizas\gestionPolizas\funcion\redireccion;

include_once ('redireccionar.php');
if (!isset($GLOBALS ["autorizado"])) {
    include ("../index.php");
    exit();
}

class RegistradorOrden {

    var $miConfigurador;
    var $lenguaje;
    var $miFormulario;
    var $miFuncion;
    var $miSql;
    var $conexion;

    function __construct($lenguaje, $sql, $funcion) {
        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->lenguaje = $lenguaje;
        $this->miSql = $sql;
        $this->miFuncion = $funcion;
    }

    function procesarFormulario() {

        $conexion = "contractual";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $cadenaSql = $this->miSql->getCadenaSql('obtenerEstadoPoliza', $_REQUEST ['id_poliza']);
        $estadoActual = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        if ($estadoActual[0]['estado'] == "t") {
            $nuevoEstado = 'f';
        } else {
            $nuevoEstado = 't';
        }

        $cadenaSql = $this->miSql->getCadenaSql('cambiarEstadoPoliza', array(0 => $nuevoEstado, 1 => $_REQUEST ['id_poliza']));
        $resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso",array(0 => $nuevoEstado, 1 => $_REQUEST ['id_poliza']),"cambiarEstadoPoliza");
        
        $datos = array(
            0 => $nuevoEstado,
            1 => $_REQUEST ['id_poliza'],
            'numero_contrato' => $_REQUEST['numero_contrato'],
            'vigencia' => $_REQUEST['vigencia'],
            'numero_contrato_suscrito' => $_REQUEST['numero_contrato_suscrito'],
            'numero_poliza' => $_REQUEST['numero_poliza'],
            'mensaje_titulo' => $_REQUEST['mensaje_titulo'],
        );
        if ($resultado != false) {
            redireccion::redireccionar('cambioEstado', $datos);
            exit();
        } else {

            redireccion::redireccionar('noCambioEstado', $datos);
            exit();
        }
    }

    function resetForm() {
        foreach ($_REQUEST as $clave => $valor) {

            if ($clave != 'pagina' && $clave != 'development' && $clave != 'jquery' && $clave != 'tiempo') {
                unset($_REQUEST [$clave]);
            }
        }
    }

}

$miRegistrador = new RegistradorOrden($this->lenguaje, $this->sql, $this->funcion);

$resultado = $miRegistrador->procesarFormulario();
?>