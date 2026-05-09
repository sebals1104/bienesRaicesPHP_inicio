<?php 
require 'includes/app.php';
incluirTemplate('header');
?>


    <main class="contenedor seccion">

        <h2>Casas y Depas en Venta</h2>
        <?php
        $limit = 3;
        include 'includes/templates/anuncios.php'; 
        ?>
    </main>

<?php incluirTemplate('footer') ?>

    <script src="build/js/bundle.min.js"></script>
</body>
</html>
<?php
//cerrar conexion
mysqli_close($db);
?>