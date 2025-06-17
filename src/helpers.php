<?php
session_start();

//храним здесь набор удобных функций

//нужно подключить конфигурационный файл чтобы ниже успешно инициализировать подключение к БД с помощью PDO
require_once __DIR__ . '/config.php';
function redirect(string $path) {
	header("Location: $path");
	die();
}

function addValidationError(string $fieldName, string $message) {
	$_SESSION['validation'][$fieldName] = $message;
}

function hasValidationError(string $fieldName): bool { //проверяем есть ли такое поле в массиве с ошибками (массив валидации формируется во время сессии)
	return isset($_SESSION['validation'][$fieldName]);
}

function validationErrorAttr(string $fieldName) { //орисовывать либо не отрисовывать атрибут с ошибкой
	echo isset($_SESSION['validation'][$fieldName]) ? 'aria-invalid="true"': '';
}

function validationErrorMessage(string $fieldName) { //выводит текст ошибки
	$message = $_SESSION['validation'][$fieldName] ?? '';
	unset($_SESSION['validation'][$fieldName]);
	echo $message;
	// ?? оператор означает что если поле $fieldName существует то
	// если не существует поля $fieldName то пустая строка
}

function addOldValue(string $key, mixed $value) { //сохраняет старые данные формы
	$_SESSION['old'][$key] = $value;
}

function old(string $key) { //возвращает старое значение по ключу c автоматическим очищением ключа
	$value = $_SESSION['old'][$key] ?? '';
	unset($_SESSION['old'][$key]);
	return $value;
}

function uploadFile(array $file, string $prefix = ''): string {

	$uploadPath = __DIR__.'../uploads';

	if(!is_dir($uploadPath)){
		mkdir($uploadPath, 0777, true);
	}

	$ext = pathinfo($file['name'], PATHINFO_EXTENSION);//получим расширение файла
	$fileName = $prefix . '_' . time() . ".$ext";

	if(!move_uploaded_file($file['tmp_name'], "$uploadPath/$fileName")){
		die('Ошибка во время загрузки файла');
	}

	return "uploads/$fileName";
}

function setMessage(string $key, string $message) {
	$_SESSION['message'][$key] = $message; //добавление сообщения в сессию под ключами ['message'][$key]
}

function hasMessage(string $key) { //проверяет есть ли какое-то сообщение сейчас в сессии
	return isset($_SESSION['message'][$key]);
}

function getMessage(string $key) {
	$message = $_SESSION['message'][$key] ?? '';
	unset($_SESSION['message'][$key]);
	return $message;
}

function getPDO(): PDO { //инициализируем подключение с помощью PDO
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