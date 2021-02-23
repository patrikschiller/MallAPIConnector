<?php
include 'MpApi.php';

/**
 * @var OrderStatus - Order status enumeration
 */
abstract class OrderStatus{
    const OPEN = 0;
    const CLOSED = 1;
    const UNCONFIRMED = 2;
}

/**
 * @route /orders
 */
class MallOrder{
    private $urlBase = "/orders";
    private $MPAPI;

    public function __construct($test = true){
        $this->MPAPI = new MPApi($test);
    }

    /**
     * @summary: Returns all orders with basic data 
     * @param test - optional param, retrieve also test orders? (0 true/1 false)
     * @param filter - optional param, type of data filtering
     * @return {JSON array} - structured response data
     *
     * @method GET
     * @route: https://mpapi.mallgroup.com/v1/orders?client_id=yourClientId&filter=&test=0
     * @routeParams:
     *   - client_id = {apiKey}
     *   - filter = {none|basic|}
     *   - test = include test orders
     */
    public function getAll($test = 0, $filter = "basic"){
        $endpoint = $this->urlBase;
        $params = array('url_params' => "filter=".$filter."&test=".$test);
    }

    /**
     * @summary: Returns list of unconfirmed orders with status (blocked/open)
     * @param orderStatus - OrderStatus enumerator
     * @param filter - optional param, type of data filtering
     * @return {Array} - structured response data (if no error ocurred)
     * 
     * @method GET
     * @route: https://mpapi.mallgroup.com/v1/orders/unconfirmed?client_id=yourClientId&filter=
     * @routeParams:
     *   - client_id = {apiKey}
     *   - filter = {none|basic|}
     */
    public function getAllSpecific($orderStatus, $filter = ""){
        $endpoint = $this->urlBase;
        $params = array('url_params' => "filter=".$filter);

        switch($orderStatus){
            case OrderStatus::OPEN: //0
                $endpoint .= "/open";
                break;
            case OrderStatus::UNCONFIRMED: //2
                $endpoint .= "/unconfirmed";
                break;
        }

        return $this->MPAPI->callMallAPI("GET", $endpoint, $params);
    }

    /**
     * @summary: Returns details of given order
     * @param orderId - id of order for which the details should be retrieved
     * @return {JSON array} - structured response data
     * 
     * @method GET
     * @route: https://mpapi.mallgroup.com/v1/orders/orderId?client_id=yourClientId
     */
    public function getOrderDetail($orderId){
        $endpoint = $this->urlBase."/".$orderId;
        $params = array('url_params' => "");

        return $this->MPAPI->callMallAPI("GET", $endpoint, $params);
    }


    /**
     * summary: Set order status or confirm order
     *      - request data must satisfy JSON schema (defined in api documentation - Apiary link in MPApi class)
     * 
     * @method PUT
     * @route: https://mpapi.mallgroup.com/v1/orders/orderId?client_id=yourClientId 
     */
    public function updateOrderDetail($orderId, $confirmed = false, $status = "shipping"){
        $endpoint = $this->urlBase."/".$orderId;
        $params = array('url_params' => "");

        return $this->MPAPI->callMallAPI("SET", $endpoint, $params);
    }
}


?>