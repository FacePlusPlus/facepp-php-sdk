<?PHP
/**
* Face++ PHP SDK
* author: Tianye
* since:  2013-12-11
**/
class Facepp{
    ######################################################
    ### If you choose Amazon(US) server,please use the ###
    ### http://apius.faceplusplus.com/v2               ###
    ### or                                             ###
    ### https://apius.faceplusplus.com/v2              ###
    ######################################################
    var $server = 'http://apicn.faceplusplus.com/v2';
    #var $server = 'https://apicn.faceplusplus.com/v2';
    #var $server = 'http://apius.faceplusplus.com/v2';
    #var $server = 'https://apius.faceplusplus.com/v2';

    #############################################
    ### set your api key and api secret here. ###
    #############################################
    var $api_key = '{your API KEY}';
    var $api_secret = '{your API SECRET}';

    public function __construct($api_key=NULL, $api_secret=NULL, $server=NULL){
        if($api_key){
            $this->api_key = $api_key;
        }
        if($api_secret){
            $this->api_secret = $api_secret;
        }
        if($server){
            $this->server = $server;
        }
    }

    /**
    * @param $method : The Face++ API 
    * @param $params : Request Parameters
    * @return : Array {'http_code':'Http Status Code', 'request_url':'Http Request URL','body':' JSON Response'}
    **/
    public function execute($method,$params){
        if(empty($params)){
            $params=array();
        }
        $params['api_key'] = $this->api_key;
        $params['api_secret'] = $this->api_secret;

        return $this->request("{$this->server}{$method}",$params);
    }

    private function request($request_url , $request_body){
        $useragent = 'Faceplusplus PHP SDK/1.0';
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $request_url);
        curl_setopt($curl_handle, CURLOPT_FILETIME, TRUE);
        curl_setopt($curl_handle, CURLOPT_FRESH_CONNECT, FALSE);
        curl_setopt($curl_handle, CURLOPT_CLOSEPOLICY, CURLCLOSEPOLICY_LEAST_RECENTLY_USED);
        curl_setopt($curl_handle, CURLOPT_MAXREDIRS, 5);
        curl_setopt($curl_handle, CURLOPT_HEADER, FALSE);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl_handle, CURLOPT_TIMEOUT, 5184000);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 120);
        curl_setopt($curl_handle, CURLOPT_NOSIGNAL, TRUE);
        curl_setopt($curl_handle, CURLOPT_REFERER, $request_url);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, $useragent);
        if (extension_loaded('zlib')){
            curl_setopt($curl_handle, CURLOPT_ENCODING, '');
        }
        curl_setopt($curl_handle, CURLOPT_POST, TRUE);
        if(array_key_exists('img',$request_body)){
            $request_body['img'] = '@'.$request_body['img'];
        }else{
            $request_body=http_build_query($request_body);
        }
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $request_body);
        $response_text = curl_exec($curl_handle);
        $reponse_header = curl_getinfo($curl_handle);
        curl_close($curl_handle);
        return array('http_code'=>$reponse_header['http_code'],'request_url'=>$request_url,'body'=>$response_text);
    }
}
