<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();



$this->setFrameMode(true);

global $arSettings;

$isSiteClosed = false;
if(COption::GetOptionString("main", "site_stopped") == "Y" && !$USER->CanDoOperation("edit_other_settings"))
	$isSiteClosed = true;

$obName = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $this->GetEditAreaId($this->randString()));
$containerName = 'catalog-menu-'.$obName;

//pre($arResult);
//pre($arSettings);

if(!$isSiteClosed && !empty($arResult)) {
	if($arSettings["CATALOG_MENU"]["VALUE"] == "OPTION-3") {?>
		<div class="hidden-xs hidden-sm top-panel__col top-panel__catalog-menu">
	<?} else {?>
		<div class="hidden-xs hidden-sm<?=($APPLICATION->GetDirProperty('PERSONAL_SECTION') ? ' hidden-md hidden-lg' : '')?> hidden-print catalog-menu-wrapper">
	<?}?>
		<ul class="catalog-menu<?=($arSettings['CATALOG_MENU']['VALUE'] == 'OPTION-5' ? ' catalog-menu-item-column ' : '')?> scrollbar-inner" id="<?=$containerName?>" data-entity="dropdown-menu">
			<?$previousLevel = 0;					
			foreach($arResult as $arItem) {
				if($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel)
					echo str_repeat("</ul></div></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));
				if($arItem["IS_PARENT"]) {?>
					<li<?=($arItem["SELECTED"] ? " class='active'" : "")?> data-entity="dropdown">
						<a href="<?=$arItem['LINK']?>">
							<?if(!empty($arItem["PARAMS"]["ICON"])) {?>
								<span class="<?=($arItem['DEPTH_LEVEL'] > 2 ? 'hidden-md hidden-lg ' : '')?>catalog-menu-icon"><i class="<?=$arItem['PARAMS']['ICON']?>"></i></span>
							<?} elseif(is_array($arItem["PARAMS"]["PICTURE"])) {?>
								<span class="<?=($arItem['DEPTH_LEVEL'] > 2 ? 'hidden-md hidden-lg ' : '')?>catalog-menu-pic">
									<img src="<?=$arItem['PARAMS']['PICTURE']['SRC']?>" width="<?=$arItem['PARAMS']['PICTURE']['WIDTH']?>" height="<?=$arItem['PARAMS']['PICTURE']['HEIGHT']?>" alt="<?=$arItem['PARAMS']['PICTURE']['ALT']?>" title="<?=$arItem['PARAMS']['PICTURE']['TITLE']?>" />
								</span>
							<?}?>
							<span class="catalog-menu-text"><?=($arSettings["CATALOG_MENU"]["VALUE"] != "OPTION-5" && $arItem["DEPTH_LEVEL"] == 1 ? preg_replace("/\s/", "<span></span>", $arItem["TEXT"], 1) : $arItem["TEXT"])?></span>
							<span class="<?=($arSettings['CATALOG_MENU']['VALUE'] == 'OPTION-5' || $arItem['DEPTH_LEVEL'] > 1 ? 'hidden-md hidden-lg ' : '')?>catalog-menu-arrow"><i class="hidden-xs hidden-sm icon-arrow-down"></i><i class="hidden-md hidden-lg icon-arrow-right"></i></span>
						</a>
						<div class="catalog-menu-dropdown-menu scrollbar-inner" data-entity="dropdown-menu">
							<ul>
								<li class="hidden-md hidden-lg" data-entity="title">
									<i class="icon-arrow-left catalog-menu-back"></i>
									<span class="catalog-menu-title"><?=$arItem["TEXT"]?></span>
									<i class="icon-close catalog-menu-close"></i>
								</li>
				<?} else {?>
					<li<?=$arItem["SELECTED"] ? " class='active'" : ""?>>
						<a href="<?=$arItem['LINK']?>">
							<?if(!empty($arItem["PARAMS"]["ICON"])) {?>
								<span class="<?=($arItem['DEPTH_LEVEL'] > 2 ? 'hidden-md hidden-lg ' : '')?>catalog-menu-icon"><i class="<?=$arItem['PARAMS']['ICON']?>"></i></span>
							<?} elseif(is_array($arItem["PARAMS"]["PICTURE"])) {?>
								<span class="<?=($arItem['DEPTH_LEVEL'] > 2 ? 'hidden-md hidden-lg ' : '')?>catalog-menu-pic">
									<img src="<?=$arItem['PARAMS']['PICTURE']['SRC']?>" width="<?=$arItem['PARAMS']['PICTURE']['WIDTH']?>" height="<?=$arItem['PARAMS']['PICTURE']['HEIGHT']?>" alt="<?=$arItem['PARAMS']['PICTURE']['ALT']?>" title="<?=$arItem['PARAMS']['PICTURE']['TITLE']?>" />
								</span>
							<?}?>
							<span class="catalog-menu-text"><?=($arSettings["CATALOG_MENU"]["VALUE"] != "OPTION-5" && $arItem["DEPTH_LEVEL"] == 1 ? preg_replace("/\s/", "<span></span>", $arItem["TEXT"], 1) : $arItem["TEXT"])?></span>
							<?if($arItem["PARAMS"]["ELEMENT_CNT"] > 0) {?>
								<span class="<?=($arSettings['CATALOG_MENU']['VALUE'] == 'OPTION-3' || ($arSettings['CATALOG_MENU']['VALUE'] != 'OPTION-3' && $arItem["DEPTH_LEVEL"] > 1) ? 'hidden-md hidden-lg ' : '')?>catalog-menu-count"><?=$arItem["PARAMS"]["ELEMENT_CNT"]?></span>
							<?}?>
						</a>
					</li>
				<?}
				$previousLevel = $arItem["DEPTH_LEVEL"];						
			}
			if($previousLevel > 1)
				echo str_repeat("</ul></div></li>", ($previousLevel - 1));?>
		</ul>
		<script type="text/javascript">
			BX.message({
				MAIN_MENU: '<?=GetMessageJS("BM_MAIN_MENU")?>'
			});
			var <?=$obName?> = new JCCatalogMenu({
				inside: <?=($arSettings["CATALOG_MENU"]["VALUE"] == "OPTION-3" ? "true" : "false")?>,
				setActive: <?=($arSettings["CATALOG_MENU_OPEN"]["VALUE"] == "ACTIVE_LEVEL" ? "true" : "false")?>,
				openLast: <?=($arSettings["CATALOG_MENU_NAV"]["VALUE"] == "LAST_ITEM" ? "true" : "false")?>,
				container: '<?=$containerName?>'
			});
		</script>
	</div>
<?}?>