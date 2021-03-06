<?php
require_once ("core/auth/SesionSql.class.php");

class SesionBase {
    
    /**
     * Atributos de la sesión
     */
    var $sesionId;
    
    var $sesionExpiracion;
    
    var $sesionUsuarioId;
    
    var $registro_sesion;
    
    var $miSql;
    
    var $prefijoTablas;
    
    var $tiempoExpiracion;
    
    /**
     * @METHOD setSesionId
     *
     * Asigna el valor del atributo sesionId
     *
     * @return valor
     * @access public
     */
    function setSesionId($sesionId) {
        
        $this->sesionId = $sesionId;
    
    }
    // end of member function especificar_sesion
    
    /**
     * @METHOD setSesionExpiracion
     *
     * @return valor
     * @access public
     */
    function setSesionExpiracion($expiracion) {
        
        $this->sesionExpiracion = $expiracion;
    
    }
    // Fin del mètodo especificar_expiracion
    function setConexion($conexion) {
        
        $this->miConexion = $conexion;
    
    }
    
    /**
     * @METHOD setSesionNivel
     *
     * @param
     *            nivel
     * @access public
     */
    function setSesionNivel($nivel) {
        
        $this->sesionNivel = $nivel;
    
    }
    // Fin de la función especificar_enlace
    
    /**
     * @METHOD setIdusuario
     *
     * @return valor
     * @access public
     */
    function setIdUsuario($idUsuario) {
        
        $this->setSesionUsuarioId = $idUsuario;
    
    }
    // Fin del mètodo especificar_usuario
    function setSesionUsuario($valor) {
        
        $this->sesionUsuario = $valor;
    
    }
    
    function setTiempoExpiracion($valor) {
        
        $this->tiempoExpiracion = $valor;
    
    }
    
    function setPrefijoTablas($valor) {
        
        $this->prefijoTablas = $valor;
        $this->miSql->setPrefijoTablas ( $this->prefijoTablas );
    
    }
    
    /**
     * @METHOD rescatar_valor_sesion
     * @PARAM variable
     * @PARAM usuario_aplicativo ??
     *
     * @return boolean
     * @access public
     */
    function getValorSesion($variable) {
        
        if (isset ( $_COOKIE ["aplicativo"] )) {
            $this->sesionId = $_COOKIE ["aplicativo"];
        } else {
            return FALSE;
        }
        
        $parametro ["sesionId"] = $this->sesionId;
        $parametro ["variable"] = $variable;
        
        $clausulaSQL = $this->miSql->getCadenaSql ( "buscarValorSesion", $parametro );
        $resultado = $this->miConexion->ejecutarAcceso ( $clausulaSQL, "busqueda" );
        
        if ($resultado) {
            $this->sesionExpiracion = $resultado [0] ["expiracion"];
            return $resultado [0] ["valor"];
        }
        return false;
    
    }
    // Fin del método
    function getSesionId() {
        
        return $this->sesionId;
    
    }
    
    function getSesionUsuarioId() {
        
        return $this->sesionUsuarioId;
    
    }
    
    function getSesionNivel() {
        
        return $this->sesionNivel;
    
    }
    
    function getSesionExpiracion() {
        
        return $this->sesionExpiracion;
    
    }

}

?>
