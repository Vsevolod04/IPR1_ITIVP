<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Результат обработки формы</title>
</head>

<?php
$STATUS = "";
$MESSAGE = "";

function process_data()
{
    global $STATUS, $MESSAGE;

    $fio = trim($_POST["customer_name"]);
    $tel = trim($_POST["tel_num"]);
    $file = $_FILES["file"];
    $format = trim($_POST["print_format"]);
    $copies = trim($_POST["copies"]);
    $date = date("Y-m-d", strtotime($_POST["pickup_date"]));

    //Проверка файла:
    if ($file["size"] > 20971520) {  //20 МБ
        $STATUS = "Ошибка";
        $MESSAGE = "Размер файла превышает максимально допустимый (20 МБ)";
        return;
    } elseif (!in_array(strtolower($file["type"]), array(
        "application/msword",
        "application/pdf",
        "image/png",
        "image/jpeg",
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document"
    ))) {
        $STATUS = "Ошибка";
        $MESSAGE = "Неверный формат файла";
        return;
    }

    // Проверка фио
    if (strlen($fio) > 200 || strlen($fio) < 2) {
        $STATUS = "Ошибка";
        $MESSAGE = "Недопустимая длина ФИО";
        return;
    }

    // Проверка номера
    if (!preg_match("/^((80|\+375)[\- ]?)?(\(?\d{2}\)?[\- ]?)?[\d\- ]{7,10}$/", $tel)) {
        $STATUS = "Ошибка";
        $MESSAGE = "Неверный формат номера";
        return;
    }

    //Проверка строки формата печати
    if (strlen($format) > 200 || strlen($format) < 2) {
        $STATUS = "Ошибка";
        $MESSAGE = "Введите корректный формат печати";
        return;
    }

    //Проверка количества копий
    if (!settype($copies, "integer")) {
        $STATUS = "Ошибка";
        $MESSAGE = "Указано неверное количество копий";
        return;
    } elseif ($copies > 500 || $copies < 1) {
        $STATUS = "Ошибка";
        $MESSAGE = "Указано неверное количество копий";
        return;
    }

    //Проверка даты
    if (!preg_match("/^\d{4}\-\d{2}\-\d{2}$/", $date)) {
        $STATUS = "Ошибка";
        $MESSAGE = "Неверный формат даты";
        return;
    } elseif (
        strcmp(date("Y.m.d", strtotime($_POST["pickup_date"])), date("Y.m.d", strtotime('+14 days'))) > 0 ||
        strcmp(date("Y.m.d", strtotime($_POST["pickup_date"])), date("Y.m.d", strtotime('+1 days'))) < 0
    ) {
        $STATUS = "Ошибка";
        $MESSAGE = "Указана неверная дата (доступны даты с завтрашнего дня до +14 дней)";
        return;
    }

    //Добавить копирование файла 
    //Добавить вставку в БД

    $STATUS = "Успех";
    $MESSAGE = "Данные добавлены ";
}

// Проверка на заполнение всей формы
if (! (isset($_POST["customer_name"]) && isset($_POST["tel_num"]) && isset($_FILES["file"]) &&
    isset($_POST["print_format"]) && isset($_POST["copies"]) && isset($_POST["pickup_date"]))) {
    $STATUS = "Ошибка";
    $MESSAGE = "Обязательные поля не заполненны";
} else {
    process_data();
}




?>

<body>
    <h1><?php echo $STATUS ?></h1>
    <p><?php echo $MESSAGE ?></p>
    <a href="form.html">Вернуться к форме</a>
</body>

</html>