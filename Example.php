<?php
require_once __DIR__ . '/Autoload.php'; // phpcs:ignore

use CustomJsonEncode\JsonEncoder;

$jsonEncodeObj = JsonEncoder::getObject();
$data = [
    'firstname' => 'Ramesh',
    'lastname' => 'Jangid',
    'address' => [
        'city' => 'Mumbai',
        'state' => 'Maha',
    ]
];
$jsonEncodeObj->startObject();
$jsonEncodeObj->addKeyValue(key: 'details', value: $data);
$jsonEncodeObj->endObject();
