<?php
$propiedad = $propiedad ?? new stdClass();
$titulo = $propiedad->Titulo ?? '';
$precio = $propiedad->Precio ?? '';
$descripcion = $propiedad->Descripcion ?? '';
$habitaciones = $propiedad->Habitaciones ?? '';
$wc = $propiedad->wc ?? '';
$estacionamiento = $propiedad->Estacionamiento ?? '';
$vendedores_Id = $propiedad->Vendedores_Id ?? '';
?>

<fieldset>
    <legend>Informacion general de la propiedad</legend>

    <label for="titulo">Titulo</label>
    <input type="text" id="titulo" name="propiedad[titulo]" placeholder="Titulo propiedad" value="<?php echo s($titulo); ?>">

    <label for="precio">Precio</label>
    <input type="number" id="precio" name="propiedad[precio]" placeholder="Precio propiedad" value="<?php echo s($precio); ?>">

    <label for="imagenes">Imagen</label>
    <input type="file" id="imagenes" name="propiedad[imagenes]" accept="image/jpeg, image/png">
        <?php if(isset($propiedad->Imagenes)): ?>
            <img src="/imagenes/<?php echo $propiedad->Imagenes ?>" class="imgSmall">
        <?php endif; ?>

    <label for="descripcion">Descripcion</label>
    <textarea id="descripcion" name="propiedad[descripcion]"><?php echo s($descripcion); ?></textarea>
</fieldset>

<fieldset>
    <legend>Informacion de la propiedad</legend>

    <label for="habitaciones">Numero de Habitaciones</label>
    <input type="number" id="habitaciones" name="propiedad[habitaciones]" placeholder="Ej: 3" min="1" max="9" value="<?php echo s($habitaciones); ?>">

    <label for="wc">Numero de Banos</label>
    <input type="number" id="wc" name="propiedad[wc]" placeholder="Ej: 2" min="1" max="9" value="<?php echo s($wc); ?>">

    <label for="estacionamiento">Numero de estacionamientos</label>
    <input type="number" id="estacionamiento" name="propiedad[estacionamiento]" placeholder="Ej: 2" min="1" max="9" value="<?php echo s($estacionamiento); ?>">
</fieldset>

<fieldset>
    <legend>Vendedor</legend>
    <select name="propiedad[vendedores_Id]" id="vendedores_Id">
        <option value="" selected disabled>--Seleccione--</option>
        <?php if (isset($resultado)): ?>
            <?php while($vendedores = mysqli_fetch_assoc($resultado)): ?>
                <option <?php echo $vendedores_Id == $vendedores['Id'] ? 'selected' : ''; ?> value="<?php echo $vendedores['Id']; ?>"><?php echo s($vendedores['Nombre'].' '.$vendedores['Apellido']); ?></option>
            <?php endwhile; ?>
        <?php endif; ?>
    </select>
</fieldset>
