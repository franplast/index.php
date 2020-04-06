<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

//CURRENCIES//
if(!empty($templateData["TEMPLATE_LIBRARY"])) {
	$loadCurrency = false;
	if(!empty($templateData["CURRENCIES"])) {
		$loadCurrency = \Bitrix\Main\Loader::includeModule("currency");
	}

	CJSCore::Init($templateData["TEMPLATE_LIBRARY"]);

	if($loadCurrency) {?>
		<script type="text/javascript">
			BX.Currency.setCurrencies(<?=$templateData["CURRENCIES"]?>);
		</script>
	<?}
}

//LAZY_LOAD//
$request = \Bitrix\Main\Context::getCurrent()->getRequest();
if($request->isAjaxRequest() && ($request->get("action") === "showMore" || $request->get("action") === "deferredLoad")) {
	$content = ob_get_contents();
	ob_end_clean();

	list(, $itemsContainer) = explode("<!-- items-container -->", $content);
	list(, $paginationContainer) = explode("<!-- pagination-container -->", $content);
	list(, $epilogue) = explode("<!-- component-end -->", $content);

	if($arParams["AJAX_MODE"] === "Y") {
		$component->prepareLinks($paginationContainer);
	}

	$component::sendJsonAnswer(array(
		"items" => $itemsContainer,
		"pagination" => $paginationContainer,
		"epilogue" => $epilogue
	));
}