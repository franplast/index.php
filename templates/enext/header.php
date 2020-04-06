<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
IncludeTemplateLangFile(__FILE__);
CJSCore::Init(array("fx"));
$scheme = CMain::IsHTTPS() ? "https" : "http";?>
<!DOCTYPE html>
<html lang="<?=LANGUAGE_ID?>">
	<head>
		<?=$APPLICATION->ShowProperty("countersScriptsHead");?>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />		
		<link rel="preload" href="<?=SITE_TEMPLATE_PATH?>/fonts/MuseoSansCyrl-300.woff" as="font" type="font/woff" crossorigin />
		<link rel="preload" href="<?=SITE_TEMPLATE_PATH?>/fonts/MuseoSansCyrl-500.woff" as="font" type="font/woff" crossorigin />
		<link rel="preload" href="<?=SITE_TEMPLATE_PATH?>/fonts/MuseoSansCyrl-700.woff" as="font" type="font/woff" crossorigin />
		<title><?$APPLICATION->ShowTitle()?></title>
		<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/colors.min.css", true);
		$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/animation.min.css");		
		$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/csshake-default.min.css");
		$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/js/scrollbar/jquery.scrollbar.min.css");
		$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/bootstrap.min.css");
		$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/font-awesome.min.css");
		$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/elasto-font.min.css");
		$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/uinext2020.min.css");
        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/custom.css");
        if(!defined("fancybox"))$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/js/fancybox/jquery.fancybox.min.css");
		CJSCore::Init(array("jquery2", "enextIntlTelInput"));
        if(!defined("fancybox"))$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/fancybox/jquery.fancybox.min.js");

        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/bootstrap.min.js");
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/formValidation.min.js");
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/inputmask.min.js");		
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery.hoverIntent.min.js");
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/moremenu.min.js");		
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/scrollbar/jquery.scrollbar.min.js");
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/main.min.js");
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/script.min.js");
		// START Modificate
		if ( file_exists($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH."/custom.js") ) 
			$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/custom.js"); 
		// END Modificate 
		$APPLICATION->ShowHead();?>
	</head>
	<body class="<?=$APPLICATION->ShowProperty('catalogMenu')?>"<?=$APPLICATION->ShowProperty("backgroundColor");?>>
		<?=$APPLICATION->ShowProperty("countersScriptsBodyStart");
		echo $APPLICATION->ShowPanel();
		global $arSettings;
		$arSettings = $APPLICATION->IncludeComponent("altop:settings.enext", "", array(), false, array("HIDE_ICONS" => "Y"));
		$isSiteBg = $arSettings["SITE_BACKGROUND"]["VALUE"] == "Y" ? true : false;
		$siteBgFixed = $arSettings["SITE_BACKGROUND_FIXED"]["VALUE"] == "Y" ? true : false;
		$isSiteClosed = COption::GetOptionString("main", "site_stopped") == "Y" && !$USER->CanDoOperation("edit_other_settings") ? true : false;
		$isWideScreenCatalog = $arSettings["WIDESCREEN_CATALOG"]["VALUE"] == "Y" && (CSite::InDir(SITE_DIR."catalog/") ? true : false || defined("IS_CATALOG"));?>
		<div class="page-wrapper<?=(!$siteBgFixed ? " page-wrapper-rel" : "");?>">
			<?if($isSiteBg) {?>
				<div class="hidden-print page-bg<?=($arSettings['SITE_BACKGROUND_REPEAT_X']['VALUE'] == 'Y' ? ' page-bg__repeat-x' : '').($arSettings['SITE_BACKGROUND_REPEAT_Y']['VALUE'] == 'Y' ? ' page-bg__repeat-y' : '').($siteBgFixed ? ' page-bg__fixed' : '').($arSettings['SITE_BACKGROUND_BLUR']['VALUE'] == 'Y' ? ' page-bg__blur' : '');?> hidden-xs hidden-sm"<?=$APPLICATION->ShowProperty("backgroundImage");?>></div>
			<?}
			if(!$isSiteClosed && in_array("TOP_MENU", $arSettings["SITE_BLOCKS"]["VALUE"])) {?>
				<div class="hidden-xs hidden-sm hidden-print top-menu-wrapper">
					<div class="top-menu">
						<?//TOP_MENU//?>
						<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
							array(
								"AREA_FILE_SHOW" => "file",
								"PATH" => SITE_DIR."include/header_top_menu.php"
							),
							false,
							array("HIDE_ICONS" => "Y")
						);?>
					</div>
				</div>
			<?}?>
			<div class="hidden-print top-panel-wrapper">				
				<div class="top-panel<?=(!$APPLICATION->GetDirProperty('PERSONAL_SECTION') && ($arSettings['CATALOG_MENU']['VALUE'] == 'OPTION-4' || $arSettings['CATALOG_MENU']['VALUE'] == 'OPTION-5') ? ' catalog-menu-outside' : '')?>">
					<div class="top-panel__cols">
						<div class="top-panel__col top-panel__thead">								
							<div class="top-panel__cols">								
								<?//MENU_ICON//
								if(!$isSiteClosed) {?>
									<div class="top-panel__col top-panel__menu-icon-container<?=($arSettings['CATALOG_MENU']['VALUE'] == 'OPTION-3' || $arSettings['CATALOG_MENU']['VALUE'] == 'OPTION-4' || $arSettings['CATALOG_MENU']['VALUE'] == 'OPTION-5' ? ' hidden-md hidden-lg' : '')?>" data-entity="menu-icon"><i class="icon-menu"></i><span class="title-menu">Каталог</span></div> <?// Modificate ?>
								<?}
								//LOGO//?>								
								<div class="top-panel__col top-panel__logo">
									<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
										array(
											"AREA_FILE_SHOW" => "file",
											"PATH" => SITE_DIR."include/header_logo.php"
										),
										false
									);?>
								</div>
							</div>
						</div>
						<div class="top-panel__col top-panel__tfoot">
							<div class="top-panel__cols">
								<?if($arSettings["CATALOG_MENU"]["VALUE"] == "OPTION-3") {
									//MENU//?>
									<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
										array(
											"AREA_FILE_SHOW" => "file",
											"PATH" => SITE_DIR."include/slide_menu.php"
										),
										false,
										array("HIDE_ICONS" => "Y")
									);?>
									<div class="hidden-xs hidden-sm top-panel__col"></div>
								<?}
								if($arSettings["TOP_PANEL_SEARCH_BUTTON"]["VALUE"] == "Y" && ($arSettings["CATALOG_MENU"]["VALUE"] == "OPTION-1" || $arSettings["CATALOG_MENU"]["VALUE"] == "OPTION-2" || $arSettings["CATALOG_MENU"]["VALUE"] == "OPTION-4" || $arSettings['CATALOG_MENU']['VALUE'] == 'OPTION-5')) {?>
									<div class="hidden-xs hidden-sm top-panel__col"></div>
								<?}
								if(!$isSiteClosed) {?>
									<div class="top-panel__col top-panel__search-container<?=($arSettings['TOP_PANEL_SEARCH_BUTTON']['VALUE'] == 'Y' ? ' top-panel__search-container-button' : '')?>">												
										<a class="top-panel__search-btn<?=($arSettings['TOP_PANEL_SEARCH_BUTTON']['VALUE'] != 'Y' ? ' hidden-md hidden-lg' : '')?>" href="javascript:void(0)" data-entity="showSearch">
											<span class="top-panel__search-icon"><i class="icon-search"></i></span>
										</a>
										<div class="top-panel__search <?=($arSettings['TOP_PANEL_SEARCH_BUTTON']['VALUE'] != 'Y' ? 'hidden-xs hidden-sm' : 'hidden')?>">
											<?//SEARCH//?>
											<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
												array(
													"AREA_FILE_SHOW" => "file",
													"PATH" => SITE_DIR."include/header_search.php"
												),
												false,
												array("HIDE_ICONS" => "Y")
											);?>
										</div>
									</div>									
								<?}
								if($arSettings["TOP_PANEL_SEARCH_BUTTON"]["VALUE"] != "Y" && ($arSettings["CATALOG_MENU"]["VALUE"] == "OPTION-1" || $arSettings["CATALOG_MENU"]["VALUE"] == "OPTION-2" || $arSettings["CATALOG_MENU"]["VALUE"] == "OPTION-4" || $arSettings['CATALOG_MENU']['VALUE'] == 'OPTION-5')) {?>
									<div class="hidden-xs hidden-sm top-panel__col"></div>
								<?}
								//CONTACTS//?>
								<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
									array(
										"AREA_FILE_SHOW" => "file",
										"PATH" => SITE_DIR."include/header_contacts.php"
									),
									false,
									array("HIDE_ICONS" => "Y")
								);?>
								<?if(!$isSiteClosed) {
									if($arSettings["TOP_PANEL_DISABLE_COMPARE"]["VALUE"] != "Y") {?>
										<div class="top-panel__col top-panel__compare">
											<?//COMPARE//?>
											<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
												array(
													"AREA_FILE_SHOW" => "file",
													"PATH" => SITE_DIR."include/header_compare.php"
												),
												false,
												array("HIDE_ICONS" => "Y")
											);?>
										</div>
									<?}
									//CART//?>
									<?$APPLICATION->IncludeComponent("altop:sale.basket.basket.line", "",
										array(
											"SHOW_DELAY" => "Y",
											"PATH_TO_BASKET" => SITE_DIR."personal/cart/"
										),
										false,
										array("HIDE_ICONS" => "Y")
									);?>
									<div class="top-panel__col top-panel__user">
										<?//USER//?>
										<?$APPLICATION->IncludeComponent("altop:user.enext", ".default",
											array(
												"PATH_TO_PERSONAL" => SITE_DIR."personal/",
												"CACHE_TYPE" => "A",
												"CACHE_TIME" => "36000000"
											),
											false
										);?>
									</div>
								<?}?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?if($arSettings["CATALOG_MENU"]["VALUE"] == "OPTION-1" || $arSettings["CATALOG_MENU"]["VALUE"] == "OPTION-2" || $arSettings["CATALOG_MENU"]["VALUE"] == "OPTION-4" || $arSettings["CATALOG_MENU"]["VALUE"] == "OPTION-5") {
				//SLIDE_MENU//?>
				<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
					array(
						"AREA_FILE_SHOW" => "file",
						"PATH" => SITE_DIR."include/slide_menu.php"
					),
					false,
					array("HIDE_ICONS" => "Y")
				);?>
			<?}
			if(!$isSiteClosed) {?>
				<div class="page-container-wrapper">
			<?}
			if($isSiteBg && !$isWideScreenCatalog) {?>				
				<div class="page-container">
			<?}
			if(!$isSiteClosed) {
				if(!CSite::inDir(SITE_DIR."index.php")) {
					if(!CSite::InDir(SITE_DIR."personal/order/make/") && $APPLICATION->GetDirProperty("PERSONAL_SECTION") && $USER->IsAuthorized()) {
						//PERSONAL_MENU//?>
						<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
							array(
								"AREA_FILE_SHOW" => "file",
								"PATH" => SITE_DIR."include/personal_menu.php"
							),
							false,
							array("HIDE_ICONS" => "Y")
						);?>
					<?}
					//SECTION_BANNER//
					$APPLICATION->ShowViewContent("UF_BANNER");
					if(!CSite::InDir(SITE_DIR."personal/")) {
						//NAVIGATION//?>
						<div class="hidden-print navigation-wrapper">
							<div class="container<?=($isWideScreenCatalog ? '-ws' : '')?>">
								<div class="row">
									<div class="col-xs-12">
										<div class="navigation-content">
											<div id="navigation" class="navigation">
												<?$APPLICATION->IncludeComponent("bitrix:breadcrumb", "", 
													array(
														"START_FROM" => "0",
														"PATH" => "",
														"SITE_ID" => "-"
													),
													false,
													array("HIDE_ICONS" => "Y")
												);?>
											</div>
											<?//SHARE//?>
											<div class="navigation-share">
												<div class="navigation-share-icon" data-entity="showShare"><i class="icon-share"></i></div>
												<div class="navigation-share-content" data-entity="shareContent">
													<div class="navigation-share-content-title"><?=GetMessage("ENEXT_SHARE")?></div>
													<div class="navigation-share-content-block">
														<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
															array(
																"AREA_FILE_SHOW" => "file",
																"PATH" => SITE_DIR."include/footer_share.php"
															),
															false
														);?>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					<?}
					//SECTION_PANEL//
					$APPLICATION->ShowViewContent("CATALOG_SECTION_PANEL");
					?>
					<div class="content-wrapper internal">
						<div class="container<?=($isWideScreenCatalog ? '-ws' : '')?>">
							<div class="row">
								<div class="col-xs-12">
				<?}
			}