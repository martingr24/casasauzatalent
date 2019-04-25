<!DOCTYPE html>
<html>
<head>
    <title>Casa Sauza</title>
    <meta charset="UTF-8">

    <?php
        require "includes/jQuery.php";
        require "includes/bootstrap.php";
    ?>

    <script src="includes/cargas.js"></script>
    <script src="includes/libs/jquery.form.js"></script>
    <script src="includes/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="includes/jquery.dataTables.min.css"/>
    <link rel="stylesheet" href="css/style.css"/>
</head>
    <body>
        <p id="mensaje" hidden></p>
        <div class="progress" hidden>   
          <div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <script>
            $(function () {
                actualizarPantalla("Pantallas/inicio.php","#pantalla");
            });
        </script>

        <h1 class="col-12" align="center">Casa Sauza</h1>
        <hr>

        <div id="pantalla" class="container">
        </div>

    </body>
</html>
