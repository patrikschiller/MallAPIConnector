<?php
//include './../../../../core/libs/REST/MallOrders.php' or die('f');
//if((include '../../../../../core/libs/REST/MallOrders.php') == TRUE){
/*if((include './MallOrders.php') == TRUE){
    echo 'OK';
}else{
    echo "F";
}*/
include 'MallOrders.php';

$Orders = new MallOrder();
?>
<html>
<h2>Test MPAPI</h2>

<?php
    function parseData($data){
        foreach($data as $key => $val){
            if(is_array($val) || is_object($val)){
                echo "<font style='font-weight: bold'>".$key. ": </font><br/>";
                parseData($val);
            }else{
                echo "&nbsp;&nbsp; - ".$key . ": " . $val . "<br/>";
            }
        }
    }

    $statusMapping = [
        'open' => OrderStatus::OPEN,
        'cancelled' => OrderStatus::CANCELLED,
        'shipping' => OrderStatus::SHIPPING,
        'shipped' => OrderStatus::SHIPPED,
        'unconfirmed' => OrderStatus::UNCONFIRMED,
        'blocked' => OrderStatus::BLOCKED,
        'returned' => OrderStatus::RETURNED
    ];

    if(isset($_POST['getAll'])){
        $status = $_POST['status'];

        echo "Status: [".$status."] - ( ".$statusMapping[$status].")<br/>";

        $response = $Orders->getAllByStatus($statusMapping[$status]);

        var_dump($response);
        echo "<br/>";

        /*foreach($response['data'] as $val){
            echo "Id: ".$val."<br/>";
        }*/
    }

    if(isset($_POST['getById'])){
        $productId = $_POST['productId'];
        $response = $Orders->getOrderDetail($productId);

        echo "<br/>";

        var_dump($response);

        echo "<br/><br/><b><u>Response data</u>: </b><br/>";

        foreach($response['data'][0] as $key => $val){
            if(is_object($val)){
                echo "<font style='font-weight: bold'>".$key. ": </font><br/>";
                foreach($val as $k => $el){
                    echo "&nbsp;&nbsp; - ".$k . ": " . $el . "<br/>";
                }
            }elseif(is_array($val)){
                echo "<font style='font-weight: bold'>".$key. ": </font><br/>";
                foreach($val as $e){
                   if(is_array($e) || is_object($e)){
                       parseData($e);
                   }
                }
            }else{
                echo $key . ": " . $val . "<br/>";
            }
        }
    }

    echo "<hr/>"
?>

<form action="" method="post">
    <label for="status">Orders status:</label>
    <select name="status" id="status">
        <option value='open'>Open</option>
        <option value='cancelled'>Cancelled</option>
        <option value='shipping'>Shipping</option>
        <option value='shipped'>Shipped</option>
        <option value='unconfirmed'>Unconfirmed</option>
        <option value='blocked'>Blocked</option>
        <option value='returned'>Returned</option>
    </select>
    <input type="submit" value="Get All" name='getAll' id='getAll'/><br/>
    <input type="text" name="productId" id="productId" />
    <input type="submit" value="Get By Id" name='getById' id='getById'/><br/>
    <input type="submit" value="Submit" id='submit_API_test'>
</form>

</html>