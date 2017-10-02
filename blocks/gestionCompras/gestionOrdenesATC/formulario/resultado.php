<?php

if (!isset($GLOBALS ["autorizado"])) {
    include ("../index.php");
    exit();
}

class registrarForm {

    var $miConfigurador;
    var $lenguaje;
    var $miFormulario;
    var $miSql;

    function __construct($lenguaje, $formulario, $sql) {
        $this->miConfigurador = \Configurador::singleton();

        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');

        $this->lenguaje = $lenguaje;

        $this->miFormulario = $formulario;

        $this->miSql = $sql;
    }

    function miForm() {


        // Rescatar los datos de este bloque
        $esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");
        $miPaginaActual = $this->miConfigurador->getVariableConfiguracion('pagina');

        $directorio = $this->miConfigurador->getVariableConfiguracion("host");
        $directorio .= $this->miConfigurador->getVariableConfiguracion("site") . "/index.php?";
        $directorio .= $this->miConfigurador->getVariableConfiguracion("enlace");

        $rutaBloque = $this->miConfigurador->getVariableConfiguracion("host");
        $rutaBloque .= $this->miConfigurador->getVariableConfiguracion("site") . "/blocks/";
        $rutaBloque .= $esteBloque ['grupo'] . "/" . $esteBloque ['nombre'];

        // ---------------- SECCION: Parámetros Globales del Formulario ----------------------------------
        /**
         * Atributos que deben ser aplicados a todos los controles de este formulario.
         * Se utiliza un arreglo
         * independiente debido a que los atributos individuales se reinician cada vez que se declara un campo.
         *
         * Si se utiliza esta técnica es necesario realizar un mezcla entre este arreglo y el específico en cada control:
         * $atributos= array_merge($atributos,$atributosGlobales);
         */
        $atributosGlobales ['campoSeguro'] = 'true';


        $conexion = "contractual";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
        $conexionFrameWork = "estructura";
        $DBFrameWork = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexionFrameWork);
//        $conexionSICA = "sicapital";
//        $DBSICA = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexionSICA);
//


        if (isset($_REQUEST ['tipo_orden']) && $_REQUEST ['tipo_orden'] != '') {
            $tipo_orden = $_REQUEST ['tipo_orden'];
            if ($tipo_orden == '254') {
                $tituloBloque = "Anuladas";
                $estado = 5;
            } elseif ($tipo_orden == '255') {
                $tituloBloque = "Canceladas";
                $estado = 7;
            } elseif ($tipo_orden == '259') {
                $tituloBloque = "Terminados Anticipadamente";
                $estado = 8;
            } elseif ($tipo_orden == '260') {
                $tituloBloque = "Liquidados";
                $estado = 9;
            } else {
                $tituloBloque = "Terminadas";
                $estado = 6;
            }
        } else {
            $tipo_orden = '';
        }
        if (isset($_REQUEST ['vigencia']) && $_REQUEST ['vigencia'] != '') {
            $vigencia = $_REQUEST ['vigencia'];
        } else {
            $vigencia = '';
        }


        if (isset($_REQUEST ['fecha_inicial']) && $_REQUEST ['fecha_inicial'] != '') {
            $fecha_inicial = $_REQUEST ['fecha_inicial'];
        } else {
            $fecha_inicial = '';
        }

        if (isset($_REQUEST ['fecha_final']) && $_REQUEST ['fecha_final'] != '') {
            $fecha_final = $_REQUEST ['fecha_final'];
        } else {
            $fecha_final = '';
        }



        $id_usuario = $_REQUEST['usuario'];
        $cadenaSqlUnidad = $this->miSql->getCadenaSql("obtenerInfoUsuario", $id_usuario);
        $unidadEjecutora = $DBFrameWork->ejecutarAcceso($cadenaSqlUnidad, "busqueda");

        $arreglo_consulta = array(
            'tipo' => $tipo_orden,
            'fecha_inicial' => $fecha_inicial,
            'fecha_final' => $fecha_final,
            'unidad_ejecutora' => $unidadEjecutora[0]['unidad_ejecutora'],
            'estado' => $estado,
            'vigencia' => $vigencia
        );



        $cadenaOrdenes = $this->miSql->getCadenaSql("consultarOrdenGeneral", $arreglo_consulta);
        $Orden = $esteRecursoDB->ejecutarAcceso($cadenaOrdenes, "busqueda");


        $arreglo_consulta = serialize($arreglo_consulta);
        // ---------------- SECCION: Parámetros Generales del Formulario ----------------------------------
        $esteCampo = $esteBloque ['nombre'];
        $atributos ['id'] = $esteCampo;
        $atributos ['nombre'] = $esteCampo;
        // Si no se coloca, entonces toma el valor predeterminado 'application/x-www-form-urlencoded'
        $atributos ['tipoFormulario'] = 'multipart/form-data';
        // Si no se coloca, entonces toma el valor predeterminado 'POST'
        $atributos ['metodo'] = 'POST';
        // Si no se coloca, entonces toma el valor predeterminado 'index.php' (Recomendado)
        $atributos ['action'] = 'index.php';
        // $atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo );
        // Si no se coloca, entonces toma el valor predeterminado.
        $atributos ['estilo'] = '';
        $atributos ['marco'] = true;
        $tab = 1;
        // ---------------- FIN SECCION: de Parámetros Generales del Formulario ----------------------------
        // ----------------INICIAR EL FORMULARIO ------------------------------------------------------------
        $atributos ['tipoEtiqueta'] = 'inicio';
        echo $this->miFormulario->formulario($atributos);
        unset($atributos);
        $miPaginaActual = $this->miConfigurador->getVariableConfiguracion('pagina');

        $directorio = $this->miConfigurador->getVariableConfiguracion("host");
        $directorio .= $this->miConfigurador->getVariableConfiguracion("site") . "/index.php?";
        $directorio .= $this->miConfigurador->getVariableConfiguracion("enlace");

        $variable = "pagina=" . $miPaginaActual;
        $variable = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $directorio);

        // ---------------- SECCION: Controles del Formulario -----------------------------------------------

        $esteCampo = "marcoDatosBasicos";
        $atributos ['id'] = $esteCampo;
        $atributos ["estilo"] = "jqueryui";
        $atributos ['tipoEtiqueta'] = 'inicio';
        $atributos ["leyenda"] = "Consulta de Contratos $tituloBloque";
        echo $this->miFormulario->marcoAgrupacion('inicio', $atributos);
        unset($atributos);
        // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
        $esteCampo = 'botonRegresar';
        $atributos ['id'] = $esteCampo;
        $atributos ['enlace'] = $variable;
        $atributos ['tabIndex'] = 1;
        $atributos ['estilo'] = 'textoSubtitulo';
        $atributos ['enlaceTexto'] = $this->lenguaje->getCadena($esteCampo);
        $atributos ['ancho'] = '10%';
        $atributos ['alto'] = '10%';
        $atributos ['redirLugar'] = true;
        echo $this->miFormulario->enlace($atributos);
        unset($atributos);
        if ($Orden) {

            if ($estado == 6 || $estado == 8) {
                echo "<table id='tablaContratosATCLiquidar'>";
            } else {
                echo "<table id='tablaContratosATC'>";
            }

            echo "<thead>
                             <tr>
                                <th>Tipo Orden</th>
                                <th> Número Contrato y Vigencia</th>            
            			<th>Identificación<br>Nombre Contratista</th>
                                <th>Vigencia</th>
                                <th>Sede-Dependencia</th>
                                <th>Estado</th>
                                <th>Consultar </th>
                                <th>Documento </th>
                                <th>Consultar Novedades</th>";
            if ($estado == 6 || $estado == 8) {
                echo "<th>Liquidar Contrato</th>";
            }


            echo "</tr>
            </thead>
            <tbody>";

            for ($i = 0; $i < count($Orden); $i ++) {

                $variable = "&pagina=consultaOrdenesAprobadas";
                $variable .= "&bloque=consultaOrdenesAprobadas";
                $variable .= "&bloqueGrupo=" . $esteBloque ["grupo"];
                $variable .= "&opcion=consultarOrdenDetalle";
                $variable .= "&arreglo=" . $arreglo_consulta;
                $variable .= "&numerocontrato=" . $Orden [$i] ['numero_contrato'];
                $variable .= "&id_orden=" . $Orden [$i] ['id_orden'];
                $variable .= "&id_solicitud_necesidad=" . $Orden [$i] ['numero_solicitud_necesidad'];
                $variable .= "&vigencia=" . $Orden [$i] ['vigencia'];
                $variable .= "&usuario=" . $_REQUEST['usuario'];
                $variable .= "&estado=" . $Orden [$i] ['estado_orden'];
                $variable .= "&ordenATC=true";
                $variable .= "&numero_contrato_suscrito=" . $Orden [$i] ['numero_contrato_suscrito'];
                $variable .= "&mensaje_titulo= --> Contrato: " . $Orden [$i] ['tipo_contrato'] . " | VIGENCIA: " . $Orden [$i] ['vigencia'] . " | NÚMERO CONTRATO : " . $Orden [$i] ['numero_contrato_suscrito'];
                $variable = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $directorio);


                $variableNovedad = "bloque=gestionOrdenesATC";
                $variableNovedad .= "&bloqueGrupo=gestionOrdenesATC/novedadOrden/";
                $variableNovedad .= "&pagina=registrarNovedadOrden";
                $variableNovedad .= "&opcion=consultanovedades";
                $variableNovedad .= "&numero_contrato=" . $Orden [$i] ['numero_contrato'];
                $variableNovedad .= "&numero_contrato_suscrito=" . $Orden [$i] ['numero_contrato_suscrito'];
                $variableNovedad .= "&arreglo=" . $arreglo_consulta;
                $variableNovedad .= "&gestionContratosATC=true";
                $variableNovedad .= "&vigencia=" . $Orden [$i] ['vigencia'];
                $variableNovedad .= "&usuario=" . $_REQUEST ['usuario'];
                $variableNovedad .= "&mensaje_titulo= --> Contrato: " . $Orden [$i] ['tipo_contrato'] . " | VIGENCIA: " . $Orden [$i] ['vigencia'] . " | NÚMERO CONTRATO : " . $Orden [$i] ['numero_contrato_suscrito'];
                $variableNovedad .= "&tiempo=" . $_REQUEST['tiempo'];
                $variableNovedad = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variableNovedad, $directorio);


                $variable_documento = "action=consultaOrden";
                $variable_documento .= "&pagina=consultaOrden";
                $variable_documento .= "&bloque=consultaOrden";
                $variable_documento .= "&bloqueGrupo=gestionCompras";
                $variable_documento .= "&opcion=generarDocumento";
                $variable_documento .= "&id_orden=" . $Orden [$i] ['id_orden'];
                $variable_documento .= "&numero_contrato=" . $Orden [$i] ['numero_contrato'];
                $variable_documento .= "&vigencia=" . $Orden [$i] ['vigencia'];
                $variable_documento .= "&usuario=" . $_REQUEST['usuario'];

                if ($Orden [$i] ['unidad_ejecutora'] == 1) {
                    $variable_documento .= "&opcion=generarDocumento";
                } else {
                    $variable_documento .= "&opcion=generarDocumento";
                }
                $variable_documento = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable_documento, $directorio);

                $mostrarHtml = "<tr>";
                if ($Orden [$i]["convenio"] == '') {
                    $mostrarHtml .= "<td><center>" . $Orden [$i] ['tipo_contrato'] . "</td>";
                } else {
                    $mostrarHtml .= "<td><center>" . $Orden [$i] ['tipo_contrato'] . "<a href='javascript:void(0);' onclick='VerInfoConvenio(" . $Orden [$i] ['convenio'] . ");'> (Convenio)</a></center></td>";
                }

                $mostrarHtml .= "
                    <td><center>" . $Orden [$i] ['numero_contrato_suscrito'] . " -- " . $Orden [$i] ['vigencia'] . "</center></td>		
                                      ";
                if ($Orden [$i]["clase_contratista"] == '33') {
                    $mostrarHtml .= "<td><center><a href='javascript:void(0);' onclick='VerInfoContratista(" . $Orden [$i] ['proveedor'] . ");'> Información Contratista</a></center></td>";
                } else {
                    $mostrarHtml .= "<td><center><a href='javascript:void(0);' onclick='VerInfoSociedadTemporal(" . $Orden [$i] ['proveedor'] . ");'> Información Contratista</a></center></td>";
                }

                $mostrarHtml .="
                    <td><center>" . $Orden [$i] ['vigencia'] . "</center></td>";

                $mostrarHtml .="<td><center>" . $Orden [$i] ['sededependencia'] . "</center></td>";

                $mostrarHtml .= "<td><center>" . $Orden [$i] ['nombre_estado'] . "</center></td>
                    <td><center>
                    	<a href='" . $variable . "'>
                            <img src='" . $rutaBloque . "/css/images/consulta.png' width='15px'>
                        </a>
                  	</center> </td>
                    <td><center>
                    	<a href='" . $variable_documento . "'>
                            <img src='" . $rutaBloque . "/css/images/documento.png' width='15px'>
                        </a>
                  	</center> </td>
                        <td><center>
                    	<a href='" . $variableNovedad . "'>
                            <img src='" . $rutaBloque . "/css/images/consulta.png' width='15px'>
                        </a>
                  	</center> </td>";


                $variable_liquidar = "pagina=" . $miPaginaActual; // pendiente la pagina para modificar parametro
                $variable_liquidar .= "&opcion=liquidarContrato";
                $variable_liquidar .= "&numero_contrato=" . $Orden [$i] ['numero_contrato'];
                $variable_liquidar .= "&vigencia=" . $Orden [$i] ['vigencia'];
                $variable_liquidar .= "&usuario=" . $_REQUEST ['usuario'];
                $variable_liquidar .= "&arreglo=" . $arreglo_consulta;
                $variable_liquidar .= "&numero_contrato_suscrito=" . $Orden [$i] ['numero_contrato_suscrito'];
                $variable_liquidar .= "&mensaje_titulo= --> Contrato: " . $Orden [$i] ['tipo_contrato'] . " | VIGENCIA: " . $Orden [$i] ['vigencia'] . " | NÚMERO CONTRATO : " . $Orden [$i] ['numero_contrato_suscrito'];
                $variable_liquidar .= "&tiempo=" . $_REQUEST['tiempo'];
                $variable_liquidar = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable_liquidar, $directorio);


                if ($estado == 6 || $estado == 8) {
                    $mostrarHtml .= "<td><center>
                    	<a href='" . $variable_liquidar . "'>
                            <img src='" . $rutaBloque . "/css/images/liquidar.png' width='15px'>
                        </a>
                  	</center> </td>";
                }




                $mostrarHtml .= "</tr>";
                echo $mostrarHtml;
                unset($mostrarHtml);
                unset($variable);
            }

            echo "</tbody>";

            echo "</table>";

            $atributos ["id"] = "ventanaEmergenteContratista";
            $atributos ["estilo"] = " ";
            echo $this->miFormulario->division("inicio", $atributos);

            // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
            $esteCampo = 'infoContratista';
            $atributos ['id'] = $esteCampo;
            $atributos ['tipo'] = 'information';
            $atributos ['estilo'] = 'textoNotasFormulario';
            $atributos ['mensaje'] = "";
            $atributos ['span'] = "spandid";

            $tab ++;

            // Aplica atributos globales al control
            $atributos = array_merge($atributos, $atributosGlobales);
            echo $this->miFormulario->cuadroMensaje($atributos);
            unset($atributos);

            echo $this->miFormulario->division("fin");
            unset($atributos);

            // Fin de Conjunto de Controles
            // echo $this->miFormulario->marcoAgrupacion("fin");
        } else {

            $mensaje = "No Se Encontraron<br>Ordenes.";

            // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
            $esteCampo = 'mensajeRegistro';
            $atributos ['id'] = $esteCampo;
            $atributos ['tipo'] = 'error';
            $atributos ['estilo'] = 'textoCentrar';
            $atributos ['mensaje'] = $mensaje;

            $tab ++;

            // Aplica atributos globales al control
            $atributos = array_merge($atributos, $atributosGlobales);
            echo $this->miFormulario->cuadroMensaje($atributos);
            // --------------- FIN CONTROL : Cuadro de Texto --------------------------------------------------
        }

        echo $this->miFormulario->marcoAgrupacion('fin');

        // ------------------- SECCION: Paso de variables ------------------------------------------------

        /**
         * En algunas ocasiones es útil pasar variables entre las diferentes páginas.
         * SARA permite realizar esto a través de tres
         * mecanismos:
         * (a). Registrando las variables como variables de sesión. Estarán disponibles durante toda la sesión de usuario. Requiere acceso a
         * la base de datos.
         * (b). Incluirlas de manera codificada como campos de los formularios. Para ello se utiliza un campo especial denominado
         * formsara, cuyo valor será una cadena codificada que contiene las variables.
         * (c) a través de campos ocultos en los formularios. (deprecated)
         */
        // En este formulario se utiliza el mecanismo (b) para pasar las siguientes variables:
        // Paso 1: crear el listado de variables

        $valorCodificado = "actionBloque=" . $esteBloque ["nombre"];
        $valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion('pagina');
        $valorCodificado .= "&bloque=" . $esteBloque ['nombre'];
        $valorCodificado .= "&bloqueGrupo=" . $esteBloque ["grupo"];
        $valorCodificado .= "&opcion=regresar";
        $valorCodificado .= "&redireccionar=regresar";
        /**
         * SARA permite que los nombres de los campos sean dinámicos.
         * Para ello utiliza la hora en que es creado el formulario para
         * codificar el nombre de cada campo. Si se utiliza esta técnica es necesario pasar dicho tiempo como una variable:
         * (a) invocando a la variable $_REQUEST ['tiempo'] que se ha declarado en ready.php o
         * (b) asociando el tiempo en que se está creando el formulario
         */
        $valorCodificado .= "&tiempo=" . time();
        // Paso 2: codificar la cadena resultante
        $valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar($valorCodificado);

        $atributos ["id"] = "formSaraData"; // No cambiar este nombre
        $atributos ["tipo"] = "hidden";
        $atributos ['estilo'] = '';
        $atributos ["obligatorio"] = false;
        $atributos ['marco'] = true;
        $atributos ["etiqueta"] = "";
        $atributos ["valor"] = $valorCodificado;
        echo $this->miFormulario->campoCuadroTexto($atributos);
        unset($atributos);

        $atributos ['marco'] = true;
        $atributos ['tipoEtiqueta'] = 'fin';
        echo $this->miFormulario->formulario($atributos);
    }

}

$miSeleccionador = new registrarForm($this->lenguaje, $this->miFormulario, $this->sql);

$miSeleccionador->miForm();
?>
