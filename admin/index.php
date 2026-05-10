<?php 
require '../includes/app.php';
estaAutenticado();

use App\Propiedad;

//obtener las propiedades
$db = conectarDB();
$propiedades = Propiedad::all();

//mensaje condicional
$resultado = $_GET['resultado'] ?? null;

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    //Eliminar la propiedad
    $id = $_POST['id'];
    $id = filter_var($id, FILTER_VALIDATE_INT);

    if($id){
        //Eliminar archivo asociado antes del registro
        $queryImagen = "SELECT Imagenes FROM propiedades WHERE Id = {$id}";
        $resultadoImagen = mysqli_query($db, $queryImagen);
        if($resultadoImagen instanceof mysqli_result){
            $propiedad = mysqli_fetch_assoc($resultadoImagen);
            if($propiedad && !empty($propiedad['Imagenes'])){
                $rutaImagen = '../imagenes/' . $propiedad['Imagenes'];
                if(file_exists($rutaImagen)){
                    unlink($rutaImagen);
                }
            }
        }

        $query = "DELETE FROM propiedades WHERE Id = $id";
        $resultadoDelete = mysqli_query($db, $query);
        if($resultadoDelete){
            header('Location: /admin?resultado=3');
        }
    }
}

//Incluye un template

incluirTemplate('header'); 
?>

    <main class="contenedor seccion">
        <h1>Administrador de bienes raices</h1>
        <?php if( intval($resultado) === 1 ): ?>
            <p class="alerta exito">Anuncio creado correctamente </p>
        <?php  elseif( intval($resultado) === 2 ): ?>
            <p class="alerta exito">Anuncio actualizado correctamente </p>
        <?php  elseif( intval($resultado) === 3 ): ?>
            <p class="alerta exito">Anuncio eliminado correctamente </p>
        <?php  endif; ?>
        <a href="/admin/propiedades/crear.php" class="boton boton-verde">Nueva propiedad</a>

        <table class="propiedades">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Titulo</th>
                    <th>Imagen</th>
                    <th>Precio</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach($propiedades as $propiedad): ?>
                <tr>
                    <td><?php echo $propiedad->Id ?></td>
                    <td><?php echo $propiedad->Titulo ?></td>
                    <td><img src="/imagenes/<?php echo $propiedad->Imagenes ?>" class="imagenTabla"></td>
                    <td><?php echo $propiedad->Precio ?>$</td>
                    <td>
                        <form method="POST" class="w-100">
                            <input type="hidden" name="id" value="<?php echo $propiedad->Id; ?>">
                            <input type="submit" class="boton-rojo-block" value="Eliminar">
                        </form>

                        <a href="/admin/propiedades/actualizar.php?id=<?php echo $propiedad->Id ?>" class="boton-amarillo-block">Actualizar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
    <script src="build/js/bundle.min.js"></script>
</body>
</html>


<?php 
incluirTemplate('footer');
//cerrar la conexion

?>
