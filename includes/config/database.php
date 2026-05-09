<?php
function conectarDB() : mysqli{
    $db = new mysqli('localhost', 'root', 'Admin.', 'bienesraices_crud');

    if(!$db){
        echo "Nose pudo conectar";
        exit;
    } 
    return $db;
}