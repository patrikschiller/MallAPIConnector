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
    if(isset($_POST['getAll'])){
        $response = $Orders->getAllSpecific(OrderStatus::OPEN);

        var_dump($response);
        echo "<br/>";

        foreach($response['data'] as $val){
            echo "Id: ".$val."<br/>";
        }
    }

    if(isset($_POST['getById'])){
        $productId = $_POST['productId'];
        $response = $Orders->getOrderDetail($productId);
    }
?>

<form action="" method="post">
    <input type="submit" value="Get All" name='getAll' id='getAll'/><br/>
    <input type="text" name="productId" id="productId" />
    <input type="submit" value="Get By Id" name='getById' id='getById'/><br/>
    <input type="submit" value="Submit" id='submit_API_test'>
</form>

</html>