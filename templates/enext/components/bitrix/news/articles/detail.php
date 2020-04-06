<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

//ELEMENT//?>
<?$elementId = $APPLICATION->IncludeComponent("bitrix:news.detail", "article",
	array(
		"DISPLAY_DATE" => $arParams["DISPLAY_DATE"],
		"DISPLAY_NAME" => $arParams["DISPLAY_NAME"],
		"DISPLAY_PICTURE" => $arParams["DISPLAY_PICTURE"],
		"DISPLAY_PREVIEW_TEXT" => $arParams["DISPLAY_PREVIEW_TEXT"],
		"USE_SHARE" => $arParams["USE_SHARE"],
		"SHARE_HIDE" => $arParams["SHARE_HIDE"],
		"SHARE_TEMPLATE" => $arParams["SHARE_TEMPLATE"],
		"SHARE_HANDLERS" => $arParams["SHARE_HANDLERS"],
		"SHARE_SHORTEN_URL_LOGIN" => $arParams["SHARE_SHORTEN_URL_LOGIN"],
		"SHARE_SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
		"AJAX_MODE" => $arParams["AJAX_MODE"],
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
		"ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
		"CHECK_DATES" => $arParams["CHECK_DATES"],
		"FIELD_CODE" => $arParams["DETAIL_FIELD_CODE"],
		"PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
		"IBLOCK_URL" => $arParams["IBLOCK_URL"],
		"DETAIL_URL" => $arParams["DETAIL_URL"],
		"SET_TITLE" => $arParams["SET_TITLE"],
		"SET_CANONICAL_URL" => $arParams["DETAIL_SET_CANONICAL_URL"],
		"SET_BROWSER_TITLE" => $arParams["SET_BROWSER_TITLE"],
		"BROWSER_TITLE" => $arParams["BROWSER_TITLE"],
		"SET_META_KEYWORDS" => $arParams["SET_META_KEYWORDS"],
		"META_KEYWORDS" => $arParams["META_KEYWORDS"],
		"SET_META_DESCRIPTION" => $arParams["SET_META_DESCRIPTION"],
		"META_DESCRIPTION" => $arParams["META_DESCRIPTION"],
		"SET_STATUS_404" => $arParams["SET_STATUS_404"],
		"SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
		"INCLUDE_IBLOCK_INTO_CHAIN" => $arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
		"ADD_SECTIONS_CHAIN" => "N",
		"ADD_ELEMENT_CHAIN" => "N",
		"ACTIVE_DATE_FORMAT" => $arParams["DETAIL_ACTIVE_DATE_FORMAT"],
		"USE_PERMISSIONS" => $arParams["USE_PERMISSIONS"],
		"GROUP_PERMISSIONS" => $arParams["GROUP_PERMISSIONS"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"DISPLAY_TOP_PAGER" => $arParams["DETAIL_DISPLAY_TOP_PAGER"],
		"DISPLAY_BOTTOM_PAGER" => $arParams["DETAIL_DISPLAY_BOTTOM_PAGER"],
		"PAGER_TITLE" => $arParams["DETAIL_PAGER_TITLE"],
		"PAGER_TEMPLATE" => $arParams["DETAIL_PAGER_TEMPLATE"],
		"PAGER_SHOW_ALL" => $arParams["DETAIL_PAGER_SHOW_ALL"],
		"PAGER_BASE_LINK_ENABLE" => $arParams["PAGER_BASE_LINK_ENABLE"],
		"SET_STATUS_404" => $arParams["SET_STATUS_404"],
		"SHOW_404" => $arParams["SHOW_404"],
		"MESSAGE_404" => $arParams["MESSAGE_404"],
		"PAGER_BASE_LINK" => $arParams["PAGER_BASE_LINK"],
		"PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
		"AJAX_OPTION_JUMP" => $arParams["AJAX_OPTION_JUMP"],
		"AJAX_OPTION_STYLE" => $arParams["AJAX_OPTION_STYLE"],
		"AJAX_OPTION_HISTORY" => $arParams["AJAX_OPTION_HISTORY"],
		"CATALOG_IBLOCK_TYPE" => $arParams["CATALOG_IBLOCK_TYPE"],
		"CATALOG_IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"],
		"CATALOG_HIDE_NOT_AVAILABLE" => $arParams["CATALOG_HIDE_NOT_AVAILABLE"],
		"CATALOG_HIDE_NOT_AVAILABLE_OFFERS" => $arParams["CATALOG_HIDE_NOT_AVAILABLE_OFFERS"],
		"CATALOG_DETAIL_ADD_PICT_PROP" => $arParams["CATALOG_DETAIL_ADD_PICT_PROP"],
		"CATALOG_PRODUCT_DISPLAY_MODE" => $arParams["CATALOG_PRODUCT_DISPLAY_MODE"],
		"CATALOG_DETAIL_OFFER_ADD_PICT_PROP" => $arParams["CATALOG_DETAIL_OFFER_ADD_PICT_PROP"],
		"CATALOG_OFFER_TREE_PROPS" => $arParams["CATALOG_OFFER_TREE_PROPS"],
		"CATALOG_PRODUCT_SUBSCRIPTION" => $arParams["CATALOG_PRODUCT_SUBSCRIPTION"],
		"CATALOG_SHOW_DISCOUNT_PERCENT" => $arParams["CATALOG_SHOW_DISCOUNT_PERCENT"],
		"CATALOG_SHOW_OLD_PRICE" => $arParams["CATALOG_SHOW_OLD_PRICE"],
		"CATALOG_SHOW_MAX_QUANTITY" => $arParams["CATALOG_SHOW_MAX_QUANTITY"],
		"CATALOG_MESS_SHOW_MAX_QUANTITY" => $arParams["CATALOG_MESS_SHOW_MAX_QUANTITY"],
		"CATALOG_RELATIVE_QUANTITY_FACTOR" => $arParams["CATALOG_RELATIVE_QUANTITY_FACTOR"],
		"CATALOG_MESS_RELATIVE_QUANTITY_MANY" => $arParams["CATALOG_MESS_RELATIVE_QUANTITY_MANY"],
		"CATALOG_MESS_RELATIVE_QUANTITY_FEW" => $arParams["CATALOG_MESS_RELATIVE_QUANTITY_FEW"],
		"CATALOG_MESS_BTN_BUY" => $arParams["CATALOG_MESS_BTN_BUY"],
		"CATALOG_MESS_BTN_ADD_TO_BASKET" => $arParams["CATALOG_MESS_BTN_ADD_TO_BASKET"],
		"CATALOG_MESS_BTN_SUBSCRIBE" => $arParams["CATALOG_MESS_BTN_SUBSCRIBE"],
		"CATALOG_MESS_BTN_DETAIL" => $arParams["CATALOG_MESS_BTN_DETAIL"],
		"CATALOG_MESS_NOT_AVAILABLE" => $arParams["CATALOG_MESS_NOT_AVAILABLE"],
		"CATALOG_USE_MAIN_ELEMENT_SECTION" => $arParams["CATALOG_USE_MAIN_ELEMENT_SECTION"],
		"CATALOG_CUSTOM_CURRENT_PAGE" => SITE_DIR."catalog/",
		"CATALOG_PRICE_CODE" => $arParams["CATALOG_PRICE_CODE"],
		"CATALOG_USE_PRICE_COUNT" => $arParams["CATALOG_USE_PRICE_COUNT"],
		"CATALOG_SHOW_PRICE_COUNT" => $arParams["CATALOG_SHOW_PRICE_COUNT"],
		"CATALOG_PRICE_VAT_INCLUDE" => $arParams["CATALOG_PRICE_VAT_INCLUDE"],
		"CATALOG_CONVERT_CURRENCY" => $arParams["CATALOG_CONVERT_CURRENCY"],		
		"CATALOG_CURRENCY_ID" => $arParams["CATALOG_CURRENCY_ID"],
		"CATALOG_DETAIL_USE_RATIO_IN_RANGES" => $arParams["CATALOG_DETAIL_USE_RATIO_IN_RANGES"],
		"CATALOG_BASKET_URL" => $arParams["CATALOG_BASKET_URL"],
		"CATALOG_USE_PRODUCT_QUANTITY" => $arParams["CATALOG_USE_PRODUCT_QUANTITY"],
		"CATALOG_ADD_PROPERTIES_TO_BASKET" => $arParams["CATALOG_ADD_PROPERTIES_TO_BASKET"],
		"CATALOG_PARTIAL_PRODUCT_PROPERTIES" => $arParams["CATALOG_PARTIAL_PRODUCT_PROPERTIES"],
		"CATALOG_PRODUCT_PROPERTIES" => $arParams["CATALOG_PRODUCT_PROPERTIES"],
		"CATALOG_OFFERS_CART_PROPERTIES" => $arParams["CATALOG_OFFERS_CART_PROPERTIES"],
		"CATALOG_ADD_TO_BASKET_ACTION" => $arParams["CATALOG_ADD_TO_BASKET_ACTION"],		
		"CATALOG_ELEMENT_SORT_FIELD" => $arParams["CATALOG_ELEMENT_SORT_FIELD"],
		"CATALOG_ELEMENT_SORT_ORDER" => $arParams["CATALOG_ELEMENT_SORT_ORDER"],
		"CATALOG_ELEMENT_SORT_FIELD2" => $arParams["CATALOG_ELEMENT_SORT_FIELD2"],
		"CATALOG_ELEMENT_SORT_ORDER2" => $arParams["CATALOG_ELEMENT_SORT_ORDER2"],
		"CATALOG_INCLUDE_SUBSECTIONS" => $arParams["CATALOG_INCLUDE_SUBSECTIONS"],
		"CATALOG_OFFERS_PROPERTY_CODE" => $arParams["CATALOG_OFFERS_PROPERTY_CODE"],
		"CATALOG_DETAIL_PROPERTY_CODE" => $arParams["CATALOG_DETAIL_PROPERTY_CODE"],
		"CATALOG_DETAIL_OFFERS_FIELD_CODE" => $arParams["CATALOG_DETAIL_OFFERS_FIELD_CODE"],		
		"CATALOG_DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE" => $arParams["CATALOG_DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE"],
		"CATALOG_DETAIL_IMAGE_RESOLUTION" => $arParams["CATALOG_DETAIL_IMAGE_RESOLUTION"],
		"CATALOG_DETAIL_SHOW_SLIDER" => $arParams["CATALOG_DETAIL_SHOW_SLIDER"],
		"CATALOG_DETAIL_SLIDER_INTERVAL" => $arParams["CATALOG_DETAIL_SLIDER_INTERVAL"],
		"CATALOG_DETAIL_SLIDER_PROGRESS" => $arParams["CATALOG_DETAIL_SLIDER_PROGRESS"],
		"CATALOG_DETAIL_DETAIL_PICTURE_MODE" => $arParams["CATALOG_DETAIL_DETAIL_PICTURE_MODE"],
		"CATALOG_DETAIL_ADD_DETAIL_TO_SLIDER" => $arParams["CATALOG_DETAIL_ADD_DETAIL_TO_SLIDER"],
		"CATALOG_OFFERS_SORT_FIELD" => $arParams["CATALOG_OFFERS_SORT_FIELD"],
		"CATALOG_OFFERS_SORT_ORDER" => $arParams["CATALOG_OFFERS_SORT_ORDER"],
		"CATALOG_OFFERS_SORT_FIELD2" => $arParams["CATALOG_OFFERS_SORT_FIELD2"],
		"CATALOG_OFFERS_SORT_ORDER2" => $arParams["CATALOG_OFFERS_SORT_ORDER2"],
		"CATALOG_USE_REVIEW" => $arParams["CATALOG_USE_REVIEW"],
		"CATALOG_REVIEWS_IBLOCK_TYPE" => $arParams["CATALOG_REVIEWS_IBLOCK_TYPE"],
		"CATALOG_REVIEWS_IBLOCK_ID" => $arParams["CATALOG_REVIEWS_IBLOCK_ID"],
		"CATALOG_DISPLAY_COMPARE" => $arParams["CATALOG_DISPLAY_COMPARE"],
		"CATALOG_COMPARE_PATH" => $arParams["CATALOG_COMPARE_PATH"],
		"CATALOG_MESS_BTN_COMPARE" => $arParams["CATALOG_MESS_BTN_COMPARE"],
		"CATALOG_COMPARE_NAME" => $arParams["CATALOG_COMPARE_NAME"],
		"OBJECTS_USE_REVIEW" => $arParams["OBJECTS_USE_REVIEW"],
		"OBJECTS_REVIEWS_IBLOCK_TYPE" => $arParams["OBJECTS_REVIEWS_IBLOCK_TYPE"],
		"OBJECTS_REVIEWS_IBLOCK_ID" => $arParams["OBJECTS_REVIEWS_IBLOCK_ID"],
		"CONTACTS_IBLOCK_TYPE" => $arParams["CONTACTS_IBLOCK_TYPE"],
		"CONTACTS_IBLOCK_ID" => $arParams["CONTACTS_IBLOCK_ID"],
		"CONTACTS_USE_REVIEW" => $arParams["CONTACTS_USE_REVIEW"],
		"CONTACTS_REVIEWS_IBLOCK_TYPE" => $arParams["CONTACTS_REVIEWS_IBLOCK_TYPE"],
		"CONTACTS_REVIEWS_IBLOCK_ID" => $arParams["CONTACTS_REVIEWS_IBLOCK_ID"]
	),
	$component
);?>

<?if($elementId) {
	//CURRENT_ELEMENT//
	$arFilter = array(
		"ID" => $elementId,	
		"ACTIVE" => "Y",
		"IBLOCK_ID" => $arParams["IBLOCK_ID"]
	);

	$obCache = new CPHPCache();
	if($obCache->InitCache($arParams["CACHE_TIME"], serialize($arFilter), "/iblock/news")) {
		$arCurElement = $obCache->GetVars();
	} elseif($obCache->StartDataCache()) {
		$arCurElement = array();
		if(Bitrix\Main\Loader::includeModule("iblock")) {
			$rsElement = CIBlockElement::GetList(array(), $arFilter, false, false, array("ID", "IBLOCK_ID", "NAME", "PREVIEW_TEXT", "DETAIL_PICTURE", "DETAIL_PAGE_URL", "PROPERTY_BREADCRUMB_TITLE"));
			if(defined("BX_COMP_MANAGED_CACHE")) {
				global $CACHE_MANAGER;
				$CACHE_MANAGER->StartTagCache("/iblock/news");
				if($arCurElement = $rsElement->GetNext()) {
					$CACHE_MANAGER->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);

					//ELEMENT_DETAIL_PICTURE//
					if($arCurElement["DETAIL_PICTURE"] > 0)
						$arCurElement["DETAIL_PICTURE"] = CFile::GetFileArray($arCurElement["DETAIL_PICTURE"]);
					
					//ELEMENT_TITLE//
					$ipropValues = new Bitrix\Iblock\InheritedProperty\ElementValues($arCurElement["IBLOCK_ID"], $arCurElement["ID"]);
					$iproperty = $ipropValues->getValues();				
					$arCurElement["TITLE"] = !empty($iproperty["ELEMENT_PAGE_TITLE"]) ? $iproperty["ELEMENT_PAGE_TITLE"] : $arCurElement["NAME"];
					unset($iproperty, $ipropValues);

					//ELEMENT_BREADCRUMBS//
					if($arParams["ADD_SECTIONS_CHAIN"] == "Y" && $arCurElement["IBLOCK_SECTION_ID"] > 0) {
						$arCurElement["PATH"] = array();
						$rsPath = CIBlockSection::GetNavChain($arCurElement["IBLOCK_ID"], $arCurElement["IBLOCK_SECTION_ID"], array("ID", "IBLOCK_ID", "NAME", "SECTION_PAGE_URL"));
						while($path = $rsPath->GetNext()) {								
							$ipropValues = new Bitrix\Iblock\InheritedProperty\SectionValues($arCurElement["IBLOCK_ID"], $path["ID"]);
							$iproperty = $ipropValues->getValues();							
							$path["TITLE"] = !empty($iproperty["SECTION_PAGE_TITLE"]) ? $iproperty["SECTION_PAGE_TITLE"] : $path["NAME"];
							$arCurElement["PATH"][$path["ID"]] = $path;
						}
						unset($iproperty, $ipropValues, $path, $rsPath);
						
						if(!empty($arCurElement["PATH"])) {
							$rsSections = CIBlockSection::GetList(array(), array("IBLOCK_ID" => $arCurElement["IBLOCK_ID"], "ID" => array_keys($arCurElement["PATH"])), false, array("ID", "IBLOCK_ID", "UF_BREADCRUMB_TITLE"));
							while($arSection = $rsSections->GetNext()) {
								$arCurElement["PATH"][$arSection["ID"]]["BREADCRUMB_TITLE"] = $arSection["UF_BREADCRUMB_TITLE"];
							}
							unset($arSection, $rsSections);
						}
					}
				}
				$CACHE_MANAGER->EndTagCache();
			} else {
				if(!$arCurElement = $rsElement->GetNext())
					$arCurElement = array();
			}
		}
		$obCache->EndDataCache($arCurElement);
	}
	if(!isset($arCurElement))
		$arCurElement = array();
	
	//ELEMENT_BREADCRUMBS//
	if(!empty($arCurElement["PATH"])) {
		foreach($arCurElement["PATH"] as $path) {
			if(!empty($path["BREADCRUMB_TITLE"]))
				$APPLICATION->AddChainItem($path["BREADCRUMB_TITLE"], $path["~SECTION_PAGE_URL"]);
			elseif(!empty($path["TITLE"]))
				$APPLICATION->AddChainItem($path["TITLE"], $path["~SECTION_PAGE_URL"]);
		}
		unset($path);
	}

	if($arParams["ADD_ELEMENT_CHAIN"] == "Y") {
		if(!empty($arCurElement["PROPERTY_BREADCRUMB_TITLE_VALUE"]))
			$APPLICATION->AddChainItem($arCurElement["PROPERTY_BREADCRUMB_TITLE_VALUE"], $arCurElement['~DETAIL_PAGE_URL']);
		elseif(!empty($arCurElement["TITLE"]))
			$APPLICATION->AddChainItem($arCurElement["TITLE"], $arCurElement['~DETAIL_PAGE_URL']);
	}
	
	//ELEMENT_META_PROPERTY//
	if(!empty($arCurElement["TITLE"]))
		$APPLICATION->AddHeadString("<meta property='og:title' content='".$arCurElement["TITLE"]."' />", true);
	if(!empty($arCurElement["PREVIEW_TEXT"]))
		$APPLICATION->AddHeadString("<meta property='og:description' content='".strip_tags($arCurElement["PREVIEW_TEXT"])."' />", true);
	
	$ogScheme = CMain::IsHTTPS() ? "https" : "http";
	$APPLICATION->AddHeadString("<meta property='og:url' content='".$ogScheme."://".SITE_SERVER_NAME.$APPLICATION->GetCurPage()."' />", true);
	if(is_array($arCurElement["DETAIL_PICTURE"])) {
		$APPLICATION->AddHeadString("<meta property='og:image' content='".$ogScheme."://".SITE_SERVER_NAME.$arCurElement["DETAIL_PICTURE"]["SRC"]."' />", true);
		$APPLICATION->AddHeadString("<meta property='og:image:width' content='".$arCurElement["DETAIL_PICTURE"]["WIDTH"]."' />", true);
		$APPLICATION->AddHeadString("<meta property='og:image:height' content='".$arCurElement["DETAIL_PICTURE"]["HEIGHT"]."' />", true);
		$APPLICATION->AddHeadString("<link rel='image_src' href='".$ogScheme."://".SITE_SERVER_NAME.$arCurElement["DETAIL_PICTURE"]["SRC"]."' />", true);
	}
}