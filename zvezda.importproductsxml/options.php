<?php
use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/zvezda.importproductsxml/tools/script.php");
CJSCore::Init(array("jquery"));

define("MODULE_NAME", "zvezda.importproductsxml");

if (!$USER->isAdmin())
{
    $APPLICATION->authForm('Nope');
}

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();

Loc::loadMessages($context->getServer()->getDocumentRoot()."/bitrix/modules/main/options.php");
Loc::loadMessages(__FILE__);

function fixObject (&$object)
{
    if (!is_object ($object) && gettype ($object) == 'object')
        return ($object = unserialize (serialize ($object)));
    return $object;
}

function start($shopId, $xmlPath)
{
    session_start();

    if (!isset($_SESSION['importProductsXml']))
    {
        $import = new importProductsXml($shopId, $xmlPath);
        $_SESSION['importProductsXml'] = $import;
    }
    else
    {
        $import = fixObject($_SESSION['importProductsXml']);

        /*
        $this->updatePrice = Option::get(self::MODULE, "update_price");
        $this->updateProperties = Option::get(self::MODULE, "update_properties");
        $this->updatePictures = Option::get(self::MODULE, "update_pictures");

        || $this->updatePrice != Option::get(self::MODULE, "update_price") || $this->updateProperties != Option::get(self::MODULE, "update_properties") ||
        */

        if(($shopId != 0 && $shopId != $import->shopId) || (!empty($xmlPath) && $xmlPath != $import->xmlPath) || $import->updatePictures != Option::get($import::MODULE, "update_pictures")
            || $import->updatePrice != Option::get($import::MODULE, "update_price") || $import->updateProperties != Option::get($import::MODULE, "update_properties")
        )
        {
            unset($_SESSION['importProductsXml']);
            session_destroy();
            return start($shopId, $xmlPath);
        }
    }

    return $_SESSION['importProductsXml'];
}

$step = 1; // по умолчанию шаг 1

if(!empty($next)) // если нажата кнопка далее
    $step = $request->getPost('step') + 1; // увеличиваем шаг на 1

if($step == 1) // Шаг 1
{
    if((!empty($save) || !empty($restore)) && $request->isPost() && check_bitrix_sessid())
    {
        //echo "<pre>"; print_r($request->getPostList()); echo "</pre>";

        if(!empty($restore))
        {
            CAdminMessage::showMessage(array(
                "MESSAGE" => "Восстановлены настройки по умолчанию",
                "TYPE" => "OK",
            ));

            Option::set(MODULE_NAME, "update_price", "on"); // обновление цены по умолчанию включено
            Option::set(MODULE_NAME, "update_properties", ""); // обновление полей и св-в по умолчанию отключено
            Option::set(MODULE_NAME, "update_pictures", ""); // обновление картинок по умолчанию отключено
        }
        elseif(!empty($save))
        {
            if(empty($request->getPost('email')))
            {
                CAdminMessage::showMessage("Не указан email.");
            }
            else
            {
                CAdminMessage::showMessage(array(
                    "MESSAGE" => "Настройки сохранены",
                    "TYPE" => "OK",
                ));

                Option::set(MODULE_NAME, "email", $request->getPost('email'));
                Option::set(MODULE_NAME, "update_price", $request->getPost('update_price'));
                Option::set(MODULE_NAME, "update_pictures", $request->getPost('update_pictures'));
                Option::set(MODULE_NAME, "update_properties", $request->getPost('update_properties'));
            }
        }
    }
}
elseif($step == 2) // Шаг 2
{
    $stepName = "Шаг 2 (Сопоставление категорий с инфоблоками).";

    if(!empty($next))
    {
        $shopId = $request->getPost('shop_id') ? $request->getPost('shop_id') : 0;
        $xmlPath = $request->getPost('xml_path') ? $request->getPost('xml_path') : "";
        $import = start($shopId, $xmlPath);
        $import->operationCategories();
    }
}
elseif($step == 3) // Шаг 3
{
    $stepName = "Шаг 3 (Сопоставление полей, св-в узлов offer из xml с инфоблоком товаров)";

    if(!empty($next))
    {
        //echo "<pre>"; print_r($request->getPostList()); echo "</pre>";
        $import = fixObject($_SESSION['importProductsXml']);

        foreach ($request->getPost('categories') as $categoryId => $iblockId)
        {
            //$categoryIblockId = $import->getCategoryIblockIdById($categoryId);

            if($categoryIblockId != $iblockId)            
                $import->updateIblockIdByCategoryId($categoryId, $iblockId);
        }

        $import->operationCategories();
    }
}
elseif($step == 4) // Шаг 4
{
    $stepName = "Шаг 4 (Импорт товаров).";

    if(!empty($next))
    {
        //echo "<pre>"; print_r($request->getPostList()); echo "</pre>";
        $import = fixObject($_SESSION['importProductsXml']);
    }
}

$title = $step == 1 ? Loc::getMessage("MAIN_TAB_TITLE_SET") : $stepName;

$tabControl = new CAdminTabControl("tabControl", array(
    array(
        "DIV" => "edit1",
        "TAB" => Loc::getMessage("MAIN_TAB_SET"),
        "TITLE" => $title,
    ),
    array(
        "DIV" => "edit2",
        "TAB" => "Инструкция",
        "TITLE" => "Инструкция по модулю",
    ),
));
?>
<div class="adm-info-message-wrap adm-info-message-green" style="display: none;">
    <div class="adm-info-message">
        <div class="adm-info-message-title" id="result"></div>

        <div class="adm-info-message-icon"></div>
    </div>
</div>
<?
$tabControl->begin();
?>

<form method="post" action="<?=sprintf('%s?mid=%s&lang=%s&mid_menu=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID)?>">
    <?php
    echo bitrix_sessid_post();
    $tabControl->beginNextTab();
    ?>

    <?if($step == 1)
    {
        $import = new importProductsXml();
    ?>

    <tr>
        <td width="40%">
            <label>Выберите магазин для импорта товаров:</label>
        <td width="60%">
            <select name="shop_id" id="shop_id">
                <option value="">Выберите магазин для импорта</option>
                <?
                $arShops = $import->getArrayShops();
                foreach($arShops as $arShop) {?>
                    <option value="<?=$arShop["ID"]?>"><?=htmlspecialcharsEx($arShop["NAME"])?></option>
                <?}?>
            </select>
        </td>
    </tr>

    <tr>
        <td width="40%">
            <label>Email (или список через запятую), на который будут отправляться сообщения:</label>
        <td width="60%">
            <input type="text" size="23" name="email" value="<?=Option::get(MODULE_NAME, "email", "");?>"/>
        </td>
    </tr>

    <tr>
        <td width="40%">
            <label>Ссылка на xml файл:</label>
        <td width="60%">
            <input type="text" size="23" name="xml_path" id="xml_path" value="" />
        </td>
    </tr>

    <tr>
        <td width="40%">
            <label>Обновлять цену:</label>
        <td width="60%">
            <input type="checkbox" name="update_price" <?if(Option::get(MODULE_NAME, "update_price")):?>checked<?endif;?>>
        </td>
    </tr>

    <tr>
        <td width="40%">
            <label>Обновлять поля и свойства (кроме картинок):</label>
        <td width="60%">
            <input type="checkbox" name="update_properties" <?if(Option::get(MODULE_NAME, "update_properties")):?>checked<?endif;?>>
        </td>
    </tr>

    <tr>
        <td width="40%">
            <label>Обновлять картинки:</label>
        <td width="60%">
            <input type="checkbox" name="update_pictures" <?if(Option::get(MODULE_NAME, "update_pictures")):?>checked<?endif;?>>
        </td>
    </tr>

    <?
    }
    elseif($step == 2)
    {
    ?>
        <p>Выбран магазин: <?=$import->shopName;?></p>
        <p>Сайт магазина: <?=$import->xmlUrl;?></p>
        <p>Адрес файла для загрузки: <a target="_blank" href="<?=$import->xmlPath?>"><?=$import->xmlPath?></a> <?=$import->xmlFileStatus?></p>
        <p>Товаров в файле: <?=$import->countOffers?></p>
        <p>По завершению загрузки письмо с результатами будет направлено по элестронной почте на адреса: <?=$import->email?></p>
        <?foreach($import->getCategoriesInIblockByXmlUrl() as $categoryId => $arCategory):?>
        <tr>
            <td id="<?=$categoryId?>"><?=$arCategory["NAME"]?> [<?=$categoryId?>]:</td>
            <td>
                <select size="5" name="categories[<?=$categoryId?>]">
                    <option <?if(empty($arCategory["IBLOCK_ID"])):?>selected<?endif;?> value="">(Не указан инфоблок)</option>
                    <?foreach($import->getArIblocks() as $arIblock):?>
                        <option <?if($arCategory["IBLOCK_ID"] == $arIblock["ID"]):?>selected<?endif;?> value="<?=$arIblock["ID"]?>"><?=$arIblock["NAME"]?> [<?=$arIblock["ID"]?>]</option>
                    <?endforeach;?>
                </select>
            </td>
        </tr>
        <?endforeach;?>
    <?
    }
    elseif($step == 3)
    {
    ?>

    <tr>
        <td><h3>Стандартные поля и свойства</h3></td>
    </tr>
    <tr>
        <td>
            <h4>Поля товара:</h4>

            <table>
                <?
                foreach($import->arFieldsProduct as $fieldXml => $arFieldProduct)
                {
                    if(array_key_exists($fieldXml, $import->fieldsOffers))
                    {
                        ?>
                            <tr>
                                <td><?=$fieldXml?>:</td>
                                <td>
                                    <select disabled><option selected><?=$arFieldProduct["NAME"]?> [<?=$arFieldProduct["CODE"]?>]</option></select>
                                </td>
                            </tr>
                        <?
                    }
                }
                ?>
            </table>

            <h4>Свойства товара:</h4>

            <table>
                <?
                foreach($import->arPropertiesProduct as $fieldXml => $arPropertyProduct)
                {
                    if(array_key_exists($fieldXml, $import->fieldsOffers))
                    {
                        ?>
                            <tr>
                                <td><?=$fieldXml?>:</td>
                                <td>
                                    <select disabled><option selected><?=$arPropertyProduct["NAME"]?> [<?=$arPropertyProduct["CODE"]?>]</option></select>
                                </td>
                            </tr>
                        <?
                    }
                }
                ?>
            </table>

            <h4>Поля торговых предложений:</h4>

            <table>
                <?
                foreach($import->arFieldsSku as $fieldXml => $arFieldSku)
                {
                    if(array_key_exists($fieldXml, $import->fieldsOffers))
                    {
                        ?>
                            <tr>
                                <td><?=$fieldXml?>:</td>
                                <td>
                                    <select disabled><option selected><?=$arFieldSku["NAME"]?><?if(isset($arFieldSku["CODE"])):?> [<?=$arFieldSku["CODE"]?>]<?endif;?></option></select>
                                </td>
                            </tr>
                        <?
                    }
                }
                ?>
            </table>

            <h4>Свойства торговых предложений:</h4>

            <table>
                <?
                foreach($import->arPropertiesSku as $fieldXml => $arPropertySku)
                {
                    if(array_key_exists($fieldXml, $import->fieldsOffers))
                    {
                        ?>
                            <tr>
                                <td><?=$fieldXml?>:</td>
                                <td>
                                    <select disabled><option selected><?=$arPropertySku["NAME"]?> [<?=$arPropertySku["CODE"]?>]</option></select>
                                </td>
                            </tr>
                        <?
                    }
                }
                ?>
            </table>
        </td>
    </tr>

    <?
    if($request->isAjaxRequest())
    {
        $APPLICATION->RestartBuffer();
    }
    ?>
    <?if(!empty($import->arParamsCategories)) {?>

        <style>
            .param_list select {
                width: 300px;
            }

            .param_list td {
                padding: 0px 10px 0px 10px;
                border: 1px solid #808080;
            }

            .param_list select {
                border: none;
                border-radius: 0px;
            }

            .ignore, .sku {
                text-align: center;
            }

            .column {
                padding: 5px 10px 5px 10px !important;
                font-weight: bold;
            }

            .prop {
                padding: 0px 0px 0px 0px !important;
            }
        </style>
        <tr>
            <td id="params">

                <?
                $url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

                $entityDataClassCategories = $import->entityDataClass($import->HlBlockIdCategories);
                $entityDataClassParams = $import->entityDataClass($import->HlBlockIdParams);

                //echo "<pre>"; print_r($import->getCategoriesInIblockByXmlUrl()); echo "</pre>";

                foreach($import->getCategoriesInIblockByXmlUrl() as $hlCategoryId => $arCategory)
                {
                    if(empty($arCategory["IBLOCK_ID"]))
                        continue;

                    //echo "<pre>"; print_r($arCategory); echo "</pre>";

                    $iblockId = $arCategory["IBLOCK_ID"];
                    $mxResult = CCatalogSKU::GetInfoByProductIBlock($iblockId);
                    $skuIblockId = $mxResult ? $mxResult["IBLOCK_ID"] : false;

                    if($skuIblockId)
                    {
                        $arSkuIblockProperties = [];

                        $rsSkuIblockProperties = CIBlock::GetProperties($skuIblockId, Array("NAME" => "ASC"), Array());

                        while($arSkuIblockProperty = $rsSkuIblockProperties->Fetch())
                        {
                            $arSkuIblockProperties[$arSkuIblockProperty["CODE"]] = $arSkuIblockProperty["NAME"];
                        }

                        $arSavedSkuProperties = [];

                        $rsData = $entityDataClassParams::getList(["select" => ["*"], "filter" => ["UF_IBLOCK_ID" => $skuIblockId, "UF_HL_CATEGORY_ID" => $hlCategoryId]]);

                        while($arData = $rsData->Fetch())
                        {
                            $arSavedSkuProperties[$arData["UF_PARAM_CODE"]]["PROPERTY_CODE"] = $arData["UF_PROPERTY_CODE"];
                            $arSavedSkuProperties[$arData["UF_PARAM_CODE"]]["IGNORE"] = $arData["UF_IGNORE"];
                        }

                        $showCreatPropSku = false;

                        foreach($import->arParamsCategories[$arCategory["CATEGORY_ID"]] as $paramName => $paramCode)
                        {
                            if(!array_key_exists($paramCode, $arSkuIblockProperties))
                                $showCreatPropSku = true;
                        }
                    }

                    $arIblockProperties = [];

                    $rsIblockProperties = CIBlock::GetProperties($iblockId, Array("NAME" => "ASC"), Array());

                    while($arIblockProperty = $rsIblockProperties->Fetch())
                    {
                        $arIblockProperties[$arIblockProperty["CODE"]] = $arIblockProperty["NAME"];
                    }

                    $arSavedProperties = [];

                    $rsData = $entityDataClassParams::getList(["select" => ["*"], "filter" => ["UF_IBLOCK_ID" => $iblockId, "UF_HL_CATEGORY_ID" => $hlCategoryId]]);

                    while($arData = $rsData->Fetch())
                    {
                        $arSavedProperties[$arData["UF_PARAM_CODE"]]["PROPERTY_CODE"] = $arData["UF_PROPERTY_CODE"];
                        $arSavedProperties[$arData["UF_PARAM_CODE"]]["IGNORE"] = $arData["UF_IGNORE"];
                    }

                    $showCreatProp = false;

                    foreach($import->arParamsCategories[$arCategory["CATEGORY_ID"]] as $paramName => $paramCode)
                    {
                        if(!array_key_exists($paramCode, $arIblockProperties))
                            $showCreatProp = true;
                    }
                    ?>

                    <?if(!empty($import->arParamsCategories[$arCategory["CATEGORY_ID"]])){?>

                    <h4>Сопоставление узлов "param" для категории "<?=$arCategory["NAME"]?>" инфоблока товаров [<?=$iblockId?>]:</h4>

                    <table class="param_list">
                        <tr>
                            <td class="column">Имя param</td>
                            <td class="column">Свойство инфоблока</td>
                            <?if($showCreatProp):?><td class="column">Создать свойство</td><?endif;?>
                            <td class="column">Игнорировать</td>
                            <?if($skuIblockId):?><td class="column">Сопоставить поле для инфоблока ТП</td><?endif;?>
                        </tr>
                        <?
                        //foreach($import->arParams as $paramName => $paramCode)
                        foreach($import->arParamsCategories[$arCategory["CATEGORY_ID"]] as $paramName => $paramCode)
                        {?>
                            <tr>
                                <td><?=$paramName?>:</td>
                                <td class="prop">
                                    <select data-param-name="<?=$paramName?>" data-param-code="<?=$paramCode?>" data-iblock-id="<?=$iblockId?>" data-hl-category-id="<?=$hlCategoryId?>">
                                        <option value="">Не выбрано</option>
                                        <option value="" disabled>Свойства:</option>
                                        <?foreach($arIblockProperties as $propertyCode => $propertyName):?>
                                            <option <?if($arSavedProperties[$paramCode]["PROPERTY_CODE"] == $propertyCode):?>selected<?endif?> value="<?=$propertyCode?>"><?=$propertyName?> [<?=$propertyCode?>]</option>
                                        <?endforeach;?>
                                        <option value="" disabled>Поля:</option>
                                        <option <?if($arSavedProperties[$paramCode]["PROPERTY_CODE"] == "PREVIEW_TEXT"):?>selected<?endif?> value="PREVIEW_TEXT">Описание для анонса [PREVIEW_TEXT]</option>
                                        <option <?if($arSavedProperties[$paramCode]["PROPERTY_CODE"] == "DETAIL_TEXT"):?>selected<?endif?> value="DETAIL_TEXT">Детальное описание [DETAIL_TEXT]</option>
                                    </select>
                                </td>
                                <?if($showCreatProp):?>
                                <td>
                                    <?if(!array_key_exists($paramCode, $arIblockProperties)):?>
                                        <a href="<?=$url?>" data-property-name="<?=$paramName?>" data-property-code="<?=$paramCode?>" data-iblock-id="<?=$iblockId?>"><?=$paramName?> [<?=$paramCode?>]</a>
                                    <?endif;?>
                                </td>
                                <?endif;?>
                                <td class="ignore"><input type="checkbox" name="ignore" data-param-name="<?=$paramName?>" data-param-code="<?=$paramCode?>" data-iblock-id="<?=$iblockId?>" data-hl-category-id="<?=$hlCategoryId?>" <?if($arSavedProperties[$paramCode]["IGNORE"]):?>checked<?endif;?>></td>
                                <?if($skuIblockId):?><td class="sku"><input type="checkbox" name="sku" data-xml-id="<?=$hlCategoryId?>_<?=$skuIblockId?>" data-param-code="<?=$paramCode?>" <?if($arSavedSkuProperties[$paramCode]["PROPERTY_CODE"] == $paramCode):?>checked<?endif;?>></td><?endif;?>
                            </tr>
                            <?
                        }
                        ?>
                    </table>
                    <?}?>

                    <?
                    if($skuIblockId)
                    {
                    ?>
                        <?
                        $countSaveSkuProperties = 0;

                        foreach($arSavedSkuProperties as $paramCode => $arParam)
                        {
                            if(empty($arParam["PROPERTY_CODE"]))
                                continue;

                            $countSaveSkuProperties++;
                        }
                        ?>

                        <?if(!empty($import->arParamsCategories[$arCategory["CATEGORY_ID"]])){?>

                        <div id="<?=$hlCategoryId?>_<?=$skuIblockId?>" <?if(!$countSaveSkuProperties):?>style="display: none;"<?endif;?>>

                        <h4>Сопоставление узлов "param" для категории "<?=$arCategory["NAME"]?>" инфоблока торговых предложений [<?=$skuIblockId?>]:</h4>

                        <table class="param_list">
                            <tr>
                                <td class="column">Имя param</td>
                                <td class="column">Свойство инфоблока</td>
                                <?if($showCreatPropSku):?><td class="column">Создать свойство</td><?endif;?>
                                <td class="column">Игнорировать</td>
                            </tr>
                            <?
                            $arFields = ["DETAIL_TEXT", "PREVIEW_TEXT"];
                            //foreach($import->arParams as $paramName => $paramCode)
                            foreach($import->arParamsCategories[$arCategory["CATEGORY_ID"]] as $paramName => $paramCode)
                            {?>
                                <tr id="<?=$paramCode?>" <?if(!array_key_exists($arSavedSkuProperties[$paramCode]["PROPERTY_CODE"], $arSkuIblockProperties) && !in_array($arSavedSkuProperties[$paramCode]["PROPERTY_CODE"], $arFields)):?>style="display: none;"<?endif;?>>
                                    <td><?=$paramName?>:</td>
                                    <td class="prop">
                                        <select data-param-name="<?=$paramName?>" data-param-code="<?=$paramCode?>" data-iblock-id="<?=$skuIblockId?>" data-hl-category-id="<?=$hlCategoryId?>">
                                            <option value="">Не выбрано</option>
                                            <option value="" disabled>Свойства:</option>
                                            <?foreach($arSkuIblockProperties as $propertyCode => $propertyName):?>
                                                <option <?if($arSavedSkuProperties[$paramCode]["PROPERTY_CODE"] == $propertyCode):?>selected<?endif?> value="<?=$propertyCode?>"><?=$propertyName?> [<?=$propertyCode?>]</option>
                                            <?endforeach;?>
                                            <option value="" disabled>Поля:</option>
                                            <option <?if($arSavedSkuProperties[$paramCode]["PROPERTY_CODE"] == "PREVIEW_TEXT"):?>selected<?endif?> value="PREVIEW_TEXT">Описание для анонса [PREVIEW_TEXT]</option>
                                            <option <?if($arSavedSkuProperties[$paramCode]["PROPERTY_CODE"] == "DETAIL_TEXT"):?>selected<?endif?> value="DETAIL_TEXT">Детальное описание [DETAIL_TEXT]</option>
                                        </select>
                                    </td>
                                    <?if($showCreatPropSku):?>
                                        <td>
                                            <?if(!array_key_exists($paramCode, $arSkuIblockProperties)):?>
                                                <a href="<?=$url?>" data-property-name="<?=$paramName?>" data-property-code="<?=$paramCode?>" data-iblock-id="<?=$skuIblockId?>"><?=$paramName?> [<?=$paramCode?>]</a>
                                            <?endif;?>
                                        </td>
                                    <?endif;?>
                                    <td class="ignore"><input type="checkbox" name="ignore" data-param-name="<?=$paramName?>" data-param-code="<?=$paramCode?>" data-iblock-id="<?=$skuIblockId?>" data-hl-category-id="<?=$hlCategoryId?>" <?if($arSavedSkuProperties[$paramCode]["IGNORE"]):?>checked<?endif;?>></td>
                                </tr>
                                <?
                            }
                            ?>
                        </table>

                        </div>

                        <?}?>

                    <?
                    }
                    ?>

                    <?
                }
                ?>

            </td>
        </tr>
    <?}?>

    <?
    if($request->isAjaxRequest())
    {
        die();
    }
    ?>

    <?}?>

    <input id="step" name="step" type="hidden" value="<?=$step?>">

    <?
    $tabControl->BeginNextTab();
    ?>

    <?include $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/zvezda.importproductsxml/tools/tab_instruction.php";?>

    <?php
    $tabControl->buttons();
    ?>

    <?if($step == 1):?>
    <input type="submit"
           name="save"
           value="<?=Loc::getMessage("MAIN_SAVE") ?>"
           title="<?=Loc::getMessage("MAIN_OPT_SAVE_TITLE") ?>"
    />

    <input type="submit"
           name="restore"
           title="<?=Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
           onclick="return confirm('<?= AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')"
           value="<?=Loc::getMessage("MAIN_RESTORE_DEFAULTS") ?>"
    />
    <?endif;?>

    <?if($step == 4):?><input type="button" id="start" value="Запустить"><?endif;?>

    <?if($step != 4):?>
    <input type="submit"
           name="next"
           title="<?=$stepName?>"
           value="Далее"
           class="adm-btn-save"
           <?if($step == 1):?>disabled<?endif;?>
    />
    <?endif;?>

    <?php
    $tabControl->end();
    ?>
</form>

<script>
    // действия при клике на чекбокс Сопоставить поле для инфоблока ТП
    $("#params").on('change', "input[name='sku']", function()
    {
        var checked = $(this).prop('checked');
        var xmlId = $(this).attr("data-xml-id");
        var paramCode = $(this).attr("data-param-code");
        var div = $('#'+xmlId);
        var tr = div.find('#'+paramCode);

        if(checked)
        {
            tr.show();
            div.show();
        }
        else
        {
            tr.hide();

            var trLenght = div.find("tr:visible").length;
            if(trLenght == 1)
                div.hide();
        }
    });

    // действия при клике на чекбокс игнорировать
    $("#params").on('change', "input[name='ignore']", function()
    {
        var checked = $(this).prop('checked');
        var ignore = checked ? 1 : 0;
        var paramCode = $(this).attr("data-param-code");
        var paramName = $(this).attr("data-param-name");
        var iblockId = $(this).attr("data-iblock-id");
        var hlCategoryId = $(this).attr("data-hl-category-id");

        $.ajax({
            type: "POST",
            url: "/bitrix/modules/zvezda.importproductsxml/tools/ajax_ignore_param.php",
            data: "ignore="+ignore+"&param_code="+paramCode+"&param_name="+paramName+"&iblockId="+iblockId+"&hlCategoryId="+hlCategoryId,
            success: function(data)
            {
                //console.log(data);
            },
            error: function (jqXHR, exception) {
                console.log(jqXHR);
                console.log(exception);
            }
        });

    });

    // действия при выборе св-ва
    $("#params").on('change', 'select', function()
    {
        var propertyCode = $(this).val();
        var paramCode = $(this).attr("data-param-code");
        var paramName = $(this).attr("data-param-name");
        var iblockId = $(this).attr("data-iblock-id");
        var hlCategoryId = $(this).attr("data-hl-category-id");

        if(!propertyCode)
        {
            var skuXmlId = hlCategoryId+"_"+iblockId;
            var sku = $('input[data-xml-id="'+skuXmlId+'"], input[name="'+paramCode+'"]');
            //var sku = $(skuBlockId).find('input[data-id="'+skuBlockId+'"]');
            console.log(sku);
        }

        $.ajax({
            type: "POST",
            url: "/bitrix/modules/zvezda.importproductsxml/tools/ajax_select_prop.php",
            data: "property_code="+propertyCode+"&param_code="+paramCode+"&param_name="+paramName+"&iblockId="+iblockId+"&hlCategoryId="+hlCategoryId,
            success: function(data)
            {
                //console.log(data);
            },
            error: function (jqXHR, exception) {
                console.log(jqXHR);
                console.log(exception);
            }
        });

    });

    // действия по нажаютию ссылки создать св-во
    $("#params").on('click', 'a', function(e)
    {
        e.preventDefault();

        var url = $(this).attr("href");
        var propertyName = $(this).attr("data-property-name");
        var propertyCode = $(this).attr("data-property-code");
        var iblockId = $(this).attr("data-iblock-id");

        $.ajax({
            type: "POST",
            url: "/bitrix/modules/zvezda.importproductsxml/tools/ajax_add_property.php",
            data: "propertyName="+propertyName+"&propertyCode="+propertyCode+"&iblockId="+iblockId,
            success: function(result)
            {
                if(result.propertyId)
                {
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: "step=<?=$step-1?>&next=Далее",
                        success: function(result)
                        {
                            $("#params").html(result);
                        },
                        error: function (jqXHR, exception) {
                            console.log(jqXHR);
                            console.log(exception);
                        }
                    });
                }
            },
            error: function (jqXHR, exception) {
                console.log(jqXHR);
                console.log(exception);
            }
        });


    });

    // действия при выборе магазина
    $('#shop_id').on('change', function()
    {
        if($(this).val()) // если выбран магазин для импорта
        {
            $('#xml_path').val(""); // удаляем значение введенного xml пути
            $('input[name="next"]').removeAttr("disabled"); // делаем кнопку далее активной
        }

        if(!$(this).val() && !$('#xml_path').val())
        {
            $('input[name="next"]').attr('disabled','disabled');
        }
    });

    // действия при вводе ссылки на xml файл
    $('#xml_path').on('keyup', function()
    {
        if($(this).val()) // если не пустое значение
        {
            $('#shop_id').val(""); // удаляем значение выбранного магазина
            $('input[name="next"]').removeAttr("disabled"); // делаем кнопку далее активной
        }

        if(!$(this).val() && !$('#shop_id').val())
        {
            $('input[name="next"]').attr('disabled','disabled');
        }
    });

    function requestScript(count = 10, num = 0)
    {
        var xmlMode = "<?=$import->mode?>";
        var data = (xmlMode == "xml") ? "count="+count+"&num="+num+"&xml_path=<?=$import->xmlPath?>" : "count="+count+"&num="+num+"&shop_id=<?=$import->shopId?>";

        $.ajax({
            type: "POST",
            url: "/bitrix/modules/zvezda.importproductsxml/tools/ajax.php",
            data: data,
            success: function(data)
            {
                console.log(data);

                $('#result').html(data.msg);

                if(data.num < data.countItems)
                    requestScript(count, data.num);

                if(data.num == data.countItems)
                    $('#start').removeAttr('disabled');
            },
            error: function (jqXHR, exception) {
                console.log(jqXHR);
                requestScript(count, num);
            }
        });
    }

    $('#start').on('click', function() {
        $(".adm-info-message-wrap").css("display", "block");
        $('#result').html("Импорт запущен, подождите.");
        $('#start').attr('disabled','disabled');
        requestScript();
    });
</script>
