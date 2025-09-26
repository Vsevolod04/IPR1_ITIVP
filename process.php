<?php
$STATUS = "";
$MESSAGE = "";      //Здесь будет обработка данных формы


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