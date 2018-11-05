<?php
require_once __DIR__ . '/../lib/Adapter.php';
require_once __DIR__ . '/../lib/MultiPartForm.php';

use Fpp\Adapter;
use Fpp\MultiPartForm;

$adapter = new Adapter();
$adapter->setSslVerification(false);

/*
$conf = array(
    CURLOPT_PROXY => "127.0.0.1",
    CURLOPT_PROXYPORT => 8888,
);
$adapter->setCurlOpts($conf);
*/

$data = array(
    'api_key' => "<Your Key>",
    'api_secret' => "<Your Secret>",
    'image_url' => "https://www.faceplusplus.com.cn/scripts/demoScript/images/demo-pic10.jpg",
);

$form = new MultiPartForm();
$form->addForms($data);

var_dump($form);
echo $form . "\n";

$headers = array(
    'Content-Type' => $form->getContentType(),
    'Cache-Control' => 'no-cache'
);

$res = $adapter->post("https://api-cn.faceplusplus.com/facepp/v3/detect", $form, null, $headers);
$body = $res->body;
echo json_encode($body) . "\n";
echo $body['image_id'] . "\n";
?>
