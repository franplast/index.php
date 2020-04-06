<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

global $arSettings;

$isSiteClosed = false;
if(COption::GetOptionString("main", "site_stopped") == "Y" && !$USER->CanDoOperation("edit_other_settings"))
	$isSiteClosed = true;

$obName = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $this->GetEditAreaId($this->randString()));
$containerName = 'slide-menu-'.$obName;

if(!$isSiteClosed && !empty($arResult)) {?>
    <div class="slide-menu_desc">
	<ul class="slide-menu_desc scrollbar-inner" id="<?=$containerName?>">
		<?$previousLevel = 0;					
		foreach($arResult as $arItem) {			
			if($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel)
				echo str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));
			if($arItem["IS_PARENT"]) {?>
				<li<?=$arItem["SELECTED"] ? " class='active'" : ""?> data-entity="dropdown">
					<a href="<?=$arItem['LINK']?>" >
						<?if(!empty($arItem["PARAMS"]["ICON"])) {?>
							<span class="slide-menu_desc-icon">
								<i class="<?=$arItem['PARAMS']['ICON']?>"></i>
							</span>
						<?} elseif(is_array($arItem["PARAMS"]["PICTURE"])) {?>
							<span class="slide-menu_desc-pic">
								<img src="<?=$arItem['PARAMS']['PICTURE']['SRC']?>" width="<?=$arItem['PARAMS']['PICTURE']['WIDTH']?>" height="<?=$arItem['PARAMS']['PICTURE']['HEIGHT']?>" alt="<?=$arItem['PARAMS']['PICTURE']['ALT']?>" />
							</span>
						<?}?>
						<span class="slide-menu_desc-text"><?=$arItem["TEXT"]?></span>
						<span class="slide-menu_desc-arrow"><i class="icon-arrow-right"></i></span>
					</a>
					<ul class="slide-menu_desc-dropdown-menu scrollbar-inner" data-entity="dropdown-menu2">
						<li class="hidden-md hidden-lg" data-entity="title">
							<i class="icon-arrow-left slide-menu_desc-back"></i>
							<span class="slide-menu_desc-title"><?=$arItem["TEXT"]?></span>
							<i class="icon-close slide-menu_desc-close"></i>
						</li>
			<?} else {?>
				<li<?=$arItem["SELECTED"] ? " class='active'" : ""?>>
					<a href="<?=$arItem['LINK']?>">
						<?if(!empty($arItem["PARAMS"]["ICON"])) {?>
							<span class="slide-menu_desc-icon">
								<i class="<?=$arItem['PARAMS']['ICON']?>"></i>
							</span>
						<?} elseif(is_array($arItem["PARAMS"]["PICTURE"])) {?>
							<span class="slide-menu_desc-pic">
								<img src="<?=$arItem['PARAMS']['PICTURE']['SRC']?>" width="<?=$arItem['PARAMS']['PICTURE']['WIDTH']?>" height="<?=$arItem['PARAMS']['PICTURE']['HEIGHT']?>" alt="<?=$arItem['PARAMS']['PICTURE']['ALT']?>" />
							</span>
						<?}?>
						<span class="slide-menu_desc-text"><?=$arItem["TEXT"]?></span>
						<?if($arItem["PARAMS"]["ELEMENT_CNT"] > 0) {?>
							<span class="slide-menu_desc-count"><?=$arItem["PARAMS"]["ELEMENT_CNT"]?></span>
						<?}?>
					</a>
				</li>
			<?}
			$previousLevel = $arItem["DEPTH_LEVEL"];						
		}
		if($previousLevel > 1)
			echo str_repeat("</ul></li>", ($previousLevel - 1));?>
	</ul>
    </div>
<?}?>