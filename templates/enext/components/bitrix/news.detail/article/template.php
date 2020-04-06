<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

global $arSettings;
$imgLazyLoad = $arSettings["LAZYLOAD"]["VALUE"] == "Y" ? true : false;

$hasProducts = !empty($arResult["PROPERTIES"]["PRODUCTS"]["VALUE"]) ? true : false;
if($hasProducts) {
	$this->addExternalCss(SITE_TEMPLATE_PATH."/js/owlCarousel/owl.carousel.min.css");
	$this->addExternalJS(SITE_TEMPLATE_PATH."/js/owlCarousel/owl.carousel.min.js");

	$mainId = $this->GetEditAreaId($arResult["ID"]);
	$obName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $mainId);
}?>

<div class="articles-detail"<?=($hasProducts ? " id=".$mainId : "")?>>
	<?if($hasProducts) {
		//TABS//?>
		<div class="articles-detail-tabs-container">
			<div class="articles-detail-tabs-block" data-entity="tabs">
				<div class="articles-detail-tabs-scroll">
					<ul class="articles-detail-tabs-list">
						<li class="articles-detail-tab active" data-entity="tab" data-value="article"><?=Loc::getMessage("ARTICLES_ITEM_DETAIL_TAB_ARTICLE")?></li>
						<li class="articles-detail-tab" data-entity="tab" data-value="products"><?=Loc::getMessage("ARTICLES_ITEM_DETAIL_TAB_PRODUCTS")?><span><?=count($arResult["PROPERTIES"]["PRODUCTS"]["VALUE"])?></span></li>
					</ul>
				</div>
			</div>
		</div>
		<div class="articles-detail-tabs-content">
			<div class="articles-item-detail-container" data-entity="tab-container" data-value="article">
	<?}
	//ITEM//?>
	<div class="articles-item-detail">
		<?//ITEM_PIC//?>
		<div class="articles-item-detail-pic-container">
			<div class="articles-item-detail-pic">
				<?if(is_array($arResult["DETAIL_PICTURE"])) {?>
					<img class="no-popup" width="<?=$arResult['DETAIL_PICTURE']['WIDTH']?>" height="<?=$arResult['DETAIL_PICTURE']['HEIGHT']?>" src="<?=$arResult['DETAIL_PICTURE']['SRC']?>" alt="<?=$arResult['DETAIL_PICTURE']['ALT']?>" />
				<?}?>
			</div>
		</div>
		<?//ITEM_BLOCK//?>
		<div class="articles-item-detail-block">
			<div class="articles-item-detail-title"><?=$arResult["NAME"]?></div>

		</div>
		<?//ITEM_MARKER//
		if(!empty($arResult["MARKER"])) {?>
			<div class="articles-item-detail-marker-container">
				<div class="articles-item-detail-marker"<?=(!empty($arResult["MARKER"]["BACKGROUND_1"]) && !empty($arResult["MARKER"]["BACKGROUND_2"]) ? " style='background: ".$arResult["MARKER"]["BACKGROUND_2"]."; background: -webkit-linear-gradient(left, ".$arResult["MARKER"]["BACKGROUND_1"].", ".$arResult["MARKER"]["BACKGROUND_2"]."); background: -moz-linear-gradient(left, ".$arResult["MARKER"]["BACKGROUND_1"].", ".$arResult["MARKER"]["BACKGROUND_2"]."); background: -o-linear-gradient(left, ".$arResult["MARKER"]["BACKGROUND_1"].", ".$arResult["MARKER"]["BACKGROUND_2"]."); background: -ms-linear-gradient(left, ".$arResult["MARKER"]["BACKGROUND_1"].", ".$arResult["MARKER"]["BACKGROUND_2"]."); background: linear-gradient(to right, ".$arResult["MARKER"]["BACKGROUND_1"].", ".$arResult["MARKER"]["BACKGROUND_2"].");'" : (!empty($arResult["MARKER"]["BACKGROUND_1"]) && empty($arResult["MARKER"]["BACKGROUND_2"]) ? " style='background: ".$arResult["MARKER"]["BACKGROUND_1"].";'" : (empty($arResult["MARKER"]["BACKGROUND_1"]) && !empty($arResult["MARKER"]["BACKGROUND_2"]) ? " style='background: ".$arResult["MARKER"]["BACKGROUND_2"].";'" : "")))?>><span><?=$arResult["MARKER"]["NAME"]?></span></div>
			</div>
		<?}?>
	</div>
	<?//ITEM_DETAIL_TEXT//
	if(!empty($arResult["DETAIL_TEXT"])) {?>
		<div class="articles-item-detail-detail-text"><?=$arResult["DETAIL_TEXT"]?></div>
	<?}
	if($hasProducts) {?>
			</div>
			<?//SECTIONS_PRODUCTS//?>				
			<div class="articles-detail-products-container" data-entity="tab-container" data-value="products">
				<div class="h2"><?=Loc::getMessage("ARTICLES_ITEM_DETAIL_PRODUCTS")?></div>
				<?//SECTIONS//?>
				<div class="articles-detail-sections-links" data-entity="links">
					<div class="articles-detail-section-link active" data-section-id="0"><?=Loc::getMessage("ARTICLES_ITEM_DETAIL_PRODUCTS_SECTIONS_ALL")?><span><?=count($arResult["PROPERTIES"]["PRODUCTS"]["VALUE"])?></span></div>
					<?if(!empty($arResult["SECTIONS"])) {
						foreach($arResult["SECTIONS"] as $arSection) {?>
							<div class="articles-detail-section-link" data-section-id="<?=$arSection['ID']?>"><?=$arSection["NAME"]?><span><?=$arSection["COUNT"]?></span></div>
						<?}
						unset($arSection);
					}?>
				</div>
				<div class="articles-detail-products">
					<?//PRODUCTS//
					$GLOBALS["arArticlesProdFilter"] = array("ID" => $arResult["PROPERTIES"]["PRODUCTS"]["VALUE"]);?>
					<?$APPLICATION->IncludeComponent("bitrix:catalog.section", ".default", 
						array(
							"COMPONENT_TEMPLATE" => ".default",
							"IBLOCK_TYPE" => $arParams["CATALOG_IBLOCK_TYPE"],
							"IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"],
							"SECTION_ID" => "",
							"SECTION_CODE" => "",
							"SECTION_USER_FIELDS" => array(),
							"FILTER_NAME" => "arArticlesProdFilter",
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
					<?unset($basketAction);?>
				</div>
			</div>
		</div>
	<?}?>
</div>

<?if($hasProducts) {
	$arParamsCatalog = array(
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

	$signer = new Bitrix\Main\Security\Sign\Signer;
	$signedParams = $signer->sign(base64_encode(serialize($arParamsCatalog)), "news.detail");
	$signedProductsIds = $signer->sign(base64_encode(serialize($arResult["PROPERTIES"]["PRODUCTS"]["VALUE"])), "news.detail");
	
	$arJSParams = array(
		"CONFIG" => array(
			"IMG_LAZYLOAD" => $imgLazyLoad,
			"PARAMS" => $signedParams
		),
		"ITEM" => array(
			"PRODUCTS_IDS" => $signedProductsIds
		),
		"VISUAL" => array(
			"ID" => $mainId
		)
	);?>

	<script type="text/javascript">
		BX.message({		
			ARTICLE_TEMPLATE_PATH: "<?=$templateFolder?>"
		});
		var <?=$obName;?> = new JCNewsDetailArticles(<?=CUtil::PhpToJSObject($arJSParams, false, true);?>);
	</script>
<?}

$component->arResult["RESULT_HTML"] = ob_get_clean();
$component->SetResultCacheKeys(
	array(
		"RESULT_HTML" => "RESULT_HTML"
	)
);