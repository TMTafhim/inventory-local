<?php
$lifecycle_columns = [
    'transaction_date' => 'Transaction Date',
    'transaction_time' => 'Transaction Time',
    'transaction_type' => 'Transaction Type',
    'source_module' => 'Source Module',
    'reference_no' => 'Reference / Invoice No',
    'challan_no' => 'Challan / Delivery Note No',
    'product_code' => 'Product Code',
    'product_name' => 'Product Name',
    'category' => 'Category',
    'subcategory' => 'Subcategory',
    'unit' => 'Unit',
    'store' => 'Store',
    'from_store' => 'From Store',
    'to_store' => 'To Store',
    'project' => 'Project',
    'employee' => 'Employee / Requested By',
    'supplier_receiver' => 'Supplier / Receiver',
    'qty_in' => 'Qty In',
    'qty_out' => 'Qty Out',
    'net_qty' => 'Net Qty',
    'before_stock' => 'Before Stock',
    'after_stock' => 'After Stock',
    'running_balance' => 'Running Balance',
    'rate' => 'Rate',
    'amount' => 'Amount',
    'status' => 'Status',
    'received_status' => 'Received Status',
    'received_qty' => 'Received Qty',
    'received_date' => 'Received Date',
    'approved_by' => 'Approved By / Approval Stage',
    'created_by_name' => 'Created By',
    'created_at' => 'Created At',
    'remarks' => 'Remarks / Note',
    'attachment' => 'Attachment',
    'action' => 'Action'
];

$default_columns = [
    'transaction_date',
    'transaction_type',
    'reference_no',
    'challan_no',
    'product_code',
    'product_name',
    'store',
    'project',
    'qty_in',
    'qty_out',
    'net_qty',
    'running_balance',
    'amount',
    'status',
    'action'
];

$selected_columns = isset($_POST['columns']) && is_array($_POST['columns']) ? $_POST['columns'] : $default_columns;
$selected_columns = array_values(array_intersect($selected_columns, array_keys($lifecycle_columns)));
if (empty($selected_columns)) {
    $selected_columns = $default_columns;
}

function lifecycleNumber($value) {
    return is_numeric($value) ? (float)$value : 0;
}

function lifecycleText($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function lifecycleDate($value) {
    if (empty($value) || $value == '0000-00-00') {
        return '';
    }
    $time = strtotime($value);
    return $time ? date('d-m-Y', $time) : lifecycleText($value);
}

function lifecycleAmount($value) {
    $number = lifecycleNumber($value);
    return $number == 0 ? '' : number_format($number, 2);
}

function lifecycleLink($row) {
    $reference = urlencode((string)$row['reference_no']);
    $type = $row['transaction_type'];
    if ($type == 'Purchase') {
        return '?Purchase_HistoryDetail/' . $reference;
    }
    if ($type == 'Distribution') {
        return '?Distribution_History_Detail/' . $reference;
    }
    if ($type == 'Return' || $type == 'Damage') {
        return '?Return_History_View/' . $reference;
    }
    if ($type == 'Transfer Out' || $type == 'Transfer In') {
        return '?Stock_Transfer_Detail/' . $reference;
    }
    return '#';
}

$product_id = $_POST['product_id'] ?? '';
$store_id = $_POST['store_id'] ?? '';
$project_id = $_POST['project_id'] ?? '';
$transaction_type = $_POST['transaction_type'] ?? '';
$from_date = $_POST['from_date'] ?? $current_date;
$to_date = $_POST['to_date'] ?? $current_date;

$where = ["lifecycle.deleted_at IS NULL"];
$params = [];
if (!empty($product_id)) {
    $where[] = "lifecycle.product_id = :product_id";
    $params[':product_id'] = $product_id;
}
if (!empty($store_id)) {
    $where[] = "lifecycle.store_id = :store_id";
    $params[':store_id'] = $store_id;
}
if (!empty($project_id)) {
    $where[] = "lifecycle.project_id = :project_id";
    $params[':project_id'] = $project_id;
}
if (!empty($transaction_type)) {
    $where[] = "lifecycle.transaction_type = :transaction_type";
    $params[':transaction_type'] = $transaction_type;
}
if (!empty($from_date) && !empty($to_date)) {
    $where[] = "lifecycle.transaction_date >= :from_date AND lifecycle.transaction_date <= :to_date";
    $params[':from_date'] = $from_date;
    $params[':to_date'] = $to_date;
} elseif (!empty($from_date)) {
    $where[] = "lifecycle.transaction_date = :from_date";
    $params[':from_date'] = $from_date;
} elseif (!empty($to_date)) {
    $where[] = "lifecycle.transaction_date = :to_date";
    $params[':to_date'] = $to_date;
}

$where_sql = implode(' AND ', $where);

$lifecycle_sql = "
SELECT * FROM (
    SELECT
        pd.date AS transaction_date,
        ph.created_at AS transaction_time,
        'Purchase' AS transaction_type,
        'Purchase' AS source_module,
        pd.invoice_id AS reference_no,
        pd.purchase_id AS challan_no,
        pd.product_id,
        pd.store_id,
        '' AS project_id,
        pi.code AS product_code,
        pi.name AS product_name,
        pc.name AS category,
        ps.name AS subcategory,
        COALESCE(NULLIF(pd.unit, ''), pi.unit) AS unit,
        si.name AS store,
        '' AS from_store,
        '' AS to_store,
        '' AS project,
        '' AS employee,
        sup.organization AS supplier_receiver,
        CAST(COALESCE(NULLIF(pd.after_quantity, ''), 0) AS DECIMAL(18,2)) AS qty_in,
        0 AS qty_out,
        CAST(COALESCE(NULLIF(pd.after_quantity, ''), 0) AS DECIMAL(18,2)) AS net_qty,
        CAST(COALESCE(NULLIF(pd.before_quantity, ''), 0) AS DECIMAL(18,2)) AS before_stock,
        CAST(COALESCE(NULLIF(pd.quantity, ''), 0) AS DECIMAL(18,2)) AS after_stock,
        CAST(COALESCE(NULLIF(pd.rate, ''), 0) AS DECIMAL(18,2)) AS rate,
        CAST(COALESCE(NULLIF(pd.amount, ''), 0) AS DECIMAL(18,2)) AS amount,
        'Approved' AS status,
        'Received' AS received_status,
        CAST(COALESCE(NULLIF(pd.after_quantity, ''), 0) AS DECIMAL(18,2)) AS received_qty,
        pd.date AS received_date,
        '' AS approved_by,
        creator.name_en AS created_by_name,
        ph.created_at,
        ph.note AS remarks,
        ph.photo AS attachment,
        pd.deleted_at
    FROM purchase_detail pd
    LEFT JOIN purchase_history ph ON pd.invoice_id = ph.invoice_id AND ph.deleted_at IS NULL
    LEFT JOIN product_information pi ON pd.product_id = pi.id
    LEFT JOIN product_category pc ON pi.product_category = pc.id
    LEFT JOIN product_subcategory ps ON pi.product_subcategory = ps.id
    LEFT JOIN store_information si ON pd.store_id = si.id
    LEFT JOIN supplier_information sup ON pd.supplier_id = sup.id
    LEFT JOIN employee_information creator ON pd.created_by = creator.id

    UNION ALL

    SELECT
        dh.date AS transaction_date,
        ds.created_at AS transaction_time,
        'Distribution' AS transaction_type,
        'Distribution' AS source_module,
        dh.invoice_id AS reference_no,
        dh.distribution_id AS challan_no,
        dh.product_id,
        dh.store_id,
        dh.project_id,
        pi.code AS product_code,
        pi.name AS product_name,
        pc.name AS category,
        ps.name AS subcategory,
        pi.unit AS unit,
        si.name AS store,
        '' AS from_store,
        '' AS to_store,
        pr.name AS project,
        emp.name_en AS employee,
        recv.name_en AS supplier_receiver,
        0 AS qty_in,
        CAST(COALESCE(NULLIF(dh.distribution_quantity, ''), 0) AS DECIMAL(18,2)) AS qty_out,
        -CAST(COALESCE(NULLIF(dh.distribution_quantity, ''), 0) AS DECIMAL(18,2)) AS net_qty,
        0 AS before_stock,
        0 AS after_stock,
        0 AS rate,
        CAST(COALESCE(NULLIF(dh.distribution_amount, ''), 0) AS DECIMAL(18,2)) AS amount,
        'Distributed' AS status,
        dh.received_status,
        CAST(COALESCE(NULLIF(dh.received_quantity, ''), 0) AS DECIMAL(18,2)) AS received_qty,
        dh.received_date,
        rh.approval_status AS approved_by,
        creator.name_en AS created_by_name,
        dh.created_at,
        dh.comment AS remarks,
        '' AS attachment,
        dh.deleted_at
    FROM distribution_history dh
    LEFT JOIN distribution_summary ds ON dh.distribution_id = ds.distribution_id AND ds.deleted_at IS NULL
    LEFT JOIN requestion_histiory rh ON dh.invoice_id = rh.invoice_id AND rh.deleted_at IS NULL
    LEFT JOIN product_information pi ON dh.product_id = pi.id
    LEFT JOIN product_category pc ON pi.product_category = pc.id
    LEFT JOIN product_subcategory ps ON pi.product_subcategory = ps.id
    LEFT JOIN store_information si ON dh.store_id = si.id
    LEFT JOIN project_information pr ON dh.project_id = pr.id
    LEFT JOIN employee_information emp ON dh.employee_id = emp.id
    LEFT JOIN employee_information recv ON dh.assign_receiver_id = recv.id
    LEFT JOIN employee_information creator ON dh.created_by = creator.id

    UNION ALL

    SELECT
        rhd.date AS transaction_date,
        rhn.created_at AS transaction_time,
        'Return' AS transaction_type,
        'Return' AS source_module,
        rhd.invoice_id AS reference_no,
        rhd.invoice_id AS challan_no,
        rhd.product_id,
        rhd.store_id,
        rhd.project_id,
        pi.code AS product_code,
        pi.name AS product_name,
        pc.name AS category,
        ps.name AS subcategory,
        pi.unit AS unit,
        si.name AS store,
        '' AS from_store,
        '' AS to_store,
        pr.name AS project,
        emp.name_en AS employee,
        recv.name_en AS supplier_receiver,
        CAST(COALESCE(NULLIF(rhd.return_quantity, ''), 0) AS DECIMAL(18,2)) AS qty_in,
        0 AS qty_out,
        CAST(COALESCE(NULLIF(rhd.return_quantity, ''), 0) AS DECIMAL(18,2)) AS net_qty,
        0 AS before_stock,
        0 AS after_stock,
        0 AS rate,
        0 AS amount,
        'Returned' AS status,
        rhd.received_status,
        CAST(COALESCE(NULLIF(rhd.received_quantity, ''), 0) AS DECIMAL(18,2)) AS received_qty,
        rhd.received_date,
        '' AS approved_by,
        creator.name_en AS created_by_name,
        rhd.created_at,
        rhn.note AS remarks,
        rhn.photo AS attachment,
        rhd.deleted_at
    FROM return_history_detail rhd
    LEFT JOIN return_history rhn ON rhd.invoice_id = rhn.invoice_id AND rhn.deleted_at IS NULL
    LEFT JOIN product_information pi ON rhd.product_id = pi.id
    LEFT JOIN product_category pc ON pi.product_category = pc.id
    LEFT JOIN product_subcategory ps ON pi.product_subcategory = ps.id
    LEFT JOIN store_information si ON rhd.store_id = si.id
    LEFT JOIN project_information pr ON rhd.project_id = pr.id
    LEFT JOIN employee_information emp ON rhd.employee_id = emp.id
    LEFT JOIN employee_information recv ON rhd.assign_receiver_id = recv.id
    LEFT JOIN employee_information creator ON rhd.created_by = creator.id
    WHERE CAST(COALESCE(NULLIF(rhd.return_quantity, ''), 0) AS DECIMAL(18,2)) > 0

    UNION ALL

    SELECT
        rhd.date AS transaction_date,
        rhn.created_at AS transaction_time,
        'Damage' AS transaction_type,
        'Return' AS source_module,
        rhd.invoice_id AS reference_no,
        rhd.invoice_id AS challan_no,
        rhd.product_id,
        rhd.store_id,
        rhd.project_id,
        pi.code AS product_code,
        pi.name AS product_name,
        pc.name AS category,
        ps.name AS subcategory,
        pi.unit AS unit,
        si.name AS store,
        '' AS from_store,
        '' AS to_store,
        pr.name AS project,
        emp.name_en AS employee,
        recv.name_en AS supplier_receiver,
        0 AS qty_in,
        CAST(COALESCE(NULLIF(rhd.damage_quantity, ''), 0) AS DECIMAL(18,2)) AS qty_out,
        -CAST(COALESCE(NULLIF(rhd.damage_quantity, ''), 0) AS DECIMAL(18,2)) AS net_qty,
        0 AS before_stock,
        0 AS after_stock,
        0 AS rate,
        0 AS amount,
        'Damaged' AS status,
        rhd.received_status,
        CAST(COALESCE(NULLIF(rhd.received_quantity, ''), 0) AS DECIMAL(18,2)) AS received_qty,
        rhd.received_date,
        '' AS approved_by,
        creator.name_en AS created_by_name,
        rhd.created_at,
        rhn.note AS remarks,
        rhn.photo AS attachment,
        rhd.deleted_at
    FROM return_history_detail rhd
    LEFT JOIN return_history rhn ON rhd.invoice_id = rhn.invoice_id AND rhn.deleted_at IS NULL
    LEFT JOIN product_information pi ON rhd.product_id = pi.id
    LEFT JOIN product_category pc ON pi.product_category = pc.id
    LEFT JOIN product_subcategory ps ON pi.product_subcategory = ps.id
    LEFT JOIN store_information si ON rhd.store_id = si.id
    LEFT JOIN project_information pr ON rhd.project_id = pr.id
    LEFT JOIN employee_information emp ON rhd.employee_id = emp.id
    LEFT JOIN employee_information recv ON rhd.assign_receiver_id = recv.id
    LEFT JOIN employee_information creator ON rhd.created_by = creator.id
    WHERE CAST(COALESCE(NULLIF(rhd.damage_quantity, ''), 0) AS DECIMAL(18,2)) > 0

    UNION ALL

    SELECT
        mud.date AS transaction_date,
        mus.created_at AS transaction_time,
        'Used' AS transaction_type,
        'Material Used' AS source_module,
        mud.invoice_id AS reference_no,
        mud.invoice_id AS challan_no,
        mud.product_id,
        '' AS store_id,
        mud.project_id,
        pi.code AS product_code,
        pi.name AS product_name,
        pc.name AS category,
        ps.name AS subcategory,
        pi.unit AS unit,
        '' AS store,
        '' AS from_store,
        '' AS to_store,
        pr.name AS project,
        emp.name_en AS employee,
        '' AS supplier_receiver,
        0 AS qty_in,
        CAST(COALESCE(NULLIF(mud.used_quantity, ''), NULLIF(mud.used_amount, ''), 0) AS DECIMAL(18,2)) AS qty_out,
        -CAST(COALESCE(NULLIF(mud.used_quantity, ''), NULLIF(mud.used_amount, ''), 0) AS DECIMAL(18,2)) AS net_qty,
        0 AS before_stock,
        0 AS after_stock,
        0 AS rate,
        CAST(COALESCE(NULLIF(mud.used_amount, ''), 0) AS DECIMAL(18,2)) AS amount,
        'Used' AS status,
        'Used' AS received_status,
        CAST(COALESCE(NULLIF(mud.used_quantity, ''), NULLIF(mud.used_amount, ''), 0) AS DECIMAL(18,2)) AS received_qty,
        mud.date AS received_date,
        '' AS approved_by,
        creator.name_en AS created_by_name,
        mud.created_at,
        mus.note AS remarks,
        '' AS attachment,
        mud.deleted_at
    FROM material_used_detail_history mud
    LEFT JOIN material_used_summary mus ON mud.invoice_id = mus.invoice_id AND mus.deleted_at IS NULL
    LEFT JOIN product_information pi ON mud.product_id = pi.id
    LEFT JOIN product_category pc ON pi.product_category = pc.id
    LEFT JOIN product_subcategory ps ON pi.product_subcategory = ps.id
    LEFT JOIN project_information pr ON mud.project_id = pr.id
    LEFT JOIN employee_information emp ON mud.employee_id = emp.id
    LEFT JOIN employee_information creator ON mud.created_by = creator.id

    UNION ALL

    SELECT
        sti.transfer_date AS transaction_date,
        sts.created_at AS transaction_time,
        'Transfer Out' AS transaction_type,
        'Stock Transfer' AS source_module,
        sti.transfer_id AS reference_no,
        sti.transfer_id AS challan_no,
        sti.product_id,
        sti.from_store_id AS store_id,
        '' AS project_id,
        pi.code AS product_code,
        pi.name AS product_name,
        pc.name AS category,
        ps.name AS subcategory,
        pi.unit AS unit,
        fs.name AS store,
        fs.name AS from_store,
        ts.name AS to_store,
        '' AS project,
        creator.name_en AS employee,
        ts.name AS supplier_receiver,
        0 AS qty_in,
        CAST(COALESCE(NULLIF(sti.quantity, ''), 0) AS DECIMAL(18,2)) AS qty_out,
        -CAST(COALESCE(NULLIF(sti.quantity, ''), 0) AS DECIMAL(18,2)) AS net_qty,
        CAST(COALESCE(NULLIF(sti.from_stock, ''), 0) AS DECIMAL(18,2)) AS before_stock,
        CAST(COALESCE(NULLIF(sti.from_new_stock, ''), 0) AS DECIMAL(18,2)) AS after_stock,
        0 AS rate,
        0 AS amount,
        'Approved' AS status,
        sti.received_status,
        CAST(COALESCE(NULLIF(sti.received_quantity, ''), 0) AS DECIMAL(18,2)) AS received_qty,
        sti.received_date,
        '' AS approved_by,
        creator.name_en AS created_by_name,
        sti.created_at,
        sts.note AS remarks,
        sts.photo AS attachment,
        sti.deleted_at
    FROM stock_transfer_information sti
    LEFT JOIN stock_transfer_summary sts ON sti.transfer_id = sts.transfer_id AND sts.deleted_at IS NULL
    LEFT JOIN product_information pi ON sti.product_id = pi.id
    LEFT JOIN product_category pc ON pi.product_category = pc.id
    LEFT JOIN product_subcategory ps ON pi.product_subcategory = ps.id
    LEFT JOIN store_information fs ON sti.from_store_id = fs.id
    LEFT JOIN store_information ts ON sti.to_store_id = ts.id
    LEFT JOIN employee_information creator ON sti.created_by = creator.id

    UNION ALL

    SELECT
        sti.received_date AS transaction_date,
        sti.received_at AS transaction_time,
        'Transfer In' AS transaction_type,
        'Stock Transfer' AS source_module,
        sti.transfer_id AS reference_no,
        sti.transfer_id AS challan_no,
        sti.product_id,
        sti.to_store_id AS store_id,
        '' AS project_id,
        pi.code AS product_code,
        pi.name AS product_name,
        pc.name AS category,
        ps.name AS subcategory,
        pi.unit AS unit,
        ts.name AS store,
        fs.name AS from_store,
        ts.name AS to_store,
        '' AS project,
        receiver.name_en AS employee,
        fs.name AS supplier_receiver,
        CAST(COALESCE(NULLIF(sti.received_quantity, ''), NULLIF(sti.quantity, ''), 0) AS DECIMAL(18,2)) AS qty_in,
        0 AS qty_out,
        CAST(COALESCE(NULLIF(sti.received_quantity, ''), NULLIF(sti.quantity, ''), 0) AS DECIMAL(18,2)) AS net_qty,
        CAST(COALESCE(NULLIF(sti.to_stock, ''), 0) AS DECIMAL(18,2)) AS before_stock,
        CAST(COALESCE(NULLIF(sti.to_new_stock, ''), 0) AS DECIMAL(18,2)) AS after_stock,
        0 AS rate,
        0 AS amount,
        'Received' AS status,
        sti.received_status,
        CAST(COALESCE(NULLIF(sti.received_quantity, ''), NULLIF(sti.quantity, ''), 0) AS DECIMAL(18,2)) AS received_qty,
        sti.received_date,
        '' AS approved_by,
        creator.name_en AS created_by_name,
        sti.created_at,
        sts.note AS remarks,
        sts.photo AS attachment,
        sti.deleted_at
    FROM stock_transfer_information sti
    LEFT JOIN stock_transfer_summary sts ON sti.transfer_id = sts.transfer_id AND sts.deleted_at IS NULL
    LEFT JOIN product_information pi ON sti.product_id = pi.id
    LEFT JOIN product_category pc ON pi.product_category = pc.id
    LEFT JOIN product_subcategory ps ON pi.product_subcategory = ps.id
    LEFT JOIN store_information fs ON sti.from_store_id = fs.id
    LEFT JOIN store_information ts ON sti.to_store_id = ts.id
    LEFT JOIN employee_information creator ON sti.created_by = creator.id
    LEFT JOIN employee_information receiver ON sti.received_by = receiver.id
    WHERE sti.received_status = 'Complete'
) lifecycle
WHERE $where_sql
ORDER BY lifecycle.transaction_date ASC, lifecycle.created_at ASC, lifecycle.reference_no ASC";

$lifecycle_statement = $pdo->prepare($lifecycle_sql);
$lifecycle_statement->execute($params);
$lifecycle_rows = $lifecycle_statement->fetchAll();

$total_in = 0;
$total_out = 0;
$total_amount = 0;
$running_balance = 0;
?>

<section class="content product-lifecycle-report">
      <div class="card lifecycle-card">
        <div class="card-header">
          <h3 class="card-title"><?php echo str_replace("_"," ",$page_title); ?></h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
              <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
        <div class="card-body">
          <form method="post" action="?<?php echo $page_title; ?>/<?php echo $MenuName; ?>" enctype="multipart/form-data" class="lifecycle-filter d-print-none">
            <div class="row">
              <div class="col-lg-3 col-md-6">
                <div class="form-group">
                  <label for="product_id">Product Name</label>
                  <select class="form-control select2" name="product_id" id="product_id" data-placeholder="Select Product Name" style="width: 100%;">
                    <option value="">All Products</option>
                    <?php
                    $ProductInfo = $pdo->query("SELECT id,name,code FROM product_information WHERE deleted_at is NULL ORDER BY name ASC");
                    while($rowProductInfo = $ProductInfo->fetch()){
                    ?>
                    <option value="<?php echo $rowProductInfo["id"]; ?>" <?php if($product_id == $rowProductInfo["id"]){ echo "selected"; } ?>>
                      <?php echo lifecycleText($rowProductInfo["name"]); ?><?php if(!empty($rowProductInfo["code"])){ echo " - ".lifecycleText($rowProductInfo["code"]); } ?>
                    </option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="col-lg-2 col-md-6">
                <div class="form-group">
                  <label for="store_id">Store</label>
                  <select class="form-control select2" name="store_id" id="store_id" data-placeholder="Select Store" style="width: 100%;">
                    <option value="">All Stores</option>
                    <?php
                    $StoreInfo = $pdo->query("SELECT id,name FROM store_information WHERE deleted_at is NULL ORDER BY name ASC");
                    while($rowStoreInfo = $StoreInfo->fetch()){
                    ?>
                    <option value="<?php echo $rowStoreInfo["id"]; ?>" <?php if($store_id == $rowStoreInfo["id"]){ echo "selected"; } ?>><?php echo lifecycleText($rowStoreInfo["name"]); ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="col-lg-2 col-md-6">
                <div class="form-group">
                  <label for="project_id">Project</label>
                  <select class="form-control select2" name="project_id" id="project_id" data-placeholder="Select Project" style="width: 100%;">
                    <option value="">All Projects</option>
                    <?php
                    $ProjectInfo = $pdo->query("SELECT id,name FROM project_information WHERE deleted_at is NULL ORDER BY name ASC");
                    while($rowProjectInfo = $ProjectInfo->fetch()){
                    ?>
                    <option value="<?php echo $rowProjectInfo["id"]; ?>" <?php if($project_id == $rowProjectInfo["id"]){ echo "selected"; } ?>><?php echo lifecycleText($rowProjectInfo["name"]); ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="col-lg-2 col-md-6">
                <div class="form-group">
                  <label for="transaction_type">Transaction Type</label>
                  <select class="form-control" name="transaction_type" id="transaction_type">
                    <option value="">All Types</option>
                    <?php foreach(['Purchase','Distribution','Return','Damage','Used','Transfer Out','Transfer In'] as $type){ ?>
                    <option value="<?php echo $type; ?>" <?php if($transaction_type == $type){ echo "selected"; } ?>><?php echo $type; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="col-lg-1 col-md-6">
                <div class="form-group">
                  <label for="from_date">From</label>
                  <input type="date" class="form-control" name="from_date" id="from_date" value="<?php echo lifecycleText($from_date); ?>">
                </div>
              </div>
              <div class="col-lg-1 col-md-6">
                <div class="form-group">
                  <label for="to_date">To</label>
                  <input type="date" class="form-control" name="to_date" id="to_date" value="<?php echo lifecycleText($to_date); ?>">
                </div>
              </div>
              <div class="col-lg-1 col-md-12">
                <div class="form-group">
                  <label>&nbsp;</label>
                  <button type="submit" name="view" value="Search" class="btn btn-primary btn-block">Search</button>
                </div>
              </div>
            </div>

            <div class="lifecycle-column-panel">
              <div class="d-flex flex-wrap align-items-center justify-content-between">
                <strong>Visible Columns</strong>
                <div class="btn-group btn-group-sm" role="group">
                  <button type="button" class="btn btn-outline-secondary" id="lifecycleDefaultColumns">Default</button>
                  <button type="button" class="btn btn-outline-secondary" id="lifecycleAllColumns">All</button>
                </div>
              </div>
              <div class="lifecycle-column-grid">
                <?php foreach($lifecycle_columns as $column_key => $column_label){ ?>
                <label class="lifecycle-column-option">
                  <input type="checkbox" name="columns[]" value="<?php echo $column_key; ?>" data-default="<?php echo in_array($column_key, $default_columns) ? '1' : '0'; ?>" <?php if(in_array($column_key, $selected_columns)){ echo "checked"; } ?>>
                  <span><?php echo $column_label; ?></span>
                </label>
                <?php } ?>
              </div>
            </div>
          </form>

          <div class="row lifecycle-summary">
            <div class="col-md-3 col-6">
              <div class="info-box mb-2">
                <span class="info-box-icon bg-info"><i class="fas fa-list"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Rows</span>
                  <span class="info-box-number"><?php echo count($lifecycle_rows); ?></span>
                </div>
              </div>
            </div>
            <div class="col-md-3 col-6">
              <div class="info-box mb-2">
                <span class="info-box-icon bg-success"><i class="fas fa-arrow-down"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Qty In</span>
                  <span class="info-box-number" id="lifecycleTotalIn">0</span>
                </div>
              </div>
            </div>
            <div class="col-md-3 col-6">
              <div class="info-box mb-2">
                <span class="info-box-icon bg-warning"><i class="fas fa-arrow-up"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Qty Out</span>
                  <span class="info-box-number" id="lifecycleTotalOut">0</span>
                </div>
              </div>
            </div>
            <div class="col-md-3 col-6">
              <div class="info-box mb-2">
                <span class="info-box-icon bg-secondary"><i class="fas fa-balance-scale"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Net Qty</span>
                  <span class="info-box-number" id="lifecycleNetQty">0</span>
                </div>
              </div>
            </div>
          </div>

          <div class="table-responsive lifecycle-table-wrap">
            <table class="table table-bordered table-striped table-hover lifecycle-table" id="example1">
              <thead>
                <tr>
                  <th>SL</th>
                  <?php foreach($selected_columns as $column_key){ ?>
                  <th><?php echo $lifecycle_columns[$column_key]; ?></th>
                  <?php } ?>
                </tr>
              </thead>
              <tbody>
                <?php
                $sl = 1;
                foreach($lifecycle_rows as $rowdata){
                    $qty_in = lifecycleNumber($rowdata['qty_in']);
                    $qty_out = lifecycleNumber($rowdata['qty_out']);
                    $net_qty = lifecycleNumber($rowdata['net_qty']);
                    $running_balance += $net_qty;
                    $total_in += $qty_in;
                    $total_out += $qty_out;
                    $total_amount += lifecycleNumber($rowdata['amount']);
                    $rowdata['running_balance'] = $running_balance;
                    $rowdata['action'] = lifecycleLink($rowdata);
                ?>
                <tr>
                  <td class="text-center"><?php echo $sl++; ?></td>
                  <?php foreach($selected_columns as $column_key){ ?>
                  <td class="<?php echo in_array($column_key, ['qty_in','qty_out','net_qty','before_stock','after_stock','running_balance','rate','amount','received_qty']) ? 'text-right' : ''; ?>">
                    <?php
                    if ($column_key == 'transaction_date' || $column_key == 'received_date') {
                        echo lifecycleDate($rowdata[$column_key]);
                    } elseif (in_array($column_key, ['qty_in','qty_out','net_qty','before_stock','after_stock','running_balance','received_qty'])) {
                        echo number_format(lifecycleNumber($rowdata[$column_key]), 2);
                    } elseif (in_array($column_key, ['rate','amount'])) {
                        echo lifecycleAmount($rowdata[$column_key]);
                    } elseif ($column_key == 'attachment') {
                        if (!empty($rowdata['attachment'])) {
                            $attachmentPath = ($rowdata['source_module'] == 'Purchase') ? 'PurchaseHistory/' : (($rowdata['source_module'] == 'Stock Transfer') ? 'StockTransfer/' : 'ReturnHistory/');
                            echo '<a href="download.php?path=' . lifecycleText($attachmentPath) . '&download_file=' . lifecycleText($rowdata['attachment']) . '">Download</a>';
                        }
                    } elseif ($column_key == 'action') {
                        echo '<a class="btn btn-primary btn-xs" href="' . lifecycleText($rowdata['action']) . '">View</a>';
                    } else {
                        echo lifecycleText($rowdata[$column_key]);
                    }
                    ?>
                  </td>
                  <?php } ?>
                </tr>
                <?php } ?>
              </tbody>
              <tfoot>
                <tr>
                  <th class="text-right">Total</th>
                  <?php foreach($selected_columns as $column_key){ ?>
                    <?php if($column_key == 'qty_in'){ ?>
                      <th class="text-right"><?php echo number_format($total_in, 2); ?></th>
                    <?php } elseif($column_key == 'qty_out'){ ?>
                      <th class="text-right"><?php echo number_format($total_out, 2); ?></th>
                    <?php } elseif($column_key == 'net_qty' || $column_key == 'running_balance'){ ?>
                      <th class="text-right"><?php echo number_format($running_balance, 2); ?></th>
                    <?php } elseif($column_key == 'amount'){ ?>
                      <th class="text-right"><?php echo number_format($total_amount, 2); ?></th>
                    <?php } else { ?>
                      <th></th>
                    <?php } ?>
                  <?php } ?>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </section>

<style>
.product-lifecycle-report .card-body {
  padding: 16px;
}
.product-lifecycle-report .form-group {
  margin-bottom: 12px;
}
.lifecycle-column-panel {
  border: 1px solid #dee2e6;
  border-radius: 6px;
  padding: 12px;
  margin-bottom: 14px;
  background: #f8f9fa;
}
.lifecycle-column-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
  gap: 8px 12px;
  margin-top: 10px;
}
.lifecycle-column-option {
  display: flex;
  align-items: center;
  gap: 7px;
  margin: 0;
  font-weight: 400;
}
.lifecycle-column-option span {
  line-height: 1.25;
}
.lifecycle-summary .info-box {
  min-height: 72px;
}
.lifecycle-table-wrap {
  width: 100%;
  overflow-x: auto;
}
.lifecycle-table {
  width: 100%;
  min-width: 1180px;
  font-size: 13px;
}
.lifecycle-table th,
.lifecycle-table td {
  vertical-align: middle;
  white-space: nowrap;
}
.lifecycle-table td:nth-child(2),
.lifecycle-table td:nth-child(7),
.lifecycle-table td:nth-child(8),
.lifecycle-table th:nth-child(2),
.lifecycle-table th:nth-child(7),
.lifecycle-table th:nth-child(8) {
  white-space: normal;
  min-width: 130px;
}
@media screen and (max-width: 767.98px) {
  .product-lifecycle-report .card-body {
    padding: 10px;
  }
  .lifecycle-column-grid {
    grid-template-columns: 1fr;
  }
  .lifecycle-table {
    font-size: 12px;
    min-width: 980px;
  }
  .lifecycle-summary .info-box {
    min-height: 64px;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var totalIn = <?php echo json_encode(number_format($total_in, 2)); ?>;
  var totalOut = <?php echo json_encode(number_format($total_out, 2)); ?>;
  var netQty = <?php echo json_encode(number_format($running_balance, 2)); ?>;
  var totalInEl = document.getElementById('lifecycleTotalIn');
  var totalOutEl = document.getElementById('lifecycleTotalOut');
  var netQtyEl = document.getElementById('lifecycleNetQty');
  if (totalInEl) totalInEl.textContent = totalIn;
  if (totalOutEl) totalOutEl.textContent = totalOut;
  if (netQtyEl) netQtyEl.textContent = netQty;

  var defaultButton = document.getElementById('lifecycleDefaultColumns');
  var allButton = document.getElementById('lifecycleAllColumns');
  var checkboxes = document.querySelectorAll('.lifecycle-column-option input[type="checkbox"]');
  if (defaultButton) {
    defaultButton.addEventListener('click', function () {
      checkboxes.forEach(function (input) {
        input.checked = input.getAttribute('data-default') === '1';
      });
    });
  }
  if (allButton) {
    allButton.addEventListener('click', function () {
      checkboxes.forEach(function (input) {
        input.checked = true;
      });
    });
  }
});
</script>
