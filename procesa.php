<?php
$proceso = false;
$contenido = array();
$tipoArchivo = "";

function procesarArchivo($nombreArchivo)
{
    if (isset($_FILES[$nombreArchivo]) && $_FILES[$nombreArchivo]["size"] > 0) {
        $archivo = $_FILES[$nombreArchivo]["tmp_name"];
        $tamanio = $_FILES[$nombreArchivo]["size"];
        $tipo = $_FILES[$nombreArchivo]["type"];
        $nombre = $_FILES[$nombreArchivo]["name"];

        $archi = fopen($archivo, "rb");

        $contenido = array();

        while (($linea = fgets($archi)) !== false) {
            $campos = explode('&', $linea);
            $contenido[] = $campos;
        }

        fclose($archi);

        $tipoArchivo = $nombreArchivo;
        return $contenido;
    }
    return null;
}

if (isset($_POST["oc_Control"])) {
    $nombreArchivo1 = "txtArchi1";
    $nombreArchivo2 = "txtArchi2";

    $contenido1 = procesarArchivo($nombreArchivo1);
    $contenido2 = procesarArchivo($nombreArchivo2);

    if ($contenido1 !== null && $contenido2 !== null) {
        $proceso = true;
    }
}

function determinarTipoDato($valor)
{
    if (is_numeric($valor)) {
        return "Entero";
    } else {
        return "Cadena";
    }
}

function determinarUso($valor)
{
    if (is_numeric($valor)) {
        return "Cuantitativo";
    } else {
        return "Cualitativo";
    }
}

function determinarValor($valor)
{
    if (is_numeric($valor)) {
        $valor = (int) $valor;
        return "0 a $valor";
    } else {
        return "Variado";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    include_once("segmentos/encabe.inc");
    ?>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.16/css/jquery.dataTables.css">
    <title>Proceso de datos</title>
</head>

<body class="container">
    <header class="row">
        <?php
        include_once("segmentos/menu.inc");
        ?>
    </header>

    <main class="row">
        <div class="linea_sep">
            <h3>Procesando archivos.</h3>
            <br>
            <?php
            if (!$proceso) {
                echo '<div class="alert alert-danger" role="alert">';
                echo '  Los archivos no pueden ser procesados, verifique sus datos.....!';
                echo '</div>';
            } else {
                echo "<h4>Datos Generales.</h4>";

                echo "<table class='table table-bordered table-hover'>";
                echo "  <tr>";
                echo "      <td>Nombre</td>";
                echo "      <td>Tipo</td>";
                echo "      <td>Peso</td>";
                echo "      <td>Observaciones</td>";
                echo "  <tr>";
                echo "  <tr>";
                echo "      <td>" . $_FILES["txtArchi1"]["name"] . "</td>";
                echo "      <td>" . $_FILES["txtArchi1"]["type"] . "</td>";
                echo "      <td>" . number_format(($_FILES["txtArchi1"]["size"] / 1024) / 1024, 2, '.', ',') . " MB</td>";
                echo "      <td> " . count($contenido1) . "</td>";
                echo "  </tr>";
                echo "  <tr>";
                echo "      <td>" . $_FILES["txtArchi2"]["name"] . "</td>";
                echo "      <td>" . $_FILES["txtArchi2"]["type"] . "</td>";
                echo "      <td>" . number_format(($_FILES["txtArchi2"]["size"] / 1024) / 1024, 2, '.', ',') . " MB</td>";
                echo "      <td> " . count($contenido2) . "</td>";
                echo "  </tr>";

                echo "<br>";

                echo "<table id='tblDatos1' class='table table-bordered table-hover'>";
                echo "<h4>" . $_FILES["txtArchi1"]["name"] . "</h4>";
                echo "<thead><tr>";
                echo "<th>Campo</th>";
                echo "<th>Tipo</th>";
                echo "<th>Uso</th>";
                echo "<th>Valor</th>";
                echo "</tr></thead><tbody>";

                $campoIncremental1 = 1;

                foreach ($contenido1 as $i => $campos) {
                    foreach ($campos as $j => $valor) {
                        echo "<tr>";
                        echo "<td>Campo " . $campoIncremental1++ . "</td>";
                        echo "<td>" . determinarTipoDato($valor) . "</td>";
                        echo "<td>" . determinarUso($valor) . "</td>";
                        echo "<td>" . determinarValor($valor) . "</td>";
                        echo "</tr>";
                    }
                }

                echo "</tbody></table>";

                /*
                Esta parte es para leer el segundo archivo en la misma pagina pero lo pongo como comentario
                porque si no tarda mucho la pagina en carga y esa no es la idea, lo puede
                descomentar para ver si funciona correctamente

                echo "<br>";
                echo "<h4>" . $_FILES["txtArchi2"]["name"] . "</h4>";
                echo "<table id='tblDatos2' class='table table-bordered table-hover'>";
                echo "<thead><tr>";
                echo "<th>Campo</th>";
                echo "<th>Tipo</th>";
                echo "<th>Uso</th>";
                echo "<th>Valor</th>";
                echo "</tr></thead><tbody>";

                $campoIncremental2 = 1;

                foreach ($contenido2 as $i => $campos) {
                    foreach ($campos as $j => $valor) {
                        echo "<tr>";
                        echo "<td>Campo " . $campoIncremental2++ . "</td>";
                        echo "<td>" . determinarTipoDato($valor) . "</td>";
                        echo "<td>" . determinarUso($valor) . "</td>";
                        echo "<td>" . determinarValor($valor) . "</td>";
                        echo "</tr>";
                    }
                }

                echo "</tbody></table>";
                */

                echo "<br>";

                if ($proceso) {
                    echo "<h4>" . $_FILES["txtArchi1"]["name"] . "</h4>";

                    $resumen = array(
                        'M' => array(),
                        'F' => array()
                    );

                    $provincias = array('San José', 'Cartago', 'Alajuela', 'Heredia', 'Puntarenas', 'Guanacaste', "Limón");
                    foreach ($provincias as $provincia) {
                        $resumen['M'][$provincia] = 0;
                        $resumen['F'][$provincia] = 0;
                    }

                    foreach ($contenido1 as $campos) {
                        $genero = $campos[1];
                        $provincia = $campos[6];

                        if ($genero === 'M' || $genero === 'F') {
                            $resumen[$genero][$provincia]++;
                        }
                    }

                    echo "<table class='table table-bordered table-hover'>";
                    echo "<thead><tr><th>Género</th>";
                    foreach ($provincias as $provincia) {
                        echo "<th>$provincia</th>";
                    }
                    echo "<th>Total</th></tr></thead><tbody>";

                    $totalM = 0;
                    $totalF = 0;

                    foreach (['M', 'F'] as $genero) {
                        echo "<tr><td>$genero</td>";
                        $totalGenero = 0;
                        foreach ($provincias as $provincia) {
                            echo "<td>{$resumen[$genero][$provincia]}</td>";
                            $totalGenero += $resumen[$genero][$provincia];
                        }
                        echo "<td>$totalGenero</td>";

                        if ($genero === 'M') {
                            $totalM = $totalGenero;
                        } else {
                            $totalF = $totalGenero;
                        }
                    }

                    echo "<tr><td>Observaciones</td>";
                    foreach ($provincias as $provincia) {
                        echo "<td></td>";
                    }
                    echo "<td>" . ($totalM + $totalF) . "</td>";

                    echo "</tbody></table>";
                }

                echo "<br>";

                if ($proceso) {
                    echo "<h4>" . $_FILES["txtArchi1"]["name"] . "</h4>";

                    if ($totalM == 0) $totalM = 1;
                    if ($totalF == 0) $totalF = 1;

                    $factor = 100 / ($totalM + $totalF);

                    echo "<table class='table table-bordered table-hover'>";
                    echo "<thead><tr><th>Género</th>";
                    foreach ($provincias as $provincia) {
                        echo "<th>$provincia</th>";
                    }
                    echo "<th>Total</th></tr></thead><tbody>";

                    foreach (['M', 'F'] as $genero) {
                        echo "<tr><td>$genero</td>";
                        foreach ($provincias as $provincia) {
                            $porcentaje = ($resumen[$genero][$provincia] * $factor);
                            echo "<td>" . number_format($porcentaje, 2) . "%</td>";
                        }
                        $totalGenero = $genero === 'M' ? $totalM : $totalF;
                        $totalPorcentaje = $totalGenero * $factor;
                        echo "<td>" . number_format($totalPorcentaje, 2) . "%</td>";
                    }

                    echo "<tr><td>Observaciones</td>";
                    foreach ($provincias as $provincia) {
                        echo "<td></td>";
                    }
                    $total = ($totalM + $totalF) * $factor;
                    echo "<td>" . number_format($total, 2) . "%</td>";

                    echo "</tbody></table>";
                }

                echo "<br>";

                if ($proceso) {
                    echo "<h4>" . $_FILES["txtArchi2"]["name"] . "</h4>";

                    $resumen2 = array(
                        'M' => array(),
                        'F' => array()
                    );

                    $provincias = array('San José', 'Cartago', 'Alajuela', 'Heredia', 'Puntarenas', 'Guanacaste', "Limón");
                    foreach ($provincias as $provincia) {
                        $resumen2['M'][$provincia] = 0;
                        $resumen2['F'][$provincia] = 0;
                    }

                    foreach ($contenido2 as $campos) {
                        $genero = $campos[1];
                        $provincia = $campos[6];

                        if ($genero === 'M' || $genero === 'F') {
                            $resumen2[$genero][$provincia]++;
                        }
                    }

                    echo "<table class='table table-bordered table-hover'>";
                    echo "<thead><tr><th>Género</th>";
                    foreach ($provincias as $provincia) {
                        echo "<th>$provincia</th>";
                    }
                    echo "<th>Total</th></tr></thead><tbody>";

                    $totalM2 = 0;
                    $totalF2 = 0;

                    foreach (['M', 'F'] as $genero) {
                        echo "<tr><td>$genero</td>";
                        $totalGenero2 = 0;
                        foreach ($provincias as $provincia) {
                            echo "<td>{$resumen2[$genero][$provincia]}</td>";
                            $totalGenero2 += $resumen2[$genero][$provincia];
                        }
                        echo "<td>$totalGenero2</td>";

                        if ($genero === 'M') {
                            $totalM2 = $totalGenero2;
                        } else {
                            $totalF2 = $totalGenero2;
                        }
                    }

                    echo "<tr><td>Observaciones</td>";
                    foreach ($provincias as $provincia) {
                        echo "<td></td>";
                    }
                    echo "<td>" . ($totalM2 + $totalF2) . "</td>";

                    echo "</tbody></table>";
                }

                echo "<br>";

                if ($proceso) {
                    echo "<h4>" . $_FILES["txtArchi2"]["name"] . "</h4>";

                    if ($totalM2 == 0) $totalM2 = 1;
                    if ($totalF2 == 0) $totalF2 = 1;

                    $factor = 100 / ($totalM2 + $totalF2);

                    echo "<table class='table table-bordered table-hover'>";
                    echo "<thead><tr><th>Género</th>";
                    foreach ($provincias as $provincia) {
                        echo "<th>$provincia</th>";
                    }
                    echo "<th>Total</th></tr></thead><tbody>";

                    foreach (['M', 'F'] as $genero) {
                        echo "<tr><td>$genero</td>";
                        foreach ($provincias as $provincia) {
                            $porcentaje = ($resumen2[$genero][$provincia] * $factor);
                            echo "<td>" . number_format($porcentaje, 2) . "%</td>";
                        }
                        $totalGenero2 = $genero === 'M' ? $totalM2 : $totalF2;
                        $totalPorcentaje2 = $totalGenero2 * $factor;
                        echo "<td>" . number_format($totalPorcentaje2, 2) . "%</td>";
                    }

                    echo "<tr><td>Observaciones</td>";
                    foreach ($provincias as $provincia) {
                        echo "<td></td>";
                    }
                    $total2 = ($totalM2 + $totalF2) * $factor;
                    echo "<td>" . number_format($total2, 2) . "%</td>";

                    echo "</tbody></table>";
                }

                echo "<br>";

                echo "<div class='row'>";
                echo "<div class='col-md-6'>";
                echo "<h4>Género y Provincia " . $_FILES['txtArchi1']['name'] . "</h4>";
                echo "<div id='chart1'></div>";
                echo "</div>";
                echo "<div class='col-md-6'>";
                echo "<h4>Género y Provincia " . $_FILES['txtArchi2']['name'] . "</h4>";
                echo "<div id='chart2'></div>";
                echo "</div>";
                echo "</div>";

                echo "<script type='text/javascript' src='https://www.google.com/jsapi'></script>";
                echo "<script type='text/javascript'>";

                echo "google.load('visualization', '1', {packages:['corechart']});";
                echo "google.setOnLoadCallback(drawCharts);";

                echo "function drawCharts() {";

                // Datos del Archivo 1
                echo "var data1 = new google.visualization.DataTable();";
                echo "data1.addColumn('string', 'Provincia');";
                echo "data1.addColumn('number', 'Hombres');";
                echo "data1.addColumn('number', 'Mujeres');";
                echo "data1.addRows([";
                foreach ($provincias as $provincia) {
                    echo "['" . $provincia . "', " . $resumen['M'][$provincia] . ", " . $resumen['F'][$provincia] . "],";
                }
                echo "]);";

                // Datos del Archivo 2
                echo "var data2 = new google.visualization.DataTable();";
                echo "data2.addColumn('string', 'Provincia');";
                echo "data2.addColumn('number', 'Hombres');";
                echo "data2.addColumn('number', 'Mujeres');";
                echo "data2.addRows([";
                foreach ($provincias as $provincia) {
                    echo "['" . $provincia . "', " . $resumen2['M'][$provincia] . ", " . $resumen2['F'][$provincia] . "],";
                }
                echo "]);";

                echo "var options = {";
                echo "title: 'Cantidad de Personas por Genero en cada Provincia',";
                echo "width: 600,";
                echo "height: 500,";
                echo "hAxis: { title: 'Provincia' },";
                echo "vAxis: { title: 'Cantidad' }";
                echo "};";

                echo "var chart1 = new google.visualization.ColumnChart(document.getElementById('chart1'));";
                echo "chart1.draw(data1, options);";

                echo "var chart2 = new google.visualization.ColumnChart(document.getElementById('chart2'));";
                echo "chart2.draw(data2, options);";
                echo "}";
                echo "</script>";
            }
            ?>
        </div>
    </main>

    <footer class="row pie">
        <?php
        include_once("segmentos/pie.inc");
        ?>
    </footer>
    <script src="formatos/bootstrap/js/jquery-1.11.3.min.js"></script>
    <script src="formatos/bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#tblDatos1').dataTable({
                "language": {
                    "url": "dataTables.Spanish.lang"
                }
            });

            $('#tblDatos2').dataTable({
                "language": {
                    "url": "dataTables.Spanish.lang"
                }
            });
        });
    </script>
</body>

</html>