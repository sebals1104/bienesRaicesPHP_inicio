<?php 
use App\Propiedad;
use Intervention\Image\Drivers\Gd\Driver;
use App\ImageManagerCompat as Image;
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
$errores = Propiedad::getErrores();

//ejecutar el codigo despues de que el usuario envia el formulario

if($_SERVER["REQUEST_METHOD"] === 'POST'){

//Asignar los atributos
$args = $_POST['propiedad'];
$propiedad->sincronizar($args);

//validacion
$errores = $propiedad->validar();

//subida de archivos
$imagen = $_FILES['propiedad'] ?? [];
$imagenTmp = $imagen['tmp_name']['imagenes'] ?? '';

if($imagenTmp){
    //generar un nombre unico
    $nombreImagen = md5(uniqid(rand(), true)).".jpg";

    $manager = Image::usingDriver(Driver::class);
    $image = $manager->read($imagenTmp);
    $image->cover(800, 600);

    $propiedad->setImagen($nombreImagen);
}

//revisar que el arreglo de errores este vacio
if(empty($errores)){
    if($imagenTmp){
        if(!is_dir(CARPETA_IMAGENES)){
            mkdir(CARPETA_IMAGENES);
        }

        $image->save(CARPETA_IMAGENES.$nombreImagen);

    }

    $resultado = $propiedad->guardar();
    if($resultado){
        header('Location: /admin');
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