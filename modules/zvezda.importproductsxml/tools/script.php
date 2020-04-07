<?
set_time_limit(0);
ini_set('memory_limit', '1000M');

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
//require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/classes/general/xml.php');
//
//CModule::IncludeModule("highloadblock");
//
//use Bitrix\Highloadblock as HL;
//use Bitrix\Main\Entity;
//use Bitrix\Main\Config\Option;
//
//CModule::IncludeModule("iblock");
//CModule::IncludeModule("sale");
//CModule::IncludeModule("catalog");
//
//$el = new CIBlockElement;
//$ibp = new CIBlockProperty;

class importProductsXml
{
    // перенести в настройки модуля

    private $offersInIteration; // количество оферов на итерацию

    // Св-ва
    private $tovarProps; // список св-в товаров для проверки и добавления в ИБ
    private $skuProps; // список св-в ску для проверки и добавления в ИБ


    // Методы

    public function __construct(){

        // перенести в настройки модуля
        $this->offersInIteration = 10;


    }




    public function sinchronize(){

        $this->updateProps( $this->getTovarProps(), $this->tovarIbId); // проверка и дополнение необходимых св-в ИБ товаров
        $this->updateProps( $this->getSkuProps(), $this->skuIbId); // проверка и дополнение необходимых св-в ИБ SKU

        $currentOffers = $this->getOffers( $this->offersInIteration ); // получаем новую группу оферов в установленном количестве

        foreach ( $currentOffers as $offer ){

            if( $SKU = $this->getSku($offer) ){
                $this->updateSku($offer, $SKU);
            }
            elseif( $tovar = $this->getSku($offer) ){

            }




        }

        private function getTovarProps(){
            if( !$this->tovarProps ){
                $tovarProps = [];
                $this->tovarProps = $tovarProps;
            }
            return $this->tovarProps;
        }

        private function getSkuProps(){
            if( !$this->skuProps ){
                $skuProps = [];
                $this->skuProps = $skuProps;
            }
            return $this->skuProps;

        }

        private function getOffers( ){

            $arXml = simplexml_load_string($this->xmlContent);
            return $arXml->xpath("/yml_catalog/shop/offers/offer");


            $arOffers = [];

            $this->offersInIteration;

            return $arOffers;
        }

        private function getXmlContent

    }

}