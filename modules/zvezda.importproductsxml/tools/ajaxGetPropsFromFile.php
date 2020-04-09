<?php
header("Content-type: application/json; charset=utf-8");
$status = "1"; // Пока доступен файл или нет, позже сюда же можно добавить обновлён удалённый или нет. 0- недоступен, 1 - доступен
$message =""; // Заполняем, если есть что сказать, например, что файл не обновлялся с последней проверки, или что он не доступен
$arResult['STATUS'] = $status;
$arResult['MASSAGE'] = $message;
$arResult['RESULT'] = array();

$FILE = $_POST["file_path"];
//$FILE = "index.xml";//удалить
$d = file_get_contents($FILE);
$fields = array();
$props = array();
if($d){
    $data = simplexml_load_string($d);
    foreach ($data->shop->offers->offer as $items) {
        $el = json_decode(json_encode($items), true);
        $par = get_name_params($el["param"],true);
        if(is_array($par)){
            $props = array_merge($props,$par);
        }
        unset($el["param"]);
        $p = get_name_params($el);
        if(is_array($p)){
            $fields = array_merge($fields,$p);
        }
    }
}
$fields = array_unique($fields);
$props = array_unique($props);
$arResult['ITEMS']["FIELDS"] = $fields;
$arResult['ITEMS']['PROPS'] = $props;

echo json_encode($arResult);

function get_name_params($el,$all = false){
    $ar = array();
    foreach ($el as $name=>$val){
        if(is_array($val)){
            if($all || ($name!="@attributes" && !is_numeric($name)))$ar[] = $name;
            $t = get_name_params($val,$all);
            $ar = array_merge($ar,$t);
        }
        else {
            if($all || ($name!="@attributes" && !is_numeric($name)))$ar[] = $name;
        }
    }
    return $ar;
}
