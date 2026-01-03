<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
error_reporting(E_ALL);

$tipo = $_GET['tipo'] ?? '';
$parentId = $_GET['parentId'] ?? null;
$folder = __DIR__ . "/catalogos/reune/";

$archivos = [
    'operaciones'  => 'CatalogoDeProductosCausasREUNE.csv',
    'productos'    => 'CatalogoDeProductosCausasREUNE.csv',
    'subproductos' => 'CatalogoDeProductosCausasREUNE.csv',
    'causas'       => 'CatalogoDeProductosCausasREUNE.csv',
    'medios'       => 'CatalogoDeMediosRecepcionREUNE.csv',
    'niveles'      => 'CatalogoDeNivelesAtenREUNE.csv',
    'estados'      => 'CatalogoDeEntidadesFederativasREUNE.csv',
    'colonias'     => 'CatalogoSepomex.csv'
];

$path = $folder . ($archivos[$tipo] ?? '');

if (!file_exists($path)) {
    echo json_encode(["error" => "No existe: " . $path]);
    exit;
}

/**
 * FUNCIÓN DE LIMPIEZA BINARIA
 * Convierte a UTF-8 y elimina caracteres de control y espacios "sucios" (A0)
 */
function limpiar_super_strict($dato) {
    // Detectar si ya es UTF-8, si no, convertir desde Windows-1252
    if (!mb_check_encoding($dato, 'UTF-8')) {
        $dato = mb_convert_encoding($dato, 'UTF-8', 'Windows-1252');
    }
    // Eliminar comillas simples de CONDUSEF, espacios duros (A0) y espacios de control
    $dato = str_replace(["'", "\xA0", "\xC2\xA0"], ["", " ", " "], $dato);
    return trim($dato);
}

// 1. CARGAR Y LIMPIAR EL ARCHIVO COMPLETO EN MEMORIA
$raw_content = file_get_contents($path);

// Eliminar el BOM (Byte Order Mark) de Excel si existe
$bom = pack('H*','EFBBBF');
$raw_content = preg_replace("/^$bom/", '', $raw_content);

// 2. CREAR RECURSO DE LECTURA
$handle = fopen('php://temp', 'r+');
fwrite($handle, $raw_content);
rewind($handle);

$response = [];

// --- LÓGICA REUNE (Transformación tipo Árbol para evitar duplicados) ---
if ($archivos[$tipo] === 'CatalogoDeProductosCausasREUNE.csv') {
    $tree = [];
    fgetcsv($handle); // Saltar encabezado

    while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
        $op  = limpiar_super_strict($data[0]);
        $pr  = limpiar_super_strict($data[1]);
        $sp  = limpiar_super_strict($data[2]);
        $cs  = limpiar_super_strict($data[3]);
        $idp = limpiar_super_strict($data[4]); // Código Producto
        $idc = limpiar_super_strict($data[5]); // Código Causa
        $rec = (strtoupper(limpiar_super_strict($data[7] ?? '')) == 'SI');

        if (empty($op)) continue;

        if (!isset($tree[$op])) $tree[$op] = [];
        if (!isset($tree[$op][$pr])) $tree[$op][$pr] = [];
        if (!isset($tree[$op][$pr][$sp])) {
            $tree[$op][$pr][$sp] = ['id' => $idp, 'causas' => []];
        }
        $tree[$op][$pr][$sp]['causas'][] = ['n' => $cs, 'id' => $idc, 'r' => $rec];
    }

    if ($tipo == 'operaciones') {
        $response = array_keys($tree);
    } elseif ($tipo == 'productos') {
        if (isset($tree[$parentId])) {
            foreach ($tree[$parentId] as $nom => $v) $response[] = ["id" => $nom, "nombre" => $nom];
        }
    } elseif ($tipo == 'subproductos') {
        foreach ($tree as $op) {
            if (isset($op[$parentId])) {
                foreach ($op[$parentId] as $nom => $info) $response[] = ["id" => $info['id'], "nombre" => $nom];
            }
        }
    } elseif ($tipo == 'causas') {
        foreach ($tree as $op) {
            foreach ($op as $pr) {
                foreach ($pr as $nomSub => $info) {
                    if ($info['id'] === $parentId) {
                        foreach ($info['causas'] as $c) {
                            if ($c['r']) $response[] = ["id" => $c['id'], "nombre" => $c['n']];
                        }
                    }
                }
            }
        }
    }
} 

// --- LÓGICA SEPOMEX (Basada en tu archivo CatalogoSepomex.csv) ---
elseif ($tipo === 'colonias') {
    // parentId aquí es el Código Postal que el usuario tecleó
    $cp_buscado = limpiar_super_strict($parentId);
    
    if (($handle = fopen($path, "r")) !== FALSE) {
        fgetcsv($handle); // Saltar encabezado: Clave Edo, Desc Edo, CP, etc.
        
        while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
            // Índice 2 es el Código Postal en tu CSV
            $cp_csv = limpiar_super_strict($data[2]);
            
            if ($cp_csv === $cp_buscado) {
                // Índice 8 es la Descripción de la colonia
                $colonia = limpiar_super_strict($data[8]);
                
                // Agregamos al array. Si hay 5 colonias con el mismo CP, las 5 se van al combo.
                $response[] = [
                    "id" => $colonia, 
                    "nombre" => $colonia
                ];
            }
        }
        fclose($handle);
    }
}

// --- LÓGICA ESTADOS, MEDIOS Y NIVELES ---
else {
    fgetcsv($handle); 
    while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
        $id = limpiar_super_strict($data[0]);
        $nombre = limpiar_super_strict($data[1]);
        if ($id !== "") $response[] = ["id" => $id, "nombre" => $nombre];
    }
}

fclose($handle);
echo json_encode($response, JSON_UNESCAPED_UNICODE);