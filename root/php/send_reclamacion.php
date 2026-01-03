<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once __DIR__ . '/../Mailer.php';
$config = require __DIR__ . '/../config.php';

function loadEnv($path) {
    if (!file_exists($path)) return [];
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $env = [];
    foreach ($lines as $line) {
        if (empty($line) || strpos($line, '#') === 0) continue;
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) $env[trim($parts[0])] = trim($parts[1], '" ');
    }
    return $env;
}
$env = loadEnv(__DIR__ . '/../.env');

function formatFecha($fecha) {
    return !empty($fecha) ? date("d/m/Y", strtotime($fecha)) : null;
}

$input = $_POST;

if (isset($input['modulo']) && $input['modulo'] === 'reclamacion') {
    
    $isMonetario = ($input['monetario'] === "SI");
    // Medio 6 o 7 requieren folio Condusef según manual
    $medioRecepcion = (int)$input['medio_id'];
    $folioCondusef = (!empty($input['folio_condusef']) && in_array($medioRecepcion, [6, 7])) 
                     ? $input['folio_condusef'] 
                     : null;

    // CONSTRUCCIÓN ESTRICTA L13
    $jsonFinal = [
        "RecDenominacion"        => $input['INSTITUCION_NOMBRE'] ?? '',
        "RecSector"              => $input['INSTITUCION_SECTOR_TEXTO'] ?? '',
        "RecTrimestre"           => (int)$input['trimestre'],
        "RecNumero"              => 1,
        "RecFolioAtencion"       => $input['folio'],
        "RecEstadoConPend"       => 2, 
        "RecFechaReclamacion"    => formatFecha($input['fecha_recepcion']),
        "RecFechaAtencion"       => formatFecha($input['fecha_resolucion']),
        "RecMedioRecepcionCanal" => $medioRecepcion,
        "RecProductoServicio"    => $input['producto_id'], // Enviamos el ID numérico del catálogo
        "RecCausaMotivo"         => $input['causa_id'],    // Enviamos el ID numérico del catálogo
        "RecFechaResolucion"     => formatFecha($input['fecha_resolucion']),
        "RecFechaNotifiUsuario"  => formatFecha($input['fecha_notificacion']),
        "RecEntidadFederativa"   => (int)$input['estado_id'],
        "RecCodigoPostal"        => (int)$input['cp_busqueda'],
        "RecMunicipioAlcaldia"   => (int)$input['municipio_id'],
        "RecLocalidad"           => (int)$input['localidad_id'],
        "RecColonia"             => (int)$input['colonia_id'],
        "RecMonetario"           => $input['monetario'], 
        "RecMontoReclamado"      => $isMonetario ? (float)$input['monto'] : null,
        "RecImporteAbonado"      => $isMonetario ? 0.0 : null,
        "RecFechaAbonoImporte"   => null, // Corregido: null sin comillas
        "RecPori"                => $input['pori'], 
        "RecTipoPersona"         => (int)$input['tipo_persona'], 
        "RecSexo"                => ($input['tipo_persona'] == "2") ? null : $input['sexo'], // Nulo si es Persona Moral
        "RecEdad"                => ($input['tipo_persona'] == "2") ? null : (int)$input['edad'], // Nulo si es Persona Moral
        "RecSentidoResolucion"   => (int)$input['sentido_id'], 
        "RecNivelAtencion"       => (int)$input['nivel_id'],   
        "RecFolioCondusef"       => $folioCondusef,
        "RecReversa"             => 0
    ];

    // IMPORTANTE: JSON_PRESERVE_ZERO_FRACTION asegura que 0.0 no se convierta en 0
    $jsonData = json_encode([$jsonFinal], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    try {
        $mailer = new Mailer($config['smtp']);
        $enviado = $mailer->send(
            $env['MAIL_TO'],
            "JSON L13 VALIDADO - Folio " . $input['folio'],
            "Adjunto archivo corregido con tipos de datos estrictos (nulls y numéricos).",
            ['filename' => 'reclamacion_condusef.json', 'content' => $jsonData]
        );

        if ($enviado) {
            echo json_encode(["status" => "success", "message" => "JSON generado y enviado."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al enviar correo."]);
        }
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}