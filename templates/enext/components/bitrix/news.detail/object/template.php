<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

$this->addExternalCss(SITE_TEMPLATE_PATH."/js/owlCarousel/owl.carousel.min.css");
$this->addExternalJS(SITE_TEMPLATE_PATH."/js/owlCarousel/owl.carousel.min.js");

global $arSettings;
$imgLazyLoad = $arSettings["LAZYLOAD"]["VALUE"] == "Y" ? true : false;

$mainId = $this->GetEditAreaId($arResult['ID']);
$obName = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $mainId);?>

<div class="objects-detail" id="<?=$mainId?>" itemscope itemtype="http://schema.org/Organization">
	<?//TABS//?>
	<div class="objects-detail-tabs-container">
		<div class="objects-detail-tabs-block" data-entity="tabs">
			<div class="objects-detail-tabs-scroll">
				<ul class="objects-detail-tabs-list">
					<li class="objects-detail-tab active" data-entity="tab" data-value="object"><?=Loc::getMessage("OBJECTS_ITEM_DETAIL_TAB_OBJECT")?></li>
					<?if(!empty($arResult["PROMOTIONS_IDS"])) {?>
						<li class="objects-detail-tab" data-entity="tab" data-value="promotions"><?=Loc::getMessage("OBJECTS_ITEM_DETAIL_TAB_PROMOTIONS")?><span><?=count($arResult["PROMOTIONS_IDS"])?></span></li>
					<?}
					if(!empty($arResult["PRODUCTS_IDS"])) {?>
						<li class="objects-detail-tab" data-entity="tab" data-value="products"><?=Loc::getMessage("OBJECTS_ITEM_DETAIL_TAB_PRODUCTS")?><span><?=count($arResult["PRODUCTS_IDS"])?></span></li>
					<?}
					if(!empty($arResult["TOUR_3D"])) {?>
						<li class="objects-detail-tab" data-entity="tab" data-value="3d-tour"><?=$arResult["TOUR_3D"]["NAME"]?></li>
					<?}
					if(!empty($arResult["AFFILIATES"]["VALUE"])) {?>
						<li class="objects-detail-tab" data-entity="tab" data-value="affiliates"><?=$arResult["AFFILIATES"]["NAME"]?><span><?=count($arResult["AFFILIATES"]["VALUE"])?></span></li>
					<?}
					if(!empty($arResult["DETAIL_TEXT"])) {?>
						<li class="objects-detail-tab" data-entity="tab" data-value="description"><?=Loc::getMessage("OBJECTS_ITEM_DETAIL_TAB_DESCRIPTION")?></li>
					<?}
					if(isset($arResult["REVIEWS_COUNT"])) {?>
						<li class="objects-detail-tab" data-entity="tab" data-value="reviews"><?=Loc::getMessage("OBJECTS_ITEM_DETAIL_TAB_REVIEWS")?><span><?=$arResult["REVIEWS_COUNT"]?></span></li>
					<?}?>
				</ul>
			</div>
		</div>
	</div>
	<div class="objects-detail-tabs-content">
		<?//ITEM//?>
		<div class="objects-item-detail" data-entity="tab-container" data-value="object">
			<div class="objects-item-detail-image">
				<?if(!empty($arResult["PREVIEW_PICTURE"])) {?>
					<img src="<?=$arResult['PREVIEW_PICTURE']['SRC']?>" width="<?=$arResult['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arResult['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arResult['PREVIEW_PICTURE']['ALT']?>" itemprop="logo" />
				<?} else {?>
					<img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo.png" width="120" height="120" alt="<?=$arResult['NAME']?>" />
				<?}?>
			</div>
			<div class="objects-item-detail-caption">					
				<div class="objects-item-detail-title" itemprop="name"><?=$arResult["NAME"]?></div>
				<?if(!empty($arResult["ADDRESS"])) {?>
					<div class="objects-item-detail-address" itemprop="address"><i class="icon-map-marker"></i><span><?=$arResult["ADDRESS"]?></span></div>
				<?}?>
				<div class="objects-item-detail-hours objects-item-detail-hours-hidden"></div>
				<?if(isset($arResult["REVIEWS_COUNT"])) {?>							
					<div class="objects-item-detail-rating">
						<?if($arResult["RATING_VALUE"] > 0) {?>
							<div class="objects-item-detail-rating-val"<?=($arResult["RATING_VALUE"] <= 4.4 ? " data-rate='".intval($arResult["RATING_VALUE"])."'" : "")?>><?=$arResult["RATING_VALUE"]?></div>
						<?}
						$arReviewsDeclension = new Bitrix\Main\Grid\Declension(Loc::getMessage("OBJECTS_ITEM_DETAIL_REVIEW"), Loc::getMessage("OBJECTS_ITEM_DETAIL_REVIEWS_1"), Loc::getMessage("OBJECTS_ITEM_DETAIL_REVIEWS_2"));?>
						<div class="objects-item-detail-rating-reviews-count"><?=($arResult["REVIEWS_COUNT"] > 0 ? $arResult["REVIEWS_COUNT"]." ".$arReviewsDeclension->get($arResult["REVIEWS_COUNT"]) : Loc::getMessage("OBJECTS_ITEM_DETAIL_NO_REVIEWS"))?></div>
						<?unset($arReviewsDeclension);?>
					</div>
					<?if($arResult["REVIEWS_COUNT"] > 0) {?>
						<span itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
							<meta itemprop="ratingValue" content="<?=$arResult['RATING_VALUE']?>" />
							<meta itemprop="reviewCount" content="<?=$arResult['REVIEWS_COUNT']?>" />
						</span>
					<?}
				}
				if(!empty($arResult["PREVIEW_TEXT"])) {?>
					<div class="objects-item-detail-descr" itemprop="description"><?=$arResult["PREVIEW_TEXT"]?></div>
				<?}
                    if(!empty($arResult["DELIVERY_METHODS"]) || !empty($arResult["PAYMENT_METHODS"])) {?>
					<div class="objects-item-detail-methods">
						<?if(!empty($arResult["DELIVERY_METHODS"])) {?>
							<div class="objects-item-detail-method"><i class="icon-delivery"></i>
                                <div>
                                <?if(is_array($arResult["DELIVERY_METHODS"])){
                                    if($arResult["DELIVERY_METHODS"]["TYPE"]=="HTML"){
                                        echo $arResult["DELIVERY_METHODS"]["TEXT"];
                                    }
                                    else {
                                        ?>
                                        <p><?=$arResult["DELIVERY_METHODS"]["TEXT"]?></p>
                                        <?
                                    }
                                }
                                else {
                                ?>
                                <p><?=$arResult["DELIVERY_METHODS"]?></p>
                                <?
                                }
                                ?>
                                </div>
                            </div>
						<?}
						if(!empty($arResult["DELIVERY_METHODS"]) && !empty($arResult["PAYMENT_METHODS"])) {?>
							<div class="objects-item-detail-method-sape"></div>
						<?}
						if(!empty($arResult["PAYMENT_METHODS"])) {?>
							<div class="objects-item-detail-method"><i class="icon-cards"></i>
                                <div>
                                <?if(is_array($arResult["PAYMENT_METHODS"])){
                                    if($arResult["PAYMENT_METHODS"]["TYPE"]=="HTML"){
                                        echo $arResult["PAYMENT_METHODS"]["TEXT"];
                                    }
                                    else {
                                        ?>
                                        <p><?=$arResult["PAYMENT_METHODS"]["TEXT"]?></p>
                                        <?
                                    }
                                }
                                else {
                                    ?>
                                    <p><?=$arResult["PAYMENT_METHODS"]?></p>
                                    <?
                                }
                                ?>
                                </div>
                            </div>
						<?}?>
					</div>
				<?}
				if(!empty($arResult["LINKS"])) {?>
					<div class="objects-item-detail-links">
						<?foreach($arResult["LINKS"]["VALUE"] as $key => $arLinkVal) {?>
							<a target="_blank" class="btn btn-default objects-item-detail-link" href="<?=$arLinkVal?>"><?=(!empty($arResult["LINKS"]["DESCRIPTION"][$key]) ? $arResult["LINKS"]["DESCRIPTION"][$key] : $arLinkVal)?></a>
						<?}
						unset($key, $arLinkVal);?>
					</div>
				<?}?>
			</div>
			<div class="objects-item-detail-contacts">
				<button type="button" class="objects-item-detail-btn"><i class="icon-phone-call"></i></button>
			</div>			
			<?if(!empty($arResult["MAP"]["VALUE"])) {?>
				<div class="objects-item-detail-map">
					<?$arTmp = explode(",", $arResult["MAP"]["VALUE"]);
					$mapData = array(
						"google_lat" => $arTmp[0],
						"google_lon" => $arTmp[1],
						"google_scale" => "13",
						"PLACEMARKS" => array(
							array(
								"LON" => $arTmp[1],
								"LAT" => $arTmp[0]
							)
						)
					);
					unset($arTmp);?>
					<?$APPLICATION->IncludeComponent("bitrix:map.google.view", "",
						array(
							"API_KEY" => $arResult["MAP"]["API_KEY"],
							"CONTROLS" => array(
								0 => "SMALL_ZOOM_CONTROL",
							),
							"INIT_MAP_TYPE" => "ROADMAP",
							"MAP_DATA" => serialize($mapData),
							"MAP_HEIGHT" => "189",
							"MAP_ID" => "object",
							"MAP_WIDTH" => "100%",
							"OPTIONS" => array(
								0 => "ENABLE_DBLCLICK_ZOOM",
								1 => "ENABLE_DRAGGING",
								2 => "ENABLE_KEYBOARD",
							),
							"COMPONENT_TEMPLATE" => ".default"
						),
						$component,
						array("HIDE_ICONS" => "Y")
					);?>
					<?unset($mapData);?>
				</div>
			<?}?>			
		</div>
		<?//PROMOTIONS//
		if(!empty($arResult["PROMOTIONS_IDS"])) {?>
			<div class="objects-detail-promotions-container" data-entity="tab-container" data-value="promotions">
				<div class="h2"><?=Loc::getMessage("OBJECTS_ITEM_DETAIL_PROMOTIONS")?></div>
				<div class="objects-detail-promotions">
					<?$GLOBALS["arObjectsPromoFilter"] = array("ID" => $arResult["PROMOTIONS_IDS"]);?>
					<?$APPLICATION->IncludeComponent("bitrix:news.list", "promotions",
						array(
							"IBLOCK_TYPE" => $arParams["PROMOTIONS_IBLOCK_TYPE"],
							"IBLOCK_ID" => $arParams["PROMOTIONS_IBLOCK_ID"],
							"NEWS_COUNT" => $arParams["PROMOTIONS_NEWS_COUNT"],
							"SORT_BY1" => $arParams["PROMOTIONS_SORT_BY1"],
							"SORT_ORDER1" => $arParams["PROMOTIONS_SORT_ORDER1"],
							"SORT_BY2" => $arParams["PROMOTIONS_SORT_BY2"],
							"SORT_ORDER2" => $arParams["PROMOTIONS_SORT_ORDER2"],
							"FILTER_NAME" => "arObjectsPromoFilter",
							"FIELD_CODE" => array(),
							"PROPERTY_CODE" => $arParams["PROMOTIONS_PROPERTY_CODE"],
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
							"DISPLAY_BOTTOM_PAGER" => "Y",
							"PAGER_SHOW_ALWAYS" => "",
							"PAGER_TEMPLATE" => "arrows",
							"PAGER_DESC_NUMBERING" => "",
							"PAGER_DESC_NUMBERING_CACHE_TIME" => "",
							"PAGER_SHOW_ALL" => "",
							"AJAX_OPTION_ADDITIONAL" => "",							
							"DISPLAY_PAGINATION" => "Y",
						),
						$component,
						array("HIDE_ICONS" => "Y")
					);?>
				</div>
			</div>
		<?}
		//SECTIONS_PRODUCTS//
		if(!empty($arResult["PRODUCTS_IDS"])) {?>
			<div class="objects-detail-products-container" data-entity="tab-container" data-value="products">
				<div class="h2"><?=Loc::getMessage("OBJECTS_ITEM_DETAIL_PRODUCTS")?></div>
				<?//SECTIONS//?>
				<div class="objects-detail-sections-links" data-entity="links">
					<div class="objects-detail-section-link active" data-section-id="0"><?=Loc::getMessage("OBJECTS_ITEM_DETAIL_PRODUCTS_SECTIONS_ALL")?><span><?=count($arResult["PRODUCTS_IDS"])?></span></div>
					<?if(!empty($arResult["SECTIONS"])) {
						foreach($arResult["SECTIONS"] as $arSection) {?>
							<div class="objects-detail-section-link" data-section-id="<?=$arSection['ID']?>"><?=$arSection["NAME"]?><span><?=$arSection["COUNT"]?></span></div>
						<?}
						unset($arSection);
					}?>
				</div>
				<div class="objects-detail-products">
					<?//PRODUCTS//
                    $GLOBALS["arObjectsProdFilter"] = array("ID" => $arResult["PRODUCTS_IDS"], "IBLOCK_ID" => $arResult["IBLOCKS_IDS"]);?>
					<?$APPLICATION->IncludeComponent("zs:catalog.section", ".default",
						array(
							"COMPONENT_TEMPLATE" => ".default",
							"IBLOCK_TYPE" => $arParams["CATALOG_IBLOCK_TYPE"],
							"IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"],
							"SECTION_ID" => "",
							"SECTION_CODE" => "",
							"SECTION_USER_FIELDS" => array(),
							"FILTER_NAME" => "arObjectsProdFilter",
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
							"PAGE_ELEMENT_COUNT" => "16",
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
							"DISPLAY_BOTTOM_PAGER" => "Y",
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
							"DISABLE_INIT_JS_IN_COMPONENT" => "N",
                            "DISPLAY_PAGINATION" => "Y",

                        ),
						$component,
						array("HIDE_ICONS" => "Y")
					);?>
				</div>
			</div>
		<?}
		//TOUR_3D//
		if(!empty($arResult["TOUR_3D"])) {?>
			<div class="objects-detail-3d-tour-container" data-entity="tab-container" data-value="3d-tour">
				<div class="h2"><?=$arResult["TOUR_3D"]["NAME"]?></div>
				<div class="objects-detail-3d-tour"><?=$arResult["TOUR_3D"]["VALUE"]?></div>
			</div>
		<?}
		//AFFILIATES_MAP_AFFILIATES//		
		if(!empty($arResult["AFFILIATES"]["VALUE"])) {?>			
			<div class="objects-detail-affiliates-container" data-entity="tab-container" data-value="affiliates">
				<div class="h2"><?=$arResult["AFFILIATES"]["NAME"]?></div>
				<?foreach($arResult["AFFILIATES"]["VALUE"] as $arVal) {
					if(!empty($arVal["MAP"])) {
						$arTmp = explode(",", $arVal["MAP"]);
						$mapData["PLACEMARKS"][] = array(
							"LON" => $arTmp[1],
							"LAT" => $arTmp[0],
							"TEXT" => "<div class='object-item-marker'>".(is_array($arVal["PREVIEW_PICTURE"]) ? "<div class='object-item-marker-image'><img src='".$arVal["PREVIEW_PICTURE"]["SRC"]."' /></div>" : "")."<div class='object-item-marker-caption'><div class='object-item-marker-title'>".$arVal["NAME"]."</div>".(!empty($arVal["ADDRESS"]) ? "<div class='object-item-marker-address'><i class='icon-map-marker'></i><span>".$arVal["ADDRESS"]."</span></div>" : "")."<a target='_blank' class='object-item-marker-link' href='".$arVal["DETAIL_PAGE_URL"]."'>".GetMessage("OBJECTS_ITEM_DETAIL_OBJECT_MORE")."</a></div></div>"
						);
						unset($arTmp);
					}
				}
				unset($arVal);				
				if(!empty($mapData["PLACEMARKS"])) {					
					if(count($mapData["PLACEMARKS"]) == 1) {
						$mapData["google_lat"] = $mapData["PLACEMARKS"][0]["LAT"];
						$mapData["google_lon"] = $mapData["PLACEMARKS"][0]["LON"];
						$mapData["google_scale"] = "13";
					}?>
					<div class="objects-detail-affiliates-map">
						<?//AFFILIATES_MAP//?>
						<?$APPLICATION->IncludeComponent("bitrix:map.google.view", "",
							array(
								"API_KEY" => $arResult["MAP"]["API_KEY"],								
								"CONTROLS" => array(
									0 => "SMALL_ZOOM_CONTROL",
								),
								"INIT_MAP_TYPE" => "ROADMAP",
								"MAP_DATA" => serialize($mapData),
								"MAP_HEIGHT" => "100%",
								"MAP_ID" => "affiliates",
								"MAP_WIDTH" => "100%",
								"OPTIONS" => array(
									0 => "ENABLE_DBLCLICK_ZOOM",
									1 => "ENABLE_DRAGGING",
									2 => "ENABLE_KEYBOARD",
								),
								"COMPONENT_TEMPLATE" => ".default"
							),
							$component,
							array("HIDE_ICONS" => "Y")
						);?>
					</div>
				<?}
				unset($mapData);?>
				<div class="objects-detail-affiliates">
					<?//AFFILIATES//
					$GLOBALS["arObjectsAffilFilter"] = array("ID" => array_keys($arResult["AFFILIATES"]["VALUE"]));?>
					<?$APPLICATION->IncludeComponent("bitrix:news.list", "objects",
						array(
							"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
							"IBLOCK_ID" => $arParams["IBLOCK_ID"],
							"NEWS_COUNT" => $arParams["LIST_NEWS_COUNT"],
							"SORT_BY1" => $arParams["LIST_SORT_BY1"],
							"SORT_ORDER1" => $arParams["LIST_SORT_ORDER1"],
							"SORT_BY2" => $arParams["LIST_SORT_BY2"],
							"SORT_ORDER2" => $arParams["LIST_SORT_ORDER2"],
							"FILTER_NAME" => "arObjectsAffilFilter",
							"FIELD_CODE" => array(),
							"PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
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
							"DISPLAY_BOTTOM_PAGER" => "Y",
							"PAGER_SHOW_ALWAYS" => "",
							"PAGER_TEMPLATE" => "arrows",
							"PAGER_DESC_NUMBERING" => "",
							"PAGER_DESC_NUMBERING_CACHE_TIME" => "",
							"PAGER_SHOW_ALL" => "",
							"AJAX_OPTION_ADDITIONAL" => "",
							"SHOW_PROMOTIONS" => $arParams["SHOW_PROMOTIONS"],
							"PROMOTIONS_IBLOCK_ID" => $arParams["PROMOTIONS_IBLOCK_ID"],
							"CATALOG_IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"],
							"USE_REVIEW" => $arParams["USE_REVIEW"],
							"REVIEWS_IBLOCK_ID" => $arParams["REVIEWS_IBLOCK_ID"]
						),
						$component,
						array("HIDE_ICONS" => "Y")
					);?>
				</div>
			</div>
		<?}
		//DETAIL_TEXT//
		if(!empty($arResult["DETAIL_TEXT"])) {?>
			<div class="objects-detail-detail-text" data-entity="tab-container" data-value="description"><?=$arResult["DETAIL_TEXT"]?></div>
		<?}
		//REVIEWS//
		if(isset($arResult["REVIEWS_COUNT"])) {?>
			<div class="objects-detail-reviews-content" data-entity="tab-container" data-value="reviews">
				<div class="h2"><?=Loc::getMessage("OBJECTS_ITEM_DETAIL_REVIEWS")?></div>
				<div class="objects-detail-reviews">
					<?$GLOBALS["arObjectsReviewsFilter"] = array("PROPERTY_OBJECT_ID" => $arResult["ID"]);?>
					<?$APPLICATION->IncludeComponent("bitrix:news.list", "reviews",
						array(
							"IBLOCK_TYPE" => $arParams["REVIEWS_IBLOCK_TYPE"],
							"IBLOCK_ID" => $arParams["REVIEWS_IBLOCK_ID"],
							"NEWS_COUNT" => $arParams["REVIEWS_NEWS_COUNT"],
							"SORT_BY1" => $arParams["REVIEWS_SORT_BY1"],
							"SORT_ORDER1" => $arParams["REVIEWS_SORT_ORDER1"],
							"SORT_BY2" => $arParams["REVIEWS_SORT_BY2"],
							"SORT_ORDER2" => $arParams["REVIEWS_SORT_ORDER2"],
							"FILTER_NAME" => "arObjectsReviewsFilter",
							"FIELD_CODE" => array(),
							"PROPERTY_CODE" => $arParams["REVIEWS_PROPERTY_CODE"],
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
							"ACTIVE_DATE_FORMAT" => $arParams["REVIEWS_ACTIVE_DATE_FORMAT"],
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
							"DISPLAY_BOTTOM_PAGER" => "Y",
							"PAGER_SHOW_ALWAYS" => "",
							"PAGER_TEMPLATE" => "arrows",
							"PAGER_DESC_NUMBERING" => "",
							"PAGER_DESC_NUMBERING_CACHE_TIME" => "",
							"PAGER_SHOW_ALL" => "",
							"AJAX_OPTION_ADDITIONAL" => ""
						),
						$component,
						array("HIDE_ICONS" => "Y")
					);?>
				</div>
			</div>
		<?}?>
	</div>
	<?//META//?>
	<link itemprop="url" href="<?=(CMain::IsHTTPS() ? 'https' : 'http').'://'.SITE_SERVER_NAME.$arResult['DETAIL_PAGE_URL']?>" />
	<?if(!empty($arResult["PHONE"]["VALUE"])) {
		foreach($arResult["PHONE"]["VALUE"] as $arPhone) {?>
			<meta itemprop="telephone" content="<?=$arPhone?>" />
		<?}
		unset($arPhone);
	}
	if(!empty($arResult["EMAIL"]["VALUE"])) {
		foreach($arResult["EMAIL"]["VALUE"] as $arEmail) {?>
			<meta itemprop="email" content="<?=$arEmail?>" />
		<?}
		unset($arEmail);
	}?>
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
$signedProductsIds = $signer->sign(base64_encode(serialize($arResult["PRODUCTS_IDS"])), "news.detail");

$arJSParams = array(				
	"CONFIG" => array(		
		"IMG_LAZYLOAD" => $imgLazyLoad,
		"PARAMS" => $signedParams
	),
	"ITEM" => array(
		"ID" => $arResult["ID"],
		"NAME" => $arResult["NAME"],
		"ADDRESS" => $arResult["ADDRESS"],
		"TIMEZONE" => $arResult["TIMEZONE"],
		"WORKING_HOURS" => $arResult["WORKING_HOURS"],		
		"PHONE" => $arResult["PHONE"],						
		"EMAIL" => $arResult["EMAIL"],
		"SKYPE" => $arResult["SKYPE"],
		"CALLBACK_FORM" => $arResult["PHONE_SMS"] || $arResult["EMAIL_EMAIL"] ? true : false,
		"PRODUCTS_IDS" => $signedProductsIds
	),
	"VISUAL" => array(
		"ID" => $mainId
	)
);?>

<script type="text/javascript">
	BX.message({
		OBJECTS_ITEM_DETAIL_TODAY: '<?=GetMessageJS("OBJECTS_ITEM_DETAIL_TODAY");?>',
		OBJECTS_ITEM_DETAIL_24_HOURS: '<?=GetMessageJS("OBJECTS_ITEM_DETAIL_24_HOURS");?>',
		OBJECTS_ITEM_DETAIL_OFF: '<?=GetMessageJS("OBJECTS_ITEM_DETAIL_OFF");?>',
		OBJECTS_ITEM_DETAIL_BREAK: '<?=GetMessageJS("OBJECTS_ITEM_DETAIL_BREAK");?>',
		OBJECTS_ITEM_DETAIL_LOADING: '<?=GetMessageJS("OBJECTS_ITEM_DETAIL_LOADING");?>',
		OBJECT_TEMPLATE_PATH: '<?=$templateFolder?>'
	});
	var <?=$obName;?> = new JCNewsDetailObjects(<?=CUtil::PhpToJSObject($arJSParams, false, true);?>);
</script>