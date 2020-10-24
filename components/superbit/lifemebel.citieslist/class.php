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
     * @var string
     */
    private $navs = "";
    /**
     * @var array
     */
    private $arNavParams = [
        "nPageSize" => "50",
        'bShowAll' => true
    ];
    /**
     * @var
     */
    private $arNavigation;


    /**
     * @return mixed|void|null
     */
    public function executeComponent()
    {
        if ($this->startResultCache(false, $this->getArNav())) {
            $this->arResult['ITEMS'] = $this->getCities();
            $this->arResult['NAVS'] = $this->navs;
            $this->includeComponentTemplate();
        }
    }

    /**
     * Метод для формаирования постраничной навигации и кеша
     */
    private function setArNav()
    {
        $this->arNavigation = CDBResult::GetNavParams($this->arNavParams);
    }

    /**
     * @return mixed
     */
    private function getArNav()
    {
        $this->setArNav();
        return $this->arNavigation;
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


        $arCities = CIBlockElement::GetList(array(), $arFilter, false, $this->arNavParams, $arSelect);
        if (!$arCities)
            $this->abortResultCache();

        $this->addNavs($arCities);

        while ($arCity = $arCities->GetNextElement()) {
            $arFields = $arCity->GetFields();
            $arFields['CACHE_TEST'] = Date("Y-m-d:h-m-s");
            $this->arCities[] = $arFields;
        }


    }

    /**
     * @param $arCities
     * подключаем компонент вывода pagenavigation
     */
    private function addNavs($arCities)
    {

        $navString = $arCities->GetPageNavString(
            'Элементы', // поясняющий текст
            'round',   // имя шаблона
            true       // показывать всегда?
        );
        $this->navs = $navString;
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