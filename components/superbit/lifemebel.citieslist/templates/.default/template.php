<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var array $arResult */
/** @var string $componentPath */

?>

<form method="post" id="addCityForm">
    <label>
        <input class="form-control" name="cityName" placeholder="Название города" type="text">
    </label>
    <button type="submit" class="btn btn-success add-city">Добавить город</button>
</form>

<table class="table">
    <thead>
    <tr>
        <th scope="col">#</th>
        <th scope="col">Название города</th>
        <th scope="col">Код города</th>
        <th scope="col">CACHE_TEST</th>
        <th scope="col">Действие</th>
    </tr>
    </thead>
    <tbody id="cityTable">
    <? foreach ($arResult['ITEMS'] as $arItem): ?>
        <tr>
            <th scope="row"><?= $arItem['ID'] ?></th>
            <td><?= $arItem['NAME'] ?></td>
            <td><?= $arItem['CODE'] ?></td>
            <td><?= $arItem['CACHE_TEST'] ?></td>
            <td>
                <button data-city-id="<?= $arItem['ID'] ?>" class="btn btn-danger delete-city">Удалить</button>
            </td>
        </tr>
    <? endforeach; ?>
    </tbody>
</table>


<script>

    $(".table").on("click", ".delete-city", function () { // вешаем событие onClick на кнопку удаления города.
        let requestData = {
            "cityId": $(this).attr("data-city-id"), // получаем id удаляемого города
            "ajaxTask": "del" // передаем задание для Ajax
        };
        let deleteItem = this.parentElement.parentElement; // получаем элемент tr для удаления строки на frontend'e
        // используем BX.Ajax для общения с сервером
        BX.ready(function () {
            BX.ajax.post(
                '<?= $componentPath . "/ajax.php"?>', // получем путь к Ajax файлу
                requestData, // передаем массив значений серверу
                function (data) {
                    let response = JSON.parse(data); // парсим ответ от сервера
                    $(deleteItem).remove(); // удаляем элемент из frontend'a
                    alert(response); // выводим сообщение пользователю
                }
            );
        });
    })

    $(".add-city").on("click", function (e) { // вешаем событие на кнопку добавления города
        e.preventDefault(); // отменяем дефолтный submit
        let formData = $("#addCityForm").serializeArray(); // получаем значения из формы
        let arDormData = [];
        $.each(formData, function () {
            arDormData[this.name] = this.value; // форматируем значение формы в нормальный вид
        });
        let requestData = {
            "formData": arDormData, // передаем название города
            "ajaxTask": "add" // передаем задание для Ajax
        };
        // используем BX.Ajax для общения с сервером
        BX.ready(function () {
            BX.ajax.post(
                '<?= $componentPath . "/ajax.php"?>', // получем путь к Ajax файлу
                requestData, // передаем массив значений серверу
                function (data) {
                    let response = JSON.parse(data); // парсим ответ от сервера

                    if (response.length != 0) { // проверяем, пришел массив или сообщение об ошибке
                        alert("Элемент успешно добавлен"); // выводим сообщение пользовтелю
                        // добавляем в таблицу новую запись
                        $("#cityTable").append('<tr><th scope="row">' + response['ID'] + '</th><td>' + response['NAME'] + '</td><td>' + response['CODE'] + '</td><td>' + response['CACHE_TEST'] + '</td><td><button data-city-id="' + response['ID'] + '" class="btn btn-danger delete-city">Удалить</button></td>');
                    } else {
                        alert(response); // выводим сообщение пользовтелю
                    }
                }
            )
        })
    })

</script>