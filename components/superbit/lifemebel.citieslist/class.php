<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

class LifeMebelCitiesList extends CBitrixComponent
{
    private $arCities = [];

    public function executeComponent()
    {
        if ($this->startResultCache()) {
            $this->arResult['ITEMS'] = $this->getCities();
            $this->includeComponentTemplate();
        }
    }

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

    private function getCities()
    {
        $this->setListCities();
        return $this->arCities;
    }



    /*    private function addCitiesToIblock()
        {
            Loader::includeModule("sale");
            Loader::includeModule("iblock");

            $arCities = $this->getCities();
            $arErrors = "";

            foreach ($arCities as $arCity)
            {
                $arCity['IBLOCK_ID'] = 1;
                $el = new CIBlockElement;
                if ($sID = $el->Add($arCity))
                    $arErrors .= "";
                else
                    $arErrors .= $el->LAST_ERROR;
            }

            if (!empty($arErrors))
                return $arErrors;
        }

        private function getCities()
        {
            $res = \Bitrix\Sale\Location\LocationTable::getList(array(
                'filter' => array('=TYPE.ID' => '5', '=NAME.LANGUAGE_ID' => LANGUAGE_ID),
                'select' => array('NAME_RU' => 'NAME.NAME')
            ));
            $arResult = [];
            while ($item = $res->fetch()) {
                $arItem = [
                    "NAME" => $item['NAME_RU'],
                    "CODE" => CUtil::translit($item['NAME_RU'], "ru")
                ];
                $arResult[] = $arItem;
            }

            return $arResult;
        }*/
}