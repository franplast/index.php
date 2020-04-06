<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1)
	return;

$elementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
$elementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
$elementDeleteParams = array("CONFIRM" => Loc::getMessage("CONTACTS_ITEM_DELETE_CONFIRM"));?>

<div class="contacts">
	<?foreach($arResult["ITEMS"] as $arItem) {
		$this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], $elementEdit);
		$this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], $elementDelete, $elementDeleteParams);
		
		$strMainID = $this->GetEditAreaId($arItem["ID"]);	
		$strObName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);?>
		
		<div class="contacts-item">
			<div class="container">				
				<div class="row">
					<div class="col-xs-12 col-md-4">
						<div class="contacts-item-caption" id="<?=$strMainID?>">
							<div class="contacts-item-title"><?=$arItem["NAME"]?></div>
							<?if(!empty($arItem["ADDRESS"])) {?>
								<div class="contacts-item-row contacts-item-address">
									<div class="contacts-item-icon"><i class="icon-map-marker"></i></div>
									<div class="contacts-item-text"><?=$arItem["ADDRESS"]?></div>
								</div>
							<?}?>
							<div class="contacts-item-row contacts-item-working-hours contacts-item-working-hours-hidden"></div>
							<?if(!empty($arItem["PHONE"])) {
								foreach($arItem["PHONE"]["VALUE"] as $key => $val) {?>
									<div class="contacts-item-row contacts-item-phone">
										<div class="contacts-item-icon"><i class="icon-phone"></i></div>
										<a class="contacts-item-text contacts-item-link" href="tel:<?=preg_replace('/[^0-9+]/', '', $val)?>"><?=$val.(!empty($arItem["PHONE"]["DESCRIPTION"][$key]) ? "<span class='contacts-item-descr'>".$arItem["PHONE"]["DESCRIPTION"][$key]."</span>" : "")?></a>
									</div>
								<?}
								unset($key, $val);
							}
							if(!empty($arItem["EMAIL"])) {
								foreach($arItem["EMAIL"]["VALUE"] as $key => $val) {?>
									<div class="contacts-item-row contacts-item-email">
										<div class="contacts-item-icon"><i class="icon-mail"></i></div>
										<a class="contacts-item-text contacts-item-link" href="mailto:<?=$val?>"><?=$val.(!empty($arItem["EMAIL"]["DESCRIPTION"][$key]) ? "<span class='contacts-item-descr'>".$arItem["EMAIL"]["DESCRIPTION"][$key]."</span>" : "")?></a>
									</div>
								<?}
								unset($key, $val);
							}
							if(!empty($arItem["SKYPE"])) {
								foreach($arItem["SKYPE"]["VALUE"] as $key => $val) {?>
									<div class="contacts-item-row contacts-item-skype">
										<div class="contacts-item-icon"><i class="fa fa-skype"></i></div>
										<a class="contacts-item-text contacts-item-link" href="skype:<?=$val?>?chat"><?=$val.(!empty($arItem["SKYPE"]["DESCRIPTION"][$key]) ? "<span class='contacts-item-descr'>".$arItem["SKYPE"]["DESCRIPTION"][$key]."</span>" : "")?></a>
									</div>
								<?}
								unset($key, $val);
							}?>
							<div class="contacts-item-btn">
								<a class="btn btn-primary" href="javascript:void(0)" data-entity="callback"><i class="icon-phone"></i><span><?=Loc::getMessage("CONTACTS_ITEM_CALLBACK")?></span></a>
							</div>
						</div>
						<?$arJSParams = array(				
							"ITEM" => array(			
								"TIMEZONE" => $arItem["TIMEZONE"],
								"WORKING_HOURS" => $arItem["WORKING_HOURS"]
							),
							"VISUAL" => array(
								"ID" => $strMainID
							)
						);?>
						<script type="text/javascript">
							BX.message({
								CONTACTS_ITEM_TODAY: '<?=GetMessageJS("CONTACTS_ITEM_TODAY");?>',
								CONTACTS_ITEM_24_HOURS: '<?=GetMessageJS("CONTACTS_ITEM_24_HOURS");?>',
								CONTACTS_ITEM_OFF: '<?=GetMessageJS("CONTACTS_ITEM_OFF");?>',
								CONTACTS_ITEM_BREAK: '<?=GetMessageJS("CONTACTS_ITEM_BREAK");?>',
								CONTACTS_LOADING: '<?=GetMessageJS("CONTACTS_LOADING");?>',
								CONTACTS_TEMPLATE_PATH: '<?=CUtil::JSEscape($templateFolder)?>'
							});
							var <?=$strObName;?> = new JCNewsListContacts(<?=CUtil::PhpToJSObject($arJSParams, false, true);?>);
						</script>
					</div>
				</div>
			</div>
			<div class="contacts-item-map">
				<?$mapData = array();
				if(!empty($arItem["YMAP"])) {
					$arTmp = explode(",", $arItem["YMAP"]["VALUE"]);
					$mapData["PLACEMARKS"][] = array(
						"LON" => $arTmp[1],
						"LAT" => $arTmp[0],
						"TEXT" => "<div class='object-item-marker'>".(is_array($arItem["PREVIEW_PICTURE"]) ? "<div class='object-item-marker-image'><img src='".$arItem["PREVIEW_PICTURE"]["SRC"]."' /></div>" : "")."<div class='object-item-marker-caption'><div class='object-item-marker-title'>".$arItem["NAME"]."</div>".(!empty($arItem["ADDRESS"]) ? "<div class='object-item-marker-address'><i class='icon-map-marker'></i><span>".$arItem["ADDRESS"]."</span></div>" : "")."</div></div>"
					);
					unset($arTmp);
				}
				if(!empty($arResult["OBJECTS"]["VALUE"])) {
					foreach($arResult["OBJECTS"]["VALUE"] as $val) {
						if(!empty($val["YMAP"])) {
							$arTmp = explode(",", $val["YMAP"]);
							$mapData["PLACEMARKS"][] = array(
								"LON" => $arTmp[1],
								"LAT" => $arTmp[0],
								"TEXT" => "<div class='object-item-marker'>".(is_array($val["PREVIEW_PICTURE"]) ? "<div class='object-item-marker-image'><img src='".$val["PREVIEW_PICTURE"]["SRC"]."' /></div>" : "")."<div class='object-item-marker-caption'><div class='object-item-marker-title'>".$val["NAME"]."</div>".(!empty($val["ADDRESS"]) ? "<div class='object-item-marker-address'><i class='icon-map-marker'></i><span>".$val["ADDRESS"]."</span></div>" : "")."<a target='_blank' class='object-item-marker-link' href='".$val["DETAIL_PAGE_URL"]."'>".Loc::getMessage("CONTACTS_ITEM_OBJECT_MORE")."</a></div></div>"
							);
							unset($arTmp);
						}
					}
					unset($val);
				}
				if(count($mapData["PLACEMARKS"]) == 1) {
					$mapData["yandex_lat"] = $mapData["PLACEMARKS"][0]["LAT"];
					$mapData["yandex_lon"] = $mapData["PLACEMARKS"][0]["LON"];
					$mapData["yandex_scale"] = "15";
				}?>
				<?$APPLICATION->IncludeComponent("bitrix:map.yandex.view", "default",
					array(
                        "CONTROLS" => array("ZOOM", "SMALLZOOM"),
                        "INIT_MAP_TYPE" => "MAP",
						"MAP_DATA" => serialize($mapData),
						"MAP_HEIGHT" => "100%",
						"MAP_ID" => "contacts",
						"MAP_WIDTH" => "100%",
						"OPTIONS" => array(
							0 => "ENABLE_DBLCLICK_ZOOM",
							1 => "ENABLE_DRAGGING",
							2 => "ENABLE_KEYBOARD",
						),
						"COMPONENT_TEMPLATE" => "default"
					),
					$component,
					array("HIDE_ICONS" => "Y")
				);?>		
			</div>
		</div>
	<?}?>
</div>