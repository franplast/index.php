<?
header("Content-type: application/json; charset=utf-8");
$status = "1"; // Пока доступен файл или нет, позже сюда же можно добавить обновлён удалённый или нет. 0- недоступен, 1 - доступен
$message =""; // Заполняем, если есть что сказать, например, что файл не обновлялся с последней проверки, или что он не доступен
$arResult['STATUS'] = $status;
$arResult['MASSAGE'] = $message;
$arResult['RESULT'] = array();

$FILE = $_POST["file_path"];
//$FILE = "http://tk-konstruktor.ru/export/xml.php?iblock_id=16";//удалить
$d = file_get_contents($FILE);
$result = array();
$param = array();
if($d){
    $data = simplexml_load_string($d);
    foreach ($data->shop->offers->offer as $items) {
        $el = json_decode(json_encode($items), true);
        $par = get_params($items);
        if(is_array($par)){
            $param = array_merge($param,$par);
        }
        //pre($el["param"]);
        unset($el["param"]);
        $p = get_properties($el);
        if(is_array($p)){
            $result = array_merge($result,$p);
        }
        //pre(array_keys($el));
    }
}
$result = array_unique($result);
$param = array_unique($param);
$arResult['RESULT']["PROPERTIES"] = $result;
$arResult['RESULT']['PARAMS'] = $param;
echo json_encode($arResult);
//оплучение свойств
function get_properties($el,$all = false){
    $ar = array();
    foreach ($el as $name=>$val){
        if(is_array($val)){
            if($all || ($name!="@attributes" && !is_numeric($name)))$ar[] = $name;
            $t = get_properties($val,$all);
            $ar = array_merge($ar,$t);
        }
        else {
            if($all || ($name!="@attributes" && !is_numeric($name)))$ar[] = $name;
        }
    }
    return $ar;
}
//получение параметров
function get_params($items){
    $params = array();
    foreach($items->param as $par){
        foreach ($par->attributes() as $name=>$val){
            //pre($name);
            $params[] = (string)$val;
        };
    }
    return $params;

}
