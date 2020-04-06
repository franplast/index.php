<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1)
	return;

$this->addExternalCss(SITE_TEMPLATE_PATH."/js/owlCarousel/owl.carousel.min.css");
$this->addExternalCss(SITE_TEMPLATE_PATH."/js/owlCarousel/animate.min.css");
$this->addExternalJS(SITE_TEMPLATE_PATH."/js/owlCarousel/owl.carousel.min.js");

$elementEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');
$elementDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE');
$elementDeleteParams = array('CONFIRM' => GetMessage('SLIDER_ITEM_DELETE_CONFIRM'));

$obName = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $this->GetEditAreaId($this->randString()));
$containerName = 'slider-'.$obName;?>

<div class="slider-wrapper">
	<div class="slider owl-carousel" id="<?=$containerName?>">
		<?foreach($arResult["ITEMS"] as $arItem) {			
			$this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], $elementEdit);
			$this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], $elementDelete, $elementDeleteParams);?>
			<div class="slider-item"<?=(!empty($arItem["DISPLAY_PROPERTIES"]["VIDEO"]["FILE_VALUE"]) ? " data-video-src='".$arItem["DISPLAY_PROPERTIES"]["VIDEO"]["FILE_VALUE"]["SRC"]."'" : "").(!empty($arItem["DISPLAY_PROPERTIES"]["VIDEO"]["FILE_VALUE"]) && !empty($arItem["DISPLAY_PROPERTIES"]["VIDEO_WIDTH"]["VALUE"]) ? " data-video-width='".$arItem["DISPLAY_PROPERTIES"]["VIDEO_WIDTH"]["VALUE"]."'" : "").(!empty($arItem["DISPLAY_PROPERTIES"]["VIDEO"]["FILE_VALUE"]) && !empty($arItem["DISPLAY_PROPERTIES"]["VIDEO_HEIGHT"]["VALUE"]) ? " data-video-height='".$arItem["DISPLAY_PROPERTIES"]["VIDEO_HEIGHT"]["VALUE"]."'" : "").(!empty($arItem["PREVIEW_PICTURE"]) ? " style='background-image:url(".$arItem["PREVIEW_PICTURE"]["SRC"].");'" : "")?>>
				<div class="slider-item__caption<?=(!empty($arItem['DISPLAY_PROPERTIES']['ALIGN']['VALUE_XML_ID']) ? ' '.$arItem['DISPLAY_PROPERTIES']['ALIGN']['VALUE_XML_ID'] : '').(!empty($arItem['DISPLAY_PROPERTIES']['VERTICAL_ALIGN']['VALUE_XML_ID']) ? ' '. $arItem['DISPLAY_PROPERTIES']['VERTICAL_ALIGN']['VALUE_XML_ID'] : '');?>" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
					<div class="container">
						<div class="row">
							<div class="col-xs-12">
								<div class="slider-item__block fadeInLeftBig">
									<?if(isset($arItem["DISPLAY_PROPERTIES"]["SHOW_NAME"]) && !$arItem["DISPLAY_PROPERTIES"]["SHOW_NAME"]["VALUE"] == false) {?>
										<div class="slider-item__title"><?=$arItem["NAME"]?></div>
									<?}
									if(!empty($arItem["PREVIEW_TEXT"])) {?>
										<div class="slider-item__text"><?=$arItem["PREVIEW_TEXT"]?></div>
									<?}
									if(!empty($arItem["DISPLAY_PROPERTIES"]["BUTTON_1_TEXT"]["VALUE"]) || !empty($arItem["DISPLAY_PROPERTIES"]["BUTTON_2_TEXT"]["VALUE"]) || !empty($arItem["DISPLAY_PROPERTIES"]["BUTTON_3_TEXT"]["VALUE"]) || !empty($arItem["DISPLAY_PROPERTIES"]["BUTTON_4_TEXT"]["VALUE"])) {?>
										<div class="slider-item__buttons">
											<?for($i = 1; $i <= 4; $i++) {
												if(!empty($arItem["DISPLAY_PROPERTIES"]["BUTTON_".$i."_TEXT"]["VALUE"])) {?>
													<a class="btn btn-slider btn-slider-<?=$i?>" href="<?=(!empty($arItem['DISPLAY_PROPERTIES']['BUTTON_'.$i.'_URL']['VALUE']) ? $arItem['DISPLAY_PROPERTIES']['BUTTON_'.$i.'_URL']['VALUE'] : 'javascript:void(0)');?>" role="button"><span><?=$arItem["DISPLAY_PROPERTIES"]["BUTTON_".$i."_TEXT"]["VALUE"]?></span></a>
												<?}
											}?>
										</div>
									<?}?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?if(!empty($arItem["DISPLAY_PROPERTIES"]["URL"]["VALUE"])) {?>
					<a class="slider-item__link" href="<?=$arItem['DISPLAY_PROPERTIES']['URL']['VALUE']?>"></a>
				<?}?>
			</div>
		<?}?>
	</div>
</div>

<script type="text/javascript">
	var <?=$obName?> = new JCNewsListBlockSlider({		
		container: '<?=$containerName?>'
	});
</script>