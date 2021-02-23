  <?php
class RESTApi{
    private $curl;

    function __construct(){
        //$curl = curl_init();
    }

    function apiCall($method, $url, $paramData){
        $this->curl = curl_init();
        $result = null;//{"code" => null, "responseData" => null};

        curl_setopt($this->curl, CURLOPT_URL, $url);

        echo "<br>".$url;

        switch($method){
            case "GET":
                {
                    $result = $this->get($paramData);
                break;
                }
            case "SET": // = PUT
                {
                    $result = $this->set($paramData);
                break;
                }
            case "UPDATE":
                {
                    $result = $this->update($paramData);
                break;
                }
            case "DELETE":
                {
                    $result = $this->delete($paramData);
                break;
                }
        }

        curl_close($this->curl);

        return $result;
    }

    function get($params){
        //curl_setopt($this->curl, CURLOPT_GET, 1);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        $response =  curl_exec($this->curl);
        return json_decode($response);
        // method GET
    }

    function set($params){
        curl_setopt($this->curl, CURLOPT_PUT, 1);
        // PUT
    }

    function update($params){
        curl_setopt($this->curl, CURLOPT_UPDATE, true);
        // UPDATE
    }

    function delete($params){
        curl_setopt($this->curl, CURLOPT_DELETE, 1);
        // DELETE
    }
}
?>