<?php
include 'RESTApi.php';
include '_Private.php';

/**
 * @summary - Mall API implementation root
 *          - extends general RestAPI implementation
 * @todo - Work In Progress
 */
class MPApi extends RESTApi{
    //hint: https://knowledgebase.mallgroup.com/marketplace-api/
    //https://marketplaceapiv2.docs.apiary.io/#reference/orders

    // authorization key
    private $authKey = Privates::API_KEY; //client_id
    private $apiProxy = "v1";
    private $baseUrl = "https://mpapi.mallgroup.com";

    //private $curl;

    // Fetch orders: 
    // Content-Type: application/json; Charset=UTF-8.


    public function __construct($test = true){
        if($test){
            $this->baseUrl = "private-anon-774fc3af17-marketplaceapiv2.apiary-mock.com";
            $this->apiProxy = "v1";
            $this->authKey = "";
        }
        parent::__construct();
        $this->Log = new RestApiLog("MpAPI");
    }

    /**
     * @param method {HTTP_Method} - type of HTTP method {GET/POST/PUT/DELETE}
     * @param endpoint - API endpoint (after 'url.com/v1')
     * @param params - array of URL parameters
     *          - should contain element 'url_params', which represents url params substring, 
     *            without leading '&' => e.g.: 'firstParam=xx&secondParam=yy'
     * @return {Array}
     */
    public function callMallAPI($method, $endpoint, $params){
        $url = $this->baseUrl."/".$this->apiProxy."".$endpoint."?client_id=".$this->authKey."&".$params['url_params'];
        $json_params = array();
        $response = parent::apiCall($method, $url, $params);

        $response_code = "";
        if(isset($response->errorCodes)){
            $response_code = $response->errorCodes[0]->errorCode;
        }else{
            $response_code = $response->result->code;
        }

        echo "<br> Response code: ".$response_code."<br/>";

        switch($response_code){
            case "RESOURCE_NOT_FOUND":{
                echo "Not found";
                break;
            }
            case 200:
                {
                    echo $response_code . " : " .$response->result->code." ".$response->result->status;
                    return ["data" => $response->data/*, "paging" => $response['paging']*/];
                    break;
                }
        }
        //handle response
        //return data
    }
}

?>