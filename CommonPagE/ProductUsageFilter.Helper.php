<?php
if (!function_exists('productUsageFilterIds')) {
    function productUsageFilterIds($pdo, $module, $reference, $secondary = '') {
        static $cache = array();
        $cacheKey = $module . '|' . $reference . '|' . $secondary;
        if (isset($cache[$cacheKey])) {
            return $cache[$cacheKey];
        }

        $sql = '';
        $params = array(':reference' => $reference);
        if ($module === 'requisition') {
            $sql = "SELECT DISTINCT product_id FROM requestion_detail WHERE invoice_id=:reference AND deleted_at IS NULL AND product_id<>''";
        } elseif ($module === 'requisition_draft') {
            $sql = "SELECT DISTINCT product_id FROM requestion_draft_detail WHERE invoice_id=:reference AND deleted_at IS NULL AND product_id<>''";
        } elseif ($module === 'distribution') {
            $sql = "SELECT DISTINCT product_id FROM distribution_history WHERE invoice_id=:reference AND deleted_at IS NULL AND product_id<>''";
        } elseif ($module === 'distribution_id') {
            $sql = "SELECT DISTINCT product_id FROM distribution_history WHERE distribution_id=:reference AND deleted_at IS NULL AND product_id<>''";
        } elseif ($module === 'purchase') {
            $sql = "SELECT DISTINCT product_id FROM purchase_detail WHERE invoice_id=:reference AND deleted_at IS NULL AND product_id<>''";
        } elseif ($module === 'return') {
            $sql = "SELECT DISTINCT product_id FROM return_history_detail WHERE invoice_id=:reference AND deleted_at IS NULL AND product_id<>''";
        } elseif ($module === 'material_used') {
            $sql = "SELECT DISTINCT product_id FROM material_used_detail_history WHERE invoice_id=:reference AND deleted_at IS NULL AND product_id<>''";
        } elseif ($module === 'stock_transfer') {
            $sql = "SELECT DISTINCT product_id FROM stock_transfer_information WHERE transfer_id=:reference AND deleted_at IS NULL AND product_id<>''";
        } elseif ($module === 'emergency') {
            $sql = "SELECT DISTINCT product_id FROM emergency_request_detail WHERE emergency_request_id=:reference AND product_id<>''";
        } else {
            $cache[$cacheKey] = '';
            return '';
        }

        $statement = $pdo->prepare($sql);
        $statement->execute($params);
        $ids = array();
        while ($row = $statement->fetch()) {
            if ($row['product_id'] !== null && $row['product_id'] !== '') {
                $ids[] = (string)$row['product_id'];
            }
        }
        $cache[$cacheKey] = implode(',', array_values(array_unique($ids)));
        return $cache[$cacheKey];
    }
}
?>
