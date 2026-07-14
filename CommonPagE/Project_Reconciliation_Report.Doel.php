<?php
$reconProjectId = isset($_POST['project_id']) ? trim((string) $_POST['project_id']) : '';
$reconStoreId = isset($_POST['store_id']) ? trim((string) $_POST['store_id']) : '';
$reconProductId = isset($_POST['product_id']) ? trim((string) $_POST['product_id']) : '';
$reconStatus = isset($_POST['reconciliation_status']) ? trim((string) $_POST['reconciliation_status']) : 'all';
$reconFromDate = isset($_POST['from_date']) ? trim((string) $_POST['from_date']) : date('Y-m-01', strtotime($current_date));
$reconToDate = isset($_POST['to_date']) ? trim((string) $_POST['to_date']) : $current_date;
$reconNotice = '';

$reconValidStatuses = array('all', 'outstanding', 'balanced', 'over');
if (!in_array($reconStatus, $reconValidStatuses, true)) {
    $reconStatus = 'all';
}

$reconIsValidDate = static function ($date) {
    $parsedDate = DateTime::createFromFormat('!Y-m-d', $date);
    return $parsedDate !== false && $parsedDate->format('Y-m-d') === $date;
};

if (!$reconIsValidDate($reconFromDate)) {
    $reconFromDate = date('Y-m-01', strtotime($current_date));
    $reconNotice = 'Invalid start date was replaced with the first day of the current month.';
}
if (!$reconIsValidDate($reconToDate)) {
    $reconToDate = $current_date;
    $reconNotice = 'Invalid end date was replaced with today.';
}
if ($reconFromDate > $reconToDate) {
    $swapDate = $reconFromDate;
    $reconFromDate = $reconToDate;
    $reconToDate = $swapDate;
    $reconNotice = 'Start and end dates were reversed, so they have been swapped.';
}

function projectReconText($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function projectReconNumber($value)
{
    return is_numeric($value) ? (float) $value : 0.0;
}

function projectReconQuantity($value)
{
    return number_format(projectReconNumber($value), 2, '.', ',');
}

$projectOptions = $pdo->query("SELECT id, MAX(name) AS name, MAX(location) AS location FROM project_information WHERE deleted_at IS NULL GROUP BY id ORDER BY name ASC")->fetchAll();
$storeOptions = $pdo->query("SELECT id, MAX(name) AS name FROM store_information WHERE deleted_at IS NULL GROUP BY id ORDER BY name ASC")->fetchAll();
$productOptions = $pdo->query(
    "SELECT product_information.id, MAX(product_information.code) AS code, MAX(product_information.name) AS name, MAX(product_information.unit) AS unit
     FROM product_information
     INNER JOIN (
        SELECT product_id FROM distribution_history WHERE deleted_at IS NULL
        UNION
        SELECT product_id FROM return_history_detail WHERE deleted_at IS NULL
        UNION
        SELECT product_id FROM material_used_detail_history WHERE deleted_at IS NULL
        UNION
        SELECT product_id FROM emergency_request_detail
     ) movement_products ON movement_products.product_id = product_information.id
     WHERE product_information.deleted_at IS NULL
     GROUP BY product_information.id
     ORDER BY product_information.name ASC"
)->fetchAll();

$cleanDistributionDate = "REPLACE(REPLACE(REPLACE(TRIM(distribution_history.date), CHAR(9), ''), CHAR(10), ''), CHAR(13), '')";
$cleanReturnDate = "REPLACE(REPLACE(REPLACE(TRIM(return_history_detail.date), CHAR(9), ''), CHAR(10), ''), CHAR(13), '')";
$cleanUsedDate = "REPLACE(REPLACE(REPLACE(TRIM(material_used_detail_history.date), CHAR(9), ''), CHAR(10), ''), CHAR(13), '')";
$numericDistribution = "CASE WHEN TRIM(distribution_history.distribution_quantity) REGEXP '^-?[0-9]+([.][0-9]+)?$' THEN CAST(distribution_history.distribution_quantity AS DECIMAL(24,4)) ELSE 0 END";
$numericReturn = "CASE WHEN TRIM(return_history_detail.return_quantity) REGEXP '^-?[0-9]+([.][0-9]+)?$' THEN CAST(return_history_detail.return_quantity AS DECIMAL(24,4)) ELSE 0 END";
$numericReturnUsed = "CASE WHEN TRIM(return_history_detail.used_quantity) REGEXP '^-?[0-9]+([.][0-9]+)?$' THEN CAST(return_history_detail.used_quantity AS DECIMAL(24,4)) ELSE 0 END";
$numericDamage = "CASE WHEN TRIM(return_history_detail.damage_quantity) REGEXP '^-?[0-9]+([.][0-9]+)?$' THEN CAST(return_history_detail.damage_quantity AS DECIMAL(24,4)) ELSE 0 END";
$numericStandaloneUsed = "CASE WHEN TRIM(material_used_detail_history.used_quantity) REGEXP '^-?[0-9]+([.][0-9]+)?$' THEN CAST(material_used_detail_history.used_quantity AS DECIMAL(24,4)) ELSE 0 END";

$eventSql = "
    SELECT {$cleanDistributionDate} AS event_date,
           distribution_history.project_id,
           distribution_history.store_id,
           distribution_history.product_id,
           {$numericDistribution} AS distributed_quantity,
           0 AS emergency_issued_quantity,
           0 AS emergency_reconciled_quantity,
           0 AS used_quantity,
           0 AS return_quantity,
           0 AS damage_quantity
    FROM distribution_history
    WHERE distribution_history.deleted_at IS NULL

    UNION ALL

    SELECT DATE_FORMAT(emergency_request.date, '%Y-%m-%d') AS event_date,
           emergency_request.project_id,
           emergency_request.store_id,
           emergency_request_detail.product_id,
           0 AS distributed_quantity,
           emergency_request_detail.issued_quantity AS emergency_issued_quantity,
           0 AS emergency_reconciled_quantity,
           0 AS used_quantity,
           0 AS return_quantity,
           0 AS damage_quantity
    FROM emergency_request_detail
    INNER JOIN emergency_request ON emergency_request.id = emergency_request_detail.emergency_request_id
    WHERE emergency_request.deleted_at IS NULL

    UNION ALL

    SELECT DATE_FORMAT(emergency_request_reconciliation.created_at, '%Y-%m-%d') AS event_date,
           emergency_request.project_id,
           emergency_request.store_id,
           emergency_request_detail.product_id,
           0 AS distributed_quantity,
           0 AS emergency_issued_quantity,
           emergency_request_reconciliation.quantity AS emergency_reconciled_quantity,
           0 AS used_quantity,
           0 AS return_quantity,
           0 AS damage_quantity
    FROM emergency_request_reconciliation
    INNER JOIN emergency_request_detail ON emergency_request_detail.id = emergency_request_reconciliation.emergency_request_detail_id
    INNER JOIN emergency_request ON emergency_request.id = emergency_request_detail.emergency_request_id
    WHERE emergency_request.deleted_at IS NULL

    UNION ALL

    SELECT {$cleanReturnDate} AS event_date,
           return_history_detail.project_id,
           return_history_detail.store_id,
           return_history_detail.product_id,
           0 AS distributed_quantity,
           0 AS emergency_issued_quantity,
           0 AS emergency_reconciled_quantity,
           {$numericReturnUsed} AS used_quantity,
           {$numericReturn} AS return_quantity,
           {$numericDamage} AS damage_quantity
    FROM return_history_detail
    WHERE return_history_detail.deleted_at IS NULL

    UNION ALL

    SELECT {$cleanUsedDate} AS event_date,
           material_used_detail_history.project_id,
           NULL AS store_id,
           material_used_detail_history.product_id,
           0 AS distributed_quantity,
           0 AS emergency_issued_quantity,
           0 AS emergency_reconciled_quantity,
           {$numericStandaloneUsed} AS used_quantity,
           0 AS return_quantity,
           0 AS damage_quantity
    FROM material_used_detail_history
    WHERE material_used_detail_history.deleted_at IS NULL
";

$eventDateValue = "STR_TO_DATE(events.event_date, '%Y-%m-%d')";
$reconWhere = array(
    "events.event_date REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}$'",
    $eventDateValue . ' <= report_bounds.to_date',
);
$reconParameters = array(
    ':from_date' => $reconFromDate,
    ':to_date' => $reconToDate,
);

if ($reconProjectId !== '') {
    $reconWhere[] = 'events.project_id = :project_id';
    $reconParameters[':project_id'] = $reconProjectId;
}
if ($reconStoreId !== '') {
    $reconWhere[] = 'events.store_id = :store_id';
    $reconParameters[':store_id'] = $reconStoreId;
}
if ($reconProductId !== '') {
    $reconWhere[] = 'events.product_id = :product_id';
    $reconParameters[':product_id'] = $reconProductId;
}

$openingExpression = "SUM(CASE WHEN {$eventDateValue} < report_bounds.from_date THEN events.distributed_quantity + events.emergency_issued_quantity - events.used_quantity - events.return_quantity - events.damage_quantity ELSE 0 END)";
$closingExpression = "SUM(events.distributed_quantity + events.emergency_issued_quantity - events.used_quantity - events.return_quantity - events.damage_quantity)";
$periodActivityExpression = "SUM(CASE WHEN {$eventDateValue} >= report_bounds.from_date THEN ABS(events.distributed_quantity) + ABS(events.emergency_issued_quantity) + ABS(events.emergency_reconciled_quantity) + ABS(events.used_quantity) + ABS(events.return_quantity) + ABS(events.damage_quantity) ELSE 0 END)";

$reconHaving = "(ABS(opening_balance) > 0.0001 OR period_activity > 0.0001)";
if ($reconStatus === 'outstanding') {
    $reconHaving .= ' AND closing_outstanding > 0.0001';
} elseif ($reconStatus === 'balanced') {
    $reconHaving .= ' AND ABS(closing_outstanding) <= 0.0001';
} elseif ($reconStatus === 'over') {
    $reconHaving .= ' AND closing_outstanding < -0.0001';
}

$reconSql = "
    SELECT
        events.project_id,
        events.product_id,
        COALESCE(project_information.name, CONCAT('Project #', events.project_id)) AS project_name,
        COALESCE(project_information.location, '') AS project_location,
        COALESCE(product_information.code, '') AS product_code,
        COALESCE(product_information.name, CONCAT('Product #', events.product_id)) AS product_name,
        COALESCE(product_information.unit, '') AS unit,
        COALESCE(GROUP_CONCAT(DISTINCT store_information.name ORDER BY store_information.name SEPARATOR ', '), 'Project level') AS store_names,
        {$openingExpression} AS opening_balance,
        SUM(CASE WHEN {$eventDateValue} >= report_bounds.from_date THEN events.distributed_quantity ELSE 0 END) AS distributed_quantity,
        SUM(CASE WHEN {$eventDateValue} >= report_bounds.from_date THEN events.emergency_issued_quantity ELSE 0 END) AS emergency_issued_quantity,
        SUM(CASE WHEN {$eventDateValue} >= report_bounds.from_date THEN events.emergency_reconciled_quantity ELSE 0 END) AS emergency_reconciled_quantity,
        SUM(CASE WHEN {$eventDateValue} >= report_bounds.from_date THEN events.used_quantity ELSE 0 END) AS used_quantity,
        SUM(CASE WHEN {$eventDateValue} >= report_bounds.from_date THEN events.return_quantity ELSE 0 END) AS return_quantity,
        SUM(CASE WHEN {$eventDateValue} >= report_bounds.from_date THEN events.damage_quantity ELSE 0 END) AS damage_quantity,
        SUM(events.emergency_issued_quantity - events.emergency_reconciled_quantity) AS emergency_pending_quantity,
        {$closingExpression} AS closing_outstanding,
        {$periodActivityExpression} AS period_activity
    FROM ({$eventSql}) events
    CROSS JOIN (SELECT CAST(:from_date AS DATE) AS from_date, CAST(:to_date AS DATE) AS to_date) report_bounds
    LEFT JOIN (
        SELECT id, MAX(name) AS name, MAX(location) AS location
        FROM project_information
        GROUP BY id
    ) project_information ON project_information.id = events.project_id
    LEFT JOIN (
        SELECT id, MAX(code) AS code, MAX(name) AS name, MAX(unit) AS unit
        FROM product_information
        GROUP BY id
    ) product_information ON product_information.id = events.product_id
    LEFT JOIN (
        SELECT id, MAX(name) AS name
        FROM store_information
        GROUP BY id
    ) store_information ON store_information.id = events.store_id
    WHERE " . implode(' AND ', $reconWhere) . "
    GROUP BY events.project_id, events.product_id, project_information.name, project_information.location, product_information.code, product_information.name, product_information.unit
    HAVING {$reconHaving}
    ORDER BY project_name ASC, product_name ASC
";

$reconStatement = $pdo->prepare($reconSql);
$reconStatement->execute($reconParameters);
$reconRows = $reconStatement->fetchAll();

$reconTotals = array(
    'opening_balance' => 0.0,
    'distributed_quantity' => 0.0,
    'emergency_issued_quantity' => 0.0,
    'emergency_reconciled_quantity' => 0.0,
    'used_quantity' => 0.0,
    'return_quantity' => 0.0,
    'damage_quantity' => 0.0,
    'emergency_pending_quantity' => 0.0,
    'closing_outstanding' => 0.0,
);
foreach ($reconRows as $reconRow) {
    foreach ($reconTotals as $totalKey => $totalValue) {
        $reconTotals[$totalKey] += projectReconNumber($reconRow[$totalKey]);
    }
}
?>

<section class="content project-reconciliation-report">
  <div class="card recon-filter-card">
    <div class="card-header">
      <h3 class="card-title">Project Material Reconciliation</h3>
      <div class="card-tools">
        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse"><i class="fas fa-minus"></i></button>
      </div>
    </div>
    <div class="card-body">
      <?php if ($reconNotice !== '') { ?>
        <div class="alert alert-warning py-2"><?php echo projectReconText($reconNotice); ?></div>
      <?php } ?>
      <?php if ($reconStoreId !== '') { ?>
        <div class="alert alert-info py-2">
          <i class="fas fa-info-circle mr-1"></i>
          Store filter is active. Project-level Material Used entries have no store reference and are excluded from this filtered result.
        </div>
      <?php } ?>

      <form method="post" action="?<?php echo projectReconText($page_title); ?>/<?php echo projectReconText($MenuName); ?>">
        <div class="row">
          <div class="col-lg-3 col-md-6">
            <div class="form-group">
              <label for="recon_project_id">Project Name</label>
              <select class="form-control select2" id="recon_project_id" name="project_id" style="width:100%;">
                <option value="">All Projects</option>
                <?php foreach ($projectOptions as $projectOption) {
                    $projectLabel = trim((string) $projectOption['name']);
                    if (!empty($projectOption['location'])) {
                        $projectLabel .= ' - ' . trim((string) $projectOption['location']);
                    }
                ?>
                  <option value="<?php echo projectReconText($projectOption['id']); ?>" <?php echo (string) $projectOption['id'] === $reconProjectId ? 'selected' : ''; ?>><?php echo projectReconText($projectLabel); ?></option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="col-lg-3 col-md-6">
            <div class="form-group">
              <label for="recon_store_id">Store Name</label>
              <select class="form-control select2" id="recon_store_id" name="store_id" style="width:100%;">
                <option value="">All Stores</option>
                <?php foreach ($storeOptions as $storeOption) { ?>
                  <option value="<?php echo projectReconText($storeOption['id']); ?>" <?php echo (string) $storeOption['id'] === $reconStoreId ? 'selected' : ''; ?>><?php echo projectReconText($storeOption['name']); ?></option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="col-lg-3 col-md-6">
            <div class="form-group">
              <label for="recon_product_id">Product Name / Code</label>
              <select class="form-control select2" id="recon_product_id" name="product_id" style="width:100%;">
                <option value="">All Products</option>
                <?php foreach ($productOptions as $productOption) {
                    $productLabel = !empty($productOption['code']) ? $productOption['code'] . ' - ' . $productOption['name'] : $productOption['name'];
                ?>
                  <option value="<?php echo projectReconText($productOption['id']); ?>" <?php echo (string) $productOption['id'] === $reconProductId ? 'selected' : ''; ?>><?php echo projectReconText($productLabel); ?></option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="col-lg-3 col-md-6">
            <div class="form-group">
              <label for="reconciliation_status">Reconciliation Status</label>
              <select class="form-control" id="reconciliation_status" name="reconciliation_status">
                <option value="all" <?php echo $reconStatus === 'all' ? 'selected' : ''; ?>>All Statuses</option>
                <option value="outstanding" <?php echo $reconStatus === 'outstanding' ? 'selected' : ''; ?>>Outstanding</option>
                <option value="balanced" <?php echo $reconStatus === 'balanced' ? 'selected' : ''; ?>>Balanced</option>
                <option value="over" <?php echo $reconStatus === 'over' ? 'selected' : ''; ?>>Over Reconciled</option>
              </select>
            </div>
          </div>

          <div class="col-lg-3 col-md-6">
            <div class="form-group">
              <label for="recon_from_date">Period Start Date</label>
              <input type="date" class="form-control" id="recon_from_date" name="from_date" value="<?php echo projectReconText($reconFromDate); ?>" required>
            </div>
          </div>

          <div class="col-lg-3 col-md-6">
            <div class="form-group">
              <label for="recon_to_date">Period End Date</label>
              <input type="date" class="form-control" id="recon_to_date" name="to_date" value="<?php echo projectReconText($reconToDate); ?>" required>
            </div>
          </div>

          <div class="col-lg-3 col-md-6 d-flex align-items-end">
            <div class="form-group w-100">
              <button type="submit" name="view" value="1" class="btn btn-primary btn-block"><i class="fas fa-search mr-1"></i> Generate Report</button>
            </div>
          </div>

          <div class="col-lg-3 col-md-6 d-flex align-items-end">
            <div class="form-group w-100">
              <a href="?Project_Reconciliation_Report/Report" class="btn btn-outline-secondary btn-block"><i class="fas fa-undo mr-1"></i> Reset Filters</a>
            </div>
          </div>
        </div>
      </form>

      <div class="callout callout-info mt-2 mb-0">
        <div><strong>Opening Balance:</strong> Unreconciled quantity accumulated before the Period Start Date.</div>
        <div><strong>Closing Outstanding:</strong> Opening + Distributed + Emergency Issued - Used - Returned - Damaged.</div>
        <div><strong>Emergency Pending:</strong> Total Emergency Issued - Total Emergency Reconciled, calculated up to the Period End Date.</div>
        <small class="text-muted">All values are quantities in each product's own unit. Totals containing mixed units (for example, Nos + Kg) are for reference only.</small>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-3 col-6">
      <div class="info-box mb-3">
        <span class="info-box-icon bg-info"><i class="fas fa-truck-loading"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Distributed Qty (In Period)</span>
          <span class="info-box-number"><?php echo projectReconQuantity($reconTotals['distributed_quantity']); ?></span>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="info-box mb-3">
        <span class="info-box-icon bg-warning"><i class="fas fa-bolt"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Emergency Issued Qty (In Period)</span>
          <span class="info-box-number"><?php echo projectReconQuantity($reconTotals['emergency_issued_quantity']); ?></span>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="info-box mb-3">
        <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Used + Returned Qty (In Period)</span>
          <span class="info-box-number"><?php echo projectReconQuantity($reconTotals['used_quantity'] + $reconTotals['return_quantity']); ?></span>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="info-box mb-3">
        <span class="info-box-icon bg-secondary"><i class="fas fa-balance-scale"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Closing Outstanding Qty (To Date)</span>
          <span class="info-box-number"><?php echo projectReconQuantity($reconTotals['closing_outstanding']); ?></span>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Reconciliation from <?php echo projectReconText(date('d-m-Y', strtotime($reconFromDate))); ?> to <?php echo projectReconText(date('d-m-Y', strtotime($reconToDate))); ?></h3>
      <div class="card-tools"><span class="badge badge-light"><?php echo count($reconRows); ?> product rows</span></div>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover mb-0" id="example1">
          <thead>
            <tr>
              <th>SL</th>
              <th>Project Name</th>
              <th>Related Store(s)</th>
              <th>Product Code</th>
              <th>Product Name</th>
              <th>Unit</th>
              <th>Opening Qty (Before Period)</th>
              <th>Distributed Qty (In Period)</th>
              <th>Emergency Issued Qty (In Period)</th>
              <th>Emergency Reconciled Qty (In Period)</th>
              <th>Used Qty (In Period)</th>
              <th>Returned Qty (In Period)</th>
              <th>Damaged Qty (In Period)</th>
              <th>Emergency Pending Qty (To Date)</th>
              <th>Closing Outstanding Qty (To Date)</th>
              <th>Reconciliation Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($reconRows as $rowIndex => $reconRow) {
                $closingOutstanding = projectReconNumber($reconRow['closing_outstanding']);
                if ($closingOutstanding > 0.0001) {
                    $statusLabel = 'Outstanding';
                    $statusClass = 'badge-warning';
                    $quantityClass = 'text-warning font-weight-bold';
                } elseif ($closingOutstanding < -0.0001) {
                    $statusLabel = 'Over Reconciled';
                    $statusClass = 'badge-danger';
                    $quantityClass = 'text-danger font-weight-bold';
                } else {
                    $statusLabel = 'Balanced';
                    $statusClass = 'badge-success';
                    $quantityClass = 'text-success font-weight-bold';
                }
                $projectDisplay = $reconRow['project_name'];
                if (!empty($reconRow['project_location'])) {
                    $projectDisplay .= ' - ' . $reconRow['project_location'];
                }
            ?>
              <tr data-product-ids="<?php echo projectReconText($reconRow['product_id']); ?>">
                <td><?php echo $rowIndex + 1; ?></td>
                <td><?php echo projectReconText($projectDisplay); ?></td>
                <td><?php echo projectReconText($reconRow['store_names']); ?></td>
                <td><?php echo projectReconText($reconRow['product_code']); ?></td>
                <td><?php echo projectReconText($reconRow['product_name']); ?></td>
                <td><?php echo projectReconText($reconRow['unit']); ?></td>
                <td><?php echo projectReconQuantity($reconRow['opening_balance']); ?></td>
                <td><?php echo projectReconQuantity($reconRow['distributed_quantity']); ?></td>
                <td><?php echo projectReconQuantity($reconRow['emergency_issued_quantity']); ?></td>
                <td><?php echo projectReconQuantity($reconRow['emergency_reconciled_quantity']); ?></td>
                <td><?php echo projectReconQuantity($reconRow['used_quantity']); ?></td>
                <td><?php echo projectReconQuantity($reconRow['return_quantity']); ?></td>
                <td><?php echo projectReconQuantity($reconRow['damage_quantity']); ?></td>
                <td><?php echo projectReconQuantity($reconRow['emergency_pending_quantity']); ?></td>
                <td class="<?php echo $quantityClass; ?>"><?php echo projectReconQuantity($closingOutstanding); ?></td>
                <td><span class="badge badge-pill <?php echo $statusClass; ?> px-2 py-1"><?php echo $statusLabel; ?></span></td>
              </tr>
            <?php } ?>
          </tbody>
          <tfoot>
            <tr>
              <th colspan="6" class="text-right">Quantity Totals (Mixed Units)</th>
              <th><?php echo projectReconQuantity($reconTotals['opening_balance']); ?></th>
              <th><?php echo projectReconQuantity($reconTotals['distributed_quantity']); ?></th>
              <th><?php echo projectReconQuantity($reconTotals['emergency_issued_quantity']); ?></th>
              <th><?php echo projectReconQuantity($reconTotals['emergency_reconciled_quantity']); ?></th>
              <th><?php echo projectReconQuantity($reconTotals['used_quantity']); ?></th>
              <th><?php echo projectReconQuantity($reconTotals['return_quantity']); ?></th>
              <th><?php echo projectReconQuantity($reconTotals['damage_quantity']); ?></th>
              <th><?php echo projectReconQuantity($reconTotals['emergency_pending_quantity']); ?></th>
              <th><?php echo projectReconQuantity($reconTotals['closing_outstanding']); ?></th>
              <th></th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</section>
