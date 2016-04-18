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

        $_REQUEST ['tiempo'] = time();
        $tiempo = $_REQUEST ['tiempo'];

        // -------------------------------------------------------------------------------------------------
        $conexion = "inventarios";

        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
        
        $conexionArgo = "estructura";

        $esteRecursoDBArgo = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexionArgo);


        $cadenaSql = $this->miSql->getCadenaSql('polizas');

        $resultado_polizas = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        $resultado_polizas = $resultado_polizas [0];

        $letras = array(
            '1',
            'A',
            'B',
            'C',
            'D'
        );

        $cadenaSql = $this->miSql->getCadenaSql('textos');

        $resultado_textos = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        $texto = array(
            'forma_pago' => $resultado_textos [0] [1],
            'objeto_contrato' => $resultado_textos [1] [1]
        );

        $_REQUEST = array_merge($_REQUEST, $texto);

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
        $atributos ['marco'] = false;
        $tab = 1;
        // ---------------- FIN SECCION: de Parámetros Generales del Formulario ----------------------------
        // ----------------INICIAR EL FORMULARIO ------------------------------------------------------------
        $atributos ['tipoEtiqueta'] = 'inicio';
        echo $this->miFormulario->formulario($atributos); {
            // ---------------- SECCION: Controles del Formulario -----------------------------------------------

            $miPaginaActual = $this->miConfigurador->getVariableConfiguracion('pagina');

            $directorio = $this->miConfigurador->getVariableConfiguracion("host");
            $directorio .= $this->miConfigurador->getVariableConfiguracion("site") . "/index.php?";
            $directorio .= $this->miConfigurador->getVariableConfiguracion("enlace");

            $variable = "pagina=" . $miPaginaActual;
            $variable = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $directorio);



            $esteCampo = "marcoDatosBasicos";
            $atributos ['id'] = $esteCampo;
            $atributos ["estilo"] = "jqueryui";
            $atributos ['tipoEtiqueta'] = 'inicio';
            $atributos ["leyenda"] = "Registrar Orden ";
            echo $this->miFormulario->marcoAgrupacion('inicio', $atributos);
            unset($atributos); {


                // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                $esteCampo = 'tipo_orden';
                $atributos ['columnas'] = 2;
                $atributos ['nombre'] = $esteCampo;
                $atributos ['id'] = $esteCampo;
                $atributos ['evento'] = '';
                $atributos ['deshabilitado'] = false;
                $atributos ["etiquetaObligatorio"] = true;
                $atributos ['tab'] = $tab;
                $atributos ['tamanno'] = 1;
                $atributos ['estilo'] = 'jqueryui';
                $atributos ['validar'] = 'required';
                $atributos ['limitar'] = true;
                $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                $atributos ['anchoEtiqueta'] = 190;

                if (isset($_REQUEST [$esteCampo])) {
                    $atributos ['seleccion'] = $_REQUEST [$esteCampo];
                } else {
                    $atributos ['seleccion'] = - 1;
                }

                $atributos ['cadena_sql'] = $this->miSql->getCadenaSql("tipo_orden");
                $matrizItems = $esteRecursoDB->ejecutarAcceso($atributos ['cadena_sql'], "busqueda");
                $atributos ['matrizItems'] = $matrizItems;

                // Utilizar lo siguiente cuando no se pase un arreglo:
                // $atributos['baseDatos']='ponerAquiElNombreDeLaConexión';
                // $atributos ['cadena_sql']='ponerLaCadenaSqlAEjecutar';
                $tab ++;
                $atributos = array_merge($atributos, $atributosGlobales);
                echo $this->miFormulario->campoCuadroLista($atributos);
                unset($atributos);


                // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                $miSesion = Sesion::singleton();
                $id_usuario = $miSesion->idUsuario();
                $cadenaSqlUnidad = $this->miSql->getCadenaSql("obtenerInfoUsuario", $id_usuario);
                $unidadEjecutora = $esteRecursoDBArgo->ejecutarAcceso($cadenaSqlUnidad, "busqueda");
                $esteCampo = 'unidad_ejecutora';
                $atributos ['id'] = $esteCampo;
                $atributos ['nombre'] = $esteCampo;
                $atributos ['tipo'] = 'text';
                $atributos ['estilo'] = 'jqueryui';
                $atributos ['marco'] = true;
                $atributos ['estiloMarco'] = '';
                $atributos ["etiquetaObligatorio"] = true;
                $atributos ['columnas'] = 2;
                $atributos ['dobleLinea'] = 0;
                $atributos ['tabIndex'] = $tab;
                $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                $atributos ['validar'] = '';

                if (isset($unidadEjecutora)) {
                    $atributos ['valor'] = $unidadEjecutora[0]['nombre'];
                } else {
                    $atributos ['valor'] = 'Usuario sin Dependencia Registrada';
                }
                $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                $atributos ['deshabilitado'] = true;
                $atributos ['tamanno'] = 35;
                $atributos ['maximoTamanno'] = '';
                $atributos ['anchoEtiqueta'] = 220;
                $tab ++;

                // Aplica atributos globales al control
                $atributos = array_merge($atributos, $atributosGlobales);
                echo $this->miFormulario->campoCuadroTexto($atributos);
                unset($atributos);




                $esteCampo = "AgrupacionSolicitante";
                $atributos ['id'] = $esteCampo;
                $atributos ['leyenda'] = "Información del Solicitante";
                echo $this->miFormulario->agrupacion('inicio', $atributos); {

                    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'sede';
                    $atributos ['columnas'] = 2;
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['id'] = $esteCampo;
                    $atributos ['evento'] = '';
                    $atributos ['deshabilitado'] = false;
                    $atributos ["etiquetaObligatorio"] = true;
                    $atributos ['tab'] = $tab;
                    $atributos ['tamanno'] = 1;
                    $atributos ['estilo'] = 'jqueryui';
                    $atributos ['validar'] = 'required';
                    $atributos ['limitar'] = true;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['anchoEtiqueta'] = 170;

                    if (isset($_REQUEST [$esteCampo])) {
                        $atributos ['seleccion'] = $_REQUEST [$esteCampo];
                    } else {
                        $atributos ['seleccion'] = - 1;
                    }

                    $atributos ['cadena_sql'] = $this->miSql->getCadenaSql("sede");
                    $matrizItems = $esteRecursoDB->ejecutarAcceso($atributos ['cadena_sql'], "busqueda");
                    $atributos ['matrizItems'] = $matrizItems;

                    // Utilizar lo siguiente cuando no se pase un arreglo:
                    // $atributos['baseDatos']='ponerAquiElNombreDeLaConexión';
                    // $atributos ['cadena_sql']='ponerLaCadenaSqlAEjecutar';
                    $tab ++;
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroLista($atributos);
                    unset($atributos);

                    $esteCampo = "dependencia_solicitante";
                    $atributos ['columnas'] = 2;
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['id'] = $esteCampo;
                    $atributos ['evento'] = '';
                    $atributos ['deshabilitado'] = true;
                    $atributos ["etiquetaObligatorio"] = true;
                    $atributos ['tab'] = $tab;
                    $atributos ['tamanno'] = 1;
                    $atributos ['estilo'] = 'jqueryui';
                    $atributos ['validar'] = 'required';
                    $atributos ['limitar'] = true;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['anchoEtiqueta'] = 115;
                    if (isset($_REQUEST [$esteCampo])) {
                        $atributos ['seleccion'] = $_REQUEST [$esteCampo];
                    } else {
                        $atributos ['seleccion'] = - 1;
                    }
                    // $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "dependencias" );
                    // $matrizItems = $esteRecursoDBO->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
                    $matrizItems = array(
                        array(
                            ' ',
                            'Vacio'
                        )
                    );

                    $atributos ['matrizItems'] = $matrizItems;

                    // Utilizar lo siguiente cuando no se pase un arreglo:
                    // $atributos['baseDatos']='ponerAquiElNombreDeLaConexión';
                    // $atributos ['cadena_sql']='ponerLaCadenaSqlAEjecutar';
                    $tab ++;
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroLista($atributos);
                    unset($atributos);
                }
                echo $this->miFormulario->agrupacion('fin');

                $esteCampo = "AgrupacionSupervisor";
                $atributos ['id'] = $esteCampo;
                $atributos ['leyenda'] = "Datos del Supervisor";
                echo $this->miFormulario->agrupacion('inicio', $atributos); {

                    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'sede_super';
                    $atributos ['columnas'] = 2;
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['id'] = $esteCampo;
                    $atributos ['evento'] = '';
                    $atributos ['deshabilitado'] = false;
                    $atributos ["etiquetaObligatorio"] = true;
                    $atributos ['tab'] = $tab;
                    $atributos ['tamanno'] = 1;
                    $atributos ['estilo'] = 'jqueryui';
                    $atributos ['validar'] = 'required';
                    $atributos ['limitar'] = true;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['anchoEtiqueta'] = 170;

                    if (isset($_REQUEST [$esteCampo])) {
                        $atributos ['seleccion'] = $_REQUEST [$esteCampo];
                    } else {
                        $atributos ['seleccion'] = - 1;
                    }

                    $atributos ['cadena_sql'] = $this->miSql->getCadenaSql("sede");
                    $matrizItems = $esteRecursoDB->ejecutarAcceso($atributos ['cadena_sql'], "busqueda");
                    $atributos ['matrizItems'] = $matrizItems;

                    // Utilizar lo siguiente cuando no se pase un arreglo:
                    // $atributos['baseDatos']='ponerAquiElNombreDeLaConexión';
                    // $atributos ['cadena_sql']='ponerLaCadenaSqlAEjecutar';
                    $tab ++;
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroLista($atributos);
                    unset($atributos);

                    $esteCampo = 'dependencia_supervisor';
                    $atributos ['columnas'] = 2;
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['id'] = $esteCampo;
                    $atributos ['evento'] = '';
                    $atributos ['deshabilitado'] = true;
                    $atributos ["etiquetaObligatorio"] = true;
                    $atributos ['tab'] = $tab;
                    $atributos ['tamanno'] = 1;
                    $atributos ['estilo'] = 'jqueryui';
                    $atributos ['validar'] = 'required';
                    $atributos ['limitar'] = true;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['anchoEtiqueta'] = 115;
                    if (isset($_REQUEST [$esteCampo])) {
                        $atributos ['seleccion'] = $_REQUEST [$esteCampo];
                    } else {
                        $atributos ['seleccion'] = - 1;
                    }
                    // $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "dependencias" );
                    // $matrizItems = $esteRecursoDBO->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
                    $matrizItems = array(
                        array(
                            ' ',
                            'VACIO'
                        )
                    );

                    $atributos ['matrizItems'] = $matrizItems;

                    // Utilizar lo siguiente cuando no se pase un arreglo:
                    // $atributos['baseDatos']='ponerAquiElNombreDeLaConexión';
                    // $atributos ['cadena_sql']='ponerLaCadenaSqlAEjecutar';
                    $tab ++;
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroLista($atributos);
                    unset($atributos);

                    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'nombre_supervisor';
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['id'] = $esteCampo;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ["etiquetaObligatorio"] = true;
                    $atributos ['tab'] = $tab ++;
                    $atributos ['anchoEtiqueta'] = 170;
                    $atributos ['evento'] = '';
                    if (isset($_REQUEST [$esteCampo])) {
                        $atributos ['seleccion'] = $_REQUEST [$esteCampo];
                    } else {
                        $atributos ['seleccion'] = - 1;
                    }
                    $atributos ['deshabilitado'] = false;
                    $atributos ['columnas'] = 3;
                    $atributos ['tamanno'] = 1;
                    $atributos ['ajax_function'] = "";
                    $atributos ['ajax_control'] = $esteCampo;
                    $atributos ['estilo'] = "jqueryui";
                    $atributos ['validar'] = "required";
                    $atributos ['limitar'] = true;
                    $atributos ['anchoCaja'] = 52;
                    $atributos ['miEvento'] = '';
                    $atributos ['cadena_sql'] = $this->miSql->getCadenaSql("funcionarios");

                    $matrizItems = array(
                        array(
                            0,
                            ' '
                        )
                    );
                    $matrizItems = $esteRecursoDB->ejecutarAcceso($atributos ['cadena_sql'], "busqueda");
                    $atributos ['matrizItems'] = $matrizItems;
                    // $atributos['miniRegistro']=;
                    $atributos ['baseDatos'] = "inventarios";
                    // $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "clase_entrada" );
                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroLista($atributos);
                    unset($atributos);
                    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'cargo_supervisor';
                    $atributos ['id'] = $esteCampo;
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['tipo'] = 'text';
                    $atributos ['estilo'] = 'jqueryui';
                    $atributos ['marco'] = true;
                    $atributos ['estiloMarco'] = '';
                    $atributos ["etiquetaObligatorio"] = false;
                    $atributos ['columnas'] = 3;
                    $atributos ['dobleLinea'] = 0;
                    $atributos ['tabIndex'] = $tab;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['validar'] = 'required, minSize[1]';

                    if (isset($_REQUEST [$esteCampo])) {
                        $atributos ['valor'] = $_REQUEST [$esteCampo];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = true;
                    $atributos ['tamanno'] = 33;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 115;
                    $tab ++;

                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);


                    // ---------------- CONTROL: Cuadro Lista --------------------------------------------------------
                    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'cargosExistentes';
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['id'] = $esteCampo;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ["etiquetaObligatorio"] = false;
                    $atributos ['tab'] = $tab ++;
                    $atributos ['anchoEtiqueta'] = 170;
                    $atributos ['evento'] = '';
                    if (isset($_REQUEST [$esteCampo])) {
                        $atributos ['seleccion'] = $_REQUEST [$esteCampo];
                    } else {
                        $atributos ['seleccion'] = - 1;
                    }
                    $atributos ['deshabilitado'] = false;
                    $atributos ['columnas'] = 3;
                    $atributos ['tamanno'] = 1;
                    $atributos ['estilo'] = "jqueryui";
                    $atributos ['validar'] = "";
                    $atributos ['limitar'] = true;
                    $atributos ['anchoCaja'] = 52;
                    $atributos ['cadena_sql'] = $this->miSql->getCadenaSql("cargos_existentes");

                    $matrizItems = array(
                        array(
                            0,
                            ' '
                        )
                    );
                    $matrizItems = $esteRecursoDB->ejecutarAcceso($atributos ['cadena_sql'], "busqueda");

                    for ($i = 0; $i < count($matrizItems); $i++) {
                        $opciones[$i] = array($matrizItems[$i][0], $matrizItems[$i][0]);
                    }
                    $atributos ['matrizItems'] = $opciones;


                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroLista($atributos);
                    unset($atributos);
                    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    // -----------------CONTROL: Botón ----------------------------------------------------------------
                    $esteCampo = 'botonCargo';
                    $atributos ["id"] = $esteCampo;
                    $atributos ["tabIndex"] = $tab;
                    $atributos ["tipo"] = 'boton';
                    // submit: no se coloca si se desea un tipo button genérico
                    $atributos ['onClick'] = 'registrarNuevoCargo()';
                    $atributos ["estiloMarco"] = '';
                    $atributos ["estiloBoton"] = '';
                    // verificar: true para verificar el formulario antes de pasarlo al servidor.
                    $atributos ["verificar"] = '';
                    $atributos ["tipoSubmit"] = ''; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
                    $atributos ["valor"] = $this->lenguaje->getCadena($esteCampo);
                    $tab ++;

                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoBoton($atributos);
                    unset($atributos);
                }
                echo $this->miFormulario->agrupacion('fin');

                $esteCampo = "AgrupacionContratista";
                $atributos ['id'] = $esteCampo;
                $atributos ['leyenda'] = "Información del Contratista";
                echo $this->miFormulario->agrupacion('inicio', $atributos); {

                    $esteCampo = "selec_proveedor";
                    $atributos ['id'] = $esteCampo;
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['tipo'] = 'text';
                    $atributos ['estilo'] = 'jqueryui';
                    $atributos ['marco'] = true;
                    $atributos ['estiloMarco'] = '';
                    $atributos ["etiquetaObligatorio"] = false;
                    $atributos ['columnas'] = 1;
                    $atributos ['dobleLinea'] = 0;
                    $atributos ['tabIndex'] = $tab;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['validar'] = ' ';
                    $atributos ['textoFondo'] = 'Ingrese Mínimo 3 Caracteres de Búsqueda';

                    if (isset($_REQUEST [$esteCampo])) {
                        $atributos ['valor'] = $_REQUEST [$esteCampo];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = false;
                    $atributos ['tamanno'] = 60;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 220;
                    $tab ++;

                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);

                    $esteCampo = 'id_proveedor';
                    $atributos ["id"] = $esteCampo; // No cambiar este nombre
                    $atributos ["tipo"] = "hidden";
                    $atributos ['estilo'] = '';
                    $atributos ["obligatorio"] = false;
                    $atributos ['marco'] = true;
                    $atributos ["etiqueta"] = "";
                    $atributos ['valor'] = '';
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);

                    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'nombre_razon_proveedor';
                    $atributos ['id'] = $esteCampo;
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['tipo'] = 'text';
                    $atributos ['estilo'] = 'jqueryui';
                    $atributos ['marco'] = true;
                    $atributos ['estiloMarco'] = '';
                    $atributos ["etiquetaObligatorio"] = true;
                    $atributos ['columnas'] = 2;
                    $atributos ['dobleLinea'] = 0;
                    $atributos ['tabIndex'] = $tab;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['validar'] = 'required';

                    if (isset($_REQUEST [$esteCampo])) {
                        $atributos ['valor'] = $_REQUEST [$esteCampo];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = false;
                    $atributos ['tamanno'] = 28;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 220;
                    $tab ++;

                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);

                    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'identifcacion_proveedor';
                    $atributos ['id'] = $esteCampo;
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['tipo'] = 'text';
                    $atributos ['estilo'] = 'jqueryui';
                    $atributos ['marco'] = true;
                    $atributos ['estiloMarco'] = '';
                    $atributos ["etiquetaObligatorio"] = true;
                    $atributos ['columnas'] = 2;
                    $atributos ['dobleLinea'] = 0;
                    $atributos ['tabIndex'] = $tab;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['validar'] = 'required,custom[onlyNumberSp]';

                    if (isset($_REQUEST [$esteCampo])) {
                        $atributos ['valor'] = $_REQUEST [$esteCampo];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = false;
                    $atributos ['tamanno'] = 20;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 182;
                    $tab ++;

                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);

                    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'direccion_proveedor';
                    $atributos ['id'] = $esteCampo;
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['tipo'] = 'text';
                    $atributos ['estilo'] = 'jqueryui';
                    $atributos ['marco'] = true;
                    $atributos ['estiloMarco'] = '';
                    $atributos ["etiquetaObligatorio"] = true;
                    $atributos ['columnas'] = 2;
                    $atributos ['dobleLinea'] = 0;
                    $atributos ['tabIndex'] = $tab;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['validar'] = 'required';

                    if (isset($_REQUEST [$esteCampo])) {
                        $atributos ['valor'] = $_REQUEST [$esteCampo];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = false;
                    $atributos ['tamanno'] = 28;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 220;
                    $tab ++;

                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);

                    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'telefono_proveedor';
                    $atributos ['id'] = $esteCampo;
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['tipo'] = 'text';
                    $atributos ['estilo'] = 'jqueryui';
                    $atributos ['marco'] = true;
                    $atributos ['estiloMarco'] = '';
                    $atributos ["etiquetaObligatorio"] = true;
                    $atributos ['columnas'] = 2;
                    $atributos ['dobleLinea'] = 0;
                    $atributos ['tabIndex'] = $tab;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['validar'] = 'required';

                    if (isset($_REQUEST [$esteCampo])) {
                        $atributos ['valor'] = $_REQUEST [$esteCampo];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = false;
                    $atributos ['tamanno'] = 20;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 182;
                    $tab ++;

                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);




                    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'nombre_contratista';
                    $atributos ['id'] = $esteCampo;
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['tipo'] = 'text';
                    $atributos ['estilo'] = 'jqueryui';
                    $atributos ['marco'] = true;
                    $atributos ['estiloMarco'] = '';
                    $atributos ["etiquetaObligatorio"] = true;
                    $atributos ['columnas'] = 2;
                    $atributos ['dobleLinea'] = 0;
                    $atributos ['tabIndex'] = $tab;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['validar'] = 'required, minSize[1]';

                    if (isset($_REQUEST [$esteCampo])) {
                        $atributos ['valor'] = $_REQUEST [$esteCampo];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = false;
                    $atributos ['tamanno'] = 28;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 220;
                    $tab ++;

                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);


                    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'identifcacion_contratista';
                    $atributos ['id'] = $esteCampo;
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['tipo'] = 'text';
                    $atributos ['estilo'] = 'jqueryui';
                    $atributos ['marco'] = true;
                    $atributos ['estiloMarco'] = '';
                    $atributos ["etiquetaObligatorio"] = true;
                    $atributos ['columnas'] = 2;
                    $atributos ['dobleLinea'] = 0;
                    $atributos ['tabIndex'] = $tab;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['validar'] = 'required,maxSize[10],custom[onlyNumberSp]';

                    if (isset($_REQUEST [$esteCampo])) {
                        $atributos ['valor'] = $_REQUEST [$esteCampo];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = false;
                    $atributos ['tamanno'] = 20;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 182;
                    $tab ++;

                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);



                    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'cargo_contratista';
                    $atributos ['id'] = $esteCampo;
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['tipo'] = 'text';
                    $atributos ['estilo'] = 'jqueryui';
                    $atributos ['marco'] = true;
                    $atributos ['estiloMarco'] = '';
                    $atributos ["etiquetaObligatorio"] = true;
                    $atributos ['columnas'] = 1;
                    $atributos ['dobleLinea'] = 0;
                    $atributos ['tabIndex'] = $tab;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['validar'] = 'required, minSize[1]';

                    if (isset($_REQUEST [$esteCampo])) {
                        $atributos ['valor'] = $_REQUEST [$esteCampo];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = false;
                    $atributos ['tamanno'] = 28;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 220;
                    $tab ++;

                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);
                }
                echo $this->miFormulario->agrupacion('fin');

                $esteCampo = "AgrupacionObjetoContrato";
                $atributos ['id'] = $esteCampo;
                $atributos ['leyenda'] = "Información del Contrato";
                echo $this->miFormulario->agrupacion('inicio', $atributos); {

                    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'objeto_contrato';
                    $atributos ['id'] = $esteCampo;
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['tipo'] = 'text';
                    $atributos ['estilo'] = 'jqueryui';
                    $atributos ['marco'] = true;
                    $atributos ['estiloMarco'] = '';
                    $atributos ["etiquetaObligatorio"] = true;
                    $atributos ['columnas'] = 95;
                    $atributos ['filas'] = 15;
                    $atributos ['dobleLinea'] = 0;
                    $atributos ['tabIndex'] = $tab;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['validar'] = 'required, minSize[1]';
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = false;
                    $atributos ['tamanno'] = 20;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 220;
                    if (isset($_REQUEST [$esteCampo])) {
                        $atributos ['valor'] = $_REQUEST [$esteCampo];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $tab ++;

                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoTextArea($atributos);
                    unset($atributos);

                    $esteCampo = "AgrupacionPoliza";
                    $atributos ['id'] = $esteCampo;
                    $atributos ['leyenda'] = "Requerimiento de Póliza";
                    echo $this->miFormulario->agrupacion('inicio', $atributos); {
                        for ($i = 1; $i <= 4; $i ++) {

                            // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                            $nombre = 'poliza' . $letras [$i];
                            $atributos ['id'] = $nombre;
                            $atributos ['nombre'] = $nombre;
                            $atributos ['estilo'] = 'campoCuadroSeleccionCorta';
                            $atributos ['marco'] = true;
                            $atributos ['estiloMarco'] = true;
                            $atributos ["etiquetaObligatorio"] = true;
                            $atributos ['columnas'] = 1;
                            $atributos ['dobleLinea'] = 1;
                            $atributos ['tabIndex'] = $tab;
                            $atributos ['etiqueta'] = $resultado_polizas [$i];
                            $atributos ['validar'] = '';

                            if (isset($_REQUEST [$esteCampo])) {
                                $atributos ['valor'] = $_REQUEST [$esteCampo];
                            } else {
                                $atributos ['valor'] = 'poliza' . $i;
                            }

                            $atributos ['deshabilitado'] = false;
                            $tab ++;

                            // Aplica atributos globales al control
                            $atributos = array_merge($atributos, $atributosGlobales);
                            echo $this->miFormulario->campoCuadroSeleccion($atributos);
                            unset($atributos);
                        }
                    }
                    echo $this->miFormulario->agrupacion('fin');
                }
                echo $this->miFormulario->agrupacion('fin');

                $esteCampo = "AgrupacionReferentePago";
                $atributos ['id'] = $esteCampo;
                $atributos ['leyenda'] = "Duración de Contrato";
                echo $this->miFormulario->agrupacion('inicio', $atributos); {

                    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'fecha_inicio_pago';
                    $atributos ['id'] = $esteCampo;
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['tipo'] = 'fecha';
                    $atributos ['estilo'] = 'jqueryui';
                    $atributos ['marco'] = true;
                    $atributos ['estiloMarco'] = '';
                    $atributos ["etiquetaObligatorio"] = true;
                    $atributos ['columnas'] = 3;
                    $atributos ['dobleLinea'] = 0;
                    $atributos ['tabIndex'] = $tab;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['validar'] = 'require,custom[date]';

                    if (isset($_REQUEST [$esteCampo])) {
                        $atributos ['valor'] = $_REQUEST [$esteCampo];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = false;
                    $atributos ['tamanno'] = 8;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 160;
                    $tab ++;

                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);

                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);

                    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'fecha_final_pago';
                    $atributos ['id'] = $esteCampo;
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['tipo'] = 'fecha';
                    $atributos ['estilo'] = 'jqueryui';
                    $atributos ['marco'] = true;
                    $atributos ['estiloMarco'] = '';
                    $atributos ["etiquetaObligatorio"] = true;
                    $atributos ['columnas'] = 3;
                    $atributos ['dobleLinea'] = 0;
                    $atributos ['tabIndex'] = $tab;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['validar'] = 'require,custom[date]';

                    if (isset($_REQUEST [$esteCampo])) {
                        $atributos ['valor'] = $_REQUEST [$esteCampo];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = false;
                    $atributos ['tamanno'] = 8;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 160;
                    $tab ++;

                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);

                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);

                    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'duracion';
                    $atributos ['id'] = $esteCampo;
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['tipo'] = 'text';
                    $atributos ['estilo'] = 'jqueryui';
                    $atributos ['marco'] = true;
                    $atributos ['estiloMarco'] = '';
                    $atributos ["etiquetaObligatorio"] = false;
                    $atributos ['columnas'] = 3;
                    $atributos ['dobleLinea'] = 0;
                    $atributos ['tabIndex'] = $tab;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['validar'] = 'required, minSize[1],maxSize[10],custom[onlyNumberSp]';

                    if (isset($_REQUEST [$esteCampo])) {
                        $atributos ['valor'] = $_REQUEST [$esteCampo];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = false;
                    $atributos ['tamanno'] = 11;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 160;
                    $tab ++;

                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);
                    // --------------- FIN CONTROL : Cuadro de Texto --------------------------------------------------

                    $atributos ["id"] = "numero_dias"; // No cambiar este nombre
                    $atributos ["tipo"] = "hidden";
                    $atributos ['estilo'] = '';
                    $atributos ["obligatorio"] = false;
                    $atributos ['marco'] = true;
                    $atributos ["etiqueta"] = "";
                    $atributos ["valor"] = '';
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);

                    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'forma_pago';
                    $atributos ['id'] = $esteCampo;
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['tipo'] = 'text';
                    $atributos ['estilo'] = 'jqueryui';
                    $atributos ['marco'] = true;
                    $atributos ['estiloMarco'] = '';
                    $atributos ["etiquetaObligatorio"] = true;
                    $atributos ['columnas'] = 95;
                    $atributos ['filas'] = 3;
                    $atributos ['dobleLinea'] = 0;
                    $atributos ['tabIndex'] = $tab;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['validar'] = 'required, minSize[1]';
                    $atributos ['deshabilitado'] = false;
                    $atributos ['tamanno'] = 20;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 220;
                    if (isset($_REQUEST [$esteCampo])) {
                        $atributos ['valor'] = $_REQUEST [$esteCampo];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $tab ++;

                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoTextArea($atributos);
                    unset($atributos);

                    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'total_preliminar';
                    $atributos ['id'] = $esteCampo;
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['tipo'] = 'text';
                    $atributos ['estilo'] = 'jqueryui';
                    $atributos ['marco'] = true;
                    $atributos ['estiloMarco'] = '';
                    $atributos ["etiquetaObligatorio"] = true;
                    $atributos ['columnas'] = 3;
                    $atributos ['dobleLinea'] = 0;
                    $atributos ['tabIndex'] = $tab;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['validar'] = 'required, minSize[1],maxSize[30],custom[onlyNumberSp]';

                    if (isset($_REQUEST [$esteCampo])) {
                        $atributos ['valor'] = $_REQUEST [$esteCampo];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = false;
                    $atributos ['tamanno'] = 18;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 160;
                    $tab ++;

                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
// 					echo $this->miFormulario->campoCuadroTexto ( $atributos );
                    unset($atributos);
                    // --------------- FIN CONTROL : Cuadro de Texto --------------------------------------------------
                    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'iva';
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['id'] = $esteCampo;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ["etiquetaObligatorio"] = false;
                    $atributos ['tab'] = $tab ++;
                    $atributos ['seleccion'] = 0;
                    $atributos ['anchoEtiqueta'] = 200;
                    $atributos ['evento'] = '';
                    if (isset($_REQUEST [$esteCampo])) {
                        $atributos ['valor'] = $_REQUEST [$esteCampo];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $atributos ['deshabilitado'] = false;
                    $atributos ['columnas'] = 3;
                    $atributos ['tamanno'] = 1;
                    $atributos ['ajax_function'] = "";
                    $atributos ['ajax_control'] = $esteCampo;
                    $atributos ['estilo'] = "jqueryui";
                    $atributos ['validar'] = "required";
                    $atributos ['limitar'] = 0;
                    $atributos ['anchoCaja'] = 30;
                    $atributos ['miEvento'] = '';
                    $atributos ['cadena_sql'] = $this->miSql->getCadenaSql("dependencia");
                    $matrizItems = array(
                        array(
                            0,
                            'NO'
                        ),
                        array(
                            1,
                            'SI'
                        )
                    );
                    // $matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
                    $atributos ['matrizItems'] = $matrizItems;
                    // $atributos['miniRegistro']=;
                    $atributos ['baseDatos'] = "inventarios";
                    // $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "clase_entrada" );
                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
// 					echo $this->miFormulario->campoCuadroLista ( $atributos );
                    unset($atributos);
                    // --------------- FIN CONTROL : Cuadro de Texto --------------------------------------------------
                    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'total_iva';
                    $atributos ['id'] = $esteCampo;
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['tipo'] = 'text';
                    $atributos ['estilo'] = 'jqueryui';
                    $atributos ['marco'] = true;
                    $atributos ['estiloMarco'] = '';
                    $atributos ["etiquetaObligatorio"] = false;
                    $atributos ['columnas'] = 3;
                    $atributos ['dobleLinea'] = 0;
                    $atributos ['tabIndex'] = $tab;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['validar'] = 'required, minSize[1],maxSize[30],custom[onlyNumberSp]';

                    if (isset($_REQUEST [$esteCampo])) {
                        $atributos ['valor'] = $_REQUEST [$esteCampo];
                    } else {
                        $atributos ['valor'] = 0;
                    }
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = false;
                    $atributos ['tamanno'] = 13;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 100;
                    $tab ++;

                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
// 					echo $this->miFormulario->campoCuadroTexto ( $atributos );
                    unset($atributos);

                    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'total';
                    $atributos ['id'] = $esteCampo;
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['tipo'] = 'text';
                    $atributos ['estilo'] = 'jqueryui';
                    $atributos ['marco'] = true;
                    $atributos ['estiloMarco'] = '';
                    $atributos ["etiquetaObligatorio"] = false;
                    $atributos ['columnas'] = 1;
                    $atributos ['dobleLinea'] = 0;
                    $atributos ['tabIndex'] = $tab;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['validar'] = 'required, minSize[1],maxSize[30],custom[onlyNumberSp]';

                    if (isset($_REQUEST [$esteCampo])) {
                        $atributos ['valor'] = $_REQUEST [$esteCampo];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = false;
                    $atributos ['tamanno'] = 25;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 160;
                    $tab ++;

                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
// 					echo $this->miFormulario->campoCuadroTexto ( $atributos );
                    unset($atributos);
                    // --------------- FIN CONTROL : Cuadro de Texto --------------------------------------------------
                }

                echo $this->miFormulario->agrupacion('fin');
                unset($atributos);
                /*
                  $esteCampo = "AgrupacionDisponibilidad";
                  $atributos ['id'] = $esteCampo;
                  $atributos ['leyenda'] = "Información Respaldo Presupuestal";
                  echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
                  {



                  $esteCampo = "vigencia_disponibilidad";
                  $atributos ['nombre'] = $esteCampo;
                  $atributos ['id'] = $esteCampo;
                  $atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
                  $atributos ["etiquetaObligatorio"] = true;
                  $atributos ['tab'] = $tab ++;
                  $atributos ['seleccion'] = - 1;
                  $atributos ['anchoEtiqueta'] = 200;
                  $atributos ['evento'] = '';
                  if (isset ( $_REQUEST [$esteCampo] )) {
                  $atributos ['valor'] = $_REQUEST [$esteCampo];
                  } else {
                  $atributos ['valor'] = '';
                  }
                  $atributos ['deshabilitado'] = false;
                  $atributos ['columnas'] = 2;
                  $atributos ['tamanno'] = 1;
                  $atributos ['ajax_function'] = "";
                  $atributos ['ajax_control'] = $esteCampo;
                  $atributos ['estilo'] = "jqueryui";
                  $atributos ['validar'] = "required";
                  $atributos ['limitar'] = 1;
                  $atributos ['anchoCaja'] = 27;
                  $atributos ['miEvento'] = '';
                  $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "vigencia_disponibilidad" );
                  $matrizItems = array (
                  array (
                  0,
                  ''
                  )
                  );
                  $matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
                  $atributos ['matrizItems'] = $matrizItems;
                  // $atributos['miniRegistro']=;
                  $atributos ['baseDatos'] = "sicapital";
                  // $atributos ['baseDatos'] = "inventarios";

                  // $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "clase_entrada" );

                  // Aplica atributos globales al control
                  $atributos = array_merge ( $atributos, $atributosGlobales );
                  echo $this->miFormulario->campoCuadroLista ( $atributos );
                  unset ( $atributos );



                  $esteCampo = "unidad_ejecutora";
                  $atributos ['nombre'] = $esteCampo;
                  $atributos ['id'] = $esteCampo;
                  $atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
                  $atributos ["etiquetaObligatorio"] = true;
                  $atributos ['tab'] = $tab ++;
                  $atributos ['seleccion'] = 1;
                  $atributos ['anchoEtiqueta'] = 150;
                  $atributos ['evento'] = '';
                  if (isset ( $_REQUEST [$esteCampo] )) {
                  $atributos ['valor'] = $_REQUEST [$esteCampo];
                  } else {
                  $atributos ['valor'] = '';
                  }
                  $atributos ['deshabilitado'] = false;
                  $atributos ['columnas'] = 2;
                  $atributos ['tamanno'] = 1;
                  $atributos ['ajax_function'] = "";
                  $atributos ['ajax_control'] = $esteCampo;
                  $atributos ['estilo'] = "jqueryui";
                  $atributos ['validar'] = "required";
                  $atributos ['limitar'] = 1;
                  $atributos ['anchoCaja'] = 27;
                  $atributos ['miEvento'] = '';
                  $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "Unidad_Ejecutoria" );
                  $matrizItems = array (
                  array (
                  0,
                  ' '
                  )
                  );
                  $matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );


                  $atributos ['matrizItems'] = $matrizItems;
                  // $atributos['miniRegistro']=;
                  $atributos ['baseDatos'] = "sicapital";
                  // $atributos ['baseDatos'] = "inventarios";

                  // $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "clase_entrada" );

                  // Aplica atributos globales al control
                  $atributos = array_merge ( $atributos, $atributosGlobales );
                  echo $this->miFormulario->campoCuadroLista ( $atributos );
                  unset ( $atributos );



                  $esteCampo = "AgrupacionCertificadoPresupuestal";
                  $atributos ['id'] = $esteCampo;
                  $atributos ['leyenda'] = "Certificado de Disponibilidad Presupuestal";
                  echo $this->miFormulario->agrupacion ( 'inicio', $atributos );

                  {

                  // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                  $esteCampo = 'diponibilidad';
                  $atributos ['nombre'] = $esteCampo;
                  $atributos ['id'] = $esteCampo;
                  $atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
                  $atributos ["etiquetaObligatorio"] = true;
                  $atributos ['tab'] = $tab ++;
                  $atributos ['seleccion'] = - 1;
                  $atributos ['anchoEtiqueta'] = 180;
                  $atributos ['evento'] = '';
                  if (isset ( $_REQUEST [$esteCampo] )) {
                  $atributos ['valor'] = $_REQUEST [$esteCampo];
                  } else {
                  $atributos ['valor'] = '';
                  }
                  $atributos ['deshabilitado'] = true;
                  $atributos ['columnas'] = 2;
                  $atributos ['tamanno'] = 1;
                  $atributos ['ajax_function'] = "";
                  $atributos ['ajax_control'] = $esteCampo;
                  $atributos ['estilo'] = "jqueryui";
                  $atributos ['validar'] = "required";
                  $atributos ['limitar'] = 1;
                  $atributos ['anchoCaja'] = 40;
                  $atributos ['miEvento'] = '';
                  // $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "numero_disponibilidad" );
                  $matrizItems = array (
                  array (
                  '',
                  ''
                  )
                  );
                  // $matrizItems = $esteRecursoDBO->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
                  $atributos ['matrizItems'] = $matrizItems;
                  // $atributos['miniRegistro']=;
                  $atributos ['baseDatos'] = "sicapital";
                  // $atributos ['baseDatos'] = "inventarios";

                  // $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "clase_entrada" );

                  // Aplica atributos globales al control
                  $atributos = array_merge ( $atributos, $atributosGlobales );
                  echo $this->miFormulario->campoCuadroLista ( $atributos );
                  unset ( $atributos );

                  // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                  $esteCampo = 'valor_disponibilidad';
                  $atributos ['id'] = $esteCampo;
                  $atributos ['nombre'] = $esteCampo;
                  $atributos ['tipo'] = 'fecha';
                  $atributos ['estilo'] = 'jqueryui';
                  $atributos ['marco'] = true;
                  $atributos ['estiloMarco'] = '';
                  $atributos ["etiquetaObligatorio"] = false;
                  $atributos ['columnas'] = 2;
                  $atributos ['dobleLinea'] = 0;
                  $atributos ['tabIndex'] = $tab;
                  $atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
                  $atributos ['validar'] = '';

                  if (isset ( $_REQUEST [$esteCampo] )) {
                  $atributos ['valor'] = $_REQUEST [$esteCampo];
                  } else {
                  $atributos ['valor'] = '';
                  }
                  $atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
                  $atributos ['deshabilitado'] = true;
                  $atributos ['tamanno'] = 30;
                  $atributos ['maximoTamanno'] = '';
                  $atributos ['anchoEtiqueta'] = 100;
                  $tab ++;

                  // Aplica atributos globales al control
                  $atributos = array_merge ( $atributos, $atributosGlobales );

                  echo $this->miFormulario->campoCuadroTexto ( $atributos );
                  unset ( $atributos );

                  // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                  $esteCampo = 'fecha_diponibilidad';
                  $atributos ['id'] = $esteCampo;
                  $atributos ['nombre'] = $esteCampo;
                  $atributos ['tipo'] = 'fecha';
                  $atributos ['estilo'] = 'jqueryui';
                  $atributos ['marco'] = true;
                  $atributos ['estiloMarco'] = '';
                  $atributos ["etiquetaObligatorio"] = false;
                  $atributos ['columnas'] = 1;
                  $atributos ['dobleLinea'] = 0;
                  $atributos ['tabIndex'] = $tab;
                  $atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
                  $atributos ['validar'] = '';

                  if (isset ( $_REQUEST [$esteCampo] )) {
                  $atributos ['valor'] = $_REQUEST [$esteCampo];
                  } else {
                  $atributos ['valor'] = '';
                  }
                  $atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
                  $atributos ['deshabilitado'] = true;
                  $atributos ['tamanno'] = 8;
                  $atributos ['maximoTamanno'] = '';
                  $atributos ['anchoEtiqueta'] = 180;
                  $tab ++;

                  // Aplica atributos globales al control
                  $atributos = array_merge ( $atributos, $atributosGlobales );

                  echo $this->miFormulario->campoCuadroTexto ( $atributos );
                  unset ( $atributos );

                  // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                  $esteCampo = 'valorLetras_disponibilidad';
                  $atributos ['id'] = $esteCampo;
                  $atributos ['nombre'] = $esteCampo;
                  $atributos ['tipo'] = 'text';
                  $atributos ['estilo'] = 'jqueryui';
                  $atributos ['marco'] = true;
                  $atributos ['estiloMarco'] = '';
                  $atributos ["etiquetaObligatorio"] = true;
                  $atributos ['columnas'] = 105;
                  $atributos ['filas'] = 3;
                  $atributos ['dobleLinea'] = 0;
                  $atributos ['tabIndex'] = $tab;
                  $atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
                  // $atributos ['validar'] = 'required, minSize[1]';
                  $atributos ['deshabilitado'] = true;
                  $atributos ['tamanno'] = 20;
                  $atributos ['maximoTamanno'] = '';
                  $atributos ['anchoEtiqueta'] = 220;
                  if (isset ( $_REQUEST [$esteCampo] )) {
                  $atributos ['valor'] = $_REQUEST [$esteCampo];
                  } else {
                  $atributos ['valor'] = '';
                  }
                  $tab ++;

                  // Aplica atributos globales al control
                  $atributos = array_merge ( $atributos, $atributosGlobales );
                  echo $this->miFormulario->campoTextArea ( $atributos );
                  unset ( $atributos );
                  }

                  echo $this->miFormulario->agrupacion ( 'fin' );

                  $esteCampo = "AgrupacionRegistroPresupuestal";
                  $atributos ['id'] = $esteCampo;
                  $atributos ['leyenda'] = "Certificado de Registro Presupuestal";
                  echo $this->miFormulario->agrupacion ( 'inicio', $atributos );

                  {

                  // 						$esteCampo = 'vigencia_registro';
                  // 						$atributos ['nombre'] = $esteCampo;
                  // 						$atributos ['id'] = $esteCampo;
                  // 						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
                  // 						$atributos ["etiquetaObligatorio"] = true;
                  // 						$atributos ['tab'] = $tab ++;
                  // 						$atributos ['seleccion'] = - 1;
                  // 						$atributos ['anchoEtiqueta'] = 220;
                  // 						$atributos ['evento'] = '';
                  // 						if (isset ( $_REQUEST [$esteCampo] )) {
                  // 							$atributos ['valor'] = $_REQUEST [$esteCampo];
                  // 						} else {
                  // 							$atributos ['valor'] = '';
                  // 						}
                  // 						$atributos ['deshabilitado'] = false;
                  // 						$atributos ['columnas'] = 1;
                  // 						$atributos ['tamanno'] = 1;
                  // 						$atributos ['ajax_function'] = "";
                  // 						$atributos ['ajax_control'] = $esteCampo;
                  // 						$atributos ['estilo'] = "jqueryui";
                  // 						$atributos ['validar'] = "required";
                  // 						$atributos ['limitar'] = 1;
                  // 						$atributos ['anchoCaja'] = 27;
                  // 						$atributos ['miEvento'] = '';
                  // 						$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "vigencia_registro" );
                  // 						$matrizItems = array (
                  // 								array (
                  // 										'',
                  // 										''
                  // 								)
                  // 						);
                  // 						$matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
                  // 						$atributos ['matrizItems'] = $matrizItems;
                  // 						// $atributos['miniRegistro']=;
                  // 						$atributos ['baseDatos'] = "sicapital";
                  // 						// $atributos ['baseDatos'] = "inventarios";

                  // 						// $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "clase_entrada" );

                  // 						// Aplica atributos globales al control
                  // 						$atributos = array_merge ( $atributos, $atributosGlobales );
                  // // 						echo $this->miFormulario->campoCuadroLista ( $atributos );
                  // 						unset ( $atributos );

                  // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                  $esteCampo = 'registro';
                  $atributos ['nombre'] = $esteCampo;
                  $atributos ['id'] = $esteCampo;
                  $atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
                  $atributos ["etiquetaObligatorio"] = true;
                  $atributos ['tab'] = $tab ++;
                  $atributos ['seleccion'] = - 1;
                  $atributos ['anchoEtiqueta'] = 180;
                  $atributos ['evento'] = '';
                  if (isset ( $_REQUEST [$esteCampo] )) {
                  $atributos ['valor'] = $_REQUEST [$esteCampo];
                  } else {
                  $atributos ['valor'] = '';
                  }
                  $atributos ['deshabilitado'] = true;
                  $atributos ['columnas'] = 2;
                  $atributos ['tamanno'] = 1;
                  $atributos ['ajax_function'] = "";
                  $atributos ['ajax_control'] = $esteCampo;
                  $atributos ['estilo'] = "jqueryui";
                  $atributos ['validar'] = "required";
                  $atributos ['limitar'] = 1;
                  $atributos ['anchoCaja'] = 27;
                  $atributos ['miEvento'] = '';
                  // $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "numero_registro" );
                  $matrizItems = array (
                  array (
                  '',
                  ''
                  )
                  );
                  // $matrizItems = $esteRecursoDBO->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
                  $atributos ['matrizItems'] = $matrizItems;
                  // $atributos['miniRegistro']=;
                  $atributos ['baseDatos'] = "inventarios";
                  // $atributos ['baseDatos'] = "inventarios";

                  // $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "clase_entrada" );

                  // Aplica atributos globales al control
                  $atributos = array_merge ( $atributos, $atributosGlobales );
                  echo $this->miFormulario->campoCuadroLista ( $atributos );
                  unset ( $atributos );

                  // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                  $esteCampo = 'valor_registro';
                  $atributos ['id'] = $esteCampo;
                  $atributos ['nombre'] = $esteCampo;
                  $atributos ['tipo'] = '';
                  $atributos ['estilo'] = 'jqueryui';
                  $atributos ['marco'] = true;
                  $atributos ['estiloMarco'] = '';
                  $atributos ["etiquetaObligatorio"] = false;
                  $atributos ['columnas'] = 2;
                  $atributos ['dobleLinea'] = 0;
                  $atributos ['tabIndex'] = $tab;
                  $atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
                  $atributos ['validar'] = '';

                  if (isset ( $_REQUEST [$esteCampo] )) {
                  $atributos ['valor'] = $_REQUEST [$esteCampo];
                  } else {
                  $atributos ['valor'] = '';
                  }
                  $atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
                  $atributos ['deshabilitado'] = true;
                  $atributos ['tamanno'] = 30;
                  $atributos ['maximoTamanno'] = '';
                  $atributos ['anchoEtiqueta'] = 100;
                  $atributos ['maximoTamanno'] = 10;

                  $tab ++;

                  // Aplica atributos globales al control
                  $atributos = array_merge ( $atributos, $atributosGlobales );

                  echo $this->miFormulario->campoCuadroTexto ( $atributos );
                  unset ( $atributos );

                  // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                  $esteCampo = 'fecha_registro';
                  $atributos ['id'] = $esteCampo;
                  $atributos ['nombre'] = $esteCampo;
                  $atributos ['tipo'] = 'fecha';
                  $atributos ['estilo'] = 'jqueryui';
                  $atributos ['marco'] = true;
                  $atributos ['estiloMarco'] = '';
                  $atributos ["etiquetaObligatorio"] = false;
                  $atributos ['columnas'] = 1;
                  $atributos ['dobleLinea'] = 0;
                  $atributos ['tabIndex'] = $tab;
                  $atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
                  $atributos ['validar'] = '';

                  if (isset ( $_REQUEST [$esteCampo] )) {
                  $atributos ['valor'] = $_REQUEST [$esteCampo];
                  } else {
                  $atributos ['valor'] = '';
                  }
                  $atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
                  $atributos ['deshabilitado'] = true;
                  $atributos ['tamanno'] = 8;
                  $atributos ['maximoTamanno'] = '';
                  $atributos ['anchoEtiqueta'] = 180;
                  $tab ++;

                  // Aplica atributos globales al control
                  $atributos = array_merge ( $atributos, $atributosGlobales );

                  echo $this->miFormulario->campoCuadroTexto ( $atributos );
                  unset ( $atributos );

                  // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                  $esteCampo = 'valorL_registro';
                  $atributos ['id'] = $esteCampo;
                  $atributos ['nombre'] = $esteCampo;
                  $atributos ['tipo'] = 'text';
                  $atributos ['estilo'] = 'jqueryui';
                  $atributos ['marco'] = true;
                  $atributos ['estiloMarco'] = '';
                  $atributos ["etiquetaObligatorio"] = false;
                  $atributos ['columnas'] = 105;
                  $atributos ['filas'] = 3;
                  $atributos ['dobleLinea'] = 0;
                  $atributos ['tabIndex'] = $tab;
                  $atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
                  // $atributos ['validar'] = 'required, minSize[1]';
                  $atributos ['deshabilitado'] = true;
                  $atributos ['tamanno'] = 20;
                  $atributos ['maximoTamanno'] = '';
                  $atributos ['anchoEtiqueta'] = 220;
                  if (isset ( $_REQUEST [$esteCampo] )) {
                  $atributos ['valor'] = $_REQUEST [$esteCampo];
                  } else {
                  $atributos ['valor'] = '';
                  }
                  $tab ++;

                  // Aplica atributos globales al control
                  $atributos = array_merge ( $atributos, $atributosGlobales );
                  echo $this->miFormulario->campoTextArea ( $atributos );
                  unset ( $atributos );

                  }
                  }

                  echo $this->miFormulario->agrupacion ( 'fin' );
                 */

                $esteCampo = "ordenadorGasto";
                $atributos ['id'] = $esteCampo;
                $atributos ['leyenda'] = $this->lenguaje->getCadena($esteCampo);
                echo $this->miFormulario->agrupacion('inicio', $atributos); {

                    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'asignacionOrdenador';
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['id'] = $esteCampo;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ["etiquetaObligatorio"] = true;
                    $atributos ['tab'] = $tab ++;
                    $atributos ['seleccion'] = - 1;
                    $atributos ['anchoEtiqueta'] = 160;
                    $atributos ['evento'] = '';
                    if (isset($_REQUEST [$esteCampo])) {
                        $atributos ['valor'] = $_REQUEST [$esteCampo];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $atributos ['deshabilitado'] = false;
                    $atributos ['columnas'] = 2;
                    $atributos ['tamanno'] = 1;
                    $atributos ['ajax_function'] = "";
                    $atributos ['ajax_control'] = $esteCampo;
                    $atributos ['estilo'] = "jqueryui";
                    $atributos ['validar'] = "required";
                    $atributos ['limitar'] = 1;
                    $atributos ['anchoCaja'] = 40;
                    $atributos ['miEvento'] = '';
                    $atributos ['cadena_sql'] = $this->miSql->getCadenaSql("tipoComprador");
                    $matrizItems = array(
                        array(
                            0,
                            ' '
                        )
                    );
                    $matrizItems = $esteRecursoDB->ejecutarAcceso($atributos ['cadena_sql'], "busqueda");
                    $atributos ['matrizItems'] = $matrizItems;
                    // $atributos['miniRegistro']=;
                    $atributos ['baseDatos'] = "inventarios";
                    // $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "clase_entrada" );
                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroLista($atributos);
                    unset($atributos);

                    // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'nombreOrdenador';
                    $atributos ['id'] = $esteCampo;
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['tipo'] = 'text';
                    $atributos ['estilo'] = 'jqueryui';
                    $atributos ['marco'] = true;
                    $atributos ['estiloMarco'] = '';
                    $atributos ["etiquetaObligatorio"] = false;
                    $atributos ['columnas'] = 2;
                    $atributos ['dobleLinea'] = 0;
                    $atributos ['tabIndex'] = $tab;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['validar'] = 'required, minSize[1],maxSize[2000]';

                    if (isset($_REQUEST [$esteCampo])) {
                        $atributos ['valor'] = $_REQUEST [$esteCampo];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = false;
                    $atributos ['tamanno'] = 30;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 120;
                    $tab ++;

                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);

                    $atributos ["id"] = "id_ordenador"; // No cambiar este nombre
                    $atributos ["tipo"] = "hidden";
                    $atributos ['estilo'] = '';
                    $atributos ["obligatorio"] = false;
                    $atributos ['marco'] = true;
                    $atributos ["etiqueta"] = "";
                    $atributos ["valor"] = '';
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);


                    $atributos ["id"] = "tipo_ordenador"; // No cambiar este nombre
                    $atributos ["tipo"] = "hidden";
                    $atributos ['estilo'] = '';
                    $atributos ["obligatorio"] = false;
                    $atributos ['marco'] = true;
                    $atributos ["etiqueta"] = "";
                    $atributos ["valor"] = '';
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);
                }

                echo $this->miFormulario->agrupacion('fin');
                unset($atributos);

                // $esteCampo = "contratista";
                // $atributos ['id'] = $esteCampo;
                // $atributos ['leyenda'] = $this->lenguaje->getCadena ( $esteCampo );
                // echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
                // {
                // $esteCampo = "vigencia_contratista";
                // $atributos ['nombre'] = $esteCampo;
                // $atributos ['id'] = $esteCampo;
                // $atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
                // $atributos ["etiquetaObligatorio"] = true;
                // $atributos ['tab'] = $tab ++;
                // $atributos ['seleccion'] = - 1;
                // $atributos ['anchoEtiqueta'] = 200;
                // $atributos ['evento'] = '';
                // if (isset ( $_REQUEST [$esteCampo] )) {
                // $atributos ['valor'] = $_REQUEST [$esteCampo];
                // } else {
                // $atributos ['valor'] = '';
                // }
                // $atributos ['deshabilitado'] = false;
                // $atributos ['columnas'] = 1;
                // $atributos ['tamanno'] = 1;
                // $atributos ['ajax_function'] = "";
                // $atributos ['ajax_control'] = $esteCampo;
                // $atributos ['estilo'] = "jqueryui";
                // $atributos ['validar'] = "required";
                // $atributos ['limitar'] = 1;
                // $atributos ['anchoCaja'] = 27;
                // $atributos ['miEvento'] = '';
                // $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "vigencia_contratista" );
                // $matrizItems = array (
                // array (
                // 0,
                // ' '
                // )
                // );
                // $matrizItems = $esteRecursoDBO->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
                // $atributos ['matrizItems'] = $matrizItems;
                // // $atributos['miniRegistro']=;
                // $atributos ['baseDatos'] = "sicapital";
                // // $atributos ['baseDatos'] = "inventarios";
                // // $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "clase_entrada" );
                // // Aplica atributos globales al control
                // $atributos = array_merge ( $atributos, $atributosGlobales );
                // echo $this->miFormulario->campoCuadroLista ( $atributos );
                // unset ( $atributos );
                // // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                // $esteCampo = 'nombreContratista';
                // $atributos ['nombre'] = $esteCampo;
                // $atributos ['id'] = $esteCampo;
                // $atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
                // $atributos ["etiquetaObligatorio"] = true;
                // $atributos ['tab'] = $tab ++;
                // $atributos ['seleccion'] = - 1;
                // $atributos ['anchoEtiqueta'] = 200;
                // $atributos ['evento'] = '';
                // if (isset ( $_REQUEST [$esteCampo] )) {
                // $atributos ['valor'] = $_REQUEST [$esteCampo];
                // } else {
                // $atributos ['valor'] = '';
                // }
                // $atributos ['deshabilitado'] = true;
                // $atributos ['columnas'] = 1;
                // $atributos ['tamanno'] = 1;
                // $atributos ['ajax_function'] = "";
                // $atributos ['ajax_control'] = $esteCampo;
                // $atributos ['estilo'] = "jqueryui";
                // $atributos ['validar'] = "required";
                // $atributos ['limitar'] = true;
                // $atributos ['anchoCaja'] = 100;
                // $atributos ['miEvento'] = '';
                // $atributos ['dobleLinea'] = 2;
                // // $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "constratistas" );
                // $matrizItems = array (
                // array (
                // 0,
                // ' '
                // )
                // );
                // // $matrizItems = $esteRecursoDBO->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
                // $atributos ['matrizItems'] = $matrizItems;
                // // $atributos['miniRegistro']=;
                // $atributos ['baseDatos'] = "inventarios";
                // // $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "clase_entrada" );
                // // Aplica atributos globales al control
                // $atributos = array_merge ( $atributos, $atributosGlobales );
                // echo $this->miFormulario->campoCuadroLista ( $atributos );
                // unset ( $atributos );
                // }
                // echo $this->miFormulario->agrupacion ( 'fin' );
                // unset ( $atributos );
                // }
                // echo $this->miFormulario->agrupacion ( 'fin' );
                // unset ( $atributos );
                // ------------------Division para los botones-------------------------
                $atributos ["id"] = "botones";
                $atributos ["estilo"] = "marcoBotones";
                echo $this->miFormulario->division("inicio", $atributos);

                // -----------------CONTROL: Botón ----------------------------------------------------------------
                $esteCampo = 'botonAceptar';
                $atributos ["id"] = $esteCampo;
                $atributos ["tabIndex"] = $tab;
                $atributos ["tipo"] = 'boton';
                // submit: no se coloca si se desea un tipo button genérico
                $atributos ['submit'] = 'true';
                $atributos ["estiloMarco"] = '';
                $atributos ["estiloBoton"] = 'jqueryui';
                // verificar: true para verificar el formulario antes de pasarlo al servidor.
                $atributos ["verificar"] = '';
                $atributos ["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
                $atributos ["valor"] = $this->lenguaje->getCadena($esteCampo);
                $atributos ['nombreFormulario'] = $esteBloque ['nombre'];
                $tab ++;

                // Aplica atributos globales al control
                $atributos = array_merge($atributos, $atributosGlobales);
                echo $this->miFormulario->campoBoton($atributos);
                // -----------------FIN CONTROL: Botón -----------------------------------------------------------

                echo $this->miFormulario->division('fin');

                echo $this->miFormulario->marcoAgrupacion('fin');

                // ---------------- FIN SECCION: Controles del Formulario -------------------------------------------
                // ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
                // Se debe declarar el mismo atributo de marco con que se inició el formulario.
            }

            // -----------------FIN CONTROL: Botón -----------------------------------------------------------
            // ------------------Fin Division para los botones-------------------------
            echo $this->miFormulario->division("fin");

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
            $valorCodificado .= "&opcion=registrarOrden";
            $valorCodificado .= "&seccion=" . $tiempo;
            $valorCodificado .= "&usuario=" . $_REQUEST['usuario'];
            /**
             * SARA permite que los nombres de los campos sean dinámicos.
             * Para ello utiliza la hora en que es creado el formulario para
             * codificar el nombre de cada campo. Si se utiliza esta técnica es necesario pasar dicho tiempo como una variable:
             * (a) invocando a la variable $_REQUEST ['tiempo'] que se ha declarado en ready.php o
             * (b) asociando el tiempo en que se está creando el formulario
             */
            $valorCodificado .= "&campoSeguro=" . $_REQUEST ['tiempo'];
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

            return true;
        }
    }

}

$miSeleccionador = new registrarForm($this->lenguaje, $this->miFormulario, $this->sql);

$miSeleccionador->miForm();
?>
