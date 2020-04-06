<?
set_time_limit(0);
ini_set('memory_limit', '1000M');

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("highloadblock");

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Bitrix\Main\Config\Option;

CModule::IncludeModule("iblock");
CModule::IncludeModule("sale");
CModule::IncludeModule("catalog");

$el = new CIBlockElement;
$ibp = new CIBlockProperty;

class importProductsXml
{
    const MODULE = "zvezda.importproductsxml";
    public $shopIblockId; // ИБ Магазины
    public $productIblockType; // ID типа инфоблока товаров
    public $iterationStep; // Шаг итерации
    public $mapingIblockId; // ID инфоблока для мапинга категорий верхнего уровня
    public $arMaping = []; // массив сопостовления корневых разделов к ИБ
    public $arCategories = []; // массив категори ищ xml, заполняется методом getArrayCategoriesXml
    public $shopId; // ID магазина
    public $countOffers; // кол-во товаров в xml
    public $xmlContent; // Полученный xml файл
    public $xmlUrl; // url магазина из xml
    public $xmlPath; // путь до xml файла
    public $arOffersXmlId = []; // массив внешних кодов offers
    public $email; // email на который будут отправляться логи работы скрипта
    public $updatePrice; // флаг обновлять ли цену
    public $updateProperties; // флаг обновлять ли св-ва
    public $updatePictures; // флаг обновлять ли картинки MORE_PHOTO

    public $mode; // режим запуска импорта shop или xml
    public $rootCategoryId; // id корневой дериктории полученные при $mode == xml
    public $HlBlockIdCategories;
    public $HlBlockIdParams;

    public $fieldsOffers = []; // поля узлов offer

    public $arFieldsProduct = [
        "description" => [
            "NAME" => "Детальное описание",
            "CODE" => "DETAIL_TEXT"
        ],
        "picture" => [
            "NAME" => "Детальная картинка",
            "CODE" => "DETAIL_PICTURE"
        ],
    ];

    // массив св-в товара
    public $arPropertiesProduct = [
        "vendor" => [
            "NAME" => "Бренд",
            "CODE" => "BRAND"
        ],
        "model" => [
            "NAME" => "Модель",
            "CODE" => "MODEL"
        ],
        "url" => [
            "NAME" => "Ссылка на источник",
            "CODE" => "URL"
        ],
        "picture" => [
            "NAME" => "Дополнительные изображения",
            "CODE" => "MORE_PHOTO"
        ],
        "oldprice" => [
            "NAME" => "Старая цена",
            "CODE" => "OLD_PRICE"
        ],
        "vendorCode" => [
            "NAME" => "Артикул",
            "CODE" => "ARTICLE"
        ],
        "barcode" => [
            "NAME" => "Штрих-код",
            "CODE" => "BARCODE"
        ],
        "weight" => [
            "NAME" => "Вес",
            "CODE" => "WEIGHT"
        ]
    ];

    // массив полей ТП
    public $arFieldsSku = [
        "price" => [
            "NAME" => "Цена",
        ],
        "picture" => [
            "NAME" => "Детальная картинка",
            "CODE" => "DETAIL_PICTURE"
        ]
    ];

    // массив св-в ТП
    public $arPropertiesSku = [
        "url" => [
            "NAME" => "Ссылка на источник",
            "CODE" => "URL"
        ]
    ];

    public $arParamsCategories = []; // массив св-в offer из узлов param

    public function __construct($shopId = 0, $xmlPath = "")
    {
        $this->shopIblockId = 13;
        $this->productIblockType = "catalog";
        $this->iterationStep = 50;
        $this->mapingIblockId = 266;
        $this->email = Option::get(self::MODULE, "email");
        $this->updatePrice = Option::get(self::MODULE, "update_price");
        $this->updateProperties = Option::get(self::MODULE, "update_properties");
        $this->updatePictures = Option::get(self::MODULE, "update_pictures");

        $this->HlBlockIdCategories = 4;
        $this->HlBlockIdParams = 3;

        $this->mode = !empty($shopId) ? "shop" : "xml";

        if(!empty($shopId))
        {
            $this->shopId = $shopId;

            if(!$this->setXmlContentByShopId($this->shopId))
                exit();
        }
        elseif(!empty($xmlPath))
        {
            if(!$this->setXmlContentByXmlPath($xmlPath))
                exit();
        }
        else
        {
            // на странице настройки модуля мы получаем массив магазинов, нельзя exit
            //exit;
        }

        if(!empty($shopId) || !empty($xmlPath))
        {
            if($xmlUrl = $this->getShopUrlXml())
                $this->xmlUrl = $xmlUrl;

            $this->getArrayCategoriesXml();

            if($arOffers = $this->getArrayOffersXml())
            {
                $this->countOffers = count($arOffers);

                foreach($arOffers as $i => $arOffer)
                {
                    $this->arOffersXmlId[] = $this->xmlUrl."_".$arOffer->attributes()->id->__toString();

                    $categoryId = $arOffer->categoryId->__toString();
                    $rootCategoryId = $this->getRootCategoryByCategoryId($categoryId);

                    foreach($arOffer->children() as $child)
                    {
                        if($child->getName() == "param")
                        {
                            foreach($arOffers[$i]->param as $param)
                            {
                                $paramName = $param->attributes()->name->__toString();

                                if(!array_key_exists($paramName, $this->arParamsCategories[$rootCategoryId][$paramName]))
                                {
                                    $paramCode = Cutil::translit($paramName, "ru", ["change_case" => "U"]);
                                    $this->arParamsCategories[$rootCategoryId][$paramName] = $paramCode;
                                }
                            }
                        }
                        else
                        {
                            $this->fieldsOffers[$child->getName()] = "";
                        }
                    }
                }
            }
        }
    }

    public function charactersReplace($str)
    {
        $str = str_replace('&quot;', '"', $str);
        $str = str_replace('&amp;', '&', $str);
        $str = str_replace('&gt;', '>', $str);
        $str = str_replace('&lt;', '<', $str);
        $str = str_replace('&apos;', "'", $str);

        return $str;
    }

    public function getRootCategoryByCategoryId($categoryId) // получаем корневую категорию по id категории из xml
    {
        if(empty($categoryId))
            return false;

        $arCategory = $this->arCategories[$categoryId];

        if(is_null($arCategory["PARENT_ID"]))
        {
            return $categoryId;
        }
        else
        {
            return $this->getRootCategoryByCategoryId($arCategory["PARENT_ID"]);
        }
    }

    public function getBreadcrumbsCategories($categoryId, $breadcrumb = "") // получаем хлебные крошки от корня до категории товара в xml
    {
        if(empty($categoryId))
            return false;

        $arCategory = $this->arCategories[$categoryId];

        if(is_null($arCategory["PARENT_ID"]))
        {
            return $arCategory["NAME"].$breadcrumb;
        }
        else
        {
            $breadcrumb = $breadcrumb." -> ".$arCategory["NAME"];
            return $this->getBreadcrumbsCategories($arCategory["PARENT_ID"], $breadcrumb);
        }
    }

    public function getShopUrlXml() // получаем url магазина из xml
    {
        if($this->xmlContent)
        {
            $arXml = simplexml_load_string($this->xmlContent);
            $arXml = json_encode($arXml);
            $arXml = json_decode($arXml, true);

            $xmlUrl = $arXml["shop"]["url"];

            if(!empty($xmlUrl))
            {
                $xmlUrl = str_replace("http:", "", $xmlUrl);
                $xmlUrl = str_replace("https:", "", $xmlUrl);
                $xmlUrl = str_replace("www.", "", $xmlUrl);
                $xmlUrl = str_replace("/", "", $xmlUrl);

                return $xmlUrl;
            }
        }

        return false;
    }

    public function isExistBrand($brandName) // проверяем существует ли бренд по имени
    {
        global $el;

        $rsElement = $el->getList([], ["IBLOCK_ID" => 22, "ACTIVE" => "Y", "NAME" => $brandName], false, false, ["ID"]);

        if($arElement = $rsElement->Fetch())
            return $arElement["ID"];

        return false;
    }

    public function addBrand($brandName)
    {
        if(empty($brandName))
            return false;

        global $el;

        $arFields = [
            "NAME" => $brandName,
            "CODE" => Cutil::translit($brandName, "ru", ["replace_space" => "-","replace_other" => "-"]),
            "IBLOCK_ID" => 22,
            "ACTIVE" => "Y"
        ];

        if($brandId = $el->add($arFields))
            return $brandId;
        else
            return false;
    }

    public function getArrayShops() // получаем массив магазинов у которых проставлена ссылка на xml файл
    {
        global $el;
        $arShops = [];

        $rsElement = $el->getList([], ["IBLOCK_ID" => $this->shopIblockId, "ACTIVE" => "Y", "!PROPERTY_FILE_REFERENCE" => false], false, false, ["IBLOCK_ID", "PROPERTY_FILE_REFERENCE", "ID", "NAME"]);

        while($arElement = $rsElement->Fetch())
        {
            $arShops[] = [
                "ID" => $arElement["ID"],
                "NAME" => $arElement["NAME"],
            ];
        }

        if(!empty($arShops))
            return $arShops;

        return false;
    }

    public function setXmlContentByShopId($shopId) // устанавливаем содержимое файла в переменную $this->xmlContent
    {
        global $el;
        $rsElement = $el->getList([], ["IBLOCK_ID" => $this->shopIblockId, "ID" => $shopId, "ACTIVE" => "Y", "!PROPERTY_FILE_REFERENCE" => false], false, false, ["IBLOCK_ID", "PROPERTY_FILE_REFERENCE"]);

        if($arElement = $rsElement->Fetch())
        {
            $xmlPath = $arElement["PROPERTY_FILE_REFERENCE_VALUE"];

            $this->xmlPath = $xmlPath;

            if($this->xmlContent = file_get_contents($xmlPath))
                return true;
        }

        return false;
    }

    public function setXmlContentByXmlPath($xmlPath)
    {
        $this->xmlPath = $xmlPath;

        if($this->xmlContent = file_get_contents($xmlPath))
            return true;

        return false;
    }

    public function getArrayCategoriesXml() // получить массив категорий из xml
    {
        $arXml = simplexml_load_string($this->xmlContent);
        $arXml = json_encode($arXml->xpath("/yml_catalog/shop/categories/category"));
        $arXml = json_decode($arXml, true);

        if($arXml)
        {
            foreach($arXml as $category)
            {
                $this->arCategories[$category["@attributes"]["id"]] = [
                    "PARENT_ID" => $category["@attributes"]["parentId"],
                    "NAME" => $category[0]
                ];
            }

            if(!empty($this->arCategories))
                return $this->arCategories;
        }

        return false;
    }

    public function getCategoryIblockIdById($categoryId)
    {
        /*
        global $el;

        $rsElement = $el->getList([], ["IBLOCK_ID" => $this->mapingIblockId, "ACTIVE" => "Y", "ID" => $categoryId], false, false, ["ID", "IBLOCK_ID", "PROPERTY_IBLOCK_ID"]);

        if($arElement = $rsElement->Fetch())
        {
            if(!empty($arElement["PROPERTY_IBLOCK_ID_VALUE"]))
                return $arElement["PROPERTY_IBLOCK_ID_VALUE"];
            else
                return 0;
        }
        */

        $entityDataClass = $this->entityDataClass($this->HlBlockIdCategories);

        $rsData = $entityDataClass::getList(["select" => ["UF_IBLOCK_ID"], "filter" => ["ID" => $categoryId]]);

        if($arData = $rsData->Fetch())
        {
            if(!empty($arData["UF_IBLOCK_ID"]))
                return $arData["UF_IBLOCK_ID"];
            else
                return "";
        }
    }

    public function updateIblockIdByCategoryId($categoryId, $iblockId)
    {
        /*
        global $el;

        $el->SetPropertyValuesEx($categoryId, $this->mapingIblockId, ["IBLOCK_ID" => $iblockId]);
        */

        $entityDataClass = $this->entityDataClass($this->HlBlockIdCategories);

        $entityDataClass::update($categoryId, ["UF_IBLOCK_ID" => $iblockId]);
    }

    public function operationCategories() // операции с категориями из xml
    {
        global $el;

        if($this->getArrayCategoriesXml()) // если получен массив категорий из xml файла
        {
            foreach($this->arCategories as $categoryId => $arCategory)
            {
                if(is_null($arCategory["PARENT_ID"])) // если корневой раздел
                {
                    $xmlId = $this->xmlUrl."_".$categoryId;

                    if($this->mode == "xml")
                        $this->rootCategoryId = $categoryId;

                    if($iblockId = $this->getIblockIdByCategoryXmlId($xmlId)) // если получен ID ИБ по XML_ID категории верхнего уровня
                    {
                        $this->arMaping[$categoryId] = $iblockId;
                    }
                    else
                    {
                        if(!$this->isExistCategory($xmlId)) // если категории не найдено добавляем категорию в ИБ
                        {
                            /*
                            $arProp = [
                                "ID" => $categoryId,
                            ];

                            $arFields = [
                                "IBLOCK_ID" => $this->mapingIblockId,
                                "XML_ID" => $xmlId,
                                "NAME" => $arCategory["NAME"],
                                "PROPERTY_VALUES" => $arProp,
                            ];

                            $elementId = $el->Add($arFields);
                            */

                            $entityDataClass = $this->entityDataClass($this->HlBlockIdCategories);

                            $arData = [
                                "UF_CATEGORY_NAME" => $arCategory["NAME"],
                                "UF_XML_ID" => $xmlId,
                                "UF_CATEGORY_ID" => $categoryId
                            ];

                            $result = $entityDataClass::add($arData);
                        }
                        else // если категория есть, но не заполнено св-во iblock_id
                        {

                        }
                    }
                }
                else // если не корневой раздел
                {

                }
            }
        }
    }

    public function getIblockIdByCategoryXmlId($xmlId)  // получаем ID инфоблока по XML_ID
    {
        /*
        global $el;

        $rsElement = $el->getList([], ["IBLOCK_ID" => $this->mapingIblockId, "ACTIVE" => "Y", "XML_ID" => $xmlId], false, false, ["ID", "PROPERTY_IBLOCK_ID"]);

        if($arElement = $rsElement->Fetch())
        {
            if(!empty($arElement["PROPERTY_IBLOCK_ID_VALUE"]))
                return $arElement["PROPERTY_IBLOCK_ID_VALUE"];
        }

        return false;
        */


        $entityDataClass = $this->entityDataClass($this->HlBlockIdCategories);

        $rsCategory = $entityDataClass::getList(["select" => ["UF_IBLOCK_ID"], "filter" => ["UF_XML_ID" => $xmlId]]);

        if($arCategory = $rsCategory->Fetch())
        {
            if(!empty($arCategory["UF_IBLOCK_ID"]))
                return $arCategory["UF_IBLOCK_ID"];
        }

        return false;

    }

    public function isExistCategory($xmlId) // проверяем найдена ли категория верхнего уровня по xml_id
    {
        /*
        global $el;

        $rsElement = $el->getList([], ["IBLOCK_ID" => $this->mapingIblockId, "ACTIVE" => "Y", "XML_ID" => $xmlId], false, false, ["ID"]);

        if($arElement = $rsElement->Fetch())
            return true;

        return false;
        */


        $entityDataClass = $this->entityDataClass($this->HlBlockIdCategories);

        $rsCategory = $entityDataClass::getList(["select" => ["ID"], "filter" => ["UF_XML_ID" => $xmlId]]);

        if($arCategory = $rsCategory->Fetch())
            return true;

        return false;
    }

    public function getCategoriesInIblockByXmlUrl()
    {
        global $el;

        if($this->mode == "shop")
        {
            $xmlFilter = $this->xmlUrl."_%";
        }
        else
        {
            if($this->rootCategoryId)
                $xmlFilter = $this->xmlUrl."_".$this->rootCategoryId;
        }

        $arCategories = [];

        /*
        $rsElement = $el->getList([], ["IBLOCK_ID" => $this->mapingIblockId, "ACTIVE" => "Y", "XML_ID" => $xmlFilter], false, false, ["ID", "NAME", "PROPERTY_IBLOCK_ID"]);

        while($arElement = $rsElement->Fetch())
        {
            $arCategories[$arElement["ID"]] = [
                "IBLOCK_ID" => $arElement["PROPERTY_IBLOCK_ID_VALUE"],
                "NAME" => $arElement["NAME"]
            ];
        }

        if(!empty($arCategories))
            return $arCategories;

        return false;
        */

        $entityDataClass = $this->entityDataClass($this->HlBlockIdCategories);

        $rsData = $entityDataClass::getList(["select" => ["*"], "filter" => ["UF_XML_ID" => $xmlFilter]]);

        while($arData = $rsData->Fetch())
        {
            $arCategories[$arData["ID"]] = [
                "IBLOCK_ID" => $arData["UF_IBLOCK_ID"],
                "NAME" => $arData["UF_CATEGORY_NAME"],
                "CATEGORY_ID" => $arData["UF_CATEGORY_ID"],
                "XML_ID" => $arData["UF_XML_ID"],
            ];
        }

        if(!empty($arCategories))
            return $arCategories;

        return false;
    }

    public function getArIblocks()
    {
        $arIblocks = [];

        $rsIblock = CIBlock::GetList([], ['TYPE' => 'catalog', 'SITE_ID' => "s1", 'ACTIVE' => 'Y'], false);

        while($arIblock = $rsIblock->Fetch())
        {
            $arIblocks[] = [
                "ID" => $arIblock["ID"],
                "NAME" => $arIblock["NAME"],
            ];
        }

        if(!empty($arIblocks))
            return $arIblocks;

        return false;
    }

    public function isExistPropertyIblock($propertyCode, $iblockId)
    {
        global $ibp;
        $rsIblockProp = $ibp->GetList([], ["IBLOCK_ID" => $iblockId, "CODE" => $propertyCode]);

        if($arIblockProp = $rsIblockProp->GetNext())
            return true;

        return false;
    }

    public function getArrayOffersXml() // получить массив товаров из xml
    {
        $arXml = simplexml_load_string($this->xmlContent);
        return $arXml->xpath("/yml_catalog/shop/offers/offer");
    }

    public function operationOffers($numOffer = 0, $countOffers = 10) // операции с товарами из xml
    {
        global $el, $ibp;

        $numSum = $numOffer + $countOffers;

        $countUpdateOffers = 0; // обновленные ТП
        $countAddOffers = 0; // добавленные ТП
        $countAddOffersExistProducts = 0; // добавленные ТП к существующим товарам
        $countNotAddOffersReasonNoName = 0; // не добавленные товары
        $countAddProducts = 0; // добавленные товары
        $countAddBrands = 0; // добавленные бренды

        if($numOffer == 0)
        {
            session_start();

            $_SESSION['log_import'] = [
                "countUpdateOffers" => 0,
                "countAddOffers" => 0,
                "countAddOffersExistProducts" => 0,
                "countNotAddOffersReasonNoName" => 0,
                "countAddProducts" => 0,
                "countAddBrands" => 0,
            ];
        }

        if($arOffers = $this->getArrayOffersXml())
        {
            for($i=$numOffer; $i<count($arOffers); $i++)
            {
                if($i == $numSum)
                {
                    // записываем лог в сессию
                    $_SESSION['log_import']["countUpdateOffers"] += $countUpdateOffers;
                    $_SESSION['log_import']["countAddOffers"] += $countAddOffers;
                    $_SESSION['log_import']["countAddOffersExistProducts"] += $countAddOffersExistProducts;
                    $_SESSION['log_import']["countNotAddOffersReasonNoName"] += $countNotAddOffersReasonNoName;
                    $_SESSION['log_import']["countAddProducts"] += $countAddProducts;
                    $_SESSION['log_import']["countAddBrands"] += $countAddBrands;

                    //$file = 'log.txt';
                    //file_put_contents($file, $countUpdateOffers."\n", FILE_APPEND);

                    break; // прекращаем работу цикла
                }

                $arParams = [];

                foreach($arOffers[$i]->param as $param)
                {
                    $paramName = $param->attributes()->name->__toString();
                    //$paramCode = Cutil::translit($paramName, "ru", ["change_case" => "U"]);
                    $paramValue = $param->__toString();

                    $arParams[] = [
                        "NAME" => $paramName,
                        "VALUE" => $this->charactersReplace($paramValue),
                        "CODE" => Cutil::translit($paramName, "ru", ["change_case" => "U"]),
                    ];
                }

                $description = $arOffers[$i]->description->__toString(); // получаем описание как строку

                // преобразуем объект $arOffers[$i] в массив
                $arOffers[$i] = json_encode($arOffers[$i]);
                $arOffers[$i] = json_decode($arOffers[$i], true);

                // функционал обновления картинок оставить для функционала обновления товара

                /*
                if($this->updatePictures) // если на странице настройки модуля выбрано обновлять картинки
                {
                    if(is_array($arOffers[$i]["picture"]))
                    {
                        $arPictures = [];

                        foreach($arOffers[$i]["picture"] as $pictureUrl)
                        {
                            $arPictures[] = ["VALUE" => CFile::MakeFileArray($pictureUrl), "DESCRIPTION" => ""];
                        }

                        $el::SetPropertyValuesEx($arSku["ID"], $skuIblockId, ["MORE_PHOTO" => ["VALUE" => ["del" => "Y"]]]); // удаляем старые картинки
                        $el::SetPropertyValuesEx($arSku["ID"], $skuIblockId, ["MORE_PHOTO" => $arPictures]); // обновляем картинки у ТП

                        $updatePictures = true;
                    }
                }
                */

                $categoryId = $arOffers[$i]["categoryId"];
                $rootCategoryId = $this->getRootCategoryByCategoryId($categoryId);
                $productIblockId = $this->arMaping[$rootCategoryId];

                $breadcrumbs = $this->getBreadcrumbsCategories($categoryId); // получаем хлебные крошки категории

                if($productIblockId) // если у категории товара из xml проставлен ID ИБ
                {
                    // заменяем символы
                    $arOffers[$i]["name"] = $this->charactersReplace($arOffers[$i]["name"]);
                    $arOffers[$i]["vendor"] = $this->charactersReplace($arOffers[$i]["vendor"]);
                    $arOffers[$i]["model"] = $this->charactersReplace($arOffers[$i]["model"]);
                    $arOffers[$i]["description"] = $this->charactersReplace($description);

                    if(!empty($arOffers[$i]["prop"])) // получаем id магазина по логину пользователя
                    {
                        //$rsUser = CUser::GetList(($by = "NAME"), ($order = "desc"), ["WORK_MAILBOX" => $arOffers[$i]["prop"]], ["FIELDS" => ["ID"]]);
                        $rsUser = CUser::GetList(($by = "NAME"), ($order = "desc"), ["LOGIN" => $arOffers[$i]["prop"]], ["FIELDS" => ["ID"]]);

                        if($arUser = $rsUser->Fetch()) // если получен пользователь по логину
                        {
                            $rsObject = $el->GetList([], ["IBLOCK_ID" => $this->shopIblockId, "PROPERTY_TENANT" => $arUser["ID"]], false, false, ["ID", "PROPERTY_FILE_REFERENCE"]);

                            if($arObject = $rsObject->Fetch()) // если получен магазин по ID пользователя
                            {
                                $this->shopId = $arObject["ID"]; // устанавливаем ID магазина

                                if(!empty($arObject["PROPERTY_FILE_REFERENCE_VALUE"])) // если у магазина проставленна ссылка на xml файл пропускаем offer
                                    continue;
                            }
                        }
                    }

                    $xmlId = $this->xmlUrl."_".$arOffers[$i]["@attributes"]["id"]; // внешний код

                    if(!empty($arOffers[$i]["name"]))
                    {
                        $name = $arOffers[$i]["name"];
                    }
                    elseif(!empty($arOffers[$i]["model"]) && !empty($arOffers[$i]["vendor"]))
                    {
                        $name = $arOffers[$i]["model"]." ".$arOffers[$i]["vendor"];
                    }
                    else
                    {
                        //echo "У offer с внешним кодом $xmlId нету названия";
                        $countNotAddOffersReasonNoName++;

                        continue;
                    }

                    // проверка св-в товара на существование в ИБ

                    $arProperties = [
                        "MODEL" => "Модель",
                        "SECTION_YML" => "Структура разделов из YML",
                        "WEIGHT" => "Вес",
                        "ARTICLE" => "Артикул",
                        "URL" => "Ссылка на источник",
                        "OLD_PRICE" => "Старая цена",
                        "BARCODE" => "Штрих-код",
                    ];

                    foreach($arProperties as $propertyCode => $propertyValue)
                    {
                        if(!$this->isExistPropertyIblock($propertyCode, $productIblockId))
                        {
                            $arFields = Array(
                                "NAME" => $propertyValue,
                                "ACTIVE" => "Y",
                                "SORT" => "500",
                                "CODE" => $propertyCode,
                                "PROPERTY_TYPE" => "S",
                                "IBLOCK_ID" => $productIblockId
                            );

                            $ibp->Add($arFields);
                        }
                    }

                    $mxResult = CCatalogSKU::GetInfoByProductIBlock($productIblockId);
                    $skuIblockId = $mxResult ? $mxResult["IBLOCK_ID"] : false;

                    if($skuIblockId) // если получен ИБ ТП
                    {
                        // проверка св-в ТП на существование в ИБ

                        $arProperties = [
                            "ITERATION" => "Дата итерации",
                            "SECTION_YML" => "Структура разделов из YML",
                            "URL" => "URL",
                        ];

                        foreach($arProperties as $propertyCode => $propertyValue)
                        {
                            if(!$this->isExistPropertyIblock($propertyCode, $skuIblockId))
                            {
                                $arFields = Array(
                                    "NAME" => $propertyValue,
                                    "ACTIVE" => "Y",
                                    "SORT" => "500",
                                    "CODE" => $propertyCode,
                                    "PROPERTY_TYPE" => "S",
                                    "IBLOCK_ID" => $skuIblockId
                                );

                                $ibp->Add($arFields);
                            }
                        }

                        // снимаем активацию св-ва в наличии у тех ТП, которых нету в xml

                        $rsSku = $el->GetList([], ["IBLOCK_ID" => $skuIblockId, "PROPERTY_OBJECT" => $this->shopId], false, false, ["ID", "XML_ID"]);

                        while($arSku = $rsSku->Fetch())
                        {
                            if(array_search($arSku["XML_ID"], $this->arOffersXmlId) === false)
                                $el->SetPropertyValuesEx($arSku["ID"], $skuIblockId, ["IN_STOCK" => false]);
                        }

                        $propInstockId = false;

                        $rsPropInStock = CIBlockPropertyEnum::GetList([], ["IBLOCK_ID" => $skuIblockId, "CODE" => "IN_STOCK"]);

                        if($arPropInStock = $rsPropInStock->Fetch()) // если получено ID св-во IN_STOCK
                            $propInstockId = $arPropInStock["ID"]; // ID св-ва в наличии

                        $rsSku = $el->GetList([], ["IBLOCK_ID" => $skuIblockId, "XML_ID" => $xmlId, "!PROPERTY_NOT_UPDATE_XML_FILE_VALUE" => "Y"], false, false, ["ID"]);

                        if($arSku = $rsSku->Fetch()) // если ТП найдено по внешнему коду, обновляем его
                        {
                            $updateProperties = false;
                            $updatePictures = false;
                            $updatePrice = false;

                            if($this->updateProperties) // если на странице настройки модуля выбрано обновлять св-ва
                            {
                                $mxResult = CCatalogSku::GetProductInfo($arSku["ID"]); // получаем ID товара привязанного к ТП

                                $arProp = [
                                    "OBJECT" => $this->shopId, // магазин
                                    "ITERATION" => date("Y-m-d"), // дата итерации
                                    "URL" => $arOffers[$i]["url"], // ссылка на источник
                                    "SECTION_YML" => $breadcrumbs, // хлебные крошки категорий из xml
                                    "CML2_LINK" => $mxResult["ID"], // связь с товаром
                                ];

                                if($propInstockId) // если получено ID св-во IN_STOCK
                                    $arProp["IN_STOCK"] = $propInstockId; // в наличии

                                $arParamsSku = $this->getArPropertiesParams($skuIblockId, $categoryId, $arParams);

                                foreach($arParamsSku as $propertyCode => $propertyValue)
                                {
                                    $arProp[$propertyCode] = $propertyValue;
                                }

                                $el::SetPropertyValuesEx($arSku["ID"], $skuIblockId, $arProp);

                                $picture = is_array($arOffers[$i]["picture"]) ? CFile::MakeFileArray($arOffers[$i]["picture"][0]) : CFile::MakeFileArray($arOffers[$i]["picture"]);

                                $arFields = [
                                    "NAME" => $name,
                                    "CODE" => Cutil::translit($name, "ru", ["replace_space" => "-","replace_other" => "-"]),
                                    "ACTIVE" => "Y",
                                    "DETAIL_PICTURE" => $picture,
                                    "PREVIEW_PICTURE" => $picture,
                                ];

                                if(!empty($arParamsSku["DETAIL_TEXT"]))
                                {
                                    $arFields["DETAIL_TEXT"] = $arParamsSku["DETAIL_TEXT"];
                                    $arFields["DETAIL_TEXT_TYPE"] = "html";
                                }

                                if(!empty($arParamsSku["PREVIEW_TEXT"]))
                                {
                                    $arFields["PREVIEW_TEXT"] = $arParamsSku["PREVIEW_TEXT"];
                                    $arFields["PREVIEW_TEXT_TYPE"] = "html";
                                }

                                if($el->Update($arSku["ID"], $arFields)) // обновляем поля, св-ва ТП
                                    $updateProperties = true;
                            }

                            if($this->updatePrice) // если на странице настройки модуля выбрано обновлять цену
                            {
                                $rsPrice = CPrice::GetList([],["PRODUCT_ID" => $arSku["ID"], "CATALOG_GROUP_ID" => 1]);

                                if($arPrice = $rsPrice->Fetch()) // если получена цена ТП
                                {
                                    if($arPrice["PRICE"] != $arOffers[$i]["price"]) // если цена отличается
                                    {
                                        CPrice::Update($arPrice["ID"], ["PRICE" => $arOffers[$i]["price"]]); // обновляем цену
                                        $updatePrice = true;
                                    }
                                }
                            }

                            if($updateProperties || $updatePictures || $updatePrice) // если что-то обновили у ТП прибавляем счетчик
                                $countUpdateOffers++;
                        }
                        else // если ТП не найдено по внешнему коду, ищем товар по названию
                        {
                            $rsProduct = $el->GetList([], ["IBLOCK_ID" => $productIblockId, "NAME" => $name], false, false, ["ID"]);

                            if($arProduct = $rsProduct->Fetch()) // если товар найден по названию, добавляем ТП к этому товару
                            {
                                $arProp = [
                                    "OBJECT" => $this->shopId, // магазин
                                    "ITERATION" => date("Y-m-d"), // дата итерации
                                    "URL" => $arOffers[$i]["url"], // ссылка на источник
                                    "SECTION_YML" => $breadcrumbs, // хлебные крошки категорий из xml
                                    "CML2_LINK" => $arProduct["ID"], // связь с товаром
                                ];

                                if(is_array($arOffers[$i]["picture"])) // если картинки являются массивом, добавляем картинки в св-во MORE_PHOTO
                                {
                                    $arPictures = [];

                                    foreach($arOffers[$i]["picture"] as $pictureUrl)
                                    {
                                        $arPictures[] = CFile::MakeFileArray($pictureUrl);
                                    }

                                    $arProp["MORE_PHOTO"] = $arPictures;
                                }

                                if($propInstockId) // если получено ID св-во IN_STOCK
                                    $arProp["IN_STOCK"] = $propInstockId; // в наличии

                                $arParamsSku = $this->getArPropertiesParams($skuIblockId, $categoryId, $arParams);

                                foreach($arParamsSku as $propertyCode => $propertyValue)
                                {
                                    $arProp[$propertyCode] = $propertyValue;
                                }

                                $picture = is_array($arOffers[$i]["picture"]) ? CFile::MakeFileArray($arOffers[$i]["picture"][0]) : CFile::MakeFileArray($arOffers[$i]["picture"]);

                                $arFields = [
                                    "IBLOCK_ID" => $skuIblockId,
                                    "PROPERTY_VALUES"=> $arProp,
                                    "XML_ID" => $xmlId,
                                    "NAME" => $name,
                                    "CODE" => Cutil::translit($name, "ru", ["replace_space" => "-","replace_other" => "-"]),
                                    "ACTIVE" => "Y",
                                    "DETAIL_PICTURE" => $picture,
                                    "PREVIEW_PICTURE" => $picture,
                                ];

                                if(!empty($arParamsSku["DETAIL_TEXT"]))
                                {
                                    $arFields["DETAIL_TEXT"] = $arParamsSku["DETAIL_TEXT"];
                                    $arFields["DETAIL_TEXT_TYPE"] = "html";
                                }

                                if(!empty($arParamsSku["PREVIEW_TEXT"]))
                                {
                                    $arFields["PREVIEW_TEXT"] = $arParamsSku["PREVIEW_TEXT"];
                                    $arFields["PREVIEW_TEXT_TYPE"] = "html";
                                }

                                if($skuId = $el->add($arFields)) // добавляем ТП и цену
                                {
                                    if(CCatalogProduct::Add(["ID" => $skuId, "VAT_INCLUDED" => "N"]))
                                        CPrice::Add(["PRODUCT_ID" => $skuId, "CATALOG_GROUP_ID" => 1, "CURRENCY" => "RUB", "PRICE" => $arOffers[$i]["price"]]);

                                    $countAddOffersExistProducts++;
                                }
                            }
                            else // если товар не найден по названию, создаем товар и ТП
                            {
                                $arProp = [
                                    "OBJECT" => $this->shopId, // магазин
                                    "SECTION_YML" => $breadcrumbs, // хлебные крошки категорий из xml
                                    "MODEL" => $arOffers[$i]["model"],
                                    "WEIGHT" => $arOffers[$i]["weight"],
                                    "ARTICLE" => $arOffers[$i]["vendorCode"],
                                    "URL" => $arOffers[$i]["url"],
                                    "OLD_PRICE" => $arOffers[$i]["oldprice"],
                                    "BARCODE" => $arOffers[$i]["barcode"],
                                ];

                                // если картинки являются массивом, добавляем картинки в св-во MORE_PHOTO

                                if(is_array($arOffers[$i]["picture"]))
                                {
                                    $arPictures = [];

                                    foreach($arOffers[$i]["picture"] as $pictureUrl)
                                    {
                                        $arPictures[] = CFile::MakeFileArray($pictureUrl);
                                    }

                                    $arProp["MORE_PHOTO"] = $arPictures;
                                }

                                $picture = is_array($arOffers[$i]["picture"]) ? CFile::MakeFileArray($arOffers[$i]["picture"][0]) : CFile::MakeFileArray($arOffers[$i]["picture"]);

                                // добавляем, проставляем св-ва из param
                                $arParamsProduct = $this->getArPropertiesParams($productIblockId, $categoryId, $arParams);

                                foreach($arParamsProduct as $propertyCode => $propertyValue)
                                {
                                    $arProp[$propertyCode] = $propertyValue;
                                }

                                // проставляем бренд товару
                                if(!empty($arOffers[$i]["vendor"]))
                                {
                                    if($brandId = $this->isExistBrand($arOffers[$i]["vendor"]))
                                    {
                                        $arProp["BRAND"] = $brandId;
                                    }
                                    else
                                    {
                                        if($brandId = $this->addBrand($arOffers[$i]["vendor"]))
                                        {
                                            $arProp["BRAND"] = $brandId;

                                            $countAddBrands++;
                                        }
                                    }
                                }

                                $arFields = [
                                    "IBLOCK_ID" => $productIblockId,
                                    "PROPERTY_VALUES"=> $arProp,
                                    "XML_ID" => $xmlId,
                                    "NAME" => $name,
                                    "CODE" => Cutil::translit($name, "ru", ["replace_space" => "-","replace_other" => "-"]),
                                    "ACTIVE" => "N",
                                    "DETAIL_PICTURE" => $picture,
                                    "PREVIEW_PICTURE" => $picture,
                                    "DETAIL_TEXT" => $arOffers[$i]["description"],
                                    "DETAIL_TEXT_TYPE" => "html",
                                ];

                                if(!empty($arParamsProduct["DETAIL_TEXT"]))
                                {
                                    $arFields["DETAIL_TEXT"] = $arParamsProduct["DETAIL_TEXT"];
                                    $arFields["DETAIL_TEXT_TYPE"] = "html";
                                }

                                if(!empty($arParamsProduct["PREVIEW_TEXT"]))
                                {
                                    $arFields["PREVIEW_TEXT"] = $arParamsProduct["PREVIEW_TEXT"];
                                    $arFields["PREVIEW_TEXT_TYPE"] = "html";
                                }

                                if($productId = $el->add($arFields)) // если добавлен товар, добавляем ТП
                                {
                                    // если у узла offer есть вес записываем товару в спец. св-во
                                    if(!empty($arOffers[$i]["weight"]))
                                        CCatalogProduct::Add(["ID" => $productId, "WEIGHT" => $arOffers[$i]["weight"]]);

                                    $countAddProducts++;

                                    $arProp = [
                                        "OBJECT" => $this->shopId, // магазин
                                        "ITERATION" => date("Y-m-d"), // дата итерации
                                        "URL" => $arOffers[$i]["url"], // ссылка на источник
                                        "SECTION_YML" => $breadcrumbs, // хлебные крошки категорий из xml
                                        "CML2_LINK" => $productId, // связь с товаром
                                    ];

                                    if($propInstockId) // если получено ID св-во IN_STOCK
                                        $arProp["IN_STOCK"] = $propInstockId; // в наличии

                                    $arParamsSku = $this->getArPropertiesParams($skuIblockId, $categoryId, $arParams);

                                    foreach($arParamsSku as $propertyCode => $propertyValue)
                                    {
                                        $arProp[$propertyCode] = $propertyValue;
                                    }

                                    $arFields = [
                                        "IBLOCK_ID" => $skuIblockId,
                                        "PROPERTY_VALUES" => $arProp,
                                        "XML_ID" => $xmlId,
                                        "NAME" => $name,
                                        "CODE" => Cutil::translit($name, "ru", ["replace_space" => "-","replace_other" => "-"]),
                                        "ACTIVE" => "Y",
                                        "DETAIL_PICTURE" => $picture,
                                        "PREVIEW_PICTURE" => $picture,
                                    ];

                                    if(!empty($arParamsSku["DETAIL_TEXT"]))
                                    {
                                        $arFields["DETAIL_TEXT"] = $arParamsSku["DETAIL_TEXT"];
                                        $arFields["DETAIL_TEXT_TYPE"] = "html";
                                    }

                                    if(!empty($arParamsSku["PREVIEW_TEXT"]))
                                    {
                                        $arFields["PREVIEW_TEXT"] = $arParamsSku["PREVIEW_TEXT"];
                                        $arFields["PREVIEW_TEXT_TYPE"] = "html";
                                    }

                                    if($skuId = $el->add($arFields)) // добавляем ТП и цену
                                    {
                                        if(CCatalogProduct::Add(["ID" => $skuId, "VAT_INCLUDED" => "N"]))
                                            CPrice::Add(["PRODUCT_ID" => $skuId, "CATALOG_GROUP_ID" => 1, "CURRENCY" => "RUB", "PRICE" => $arOffers[$i]["price"]]);

                                        $countAddOffers++;
                                    }
                                }
                            }
                        }
                    }
                }

                if($i == ($this->countOffers - 1)) // если конец импорта
                {

                    $file = 'log.txt';
                    file_put_contents($file, $countUpdateOffers."\n", FILE_APPEND);

                    $_SESSION['log_import']["countUpdateOffers"] += $countUpdateOffers;
                    $_SESSION['log_import']["countAddOffers"] += $countAddOffers;
                    $_SESSION['log_import']["countAddOffersExistProducts"] += $countAddOffersExistProducts;
                    $_SESSION['log_import']["countNotAddOffersReasonNoName"] += $countNotAddOffersReasonNoName;
                    $_SESSION['log_import']["countAddProducts"] += $countAddProducts;
                    $_SESSION['log_import']["countAddBrands"] += $countAddBrands;

                    $arFields = [
                        "COUNT_UPDATE_OFFERS" => $_SESSION['log_import']["countUpdateOffers"],
                        "COUNT_ADD_OFFERS" => $_SESSION['log_import']["countAddOffers"],
                        "COUNT_ADD_OFFERS_EXIST_PRODUCTS" => $_SESSION['log_import']["countAddOffersExistProducts"],
                        "COUNT_NOT_ADD_OFFERS_REASON_NO_NAME" => $_SESSION['log_import']["countNotAddOffersReasonNoName"],
                        "COUNT_ADD_PRODUCTS" => $_SESSION['log_import']["countAddProducts"],
                        "COUNT_ADD_BRANDS" => $_SESSION['log_import']["countAddBrands"],
                        "EMAIL" => $this->email,
                        "FILE" => $this->xmlPath,
                    ];

                    $this->sendEmail("XML_IMPORT_RESULT", $arFields);
                }
            }
        }
    }

    public function sendEmail($event, $arFields) // отправляем сообщение на указанное событие
    {
        CEvent::Send($event, "s1", $arFields);
    }

    public function entityDataClass($hlId)
    {
        if(empty($hlId))
            return false;

        $hlblock = HL\HighloadBlockTable::getById($hlId)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        return $entity->getDataClass();
    }

    public function getArPropertiesParams($iblockId, $categoryId, $arParams)
    {
        if(empty($iblockId) || empty($categoryId) || empty($arParams))
            return false;

        $arProp = [];

        $entityDataClassCategories = $this->entityDataClass($this->HlBlockIdCategories);
        $entityDataClassParams = $this->entityDataClass($this->HlBlockIdParams);

        $categoryXmlId = $this->xmlUrl."_".$categoryId;

        $rsCategory = $entityDataClassCategories::getList(["select" => ["ID"], "filter" => ["UF_XML_ID" => $categoryXmlId]]);

        if($arCategory = $rsCategory->Fetch())
        {
            $arHlParams = [];

            $rsHlParam = $entityDataClassParams::getList(["select" => ["UF_PARAM_CODE", "UF_PROPERTY_CODE"], "filter" => ["UF_HL_CATEGORY_ID" => $arCategory["ID"], "UF_IBLOCK_ID" => $iblockId, "UF_IGNORE" => 0]]);

            while($arHlParam = $rsHlParam->Fetch())
            {
                $arHlParams[$arHlParam["UF_PARAM_CODE"]] = $arHlParam["UF_PROPERTY_CODE"];
            }

            foreach($arParams as $arParam)
            {
                if(array_key_exists($arParam["CODE"], $arHlParams))
                    $arProp[$arHlParams[$arParam["CODE"]]] = $arParam["VALUE"];
            }

            if(!empty($arProp))
                return $arProp;
        }
    }
}
?>