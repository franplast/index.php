<?define("STOP_STATISTICS", true);
define("NOT_CHECK_PERMISSIONS", true);

$siteId = isset($_REQUEST["siteId"]) && is_string($_REQUEST["siteId"]) ? $_REQUEST["siteId"] : "";
$siteId = substr(preg_replace("/[^a-z0-9_]/i", "", $siteId), 0, 2);
if(!empty($siteId) && is_string($siteId)) {
	define("SITE_ID", $siteId);
}

if(!empty($_REQUEST["REQUEST_URI"]))
	$_SERVER["REQUEST_URI"] = $_REQUEST["REQUEST_URI"];

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();

if($request->isAjaxRequest()) {
	if($request->get("action") == "changeSectionLink") {
		$APPLICATION->ShowAjaxHead();

		$signer = new Bitrix\Main\Security\Sign\Signer;
		$parameters = unserialize(base64_decode($signer->unsign($request->get("parameters"), "news.detail")));
		
		$productsIds = unserialize(base64_decode($signer->unsign($request->get("productsIds"), "news.detail")));
		if(!empty($productsIds))
			$GLOBALS["arObjectsProdFilter"]["ID"] = $productsIds;
		
		$sectionID = intval($request->get("sectionId"));
		if($sectionID > 0)
			$GLOBALS["arObjectsProdFilter"]["SECTION_ID"] = $sectionID;

        CModule::IncludeModule("iblock");

        $rsIblock = CIBlock::GetList(
            Array(),
            Array(
                'TYPE' => "catalog",
                'SITE_ID' => SITE_ID,
                'ACTIVE' => 'Y',
            ), true
        );

        $arResult["IBLOCKS_IDS"] = [];

        while($arIblock = $rsIblock->Fetch()) {
            $GLOBALS["arObjectsProdFilter"]["IBLOCK_ID"][] = $arIblock["ID"];
        }

        $APPLICATION->IncludeComponent(
            "zs:catalog.section",
            "",
            Array(
                "ACTION_VARIABLE" => "action",
                "ADD_PROPERTIES_TO_BASKET" => "N",
                "ADD_SECTIONS_CHAIN" => "N",
                "ADD_TO_BASKET_ACTION" => "ADD",
                "AJAX_MODE" => "N",
                "AJAX_OPTION_ADDITIONAL" => "",
                "AJAX_OPTION_HISTORY" => "N",
                "AJAX_OPTION_JUMP" => "N",
                "AJAX_OPTION_STYLE" => "Y",
                "BACKGROUND_IMAGE" => "-",
                "BASKET_URL" => $parameters["BASKET_URL"],
                "BROWSER_TITLE" => "-",
                "CACHE_FILTER" => "N",
                "CACHE_GROUPS" => "N",
                "CACHE_TIME" => $parameters["CACHE_TIME"],
                "CACHE_TYPE" => "A",
                "COMPARE_NAME" => $parameters["COMPARE_NAME"],
                "COMPARE_PATH" => $parameters["COMPARE_PATH"],
                "COMPATIBLE_MODE" => "N",
                "CONVERT_CURRENCY" => "N",
                "CURRENCY_ID" => $parameters["CURRENCY_ID"],
                "CUSTOM_CURRENT_PAGE" => $parameters["CUSTOM_CURRENT_PAGE"],
                "CUSTOM_FILTER" => "",
                "DETAIL_URL" => "",
                "DISABLE_INIT_JS_IN_COMPONENT" => "N",
                "DISPLAY_BOTTOM_PAGER" => "Y",
                "DISPLAY_COMPARE" => "N",
                "DISPLAY_TOP_PAGER" => "N",
                "ELEMENT_SORT_FIELD" => $parameters["ELEMENT_SORT_FIELD"],
                "ELEMENT_SORT_FIELD2" => $parameters["ELEMENT_SORT_FIELD2"],
                "ELEMENT_SORT_ORDER" => $parameters["ELEMENT_SORT_ORDER"],
                "ELEMENT_SORT_ORDER2" => $parameters["ELEMENT_SORT_ORDER2"],
                "FILTER_NAME" => "arObjectsProdFilter",
                "HIDE_NOT_AVAILABLE" => "Y",
                "HIDE_NOT_AVAILABLE_OFFERS" => "Y",
                "IBLOCK_ID" => $parameters["IBLOCK_ID"],
                "IBLOCK_TYPE" => "catalog",
                "INCLUDE_SUBSECTIONS" => "Y",
                "LAZY_LOAD" => "Y",
                "LINE_ELEMENT_COUNT" => "4",
                "LOAD_ON_SCROLL" => "N",
                "MESSAGE_404" => "",
                "MESS_BTN_ADD_TO_BASKET" => $parameters["MESS_BTN_ADD_TO_BASKET"],
                "MESS_BTN_BUY" => $parameters["MESS_BTN_BUY"],
                "MESS_BTN_COMPARE" => $parameters["MESS_BTN_COMPARE"],
                "MESS_BTN_DETAIL" => $parameters["MESS_BTN_DETAIL"],
                "MESS_BTN_LAZY_LOAD" => "Показать ещё",
                "MESS_BTN_SUBSCRIBE" => $parameters["MESS_BTN_SUBSCRIBE"],
                "MESS_NOT_AVAILABLE" => $parameters["MESS_NOT_AVAILABLE"],
                "MESS_RELATIVE_QUANTITY_FEW" => $parameters["MESS_RELATIVE_QUANTITY_FEW"],
                "MESS_RELATIVE_QUANTITY_MANY" => $parameters["MESS_RELATIVE_QUANTITY_MANY"],
                "MESS_SHOW_MAX_QUANTITY" => $parameters["MESS_SHOW_MAX_QUANTITY"],
                "META_DESCRIPTION" => "-",
                "META_KEYWORDS" => "-",
                "OFFERS_CART_PROPERTIES" => $parameters["OFFERS_CART_PROPERTIES"],
                "OFFERS_FIELD_CODE" => array(),
                "OFFERS_LIMIT" => "0",
                "OFFERS_PROPERTY_CODE" => $parameters["OFFERS_PROPERTY_CODE"],
                "OFFERS_SORT_FIELD" => $parameters["OFFERS_SORT_FIELD"],
                "OFFERS_SORT_FIELD2" => $parameters["OFFERS_SORT_FIELD2"],
                "OFFERS_SORT_ORDER" => $parameters["OFFERS_SORT_ORDER"],
                "OFFERS_SORT_ORDER2" => $parameters["OFFERS_SORT_ORDER2"],
                "OFFER_TREE_PROPS" => $parameters["OFFER_TREE_PROPS"],
                "PAGER_BASE_LINK_ENABLE" => "N",
                "PAGER_DESC_NUMBERING" => "N",
                "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                "PAGER_SHOW_ALL" => "N",
                "PAGER_SHOW_ALWAYS" => "N",
                "PAGER_TEMPLATE" => "arrows",
                "PAGER_TITLE" => "",
                "PAGE_ELEMENT_COUNT" => "16",
                "PARTIAL_PRODUCT_PROPERTIES" => "N",
                "PRICE_CODE" => array(),
                "PRICE_VAT_INCLUDE" => "N",
                "PRODUCT_DISPLAY_MODE" => $parameters["PRODUCT_DISPLAY_MODE"],
                "PRODUCT_ID_VARIABLE" => "id",
                "PRODUCT_PROPERTIES" => array(),
                "PRODUCT_PROPS_VARIABLE" => "prop",
                "PRODUCT_QUANTITY_VARIABLE" => "quantity",
                "PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false}]",
                "PRODUCT_SUBSCRIPTION" => "N",
                "PROPERTY_CODE" => array("", ""),
                "RCM_PROD_ID" => "",
                "RCM_TYPE" => "personal",
                "RELATIVE_QUANTITY_FACTOR" => $parameters["RELATIVE_QUANTITY_FACTOR"],
                "REVIEWS_IBLOCK_ID" => $parameters["REVIEWS_IBLOCK_ID"],
                "REVIEWS_IBLOCK_TYPE" => $parameters["REVIEWS_IBLOCK_TYPE"],
                "SECTION_CODE" => "",
                "SECTION_ID" => "",
                "SECTION_ID_VARIABLE" => "SECTION_ID",
                "SECTION_URL" => "",
                "SECTION_USER_FIELDS" => array("", ""),
                "SEF_MODE" => "N",
                "SET_BROWSER_TITLE" => "N",
                "SET_LAST_MODIFIED" => "N",
                "SET_META_DESCRIPTION" => "N",
                "SET_META_KEYWORDS" => "N",
                "SET_STATUS_404" => "N",
                "SET_TITLE" => "N",
                "SHOW_404" => "N",
                "SHOW_ALL_WO_SECTION" => "Y",
                "SHOW_DISCOUNT_PERCENT" => "N",
                "SHOW_FROM_SECTION" => "N",
                "SHOW_MAX_QUANTITY" => "N",
                "SHOW_OLD_PRICE" => "N",
                "SHOW_PRICE_COUNT" => $parameters["SHOW_PRICE_COUNT"]?"Y":"N",
                "USE_ENHANCED_ECOMMERCE" => "N",
                "USE_MAIN_ELEMENT_SECTION" => "N",
                "USE_PRICE_COUNT" => "N",
                "USE_PRODUCT_QUANTITY" => "N",
                "USE_REVIEW" => "N"
            )
        );
	} elseif($request->get("action") == "workingHoursToday") {
		$timezone = $request->get("timezone");
		if(!empty($timezone))
			$currentDateTime = strtotime(gmdate("Y-m-d H:i", strtotime($timezone." hours")));
		else
			$currentDateTime = time() + CTimeZone::GetOffset();	
		
		$workingHours = $request->get("workingHours");
		if(!empty($workingHours) && SITE_CHARSET != "utf-8")
			$workingHours = Bitrix\Main\Text\Encoding::convertEncoding($workingHours, "utf-8", SITE_CHARSET);
		
		if(!empty($currentDateTime) && !empty($workingHours)) {
			$currentDay = strtoupper(date("D", $currentDateTime));
			$arCurDay = $workingHours[$currentDay];
			if(!empty($arCurDay)) {			
				$arWorkingHoursToday[$currentDay] = array(
					"WORK_START" => strtotime($arCurDay["WORK_START"]) ? $arCurDay["WORK_START"] : "",
					"WORK_END" => strtotime($arCurDay["WORK_END"]) ? $arCurDay["WORK_END"] : "",
					"BREAK_START" => strtotime($arCurDay["BREAK_START"]) ? $arCurDay["BREAK_START"] : "",
					"BREAK_END" => strtotime($arCurDay["BREAK_END"]) ? $arCurDay["BREAK_END"] : ""
				);
				
				$currentDate = date("Y-m-d", $currentDateTime);
					
				$workStart = strtotime($arCurDay["WORK_START"]);
				$workStartDateTime = strtotime($currentDate." ".$arCurDay["WORK_START"]);
				$workEnd = strtotime($arCurDay["WORK_END"]);
					
				$breakStart = strtotime($arCurDay["BREAK_START"]);
				$breakStartDateTime = strtotime($currentDate." ".$arCurDay["BREAK_START"]);
				$breakEnd = strtotime($arCurDay["BREAK_END"]);

				if($workStart && $workEnd) {
					if($workStart < $workEnd) {				
						$workEndDateTime = strtotime($currentDate." ".$arCurDay["WORK_END"]);
						$prevDayWorkEndDateTime = strtotime($currentDate." ".$arCurDay["WORK_END"]." -1 days");

						$breakEndDateTime = strtotime($currentDate." ".$arCurDay["BREAK_END"]);
						$prevDayBreakEndDateTime = strtotime($currentDate." ".$arCurDay["BREAK_END"]." -1 days");
					} elseif($workStart > $workEnd) {				
						$workEndDateTime = strtotime($currentDate." ".$arCurDay["WORK_END"]." +1 days");
						$prevDayWorkEndDateTime = strtotime($currentDate." ".$arCurDay["WORK_END"]);

						$breakEndDateTime = strtotime($currentDate." ".$arCurDay["BREAK_END"]." +1 days");
						$prevDayBreakEndDateTime = strtotime($currentDate." ".$arCurDay["BREAK_END"]);
					} else {
						$arWorkingHoursToday[$currentDay]["STATUS"] = "OPEN";
					}
				} else {
					$arWorkingHoursToday[$currentDay]["STATUS"] = "CLOSED";
				}

				if(!$arWorkingHoursToday[$currentDay]["STATUS"]) {
					if($workStartDateTime && $workEndDateTime) {
						if($currentDateTime >= $workStartDateTime && $currentDateTime < $workEndDateTime) {
							$arWorkingHoursToday[$currentDay]["STATUS"] = "OPEN";					
							if($breakStartDateTime && $breakEndDateTime)
								if($currentDateTime >= $breakStartDateTime && $currentDateTime < $breakEndDateTime)
									$arWorkingHoursToday[$currentDay]["STATUS"] = "CLOSED";					
						} elseif($currentDateTime < $workStartDateTime && $currentDateTime < $prevDayWorkEndDateTime) {
							$arWorkingHoursToday[$currentDay]["STATUS"] = "OPEN";
							if($breakStartDateTime && $breakEndDateTime)
								if($currentDateTime < $breakStartDateTime && $currentDateTime < $prevDayBreakEndDateTime)
									$arWorkingHoursToday[$currentDay]["STATUS"] = "CLOSED";
						} else {
							$arWorkingHoursToday[$currentDay]["STATUS"] = "CLOSED";
						}
					}
				}
			}
			unset($arCurDay, $currentDay);
		}
		unset($currentDateTime);

		if(Bitrix\Main\Loader::includeModule("iblock")) {
			Bitrix\Iblock\Component\Base::sendJsonAnswer(array(
				"today" => !empty($arWorkingHoursToday) ? $arWorkingHoursToday : false
			));
		}
	}
}