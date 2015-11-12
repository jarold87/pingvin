<?php

require_once dirname(__FILE__) . '/classes/apicall.php';

$username = '[USERNAME]';
$apiKey = '[APIKEY]';

$apiCall = new ApiCall($username, $apiKey);
$apiCall->setFormat('json');

$url = '[SHOPNAME].api.shoprenter.hu/products';
$response = $apiCall->execute('GET', $url);
//$response = $apiCall->execute('POST', $url, array('sku' => 'something', 'price' => 1000));
//$response = $apiCall->execute('DELETE', $url);

echo '<pre>';
print_r($response->getParsedResponseBody());
