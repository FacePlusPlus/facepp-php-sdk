<?php
require_once __DIR__ . '/../FppClient.php';

use Fpp\FppClient;

class TestClient
{
    private $client;

    public function __construct($host, $apiKey, $apiSecret)
    {
        $client = new FppClient($apiKey, $apiSecret, $host);
        $headers = array(
            // 'Cache-Control' => 'no-cache'
        );
        $client->setHeaders($headers);
        $this->client = $client;
    }

    public static function saveImage($path, $input, $isB64 = TRUE) {
        $file = fopen($path, 'w');
        if ($isB64) {
            $data = base64_decode($input);
        }else {
            $data = $input;
        }
        fwrite($file, $data);
        fclose($file);
        echo 'write file '. $path . " success\n";
    }

    public function testDetectFace()
    {
        $data = array(
            // 'image_url' => "https://www.faceplusplus.com.cn/scripts/demoScript/images/demo-pic10.jpg",
            'image_file' => 'demo-detect.jpg',
            // 'image_base64' => base64_encode(file_get_contents('demo-pic1.jpg')),
            // 'return_landmark' => '2',
            'return_attributes' => 'age,headpose'
        );

        $resp = $this->client->detectFace($data);
        var_dump($resp);
    }

    public function testCompareFace()
    {
        $data = array(
            'image_file1' => 'demo-compare-1.jpg',
            'image_base64_2' => base64_encode(file_get_contents('demo-detect.jpg'))
        );

        $resp = $this->client->compareFace($data);
        var_dump($resp);
    }

    public function testCreateFaceset()
    {
        $data = array(
            'display_name' => 'test_for_php_sdk'
        );

        $resp = $this->client->createFaceset($data);
        var_dump($resp);
    }

    public function testAddFaceset()
    {
        $data = array(
            'face_tokens' => '4f3e181f4f253b2bff72023369c901b0,5fd228dc11cf9c170dd826b7a57311d5',
            'faceset_token' => '62421f8dca63f03d6e07051ad1bad16b'
        );

        $resp = $this->client->addFaceset($data);
        var_dump($resp);
    }

    public function testRemoveFaceset()
    {
        $data = array(
            'face_tokens' => '4f3e181f4f253b2bff72023369c901b0',
            'faceset_token' => '62421f8dca63f03d6e07051ad1bad16b'
        );

        $resp = $this->client->removeFaceset($data);
        var_dump($resp);
    }

    public function testUpdateFaceset()
    {
        $data = array(
            'faceset_token' => '62421f8dca63f03d6e07051ad1bad16b',
            'display_name' => 'test_for_all_sdk'
        );

        $resp = $this->client->updateFaceset($data);
        var_dump($resp);
    }

    public function testGetDetailFaceset()
    {
        $data = array(
            'faceset_token' => '62421f8dca63f03d6e07051ad1bad16b'
        );

        $resp = $this->client->getDetailFaceset($data);
        var_dump($resp);
    }

    public function testDeleteFaceset()
    {
        $data = array(
            'faceset_token' => '62421f8dca63f03d6e07051ad1bad16b',
            'check_empty' => '0'
        );

        $resp = $this->client->deleteFaceset($data);
        var_dump($resp);
    }

    public function testGetFacesets()
    {
        $resp = $this->client->getFacesets([]);
        var_dump($resp);
    }

    public function testAnalyzeFace()
    {
        $data = array(
            'face_tokens' => '5fd228dc11cf9c170dd826b7a57311d5',
            'return_attributes' => 'gender,age,emotion'
        );

        $resp = $this->client->analyzeFace($data);
        var_dump($resp);
    }

    public function testGetDetailFace()
    {
        $data = array(
            'face_token' => '5fd228dc11cf9c170dd826b7a57311d5'
        );

        $resp = $this->client->getDetailFace($data);
        var_dump($resp);
    }

    public function testSetUserIdFace()
    {
        $data = array(
            'face_token' => '5fd228dc11cf9c170dd826b7a57311d5',
            'user_id' => 'huodongdong'
        );

        $resp = $this->client->setUserIdFace($data);
        var_dump($resp);
    }

    public function testBeautyFace()
    {
        $data = array(
            'image_file' => 'demo-beauty.jpg',
            'whitening' => 100,
            'smoothing' => 100
        );
        $resp = $this->client->beautyFace($data);

        $file = fopen('demo-beauty-result.jpg', 'w');
        fwrite($file, base64_decode($resp->body['result']));
        fclose($file);
    }

    public function testDetectHumanBody()
    {
        $data = array(
            'image_url' => 'https://www.faceplusplus.com.cn/scripts/demoScript/images/demo-pic69.jpg'
        );

        $resp = $this->client->detectHumanBody($data);
        var_dump($resp);
    }

    public function testSegmentHumanBody()
    {
        $data = array(
            // 'image_url' => 'https://www.faceplusplus.com.cn/scripts/demoScript/images/demo-pic69.jpg'
            'image_file' => 'demo-segment.jpg'
        );

        $resp = $this->client->segmentHumanBody($data);
        var_dump($resp->body['request_id']);

        // save b64 confidence data
        // $b64data = $resp->body['result'];
        // self::saveImage('v2-demo-segment.b64', $b64data, $isB64=FALSE);

        // save png data
        $b64data = $resp->body['body_image'];
        self::saveImage('v2_body_segment.png', $b64data, $isB64=TRUE);
    }

    public function testGestureHumanBody()
    {
        $data = array(
            'image_url' => 'https://www.faceplusplus.com.cn/scripts/demoScript/images/demo-pic43.jpg'
        );

        $resp = $this->client->gestureHumanBody($data);
        var_dump($resp);
    }

    public function testDetectIdCard()
    {
        $data = array(
            'image_url' => 'https://www.faceplusplus.com.cn/scripts/demoScript/images/demo-pic641.jpg'
        );

        $resp = $this->client->detectIdCard($data);
        var_dump($resp);
    }

    public function testDetectDriverLicense()
    {
        $data = array(
            'image_url' => 'https://www.faceplusplus.com.cn/scripts/demoScript/images/demo-pic63.jpg'
        );

        $resp = $this->client->detectDriverLicense($data);
        var_dump($resp);
    }

    public function testDetectBankCard()
    {
        $data = array(
            'image_file' => 'demo-bankcard.jpg'
        );

        $resp = $this->client->detectBankCard($data);
        var_dump($resp);
    }

    public function testDetectVehicleLicense()
    {
        $data = array(
            'image_url' => 'https://www.faceplusplus.com.cn/scripts/demoScript/images/demo-pic62.jpg'
        );

        $resp = $this->client->detectVehicleLicense($data);
        var_dump($resp);
    }

    public function testDetectSenceObject()
    {
        $data = array(
            'image_file' => 'demo-sence.jpg'
        );

        $resp = $this->client->detectSenceObject($data);
        var_dump($resp);
    }

    public function testRecognizeText()
    {
        $data = array(
            'image_file' => 'demo-word.png'
        );

        $resp = $this->client->recognizeText($data);
        var_dump($resp);
    }

    public function testMergeFace()
    {
        $data = array(
            'image_file' => 'demo-compare-1.jpg'
        );

        $detectResp = $this->client->detectFace($data);
        if (!$detectResp->isOk()) {
            echo "detect failed \n";
            return null;
        }

        $body = $detectResp->body;
        var_dump($body);
        $rect = $body['faces'][0]['face_rectangle'];

        $merge = array(
            'template_file' => 'demo-compare-1.jpg',
            'template_rectangle' => $rect['top'] . ',' . $rect['left'] . ',' . $rect['width'] . ',' . $rect['height'],
            'merge_url' => 'https://www.faceplusplus.com.cn/scripts/demoScript/images/demo-pic26.jpg'
        );

        echo $merge['template_rectangle'] . "\n";

        $resp = $this->client->mergeFace($merge);
        var_dump($resp);

        $file = fopen('demo-merge-result.jpg', 'w');
        fwrite($file, base64_decode($resp->body['result']));
        fclose($file);
    }

    public function testDetectLicensePlate()
    {
        $data = array(
            'image_url' => 'https://www.faceplusplus.com.cn/scripts/demoScript/images/demo-pic67.jpg'
        );

        $resp = $this->client->detectLicensePlate($data);
        var_dump($resp);
    }

    public function test()
    {
        $funcs = [
            'testDetectFace', 'testCompareFace', 'testGetDetailFaceset', 'testAddFaceset', 'testRemoveFaceset', 'testUpdateFaceset',
            'testDeleteFaceset', 'testGetDetailFaceset', 'testSetUserIdFace', 'testGetDetailFace', 'testDetectHumanBody',
            'testSegmentHumanBody', 'testGestureHumanBody', 'testDetectIdCard', 'testDetectDriverLicense', 'testDetectVehicleLicense',
            'testDetectBankCard', 'testDetectSenceObject', 'testRecognizeText', 'testMergeFace', 'testDetectLicensePlate'
        ];
        $handle = fopen ("php://stdin","r");
        foreach ($funcs as $func) {
            $line = fgets($handle);
            if (trim($line) == 'q') {
                fclose($handle);
                break;
            }
            echo $func . ' sending ...' . "\n\n";
            $this->$func();
        }
    }
}

$host = 'https://api-cn.faceplusplus.com';
$apiKey = '<Your Key>';
$apiSecret = '<Your Secret>';

$tester = new TestClient($host, $apiKey, $apiSecret);
// $tester->testDetectFace();
// $tester->testCompareFace();
// $tester->testGetDetailFaceset();
// $tester->testAddFaceset();
// $tester->testRemoveFaceset();
// $tester->testUpdateFaceset();
// $tester->testDeleteFaceset();
// $tester->testGetDetailFaceset();
// $tester->testAnalyzeFace();
// $tester->testGetDetailFace();
// $tester->testSetUserIdFace();
// $tester->testGetDetailFace();
// $tester->testBeautyFace();
// $tester->testDetectHumanBody();
// $tester->testSegmentHumanBody();
// $tester->testGestureHumanBody();
// $tester->testDetectIdCard();
// $tester->testDetectDriverLicense();
// $tester->testDetectVehicleLicense();
// $tester->testDetectBankCard();
// $tester->testDetectSenceObject();
// $tester->testRecognizeText();
$tester->testMergeFace();
// $tester->testDetectLicensePlate();
// $tester->test();
?>
