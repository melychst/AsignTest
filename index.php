<?php

if ( $_FILES ) {
	copy( $_FILES['uploadFile']['tmp_name'], "uploads/".basename($_FILES['uploadFile']['name']) );
	$path = "uploads/".basename($_FILES['uploadFile']['name']);
	
	/*
		echo "<pre>";
		print_r(parse_excel_file( $path) );
		echo "</pre>";
	
*/
	$excelData = parse_excel_file($path);
	insert_users( $excelData );
} else {
	echo "Error";
}


/* 
 * Считывает данные из любого excel файла и созадет из них массив.
 * $filename (строка) путь к файлу от корня сервера
 */
function parse_excel_file( $filename ){
	require_once dirname(__FILE__) . '/lib/PHPExcel/Classes/PHPExcel.php';

	$result = array();

	$file_type = PHPExcel_IOFactory::identify( $filename );
	$objReader = PHPExcel_IOFactory::createReader( $file_type );
	$objPHPExcel = $objReader->load( $filename ); 
	$result = $objPHPExcel->getActiveSheet()->toArray();

	return $result;
}

function insert_users ($excelData) {

	foreach ($excelData as $key => $value) {
		$firstName = $value[0];
		$lastName = $value[1];
		$gender = $value[2];
		$region = $value[3];
		$city = $value[4];
		$hobby = explode(",", $value[8] );
		$company = $value[9];
		$position = $value[10];
		$status = $value[13];
		$curator = $value[14];

		$day = ($value[5] < 10 ) ?  "0".$value[5] : $value[5];
		$month = $value[6] < 10 ? "0".$value[6] : $value[6];
		$year = $value[7];
	}	

}



?>







<h1>Import file</h1>
<form enctype="multipart/form-data" method="post">
    <div>
    	<label for="upload-file"></label>
		<input id="upload-file" type="file" name="uploadFile">    	
    </div>
    <input type="submit" name="" value="Upload">
</form>