<?php
ini_set('max_execution_time', 0);
?>

<script>
    $("#Archivo").change(function() {
        enviarFormulario("Pantallas/informe.php",$("#formulario"),"#pantalla");
    });

    $("#Exportar").click(function (){
        if($("#nombreBaseDeDatos").val() === ""){
            alert("Ingrese un nombre de la base de datos");
        } else {
            window.open("Pantallas/exportar.php?BDD=" + $("#nombreBaseDeDatos").val(),"_blank");
        }
    });
</script>

<div class="form-row">
    <div class="col-6">
        <form id="formulario" method="post">
            <label style="text-align: center; background-color: #64564e; color: white;" for="Archivo" class="form-control btn">Importar</label>
            <input id="Archivo" name="archivo" hidden type="file">
        </form>
    </div>

    <div class="col-6">
        <button id="Exportar" style="background-color: #92837A; color: white;" class="form-control btn">Exportar</button>
    </div>
</div>
<hr>

<div class="row">
    <form class="col-12">
        <div class="form-group form-control">
            <label><input type="text" class="form-control" id="nombreBaseDeDatos" value="">Nombre de la base de datos</label>
        </div>
    </form>
</div>