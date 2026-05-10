<?php

namespace App;

class Propiedad{
    //base de datos
    protected static $db;
    protected static $columnasDB = ['Id', 'Titulo', 'Precio', 'Imagenes', 'Descripcion', 'Habitaciones', 'WC', 'Estacionamiento', 'Creado', 'Vendedores_Id'];


    //Errores
    protected static $errores = [];

    public $Id;
    public $Titulo;
    public $Precio;
    public $Imagenes;
    public $Descripcion;
    public $Habitaciones;
    public $WC;
    public $Estacionamiento;
    public $Creado;
    public $Vendedores_Id;

    public function __construct($args = []){
        $this->Titulo = $args['titulo'] ?? $args['Titulo'] ?? '';
        $this->Precio = $args['precio'] ?? $args['Precio'] ?? '';
        $this->Imagenes = $args['imagenes'] ?? $args['Imagenes'] ?? '';
        $this->Descripcion = $args['descripcion'] ?? $args['Descripcion'] ?? '';
        $this->Habitaciones = $args['habitaciones'] ?? $args['Habitaciones'] ?? '';
        $this->WC = $args['wc'] ?? $args['WC'] ?? '';
        $this->Estacionamiento = $args['estacionamiento'] ?? $args['Estacionamiento'] ?? '';
        $this->Creado = date('Y/m/d');
        $this->Vendedores_Id = $args['Vendedores_Id'] ?? $args['vendedores_Id'] ?? '';
    }

    //definir la conexion a la base de datos
    public static function setDB($database){
        self::$db = $database;
    }

    public function guardar(){
        if(isset($this->Id)){
            //actualizar
            return $this->actualizar();
        }else{
            //crear
            return $this->crear();
        }
    }

    public function crear(){
        //sanitizar los datos
        $datos = $this->sanitizarDatos();

        //insertar en la base de datos
        $query = "INSERT INTO propiedades (";
        $query .= join(",", array_keys($datos));
        $query .= ") VALUES ('";
        $query .= join("','", array_values($datos));
        $query .= "')";

        $resultado = self::$db->query($query);
        return $resultado;
    }

    public function actualizar(){
        //sanitizar los datos
        $datos = $this->sanitizarDatos();

        $valores = [];
        foreach($datos as $key => $value){
            $valores[] = "{$key} = '{$value}'";
        }
        $query = "UPDATE propiedades SET ";
        $query .= join(', ', $valores);
        $query .= " WHERE Id = '" . self::$db->escape_string($this->Id) . "' ";
        $query .= " LIMIT 1";

        $resultado = self::$db->query($query);
        return $resultado;
    }

    public function atributos(){
        $atributos = [];
        foreach(self::$columnasDB as $columna){
            if(strtolower($columna) === 'id') continue;
            $atributos[$columna] = $this->$columna;
        }
        return $atributos;
    }


    public function sanitizarDatos(){
    $atributos = $this->atributos();
    $sanitizado = [];
    foreach($atributos as $key => $value){
            $sanitizado[$key] = self::$db->escape_string($value);
        }
    return $sanitizado;
    }

    public static function getErrores(){
        return self::$errores;
    }

    public function validar(){
        if(!$this->Titulo){
            self::$errores[] = "El titulo es obligatorio";
        }

        if(!$this->Precio){
            self::$errores[] = "El precio es obligatorio";
        }

        if(strlen($this->Descripcion) < 50){
            self::$errores[] = "La descripcion es obligatoria y debe tener al menos 50 caracteres";
        }

        if(!$this->Habitaciones){
            self::$errores[] = "El numero de habitaciones es obligatorio";
        }

        if(!$this->WC){
            self::$errores[] = "El numero de baños es obligatorio";
        }

        if(!$this->Estacionamiento){
            self::$errores[] = "El numero de estacionamientos es obligatorio";
        }

        if(!$this->Vendedores_Id){
            self::$errores[] = "Elige un vendedor";
        }

        if(!$this->Imagenes){
            self::$errores[] = "La imagen es obligatoria";
        }

        return self::$errores;
    }

    public function setImgen($imagen){
        if($imagen){
            $this->Imagenes = $imagen;
        }
    }

    //lista todas las propiedades
    public static function all(){
        $query = "SELECT * FROM propiedades";
        return self::consultarSQL($query);
    }
    //busca una propiedad por su id
    public static function find($id){
        $consulta = "SELECT * FROM propiedades WHERE Id = $id";
        $resultado = self::consultarSQL($consulta);
        return array_shift($resultado);
    }
    public static function consultarSQL($query){
        //Consultar la base de datos
        $resultado = self::$db->query($query);
        //iterar los resultados
        $array = [];
        while($registro = $resultado->fetch_assoc()){
            $array[] = self::crearObjeto($registro);
        }
        //liberar la memoria
        $resultado->free();
        //retornar los resultados
        return $array;
    }
    protected static function  crearObjeto($registro){
        $objeto = new self;
        foreach($registro as $key => $value){
            if(property_exists($objeto, $key)){
                $objeto->$key = $value;
            }
        }
    return $objeto;
    }
    public function sincronizar($args = []){
        foreach($args as $key => $value){
            if(property_exists($this, $key) && !is_null($value)){
                $this->$key = $value;
            }
        }
    }
}