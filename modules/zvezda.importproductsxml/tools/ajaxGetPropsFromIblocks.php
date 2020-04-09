<?
header("Content-type: application/json; charset=utf-8");
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Catalog\CatalogIblockTable;
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");
$IBLOCK_ID = $_POST['iblock']; // TODO get saved option
$NEED = $_POST['need']; // TODO get saved option
$status = "1"; // Пока доступен файл или нет, позже сюда же можно добавить обновлён удалённый или нет. 0- недоступен, 1 - доступен
$message =""; // Заполняем, если есть что сказать, например, что файл не обновлялся с последней проверки, или что он не доступен
$arResult['STATUS'] = $status;
$arResult['MASSAGE'] = $message;
$arResult['ITEMS'] = array();

$iblockIterator = Bitrix\Catalog\CatalogIblockTable::getList(array(
    'select' => array('IBLOCK_ID'),
    'filter' => array('=PRODUCT_IBLOCK_ID' => $IBLOCK_ID)
));
$arResultSKU = $iblockIterator->fetch();
$id_sky = $arResultSKU["IBLOCK_ID"];

if(!isset($NEED)){
    $ib_field = getField($IBLOCK_ID);
    $ib_prop = getProp($IBLOCK_ID);
    $sku_prop = getProp($id_sky);
}
else {
    foreach ($NEED as $val){
        switch ($val){
            case "i_f"://поля инфоблока
                $ib_field = getField($id_info);
                break;
            case "i_p"://свойства инфоблока
                $ib_prop = getProp($id_info);
                break;
            case  "s_p"://свойства предложений
                $sku_prop = getProp($id_sky);
                break;
        }
    }
}
if($ib_field){
    $arResult['ITEMS']["FIELDS"] = $ib_field;
}
if($ib_prop) {
    $arResult['ITEMS']['PROPS'] = $ib_prop;
}
if($sku_prop) {
    $arResult['ITEMS']['SKU_PROPS'] = $sku_prop;
}
echo json_encode($arResult);


//получение свойств
function getProp($id){
    $res = array();
    $prop = CIBlock::GetProperties($id);
    while ($res_arr = $prop->Fetch()){
        $ar = array(
        "ID" => $res_arr["ID"],
        "NAME" => $res_arr["NAME"],
        "CODE" => $res_arr["CODE"],
        "TYPE" => $res_arr["PROPERTY_TYPE"]
        );
        if($res_arr["PROPERTY_TYPE"] == "E" || $res_arr["PROPERTY_TYPE"] == "G"){
            $ar["LINK_IBLOCK_ID"] = $res_arr["LINK_IBLOCK_ID"];// если E или G PROPERTY_TYPE*/
        }
        $res[] = $ar;
    }
    return $res;
}
//получение полей
function getField($id){
    $res = array();
    $ar = CIBlock::GetFields($id);
    foreach ($ar as $code => $f){
        $ar = array(
          "CODE" => $code,
          "NAME" => $f["NAME"]
        );
        $res[] = $ar;
    }
    return $res;
}
