<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(0);

$cp_buscado = $_GET['cp'] ?? '';
$path = __DIR__ . "/catalogos/reune/CatalogoSepomex.csv";

$res = ["id_estado" => null, "estado" => null, "id_municipio" => null, "municipio" => null, "colonias" => []];

if (file_exists($path) && !empty($cp_buscado)) {
    $cp_int_buscado = intval($cp_buscado);
    $content = file_get_contents($path);
    
    // Eliminar el BOM de Excel si existe
    $bom = pack('H*','EFBBBF');
    $content = preg_replace("/^$bom/", '', $content);
    
    // Convertir de Windows-1252 a UTF-8 solo si es necesario
    if (!mb_check_encoding($content, 'UTF-8')) {
        $content = mb_convert_encoding($content, 'UTF-8', 'Windows-1252');
    }

    $handle = fopen('php://temp', 'r+');
    fwrite($handle, $content);
    rewind($handle);
    
    fgetcsv($handle); // Saltar encabezado

    while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
        if (intval($data[2]) === $cp_int_buscado) {
            if (!$res["estado"]) {
                $res["id_estado"] = trim($data[0]);
                $res["estado"] = trim($data[1]);
                $res["id_municipio"] = trim($data[3]);
                $res["municipio"] = trim($data[4]);
            }
            $res["colonias"][] = ["id_loc" => trim($data[7]), "nombre" => trim($data[8])];
        }
    }
    fclose($handle);
}

echo json_encode($res, JSON_UNESCAPED_UNICODE);