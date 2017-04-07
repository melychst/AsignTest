<?php
require_once("db.php");

define("GENDER_ID_VOCABLARY", 1);
define("REGION_ID_VOCABLARY", 2);
define("CITY_ID_VOCABLARY", 3);
define("HOBBY_ID_VOCABLARY", 4);
define("COMPANY_ID_VOCABLARY", 5);
define("POSITION_ID_VOCABLARY", 6);
define("STATUS_ID_VOCABLARY", 7);
define("LIMIT_GET_USERS", 5);

function validate_text_field($str) {
	$str = mb_convert_case($str, MB_CASE_TITLE, "UTF-8"); 
	return $str;
}


function parse_excel_file( $filename ){
	require_once dirname(__FILE__) . '/lib/PHPExcel/Classes/PHPExcel.php';

	$result = array();

	$file_type = PHPExcel_IOFactory::identify( $filename );
	$objReader = PHPExcel_IOFactory::createReader( $file_type );
	$objPHPExcel = $objReader->load( $filename ); 
	$result = $objPHPExcel->getActiveSheet()->toArray();

	return $result;
}

function checkUser($firstName, $lastName, $date) {

	$sql = "SELECT * FROM users WHERE firstname = '$firstName' AND lastname = '$lastName' AND born = '$date'";
	$res = mysql_fetch_assoc(mysql_query($sql));

	if ( $res ) {
		return false;
	}
	return true;
}


function insert_users ($excelData, $arrVocablary) {

	foreach ($excelData as $key => $value) {
		if ( $key != 0 ) {

			$firstName = validate_text_field($value[0]);
			$lastName = validate_text_field($value[1]);
			$gender = validate_text_field($value[2]);
			$region = validate_text_field($value[3]);
			$city = validate_text_field($value[4]);
			$hobby = explode(",", $value[8] );
			$company = validate_text_field($value[9]);
			$position = validate_text_field($value[10]);
			$status = validate_text_field($value[13]);
			//$curator = $value[14];

			$userData = array( $gender, $region, $city, $hobby, $company, $position, $status);

			$day = ($value[5] < 10 ) ?  "0".$value[5] : $value[5];
			$month = $value[6] < 10 ? "0".$value[6] : $value[6];
			$year = $value[7];
			$date = $year."-".$month."-".$day;
			
			if ( checkUser( $firstName, $lastName, $date) == true ) {

				$insertUser = "INSERT INTO users (firstname, lastname, born) 
										VALUE ('$firstName', '$lastName', '$date')";
				mysql_query($insertUser); // Insert into table users 
				$userID = mysql_insert_id();		

				for ($i=0; $i < count( $arrVocablary ); $i++) { 
					$sqlGetIdVocablary = "SELECT id_vocablary FROM vocablary WHERE name_vocablary = '$arrVocablary[$i]'";
					$idVocablaryRow = mysql_query($sqlGetIdVocablary);
					$idVocablaryRes = mysql_fetch_assoc($idVocablaryRow);
					$idVocablary = $idVocablaryRes['id_vocablary'];

					if ( is_array($userData[$i]) &&  ( count($userData[$i]) > 0) )  {
						$hobby = $userData[$i];
						for ($j=0; $j < count( $hobby ); $j++) { 
							$hobbyItem = validate_text_field($hobby[$j]);
							$insertUserDataVocablary = "INSERT INTO data_vocablary (id_vocablary, name_data) VALUE ('$idVocablary', '$hobbyItem')";
							mysql_query($insertUserDataVocablary); // Insert into table data_vocablary 
							$dataID = mysql_insert_id();
							insert_row_to_relationship($userID, $dataID); // Insert into table relationship
						}
					} else {
						$insertUserDataVocablary = "INSERT INTO data_vocablary (id_vocablary, name_data) VALUE ('$idVocablary', '$userData[$i]')";				
						mysql_query($insertUserDataVocablary); // Insert into table data_vocablary 
						$dataID = mysql_insert_id();
						insert_row_to_relationship($userID, $dataID); // Insert into table relationship
					}
				}			
			}			
		}
	}
	header("location: index.php");	
}


function insert_row_to_relationship($userID, $dataID) {
	$sql = "INSERT INTO relationship (id_user, id_data) VALUE ( $userID, $dataID ) ";
	mysql_query($sql);
}


function get_users( $page ) {
		$users = array();
		$relationship = array();
		$temparr = array();
		$rowFrom = $page*5;
		$sqlUsers = "SELECT * FROM users LIMIT ".$rowFrom.",5";
		$resUsers = mysql_query($sqlUsers);
	
		while ( $resUser = mysql_fetch_assoc($resUsers) ) {
			$userID = $resUser['id_user'];
			$temparr[$userID] = $resUser;
			$temparr[$userID]['data'] = array();

			$sqlRelations = "SELECT id_data FROM relationship WHERE id_user = $userID";
			$resRelations = mysql_query($sqlRelations);

			while ( $resRelation = mysql_fetch_assoc($resRelations) ) {
				$relationship[] = $resRelation['id_data'];
			}

			$in = "(".implode(',',$relationship).')';
			$sqlDataVocablary = "SELECT name_data, id_vocablary FROM data_vocablary WHERE id_data IN $in";
			$resDataVocablary = mysql_query($sqlDataVocablary);
			
			while ( $resData = mysql_fetch_assoc($resDataVocablary) ) {
				array_push( $temparr[$userID]['data'], $resData );
			}

			$users[] = $temparr[$userID];
			$relationship = array();

		}
		return $users;
}


function get_user_for_edit($id_user) {
		$user = array();
		$relationship = array();

		$sqlUser = "SELECT * FROM users WHERE id_user=$id_user";
		$resUser = mysql_query($sqlUser);
	
		$resUser = mysql_fetch_assoc($resUser);
		$user = $resUser;
		$user['data'] = array();

			$sqlRelations = "SELECT id_data FROM relationship WHERE id_user = $id_user";
			$resRelations = mysql_query($sqlRelations);

			while ( $resRelation = mysql_fetch_assoc($resRelations) ) {
				$relationship[] = $resRelation['id_data'];
			}

			$in = "(".implode(',',$relationship).")";
			$sqlDataVocablary = "SELECT name_data, id_vocablary FROM data_vocablary WHERE id_data IN $in";
			$resDataVocablary = mysql_query($sqlDataVocablary);
			
			while ( $resData = mysql_fetch_assoc($resDataVocablary) ) {
				array_push( $user['data'], $resData );
			}
		return $user;	
}

function save_user_to_db($user) {
	$id_user = $user['userID'];
	$firstName = validate_text_field($user['firstname']);
	$lastName = validate_text_field($user['lastname']);

	$gender = validate_text_field($user['gender']);
	$region = validate_text_field($user['region']);
	$city = validate_text_field($user['city']);
	$company = validate_text_field($user['company']);
	$position = validate_text_field($user['position']);
	$status = validate_text_field($user['status']);
	//$curator = validate_text_field($user['curator'];
	$born = $user['born'];

	if ( $user['hobby'] != '' ) {
		$hobby = explode(",", $user['hobby'] );
	} else {
		$hobby[] = $user['hobby'];
	}


	//print_r($user);
		$sqlUpdateUser = "UPDATE users SET firstname = '$firstName', lastname = '$lastName', born = '$born' WHERE id_user = $id_user";
		mysql_query($sqlUpdateUser);

		$sqlUserData = "SELECT id_data FROM relationship WHERE id_user = $id_user";
		$resUserDatas = mysql_query($sqlUserData);

			while ( $resUserData = mysql_fetch_assoc($resUserDatas) ) {
				$id_data = $resUserData['id_data'];
				
				$sqlNameData = "SELECT * FROM data_vocablary WHERE id_data = $id_data";
				$resNameDatas = mysql_query($sqlNameData);
				$resNameData = mysql_fetch_assoc($resNameDatas);

				switch ( $resNameData['id_vocablary'] ) {
					case GENDER_ID_VOCABLARY: 
						if ( $resNameData['name_data'] != $gender ) {
							$dataValue = $gender;
							$update	= true;
						}
						break;
					case REGION_ID_VOCABLARY: 
						if ( $resNameData['name_data'] != $region ) {
							$dataValue = $region;
							$update	= true;
						}					
						break;
					case CITY_ID_VOCABLARY: 
						if ( $resNameData['name_data'] != $city ) {
							$dataValue = $city;
							$update	= true;
						}
						break;
					case HOBBY_ID_VOCABLARY: 
							$sqlDeleteHobby = "DELETE FROM data_vocablary WHERE id_data = $id_data";
							mysql_query( $sqlDeleteHobby );

							$sqlDeleteRelation = "DELETE FROM relationship WHERE id_data = $id_data";
							mysql_query( $sqlDeleteRelation );

						break;
					case COMPANY_ID_VOCABLARY: 
						if ( $resNameData['name_data'] != $company ) {
							$dataValue = $company;
							$update	= true;
						}
						break;
					case POSITION_ID_VOCABLARY: 
						if ( $resNameData['name_data'] != $position ) {
							$dataValue = $position;
							$update	= true;
						}
						break;
					case STATUS_ID_VOCABLARY: 
						if ( $resNameData['name_data'] != $status ) {
							$dataValue = $status;
							$update	= true;
						}
						break;
					default : 
						$dataValue = "text";
					break;										 
				}

				if ( $update == true ) {
					$sqlUpdateUserData = "UPDATE data_vocablary SET name_data = '$dataValue' WHERE id_data = $id_data";
					mysql_query($sqlUpdateUserData);
					$update	= false;
				}
			}

			foreach ($hobby as $value) {
				$hobbyItem = validate_text_field($value);
				$sqlInsertNewHobby = "INSERT INTO data_vocablary (id_vocablary, name_data) VALUE ( ".HOBBY_ID_VOCABLARY.", '$hobbyItem')";
				mysql_query($sqlInsertNewHobby);
				$dataID = mysql_insert_id();
				insert_row_to_relationship( $id_user, $dataID );
			}

		return $sql;
}


?>