<?php

require_once __DIR__.'/../helpers.php';

checkGuest();

$email = $_POST['email'] ?? null;
$password = $_POST['password'] ?? null;

if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)){
	addOldValue('email', $email);
	addValidationError('email','Неверный формат электронной почты');
	setMessage('error', 'Ошибка валидации');
	redirect('/');
}

$user = findUser($email);

if (!$user) {
	setMessage('error', "Пользователь $email не найден");
	redirect('/');
}

if (!password_verify($password, $user['password'])){
	setMessage('error', "Неверный пароль");
	redirect('/');
}

$_SESSION['user']['id'] = $user['id'];

redirect('/home.php');