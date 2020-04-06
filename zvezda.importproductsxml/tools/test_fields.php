<?
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/zvezda.importproductsxml/tools/script.php");

$shopId = 45996;
$import = new importProductsXml($shopId);



//echo "<pre>"; print_r($import->arCategories); echo "</pre>";
echo "<pre>"; print_r($import->arParamsCategories); echo "</pre>";

?>