<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

use Bitrix\Main\Localization\Loc;

$obName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $this->GetEditAreaId($this->randString()));
$containerName = "feedback-".$obName;?>

<div class="hidden-print arenda-wrapper">
	<div class="container px-0 mx-0">
		<div class="row arenda" id="<?=$containerName?>">
			<div class="col-xs-12">
				<div class="h1"><?=$arResult["IBLOCK"]["NAME"]?></div>
			</div>
			<form action="javascript:void(0)">
                <input type="hidden" name="IBLOCK_STRING" value="<?=$arResult['IBLOCK']['STRING']?>" />
				<div class="col-xs-12 col-md-3">
					<?foreach($arResult["IBLOCK"]["PROPERTIES"] as $arProp) {
                        if($arProp["CODE"] == "FORM_HEADER"){
                            ?>
                            <input type="hidden" name="<?=$arProp['CODE']?>" value='<?=str_replace("'","\'",$APPLICATION->GetTitle());?>'  />
                            <?
                            continue;
                        }
						if($arProp["USER_TYPE"] != "HTML") {?>
							<div class="form-group<?=(!empty($arProp['HINT']) ? ' has-feedback' : '');?>">
								<input type="text" name="<?=$arProp['CODE']?>" id="<?=$arProp['CODE']?>" class="form-control" placeholder="<?=$arProp['NAME']?>" />
								<?if(!empty($arProp["HINT"])) {?>
									<i class="form-control-feedback fv-icon-no-has fa <?=$arProp['HINT']?>"></i>
								<?}?>
							</div>
						<?}
					}
					unset($arProp);?>
				</div>
				<div class="col-xs-12 col-md-6">
					<?foreach($arResult["IBLOCK"]["PROPERTIES"] as $arProp) {
					    if($arProp["CODE"] == "FORM_HEADER"){
					        continue;
                        }
						if($arProp["USER_TYPE"] == "HTML") {?>
							<div class="form-group<?=(!empty($arProp['HINT']) ? ' has-feedback' : '');?>">
								<textarea name="<?=$arProp['CODE']?>" class="form-control" rows="3" placeholder="<?=$arProp['NAME']?>" style="height:<?=$arProp['USER_TYPE_SETTINGS']['height']?>px; min-height:<?=$arProp['USER_TYPE_SETTINGS']['height']?>px; max-height:<?=$arProp['USER_TYPE_SETTINGS']['height']?>px;"></textarea>
								<?if(!empty($arProp["HINT"])) {?>
									<i class="form-control-feedback fv-icon-no-has fa <?=$arProp['HINT']?>"></i>
								<?}?>
							</div>
						<?}
					}
					unset($arProp);?>
				</div>
				<div class="col-xs-12 col-md-3">
					<?if($arParams["USE_CAPTCHA"]) {?>
						<div class="form-group captcha">
							<div class="pic" style="display:none;">								
								<img src="" width="100" height="36" alt="CAPTCHA" />
							</div>							
							<input type="text" maxlength="5" name="CAPTCHA_WORD" class="form-control" placeholder="<?=Loc::getMessage('FORMS_FEEDBACK_CAPTCHA_WORD')?>" autocomplete="off" />
							<input type="hidden" name="CAPTCHA_SID" value="" />
						</div>
					<?}?>
					<div class="form-group<?=(!$arParams['USE_CAPTCHA'] ? ' no-captcha' : '');?>">
						<button type="submit" class="btn btn-primary"><?=Loc::getMessage("FORMS_FEEDBACK_SUBMIT")?></button>
                        <?if($arParams["USER_CONSENT"]) {?>
                            <input type="hidden" name="USER_CONSENT_ID" value="<?=$arParams['USER_CONSENT_ID']?>" />
                            <input type="hidden" name="USER_CONSENT_URL" value="" />
                            <div class="mt-20">
                                <div class="form-group form-group-checkbox2">
                                    <div class="checkbox">
                                        <?$fields = array();
                                        foreach($arResult["IBLOCK"]["PROPERTIES"] as $arProp) {
                                            if($arProp["USER_TYPE"] != "HTML")
                                                $fields[] = $arProp["NAME"];
                                        }
                                        unset($arProp);?>
                                        <?$APPLICATION->IncludeComponent("bitrix:main.userconsent.request", "",
                                            array(
                                                "ID" => $arParams["USER_CONSENT_ID"],
                                                "INPUT_NAME" => "USER_CONSENT",
                                                "IS_CHECKED" => $arParams["USER_CONSENT_IS_CHECKED"],
                                                "AUTO_SAVE" => "N",
                                                "IS_LOADED" => $arParams["USER_CONSENT_IS_LOADED"],
                                                "REPLACE" => array(
                                                    "button_caption" => Loc::getMessage("FORMS_FEEDBACK_SUBMIT"),
                                                    "fields" => $fields
                                                )
                                            ),
                                            $component
                                        );?>
                                        <?unset($fields);?>
                                    </div>
                                </div>
                            </div>
                        <?}?>
					</div>
				</div>
			</form>
			<div class="col-xs-12">
				<div class="alert"></div>
			</div>
		</div>
	</div>
</div>

<?$jsProps = array();
foreach($arResult["IBLOCK"]["PROPERTIES"] as $arProp) {
	if($arProp["CODE"] != "OBJECT_ID" && $arProp["CODE"] != "PRODUCT_ID" && $arProp["CODE"] != "OFFER_ID") {
		$jsProps[$arProp["CODE"]] = array(
			"CODE" => $arProp["CODE"],
			"REQUIRED" => $arProp["IS_REQUIRED"]
		);
	}
}
unset($arProp);?>

<script type="text/javascript">	
	BX.message({
		FORMS_NOT_EMPTY_INVALID: '<?=GetMessageJS("FORMS_FEEDBACK_NOT_EMPTY_INVALID");?>',
		FORMS_PHONE_WRONG: '<?=GetMessageJS("FORMS_FEEDBACK_PHONE_WRONG");?>',
        FORMS_EMAIL_WRONG: '<?=GetMessageJS("FORMS_FEEDBACK_EMAIL_WRONG");?>',
		FORMS_USER_CONSENT_NOT_EMPTY_INVALID: '<?=GetMessageJS("FORMS_FEEDBACK_USER_CONSENT_NOT_EMPTY_INVALID");?>',
		FORMS_CAPTCHA_WRONG: '<?=GetMessageJS("FORMS_FEEDBACK_CAPTCHA_WRONG");?>',			
		FORMS_ALERT_SUCCESS: '<?=GetMessageJS("FORMS_FEEDBACK_ALERT_SUCCESS");?>',
		FORMS_ALERT_ERROR: '<?=GetMessageJS("FORMS_FEEDBACK_ALERT_ERROR");?>'
	});
	var <?=$obName?> = new JCFormsFeedbackComponent({
		componentPath: '<?=CUtil::JSEscape($componentPath)?>',
		jsProps: <?=CUtil::PhpToJSObject($jsProps)?>,
		defaultCountry: '<?=CUtil::JSEscape($arParams["DEFAULT_COUNTRY"])?>',
		userConsent: '<?=$arParams["USER_CONSENT"]?>',
		useCaptcha: '<?=$arParams["USE_CAPTCHA"]?>',		
		container: '<?=$containerName?>'
	});
</script>

<?unset($jsProps);