<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arDays = array("MON", "TUE", "WED", "THU", "FRI", "SAT", "SUN");

foreach($arResult["DISPLAY_PROPERTIES"] as $arProp) {
    //ADDRESS//
    if($arProp["CODE"] == "ADDRESS" && !empty($arProp["VALUE"])) {
        $arResult[$arProp["CODE"]] = $arProp["VALUE"];
        //MAP//
    } elseif($arProp["CODE"] == "MAP" && !empty($arProp["VALUE"])) {
        $arResult[$arProp["CODE"]] = array(
            "VALUE" => $arProp["VALUE"],
            "API_KEY" => $arProp["USER_TYPE_SETTINGS"]["API_KEY"]
        );
        //TIMEZONE//
    } elseif($arProp["CODE"] == "TIMEZONE" && !empty($arProp["VALUE"])) {
        $rsElement = CIBlockElement::GetList(array(), array("ID" => $arProp["VALUE"], "IBLOCK_ID" => $arProp["LINK_IBLOCK_ID"]), false, false, array("ID", "IBLOCK_ID"));
        while($obElement = $rsElement->GetNextElement()) {
            $arElement = $obElement->GetFields();
            $arElement["PROPERTIES"] = $obElement->GetProperties();

            $arResult[$arProp["CODE"]] = $arElement["PROPERTIES"]["OFFSET"]["VALUE"];
        }
        unset($arElement, $obElement, $rsElement);
        //WORKING_HOURS//
    } elseif(in_array($arProp["CODE"], $arDays) && !empty($arProp["VALUE"])) {
        $workingHoursIds[] = $arProp["VALUE"];
        //TOUR_3D//
    } elseif($arProp["CODE"] == "TOUR_3D" && !empty($arProp["VALUE"])) {
        $arResult[$arProp["CODE"]] = array(
            "NAME" => $arProp["NAME"],
            "VALUE" => $arProp["DISPLAY_VALUE"]
        );
        //AFFILIATES//
    } elseif($arProp["CODE"] == "AFFILIATES" && !empty($arProp["VALUE"])) {
        $arResult[$arProp["CODE"]]["NAME"] = $arProp["NAME"];
        $rsElements = CIBlockElement::GetList(array(), array("ID" => $arProp["VALUE"], "IBLOCK_ID" => $arProp["LINK_IBLOCK_ID"]), false, false, array("ID", "IBLOCK_ID", "NAME", "PREVIEW_PICTURE", "DETAIL_PAGE_URL"));
        while($obElement = $rsElements->GetNextElement()) {
            $arElement = $obElement->GetFields();
            $arElement["PROPERTIES"] = $obElement->GetProperties();

            $arResult[$arProp["CODE"]]["VALUE"][$arElement["ID"]] = array(
                "NAME" => $arElement["NAME"],
                "PREVIEW_PICTURE" => $arElement["PREVIEW_PICTURE"] > 0 ? CFile::GetFileArray($arElement["PREVIEW_PICTURE"]) : false,
                "DETAIL_PAGE_URL" => $arElement["DETAIL_PAGE_URL"],
                "ADDRESS" => !empty($arElement["PROPERTIES"]["ADDRESS"]["VALUE"]) ? $arElement["PROPERTIES"]["ADDRESS"]["VALUE"] : false,
                "MAP" => !empty($arElement["PROPERTIES"]["MAP"]["VALUE"]) ? $arElement["PROPERTIES"]["MAP"]["VALUE"] : false
            );
        }
        unset($arElement, $obElement, $rsElements);
        //PHONE_EMAIL_SKYPE_LINKS//
    } elseif(($arProp["CODE"] == "PHONE" || $arProp["CODE"] == "EMAIL" || $arProp["CODE"] == "SKYPE" || $arProp["CODE"] == "LINKS") && !empty($arProp["VALUE"])) {
        $arResult[$arProp["CODE"]] = array(
            "VALUE" => $arProp["VALUE"],
            "DESCRIPTION" => $arProp["DESCRIPTION"]
        );
        //PHONE_SMS_EMAIL_EMAIL//
    } elseif(($arProp["CODE"] == "PHONE_SMS" || $arProp["CODE"] == "EMAIL_EMAIL") && !empty($arProp["VALUE"])) {
        $arResult[$arProp["CODE"]] = true;
        //DELIVERY_PAYMENT_METHODS//
    } elseif(($arProp["CODE"] == "DELIVERY_METHODS" || $arProp["CODE"] == "PAYMENT_METHODS") && !empty($arProp["~VALUE"])) {
        $arResult[$arProp["CODE"]] = $arProp["~VALUE"];
    }
}
unset($arProp);

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
        foreach($arResult["DISPLAY_PROPERTIES"] as $arProp) {
            if(in_array($arProp["CODE"], $arDays) && !empty($arProp["VALUE"])) {
                if(array_key_exists($arProp["VALUE"], $arWorkingHours)) {
                    $arResult["WORKING_HOURS"][$arProp["CODE"]] = $arWorkingHours[$arProp["VALUE"]];
                    $arResult["WORKING_HOURS"][$arProp["CODE"]]["NAME"] = $arProp["NAME"];
                }
            }
        }
        unset($arProp);
    }
    unset($arWorkingHours);
}
unset($workingHoursIds);

//PROMOTIONS_IDS//
if($arParams["SHOW_PROMOTIONS"] != "N" && intval($arParams["PROMOTIONS_IBLOCK_ID"]) > 0) {
    $rsElements = CIBlockElement::GetList(array(), array("ACTIVE" => "Y", "ACTIVE_DATE" => "Y", "IBLOCK_ID" => $arParams["PROMOTIONS_IBLOCK_ID"], "PROPERTY_OBJECT" => $arResult["ID"]), false, false, array("ID", "IBLOCK_ID"));
    while($arElement = $rsElements->GetNext()) {
        $arResult["PROMOTIONS_IDS"][$arElement["ID"]] = $arElement["ID"];
    }
    unset($arElement, $rsElements);

    if(!empty($arResult["PROMOTIONS_IDS"])) {
        $rsGroups = CIBlockElement::GetElementGroups($arResult["PROMOTIONS_IDS"], true, array("ID", "GLOBAL_ACTIVE", "IBLOCK_ELEMENT_ID"));
        while($arGroup = $rsGroups->GetNext()) {
            if($arGroup["GLOBAL_ACTIVE"] != "Y")
                unset($arResult["PROMOTIONS_IDS"][$arGroup["IBLOCK_ELEMENT_ID"]]);
        }
        unset($arGroup, $rsGroups);
    }
}

//SECTIONS//
//PRODUCTS_IDS//

/*
if(intval($arParams["CATALOG_IBLOCK_ID"]) > 0) {
	if(Bitrix\Main\Loader::includeModule("catalog")) {
		$mxResult = CCatalogSKU::GetInfoByProductIBlock($arParams["CATALOG_IBLOCK_ID"]);
		$offersIblockId = is_array($mxResult) ? $mxResult["IBLOCK_ID"] : false;
		if(intval($offersIblockId) > 0) {
			$rsElements = CIBlockElement::GetList(array(), array("ACTIVE" => "Y", "IBLOCK_ID" => $offersIblockId, "PROPERTY_OBJECT" => $arResult["ID"]), false, false, array("ID", "IBLOCK_ID"));
			while($arElement = $rsElements->GetNext()) {
				$offersIds[] = $arElement["ID"];
			}
			unset($arElement, $rsElements);

			if(!empty($offersIds)) {
				$productList = CCatalogSku::getProductList($offersIds);
				if(!empty($productList)) {
					foreach($productList as $offerId => $offerInfo) {
						$productsIds[] = $offerInfo["ID"];
					}
					unset($offerInfo, $offerId);
				}
				unset($productList);
			}
			unset($offersIds);
		}
		unset($offersIblockId, $mxResult);
	}

	$arFilter = array(
		"ACTIVE" => "Y",
		"IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"],
		"SECTION_GLOBAL_ACTIVE" => "Y"
	);

	if(!empty($productsIds)) {
		$arFilter[] = array("LOGIC" => "OR", array("PROPERTY_OBJECT" => $arResult["ID"]), array("ID" => array_unique($productsIds)));
	} else {
		$arFilter["PROPERTY_OBJECT"] = $arResult["ID"];
	}
	unset($productsIds);

	$rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID"));
	while($arElement = $rsElements->GetNext()) {
		if(!empty($arElement["IBLOCK_SECTION_ID"]))
			$arResult["SECTIONS_IDS"][] = $arElement["IBLOCK_SECTION_ID"];
		$arResult["PRODUCTS_IDS"][] = $arElement["ID"];
	}
	unset($arElement, $rsElements, $arFilter);

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
		unset($arSection, $rsSections, $arCount);
	}
}
*/
$rsIblock = CIBlock::GetList(
    Array(),
    Array(
        'TYPE' => "catalog",
        'SITE_ID' => SITE_ID,
        'ACTIVE' => 'Y',
    ), true
);

$arResult["IBLOCKS_IDS"] = [];

while($arIblock = $rsIblock->Fetch()) {
    $rsElements = CIBlockElement::GetList(array(), array("ACTIVE" => "Y", "IBLOCK_ID" => $arIblock["ID"], "SECTION_GLOBAL_ACTIVE" => "Y", "PROPERTY_OBJECT" => $arResult["ID"]), false, false, array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID"));
    while ($arElement = $rsElements->GetNext())
    {
        if (!empty($arElement["IBLOCK_SECTION_ID"]))
            $arResult["SECTIONS_IDS"][] = $arElement["IBLOCK_SECTION_ID"];

        $arResult["PRODUCTS_IDS"][] = $arElement["ID"];
        $arResult["IBLOCKS_IDS"][] = $arIblock["ID"];
    }

    unset($arElement, $rsElements);

    $mxResult = CCatalogSKU::GetInfoByProductIBlock($arIblock["ID"]);
    $offersIblockId = is_array($mxResult) ? $mxResult["IBLOCK_ID"] : false;

    $rsElements = CIBlockElement::GetList(array(), array("ACTIVE" => "Y", "IBLOCK_ID" => $offersIblockId, "PROPERTY_OBJECT" => $arResult["ID"]), false, false, array("ID", "IBLOCK_ID"));

    while($arElement = $rsElements->GetNext()) {
        $offersIds[] = $arElement["ID"];
    }

    if(!empty($offersIds))
    {
        $productList = CCatalogSku::getProductList($offersIds);
        if(!empty($productList))
        {
            foreach($productList as $offerId => $offerInfo)
            {
                $productsIds[] = $offerInfo["ID"];

                $arResult["IBLOCKS_IDS"][] = $offerInfo["IBLOCK_ID"];
            }

            unset($offerInfo, $offerId);
        }
        unset($productList);
    }
    unset($offersIds);
}

$arResult["IBLOCKS_IDS"] = array_unique($arResult["IBLOCKS_IDS"]);

if(!empty($productsIds))
{
    $productsIds = array_unique($productsIds);

    foreach($productsIds as $productId)
    {
        $arResult["PRODUCTS_IDS"][] = $productId;

        $rsElement = CIBlockElement::GetByID($productId);
        if($arElement = $rsElement->Fetch())
        {
            if(!empty($arElement["IBLOCK_SECTION_ID"]))
                $arResult["SECTIONS_IDS"][] = $arElement["IBLOCK_SECTION_ID"];
        }
    }
}

unset($productsIds);

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

//RATING_REVIEWS_COUNT//
if($arParams["USE_REVIEW"] != "N" && intval($arParams["REVIEWS_IBLOCK_ID"]) > 0) {
    $ratingSum = $reviewsCount = 0;
    $rsElements = CIBlockElement::GetList(array(), array("ACTIVE" => "Y", "IBLOCK_ID" => $arParams["REVIEWS_IBLOCK_ID"], "PROPERTY_OBJECT_ID" => $arResult["ID"]), false, false, array("ID", "IBLOCK_ID"));
    while($obElement = $rsElements->GetNextElement()) {
        $arElement = $obElement->GetFields();
        $arProps = $obElement->GetProperties();

        $ratingSum += $arProps["RATING"]["VALUE_XML_ID"];

        $reviewsCount++;
    }
    unset($arProps, $arElement, $obElement, $rsElements);

    $arResult["RATING_VALUE"] = $reviewsCount > 0 ? sprintf("%.1f", round($ratingSum / $reviewsCount, 1)) : 0;
    $arResult["REVIEWS_COUNT"] = $reviewsCount;
}