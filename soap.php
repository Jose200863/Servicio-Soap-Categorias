<?php
// Punto de entrada del servicio SOAP.
require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/ServicioCategorias.php';

$cfg = require __DIR__ . '/config/config.php';

// Permite ver el WSDL con soap.php?wsdl
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['wsdl'])) {
    header('Content-Type: text/xml; charset=utf-8');
    readfile($cfg['wsdl']);
    exit;
}

ini_set('soap.wsdl_cache_enabled', '0');

$db       = Database::conectar($cfg);
$servicio = new ServicioCategorias(
    new CategoriaRepository($db, $cfg['tabla']),
    new Middleware()
);

$server = new SoapServer($cfg['wsdl'], ['soap_version' => SOAP_1_1]);
$server->setObject($servicio);
$server->handle();
