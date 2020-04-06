<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$regex = "/{PRODUCT_ID\s([0-9]+)}/i";

preg_match_all($regex, $arResult["RESULT_HTML"], $matches, PREG_SET_ORDER);
if(!empty($matches)) {
    $i = 0;
    foreach ($matches as $match) {
        $productId[$i] = trim($match[1]);
        $product[$i] = $match;
        ++$i;
    }
    $arSelect = Array("ID", "IBLOCK_ID");
    $arFilter = Array("IBLOCK_TYPE" => $arParams["CATALOG_IBLOCK_TYPE"], "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y","ID"=>$productId);
    $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);
    $element_list = array();
    while($ob = $res->fetch())
    {
        $i = array_search($ob["ID"],$productId);
        $element_list[$i] = $ob;

    }
        foreach($element_list as $i=>$el) {
            $productId = $el["ID"];
            $bl_id = $el["IBLOCK_ID"];
        ob_start();

        $APPLICATION->IncludeComponent("bitrix:catalog.element", "article",
            array(
                "IBLOCK_TYPE" => $arParams["CATALOG_IBLOCK_TYPE"],
                "IBLOCK_ID" => $bl_id,
                "PROPERTY_CODE" => $arParams["CATALOG_DETAIL_PROPERTY_CODE"],
                "SET_META_KEYWORDS" => "N",
                "META_KEYWORDS" => "-",
                "SET_META_DESCRIPTION" => "N",
                "META_DESCRIPTION" => "-",
                "SET_BROWSER_TITLE" => "N",
                "BROWSER_TITLE" => "-",
                "SET_CANONICAL_URL" => "N",
                "BASKET_URL" => $arParams["CATALOG_BASKET_URL"],
                "ACTION_VARIABLE" => "action",
                "PRODUCT_ID_VARIABLE" => "id",
                "SECTION_ID_VARIABLE" => "SECTION_ID",
                "CHECK_SECTION_ID_VARIABLE" => "N",
                "PRODUCT_QUANTITY_VARIABLE" => "quantity",
                "PRODUCT_PROPS_VARIABLE" => "prop",
                "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                "CACHE_TIME" => $arParams["CACHE_TIME"],
                "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                "SET_TITLE" => "N",
                "SET_LAST_MODIFIED" => "N",
                "MESSAGE_404" => "",
                "SET_STATUS_404" => "N",
                "SHOW_404" => "N",
                "FILE_404" => "",
                "PRICE_CODE" => $arParams["CATALOG_PRICE_CODE"],
                "USE_PRICE_COUNT" => $arParams["CATALOG_USE_PRICE_COUNT"],
                "USE_RATIO_IN_RANGES" => $arParams["CATALOG_DETAIL_USE_RATIO_IN_RANGES"],
                "SHOW_PRICE_COUNT" => $arParams["CATALOG_SHOW_PRICE_COUNT"],
                "PRICE_VAT_INCLUDE" => $arParams["CATALOG_PRICE_VAT_INCLUDE"],
                "PRICE_VAT_SHOW_VALUE" => "N",
                "USE_PRODUCT_QUANTITY" => $arParams["CATALOG_USE_PRODUCT_QUANTITY"],
                "PRODUCT_PROPERTIES" => $arParams["CATALOG_PRODUCT_PROPERTIES"],
                "ADD_PROPERTIES_TO_BASKET" => $arParams["CATALOG_ADD_PROPERTIES_TO_BASKET"],
                "PARTIAL_PRODUCT_PROPERTIES" => $arParams["CATALOG_PARTIAL_PRODUCT_PROPERTIES"],
                "LINK_IBLOCK_TYPE" => "",
                "LINK_IBLOCK_ID" => "",
                "LINK_PROPERTY_SID" => "",
                "LINK_ELEMENTS_URL" => "",

                "OFFERS_CART_PROPERTIES" => $arParams["CATALOG_OFFERS_CART_PROPERTIES"],
                "OFFERS_FIELD_CODE" => $arParams["CATALOG_DETAIL_OFFERS_FIELD_CODE"],
                "OFFERS_PROPERTY_CODE" => $arParams["CATALOG_OFFERS_PROPERTY_CODE"],
                "OFFERS_SORT_FIELD" => $arParams["CATALOG_OFFERS_SORT_FIELD"],
                "OFFERS_SORT_ORDER" => $arParams["CATALOG_OFFERS_SORT_ORDER"],
                "OFFERS_SORT_FIELD2" => $arParams["CATALOG_OFFERS_SORT_FIELD2"],
                "OFFERS_SORT_ORDER2" => $arParams["CATALOG_OFFERS_SORT_ORDER2"],

                "ELEMENT_ID" => $productId,
                "ELEMENT_CODE" => "",
                "SECTION_ID" => "",
                "SECTION_CODE" => "",
                "SECTION_URL" => "",
                "DETAIL_URL" => "",
                "CONVERT_CURRENCY" => $arParams["CATALOG_CONVERT_CURRENCY"],
                "CURRENCY_ID" => $arParams["CATALOG_CURRENCY_ID"],
                "HIDE_NOT_AVAILABLE" => $arParams["CATALOG_HIDE_NOT_AVAILABLE"],
                "HIDE_NOT_AVAILABLE_OFFERS" => $arParams["CATALOG_HIDE_NOT_AVAILABLE_OFFERS"],
                "PRODUCT_DISPLAY_MODE" => $arParams["CATALOG_PRODUCT_DISPLAY_MODE"],

                "USE_ELEMENT_COUNTER" => "Y",
                "SHOW_DEACTIVATED" => "N",

                "USE_MAIN_ELEMENT_SECTION" => $arParams["CATALOG_USE_MAIN_ELEMENT_SECTION"],
                "CUSTOM_CURRENT_PAGE" => $arParams["CATALOG_CUSTOM_CURRENT_PAGE"],
                "STRICT_SECTION_CHECK" => "N",
                "ADD_PICT_PROP" => $arParams["CATALOG_DETAIL_ADD_PICT_PROP"],
                "OFFER_ADD_PICT_PROP" => $arParams["CATALOG_DETAIL_OFFER_ADD_PICT_PROP"],
                "OFFER_TREE_PROPS" => $arParams["CATALOG_OFFER_TREE_PROPS"],
                "PRODUCT_SUBSCRIPTION" => $arParams["CATALOG_PRODUCT_SUBSCRIPTION"],
                "SHOW_DISCOUNT_PERCENT" => $arParams["CATALOG_SHOW_DISCOUNT_PERCENT"],
                "SHOW_OLD_PRICE" => $arParams["CATALOG_SHOW_OLD_PRICE"],
                "SHOW_MAX_QUANTITY" => $arParams["CATALOG_SHOW_MAX_QUANTITY"],
                "MESS_SHOW_MAX_QUANTITY" => $arParams["CATALOG_MESS_SHOW_MAX_QUANTITY"],
                "RELATIVE_QUANTITY_FACTOR" => $arParams["CATALOG_RELATIVE_QUANTITY_FACTOR"],
                "MESS_RELATIVE_QUANTITY_MANY" => $arParams["CATALOG_MESS_RELATIVE_QUANTITY_MANY"],
                "MESS_RELATIVE_QUANTITY_FEW" => $arParams["CATALOG_MESS_RELATIVE_QUANTITY_FEW"],
                "MESS_BTN_BUY" => $arParams["CATALOG_MESS_BTN_BUY"],
                "MESS_BTN_ADD_TO_BASKET" => $arParams["CATALOG_MESS_BTN_ADD_TO_BASKET"],
                "MESS_BTN_SUBSCRIBE" => $arParams["CATALOG_MESS_BTN_SUBSCRIBE"],
                "MESS_BTN_DETAIL" => $arParams["CATALOG_MESS_BTN_DETAIL"],
                "MESS_NOT_AVAILABLE" => $arParams["CATALOG_MESS_NOT_AVAILABLE"],
                "MESS_BTN_COMPARE" => $arParams["CATALOG_MESS_BTN_COMPARE"],
                "MAIN_BLOCK_PROPERTY_CODE" => "",
                "MAIN_BLOCK_OFFERS_PROPERTY_CODE" => $arParams["CATALOG_DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE"],
                "IMAGE_RESOLUTION" => $arParams["CATALOG_DETAIL_IMAGE_RESOLUTION"],
                "ADD_DETAIL_TO_SLIDER" => $arParams["CATALOG_DETAIL_ADD_DETAIL_TO_SLIDER"],
                "ADD_SECTIONS_CHAIN" => "N",
                "ADD_ELEMENT_CHAIN" => "N",
                "DETAIL_PICTURE_MODE" => $arParams["CATALOG_DETAIL_DETAIL_PICTURE_MODE"],
                "ADD_TO_BASKET_ACTION" => array($arParams["CATALOG_ADD_TO_BASKET_ACTION"]),
                "ADD_TO_BASKET_ACTION_PRIMARY" => "",
                "DISPLAY_COMPARE" => $arParams["CATALOG_DISPLAY_COMPARE"],
                "COMPARE_PATH" => $arParams["CATALOG_COMPARE_PATH"],
                "COMPARE_NAME" => $arParams["CATALOG_COMPARE_NAME"],
                "BACKGROUND_IMAGE" => "-",
                "COMPATIBLE_MODE" => "N",
                "DISABLE_INIT_JS_IN_COMPONENT" => "N",
                "SET_VIEWED_IN_COMPONENT" => "N",
                "SHOW_SLIDER" => $arParams["CATALOG_DETAIL_SHOW_SLIDER"],
                "SLIDER_INTERVAL" => $arParams["CATALOG_DETAIL_SLIDER_INTERVAL"],
                "SLIDER_PROGRESS" => $arParams["CATALOG_DETAIL_SLIDER_PROGRESS"],
                "USE_ENHANCED_ECOMMERCE" => "N",
                "DATA_LAYER_NAME" => "",
                "BRAND_PROPERTY" => "",

                "USE_REVIEW" => $arParams["CATALOG_USE_REVIEW"],
                "REVIEWS_IBLOCK_TYPE" => $arParams["CATALOG_REVIEWS_IBLOCK_TYPE"],
                "REVIEWS_IBLOCK_ID" => $arParams["CATALOG_REVIEWS_IBLOCK_ID"],

                "OBJECTS_USE_REVIEW" => $arParams["OBJECTS_USE_REVIEW"],
                "OBJECTS_REVIEWS_IBLOCK_ID" => $arParams["OBJECTS_REVIEWS_IBLOCK_ID"],
                "CONTACTS_IBLOCK_ID" => $arParams["CONTACTS_IBLOCK_ID"],
                "CONTACTS_USE_REVIEW" => $arParams["CONTACTS_USE_REVIEW"],
                "CONTACTS_REVIEWS_IBLOCK_ID" => $arParams["CONTACTS_REVIEWS_IBLOCK_ID"],
            ),
            false
        );

        $output = ob_get_clean();

        $arResult["RESULT_HTML"] = str_replace($product[$i][0], $output, $arResult["RESULT_HTML"]);
    }
    unset($match);
}
unset($matches, $regex);

echo $arResult["RESULT_HTML"];