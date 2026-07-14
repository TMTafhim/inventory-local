<?php
require_once __DIR__ . '/BDB/DBConnEction.php';

header('Content-Type: application/json; charset=utf-8');
if (empty($_SESSION['LoginReGiSterSession'])) {
    http_response_code(401);
    echo json_encode(array('error' => 'Your session has expired.'));
    exit;
}

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

if ($action === 'stock_products') {
    $storeId = !empty($_GET['store_id']) ? (int)$_GET['store_id'] : 0;
    $search = trim(isset($_GET['term']) ? $_GET['term'] : '');
    if (!$storeId) {
        echo json_encode(array());
        exit;
    }
    $statement = $pdo->prepare(
        "SELECT product_information.id,product_information.name,product_information.code,
                product_information.unit,CAST(stock_information.stock AS DECIMAL(18,4)) AS available_quantity
         FROM stock_information
         INNER JOIN product_information ON product_information.id=stock_information.product_id
         WHERE stock_information.store_id=:store_id
           AND stock_information.deleted_at IS NULL
           AND product_information.deleted_at IS NULL
           AND CAST(stock_information.stock AS DECIMAL(18,4)) > 0
           AND (product_information.name LIKE :name_search OR product_information.code LIKE :code_search)
         ORDER BY product_information.name ASC LIMIT 20"
    );
    $searchPattern = '%' . $search . '%';
    $statement->execute(array(':store_id' => $storeId, ':name_search' => $searchPattern, ':code_search' => $searchPattern));
    $items = array();
    foreach ($statement->fetchAll() as $row) {
        $items[] = array(
            'label' => $row['name'] . (!empty($row['code']) ? ' (' . $row['code'] . ')' : '') . ' - Stock: ' . $row['available_quantity'],
            'value' => $row['name'],
            'product_id' => $row['id'],
            'code' => $row['code'],
            'unit' => $row['unit'],
            'available_quantity' => $row['available_quantity']
        );
    }
    echo json_encode($items);
    exit;
}

if ($action === 'outstanding') {
    $projectId = !empty($_GET['project_id']) ? (int)$_GET['project_id'] : 0;
    $storeId = !empty($_GET['store_id']) ? (int)$_GET['store_id'] : 0;
    if (!$projectId || !$storeId) {
        echo json_encode(array('items' => array()));
        exit;
    }
    $historyStatement = $pdo->prepare(
        "SELECT emergency_request.id,emergency_request.request_no,emergency_request.status,emergency_request.date,
                SUM(emergency_request_detail.issued_quantity) AS issued_quantity,
                SUM(emergency_request_detail.issued_quantity-emergency_request_detail.reconciled_quantity) AS outstanding_quantity
         FROM emergency_request
         INNER JOIN emergency_request_detail ON emergency_request_detail.emergency_request_id=emergency_request.id
         WHERE emergency_request.project_id=:project_id
           AND emergency_request.store_id=:store_id
           AND emergency_request.deleted_at IS NULL
         GROUP BY emergency_request.id
         ORDER BY emergency_request.id DESC"
    );
    $historyStatement->execute(array(':project_id' => $projectId, ':store_id' => $storeId));
    $requests = $historyStatement->fetchAll();

    $reservedStatement = $pdo->prepare(
        "SELECT DISTINCT requestion_draft_histiory.invoice_id,emergency_request_detail.emergency_request_id
         FROM requestion_draft_detail
         INNER JOIN requestion_draft_histiory ON requestion_draft_histiory.invoice_id=requestion_draft_detail.invoice_id
         INNER JOIN emergency_request_detail ON FIND_IN_SET(CAST(emergency_request_detail.id AS CHAR) COLLATE utf8mb4_unicode_ci,requestion_draft_detail.emergency_detail_ids COLLATE utf8mb4_unicode_ci)
         INNER JOIN emergency_request ON emergency_request.id=emergency_request_detail.emergency_request_id
         WHERE requestion_draft_histiory.project_id=:project_id
           AND requestion_draft_histiory.store_id=:store_id
           AND requestion_draft_histiory.final_submit_status IS NULL
           AND requestion_draft_histiory.deleted_at IS NULL
           AND requestion_draft_detail.deleted_at IS NULL
         ORDER BY requestion_draft_histiory.id DESC"
    );
    $reservedStatement->execute(array(':project_id' => $projectId, ':store_id' => $storeId));
    $reservedDrafts = $reservedStatement->fetchAll();

    $statement = $pdo->prepare(
        "SELECT emergency_request_detail.product_id,
                product_information.name,product_information.code,product_information.unit,
                GROUP_CONCAT(emergency_request_detail.id ORDER BY emergency_request_detail.id) AS emergency_detail_ids,
                SUM(emergency_request_detail.issued_quantity-emergency_request_detail.reconciled_quantity) AS emergency_quantity,
                CAST(stock_information.stock AS DECIMAL(18,4)) AS available_quantity
         FROM emergency_request_detail
         INNER JOIN emergency_request ON emergency_request.id=emergency_request_detail.emergency_request_id
         INNER JOIN product_information ON product_information.id=emergency_request_detail.product_id
         LEFT JOIN stock_information ON stock_information.store_id=emergency_request.store_id AND stock_information.product_id=emergency_request_detail.product_id AND stock_information.deleted_at IS NULL
         WHERE emergency_request.project_id=:project_id
           AND emergency_request.store_id=:store_id
           AND emergency_request.status='completed'
           AND emergency_request.deleted_at IS NULL
           AND emergency_request_detail.issued_quantity > emergency_request_detail.reconciled_quantity
           AND NOT EXISTS (
               SELECT 1 FROM requestion_draft_detail reserved_detail
               INNER JOIN requestion_draft_histiory reserved_header ON reserved_header.invoice_id=reserved_detail.invoice_id
               WHERE reserved_header.final_submit_status IS NULL
                 AND reserved_header.deleted_at IS NULL
                 AND reserved_detail.deleted_at IS NULL
                 AND FIND_IN_SET(CAST(emergency_request_detail.id AS CHAR) COLLATE utf8mb4_unicode_ci,reserved_detail.emergency_detail_ids COLLATE utf8mb4_unicode_ci)
           )
         GROUP BY emergency_request_detail.product_id,product_information.name,product_information.code,product_information.unit,stock_information.stock
         ORDER BY product_information.name"
    );
    $statement->execute(array(':project_id' => $projectId, ':store_id' => $storeId));
    echo json_encode(array('requests' => $requests, 'reserved_drafts' => $reservedDrafts, 'items' => $statement->fetchAll()));
    exit;
}

http_response_code(400);
echo json_encode(array('error' => 'Invalid request.'));
