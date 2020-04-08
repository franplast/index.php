<?
// здесь проверяем доступность файла,
// проверяем у нас есть ли копия файла,
// если есть - сверяем дату и время обновления из <yml_catalog date="2020-04-08 11:28">,
// Если идиентичны - записываем это в сообщение, но продолжаем процесс
// скачиваем во временную папку или в постоянную для сверки в будущем, если файл был - заменяем. Имя файла должно быть таким, что бы можно было определить к какому магазину он привязан.
// Возможно записываен дату обновления файла из yml_catalog date="2020-04-08 11:28" в элемент ИБ для сверки в след раз
// Перебираем разделы файла, строим полное многоуровневое дерево
// Собираем массив данных

//в текущем коде идет формирование дерева разделов, на его основе строим массив вложенности.
// Нужно доработать что бы массив формировался без построения дерева
$status = "1"; // Пока доступен файл или нет, позже сюда же можно добавить обновлён удалённый или нет. 0- недоступен, 1 - доступен
$message =""; // Заполняем, если есть что сказать, например, что файл не обновлялся с последней проверки, или что он не доступен
$arResult['STATUS'] = $status;
$arResult['MASSAGE'] = $message;

$FILE = $_POST["file_path"];
//$FILE = "index.xml";//удалить
$d = file_get_contents($FILE);
if($d){
    $data = simplexml_load_string($d);
    $category = array();
    $search_cat = array();
    $category_ar = array();
    $parent_ar = array();
    //перенос категорий в массив
    foreach ($data->shop->categories->category as $row) {
        $id = intval($row['id']);
        $parent = intval($row['parentId']);
        $name = str_replace(array("\r\n", "\r", "\n"), "", strval($row));
        $ar = array("NAME" => $name);
        if ($parent) {
            $ar["parentId"] = $parent;
            $parent_ar[$parent] = $parent;
        }
        $category_ar[$id] = $ar;
    }
    foreach ($category_ar as $id => $row) {
        //$id = intval($row['id']);
        $parent = intval($row['parentId']);
        $name = $row["NAME"];
        if (!$parent) {
            $category[$id] = $row;
        } else {
            if ($category[$parent]) {
                $category[$parent]["SECTIONS"][$id] = array("NAME" => $name, "SECTIONS" => array());
            } else {
                $search_cat[] = array("par" => $parent, "id" => $id, "NAME" => $name);
            }
        }

    }
    //pre(count($search_cat));
    //построение дерева, до 11 уровней вложенностей
    for ($i = 0, $c = 10; $i < $c; ++$i) {
        if (!count($search_cat)) break;
        foreach ($search_cat as $ind => $el) {

            $par = $el["par"];
            $id = $el["id"];
            $name = $el["NAME"];
            if (isset($category[$par])) {
                $category[$par]["SECTIONS"][$id] = array("NAME" => $name, "SECTIONS" => array(),);
                unset($search_cat[$ind]);
                continue;
            } else {
                $node = find_node($category, $par, $name, $id);
                if (!$node) continue;
                $category = array_replace_recursive($category, $node);//сливаем полученный массив с общим
                //$node["SECTIONS"][$id] = array("NAME"=>$name,"SECTIONS"=>array());

                unset($search_cat[$ind]);
                unset($node);
            }
        }
    }
    $result_ar = array();
    line_node(1,$category,$result_ar);
    $arResult['SECTIONS'] = $result_ar;
    header("Content-type: application/json; charset=utf-8");
    echo json_encode($arResult);

}
//построение массива вложенности из дерева.
function line_node($level,$ar,&$result){
    foreach ($ar as $id=>$el){
        $result[] = array("ID"=>$id,"NAME"=>$el["NAME"],'DEPTH_LEVEL'=>$level);
        if(is_array($el["SECTIONS"])){
            line_node($level+1,$el["SECTIONS"],$result);
        }
    }
}
//построение дерева, вернет разделы с вложенными подразделами
function find_node($dataset, $id, $name, $ind, $res = array())
{
    //global $result; // иначе $result будет undefined
    foreach ($dataset as $key => $value) {
        if ($key != $id) {
            //if (isset($dataset[$key]['SECTIONS'])) {
            //pre($dataset[$key]['SECTIONS']);
            $r = find_node($dataset[$key]['SECTIONS'], $id, $name, $ind, $res);
            if (is_array($r)) {
                //$dataset[$key]["SECTIONS"] = array();
                $dataset[$key]["SECTIONS"] = $r;//array_replace($dataset[$key]["SECTIONS"],$r);
                return $dataset;
            }
            //return false;
            //}
        } else {
            $dataset[$key]["SECTIONS"][$ind]["NAME"] = $name;
            //$dataset[$key]["SECTIONS"][$ind]["ITEMS"] = $items;
            return $dataset;
        }
    }
    return false;
}
