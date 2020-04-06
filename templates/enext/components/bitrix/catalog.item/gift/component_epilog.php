<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader,
	Bitrix\Sale;

if(isset($templateData['JS_OBJ'])) {
	$item = $templateData['ITEM'];
	
	$haveOffers = !empty($item['JS_OFFERS']);

	$params = $templateData['PARAMETERS'];
	
	//CHECK_BUYED_ADDED//
	if((!$item['OBJECT'] || ($item['OBJECT'] && $item['OBJECT_CONTACTS'])) && !$item['PARTNERS_URL'] && (!$haveOffers || ($haveOffers && $params['OFFERS_VIEW'] == 'PROPS' && $params['PRODUCT_DISPLAY_MODE'] == 'Y'))) {
		$buyedAdded = false;
		$buyedAddedIds = array();
		
		if(Loader::includeModule('sale')) {
			$fuserId = Sale\Fuser::getId(true);
			$dbItems = CSaleBasket::GetList(
				array('NAME' => 'ASC', 'ID' => 'ASC'),
				array(			
					'LID' => SITE_ID,
					'DELAY' => 'N',
					'CAN_BUY' => 'Y',
					'FUSER_ID' => $fuserId,
					'ORDER_ID' => 'NULL'
				),
				false,
				false,
				array('ID', 'PRODUCT_ID', 'DELAY')
			);
			while($arItem = $dbItems->GetNext()) {
				if(CSaleBasketHelper::isSetItem($arItem))
					continue;			
				if(!empty($item['JS_OFFERS'])) {
					foreach($item['JS_OFFERS'] as $key => $offer) {
						if($offer['ID'] == $arItem['PRODUCT_ID']) {
							if($key == $item['OFFERS_SELECTED'])
								$buyedAdded = true;
							$buyedAddedIds[] = $offer['ID'];
						}
					}
				} elseif($item['ID'] == $arItem['PRODUCT_ID']) {
					$buyedAdded = true;
				}
			}
		}
	}
	
	//JS//?>
	<script type='text/javascript'>
		BX.ready(BX.defer(function() {
			if(!!window.<?=$templateData['JS_OBJ']?>) {
				<?if((!$item['OBJECT'] || ($item['OBJECT'] && $item['OBJECT_CONTACTS'])) && !$item['PARTNERS_URL'] && (!$haveOffers || ($haveOffers && $params['OFFERS_VIEW'] == 'PROPS' && $params['PRODUCT_DISPLAY_MODE'] == 'Y'))) {?>
					//CHECK_BUYED_ADDED//
					window.<?=$templateData['JS_OBJ']?>.setBuyedAdded('<?=$buyedAdded?>');
					<?if(!empty($buyedAddedIds)) {?>
						window.<?=$templateData['JS_OBJ']?>.setBuyAddInfo(<?=CUtil::PhpToJSObject($buyedAddedIds, false, true)?>);
					<?}
				}?>
			}
		}));
	</script>
<?}