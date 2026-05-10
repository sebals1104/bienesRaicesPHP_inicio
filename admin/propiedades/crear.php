<?php 
require '../../includes/app.php';

use App\Propiedad;
use Intervention\Image\Drivers\Gd\Driver;
use App\ImageManagerCompat as Image;

estaAutenticado();

//importar la conexion

$db = conectarDB();

//Consultar los vendedores

$consulta = "SELECT * FROM vendedores";
$resultado = mysqli_query($db, $consulta);

//arreglo con mensajes de errores
$errores = Propiedad::getErrores();

//ejecutar el codigo despues de que el usuario envia el formulario

if($_SERVER["REQUEST_METHOD"] === 'POST'){

$propiedad = new Propiedad($_POST['propiedad']);

//generar un nombre unico para la imagen
$nombreImagen = md5(uniqid(rand(), true)).".jpg";

if($_FILES['propiedad']['tmp_name']['imagenes']){
    $manager = Image::usingDriver(Driver::class);
    $image = $manager->read($_FILES['propiedad']['tmp_name']['imagenes']);
    $image->cover(800, 600);
    $propiedad->setImgen($nombreImagen);
}

$errores = $propiedad->validar();



//revisar que el arreglo de errores este vacio
if(empty($errores)){

    if(!is_dir(CARPETA_IMAGENES)){
        mkdir(CARPETA_IMAGENES);
    }

    //guardar img
    $image->save(CARPETA_IMAGENES.$nombreImagen);

    $resultado = $propiedad->guardar();

    if($resultado){
        header('location: /admin?resultado=1');
    }
}
}


incluirTemplate('header');
?>

    <main class="contenedor seccion">
        <h1>Crear</h1>
        <a href="/admin/index.php" class="boton boton-verde">Admin</a>
        <?php foreach($errores as $error): ?>
        <div class="alerta error">
        <?php echo $error; ?>
        </div>
        <?php endforeach; ?>

        <form class="formulario" method="POST" action="/admin/propiedades/crear.php" enctype="multipart/form-data">
            <?php include '../../includes/templates/formulario_propiedades.php'; ?>
            <input type="submit" value="Crear Propiedad" class="boton boton-verde">
        </form>
    </main>

<?php incluirTemplate('footer') ?>

    <script src="build/js/bundle.min.js"></script>
</body>
</html>