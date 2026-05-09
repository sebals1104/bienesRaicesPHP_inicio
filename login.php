<?php  

    //conexión a la base de datos
    require 'includes/app.php';
    $db = conectarDB();
    //  autenticar usuario

    $errores = [];

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = mysqli_real_escape_string($db, filter_var( $_POST['email'], FILTER_VALIDATE_EMAIL));
    $password = mysqli_real_escape_string($db, $_POST['password']);

    if(!$email){
        $errores[] = "El email es obligatorio o no es válido";
    }
    if(!$password){
        $errores[] = "El password es obligatorio";
    }

    if (empty($errores)){
        //Usuario existe
        $query = "SELECT * FROM usuarios WHERE email = '$email'";
        $resultado = mysqli_query($db, $query);

        if($resultado->num_rows){
            //password es correcto
            $usuario = mysqli_fetch_assoc($resultado);
            //password correcto
            $auth = password_verify($password, $usuario['password']);
            
            if($auth){
                //usuario autenticado
                session_start();
                //Llenar el arreglo de la sesión
                $_SESSION['usuario'] = $usuario['email'];
                $_SESSION['login'] = true;
                
                header('Location: /admin');
            } else {
                $errores[] = "El password es incorrecto";
            }
        } else {
            $errores[] = "El usuario no existe";
        }
    }
}

    //Header
    incluirTemplate('header'); 
?>

    <main class="contenedor seccion">
        <h1>Login</h1>

        <?php foreach($errores as $error): ?>
            <div class="alerta error">
                <?php echo $error; ?>
            </div>
        <?php endforeach; ?>

        <form method="POST" class="formulario contenido-centrado">
            <fieldset>
                <legend>Email y password</legend>

                <label for="email">E-mail</label>
                <input type="email" name="email" placeholder="Tu Email" id="email">

                <label for="password">Password</label>
                <input type="password" name="password" placeholder="Tu Password" id="password">
            </fieldset>
            <input type="submit" value="Iniciar Sesión" class="boton boton-verde">
        </form>
    </main>
    <script src="build/js/bundle.min.js"></script>
</body>
</html>

<?php incluirTemplate('footer') ?>