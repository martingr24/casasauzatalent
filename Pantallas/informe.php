<?php
ini_set('max_execution_time', 0);
require_once "../Classes/PHPExcel.php";
require '../vendor/autoload.php';
use Spipu\Html2Pdf\Html2Pdf;

if(isset($_POST)){
    $nombreArchivo = $_FILES["archivo"]["tmp_name"];
    $lectorExcel = PHPExcel_IOFactory::createReaderForFile($nombreArchivo);
    $objetoExcel = $lectorExcel->load($nombreArchivo);
    $infoArchivo = pathinfo($_FILES["archivo"]["name"]);
    $nombreArchivo = $infoArchivo["filename"];

    try{
        $conn = new PDO("sqlsrv:Server=localhost;Database=" . $nombreArchivo, 'sa', '1234');   
        $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    } catch (Exception $e) {
        die("Coneccion fallida");
    }
    $erroneos = "";
    $completados = "";

    foreach ($objetoExcel->getWorksheetIterator() as $worksheet) {
        $contRow = 1;
        $columnasArray = array();
        foreach ($worksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $valoresArray = array();
            foreach ($cellIterator as $cell) {
                if (!is_null($cell)) {
                    if($contRow == 1){
                        if($cell->getCalculatedValue() !== null){
                            array_push($columnasArray, $cell->getCalculatedValue());
                        }
                    } else {
                        array_push($valoresArray, $cell->getCalculatedValue());
                    }
                }
            }
            
            $signos = "";
            $columnas = "";
            foreach ($columnasArray as $columna) {
                $columnas .= $columna . ",";
                $signos .= "?,";
            }
            $signos = rtrim($signos,",");
            $columnas = rtrim($columnas,",");
            
            if(count($columnasArray) > 0 && count($valoresArray) > 0){
                $query = "INSERT INTO " . $worksheet->getTitle() . " (" . $columnas . ") VALUES (" . $signos . ");";

                $sql = $conn->prepare($query);
                $i = 1;
                foreach ($valoresArray as $param) {
                    $sql->bindValue($i, $param);
                    $i++;
                }
                try{
                    $sql->execute();
                    $valoresCompletados = "";
                    foreach ($valoresArray as $param) {
                        $valoresCompletados .= $param . ",";
                    }
                    $valoresCompletados = rtrim($valoresCompletados,",");
                    $completados .= "<p>Hoja: " . $worksheet->getTitle() . ", renglón: " . $contRow . ", datos: " . $valoresCompletados . "</p><br/>";
                } catch (PDOException $e){
                    $valoresErroneos = "";
                    foreach ($valoresArray as $param) {
                        $valoresErroneos .= $param . ",";
                    }
                    $valoresErroneos = rtrim($valoresErroneos,",");
                    $erroneos .= "<p>Hoja: " . $worksheet->getTitle() . ", renglón: " . $contRow . ", datos: " . $valoresErroneos . "</p><br/>";
                }
            }
            $contRow++;
        }
    }
    $html = "<style type='text/css'>
		<!--
		    table.page_header {width: 100%; border: none; background-color: #92837A; border-bottom: solid 1mm #64564e; padding: 2mm }
		    table.page_footer {width: 100%; border: none; background-color: #92837A; border-top: solid 1mm #64564e; padding: 2mm}
		    h2 {color: #000055}
		    h3 {color: #000077}
		    
		    div.standard
		    {
		        padding-left: 5mm;
		    }
		-->
		</style>
		<page backtop='14mm' backbottom='14mm' backleft='10mm' backright='10mm' style='font-size: 12pt'>
		    <page_header>
		        <table class='page_header'>
		            <tr>
		                <td style='width: 100%; text-align: left'>
		                    Informe
		                </td>
		            </tr>
		        </table>
		    </page_header>
		    <page_footer>
		        <table class='page_footer'>
		            <tr>
		                <td style='width: 100%; text-align: right'>
		                    pág. [[page_cu]]/[[page_nb]]
		                </td>
		            </tr>
		        </table>
		    </page_footer>
		    <bookmark title='Índice' level='0' ></bookmark>
		</page>
		<page pageset='old'>
		    <div class='standard'>
		    <bookmark title='1. DATOS ALMACENADOS CON ÉXITO' level='0' ></bookmark><h1 style='color: green;'>DATOS ALMACENADOS CON ÉXITO</h1>
		    </div>
		    " . $completados . "
		</page>
		<page pageset='old'>
		    <bookmark title='2. DATOS ALMACENADOS SIN ÉXITO' level='0' ></bookmark><h1 style='color: red;'>DATOS ALMACENADOS SIN ÉXITO</h1>
		    " . $erroneos . "
		</page>";
	$html2pdf = new Spipu\Html2Pdf\Html2Pdf('P','A4','en');
	$html2pdf->writeHTML($html);
	$html2pdf->createIndex('Índice', 25, 12, false, true, 1);
	$html2pdf->output(__DIR__ . '/informe.pdf', 'F');
}
?>

<img src="Pantallas/done.png" class="mx-auto d-block" height="100px" width="100px">
<h3 style="text-align: center;">Informe creado</h3>