<?php

function get_products_recent() {
    require(ROOT_PATH . "inc/database.php");
    
    try {

        $results = $db->query("SELECT name, price, img, sku, paypal
                              FROM products
                              ORDER BY sku DESC
                              LIMIT 4");
    } catch(Exception $e){
        echo "Data loading error!";
        exit;

    }

    $recent = $results->fetchAll(PDO::FETCH_ASSOC);
    $recent = array_reverse($recent);

    return $recent;
}

?>