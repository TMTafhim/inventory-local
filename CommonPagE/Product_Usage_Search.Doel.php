<?php
function productUsageText($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function productUsageDate($value) {
    if (empty($value) || $value === '0000-00-00') {
        return '';
    }
    $time = strtotime($value);
    return $time ? date('d-m-Y', $time) : productUsageText($value);
}

function productUsageNumber($value) {
    if ($value === null || $value === '') {
        return '';
    }
    return is_numeric($value) ? rtrim(rtrim(number_format((float)$value, 4, '.', ''), '0'), '.') : productUsageText($value);
}

function productUsageModuleLink($module, $reference, $extra = '') {
    $reference = urlencode((string)$reference);
    $extra = urlencode((string)$extra);
    if ($module === 'Requisition') {
        return '?Requestion_History_Detail/' . $reference;
    }
    if ($module === 'Requisition Draft') {
        return '?Requestion_Draft_History_Detail/' . $reference;
    }
    if ($module === 'Distribution') {
        return '?Distribution_History_Detail/' . $extra;
    }
    if ($module === 'Purchase') {
        return '?Purchase_HistoryDetail/' . $reference;
    }
    if ($module === 'Return') {
        return '?Return_History_View/' . $reference;
    }
    if ($module === 'Material Used') {
        return '?Project_Material_Used_History_View/' . $reference;
    }
    if ($module === 'Stock Transfer') {
        return '?Stock_Transfer_detail_vew/Stock Transfer/' . $reference;
    }
    if ($module === 'Emergency Request') {
        return '?Emergency_Request_Detail/' . $reference;
    }
    return '#';
}

$search = '';
if (!empty($_POST['product_usage_search'])) {
    $search = trim($_POST['product_usage_search']);
} elseif (!empty($_GET['product_usage_search'])) {
    $search = trim($_GET['product_usage_search']);
}

$routeProductId = (!empty($DocumentData) && is_numeric($DocumentData)) ? (int)$DocumentData : 0;
$selectedProductId = !empty($_POST['product_id']) ? (int)$_POST['product_id'] : $routeProductId;
$matchedProducts = array();
$usageRows = array();
$summaryCounts = array();

if ($selectedProductId > 0) {
    $productStatement = $pdo->prepare("
        SELECT product_information.*, product_category.name AS product_category_name
        FROM product_information
        LEFT JOIN product_category ON product_information.product_category = product_category.id
        WHERE product_information.id = :id
        LIMIT 1
    ");
    $productStatement->execute(array(':id' => $selectedProductId));
    $matchedProducts = $productStatement->fetchAll();
    if (!empty($matchedProducts[0])) {
        $search = $matchedProducts[0]['name'];
    }
} elseif ($search !== '') {
    $searchTerms = preg_split('/\s+/', preg_replace('/[^A-Za-z0-9]+/', ' ', $search));
    $searchTerms = array_values(array_filter($searchTerms, function($term) {
        return $term !== '';
    }));
    $termWhere = array();
    $termParams = array(
        ':search_name' => '%' . $search . '%',
        ':search_code' => '%' . $search . '%',
        ':search_description' => '%' . $search . '%'
    );
    foreach ($searchTerms as $index => $term) {
        $nameKey = ':search_term_name_' . $index;
        $codeKey = ':search_term_code_' . $index;
        $descriptionKey = ':search_term_description_' . $index;
        $termWhere[] = "(product_information.name LIKE $nameKey OR product_information.code LIKE $codeKey OR product_information.description LIKE $descriptionKey)";
        $termParams[$nameKey] = '%' . $term . '%';
        $termParams[$codeKey] = '%' . $term . '%';
        $termParams[$descriptionKey] = '%' . $term . '%';
    }
    $termSql = !empty($termWhere) ? ' OR (' . implode(' AND ', $termWhere) . ')' : '';
    $productStatement = $pdo->prepare("
        SELECT product_information.*, product_category.name AS product_category_name
        FROM product_information
        LEFT JOIN product_category ON product_information.product_category = product_category.id
        WHERE product_information.deleted_at IS NULL
          AND (
            product_information.name LIKE :search_name
            OR product_information.code LIKE :search_code
            OR product_information.description LIKE :search_description
            $termSql
          )
        ORDER BY product_information.name, product_information.code
        LIMIT 50
    ");
    $productStatement->execute($termParams);
    $matchedProducts = $productStatement->fetchAll();
}

$productIds = array();
foreach ($matchedProducts as $product) {
    if (!empty($product['id'])) {
        $productIds[] = (int)$product['id'];
        $summaryCounts[(int)$product['id']] = array(
            'Requisition' => 0,
            'Requisition Draft' => 0,
            'Distribution' => 0,
            'Purchase' => 0,
            'Return' => 0,
            'Material Used' => 0,
            'Current Stock' => 0,
            'Daily Stock' => 0,
            'Stock Transfer' => 0,
            'Emergency Request' => 0
        );
    }
}
$productIds = array_values(array_unique($productIds));

if (!empty($productIds)) {
    $inSql = implode(',', array_fill(0, count($productIds), '?'));

    $usageSql = "
        SELECT * FROM (
            SELECT
                'Requisition' AS module_name,
                rd.product_id,
                pi.name AS product_name,
                pi.code AS product_code,
                pi.unit AS product_unit,
                rd.invoice_id AS reference_no,
                '' AS secondary_no,
                rd.date AS record_date,
                pr.name AS project_name,
                si.name AS store_name,
                emp.name_en AS employee_name,
                rh.approval_status AS status_text,
                rd.requestion_quantity AS quantity_one,
                rd.final_quantity AS quantity_two,
                rd.due_quantity AS quantity_three,
                rd.requestion_amount AS amount_one,
                rd.final_amount AS amount_two,
                rd.comment AS note_text,
                rd.created_at,
                rd.deleted_at
            FROM requestion_detail rd
            LEFT JOIN requestion_histiory rh ON rd.invoice_id = rh.invoice_id
            LEFT JOIN product_information pi ON rd.product_id = pi.id
            LEFT JOIN project_information pr ON rd.project_id = pr.id
            LEFT JOIN store_information si ON rd.store_id = si.id
            LEFT JOIN employee_information emp ON rd.employee_id = emp.id
            WHERE rd.deleted_at IS NULL AND rd.product_id IN ($inSql)

            UNION ALL

            SELECT
                'Requisition Draft' AS module_name,
                rdd.product_id,
                pi.name AS product_name,
                pi.code AS product_code,
                pi.unit AS product_unit,
                rdd.invoice_id AS reference_no,
                '' AS secondary_no,
                rdd.date AS record_date,
                pr.name AS project_name,
                si.name AS store_name,
                emp.name_en AS employee_name,
                rdh.final_submit_status AS status_text,
                rdd.requestion_quantity AS quantity_one,
                '' AS quantity_two,
                '' AS quantity_three,
                rdd.requestion_amount AS amount_one,
                '' AS amount_two,
                rdd.comment AS note_text,
                rdd.created_at,
                rdd.deleted_at
            FROM requestion_draft_detail rdd
            LEFT JOIN requestion_draft_histiory rdh ON rdd.invoice_id = rdh.invoice_id
            LEFT JOIN product_information pi ON rdd.product_id = pi.id
            LEFT JOIN project_information pr ON rdd.project_id = pr.id
            LEFT JOIN store_information si ON rdd.store_id = si.id
            LEFT JOIN employee_information emp ON rdd.employee_id = emp.id
            WHERE rdd.deleted_at IS NULL AND rdd.product_id IN ($inSql)

            UNION ALL

            SELECT
                'Distribution' AS module_name,
                dh.product_id,
                pi.name AS product_name,
                pi.code AS product_code,
                pi.unit AS product_unit,
                dh.invoice_id AS reference_no,
                dh.distribution_id AS secondary_no,
                dh.date AS record_date,
                pr.name AS project_name,
                si.name AS store_name,
                recv.name_en AS employee_name,
                dh.received_status AS status_text,
                dh.requestion_quantity AS quantity_one,
                dh.distribution_quantity AS quantity_two,
                dh.due_quantity AS quantity_three,
                dh.requestion_amount AS amount_one,
                dh.distribution_amount AS amount_two,
                dh.comment AS note_text,
                dh.created_at,
                dh.deleted_at
            FROM distribution_history dh
            LEFT JOIN product_information pi ON dh.product_id = pi.id
            LEFT JOIN project_information pr ON dh.project_id = pr.id
            LEFT JOIN store_information si ON dh.store_id = si.id
            LEFT JOIN employee_information recv ON dh.assign_receiver_id = recv.id
            WHERE dh.deleted_at IS NULL AND dh.product_id IN ($inSql)

            UNION ALL

            SELECT
                'Purchase' AS module_name,
                pd.product_id,
                pi.name AS product_name,
                pi.code AS product_code,
                COALESCE(NULLIF(pd.unit, ''), pi.unit) AS product_unit,
                pd.invoice_id AS reference_no,
                pd.purchase_id AS secondary_no,
                pd.date AS record_date,
                '' AS project_name,
                si.name AS store_name,
                sup.organization AS employee_name,
                ph.purchase_type AS status_text,
                pd.quantity AS quantity_one,
                pd.before_quantity AS quantity_two,
                pd.after_quantity AS quantity_three,
                pd.amount AS amount_one,
                pd.rate AS amount_two,
                pd.note AS note_text,
                pd.created_at,
                pd.deleted_at
            FROM purchase_detail pd
            LEFT JOIN purchase_history ph ON pd.invoice_id = ph.invoice_id
            LEFT JOIN product_information pi ON pd.product_id = pi.id
            LEFT JOIN store_information si ON pd.store_id = si.id
            LEFT JOIN supplier_information sup ON pd.supplier_id = sup.id
            WHERE pd.deleted_at IS NULL AND pd.product_id IN ($inSql)

            UNION ALL

            SELECT
                'Return' AS module_name,
                rhd.product_id,
                pi.name AS product_name,
                pi.code AS product_code,
                pi.unit AS product_unit,
                rhd.invoice_id AS reference_no,
                '' AS secondary_no,
                rhd.date AS record_date,
                pr.name AS project_name,
                si.name AS store_name,
                emp.name_en AS employee_name,
                rhd.received_status AS status_text,
                rhd.requestion_quantity AS quantity_one,
                rhd.return_quantity AS quantity_two,
                rhd.damage_quantity AS quantity_three,
                '' AS amount_one,
                '' AS amount_two,
                '' AS note_text,
                rhd.created_at,
                rhd.deleted_at
            FROM return_history_detail rhd
            LEFT JOIN product_information pi ON rhd.product_id = pi.id
            LEFT JOIN project_information pr ON rhd.project_id = pr.id
            LEFT JOIN store_information si ON rhd.store_id = si.id
            LEFT JOIN employee_information emp ON rhd.employee_id = emp.id
            WHERE rhd.deleted_at IS NULL AND rhd.product_id IN ($inSql)

            UNION ALL

            SELECT
                'Material Used' AS module_name,
                mud.product_id,
                pi.name AS product_name,
                pi.code AS product_code,
                pi.unit AS product_unit,
                mud.invoice_id AS reference_no,
                '' AS secondary_no,
                mud.date AS record_date,
                pr.name AS project_name,
                '' AS store_name,
                emp.name_en AS employee_name,
                mus.material_used_type AS status_text,
                mud.used_quantity AS quantity_one,
                '' AS quantity_two,
                '' AS quantity_three,
                mud.used_amount AS amount_one,
                '' AS amount_two,
                mus.note AS note_text,
                mud.created_at,
                mud.deleted_at
            FROM material_used_detail_history mud
            LEFT JOIN material_used_summary mus ON mud.invoice_id = mus.invoice_id
            LEFT JOIN product_information pi ON mud.product_id = pi.id
            LEFT JOIN project_information pr ON mud.project_id = pr.id
            LEFT JOIN employee_information emp ON mud.employee_id = emp.id
            WHERE mud.deleted_at IS NULL AND mud.product_id IN ($inSql)

            UNION ALL

            SELECT
                'Current Stock' AS module_name,
                st.product_id,
                pi.name AS product_name,
                pi.code AS product_code,
                pi.unit AS product_unit,
                st.id AS reference_no,
                '' AS secondary_no,
                st.updated_at AS record_date,
                '' AS project_name,
                si.name AS store_name,
                '' AS employee_name,
                'Current Stock' AS status_text,
                st.stock AS quantity_one,
                st.total AS quantity_two,
                st.distribution AS quantity_three,
                '' AS amount_one,
                '' AS amount_two,
                CONCAT('Previous: ', COALESCE(st.previous, 0), ', New: ', COALESCE(st.new, 0), ', Return: ', COALESCE(st.`return`, 0)) AS note_text,
                st.created_at,
                st.deleted_at
            FROM stock_information st
            LEFT JOIN product_information pi ON st.product_id = pi.id
            LEFT JOIN store_information si ON st.store_id = si.id
            WHERE st.deleted_at IS NULL AND st.product_id IN ($inSql)

            UNION ALL

            SELECT
                'Daily Stock' AS module_name,
                std.product_id,
                pi.name AS product_name,
                pi.code AS product_code,
                pi.unit AS product_unit,
                std.id AS reference_no,
                '' AS secondary_no,
                std.date AS record_date,
                '' AS project_name,
                si.name AS store_name,
                '' AS employee_name,
                'Daily Stock' AS status_text,
                std.stock AS quantity_one,
                std.total AS quantity_two,
                std.distribution AS quantity_three,
                '' AS amount_one,
                '' AS amount_two,
                CONCAT('Previous: ', COALESCE(std.previous, 0), ', New: ', COALESCE(std.new, 0), ', Return: ', COALESCE(std.`return`, 0)) AS note_text,
                std.created_at,
                std.deleted_at
            FROM stock_information_detail std
            LEFT JOIN product_information pi ON std.product_id = pi.id
            LEFT JOIN store_information si ON std.store_id = si.id
            WHERE std.deleted_at IS NULL AND std.product_id IN ($inSql)

            UNION ALL

            SELECT
                'Stock Transfer' AS module_name,
                sti.product_id,
                pi.name AS product_name,
                pi.code AS product_code,
                pi.unit AS product_unit,
                sti.transfer_id AS reference_no,
                '' AS secondary_no,
                sti.transfer_date AS record_date,
                '' AS project_name,
                CONCAT(COALESCE(from_store.name, ''), ' -> ', COALESCE(to_store.name, '')) AS store_name,
                emp.name_en AS employee_name,
                sti.received_status AS status_text,
                sti.quantity AS quantity_one,
                sti.from_stock AS quantity_two,
                sti.to_stock AS quantity_three,
                '' AS amount_one,
                '' AS amount_two,
                CONCAT('From new stock: ', COALESCE(sti.from_new_stock, ''), ', To new stock: ', COALESCE(sti.to_new_stock, '')) AS note_text,
                sti.created_at,
                sti.deleted_at
            FROM stock_transfer_information sti
            LEFT JOIN product_information pi ON sti.product_id = pi.id
            LEFT JOIN store_information from_store ON sti.from_store_id = from_store.id
            LEFT JOIN store_information to_store ON sti.to_store_id = to_store.id
            LEFT JOIN employee_information emp ON sti.created_by = emp.id
            WHERE sti.deleted_at IS NULL AND sti.product_id IN ($inSql)

            UNION ALL

            SELECT
                'Emergency Request' AS module_name,
                erd.product_id,
                pi.name AS product_name,
                pi.code AS product_code,
                pi.unit AS product_unit,
                er.id AS reference_no,
                er.request_no AS secondary_no,
                er.date AS record_date,
                pr.name AS project_name,
                si.name AS store_name,
                recv.name_en AS employee_name,
                er.status AS status_text,
                erd.issued_quantity AS quantity_one,
                erd.reconciled_quantity AS quantity_two,
                (erd.issued_quantity - erd.reconciled_quantity) AS quantity_three,
                '' AS amount_one,
                '' AS amount_two,
                erd.note AS note_text,
                erd.created_at,
                er.deleted_at
            FROM emergency_request_detail erd
            LEFT JOIN emergency_request er ON erd.emergency_request_id = er.id
            LEFT JOIN product_information pi ON erd.product_id = pi.id
            LEFT JOIN project_information pr ON er.project_id = pr.id
            LEFT JOIN store_information si ON er.store_id = si.id
            LEFT JOIN employee_information recv ON er.receiver_id = recv.id
            WHERE er.deleted_at IS NULL AND erd.product_id IN ($inSql)
        ) product_usage
        ORDER BY product_usage.product_id, product_usage.record_date DESC, product_usage.created_at DESC
    ";

    $usageFilterCount = substr_count($usageSql, 'IN (' . $inSql . ')');
    $params = array();
    for ($filterIndex = 0; $filterIndex < $usageFilterCount; $filterIndex++) {
        foreach ($productIds as $productId) {
            $params[] = $productId;
        }
    }

    $usageStatement = $pdo->prepare($usageSql);
    $usageStatement->execute($params);
    $usageRows = $usageStatement->fetchAll();

    foreach ($usageRows as $row) {
        $pid = (int)$row['product_id'];
        $module = $row['module_name'];
        if (isset($summaryCounts[$pid][$module])) {
            $summaryCounts[$pid][$module]++;
        }
    }
}
?>

<style>
.usage-search-toolbar{display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap}
.usage-search-toolbar .form-group{margin-bottom:0;min-width:280px;flex:1}
.usage-product-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;margin-bottom:16px}
.usage-product-card{border:1px solid #dfe7ef;border-radius:8px;padding:14px;background:#fff}
.usage-product-card h5{font-size:16px;margin:0 0 6px;font-weight:700;color:#152238}
.usage-product-meta{font-size:13px;color:#64748b;line-height:1.6}
.usage-counts{display:flex;gap:6px;flex-wrap:wrap;margin-top:10px}
.usage-counts .badge{font-size:11px;padding:6px 8px}
.usage-empty{padding:24px;text-align:center;color:#64748b;border:1px dashed #cbd5e1;border-radius:8px;background:#f8fafc}
.usage-table th,.usage-table td{vertical-align:middle!important;font-size:13px}
.usage-module{font-weight:700;color:#0f766e}
.usage-note{max-width:260px;white-space:normal}
</style>

<section class="content">
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Product Usage Search</h3>
      <div class="card-tools">
        <a href="?Product_Information/Setting" class="btn btn-warning btn-sm"><i class="fas fa-arrow-left"></i> Product List</a>
      </div>
    </div>
    <div class="card-body">
      <form method="post" action="?Product_Usage_Search/Report" class="usage-search-toolbar">
        <div class="form-group">
          <label>Product name, code or description</label>
          <input type="text" class="form-control" name="product_usage_search" value="<?php echo productUsageText($search); ?>" placeholder="Example: Hammer Wrench 27">
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search Usage</button>
      </form>
    </div>
  </div>

  <?php if ($search !== '' || $selectedProductId > 0) { ?>
    <?php if (empty($matchedProducts)) { ?>
      <div class="usage-empty">No product found for this search.</div>
    <?php } else { ?>
      <div class="usage-product-grid">
        <?php foreach ($matchedProducts as $product) {
          $pid = (int)$product['id'];
          $totalUsage = 0;
          foreach ($summaryCounts[$pid] as $count) {
              $totalUsage += (int)$count;
          }
        ?>
          <div class="usage-product-card">
            <h5><?php echo productUsageText($product['name']); ?></h5>
            <div class="usage-product-meta">
              ID: <?php echo productUsageText($product['id']); ?> |
              Code: <?php echo productUsageText($product['code']); ?> |
              Unit: <?php echo productUsageText($product['unit']); ?><br>
              Category: <?php echo productUsageText($product['product_category_name']); ?> |
              Total Usage Rows: <strong><?php echo $totalUsage; ?></strong>
            </div>
            <div class="usage-counts">
              <?php foreach ($summaryCounts[$pid] as $module => $count) {
                if ((int)$count > 0) { ?>
                  <span class="badge badge-info"><?php echo productUsageText($module); ?>: <?php echo (int)$count; ?></span>
              <?php }} ?>
              <?php if ($totalUsage === 0) { ?>
                <span class="badge badge-secondary">No active usage found</span>
              <?php } ?>
            </div>
            <div class="mt-3">
              <a class="btn btn-info btn-sm" href="?Product_Information_Edit/Setting/<?php echo $pid; ?>/product_information"><i class="fas fa-pencil-alt"></i> Edit</a>
              <a class="btn btn-outline-primary btn-sm" href="?Product_Usage_Search/Report/<?php echo $pid; ?>"><i class="fas fa-filter"></i> Only This</a>
            </div>
          </div>
        <?php } ?>
      </div>

      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Where These Product IDs Are Used</h3>
        </div>
        <div class="card-body p-0">
          <table id="example1" class="table table-bordered table-striped usage-table">
            <thead>
              <tr>
                <th>SL</th>
                <th>Module</th>
                <th>Product</th>
                <th>Reference</th>
                <th>Date</th>
                <th>Project / Store</th>
                <th>Person / Supplier</th>
                <th>Status</th>
                <th>Qty / Amount</th>
                <th>Note</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php $sl = 1; foreach ($usageRows as $row) {
                $link = productUsageModuleLink($row['module_name'], $row['reference_no'], $row['secondary_no']);
              ?>
                <tr>
                  <td><?php echo $sl++; ?></td>
                  <td class="usage-module"><?php echo productUsageText($row['module_name']); ?></td>
                  <td>
                    <strong><?php echo productUsageText($row['product_name']); ?></strong><br>
                    <small>ID: <?php echo productUsageText($row['product_id']); ?> | <?php echo productUsageText($row['product_code']); ?> | <?php echo productUsageText($row['product_unit']); ?></small>
                  </td>
                  <td>
                    <?php echo productUsageText($row['reference_no']); ?>
                    <?php if (!empty($row['secondary_no'])) { ?><br><small><?php echo productUsageText($row['secondary_no']); ?></small><?php } ?>
                  </td>
                  <td><?php echo productUsageDate($row['record_date']); ?></td>
                  <td>
                    <?php echo productUsageText($row['project_name']); ?>
                    <?php if (!empty($row['store_name'])) { ?><br><small><?php echo productUsageText($row['store_name']); ?></small><?php } ?>
                  </td>
                  <td><?php echo productUsageText($row['employee_name']); ?></td>
                  <td><?php echo productUsageText($row['status_text']); ?></td>
                  <td>
                    Q1: <?php echo productUsageNumber($row['quantity_one']); ?><br>
                    Q2: <?php echo productUsageNumber($row['quantity_two']); ?><br>
                    Q3: <?php echo productUsageNumber($row['quantity_three']); ?>
                    <?php if ($row['amount_one'] !== '' || $row['amount_two'] !== '') { ?>
                      <br>Amount: <?php echo productUsageNumber($row['amount_one']); ?>
                      <?php if ($row['amount_two'] !== '') { ?><br>Rate/Final: <?php echo productUsageNumber($row['amount_two']); ?><?php } ?>
                    <?php } ?>
                  </td>
                  <td class="usage-note"><?php echo nl2br(productUsageText($row['note_text'])); ?></td>
                  <td>
                    <?php if ($link !== '#') { ?>
                      <a class="btn btn-primary btn-sm" href="<?php echo productUsageText($link); ?>" target="_blank"><i class="fas fa-eye"></i> View</a>
                    <?php } ?>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php } ?>
  <?php } else { ?>
    <div class="usage-empty">Search by product name or code to see requisition, distribution, purchase, stock, return, transfer and emergency usage.</div>
  <?php } ?>
</section>
