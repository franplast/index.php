<?
$propertyCode = $_REQUEST["property_code"];
$paramCode = $_REQUEST["param_code"];
$paramName = $_REQUEST["param_name"];
$iblockId = $_REQUEST["iblockId"];
$hlCategoryId = $_REQUEST["hlCategoryId"];

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

if(isset($propertyCode) && isset($paramCode) && isset($paramName) && isset($iblockId) && isset($hlCategoryId))
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
        if($propertyCode != $arData["UF_PROPERTY_CODE"])
            $result = $entity_data_class::update($arData["ID"], ["UF_PROPERTY_CODE" => $propertyCode]);
    }
    else
    {
        $data = array(
            "UF_PROPERTY_CODE" => $propertyCode,
            "UF_PARAM_CODE" => $paramCode,
            "UF_PARAM_NAME" => $paramName,
            "UF_IBLOCK_ID" => $iblockId,
            "UF_HL_CATEGORY_ID" => $hlCategoryId
        );

        $result = $entity_data_class::add($data);
    }

    /*
    CModule::IncludeModule("iblock");
    $el = new CIBlockElement;

    $rsElement = $el->GetList([], ["IBLOCK_ID" => 271, "NAME" => $paramCode, "PROPERTY_IBLOCK_ID" => $iblockId], false, false, ["ID", "IBLOCK_ID", "PROPERTY_CODE"]);

    if($arElement = $rsElement->Fetch())
    {
        if($propertyCode != $arElement["PROPERTY_CODE_VALUE"])
            $el->SetPropertyValuesEx($arElement["ID"], 271, ["CODE" => $propertyCode]);
    }
    else
    {
        $arProp["CODE"] = $propertyCode;
        $arProp["IBLOCK_ID"] = $iblockId;

        $elementId = $el->Add(["IBLOCK_ID" => 271, "NAME" => $paramCode, "PROPERTY_VALUES" => $arProp]);
    }
    */
}
?>


