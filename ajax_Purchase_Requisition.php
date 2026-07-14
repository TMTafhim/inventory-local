<?php
require_once __DIR__ . '/BDB/DBConnEction.php';

header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['LoginReGiSterSession'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Your session has expired. Please log in again.']);
    exit;
}

$invoiceId = isset($_GET['invoice_id']) ? trim($_GET['invoice_id']) : '';
if ($invoiceId === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Please select a requisition number.']);
    exit;
}

$headerStatement = $pdo->prepare(
    "SELECT invoice_id, store_id
     FROM requestion_histiory
     WHERE invoice_id = :invoice_id
       AND approval_status = 'Approve'
       AND deleted_at IS NULL
       AND EXISTS (
           SELECT 1 FROM project_material_aproval_status approved_step
           WHERE approved_step.invoice_id = requestion_histiory.invoice_id
             AND TRIM(approved_step.approval_status) = 'Approve'
             AND approved_step.deleted_at IS NULL
       )
       AND NOT EXISTS (
           SELECT 1 FROM project_material_aproval_status incomplete_step
           WHERE incomplete_step.invoice_id = requestion_histiory.invoice_id
             AND COALESCE(TRIM(incomplete_step.approval_status), '') <> 'Approve'
             AND incomplete_step.deleted_at IS NULL
       )
     LIMIT 1"
);
$headerStatement->execute([':invoice_id' => $invoiceId]);
$requisition = $headerStatement->fetch();

if (!$requisition) {
    http_response_code(404);
    echo json_encode(['error' => 'No fully approved requisition was found.']);
    exit;
}

$itemStatement = $pdo->prepare(
    "SELECT product_information.name,
            requestion_detail.requestion_quantity AS req_quantity,
            COALESCE(NULLIF(requestion_detail.final_quantity, ''), requestion_detail.requestion_quantity) AS quantity,
            COALESCE(NULLIF(requestion_detail.final_rate, ''), NULLIF(requestion_detail.requistion_rate, ''), 0) AS rate
     FROM requestion_detail
     INNER JOIN product_information ON requestion_detail.product_id = product_information.id
     WHERE requestion_detail.invoice_id = :invoice_id
       AND requestion_detail.deleted_at IS NULL
       AND CAST(COALESCE(NULLIF(requestion_detail.final_quantity, ''), requestion_detail.requestion_quantity, 0) AS DECIMAL(18,4)) > 0
     ORDER BY requestion_detail.id ASC"
);
$itemStatement->execute([':invoice_id' => $invoiceId]);

echo json_encode([
    'invoice_id' => $requisition['invoice_id'],
    'store_id' => $requisition['store_id'],
    'items' => $itemStatement->fetchAll(),
]);
