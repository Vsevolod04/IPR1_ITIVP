<?php
$STATUS = "";
$MESSAGE = "";

// Проверка на заполнение всей формы
if (! (isset($_POST["customer_name"]) && isset($_POST["tel_num"]) && isset($_FILES["file"]) &&
    isset($_POST["print_format"]) && isset($_POST["copies"]) && isset($_POST["pickup_date"]))) {
    $STATUS = "Ошибка";
    $MESSAGE = "Обязательные поля не заполненны";
    exit();
}

$fio = $_POST["customer_name"]->trim();
$tel = $_POST["tel_num"]->trim();
$file = $_FILES["file"];
$format = $_POST["print_format"]->trim();
$copies = $_POST["copies"]->trim();
$date = date("Y-m-d", $_POST["pickup_date"]->trim());

//Проверка файла:
if ($file["size"] > 20971520) {  //20 МБ
    $STATUS = "Ошибка";
    $MESSAGE = "Размер файла превышает максимально допустимый (20 МБ)";
    exit();
} elseif (!in_array(strtolower($file["type"]), array("docx", "pdf", "png", "jpeg"))) {
    $STATUS = "Ошибка";
    $MESSAGE = "Неверный формат файла";
    exit();
}

// Проверка фио
if (strlen($fio) > 200) {
    $STATUS = "Ошибка";
    $MESSAGE = "Длина имени превышает допустимое значение";
    exit();
}

// Проверка номера
if (!preg_match("^((80|\+375)[\- ]?)?(\(?\d{2}\)?[\- ]?)?[\d\- ]{7,10}$", $fio)) {
    $STATUS = "Ошибка";
    $MESSAGE = "Неверный формат номера";
    exit();
}

//Проверка строки формата печати
if (strlen($format) > 200) {
    $STATUS = "Ошибка";
    $MESSAGE = "Строка формата печати превышает допустимое количество символов";
    exit();
}

//Проверка количества копий
if (!settype($copies, "integer")) {
    $STATUS = "Ошибка";
    $MESSAGE = "Указано неверное количество копий";
    exit();
} elseif ($copies > 500 || $copies < 1) {
    $STATUS = "Ошибка";
    $MESSAGE = "Указано неверное количество копий";
    exit();
}

//Проверка даты
if (!preg_match("^\d{4}\-\d{2}\-\d{2}$", $date)) {
    $STATUS = "Ошибка";
    $MESSAGE = "Неверный формат даты";
    exit();
} elseif (
    date("d.m.Y", $_POST["pickup_date"]->trim()) > strtotime('+14 days') ||
    date("d.m.Y", $_POST["pickup_date"]->trim()) < strtotime('+1 days')
) {
    $STATUS = "Ошибка";
    $MESSAGE = "Указана неверная дата (доступны даты с завтрашнего дня до +14 дней)";
    exit();
}



?>



<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Результат обработки формы</title>
</head>

<body>
    <h1><?php echo $STATUS ?></h1>
    <p><?php echo $MESSAGE ?></p>
    <a href="form.html">Вернуться к форме</a>
</body>

</html>