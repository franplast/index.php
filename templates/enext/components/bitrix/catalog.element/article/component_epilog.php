<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader,
	Bitrix\Sale;

//CURRENCIES//
if(!empty($templateData["TEMPLATE_LIBRARY"])) {
	$loadCurrency = false;
	if(!empty($templateData["CURRENCIES"])) {
		$loadCurrency = Loader::includeModule("currency");
	}
	CJSCore::Init($templateData["TEMPLATE_LIBRARY"]);
	if($loadCurrency) {?>
		<script type="text/javascript">
			BX.Currency.setCurrencies(<?=$templateData["CURRENCIES"]?>);
		</script>
	<?}
}

if(isset($templateData["JS_OBJ"])) {
	$item = $templateData["ITEM"];

	//CHECK_COMPARED//
	if($arParams["DISPLAY_COMPARE"]) {
		$compared = false;
		$comparedIds = array();		
		
		if(!empty($_SESSION[$arParams["COMPARE_NAME"]][$item["IBLOCK_ID"]])) {
			if(!empty($item["JS_OFFERS"])) {
				foreach($item["JS_OFFERS"] as $key => $offer) {
					if(array_key_exists($offer["ID"], $_SESSION[$arParams["COMPARE_NAME"]][$item["IBLOCK_ID"]]["ITEMS"])) {
						if($templateData["OFFERS_VIEW"] == "PROPS" && $key == $item["OFFERS_SELECTED"]) {
							$compared = true;
						}
						$comparedIds[] = $offer["ID"];
					}
				}
			} elseif(array_key_exists($item["ID"], $_SESSION[$arParams["COMPARE_NAME"]][$item["IBLOCK_ID"]]["ITEMS"])) {
				$compared = true;
			}
		}
	}
	
	//CHECK_DELAYED_BUYED_ADDED//
	$delayed = $buyedAdded = false;
	$delayedIds = $buyedAddedIds = array();
	
	if(Loader::includeModule("sale")) {
		$fuserId = Sale\Fuser::getId(true);
		$dbItems = CSaleBasket::GetList(
			array("NAME" => "ASC", "ID" => "ASC"),
			array(			
				"LID" => SITE_ID,			
				"CAN_BUY" => "Y",
				"FUSER_ID" => $fuserId,
				"ORDER_ID" => "NULL"
			),
			false,
			false,
			array("ID", "PRODUCT_ID", "DELAY")
		);
		while($arItem = $dbItems->GetNext()) {
			if(CSaleBasketHelper::isSetItem($arItem))
				continue;
			if(!empty($item["JS_OFFERS"])) {
				foreach($item["JS_OFFERS"] as $key => $offer) {
					if($offer["ID"] == $arItem["PRODUCT_ID"]) {
						if($templateData["OFFERS_VIEW"] == "PROPS" && $key == $item["OFFERS_SELECTED"]) {
							if($arItem["DELAY"] == "Y")
								$delayed = true;
							else
								$buyedAdded = true;
						}					
						if($arItem["DELAY"] == "Y")
							$delayedIds[] = $offer["ID"];
						else
							$buyedAddedIds[] = $offer["ID"];
					}
				}
			} elseif($item["ID"] == $arItem["PRODUCT_ID"]) {
				if($arItem["DELAY"] == "Y")
					$delayed = true;
				else
					$buyedAdded = true;
			}
		}
	}
	
	//JS//?>
	<script type="text/javascript">
		BX.ready(BX.defer(function() {
			if(!!window.<?=$templateData['JS_OBJ']?>) {
				//CHECK_COMPARED//
				<?if($arParams["DISPLAY_COMPARE"]) {
					if(!isset($item["JS_OFFERS"]) || empty($item["JS_OFFERS"]) || (!empty($item["JS_OFFERS"]) && $templateData["OFFERS_VIEW"] == "PROPS")) {?>
						window.<?=$templateData['JS_OBJ']?>.setCompared('<?=$compared?>');
					<?}
					if(!empty($comparedIds)) {?>
						window.<?=$templateData['JS_OBJ']?>.setCompareInfo(<?=CUtil::PhpToJSObject($comparedIds, false, true)?>);
					<?}
				}?>
				
				//CHECK_DELAYED//
				<?if(!isset($item["JS_OFFERS"]) || empty($item["JS_OFFERS"]) || (!empty($item["JS_OFFERS"]) && $templateData["OFFERS_VIEW"] == "PROPS")) {?>
					window.<?=$templateData['JS_OBJ']?>.setDelayed('<?=$delayed?>');
				<?}
				if(!empty($delayedIds)) {?>
					window.<?=$templateData['JS_OBJ']?>.setDelayInfo(<?=CUtil::PhpToJSObject($delayedIds, false, true)?>);
				<?}?>
				
				//CHECK_BUYED_ADDED//
				<?if(!isset($item["JS_OFFERS"]) || empty($item["JS_OFFERS"]) || (!empty($item["JS_OFFERS"]) && $templateData["OFFERS_VIEW"] == "PROPS")) {?>
					window.<?=$templateData['JS_OBJ']?>.setBuyedAdded('<?=$buyedAdded?>');
				<?}
				if(!empty($buyedAddedIds)) {?>
					window.<?=$templateData['JS_OBJ']?>.setBuyAddInfo(<?=CUtil::PhpToJSObject($buyedAddedIds, false, true)?>);
				<?}?>
			}
		}));
	</script>
<?}