<?php
session_start();

require_once __DIR__ . '/config.php';
function redirect(string $path) {
	header("Location: $path");
	die();
}

function addValidationError(string $fieldName, string $message) {
	$_SESSION['validation'][$fieldName] = $message;
}

function hasValidationError(string $fieldName): bool {
	return isset($_SESSION['validation'][$fieldName]);
}

function validationErrorAttr(string $fieldName):string {
	return isset($_SESSION['validation'][$fieldName]) ? 'aria-invalid="true"': '';
}

function validationErrorMessage(string $fieldName) {
	$message = $_SESSION['validation'][$fieldName] ?? '';
	unset($_SESSION['validation'][$fieldName]);
	return $message;
}

function addOldValue(string $key, mixed $value) {
	$_SESSION['old'][$key] = $value;
}

function old(string $key) {
	$value = $_SESSION['old'][$key] ?? '';
	unset($_SESSION['old'][$key]);
	return $value;
}

function uploadFile(array $file, string $prefix = ''): string {

	$uploadPath = __DIR__ . '/../uploads';

	if(!is_dir($uploadPath)){
		mkdir($uploadPath, 0777, true);
	}

	$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
	$fileName = $prefix . '_' . time() . ".$ext";

	if(!move_uploaded_file($file['tmp_name'], "$uploadPath/$fileName")){
		die('Ошибка во время загрузки файла');
	}

	return "uploads/$fileName";
}

function setMessage(string $key, string $message) {
	$_SESSION['message'][$key] = $message;
}

function hasMessage(string $key) {
	return isset($_SESSION['message'][$key]);
}

function getMessage(string $key) {
	$message = $_SESSION['message'][$key] ?? '';
	unset($_SESSION['message'][$key]);
	return $message;
}

function getPDO(): PDO {
	try {
		return new \PDO('mysql:host='.DB_HOST.';port=' . DB_PORT . ';charset=utf8;dbname=' . DB_NAME,DB_USERNAME, DB_PASSWORD);
	} catch(\PDOException $exception) {
		die("Connection error: {$exception->getMessage()}");
	}
}

function findUser(string $email): array|bool {
	$pdo = getPDO();
	$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
	$stmt->execute(['email' => $email]);
	return $stmt->fetch( \PDO:: FETCH_ASSOC);
}

function currentUser(): array| false {
	$pdo = getPDO();
	if(!isset($_SESSION['user'])) {
		return false;
	}
	$userId = $_SESSION['user']['id'] ?? null;
	$stmt = $pdo->prepare(query: "SELECT * FROM users WHERE id = :id");
	$stmt->execute(['id' => $userId]);
	return $stmt->fetch(mode: \PDO:: FETCH_ASSOC);
}

function logout() {
	unset($_SESSION['user']['id']);
	redirect('/');
}

function checkAuth(): void {
	if(!isset($_SESSION['user']['id'])){
		redirect('/');
	}
}

function checkGuest(): void {
	if(isset($_SESSION['user']['id'])){
		redirect('/home.php');
	}
}

