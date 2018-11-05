<?php
require_once __DIR__ . '/../lib/MultiPartForm.php';

use Fpp\MultiPartForm;

$m = new MultiPartForm();
$m->addForm('api_key', 'testKey123');
$m->addForm('api_secret', 'testSecretabc');
$m->addForm('image_url', 'https://www.faceplusplus.com.cn/scripts/demoScript/images/demo-pic10.jpg');

echo $m;
?>
