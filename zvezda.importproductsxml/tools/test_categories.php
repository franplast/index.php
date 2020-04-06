<?
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/zvezda.importproductsxml/tools/script.php");

$shopId = 62194;
$import = new importProductsXml($shopId);
$import->operationCategories();
?>

<table>
<?foreach($import->getCategoriesInIblockByXmlUrl() as $categoryId => $arCategory):?>
    <tr>
        <td id="<?=$categoryId?>"><?=$arCategory["NAME"]?> [<?=$categoryId?>]:</td>
        <td>
            <select size="5" name="categories[]">
                <option <?if(empty($arCategory["IBLOCK_ID"])):?>selected<?endif;?>>(Не указан инфоблок)</option>
                <?foreach($import->getArIblocks() as $arIblock):?>
                    <option <?if($arCategory["IBLOCK_ID"] == $arIblock["ID"]):?>selected<?endif;?> value="<?=$arIblock["ID"]?>"><?=$arIblock["NAME"]?> [<?=$arIblock["ID"]?>]</option>
                <?endforeach;?>
            </select>
        </td>
    </tr>
<?endforeach;?>
</table>
