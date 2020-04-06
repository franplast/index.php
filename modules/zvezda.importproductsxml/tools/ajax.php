<?
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/zvezda.importproductsxml/tools/script.php");

function start()
{
    session_start();

    $numItem = isset($_REQUEST["num"]) ? $_REQUEST["num"]: 0;
    $countItem = isset($_REQUEST["count"]) ? $_REQUEST["count"]: 0;
    $shopId = isset($_REQUEST["shop_id"]) ? $_REQUEST["shop_id"]: 0;
    $xmlPath = isset($_REQUEST["xml_path"]) ? $_REQUEST["xml_path"]: "";

    $numSum = $numItem + $countItem;

    if (!isset($_SESSION['importProductsXml']))
    {
        $import = new importProductsXml($shopId, $xmlPath);
        $import->operationCategories();
        $import->operationOffers($numItem, $countItem);

        $_SESSION['importProductsXml'] = $import;
    }
    else
    {
        $import = $_SESSION['importProductsXml'];

        if(($shopId != 0 && $shopId != $import->shopId) || (!empty($xmlPath) && $xmlPath != $import->xmlPath))
        {
            unset($_SESSION['importProductsXml']);
            session_destroy();
            return start();
        }

        $import->operationOffers($numItem, $countItem);
    }

    $numSum = ($numSum > $import->countOffers) ? $import->countOffers : $numSum;

    $arResult = [
        "countItems" => $import->countOffers,
        "num" => $numSum,
        "msg" => "Пройдено $numSum элементов из ".$import->countOffers,
        "memory" => memory_get_usage(true),
    ];

    header('Content-type: application/json');
    echo json_encode($arResult);
}

start();
?>