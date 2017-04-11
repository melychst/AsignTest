<?php

	define("USER_NAME", "root");
	define("PASSWORD", "");
	define("DB_NAME", "asigntest");

	$arrVocablary = array( 'Cтать', 'Область', 'Місто', 'Хоббі', 'Компанія', 'Посада', 'Cтатус');
	$arrTables = array('users', 'vocablary', 'data_vocablary', 'relationship');

	$db = mysql_connect('localhost', USER_NAME, PASSWORD);
	$db_selected = mysql_select_db(DB_NAME, $db);


	foreach ($arrTables as $value) {
		switch ($value) {
			case 'users':
				if ( !checkTable(DB_NAME, $value) ) {
					createUsersTable($value);
				}
				break;
			case 'vocablary':
				if ( !checkTable(DB_NAME, $value) ) {
					createVocablaryTable($value, $arrVocablary);
				}	
				break;

			case 'data_vocablary':
				if ( !checkTable(DB_NAME, $value) ) {
					createDataVocablaryTable($value);
				}	
				break;

			case 'relationship':
				if ( !checkTable(DB_NAME, $value) ) {
					createRelationshipTable($value);
				}	
				break;

			default:
				break;
		}
	}

function createUsersTable($tableName) {
	$createUsers = "CREATE TABLE IF NOT EXISTS $tableName (
						id_user INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
						firstname VARCHAR(30) NOT NULL,
						lastname VARCHAR(30) NOT NULL,
						born DATE
					)";
	mysql_query( $createUsers );
}

function createVocablaryTable($tableName, $arrVocablary) {
	$createVocablary = "CREATE TABLE IF NOT EXISTS $tableName (
							id_vocablary INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
							name_vocablary VARCHAR(30) NOT NULL
						)";
	mysql_query( $createVocablary );

	for ($i=0; $i < count($arrVocablary) ; $i++) { 
		$isertValueVocablary = "INSERT INTO vocablary (name_vocablary) 
											VALUE ('$arrVocablary[$i]')";
		mysql_query($isertValueVocablary);
	}

}

function createDataVocablaryTable($tableName) {
	$createDataVocablary = "CREATE TABLE IF NOT EXISTS $tableName (
								id_data INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
								id_vocablary INT(6) NOT NULL,
								name_data VARCHAR(30)
							)";
	mysql_query( $createDataVocablary );
}

function createRelationshipTable($tableName) {
	$createRelationship = "CREATE TABLE IF NOT EXISTS $tableName (
							id_relation INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
							id_user INT(6) NOT NULL,
							id_data INT(6) NOT NULL
						)";
	mysql_query( $createRelationship );		
}


function checkTable($dataBase, $tableName) {
	$sql = mysql_query("SHOW TABLES FROM $dataBase like '$tableName';");
	if ( mysql_fetch_assoc($sql) ) {
		return true;
	}
	return false;
}


?>