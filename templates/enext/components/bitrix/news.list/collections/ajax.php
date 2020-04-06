<?define("STOP_STATISTICS", true);
define("NOT_CHECK_PERMISSIONS", true);

$siteId = isset($_REQUEST["siteId"]) && is_string($_REQUEST["siteId"]) ? $_REQUEST["siteId"] : "";
$siteId = substr(preg_replace("/[^a-z0-9_]/i", "", $siteId), 0, 2);
if(!empty($siteId) && is_string($siteId)) {
	define("SITE_ID", $siteId);
}

if(!empty($_REQUEST["REQUEST_URI"]))
	$_SERVER["REQUEST_URI"] = $_REQUEST["REQUEST_URI"];

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();
if($request->isAjaxRequest() && ($request->get("action") == "showMoreCollections" || $request->get("action") == "showPagen")) {
	$signer = new \Bitrix\Main\Security\Sign\Signer;
	$template = $signer->unsign($request->get("template"), "news.list");
	$parameters = unserialize(base64_decode($signer->unsign($request->get("parameters"), "news.list")));

	foreach($parameters as $key => $arParams) {
		if($key != "~".$key && !empty($parameters["~".$key]))
			$parameters[$key] = $parameters["~".$key];
	}
	unset($key, $arParams);

	if($parameters["CHECK_PERMISSIONS"] == true)
		$parameters["CHECK_PERMISSIONS"] = "Y";
	
	foreach($request->getPostList() as $name => $value) {
		if(preg_match('%^PAGEN_(\d+)$%', $name, $m)) {
			global $NavNum;
			$NavNum = (int)$m[1] - 1;
		}
	}
	print_r($NavNum);
	unset($name, $value);
	
	if(!empty($parameters["GLOBAL_FILTER"]))
		$GLOBALS[$parameters["FILTER_NAME"]] = $parameters["GLOBAL_FILTER"];
	
	$APPLICATION->IncludeComponent("bitrix:news.list", $template,
		$parameters,
		false
	);
	
	if(Bitrix\Main\Loader::includeModule("iblock")) {
		$content = ob_get_contents();
		ob_end_clean();

        list(, $itemsContainer) = explode("<!-- items-container -->", $content);
        list(, $showMoreContainer) = explode("<!-- show-more-container -->", $content);
        list(, $paginationContainer) = explode("<!-- pagination-container -->", $content);

        Bitrix\Iblock\Component\Base::sendJsonAnswer(array(
            "items" => $itemsContainer,
            "showMore" => $showMoreContainer,
            "pagination" => $paginationContainer
        ));
	}
}