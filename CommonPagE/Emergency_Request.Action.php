<?php
if (!function_exists('emergencyRequestRedirect')) {
    function emergencyRequestRedirect($url, $message, $success = false)
    {
        $_SESSION[$success ? 'success_message' : 'warning_message'] = $message;
        echo "<script>window.open('" . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . "','_self')</script>";
        exit;
    }
}

if (isset($_POST['Insert_Emergency_Request'])) {
    $projectId = !empty($_POST['project_id']) ? (int)$_POST['project_id'] : 0;
    $storeId = !empty($_POST['store_id']) ? (int)$_POST['store_id'] : 0;
    $receiverId = !empty($_POST['receiver_id']) ? (int)$_POST['receiver_id'] : 0;
    $referenceId = !empty($_POST['reference_id']) ? (int)$_POST['reference_id'] : 0;
    $date = isset($_POST['date']) ? date('Y-m-d', strtotime($_POST['date'])) : date('Y-m-d');
    $reason = trim(isset($_POST['reason']) ? $_POST['reason'] : '');
    $rowCount = !empty($_POST['number_count']) ? (int)$_POST['number_count'] : 0;

    if (!$projectId || !$storeId || !$receiverId || !$referenceId || !$rowCount || $reason === '') {
        emergencyRequestRedirect('?Emergency_Request_Create', 'Please complete all required emergency request fields.');
    }
    if ($receiverId === $referenceId) {
        emergencyRequestRedirect('?Emergency_Request_Create', 'Receiver and reference person must be different employees.');
    }

    $items = array();
    for ($i = 1; $i <= $rowCount; $i++) {
        $productId = isset($_POST['product_id' . $i]) ? (int)$_POST['product_id' . $i] : 0;
        $quantity = isset($_POST['quantity' . $i]) ? (float)$_POST['quantity' . $i] : 0;
        $itemNote = trim(isset($_POST['item_note' . $i]) ? $_POST['item_note' . $i] : '');
        if (!$productId && $quantity == 0) {
            continue;
        }
        if (!$productId || $quantity <= 0) {
            emergencyRequestRedirect('?Emergency_Request_Create', 'Every emergency item requires a valid product and quantity greater than zero.');
        }
        if (isset($items[$productId])) {
            emergencyRequestRedirect('?Emergency_Request_Create', 'The same product cannot be added twice. Combine it into one row.');
        }
        $items[$productId] = array('quantity' => $quantity, 'note' => $itemNote);
    }
    if (!$items) {
        emergencyRequestRedirect('?Emergency_Request_Create', 'Add at least one emergency product.');
    }

    try {
        $pdo->beginTransaction();
        $requestNo = 'TMP-' . bin2hex(random_bytes(12));

        $insertHeader = $pdo->prepare(
            "INSERT INTO emergency_request
             (request_no,date,project_id,store_id,receiver_id,reference_id,reason,status,created_by,created_at)
             VALUES (:request_no,:date,:project_id,:store_id,:receiver_id,:reference_id,:reason,'receiver_pending',:created_by,NOW())"
        );
        $insertHeader->execute(array(
            ':request_no' => $requestNo,
            ':date' => $date,
            ':project_id' => $projectId,
            ':store_id' => $storeId,
            ':receiver_id' => $receiverId,
            ':reference_id' => $referenceId,
            ':reason' => $reason,
            ':created_by' => $LoginReGiSterSession
        ));
        $requestId = (int)$pdo->lastInsertId();
        $requestNo = 'ER-' . date('Y') . '-' . str_pad($requestId, 6, '0', STR_PAD_LEFT);
        $pdo->prepare("UPDATE emergency_request SET request_no=:request_no WHERE id=:id")->execute(array(':request_no' => $requestNo, ':id' => $requestId));

        $lockStock = $pdo->prepare("SELECT id,CAST(stock AS DECIMAL(18,4)) AS stock,CAST(distribution AS DECIMAL(18,4)) AS distribution FROM stock_information WHERE store_id=:store_id AND product_id=:product_id AND deleted_at IS NULL FOR UPDATE");
        $insertDetail = $pdo->prepare("INSERT INTO emergency_request_detail (emergency_request_id,product_id,issued_quantity,note,created_by,created_at) VALUES (:request_id,:product_id,:quantity,:note,:created_by,NOW())");
        $updateStock = $pdo->prepare("UPDATE stock_information SET distribution=:distribution,stock=:stock,updated_by=:updated_by,updated_at=NOW() WHERE id=:id");
        $insertMovement = $pdo->prepare("INSERT INTO emergency_stock_movement (emergency_request_id,emergency_request_detail_id,store_id,product_id,quantity,stock_before,stock_after,created_by,created_at) VALUES (:request_id,:detail_id,:store_id,:product_id,:quantity,:stock_before,:stock_after,:created_by,NOW())");

        foreach ($items as $productId => $item) {
            $lockStock->execute(array(':store_id' => $storeId, ':product_id' => $productId));
            $stock = $lockStock->fetch();
            if (!$stock || (float)$stock['stock'] < $item['quantity']) {
                throw new RuntimeException('Insufficient stock for one or more selected emergency products.');
            }
            $stockBefore = (float)$stock['stock'];
            $stockAfter = $stockBefore - $item['quantity'];
            $distributionAfter = (float)$stock['distribution'] + $item['quantity'];

            $insertDetail->execute(array(':request_id' => $requestId, ':product_id' => $productId, ':quantity' => $item['quantity'], ':note' => $item['note'], ':created_by' => $LoginReGiSterSession));
            $detailId = (int)$pdo->lastInsertId();
            $updateStock->execute(array(':distribution' => $distributionAfter, ':stock' => $stockAfter, ':updated_by' => $LoginReGiSterSession, ':id' => $stock['id']));
            $insertMovement->execute(array(':request_id' => $requestId, ':detail_id' => $detailId, ':store_id' => $storeId, ':product_id' => $productId, ':quantity' => $item['quantity'], ':stock_before' => $stockBefore, ':stock_after' => $stockAfter, ':created_by' => $LoginReGiSterSession));
        }

        $notification = $pdo->prepare("INSERT INTO emergency_request_notification (emergency_request_id,recipient_id,stage,created_at) VALUES (:request_id,:recipient_id,'receiver_acknowledgement',NOW())");
        $notification->execute(array(':request_id' => $requestId, ':recipient_id' => $receiverId));
        $pdo->commit();
        emergencyRequestRedirect('?Emergency_Request_Detail/' . $requestId, 'Emergency request created and stock deducted successfully.', true);
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        emergencyRequestRedirect('?Emergency_Request_Create', $error instanceof RuntimeException ? $error->getMessage() : 'Emergency request could not be saved. No stock was changed.');
    }
}

if (isset($_POST['Emergency_Request_Acknowledge'])) {
    $requestId = !empty($_POST['emergency_request_id']) ? (int)$_POST['emergency_request_id'] : 0;
    if (!$requestId) {
        emergencyRequestRedirect('?Emergency_Request', 'Invalid emergency request.');
    }

    try {
        $pdo->beginTransaction();
        $requestStatement = $pdo->prepare("SELECT * FROM emergency_request WHERE id=:id AND deleted_at IS NULL FOR UPDATE");
        $requestStatement->execute(array(':id' => $requestId));
        $request = $requestStatement->fetch();
        if (!$request) {
            throw new RuntimeException('Emergency request was not found.');
        }

        if ($request['status'] === 'receiver_pending' && (int)$request['receiver_id'] === (int)$LoginReGiSterSession) {
            $update = $pdo->prepare("UPDATE emergency_request SET status='reference_pending',receiver_signed_by=:signer_id,receiver_signed_at=NOW(),updated_by=:updated_by,updated_at=NOW() WHERE id=:id");
            $update->execute(array(':signer_id' => $LoginReGiSterSession, ':updated_by' => $LoginReGiSterSession, ':id' => $requestId));
            $pdo->prepare("UPDATE emergency_request_notification SET actioned_at=NOW(),read_at=COALESCE(read_at,NOW()) WHERE emergency_request_id=:id AND recipient_id=:user_id AND stage='receiver_acknowledgement'")->execute(array(':id' => $requestId, ':user_id' => $LoginReGiSterSession));
            $pdo->prepare("INSERT INTO emergency_request_notification (emergency_request_id,recipient_id,stage,created_at) VALUES (:id,:recipient_id,'reference_acknowledgement',NOW())")->execute(array(':id' => $requestId, ':recipient_id' => $request['reference_id']));
            $message = 'Receiver acknowledgement recorded. The reference person has been notified.';
        } elseif ($request['status'] === 'reference_pending' && (int)$request['reference_id'] === (int)$LoginReGiSterSession) {
            $update = $pdo->prepare("UPDATE emergency_request SET status='completed',reference_signed_by=:signer_id,reference_signed_at=NOW(),updated_by=:updated_by,updated_at=NOW() WHERE id=:id");
            $update->execute(array(':signer_id' => $LoginReGiSterSession, ':updated_by' => $LoginReGiSterSession, ':id' => $requestId));
            $pdo->prepare("UPDATE emergency_request_notification SET actioned_at=NOW(),read_at=COALESCE(read_at,NOW()) WHERE emergency_request_id=:id AND recipient_id=:user_id AND stage='reference_acknowledgement'")->execute(array(':id' => $requestId, ':user_id' => $LoginReGiSterSession));
            $message = 'Reference acknowledgement recorded. The emergency request is now ready for reconciliation.';
        } else {
            throw new RuntimeException('This request is not waiting for your acknowledgement.');
        }
        $pdo->commit();
        emergencyRequestRedirect('?Emergency_Request_Detail/' . $requestId, $message, true);
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        emergencyRequestRedirect('?Emergency_Request_Detail/' . $requestId, $error->getMessage());
    }
}
