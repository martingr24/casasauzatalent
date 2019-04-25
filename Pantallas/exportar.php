<?php
ini_set('max_execution_time', 0);
require_once "../Classes/PHPExcel.php";

try{
    $conn = new PDO("sqlsrv:Server=localhost;Database=" . $_GET["BDD"], 'sa', '1234');
    $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
} catch (Exception $e) {
    die("Coneccion fallida");
}

$objetoPHPExcel = new PHPExcel();

$contadorHojas = 0;
$query = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES;";
$sql = $conn->prepare($query);
$sql->execute();
$tablas = $sql->fetchAll();
foreach ($tablas as $tabla) {
	$hojaActual = $objetoPHPExcel->getSheet($contadorHojas)->setTitle($tabla["TABLE_NAME"]);
	$query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ?;";
	$sql = $conn->prepare($query);
	$sql->bindValue(1, $tabla["TABLE_NAME"]);
	$sql->execute();
	$columnas = $sql->fetchAll();
	$contadorRenglones = 1;
	$contadorColumnas = 0;
	$columnasString = "";
	foreach ($columnas as $columna) {
		$columnasString .= $columna["COLUMN_NAME"] . ",";
		$celda = $hojaActual->getCellByColumnAndRow($contadorColumnas,$contadorRenglones);
		$celda->setValue($columna["COLUMN_NAME"]);
		$contadorColumnas++;
	}
	$columnasString = rtrim($columnasString,",");
	$columnasArray = explode(",", $columnasString);
	$query = "SELECT " . $columnasString . " FROM " . $tabla["TABLE_NAME"] . ";";
	$sql = $conn->prepare($query);
	$sql->execute();
	$registros = $sql->fetchAll();
	$contadorRenglones++;
	foreach ($registros as $registro) {
		$contadorColumnas = 0;
		foreach($columnasArray as $nombreColumna){
			$celda = $hojaActual->getCellByColumnAndRow($contadorColumnas,$contadorRenglones);
			$celda->setValue($registro[$nombreColumna]);
			$contadorColumnas++;
		}
		$contadorRenglones++;
	}

	$contadorHojas++;
	$objetoPHPExcel->createSheet($contadorHojas);
}

$objetoPHPExcel->removeSheetByIndex($contadorHojas);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $_GET["BDD"] . '.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objetoPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>
