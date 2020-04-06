<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

use Bitrix\Main\Localization\Loc;

if(empty($arResult))
	return;?>

<div class="hidden-print personal-menu-wrapper">
	<div class="personal-menu">
		<?foreach($arResult as $arItem) {
			if($arItem["DEPTH_LEVEL"] == "1") {?>							
				<a class="personal-menu-item<?=($arItem['SELECTED'] ? ' selected' : '')?>" href="<?=$arItem['LINK']?>" title="<?=$arItem['TEXT']?>">
					<span class="personal-menu-item-block">
						<?if(!empty($arItem['PARAMS']['ICON'])) {?>
							<span class="personal-menu-item-icon"><i class="<?=$arItem['PARAMS']['ICON']?>"></i></span>
						<?}?>
						<span class="hidden-xs hidden-sm personal-menu-item-name"><?=htmlspecialcharsbx($arItem["TEXT"])?></span>
					</span>
					<?if(isset($arItem["COUNT"])) {?>
						<span class="personal-menu-item-count<?=($arItem['PARAMS']['CODE'] == 'BASKET' ? ' personal-menu-item-scheme-count' : '').($arItem['COUNT'] > 0 ? '' : ' personal-menu-item-count-empty')?>"><?=$arItem["COUNT"]?></span>
					<?}?>
				</a>
			<?}
		}
		unset($arItem);?>
		<a class="personal-menu-item" href="<?=$APPLICATION->GetCurPageParam('logout=yes', array('logout'))?>" title="<?=Loc::getMessage('PERSONAL_MENU_EXIT')?>">
			<span class="personal-menu-item-block">
				<span class="personal-menu-item-icon"><i class="icon-logout"></i></span>
				<span class="hidden-xs hidden-sm personal-menu-item-name"><?=Loc::getMessage("PERSONAL_MENU_EXIT")?></span>
			</span>
		</a>
	</div>
</div>