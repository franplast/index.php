<?
$propertyName = $_REQUEST["propertyName"];
$propertyCode = $_REQUEST["propertyCode"];
$iblockId = $_REQUEST["iblockId"];

if(isset($propertyName) && isset($propertyCode) && isset($iblockId))
{
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
    CModule::IncludeModule("iblock");
    $ibp = new CIBlockProperty;

    //echo "<pre>"; print_r($_REQUEST["propertyName"]); echo "</pre>";
    $arFields = Array(
        "NAME" => $propertyName,
        "ACTIVE" => "Y",
        "SORT" => "500",
        "CODE" => $propertyCode,
        "PROPERTY_TYPE" => "S",
        "IBLOCK_ID" => $iblockId
    );

    header('Content-type: application/json');

    if($propertyId = $ibp->Add($arFields))
    {
        $arResult = ["propertyId" => $propertyId];
        echo json_encode($arResult);
    }
    else
    {
        $arResult = ["propertyId" => false];
        echo json_encode($arResult);
    }
}
?>