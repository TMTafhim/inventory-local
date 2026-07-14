<?php
include("BDB/DBConnEction.php");

$request = 0;
if (isset($_POST['request'])) {
    $request = $_POST['request'];
}

$response = array();

// Get product name list
if ($request == 1) {
    $search = "";
    if (isset($_POST['search'])) {
        $search = strtoupper($_POST['search']);
    }

	$resultoutput = $pdo->prepare("SELECT name FROM product_information WHERE name LIKE :search AND deleted_at IS NULL ORDER BY name ASC LIMIT 20");
	$resultoutput->execute(array(':search' => '%' . $search . '%'));

    while ($result = $resultoutput->fetch()) {
        $response[] = array(
            "value" => $result['name'],
            "label" => $result['name']
        );
    }

    echo json_encode($response);
    exit;
}

// Get selected product details
if ($request == 3) {
    $userid = "";
    if (isset($_POST['userid'])) {
        $userid = $_POST['userid'];
    }

	$store_id = isset($_POST["store_id"]) ? (int) $_POST["store_id"] : 0;

	$resultoutput = $pdo->prepare("
        SELECT 
            COALESCE(SUM(stock_information.stock), 0) AS stock,
            product_information.id AS product_id,
            product_information.code AS product_code,
            product_information.unit AS product_unit,
            product_information.name AS product_name
        FROM product_information
        LEFT JOIN stock_information 
            ON stock_information.product_id = product_information.id
		  AND stock_information.store_id = :store_id
          AND stock_information.deleted_at IS NULL
        WHERE product_information.deleted_at IS NULL
		  AND product_information.name = :product_name
		GROUP BY product_information.id, product_information.code, product_information.unit, product_information.name
    ");
	$resultoutput->execute(array(':store_id' => $store_id, ':product_name' => $userid));

    $users_arr = array();

    while ($row = $resultoutput->fetch()) {
        $stock_product_id = $row['product_id'];
        $stock = $row['stock'];
        $product_code = $row['product_code'];
        $product_unit = $row['product_unit'];
        $product_name = $row['product_name'];

        $users_arr[] = array(
            "available_quantity" => $stock,
            "product_id" => $stock_product_id,
            "product_code" => $product_code,
            "product_unit" => $product_unit,
            "product_name" => $product_name
        );
    }

    echo json_encode($users_arr);
    exit;
}
?>
