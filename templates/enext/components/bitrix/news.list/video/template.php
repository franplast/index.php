<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1)
	return;

$elementEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');
$elementDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE');
$elementDeleteParams = array('CONFIRM' => GetMessage('GALLERY_ITEM_DELETE_CONFIRM'));

$obName = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $this->GetEditAreaId($this->randString()));
$containerName = 'gallery-'.$obName;?>

<div class="row gallery" id="<?=$containerName?>">			
	<?foreach($arResult["ITEMS"] as $arItem) {
		$this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], $elementEdit);
		$this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], $elementDelete, $elementDeleteParams);?>
		<div class="col-xs-6 col-md-3" id="<?=$this->GetEditAreaId($arItem['ID'])?>">				
			<a class="gallery-item " data-fancybox href="https://www.youtube.com/embed/<?=$arItem['PROPERTIES']['YTCODE']['VALUE']?>" data-width="977" data-height="550">
				<span class="gallery-item__image">
					<?if(!empty($arItem["PREVIEW_PICTURE"])) {?>
						<img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" width="<?=$arItem['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arItem['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arItem['NAME']?>" />
					<?}?>
				</span>
				<span class="gallery-item__caption-wrap">
					<span class="gallery-item__caption">
						<span class="gallery-item__title"><?=$arItem["NAME"]?></span>
						<?=(!empty($arItem["PREVIEW_TEXT"]) ? "<span class='gallery-item__text'>".$arItem["PREVIEW_TEXT"]."</span>" : "");?>
					</span>
				</span>
			</a>
		</div>
	<?}
	if($arParams["DISPLAY_BOTTOM_PAGER"]) {
		if(!empty($arResult["NAV_STRING"])) {?>
			<div class="col-xs-12">
				<?=$arResult["NAV_STRING"];?>
			</div>
		<?}
	}?>
</div>

<script type="text/javascript">
	var <?=$obName?> = new JCNewsListGallery({
		container: '<?=$containerName?>'
	});
</script>