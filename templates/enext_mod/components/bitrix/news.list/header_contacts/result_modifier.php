<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if(count($arResult["ITEMS"]) < 1)
	return;

$arDays = array("MON", "TUE", "WED", "THU", "FRI", "SAT", "SUN");

foreach($arResult["ITEMS"] as &$arItem) {	
	foreach($arItem["DISPLAY_PROPERTIES"] as $arProp) {
		//ADDRESS//
		if($arProp["CODE"] == "ADDRESS" && !empty($arProp["VALUE"])) {
			$arItem[$arProp["CODE"]] = $arProp["VALUE"];
		//TIMEZONE//
		} elseif($arProp["CODE"] == "TIMEZONE" && !empty($arProp["VALUE"])) {
			$timezoneIds[] = $arProp["VALUE"];
		//WORKING_HOURS//
		} elseif(in_array($arProp["CODE"], $arDays) && !empty($arProp["VALUE"])) {
			$workingHoursIds[] = $arProp["VALUE"];		
		//PHONE_EMAIL_SKYPE//
		} elseif(($arProp["CODE"] == "PHONE" || $arProp["CODE"] == "EMAIL" || $arProp["CODE"] == "SKYPE") && !empty($arProp["VALUE"])) {
			$arItem[$arProp["CODE"]] = array(
				"VALUE" => $arProp["VALUE"],
				"DESCRIPTION" => $arProp["DESCRIPTION"]
			);
		}
	}
	unset($arProp);
}
unset($arItem);

//WORKING_HOURS//
if(!empty($workingHoursIds)) {	
	$rsElements = CIBlockElement::GetList(array(), array("ID" => array_unique($workingHoursIds)), false, false, array("ID", "IBLOCK_ID"));	
	while($obElement = $rsElements->GetNextElement()) {
		$arElement = $obElement->GetFields();
		$arElement["PROPERTIES"] = $obElement->GetProperties();

		$arWorkingHours[$arElement["ID"]] = array(
			"WORK_START" => strtotime($arElement["PROPERTIES"]["WORK_START"]["VALUE"]) ? $arElement["PROPERTIES"]["WORK_START"]["VALUE"] : "",
			"WORK_END" => strtotime($arElement["PROPERTIES"]["WORK_END"]["VALUE"]) ? $arElement["PROPERTIES"]["WORK_END"]["VALUE"] : "",
			"BREAK_START" => strtotime($arElement["PROPERTIES"]["BREAK_START"]["VALUE"]) ? $arElement["PROPERTIES"]["BREAK_START"]["VALUE"] : "",
			"BREAK_END" => strtotime($arElement["PROPERTIES"]["BREAK_END"]["VALUE"]) ? $arElement["PROPERTIES"]["BREAK_END"]["VALUE"] : ""
		);
	}
	unset($arElement, $obElement, $rsElements);

	if(!empty($arWorkingHours)) {
		foreach($arResult["ITEMS"] as &$arItem) {
			foreach($arItem["DISPLAY_PROPERTIES"] as $arProp) {
				if(in_array($arProp["CODE"], $arDays) && !empty($arProp["VALUE"])) {
					if(array_key_exists($arProp["VALUE"], $arWorkingHours)) {
						$arItem["WORKING_HOURS"][$arProp["CODE"]] = $arWorkingHours[$arProp["VALUE"]];
						$arItem["WORKING_HOURS"][$arProp["CODE"]]["NAME"] = $arProp["NAME"];
					}
				}
			}
			unset($arProp);
		}
		unset($arItem);
	}
	unset($arWorkingHours);
}
unset($workingHoursIds);

//TIMEZONE//
if(!empty($timezoneIds)) {
	$rsElements = CIBlockElement::GetList(array(), array("ID" => array_unique($timezoneIds)), false, false, array("ID", "IBLOCK_ID"));	
	while($obElement = $rsElements->GetNextElement()) {
		$arElement = $obElement->GetFields();
		$arElement["PROPERTIES"] = $obElement->GetProperties();

		$arTimeZones[$arElement["ID"]] = $arElement["PROPERTIES"]["OFFSET"]["VALUE"];
	}
	unset($arElement, $obElement, $rsElements);

	if(!empty($arTimeZones)) {
		foreach($arResult["ITEMS"] as &$arItem) {
			foreach($arItem["DISPLAY_PROPERTIES"] as $arProp) {
				if($arProp["CODE"] == "TIMEZONE" && !empty($arProp["VALUE"])) {
					if(array_key_exists($arProp["VALUE"], $arTimeZones))
						$arItem[$arProp["CODE"]] = $arTimeZones[$arProp["VALUE"]];
				}
			}
			unset($arProp);
		}
		unset($arItem);
	}
	unset($arTimeZones);
}
unset($timezoneIds);