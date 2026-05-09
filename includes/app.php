<?php
require 'funciones.php';
require 'config/database.php';
require __DIR__.'/../vendor/autoload.php';

use App\Propiedad;

$db = conectarDB();

//conectar a la base de datos
Propiedad::setDB($db);