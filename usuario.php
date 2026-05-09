<?php

// Importar la conexión
require 'includes/app.php';
$db = conectarDB();

//email y password 
$email = "correo@correo.com";
$password = "123456";

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

var_dump($passwordHash);
//crear cuenta
$query = "INSERT INTO usuarios (email, password) VALUES ('$email', '$passwordHash')";

//agreagar a la base de datos
mysqli_query($db, $query);