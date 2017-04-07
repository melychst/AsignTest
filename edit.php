<?php
require_once("function.php");

if ( isset($_GET['id_user']) && (int)$_GET['id_user'] ) {
	$id_user = (int)$_GET['id_user'];
	$user = get_user_for_edit( (int)$_GET['id_user'] );
	
	/*echo "<pre>";
	print_r($user);
	echo "</pre>";
	*/
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

}

if ( isset($_POST['userID']) ) {
	save_user_to_db($_POST);
	header("location: /index.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Edit user</title>
	<link rel="stylesheet" href="/css/styles.css">
	<link rel="stylesheet" href="/css/bootstrap/css/bootstrap.css">
</head>
<body>
	<div class="container">	
		<div class="row">
			<div class="col-md-12">
				<h1>Редагування користувача</h1>
				<form action="" class="form" method="POST">
					
					<div class="form-group">
					    <label for="firstname">Ім'я</label>
					    <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo $user['firstname']; ?>">
					</div>
					
					<div class="form-group">
					    <label for="lastname">Прізвище</label>
					    <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo $user['lastname']; ?>">
					</div>
					
					<div class="form-group">
					    <label for="born">Дата народження</label>
					    <input type="date" class="form-control" id="born" name="born" value="<?php echo $user['born']; ?>">
					</div>

					<div class="form-group">
					    <label for="gender">Стать</label>
					    <input type="text" class="form-control" id="gender" name="gender" value="<?php echo $gender; ?>">
					</div>

					<div class="form-group">
					    <label for="region">Область</label>
					    <input type="text" class="form-control" id="region" name="region" value="<?php echo $region; ?>">
					</div>

					<div class="form-group">
					    <label for="city">Місто</label>
					    <input type="text" class="form-control" id="city" name="city" value="<?php echo $city; ?>">
					</div>

					<div class="form-group">
					    <label for="hobby">Хоббі</label>
					    <input type="text" class="form-control" id="hobby" name="hobby" value="<?php echo implode(',',$hobby); ?>">
					</div>

					<div class="form-group">
					    <label for="company">Компанія</label>
					    <input type="text" class="form-control" id="company" name="company"  value="<?php echo $company; ?>">
					</div>

					<div class="form-group">
					    <label for="position">Посада</label>
					    <input type="text" class="form-control" id="position" name="position" value="<?php echo $position; ?>">
					</div>										

					<div class="form-group">
					    <label for="status">Статус</label>
					    <input type="text" class="form-control" id="status" name="status" value="<?php echo $status; ?>">
					</div>
					
					<input type="hidden" value='<?php echo $id_user ?>' name='userID'>

					<input type="submit" value="Save">
				</form>
			</div>
		</div>
	</div>
</body>
</html>