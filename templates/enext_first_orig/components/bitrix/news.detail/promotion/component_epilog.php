<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("promotions-detail");

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$mainId = $this->GetEditAreaId($arResult['ID']);
$obName = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $mainId);

$currentDateTime = time() + CTimeZone::GetOffset();

$itemCompleted = false;
if(!empty($arResult["ACTIVE_TO"]) && $currentDateTime >= strtotime($arResult["ACTIVE_TO"])) {
	$itemCompleted = true;
}

$showTimer = false;?>

<div class="promotions-detail" id="<?=$mainId?>">
	<?//TABS//?>
	<div class="promotions-detail-tabs-container">
		<div class="promotions-detail-tabs-block" data-entity="tabs">
			<div class="promotions-detail-tabs-scroll">
				<ul class="promotions-detail-tabs-list">
					<li class="promotions-detail-tab active" data-entity="tab" data-value="promotion"><?=Loc::getMessage("PROMOTIONS_ITEM_DETAIL_TAB_PROMOTION")?></li>
					<?if(!$itemCompleted && !empty($arResult["PRODUCTS_IDS"])) {?>
						<li class="promotions-detail-tab" data-entity="tab" data-value="products"><?=Loc::getMessage("PROMOTIONS_ITEM_DETAIL_TAB_PRODUCTS")?><span><?=count($arResult["PRODUCTS_IDS"])?></span></li>
					<?}
					if(!empty($arResult["DETAIL_TEXT"])) {?>
						<li class="promotions-detail-tab" data-entity="tab" data-value="description"><?=Loc::getMessage("PROMOTIONS_ITEM_DETAIL_TAB_DESCRIPTION")?></li>
					<?}
					if(!$itemCompleted && !empty($arResult["OBJECT"])) {?>
						<li class="promotions-detail-tab" data-entity="tab" data-value="object"><?=Loc::getMessage("PROMOTIONS_ITEM_DETAIL_TAB_OBJECT")?></li>
					<?}?>
				</ul>
			</div>
		</div>
	</div>
	<div class="promotions-detail-tabs-content">
		<?//ITEM//?>
		<div class="promotions-item-detail-container" data-entity="tab-container" data-value="promotion">
			<div class="promotions-item-detail<?=($itemCompleted ? ' promotions-item-detail-completed' : '')?>">
				<?//ITEM_PIC//?>
				<div class="promotions-item-detail-pic-container">
					<div class="promotions-item-detail-pic lazy-load"<?=(is_array($arResult["DETAIL_PICTURE"]) ? " data-src='".$arResult["DETAIL_PICTURE"]["SRC"]."'" : "")?>></div>
				</div>
				<?//ITEM_BLOCK//?>
				<div class="promotions-item-detail-block">
					<div class="promotions-item-detail-title"><?=$arResult["NAME"]?></div>
					<div class="promotions-item-detail-date">
						<?if(!$itemCompleted) {
							echo Loc::getMessage("PROMOTIONS_ITEM_DETAIL_RUNNING")." ".(!empty($arResult["DISPLAY_ACTIVE_TO"]) ? Loc::getMessage("PROMOTIONS_ITEM_DETAIL_UNTIL")." ".$arResult["DISPLAY_ACTIVE_TO"] : Loc::getMessage("PROMOTIONS_ITEM_DETAIL_ALWAYS"));
						} else {
							echo Loc::getMessage("PROMOTIONS_ITEM_DETAIL_COMPLETED")." ".$arResult["DISPLAY_ACTIVE_TO"];
						}?>
					</div>
				</div>
				<?//ITEM_ICONS//?>
				<div class="promotions-item-detail-icons">
					<div class="promotions-item-detail-icon">
						<?//ITEM_MARKER//
						if(!empty($arResult["MARKER"])) {						
							foreach($arResult["MARKER"] as $key => $arMarker) {
								if($key <= 2) {?>
									<div class="promotions-item-detail-marker-container">
										<div class="promotions-item-detail-marker<?=(!empty($arMarker['FONT_SIZE']) ? ' promotions-item-detail-marker-'.$arMarker['FONT_SIZE'] : '')?>"<?=(!empty($arMarker["BACKGROUND_1"]) && !empty($arMarker["BACKGROUND_2"]) ? " style='background: ".$arMarker["BACKGROUND_2"]."; background: -webkit-linear-gradient(left, ".$arMarker["BACKGROUND_1"].", ".$arMarker["BACKGROUND_2"]."); background: -moz-linear-gradient(left, ".$arMarker["BACKGROUND_1"].", ".$arMarker["BACKGROUND_2"]."); background: -o-linear-gradient(left, ".$arMarker["BACKGROUND_1"].", ".$arMarker["BACKGROUND_2"]."); background: -ms-linear-gradient(left, ".$arMarker["BACKGROUND_1"].", ".$arMarker["BACKGROUND_2"]."); background: linear-gradient(to right, ".$arMarker["BACKGROUND_1"].", ".$arMarker["BACKGROUND_2"].");'" : (!empty($arMarker["BACKGROUND_1"]) && empty($arMarker["BACKGROUND_2"]) ? " style='background: ".$arMarker["BACKGROUND_1"].";'" : (empty($arMarker["BACKGROUND_1"]) && !empty($arMarker["BACKGROUND_2"]) ? " style='background: ".$arMarker["BACKGROUND_2"].";'" : "")))?>><?=(!empty($arMarker["ICON"]) ? "<i class='".$arMarker["ICON"]."'></i>" : "")?><span><?=$arMarker["NAME"]?></span></div>
									</div>
								<?} else {
									break;
								}
							}
							unset($key, $arMarker);
						}?>
					</div>
					<div class="promotions-item-detail-icon">
						<?//ITEM_TIMER//
						if(!$itemCompleted) {
							if($arResult["SHOW_TIMER"] != false && !empty($arResult["ACTIVE_TO"])) {
								$showTimer = true;?>
								<div class="promotions-item-detail-timer"><i class="icon-clock"></i><span data-entity="timer"></span></div>						
							<?} elseif($arResult["SHOW_TIMER"] == false && !empty($arResult["ACTIVE_TO"])) {					
								$daysLeft = ceil(abs(strtotime($arResult["ACTIVE_TO"]) - $currentDateTime) / 86400);					
								if($daysLeft > 1 && $daysLeft <= 3) {?>
									<div class="promotions-item-detail-timer"><i class="icon-clock"></i><span><?=Loc::getMessage("PROMOTIONS_ITEM_DETAIL_DAYS_LEFT", array("#DAYS_COUNT#" => $daysLeft))?></span></div>
								<?} elseif($daysLeft == 1) {
									$hoursLeft = floor((strtotime($arResult["ACTIVE_TO"]) - $currentDateTime) / 3600);
									if($hoursLeft >= 3) {?>
										<div class="promotions-item-detail-timer"><i class="icon-clock"></i><span><?=Loc::getMessage("PROMOTIONS_ITEM_DETAIL_DAY_LEFT", array("#DAYS_COUNT#" => $daysLeft))?></span></div>
									<?} else {
										$showTimer = true;?>
										<div class="promotions-item-detail-timer"><i class="icon-clock"></i><span data-entity="timer"></span></div>
									<?}
								}
							}
						} else {?>
							<div class="promotions-item-detail-timer"><i class="icon-clock"></i><span><?=Loc::getMessage("PROMOTIONS_ITEM_DETAIL_COMPLETED")?></span></div>
						<?}?>
					</div>
				</div>
			</div>
			<?//ITEM_PREVIEW_TEXT//
			if(!empty($arResult["PREVIEW_TEXT"])) {?>
				<div class="promotions-item-detail-preview-text"><?=$arResult["PREVIEW_TEXT"]?></div>
			<?}?>
		</div>
		<?//SECTIONS_PRODUCTS//
		if(!$itemCompleted && !empty($arResult["PRODUCTS_IDS"])) {?>			
			<div class="promotions-detail-products-container" data-entity="tab-container" data-value="products">
				<div class="h2"><?=Loc::getMessage("PROMOTIONS_ITEM_DETAIL_PRODUCTS")?></div>
				<?//SECTIONS//?>
				<div class="promotions-detail-sections-links" data-entity="links">
					<div class="promotions-detail-section-link active" data-section-id="0"><?=Loc::getMessage("PROMOTIONS_ITEM_DETAIL_PRODUCTS_SECTIONS_ALL")?><span><?=count($arResult["PRODUCTS_IDS"])?></span></div>
					<?if(!empty($arResult["SECTIONS"])) {
						foreach($arResult["SECTIONS"] as $arSection) {?>
							<div class="promotions-detail-section-link" data-section-id="<?=$arSection['ID']?>"><?=$arSection["NAME"]?><span><?=$arSection["COUNT"]?></span></div>
						<?}
						unset($arSection);
					}?>
				</div>
				<div class="promotions-detail-products">
					<?//PRODUCTS//
					$GLOBALS["arPromoProdFilter"] = array("ID" => $arResult["PRODUCTS_IDS"]);?>
					<?$APPLICATION->IncludeComponent("bitrix:catalog.section", ".default", 
						array(
							"COMPONENT_TEMPLATE" => ".default",
							"IBLOCK_TYPE" => $arParams["CATALOG_IBLOCK_TYPE"],
							"IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"],
							"SECTION_ID" => "",
							"SECTION_CODE" => "",
							"SECTION_USER_FIELDS" => array(),
							"FILTER_NAME" => "arPromoProdFilter",
							"INCLUDE_SUBSECTIONS" => $arParams["CATALOG_INCLUDE_SUBSECTIONS"],
							"SHOW_ALL_WO_SECTION" => "Y",
							"CUSTOM_FILTER" => "",
							"HIDE_NOT_AVAILABLE" => $arParams["CATALOG_HIDE_NOT_AVAILABLE"],
							"HIDE_NOT_AVAILABLE_OFFERS" => $arParams["CATALOG_HIDE_NOT_AVAILABLE_OFFERS"],
							"ELEMENT_SORT_FIELD" => $arParams["CATALOG_ELEMENT_SORT_FIELD"],
							"ELEMENT_SORT_ORDER" => $arParams["CATALOG_ELEMENT_SORT_ORDER"],
							"ELEMENT_SORT_FIELD2" => $arParams["CATALOG_ELEMENT_SORT_FIELD2"],
							"ELEMENT_SORT_ORDER2" => $arParams["CATALOG_ELEMENT_SORT_ORDER2"],
							"OFFERS_SORT_FIELD" => $arParams["CATALOG_OFFERS_SORT_FIELD"],
							"OFFERS_SORT_ORDER" => $arParams["CATALOG_OFFERS_SORT_ORDER"],
							"OFFERS_SORT_FIELD2" => $arParams["CATALOG_OFFERS_SORT_FIELD2"],
							"OFFERS_SORT_ORDER2" => $arParams["CATALOG_OFFERS_SORT_ORDER2"],
							"PAGE_ELEMENT_COUNT" => "8",
							"LINE_ELEMENT_COUNT" => "4",
							"PROPERTY_CODE" => array(),
							"OFFERS_FIELD_CODE" => array(),
							"OFFERS_PROPERTY_CODE" => $arParams["CATALOG_OFFERS_PROPERTY_CODE"],
							"OFFERS_LIMIT" => "0",
							"BACKGROUND_IMAGE" => "-",
							"PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false}]",
							"PRODUCT_DISPLAY_MODE" => $arParams["CATALOG_PRODUCT_DISPLAY_MODE"],
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
							"CACHE_TYPE" => $arParams["CACHE_TYPE"],
							"CACHE_TIME" => $arParams["CACHE_TIME"],
							"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
							"SET_TITLE" => "N",
							"SET_BROWSER_TITLE" => "N",
							"BROWSER_TITLE" => "-",
							"SET_META_KEYWORDS" => "N",
							"META_KEYWORDS" => "-",
							"SET_META_DESCRIPTION" => "N",
							"META_DESCRIPTION" => "-",
							"SET_LAST_MODIFIED" => "N",
							"USE_MAIN_ELEMENT_SECTION" => $arParams["CATALOG_USE_MAIN_ELEMENT_SECTION"],
							"CUSTOM_CURRENT_PAGE" => $arParams["CATALOG_CUSTOM_CURRENT_PAGE"],
							"ADD_SECTIONS_CHAIN" => "N",
							"CACHE_FILTER" => $arParams["CACHE_FILTER"],
							"USE_REVIEW" => $arParams["CATALOG_USE_REVIEW"],
							"REVIEWS_IBLOCK_TYPE" => $arParams["CATALOG_REVIEWS_IBLOCK_TYPE"],
							"REVIEWS_IBLOCK_ID" => $arParams["CATALOG_REVIEWS_IBLOCK_ID"],
							"ACTION_VARIABLE" => "action",
							"PRODUCT_ID_VARIABLE" => "id",								
							"PRICE_CODE" => $arParams["CATALOG_PRICE_CODE"],
							"USE_PRICE_COUNT" => $arParams["CATALOG_USE_PRICE_COUNT"],
							"SHOW_PRICE_COUNT" => $arParams["CATALOG_SHOW_PRICE_COUNT"] ? "Y" : "N",
							"PRICE_VAT_INCLUDE" => $arParams["CATALOG_PRICE_VAT_INCLUDE"],
							"CONVERT_CURRENCY" => $arParams["CATALOG_CONVERT_CURRENCY"],
							"CURRENCY_ID" => $arParams["CATALOG_CURRENCY_ID"],
							"BASKET_URL" => $arParams["CATALOG_BASKET_URL"],
							"USE_PRODUCT_QUANTITY" => $arParams["CATALOG_USE_PRODUCT_QUANTITY"],
							"PRODUCT_QUANTITY_VARIABLE" => "quantity",
							"ADD_PROPERTIES_TO_BASKET" => $arParams["CATALOG_ADD_PROPERTIES_TO_BASKET"],
							"PRODUCT_PROPS_VARIABLE" => "prop",
							"PARTIAL_PRODUCT_PROPERTIES" => $arParams["CATALOG_PARTIAL_PRODUCT_PROPERTIES"],
							"PRODUCT_PROPERTIES" => $arParams["CATALOG_PRODUCT_PROPERTIES"],
							"OFFERS_CART_PROPERTIES" => $arParams["CATALOG_OFFERS_CART_PROPERTIES"],
							"ADD_TO_BASKET_ACTION" => $arParams["CATALOG_ADD_TO_BASKET_ACTION"],
							"DISPLAY_COMPARE" => $arParams["CATALOG_DISPLAY_COMPARE"],
							"COMPARE_PATH" => $arParams["CATALOG_COMPARE_PATH"],
							"MESS_BTN_COMPARE" => $arParams["CATALOG_MESS_BTN_COMPARE"],
							"COMPARE_NAME" => $arParams["CATALOG_COMPARE_NAME"],
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
						$component,
						array("HIDE_ICONS" => "Y")
					);?>
				</div>
			</div>
		<?}
		//DETAIL_TEXT//
		if(!empty($arResult["DETAIL_TEXT"])) {?>
			<div class="promotions-detail-detail-text" data-entity="tab-container" data-value="description"><?=$arResult["DETAIL_TEXT"]?></div>
		<?}
		//OBJECT//
		if(!$itemCompleted && !empty($arResult["OBJECT"])) {?>
			<div class="promotions-detail-object-container" data-entity="tab-container" data-value="object">
				<div class="h2"><?=Loc::getMessage("PROMOTIONS_ITEM_DETAIL_OBJECT")?></div>
				<div class="promotions-detail-object">
					<?$GLOBALS["arPromoObjectFilter"] = array("ID" => $arResult["OBJECT"]["ID"]);?>
					<?$APPLICATION->IncludeComponent("bitrix:news.list", "objects",
						array(
							"IBLOCK_TYPE" => $arResult["OBJECT"]["IBLOCK_TYPE"],
							"IBLOCK_ID" => $arResult["OBJECT"]["IBLOCK_ID"],
							"NEWS_COUNT" => "1",
							"SORT_BY1" => "",
							"SORT_ORDER1" => "",
							"SORT_BY2" => "",
							"SORT_ORDER2" => "",
							"FILTER_NAME" => "arPromoObjectFilter",
							"FIELD_CODE" => array(),
							"PROPERTY_CODE" => $arParams["OBJECTS_PROPERTY_CODE"],
							"CHECK_DATES" => "Y",
							"DETAIL_URL" => "",
							"AJAX_MODE" => "",
							"AJAX_OPTION_SHADOW" => "",
							"AJAX_OPTION_JUMP" => "",
							"AJAX_OPTION_STYLE" => "",
							"AJAX_OPTION_HISTORY" => "",
							"CACHE_TYPE" => $arParams["CACHE_TYPE"],
							"CACHE_TIME" => $arParams["CACHE_TIME"],
							"CACHE_FILTER" => $arParams["CACHE_FILTER"],
							"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
							"PREVIEW_TRUNCATE_LEN" => "",
							"ACTIVE_DATE_FORMAT" => "",
							"DISPLAY_PANEL" => "",
							"SET_TITLE" => "N",
							"SET_BROWSER_TITLE" => "N",
							"SET_META_KEYWORDS" => "N",
							"SET_META_DESCRIPTION" => "N",
							"SET_STATUS_404" => "N",
							"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
							"ADD_SECTIONS_CHAIN" => "",
							"HIDE_LINK_WHEN_NO_DETAIL" => "",
							"PARENT_SECTION" => "",
							"PARENT_SECTION_CODE" => "",
							"DISPLAY_NAME" => "",
							"DISPLAY_DATE" => "",
							"DISPLAY_TOP_PAGER" => "N",
							"DISPLAY_BOTTOM_PAGER" => "N",
							"PAGER_SHOW_ALWAYS" => "",
							"PAGER_TEMPLATE" => "arrows",
							"PAGER_DESC_NUMBERING" => "",
							"PAGER_DESC_NUMBERING_CACHE_TIME" => "",
							"PAGER_SHOW_ALL" => "",
							"AJAX_OPTION_ADDITIONAL" => "",
							"SHOW_PROMOTIONS" => $arParams["OBJECTS_SHOW_PROMOTIONS"],
							"PROMOTIONS_IBLOCK_ID" => $arParams["IBLOCK_ID"],
							"CATALOG_IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"],
							"USE_REVIEW" => $arParams["OBJECTS_USE_REVIEW"],
							"REVIEWS_IBLOCK_ID" => $arParams["OBJECTS_REVIEWS_IBLOCK_ID"]
						),
						$component,
						array("HIDE_ICONS" => "Y")
					);?>
				</div>
			</div>
		<?}?>
	</div>
</div>

<?$arParamsCatalog = array(
	"IBLOCK_TYPE" => $arParams["CATALOG_IBLOCK_TYPE"],
	"IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"],
	"INCLUDE_SUBSECTIONS" => $arParams["CATALOG_INCLUDE_SUBSECTIONS"],
	"HIDE_NOT_AVAILABLE" => $arParams["CATALOG_HIDE_NOT_AVAILABLE"],
	"HIDE_NOT_AVAILABLE_OFFERS" => $arParams["CATALOG_HIDE_NOT_AVAILABLE_OFFERS"],
	"ELEMENT_SORT_FIELD" => $arParams["CATALOG_ELEMENT_SORT_FIELD"],
	"ELEMENT_SORT_ORDER" => $arParams["CATALOG_ELEMENT_SORT_ORDER"],
	"ELEMENT_SORT_FIELD2" => $arParams["CATALOG_ELEMENT_SORT_FIELD2"],
	"ELEMENT_SORT_ORDER2" => $arParams["CATALOG_ELEMENT_SORT_ORDER2"],
	"OFFERS_SORT_FIELD" => $arParams["CATALOG_OFFERS_SORT_FIELD"],
	"OFFERS_SORT_ORDER" => $arParams["CATALOG_OFFERS_SORT_ORDER"],
	"OFFERS_SORT_FIELD2" => $arParams["CATALOG_OFFERS_SORT_FIELD2"],
	"OFFERS_SORT_ORDER2" => $arParams["CATALOG_OFFERS_SORT_ORDER2"],		
	"OFFERS_PROPERTY_CODE" => $arParams["CATALOG_OFFERS_PROPERTY_CODE"],		
	"PRODUCT_DISPLAY_MODE" => $arParams["CATALOG_PRODUCT_DISPLAY_MODE"],
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
	"CACHE_TYPE" => $arParams["CACHE_TYPE"],
	"CACHE_TIME" => $arParams["CACHE_TIME"],
	"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
	"USE_MAIN_ELEMENT_SECTION" => $arParams["CATALOG_USE_MAIN_ELEMENT_SECTION"],
	"CUSTOM_CURRENT_PAGE" => $arParams["CATALOG_CUSTOM_CURRENT_PAGE"],
	"CACHE_FILTER" => $arParams["CACHE_FILTER"],	
	"USE_REVIEW" => $arParams["CATALOG_USE_REVIEW"],
	"REVIEWS_IBLOCK_TYPE" => $arParams["CATALOG_REVIEWS_IBLOCK_TYPE"],
	"REVIEWS_IBLOCK_ID" => $arParams["CATALOG_REVIEWS_IBLOCK_ID"],
	"PRICE_CODE" => $arParams["CATALOG_PRICE_CODE"],
	"USE_PRICE_COUNT" => $arParams["CATALOG_USE_PRICE_COUNT"],
	"SHOW_PRICE_COUNT" => $arParams["CATALOG_SHOW_PRICE_COUNT"],
	"PRICE_VAT_INCLUDE" => $arParams["CATALOG_PRICE_VAT_INCLUDE"],
	"CONVERT_CURRENCY" => $arParams["CATALOG_CONVERT_CURRENCY"],
	"CURRENCY_ID" => $arParams["CATALOG_CURRENCY_ID"],
	"BASKET_URL" => $arParams["CATALOG_BASKET_URL"],
	"USE_PRODUCT_QUANTITY" => $arParams["CATALOG_USE_PRODUCT_QUANTITY"],
	"ADD_PROPERTIES_TO_BASKET" => $arParams["CATALOG_ADD_PROPERTIES_TO_BASKET"],
	"PARTIAL_PRODUCT_PROPERTIES" => $arParams["CATALOG_PARTIAL_PRODUCT_PROPERTIES"],
	"PRODUCT_PROPERTIES" => $arParams["CATALOG_PRODUCT_PROPERTIES"],
	"OFFERS_CART_PROPERTIES" => $arParams["CATALOG_OFFERS_CART_PROPERTIES"],
	"ADD_TO_BASKET_ACTION" => $arParams["CATALOG_ADD_TO_BASKET_ACTION"],
	"DISPLAY_COMPARE" => $arParams["CATALOG_DISPLAY_COMPARE"],
	"COMPARE_PATH" => $arParams["CATALOG_COMPARE_PATH"],
	"MESS_BTN_COMPARE" => $arParams["CATALOG_MESS_BTN_COMPARE"],
	"COMPARE_NAME" => $arParams["CATALOG_COMPARE_NAME"]
);

$signer = new \Bitrix\Main\Security\Sign\Signer;
$signedParams = $signer->sign(base64_encode(serialize($arParamsCatalog)), "news.detail");

$arJSParams = array(				
	"CONFIG" => $signedParams,
	"ITEM" => array(
		"ACTIVE_TO" => !empty($arResult["ACTIVE_TO"]) ? ParseDateTime($arResult["ACTIVE_TO"], FORMAT_DATETIME) : "",
		"SHOW_TIMER" => $showTimer,
		"PRODUCT_IDS" => $arResult["PRODUCTS_IDS"]
	),
	"VISUAL" => array(
		"ID" => $mainId
	)
);?>

<script type="text/javascript">
	BX.message({		
		PROMOTIONS_ITEM_DETAIL_COMPLETED: "<?=GetMessageJS('PROMOTIONS_ITEM_DETAIL_COMPLETED');?>",		
		PROMOTION_TEMPLATE_PATH: "<?=$templateFolder?>"
	});
	var <?=$obName;?> = new JCNewsDetailPromo(<?=CUtil::PhpToJSObject($arJSParams, false, true);?>);
</script>

<?Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("promotions-detail", "");?>