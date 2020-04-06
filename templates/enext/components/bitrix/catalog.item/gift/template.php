<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main;

$this->setFrameMode(true);

$this->addExternalCss(SITE_TEMPLATE_PATH."/components/bitrix/catalog.item/.default/style.min.css");

if(isset($arResult['ITEM'])) {
	$item = $arResult['ITEM'];
	$areaId = $arResult['AREA_ID'];
	$itemIds = array(
		'ID' => $areaId,
		'PICT_ID' => $areaId.'_pict',
		'BUY_LINK' => $areaId.'_buy_link',
		'BASKET_ACTIONS_ID' => $areaId.'_basket_actions',
		'MORE_LINK' => $areaId.'_more_link',
		'SUBSCRIBE_LINK' => $areaId.'_subscribe',
		'PRICE_ID' => $areaId.'_price',
		'OLD_PRICE_ID' => $areaId.'_price_old',
		'TREE_ID' => $areaId.'_sku_tree',		
		'BASKET_PROP_DIV' => $areaId.'_basket_prop',
	);
	$obName = 'ob'.preg_replace("/[^a-zA-Z0-9_]/", "x", $areaId);
	
	$productTitle = isset($item['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) && $item['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] != '' ? $item['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] : $item['NAME'];

	$imgTitle = isset($item['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE']) && $item['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE'] != '' ? $item['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE'] : $item['NAME'];
	
	$imgAlt = isset($item["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"]) && $item["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] != "" ? $item["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] : $item["NAME"];

	$skuProps = array();

	$haveOffers = !empty($item['OFFERS']);
	if($haveOffers) {
		$actualItem = isset($item['OFFERS'][$item['OFFERS_SELECTED']]) ? $item['OFFERS'][$item['OFFERS_SELECTED']] : reset($item['OFFERS']);
	} else {
		$actualItem = $item;
	}

	$object = !empty($item['PROPERTIES']['OBJECT']['FULL_VALUE']) ? $item['PROPERTIES']['OBJECT']['FULL_VALUE'] : false;
	$objectContacts = $object['PHONE_SMS'] || $object['EMAIL_EMAIL'] ? true : false;

	$partnersUrl = !empty($item["PROPERTIES"]["PARTNERS_URL"]["VALUE"]) ? true : false;
	if($haveOffers && $arParams["OFFERS_VIEW"] == "PROPS")
		$offerPartnersUrl = !empty($actualItem["PROPERTIES"]["PARTNERS_URL"]["VALUE"]) ? true : false;

	$price = $actualItem['ITEM_PRICES'][$actualItem['ITEM_PRICE_SELECTED']];
	
	$showSubscribe = $arParams['PRODUCT_SUBSCRIPTION'] === 'Y' && ($item['CATALOG_SUBSCRIBE'] === 'Y' || $haveOffers);?>
	
	<div class="product-item-container" id="<?=$areaId?>" data-entity="item">
		<?$documentRoot = Main\Application::getDocumentRoot();
		$templatePath = strtolower($arResult['TYPE']).'/template.php';
		$file = new Main\IO\File($documentRoot.$templateFolder.'/'.$templatePath);
		if($file->isExists()) {
			include($file->getPath());
		}

		if(!$haveOffers) {
			$jsParams = array(
				'PRODUCT_TYPE' => $item['CATALOG_TYPE'],				
				'SHOW_ADD_BASKET_BTN' => false,
				'SHOW_BUY_BTN' => true,
				'SHOW_ABSENT' => true,				
				'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
				'VIEW_MODE' => $arResult['TYPE'],
				'USE_SUBSCRIBE' => $showSubscribe,
				'PRODUCT' => array(
					'ID' => $item['ID'],
					'NAME' => $productTitle,
					'PICT' => $item['PREVIEW_PICTURE'],
					'CAN_BUY' => $item['CAN_BUY'],
					'ITEM_PRICES' => $item['ITEM_PRICES'],
					'ITEM_PRICE_SELECTED' => $item['ITEM_PRICE_SELECTED']
				),
				'BASKET' => array(					
					'ADD_PROPS' => $arParams['ADD_PROPERTIES_TO_BASKET'] === 'Y',
					'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
					'PROPS' => $arParams['PRODUCT_PROPS_VARIABLE'],
					'EMPTY_PROPS' => empty($item['PRODUCT_PROPERTIES']),
					'ADD_URL_TEMPLATE' => $arParams['~ADD_URL_TEMPLATE'],
					'BUY_URL_TEMPLATE' => $arParams['~BUY_URL_TEMPLATE']
				),
				'VISUAL' => $itemIds
			);
		} else {
			$jsParams = array(
				'PRODUCT_TYPE' => $item['CATALOG_TYPE'],
				'SHOW_ADD_BASKET_BTN' => false,
				'SHOW_BUY_BTN' => true,
				'SHOW_ABSENT' => true,
				'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
				'VIEW_MODE' => $arResult['TYPE'],
				'USE_SUBSCRIBE' => $showSubscribe,
				'DEFAULT_PICTURE' => array(
					'PICTURE' => $item['PRODUCT_PREVIEW']
				),
				'VISUAL' => $itemIds,
				'BASKET' => array(					
					'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
					'PROPS' => $arParams['PRODUCT_PROPS_VARIABLE'],
					'SKU_PROPS' => $item['OFFERS_PROP_CODES'],
					'ADD_URL_TEMPLATE' => $arParams['~ADD_URL_TEMPLATE'],
					'BUY_URL_TEMPLATE' => $arParams['~BUY_URL_TEMPLATE']
				),
				'PRODUCT' => array(
					'ID' => $item['ID'],
					'NAME' => $productTitle
				),
				'OFFERS' => array(),
				'OFFER_SELECTED' => 0,
				'TREE_PROPS' => array()
			);

			if((!$object || ($object && $objectContacts)) && !$partnersUrl && $arParams['OFFERS_VIEW'] == 'PROPS' && $arParams['PRODUCT_DISPLAY_MODE'] == 'Y' && !empty($item['OFFERS_PROP'])) {
				$jsParams['OFFERS'] = $item['JS_OFFERS'];
				$jsParams['OFFER_SELECTED'] = $item['OFFERS_SELECTED'];
				$jsParams['TREE_PROPS'] = $skuProps;
			}
		}
		
		$jsParams['PRODUCT_DISPLAY_MODE'] = $haveOffers && (($object && !$objectContacts) || $partnersUrl || $arParams['OFFERS_VIEW'] != 'PROPS') ? 'N' : $arParams['PRODUCT_DISPLAY_MODE'];
		$jsParams['USE_ENHANCED_ECOMMERCE'] = $arParams['USE_ENHANCED_ECOMMERCE'];
		$jsParams['DATA_LAYER_NAME'] = $arParams['DATA_LAYER_NAME'];
		$jsParams['BRAND_PROPERTY'] = !empty($item['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']]) ? $item['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']]['DISPLAY_VALUE'] : null;

		$templateData = array(
			'JS_OBJ' => $obName,
			'ITEM' => array(
				'ID' => $item['ID'],
				'IBLOCK_ID' => $item['IBLOCK_ID'],
				'OFFERS_SELECTED' => $item['OFFERS_SELECTED'],
				'JS_OFFERS' => $item['JS_OFFERS'],
				'OBJECT' => $object,
				'OBJECT_CONTACTS' => $objectContacts,
				'PARTNERS_URL' => $partnersUrl
			),
			'PARAMETERS' => $arParams
		);?>

		<script type="text/javascript">
			var <?=$obName?> = new JCGiftItem(<?=CUtil::PhpToJSObject($jsParams, false, true)?>);
		</script>
	</div>
	<?unset($item, $actualItem, $itemIds, $jsParams);
}