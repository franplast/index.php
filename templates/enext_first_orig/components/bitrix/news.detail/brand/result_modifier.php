<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

//MARKERS//
foreach($arResult["DISPLAY_PROPERTIES"] as $arProp) {	
	if($arProp["CODE"] == "MARKER" && !empty($arProp["VALUE"])) {
		$rsElement = CIBlockElement::GetList(array(), array("ID" => $arProp["VALUE"], "IBLOCK_ID" => $arProp["LINK_IBLOCK_ID"]), false, false, array("ID", "IBLOCK_ID", "NAME", "SORT"));	
		while($obElement = $rsElement->GetNextElement()) {
			$arElement = $obElement->GetFields();
			$arElement["PROPERTIES"] = $obElement->GetProperties();

			$arResult["MARKER"][] = array(
				"NAME" => $arElement["NAME"],
				"SORT" => $arElement["SORT"],
				"BACKGROUND_1" => $arElement["PROPERTIES"]["BACKGROUND_1"]["VALUE"],
				"BACKGROUND_2" => $arElement["PROPERTIES"]["BACKGROUND_2"]["VALUE"],
				"ICON" => $arElement["PROPERTIES"]["ICON"]["VALUE"],
				"FONT_SIZE" => $arElement["PROPERTIES"]["FONT_SIZE"]["VALUE_XML_ID"]
			);
		}
		unset($arElement, $obElement, $rsElement);

		if(!empty($arResult["MARKER"]))
			Bitrix\Main\Type\Collection::sortByColumn($arResult["MARKER"], array("SORT" => SORT_NUMERIC, "NAME" => SORT_ASC));
	}
}
unset($arProp);

//COLLECTIONS_IDS//
if($arParams["SHOW_COLLECTIONS"] != "N") {
	$rsElements = CIBlockElement::GetList(array(), array("ACTIVE" => "Y", "IBLOCK_ID" => $arParams["COLLECTIONS_IBLOCK_ID"], "PROPERTY_BRAND" => $arResult["ID"]), false, false, array("ID", "IBLOCK_ID"));	
	while($arElement = $rsElements->GetNext()) {
		$arResult["COLLECTIONS_IDS"][] = $arElement["ID"];
	}
	unset($arElement, $rsElements);
}

//SECTIONS//
//PRODUCTS_IDS//
$rsElements = CIBlockElement::GetList(array(), array("ACTIVE" => "Y", "IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"], "SECTION_GLOBAL_ACTIVE" => "Y", "PROPERTY_BRAND" => $arResult["ID"]), false, false, array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID"));	
while($arElement = $rsElements->GetNext()) {	
	if(!empty($arElement["IBLOCK_SECTION_ID"]))
		$arResult["SECTIONS_IDS"][] = $arElement["IBLOCK_SECTION_ID"];
	$arResult["PRODUCTS_IDS"][] = $arElement["ID"];
}
unset($arElement, $rsElements);

if(!empty($arResult["SECTIONS_IDS"])) {
	$arCount = array_count_values($arResult["SECTIONS_IDS"]);
	$rsSections = CIBlockSection::GetList(array("NAME" => "ASC"), array("ID" => array_unique($arResult["SECTIONS_IDS"])), false, array("ID", "IBLOCK_ID", "NAME"));	
	while($arSection = $rsSections->GetNext()) {
		$arResult["SECTIONS"][] = array(
			"ID" => $arSection["ID"],
			"NAME" => $arSection["NAME"],
			"COUNT" => $arCount[$arSection["ID"]]
		);
	}
}