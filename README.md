# Open API SDK for php developers

## Requirements

- PHP 5.5.9+.
- cURL extension.
- GD extension.

## Structure
```
├── FppClient.php              // the api client
└── lib
    ├── Adapter.php            // the http request adapter, all request be sended by it
    ├── ContentTypes.php       // request body content type
    ├── Image.php              // the image process unit
    ├── MimeTypes.php          // the request body mime type
    ├── MultiPartForm.php      // multipart/for-data request class
    ├── Request.php            // the request class
    ├── RequestException.php   // exception
    └── Response.php           // the response class
```

## Example

```php
include_once 'FppClient.php';

use Fpp\FppClient;

$host = 'https://api-cn.faceplusplus.com';
$apiKey = '<Your Key>';
$apiSecret = '<Your Secret>';

$client = new FppClient($apiKey, $apiSecret, $host);

$data = array(
    'image_url' => "https://www.faceplusplus.com.cn/scripts/demoScript/images/demo-pic10.jpg",
    'return_landmark' => '2',
    'return_attributes' => 'age,headpose'
);

$resp = $client->detectFace($data);
var_dump($resp);

```

## License

licensed under the MIT
