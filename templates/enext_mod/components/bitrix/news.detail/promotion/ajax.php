<?define('STOP_STATISTICS', true);
define('NOT_CHECK_PERMISSIONS', true);

$siteId = isset($_REQUEST['siteId']) && is_string($_REQUEST['siteId']) ? $_REQUEST['siteId'] : '';
$siteId = substr(preg_replace('/[^a-z0-9_]/i', '', $siteId), 0, 2);
if(!empty($siteId) && is_string($siteId)) {
	define('SITE_ID', $siteId);
}

if(!empty($_REQUEST["REQUEST_URI"]))
	$_SERVER["REQUEST_URI"] = $_REQUEST["REQUEST_URI"];

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

$APPLICATION->ShowAjaxHead();

$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();
if($request->isAjaxRequest() && $request->get("action") == "changeSectionLink") {
	$signer = new Bitrix\Main\Security\Sign\Signer;
	$parameters = unserialize(base64_decode($signer->unsign($request->get("parameters"), "news.detail")));
	
	$productIds = $request->get("productIds");	
	if(!empty($productIds))
		$GLOBALS["arPromoProdFilter"]["ID"] = $productIds;
	
	$sectionID = intval($request->get("sectionId"));
	if($sectionID > 0)
		$GLOBALS["arPromoProdFilter"]["SECTION_ID"] = $sectionID;
	
	$APPLICATION->IncludeComponent("bitrix:catalog.section", ".default", 
		array(
			"COMPONENT_TEMPLATE" => ".default",
			"IBLOCK_TYPE" => $parameters["IBLOCK_TYPE"],
			"IBLOCK_ID" => $parameters["IBLOCK_ID"],
			"SECTION_ID" => "",
			"SECTION_CODE" => "",
			"SECTION_USER_FIELDS" => array(),
			"FILTER_NAME" => "arPromoProdFilter",
			"INCLUDE_SUBSECTIONS" => $parameters["INCLUDE_SUBSECTIONS"],
			"SHOW_ALL_WO_SECTION" => "Y",
			"CUSTOM_FILTER" => "",
			"HIDE_NOT_AVAILABLE" => $parameters["HIDE_NOT_AVAILABLE"],
			"HIDE_NOT_AVAILABLE_OFFERS" => $parameters["HIDE_NOT_AVAILABLE_OFFERS"],
			"ELEMENT_SORT_FIELD" => $parameters["ELEMENT_SORT_FIELD"],
			"ELEMENT_SORT_ORDER" => $parameters["ELEMENT_SORT_ORDER"],
			"ELEMENT_SORT_FIELD2" => $parameters["ELEMENT_SORT_FIELD2"],
			"ELEMENT_SORT_ORDER2" => $parameters["ELEMENT_SORT_ORDER2"],
			"OFFERS_SORT_FIELD" => $parameters["OFFERS_SORT_FIELD"],
			"OFFERS_SORT_ORDER" => $parameters["OFFERS_SORT_ORDER"],
			"OFFERS_SORT_FIELD2" => $parameters["OFFERS_SORT_FIELD2"],
			"OFFERS_SORT_ORDER2" => $parameters["OFFERS_SORT_ORDER2"],
			"PAGE_ELEMENT_COUNT" => "8",
			"LINE_ELEMENT_COUNT" => "4",
			"PROPERTY_CODE" => array(),
			"OFFERS_FIELD_CODE" => array(),
			"OFFERS_PROPERTY_CODE" => $parameters["OFFERS_PROPERTY_CODE"],
			"OFFERS_LIMIT" => "0",
			"BACKGROUND_IMAGE" => "-",
			"PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false}]",
			"PRODUCT_DISPLAY_MODE" => $parameters["PRODUCT_DISPLAY_MODE"],
			"OFFER_TREE_PROPS" => $parameters["OFFER_TREE_PROPS"],
			"PRODUCT_SUBSCRIPTION" => $parameters["PRODUCT_SUBSCRIPTION"],
			"SHOW_DISCOUNT_PERCENT" => $parameters["SHOW_DISCOUNT_PERCENT"],
			"SHOW_OLD_PRICE" => $parameters["SHOW_OLD_PRICE"],
			"SHOW_MAX_QUANTITY" => $parameters["SHOW_MAX_QUANTITY"],
			"MESS_SHOW_MAX_QUANTITY" => $parameters["MESS_SHOW_MAX_QUANTITY"],
			"RELATIVE_QUANTITY_FACTOR" => $parameters["RELATIVE_QUANTITY_FACTOR"],
			"MESS_RELATIVE_QUANTITY_MANY" => $parameters["MESS_RELATIVE_QUANTITY_MANY"],
			"MESS_RELATIVE_QUANTITY_FEW" => $parameters["MESS_RELATIVE_QUANTITY_FEW"],
			"MESS_BTN_BUY" => $parameters["MESS_BTN_BUY"],
			"MESS_BTN_ADD_TO_BASKET" => $parameters["MESS_BTN_ADD_TO_BASKET"],
			"MESS_BTN_SUBSCRIBE" => $parameters["MESS_BTN_SUBSCRIBE"],
			"MESS_BTN_DETAIL" => $parameters["MESS_BTN_DETAIL"],
			"MESS_NOT_AVAILABLE" => $parameters["MESS_NOT_AVAILABLE"],
			"RCM_TYPE" => "personal",
			"RCM_PROD_ID" => "",
			"SHOW_FROM_SECTION" => "N",
			"SECTION_URL" => "",
			"DETAIL_URL" => "",
			"SECTION_ID_VARIABLE" => "SECTION_ID",
			"SEF_MODE" => "N",
			"AJAX_MODE" => "N",
			"AJAX_OPTION_JUMP" => "N",
			"AJAX_OPTION_STYLE" => "Y",
			"AJAX_OPTION_HISTORY" => "N",
			"AJAX_OPTION_ADDITIONAL" => "",
			"CACHE_TYPE" => $parameters["CACHE_TYPE"],
			"CACHE_TIME" => $parameters["CACHE_TIME"],
			"CACHE_GROUPS" => $parameters["CACHE_GROUPS"],
			"SET_TITLE" => "N",
			"SET_BROWSER_TITLE" => "N",
			"BROWSER_TITLE" => "-",
			"SET_META_KEYWORDS" => "N",
			"META_KEYWORDS" => "-",
			"SET_META_DESCRIPTION" => "N",
			"META_DESCRIPTION" => "-",
			"SET_LAST_MODIFIED" => "N",
			"USE_MAIN_ELEMENT_SECTION" => $parameters["USE_MAIN_ELEMENT_SECTION"],
			"CUSTOM_CURRENT_PAGE" => $parameters["CUSTOM_CURRENT_PAGE"],
			"ADD_SECTIONS_CHAIN" => "N",
			"CACHE_FILTER" => $parameters["CACHE_FILTER"],
			"USE_REVIEW" => $parameters["USE_REVIEW"],
			"REVIEWS_IBLOCK_TYPE" => $parameters["REVIEWS_IBLOCK_TYPE"],
			"REVIEWS_IBLOCK_ID" => $parameters["REVIEWS_IBLOCK_ID"],
			"ACTION_VARIABLE" => "action",
			"PRODUCT_ID_VARIABLE" => "id",								
			"PRICE_CODE" => $parameters["PRICE_CODE"],
			"USE_PRICE_COUNT" => $parameters["USE_PRICE_COUNT"],
			"SHOW_PRICE_COUNT" => $parameters["SHOW_PRICE_COUNT"] ? "Y" : "N",
			"PRICE_VAT_INCLUDE" => $parameters["PRICE_VAT_INCLUDE"],
			"CONVERT_CURRENCY" => $parameters["CONVERT_CURRENCY"],
			"CURRENCY_ID" => $parameters["CURRENCY_ID"],
			"BASKET_URL" => $parameters["BASKET_URL"],
			"USE_PRODUCT_QUANTITY" => $parameters["USE_PRODUCT_QUANTITY"],
			"PRODUCT_QUANTITY_VARIABLE" => "quantity",
			"ADD_PROPERTIES_TO_BASKET" => $parameters["ADD_PROPERTIES_TO_BASKET"],
			"PRODUCT_PROPS_VARIABLE" => "prop",
			"PARTIAL_PRODUCT_PROPERTIES" => $parameters["PARTIAL_PRODUCT_PROPERTIES"],
			"PRODUCT_PROPERTIES" => $parameters["PRODUCT_PROPERTIES"],
			"OFFERS_CART_PROPERTIES" => $parameters["OFFERS_CART_PROPERTIES"],
			"ADD_TO_BASKET_ACTION" => $parameters["ADD_TO_BASKET_ACTION"],
			"DISPLAY_COMPARE" => $parameters["DISPLAY_COMPARE"],
			"COMPARE_PATH" => $parameters["COMPARE_PATH"],
			"MESS_BTN_COMPARE" => $parameters["MESS_BTN_COMPARE"],
			"COMPARE_NAME" => $parameters["COMPARE_NAME"],
			"USE_ENHANCED_ECOMMERCE" => "N",
			"PAGER_TEMPLATE" => "arrows",
			"DISPLAY_TOP_PAGER" => "N",
			"DISPLAY_BOTTOM_PAGER" => "N",
			"PAGER_TITLE" => "",
			"PAGER_SHOW_ALWAYS" => "N",
			"PAGER_DESC_NUMBERING" => "N",
			"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
			"PAGER_SHOW_ALL" => "N",
			"PAGER_BASE_LINK_ENABLE" => "N",
			"LAZY_LOAD" => "Y",
			"LOAD_ON_SCROLL" => "N",
			"SET_STATUS_404" => "N",
			"SHOW_404" => "N",
			"MESSAGE_404" => "",
			"COMPATIBLE_MODE" => "N",
			"DISABLE_INIT_JS_IN_COMPONENT" => "N"
		),
		false
	);
}