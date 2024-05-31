<?php

require 'Firmadores/FirmadorBoliviaSinlge.php';

$firmador = new FirmadorBoliviaSingle('firma.digital.p12', "danielsD10");

try {
    $xmlFirmado = $firmador->firmarRuta(__DIR__ . '/ejemplos/factura bolivia real.xml');
//    $xmlFirmado = $firmador->firmar('contenido xml');

    file_put_contents('signed.xml', $xmlFirmado);

} catch (FirmaException $e) {
    print 'Hubo un error: ' . $e->getMessage();
}
