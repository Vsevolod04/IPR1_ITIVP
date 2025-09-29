<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Результат обработки формы</title>
</head>

<body class="mb-0 bg-light">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <?php
    require_once "config.php";

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
        if ($file["size"] > 15728640) {  //15 МБ
            $STATUS = "Ошибка";
            $MESSAGE = "Размер файла превышает максимально допустимый (15 МБ)";
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

        //Подключение к БД
        $conn = getConn();

        // Получение id заказа для именования файла
        $new_id = null;
        if ($conn != null) {
            $conn->query("SET information_schema_stats_expiry = 0"); //для обновления системной таблицы
            $data = $conn->query("SELECT AUTO_INCREMENT
                FROM information_schema.TABLES
                WHERE TABLE_SCHEMA = 'itivp_labs' AND TABLE_NAME = 'print_orders';");
            $new_id = $data->fetch(PDO::FETCH_ASSOC);
        } else {
            $STATUS = "Ошибка";
            $MESSAGE = "Нет доступа к БД";
            return;
        }

        // Попытка копирования файла на сервер 
        // Временная папка string(45) "C:\Users\cogog\AppData\Local\Temp\php2D67.tmp"
        $new_file_path = "./files/" . $new_id["AUTO_INCREMENT"] . "_" . basename($file["name"]);
        if (!move_uploaded_file($file["tmp_name"], $new_file_path)) {
            $STATUS = "Ошибка";
            $MESSAGE = "Не удалось обработать файл на сервере";
            return;
        };

        //Добавление данных в БД
        try {
            $stmt =  $conn->prepare("INSERT print_orders 
                (customer_name, tel_num, document_name, print_format, copies, pickup_date)
                VALUES  
                (?, ?, ?, ?, ?, ?)");

            $stmt->bindParam(1, $fio);
            $stmt->bindParam(2, $tel);
            $stmt->bindParam(3, $new_file_path);
            $stmt->bindParam(4, $format);
            $stmt->bindParam(5, $copies);
            $stmt->bindParam(6, $date);
            $stmt->execute();
        } catch (PDOException $e) {
            $STATUS = "Ошибка";
            $MESSAGE = $e->getMessage();
            return;
        }

        $STATUS = "Успех";
        $MESSAGE = "Данные добавлены. ID заявки: " . $new_id["AUTO_INCREMENT"];
    }

    // Проверка на заполнение всей формы
    if (! (isset($_POST["customer_name"]) && isset($_POST["tel_num"]) && isset($_FILES["file"]) &&
        isset($_POST["print_format"]) && isset($_POST["copies"]) && isset($_POST["pickup_date"]))) {
        $STATUS = "Ошибка";
        $MESSAGE = "Обязательные поля не заполненны";
    } elseif ($_FILES["file"]["error"] != 0) {
        $STATUS = "Ошибка";
        $MESSAGE = "Не удалось загрузить файл";
    } else {
        process_data();
    }
    ?>

    <main class="me-3 ms-3 min-vh-100">
        <h1><?php echo $STATUS ?></h1>
        <p><?php echo $MESSAGE ?></p>
        <a href="form.html">Вернуться к форме</a>
    </main>
    <footer class="sticky-bottom pt-3 pb-2" style="background-color: #dedede;">
        <p class="text-center text-body-secondary" style="font-size: 0.8rem;">Веб-сервис приёма заявок на печать<br>
            &copy; Sorkin Vsevolod
        </p>
    </footer>
</body>

</html>