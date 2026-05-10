<?php 
use App\Propiedad;
require '../../includes/app.php';
estaAutenticado();

$db = conectarDB();

//validar la URL por ID valido
$id = $_GET['id'];
$id = filter_var($id, FILTER_VALIDATE_INT);

if(!$id){
    header('Location: /admin');
}

//obtener datos de la propiedad
$propiedad = Propiedad::find($id);
//Consultar los vendedores

$consulta = "SELECT * FROM vendedores";
$resultado = mysqli_query($db, $consulta);

//arreglo con mensajes de errores
$errores = [];

//ejecutar el codigo despues de que el usuario envia el formulario

if($_SERVER["REQUEST_METHOD"] === 'POST'){

$args = $_POST['propiedad'];
//asignar files hacia una variable (si existe)
$imagen = $_FILES['imagenes'] ?? null;

//Asignar y sanear valores recibidos por POST
if(!$propiedad->Titulo){
    $errores[] = "Debes añadir un titulo";
}

if(!$propiedad->Precio){
    $errores[] = "Debes añadir un precio";
}

if(strlen($propiedad->Descripcion) < 50){
    $errores[] = "Debes añadir una mas amplia descripcion";
}

if(!$propiedad->Habitaciones){
    $errores[] = "Debes añadir habitaciones";
}

if(!$propiedad->WC){
    $errores[] = "Debes añadir baños";
}

if(!$propiedad->Estacionamiento){
    $errores[] = "Debes añadir estacionamiento";
}

if(!$propiedad->Vendedores_Id){
    $errores[] = "Debes seleccionar un vendedor";
}

//validar por tamaño (2MB por defecto)
$medida = 2 * 1024 * 1024; // 2MB

// Si el formulario POST llega vacío, puede ser porque el archivo excede post_max_size
if($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && ($imagen === null || (isset($imagen['error']) && ($imagen['error'] === UPLOAD_ERR_INI_SIZE || $imagen['error'] === UPLOAD_ERR_FORM_SIZE)))){
    $errores[] = "Los datos del formulario no se recibieron. El archivo puede exceder el límite del servidor (post_max_size / upload_max_filesize).";
}

// Manejo de errores de subida y validaciones del archivo
if($imagen && isset($imagen['error']) && $imagen['error'] !== UPLOAD_ERR_NO_FILE){
    if($imagen['error'] === UPLOAD_ERR_INI_SIZE || $imagen['error'] === UPLOAD_ERR_FORM_SIZE){
        $errores[] = "La imagen excede el tamaño máximo permitido por el servidor.";
    } elseif($imagen['error'] !== UPLOAD_ERR_OK){
        $errores[] = "Error al subir la imagen (código {$imagen['error']}).";
    } else {
        if(isset($imagen['size']) && $imagen['size'] > $medida){
            $errores[] = "La imagen es muy pesada. Tamaño máximo: ".($medida/1024/1024)."MB";
        }
        // validar tipo mime si está disponible
        if(isset($imagen['tmp_name']) && $imagen['tmp_name']){
            $finfo = @finfo_open(FILEINFO_MIME_TYPE);
            if($finfo){
                $mime = finfo_file($finfo, $imagen['tmp_name']);
                finfo_close($finfo);
                $allowedTypes = ['image/jpeg','image/png'];
                if(!in_array($mime, $allowedTypes)){
                    $errores[] = "Formato de imagen no permitido. Use JPG o PNG.";
                }
            }
        }
    }
}


//revisar que el arreglo de errores este vacio
if(empty($errores)){
    //crear carpeta
    $carpetaImagenes = '../../imagenes/';

    if(!is_dir($carpetaImagenes)){
        mkdir($carpetaImagenes);
    }

    $nombreImagen = '';

    if($imagen && !empty($imagen['name'])) {
        //eliminar la imagen previa si existe
        if(!empty($propiedad->Imagenes) && file_exists($carpetaImagenes . $propiedad->Imagenes)){
            unlink($carpetaImagenes . $propiedad->Imagenes);
        }

        //generar un nombre unico con la misma extension
        $extension = pathinfo($imagen['name'], PATHINFO_EXTENSION);
        $nombreImagen = md5( uniqid( rand(), true ) ).".".$extension;

        move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreImagen );
    }else{
        $nombreImagen = $propiedad->Imagenes;
    }

    //actualizar la propiedad
    $query = "UPDATE propiedades SET Titulo = '{$propiedad->Titulo}', Precio = '{$propiedad->Precio}', Imagenes = '{$nombreImagen}', Descripcion = '{$propiedad->Descripcion}', Habitaciones = {$propiedad->Habitaciones}, WC = {$propiedad->WC}, Estacionamiento = {$propiedad->Estacionamiento}, Vendedores_Id = {$propiedad->Vendedores_Id} WHERE Id = {$propiedad->Id} ";

    $resultado = mysqli_query($db, $query);

    if($resultado){
        //redireccionar al usuario
        header('Location: /admin?mensaje=2');
    }
}
}




incluirTemplate('header');
?>

    <main class="contenedor seccion">
        <h1>Actualizar propiedad</h1>
        <a href="/admin/index.php" class="boton boton-verde">Admin</a>
        <?php foreach($errores as $error): ?>
        <div class="alerta error">
        <?php echo $error; ?>
        </div>
        <?php endforeach; ?>

        <form class="formulario" method="POST" enctype="multipart/form-data">
            <?php include '../../includes/templates/formulario_propiedades.php'; ?>
            <input type="submit" value="Actualizar propiedad" class="boton boton-verde">
        </form>
    </main>

<?php incluirTemplate('footer') ?>

    <script src="build/js/bundle.min.js"></script>
</body>
</html>