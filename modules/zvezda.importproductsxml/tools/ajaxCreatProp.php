<?
//$_POST["NAME"];//название поля
//$_POST["CODE"];//код поля
//$_POST["ANYWAY"];//если есть то все равно создать
//$_POST['iblock'];//инфоблок id
header("Content-type: application/json; charset=utf-8");
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Catalog\CatalogIblockTable;
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");
$IBLOCK_ID = $_POST['iblock']; // TODO get saved option
$status = "1"; // Пока доступен файл или нет, позже сюда же можно добавить обновлён удалённый или нет. 0- недоступен, 1 - доступен
$message =""; // Заполняем, если есть что сказать, например, что файл не обновлялся с последней проверки, или что он не доступен
$arResult['STATUS'] = $status;
$arResult['MASSAGE'] = $message;

$name = $_POST["NAME"];//название поля
$code = $_POST["CODE"];//код поля
$anyway = $_POST["ANYWAY"];//если есть то все равно создать
//$IBLOCK_ID = 66;
//$code = "ARTNUMBER";
//$name = "Проверка";
//$anyway = 1;
if(!$name) $name = $code;//если нет имени то используем код
if($IBLOCK_ID && $code) {//если есть id блока и код то работаем
    $is_code = checkProp($IBLOCK_ID, $code);
    if ($is_code && $anyway) {
        $code = getNewName($IBLOCK_ID, $code);
        $res = addCode($IBLOCK_ID, $name, $code);
        $arResult['STATUS'] = $res;
    } elseif ($is_code === false) {
        $res = addCode($IBLOCK_ID, $name, $code);
        $arResult['STATUS'] = $res;
    } else {
        $arResult['STATUS'] = 0;
        $arResult['MASSAGE'] = "Поле уже существует";
    }
}
else {
    $arResult['STATUS'] = 0;
}
echo json_encode($arResult);



//получение свойств
function checkProp($id,$code)
{
    $prop_ib = CIBlock::GetProperties($id, Array(), Array("CODE" => $code));
    if (!$prop_ib->Fetch()) {
        return false;
    }
    return true;
}
//получаем новый код
function getNewName($id,$code){
    $num = array();
    $prop_ib = CIBlock::GetProperties($id, Array(), Array("CODE"=>$code."%"));
    while($pr = $prop_ib->fetch()){
        $num[] = (int)str_replace($code."_","",$pr["CODE"]);
    }
    $n = max($num)+1;
    return $code."_".$n;
}
//получение полей
function addCode($id,$name,$code){
    $ibp = new CIBlockProperty;
    $arFields = Array(
        "NAME" 			=> $name,
        "CODE" 			=> $code,
        "IBLOCK_ID" 	=> $id,
        "PROPERTY_TYPE"	=> "S",

    );
    return $ibp->Add($arFields)?1:0;
}
