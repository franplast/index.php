<?
// TODO проверяем доступность файла и возвращаем статус
$FILE = $_POST["file_path"];
$ch = curl_init($FILE);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_exec($ch);
$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
// $retcode > 400 -> not found, $retcode = 200, found.
curl_close($ch);
if($retcode==200) {
    $arResult = [
        "STATUS" => 1,
    ];
}
else {
    $arResult = [
        "STATUS" => 0,
        "MESSAGE" => $retcode
    ];
}
echo json_encode($arResult);
