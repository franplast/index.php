<?
$paramCode = $_REQUEST["param_code"];
$paramName = $_REQUEST["param_name"];
$iblockId = $_REQUEST["iblockId"];
$ignore = $_REQUEST["ignore"];
$hlCategoryId = $_REQUEST["hlCategoryId"];

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

if(isset($paramCode) && isset($paramName) && isset($iblockId) && isset($hlCategoryId))
{
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
    CModule::IncludeModule("highloadblock");

    $hlbl = 3;
    $hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();

    $entity = HL\HighloadBlockTable::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();

    $rsData = $entity_data_class::getList(array(
        "select" => array("*"),
        "filter" => array("UF_IBLOCK_ID" => $iblockId, "UF_PARAM_CODE" => $paramCode, "UF_HL_CATEGORY_ID" => $hlCategoryId)
    ));

    if($arData = $rsData->Fetch())
    {
        //if($propertyCode != $arData["UF_PROPERTY_CODE"])
        $result = $entity_data_class::update($arData["ID"], ["UF_IGNORE" => $ignore]);
    }
    else
    {
        $data = array(
            "UF_PARAM_CODE" => $paramCode,
            "UF_PARAM_NAME" => $paramName,
            "UF_IBLOCK_ID" => $iblockId,
            "UF_IGNORE" => $ignore,
            "UF_HL_CATEGORY_ID" => $hlCategoryId
        );

        $result = $entity_data_class::add($data);
    }
}
?>