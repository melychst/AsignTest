<?php

require_once("function.php");

if ( $_FILES ) {
	copy( $_FILES['uploadFile']['tmp_name'], "uploads/".basename($_FILES['uploadFile']['name']) );
	$path = "uploads/".basename($_FILES['uploadFile']['name']);
	$excelData = parse_excel_file($path);
	insert_users( $excelData, $arrVocablary );
}

if ( (isset($_GET['page']) && ($_GET['page'] != 1) ) ) {
		$page = $_GET['page'] - 1;
} else {
	$page = 0;
}

/*	
		echo "<pre>";
		print_r(get_users());
		echo "</pre>";
	*/

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>User</title>
	<link rel="stylesheet" href="/css/styles.css">
	<link rel="stylesheet" href="/css/bootstrap/css/bootstrap.css">
</head>
<body>
	

	<div class="container">
		<div class="row">
			<div class="col-md-12">
			<h1>Import file</h1>
				<form enctype="multipart/form-data" method="post" class="form">
				    <div>
				    	<label for="upload-file"></label>
						<input id="upload-file" type="file" name="uploadFile">    	
				    </div>
				    <input class="btn btn-default" type="submit" name="" value="Upload">
				</form>				
			</div>
		</div>
	</div>
	
	<div class="container">
		<div class="row">
			<div class="col-md-12">
			<h1>Users</h1>
				<table class="table table-striped">
					<thead>
						<tr>
							<td>№п/п</td>
							<td>Ім'я</td>
							<td>Прізвище</td>
							<td>Дата нар</td>
							<td>Стать</td>
							<td>Область</td>
							<td>Місто</td>
							<td>Хоббі</td>
							<td>Компанія</td>
							<td>Посада</td>
							<td>Статус</td>
							<td>Edit</td>
						</tr>
					</thead>
					<tbody>
					<?php 
						$users = get_users( $page ); 
							if ( $users ) {
								$i=1;
								foreach ($users as $keyUser => $user) {
						?>
						<tr>
							<td><?php echo $i++; ?></td>
							<td><?php echo $user['firstname']; ?></td>
							<td><?php echo $user['lastname']; ?></td>
							<td><?php echo $user['born']; ?></td>
							<?php 
								foreach ($user['data'] as $keyData => $value) {
									switch ( $value['id_vocablary'] ) {
										case GENDER_ID_VOCABLARY: $gender = $value['name_data'];
																							break;
										case REGION_ID_VOCABLARY: $region = $value['name_data'];
																							break;
										case CITY_ID_VOCABLARY: $city = $value['name_data'];
																							break;
										case HOBBY_ID_VOCABLARY: $hobby[] = $value['name_data'];
																							break;
										case COMPANY_ID_VOCABLARY: $company = $value['name_data'];
																							break;
										case POSITION_ID_VOCABLARY: $position = $value['name_data'];
																							break;
										case STATUS_ID_VOCABLARY: $status = $value['name_data'];
																							break;
										default : break;																																																												 
									} 
								}
							?>
							<td><?php echo $gender; ?></td>
							<td><?php echo $region; ?></td>
							<td><?php echo $city; ?></td>
							<td><?php echo implode(', ',$hobby); ?></td>
							<td><?php echo $company; ?></td>
							<td><?php echo $position; ?></td>
							<td><?php echo $status; ?></td>
							<td><a href="<?php echo 'edit.php/?id_user='.$user['id_user'] ?>">Edit</a></td>
						</tr>
					<?php
						$hobby = '';
								}
							}
					?>			
					</tbody>
				</table>
				<div class="pagination">
					<?php 
						$res = mysql_query("SELECT count(*) FROM users");
						$row=mysql_fetch_row($res);
						$userCount=$row[0];

						$pageCount = ceil( $userCount/5 );
					
						for ($i=1; $i < $pageCount + 1; $i++) { 
							echo "<a href='/?page=".$i."'>".$i."</a>";
						}
					?>
				</div>			
			</div>
		</div>
	</div>



</body>
</html>

