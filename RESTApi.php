<?php
include 'RESTApiLog.php';

abstract class HTTP_Method{
    const GET = 0;      // Read
    const POST = 1;     // Create
    const PUT = 2;      // Update
    const DELETE = 3;   // Delete
}

class RESTApi{
    //public HTTP_Method $METHOD;
    private $curl;

    public $Log;

    function __construct(){
        $curl = curl_init();
    }

    /**
     * @summary - Makes HTTP Request on API endpoint (url) using given method, with given data
     * @param method {HTTP_Method} - HTTP method used for given request
     * @param url - API endpoint url
     * @param paramData - assoc. array with request data
     */
    function apiCall($method, $url, $paramData){
        $this->curl = curl_init();
        $result = null;//{"code" => null, "responseData" => null};

        curl_setopt($this->curl, CURLOPT_URL, $url);

        echo "<br> [Debug: ]".$url."<br/>";

        switch($method){
            case HTTP_Method::GET:
                {
                    $result = $this->get($paramData);
                break;
                }
            case HTTP_Method::POST:
                {
                    $result = $this->set($paramData);
                break;
                }
            case HTTP_Method::PUT:
                {
                    $result = $this->update($paramData);
                break;
                }
            case HTTP_Method::DELETE:
                {
                    $result = $this->delete($paramData);
                break;
                }
        }

        curl_close($this->curl);

        return $result;
    }

    /**
     * Read (GET)
     * @param params - Request body data
     */
    private function get($params){
        //curl_setopt($this->curl, CURLOPT_GET, 1);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'GET'); // Default req
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        $response =  curl_exec($this->curl);

        return json_decode($response);
    }

    /**
     * Create (POST)
     * @param params - Request body data
     */
    private function set($params){
        //curl_setopt($this->curl, CURLOPT_POST, 1);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'POST');

    }

    /**
     * Update (PUT)
     * @param params - Request body data
     */
    private function update($params){
       $jsonEncoded = json_encode($params);
       curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'PUT');
       curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($this->curl, CURLOPT_POSTFIELDS, $jsonEncoded);
       $response =  curl_exec($this->curl);

       return json_decode($response);
    }

    /**
     * Delete (DELETE)
     * @param params - Request body data
     */
    private function delete($params){
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        $response =  curl_exec($this->curl);

        return json_decode($response);
    }
}
?>