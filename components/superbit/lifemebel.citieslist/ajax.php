<?php
require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;

Loader::includeModule("iblock");

CAjaxCity::AjaxController(['AJAX_TASK' => $_POST['ajaxTask'], "CITY_ID" => $_POST['cityId'], "CITY_NAME" => $_POST['formData']['cityName']]);

/**
 * Class CAjaxCity
 * Класс для работы с AJAX
 */
class CAjaxCity
{
    /**
     * @param $params
     * берем значение $_POST['ajaxTask] и определяем вызываем необходимую функцию
     */
    public static function AjaxController($params)
    {
        switch ($params['AJAX_TASK']) {
            case "del":
                echo self::deleteCity($params['CITY_ID']);
                break;
            case "add":
                echo self::addCity($params['CITY_NAME']);
                break;
        }
    }

    /**
     * @param null $cityName
     * @return false|string
     * добавляем новый город
     * у каждого города есть символьный код, который не может повторяться.
     */
    private static function addCity($cityName = null)
    {
        if (is_null($cityName))
            return json_encode("NO INPUT DATA");

        $el = new CIBlockElement;

        $cityCode = CUtil::translit($cityName, "ru");

        $arFields = [
            "IBLOCK_ID" => 1,
            "NAME" => $cityName,
            "CODE" => $cityCode
        ];
        if ($CITY_ID = $el->Add($arFields))
            return json_encode(["ID" => $CITY_ID, "CODE" => $cityCode, "NAME" => $cityName, "CACHE_TEST" => Date("Y-m-d:h-m-s")]);
        else
            return json_encode(strip_tags($el->LAST_ERROR));
    }

    /**
     * @param null $cityId
     * @return false|string
     * удаляем город из инфоблока. Так как метод Delete возвращает true даже при услвоии, что элемента не существует, делаем проверку методом checkCity(cityId)
     */
    private static function deleteCity($cityId = null)
    {
        if (is_null($cityId))
            return json_encode("NO INPUT DATA");

        if (self::checkIdCity($cityId)) {
            CIBlockElement::Delete($cityId);
            return json_encode("Item # " . $cityId . " was deleted!");
        } else {
            return json_encode("Item #" . $cityId . " not found!");
        }

    }

    /**
     * @param $cityId
     * @return bool
     * находим город с ID = cityId
     */
    private static function checkIdCity($cityId)
    {
        $result = CIBlockElement::GetList([], ["IBLOCK_ID" => 1, "ID" => $cityId], false, [], ["ID"]);
        while ($res = $result->GetNextElement()) {
            return (empty($res)) ? false : true;
        }
    }
}