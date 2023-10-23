<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    include_once("segmentos/encabe.inc");
    ?>

    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>

    <script type="text/javascript">
        var datos1 = $.ajax({
            url: 'datos.php',
            type: 'post',
            dataType: 'json',
            async: false
        }).responseText;

        datos1 = JSON.parse(datos);

        var datos2 = $.ajax({
            url: 'datos.php',
            type: 'post',
            dataType: 'json',
            async: false
        }).responseText;

        datos2 = JSON.parse(datos);

        google.load("visualization", "1", {
            packages: ["corechart"]
        });

        google.setOnLoadCallback(creaGraficos);

        function creaGraficos() {
            // Gráfica 1
            var data1 = google.visualization.arrayToDataTable(datos1);

            var opciones1 = {
                title: 'Gráfica 1',
                width: 400,
                height: 300
            };

            var grafico1 = new google.visualization.PieChart(document.getElementById('grafica1'));
            grafico1.draw(data1, opciones1);

            // Gráfica 2
            var data2 = google.visualization.arrayToDataTable(datos2);

            var opciones2 = {
                title: 'Gráfica 2',
                width: 400,
                height: 300
            };

            var grafico2 = new google.visualization.PieChart(document.getElementById('grafica2'));
            grafico2.draw(data2, opciones2);
        }
    </script>
</head>

<body class="container">
    <header class="row">
        <?php
        include_once("segmentos/menu.inc");
        ?>
    </header>

    <main class="row">
        <div class="col-md-6">
            <h4>Gráfica 1</h4>
            <div id="grafica1"></div>
        </div>
        <div class="col-md-6">
            <h4>Gráfica 2</h4>
            <div id="grafica2"></div>
        </div>
    </main>

    <footer class="row pie">
        <?php
        include_once("segmentos/pie.inc");
        ?>
    </footer>

    <!-- jQuery necesario para los efectos de bootstrap -->
    <script src="formatos/bootstrap/js/jquery-1.11.3.min.js"></script>
    <script src="formatos/bootstrap/js/bootstrap.js"></script>
</body>

</html>