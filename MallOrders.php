<?php
include 'MpApi.php';

/**
 * @summary - Safe status typing using strict Enumeration
 *          - for 'Enumerator_Value (int) --> (string)' conversion can be used Translate array
 * @var OrderStatus - Order status enumeration
 */
abstract class OrderStatus{
    const BLOCKED = 0;
    const OPEN = 1;
    const SHIPPING = 2;
    const SHIPPED = 3;
    const DELIVERED = 4;
    const RETURNED = 5;
    const CANCELLED = 6;
    const UNCONFIRMED = 10;

    const Translate = array(
        0 => 'blocked',
        1 => 'open',
        2 => 'shipping',
        3 => 'shipped',
        4 => 'delivered',
        5 => 'returned',
        6 => 'canceled'
    );
}

/**
 * @route /orders
 */
class MallOrder{
    private $endpointBase = "/orders";
    private $moduleName = "ORDERS";
    private $MPAPI;

    public function __construct($test = true){
        $this->MPAPI = new MPApi($test);
    }

    /**
     * @summary: Returns all orders with basic data 
     * @param filter - optional param, type of data filtering
     * @param testOrders - optional param, retrieve also test orders? (0 true/1 false)
     * @return {JSON array} - structured response data
     *
     * @method GET
     * @route: https://mpapi.mallgroup.com/v1/orders?client_id=yourClientId&filter=&test=0
     * @routeParams:
     *   - client_id = {apiKey}
     *   - filter = {none|basic|}
     *   - test = include test orders
     */
    public function getAll($filter = "basic", $testOrders = 0){
        $endpoint = $this->endpointBase;
        $params = array('url_params' => "filter=".$filter."&test=".$testOrders);

        return $this->MPAPI->callMallAPI(HTTP_Method::GET, $endpoint, $params);
    }

    /**
     * @summary: Returns list of unconfirmed orders with status (blocked/open)
     * @param orderStatus {OrderStatus} - OrderStatus enumerator
     * @param filter - optional param, type of data filtering
     * @return {Array} - structured response data (if no error ocurred)
     * 
     * @method GET
     * @route: https://mpapi.mallgroup.com/v1/orders/unconfirmed?client_id=yourClientId&filter=
     * @routeParams:
     *   - client_id = {apiKey}
     *   - filter = {none|basic|}
     */
    public function getAllByStatus($orderStatus, $filter = ""){
        $endpoint = $this->endpointBase;
        $params = array('url_params' => "filter=".$filter);

        switch($orderStatus){
            case OrderStatus::OPEN: 
                $endpoint .= "/open";
                break;
            case OrderStatus::UNCONFIRMED: 
                $endpoint .= "/unconfirmed";
                break;
            case OrderStatus::SHIPPED: 
                $endpoint .= "/shipped";
                break;
            default:
                // General retrieve of status using OrderStatus::Translate array
                $status = OrderStatus::Translate[$orderStatus];
                if($status){
                    $endpoint .= "/". $status;
                }else{
                    // LOG: Status with id $orderStatus doesn't exist
                    $message = "Status ".$orderStatus." was not recognized!";
                    $this->MPAPI->Log->push(MessageType::ERROR, $this->moduleName, $message);
                }
        }

        return $this->MPAPI->callMallAPI(HTTP_Method::GET, $endpoint, $params);
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
        $endpoint = $this->endpointBase."/".$orderId;
        $params = array('url_params' => "");

        return $this->MPAPI->callMallAPI(HTTP_Method::GET, $endpoint, $params);
    }

    /**
     * summary: Set order status or confirm order
     *      - General method to update order
     *          => better to use methods like confirmOrder, changeOrderStatus etc. instead
     *      - request data must satisfy JSON schema (defined in api documentation - Apiary link in MPApi class)
     * 
     * @method PUT
     * @route: https://mpapi.mallgroup.com/v1/orders/orderId?client_id=yourClientId 
     */
    private function updateOrderDetail($order){
        $endpoint = $this->endpointBase."/".$order['id'];
        $params = array('url_params' => "");

        return $this->MPAPI->callMallAPI(HTTP_Method::PUT, $endpoint, $order);
    }

    /**
     * @summary - Confirms order by Partner (E-shop)
     * @param order - Order object
     * 
     * blocked orders are not processed by the partner!
        -  Orders in the status blocked waiting for online payments or loan approval are cancelled after 10 days if payment is missing or loan is not approved.
        -  If order status is confirmed by you, do not confirm it again!
     */
    public function confirmOrder($order){
        if(!$order['confirmed']){
            $order['confirmed'] = true;
            $this->updateOrderDetail($order);
        }else{
            $message = "Order ".$order['id']." was already confirmed. Aborting request!";
            $this->MPAPI->Log->push(MessageType::WARNING, $this->moduleName, $message);
        }
    }

    /**
     * @todo TODO
     * @summary - Updates status of given order (if it is allowed)
     * @param order - Order object
     * @param newStatus {OrderStatus} - integer rep. of given status (from DB etc.), must satisfy mapping given by OrderStatus enumeration
     * 
     * Statuses managed by partner:
        -  from open to cancelled, shipping or shipped
        -  from shipping to cancelled or shipped
        -  from shipped to delivered or returned
     */
    public function changeOrderStatus($order, $newStatus){
        $actionAllowed = false;

        switch($order['status']){
            case OrderStatus::OPEN:
                switch($newStatus){
                    case OrderStatus::CANCELLED: 
                    case OrderStatus::SHIPPING: 
                    case OrderStatus::SHIPPED:
                        $order['status'] = OrderStatus::Translate[$newStatus];
                        $actionAllowed = true;
                        break;
                    default:
                        // LOG: No other action is allowed!
                }
                break;
            case OrderStatus::SHIPPING: 
                switch($newStatus){
                    case OrderStatus::CANCELLED: 
                    case OrderStatus::SHIPPED:
                        $order['status'] = OrderStatus::Translate[$newStatus];
                        $actionAllowed = true;
                        break;
                    default:
                        // LOG: No other action is allowed!
                }
                break;
            case OrderStatus::SHIPPED: 
                switch($newStatus){
                    case OrderStatus::DELIVERED: 
                    case OrderStatus::RETURNED:
                        $order['status'] = OrderStatus::Translate[$newStatus];
                        $actionAllowed = true;
                        break;
                    default:
                        // LOG: No other action is allowed!
                }
                break;
            default:
                $message = "Status ".$order['status']." was not recognized!";
                $this->MPAPI->Log->push(MessageType::ERROR, $this->moduleName, $message);
                return false;
        }

        /* Update order */
        if($actionAllowed){
            $this->updateOrderDetail($order);
            return true;
        }

        $message = "Status '".OrderStatus::Translate[$order['status']]."' can't be changed to '".OrderStatus::Translate[$newStatus]."'";
        $this->MPAPI->Log->push(MessageType::ERROR, $this->moduleName, $message);

        return false;
    }
}
?>