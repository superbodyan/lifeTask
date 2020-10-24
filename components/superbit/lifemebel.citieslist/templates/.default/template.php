<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var array $arResult */
/** @var string $componentPath */

?>

<form method="post" id="addCityForm">
    <label>
        <input class="form-control" name="cityName" placeholder="Название города" type="text">
    </label>
    <button id="addCityBtn" type="submit" class="btn btn-success add-city">Добавить город</button>
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

    $(".table").on("click", ".delete-city", function () {
        let requestData = {
            "cityId": $(this).attr("data-city-id"),
            "ajaxTask": "del"
        };
        let deleteItem = this.parentElement.parentElement;
        BX.ready(function () {
            BX.ajax.post(
                '<?= $componentPath . "/ajax.php"?>',
                requestData,
                function (data) {
                    let response = JSON.parse(data);
                    $(deleteItem).remove();
                    alert(response);
                }
            );
        });
    })

    $("#addCityBtn").on("click", function (e) {
        e.preventDefault();
        let formData = $("#addCityForm").serializeArray();
        let arDormData = [];
        $.each(formData, function () {
            arDormData[this.name] = this.value;
        });
        let requestData = {
            "formData": arDormData,
            "ajaxTask": "add"
        };
        BX.ready(function () {
            BX.ajax.post(
                '<?= $componentPath . "/ajax.php"?>',
                requestData,
                function (data) {
                    let response = JSON.parse(data);

                    if (response.length != 0) {
                        console.log("Элемент успешно добавлен");
                        $("#cityTable").append('<tr><th scope="row">' + response['ID'] + '</th><td>' + response['NAME'] + '</td><td>' + response['CODE'] + '</td><td>' + response['CACHE_TEST'] + '</td><td><button data-city-id="' + response['ID'] + '" class="btn btn-danger delete-city">Удалить</button></td>');
                    } else {
                        console.log(response);
                    }
                }
            )
        })
    })

</script>