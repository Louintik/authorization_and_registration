<?php

require_once __DIR__.'/../helpers.php';

$avatarPath = null; //потенциально создаем чтобы не возникло ошибок

// Выносим данные из $_POST в отдельные переменные
$name = $_POST['name'] ?? null; //если в передаваемом массиве нет name занесет в нашу переменную значение null
$email = $_POST['email'] ?? null;
$password = $_POST['password'] ?? null;
$passwordConfirmation = $_POST['password_confirmation'] ?? null;
$avatar = $_FILES['avatar'] ?? null;

addOldValue('name',$name);
addOldValue('email',$email);

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
//	redirect('/register.php');
}

if (!empty($avatar)){
	$avatarPath = uploadFile($avatar, 'avatar');
}

