<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

/**
 * Class LifeMebelCitiesList
 */
class LifeMebelCitiesList extends CBitrixComponent
{
    /**
     * @var array
     * в массиве будет храниться список городов
     */
    private $arCities = [];

    /**
     * @return mixed|void|null
     */
    public function executeComponent()
    {
        if ($this->startResultCache()) {
            $this->arResult['ITEMS'] = $this->getCities();
            $this->includeComponentTemplate();
        }
    }

    /**
     * @throws \Bitrix\Main\LoaderException
     * получаем города из инфоблока (ID, NAME, CODE)
     * добавлен ключ CACHE_TEST, который демонстрирует работу кеширования
     */
    private function setListCities()
    {
        Loader::includeModule("iblock");

        $arSelect = ["ID", "NAME", "CODE"];
        $arFilter = ["IBLOCK_ID" => 1];


        $arCities = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);
        if (!$arCities)
            $this->abortResultCache();

        while ($arCity = $arCities->GetNextElement()) {
            $arFields = $arCity->GetFields();
            $arFields['CACHE_TEST'] = Date("Y-m-d:h-m-s");
            $arResult[] = $arFields;
        }
        $this->arCities = $arResult;

    }

    /**
     * @return array
     * @throws \Bitrix\Main\LoaderException
     * заносим в массив arCities список городов из метода SetListCities
     */
    private function getCities()
    {
        $this->setListCities();
        return $this->arCities;
    }
}