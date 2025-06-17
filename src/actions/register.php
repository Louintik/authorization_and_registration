<?php

require_once __DIR__.'/../helpers.php';

$avatarPath = null;

$name = $_POST['name'] ?? null;
$email = $_POST['email'] ?? null;
$password = $_POST['password'] ?? null;
$passwordConfirmation = $_POST['password_confirmation'] ?? null;
$avatar = $_FILES['avatar'] ?? null;

// Валидация

if (empty($name)){
	addValidationError('name', 'Неверное имя');
}

if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	addValidationError('email', 'Неверная почта');
}

if(empty($password)){
	addValidationError('password', 'Пароль пустой');
}

if($password !== $passwordConfirmation){
	addValidationError('password', 'Пароли не совпадают');
}

if (!empty($avatar)){
	$types = ['image/jpeg','image/png'];

	if (!in_array($avatar['type'], $types)){
		addValidationError('avatar', 'Изображение профиля имеет неверный тип');
	}

	if (($avatar['size'] / 1000000) >= 1){
		addValidationError('avatar', 'Изображение дожно быть меньше 1МБ');
	}
}

if (!empty($_SESSION['validation'])){
	addOldValue('name',$name);
	addOldValue('email',$email);
	redirect('/register.php');
}

if (!empty($avatar)){
	$avatarPath = uploadFile($avatar, 'avatar');
}

$pdo = getPDO();

$query = "INSERT INTO users (name,email,avatar,password) VALUES (:name,:email,:avatar,:password)";
$params = [
	'name' => $name,
	'email' => $email,
	'avatar' => $avatarPath,
	'password' => password_hash($password, PASSWORD_DEFAULT)
];
$stmt = $pdo->prepare($query);

try {
	$stmt->execute($params);
}
catch(\Exception $exception) {
	die($exception);
}

redirect('/index.php');

