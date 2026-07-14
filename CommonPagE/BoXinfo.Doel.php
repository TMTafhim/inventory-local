<?php
$dashboard_user_id = !empty($_SESSION['LoginReGiSterSession']) ? $_SESSION['LoginReGiSterSession'] : '';
$dashboard_is_admin = (!empty($_SESSION['USER_TYPE']) && $_SESSION['USER_TYPE'] == 'Admin');
$dashboard_store_id = !empty($login_user_store_id) ? $login_user_store_id : '';
$dashboard_menu_access = !empty($menu_access) ? $menu_access : [];

function dashboardCount($pdo, $sql) {
    try {
        $statement = $pdo->query($sql);
        return $statement ? (int)$statement->fetchColumn() : 0;
    } catch (Throwable $e) {
        return 0;
    }
}

function dashboardRows($pdo, $sql) {
    try {
        $statement = $pdo->query($sql);
        return $statement ? $statement->fetchAll() : [];
    } catch (Throwable $e) {
        return [];
    }
}

function dashboardAmount($pdo, $sql) {
    try {
        $statement = $pdo->query($sql);
        return $statement ? (float)$statement->fetchColumn() : 0;
    } catch (Throwable $e) {
        return 0;
    }
}

function dashboardDate($value) {
    if (empty($value) || $value == '0000-00-00') {
        return '-';
    }
    return date('d M Y', strtotime($value));
}

function dashboardMoney($value) {
    return number_format((float)$value, 2);
}

function dashboardCanAccess($menu_access, $name) {
    return (!empty($menu_access) && in_array($name, $menu_access));
}

function dashboardStatusClass($value) {
    return !empty($value) ? strtolower((string)$value) : 'pending';
}

function dashboardJson($value) {
    return json_encode($value, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
}

$requestion_filter = $dashboard_is_admin ? "1=1" : "requestion_histiory.employee_id='".$dashboard_user_id."'";
$draft_filter = $dashboard_is_admin ? "1=1" : "requestion_draft_histiory.employee_id='".$dashboard_user_id."'";
$distribution_filter = $dashboard_is_admin ? "1=1" : "distribution_summary.created_by='".$dashboard_user_id."'";
$receive_filter = $dashboard_is_admin ? "1=1" : "distribution_summary.assign_receiver_id='".$dashboard_user_id."'";
$purchase_filter = $dashboard_is_admin ? "1=1" : "purchase_history.created_by='".$dashboard_user_id."'";

$total_requisition = dashboardCount($pdo, "SELECT COUNT(id) FROM requestion_histiory WHERE deleted_at IS NULL AND $requestion_filter");
$draft_requisition = dashboardCount($pdo, "SELECT COUNT(id) FROM requestion_draft_histiory WHERE deleted_at IS NULL AND final_submit_status IS NULL AND $draft_filter");
$pending_approval = dashboardCount($pdo, "SELECT COUNT(id) FROM requestion_histiory WHERE deleted_at IS NULL AND approval_status='Pending' AND $requestion_filter");
$approved_requisition = dashboardCount($pdo, "SELECT COUNT(id) FROM requestion_histiory WHERE deleted_at IS NULL AND approval_status='Approve' AND $requestion_filter");
$distribution_pending = dashboardCount($pdo, "SELECT COUNT(id) FROM requestion_histiory WHERE deleted_at IS NULL AND approval_status='Approve' AND (distribution_status IS NULL OR distribution_status='Pending')".($dashboard_is_admin ? "" : " AND store_id='".$dashboard_store_id."'"));
$distribution_done = dashboardCount($pdo, "SELECT COUNT(id) FROM distribution_summary WHERE deleted_at IS NULL AND $distribution_filter");
$received_pending = dashboardCount($pdo, "SELECT COUNT(id) FROM distribution_summary WHERE deleted_at IS NULL AND (received_status IS NULL OR received_status='Pending' OR received_status='Partial') AND $receive_filter");
$purchase_total = dashboardCount($pdo, "SELECT COUNT(id) FROM purchase_history WHERE deleted_at IS NULL AND $purchase_filter");
$purchase_amount = dashboardAmount($pdo, "SELECT COALESCE(SUM(billamount),0) FROM purchase_history WHERE deleted_at IS NULL AND $purchase_filter");
$stock_products = dashboardCount($pdo, "SELECT COUNT(id) FROM stock_information WHERE deleted_at IS NULL");
$stock_low = dashboardCount($pdo, "SELECT COUNT(id) FROM stock_information WHERE deleted_at IS NULL AND stock <= 5");

$my_pending_task = dashboardCount($pdo, "SELECT COUNT(project_material_aproval_status.id) FROM project_material_aproval_status INNER JOIN requestion_histiory ON project_material_aproval_status.invoice_id=requestion_histiory.invoice_id WHERE project_material_aproval_status.approval_status='Pending' AND project_material_aproval_status.employee_id='".$dashboard_user_id."' AND project_material_aproval_status.deleted_at IS NULL AND requestion_histiory.deleted_at IS NULL");
$dynamic_pending_task = dashboardCount($pdo, "SELECT COUNT(approval_instance_steps.id) FROM approval_instance_steps INNER JOIN approval_instances ON approval_instance_steps.approval_instance_id=approval_instances.id WHERE approval_instance_steps.status='Pending' AND approval_instance_steps.approver_employee_id='".$dashboard_user_id."' AND approval_instance_steps.deleted_at IS NULL AND approval_instances.deleted_at IS NULL");
$my_pending_task = max($my_pending_task, $dynamic_pending_task);

$recent_requisitions = dashboardRows($pdo, "
    SELECT requestion_histiory.invoice_id, requestion_histiory.date, requestion_histiory.approval_status, requestion_histiory.distribution_status,
           project_information.name AS project_name, store_information.name AS store_name
    FROM requestion_histiory
    LEFT JOIN project_information ON requestion_histiory.project_id=project_information.id
    LEFT JOIN store_information ON requestion_histiory.store_id=store_information.id
    WHERE requestion_histiory.deleted_at IS NULL AND $requestion_filter
    ORDER BY requestion_histiory.id DESC
    LIMIT 6
");

$recent_distributions = dashboardRows($pdo, "
    SELECT distribution_summary.invoice_id, distribution_summary.distribution_id, distribution_summary.date, distribution_summary.received_status,
           project_information.name AS project_name, store_information.name AS store_name
    FROM distribution_summary
    LEFT JOIN project_information ON distribution_summary.project_id=project_information.id
    LEFT JOIN store_information ON distribution_summary.store_id=store_information.id
    WHERE distribution_summary.deleted_at IS NULL AND $distribution_filter
    ORDER BY distribution_summary.id DESC
    LIMIT 6
");

$monthly_requisitions = dashboardRows($pdo, "
    SELECT DATE_FORMAT(requestion_histiory.date, '%b %y') AS label,
           COUNT(requestion_histiory.id) AS total,
           SUM(CASE WHEN requestion_histiory.approval_status='Approve' THEN 1 ELSE 0 END) AS approved
    FROM requestion_histiory
    WHERE requestion_histiory.deleted_at IS NULL
      AND requestion_histiory.date >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
      AND $requestion_filter
    GROUP BY YEAR(requestion_histiory.date), MONTH(requestion_histiory.date)
    ORDER BY YEAR(requestion_histiory.date), MONTH(requestion_histiory.date)
");

$monthly_purchases = dashboardRows($pdo, "
    SELECT DATE_FORMAT(purchase_history.date, '%b %y') AS label,
           COALESCE(SUM(purchase_history.billamount),0) AS total
    FROM purchase_history
    WHERE purchase_history.deleted_at IS NULL
      AND purchase_history.date >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
      AND $purchase_filter
    GROUP BY YEAR(purchase_history.date), MONTH(purchase_history.date)
    ORDER BY YEAR(purchase_history.date), MONTH(purchase_history.date)
");

$store_distribution_rows = dashboardRows($pdo, "
    SELECT store_information.name AS label, COUNT(distribution_summary.id) AS total
    FROM distribution_summary
    LEFT JOIN store_information ON distribution_summary.store_id=store_information.id
    WHERE distribution_summary.deleted_at IS NULL AND $distribution_filter
    GROUP BY distribution_summary.store_id, store_information.name
    ORDER BY total DESC
    LIMIT 6
");

$top_project_rows = dashboardRows($pdo, "
    SELECT project_information.name AS label, COUNT(requestion_histiory.id) AS total
    FROM requestion_histiory
    LEFT JOIN project_information ON requestion_histiory.project_id=project_information.id
    WHERE requestion_histiory.deleted_at IS NULL AND $requestion_filter
    GROUP BY requestion_histiory.project_id, project_information.name
    ORDER BY total DESC
    LIMIT 6
");

$stock_store_filter = $dashboard_is_admin ? "1=1" : "stock_information.store_id='".$dashboard_store_id."'";
$stock_risk_rows = dashboardRows($pdo, "
    SELECT product_information.name AS label, COALESCE(SUM(stock_information.stock),0) AS total
    FROM stock_information
    LEFT JOIN product_information ON stock_information.product_id=product_information.id
    WHERE stock_information.deleted_at IS NULL
      AND product_information.deleted_at IS NULL
      AND $stock_store_filter
    GROUP BY stock_information.product_id, product_information.name
    ORDER BY total ASC
    LIMIT 7
");

$approval_rate = $total_requisition > 0 ? round(($approved_requisition / $total_requisition) * 100) : 0;
$distribution_rate = ($distribution_done + $distribution_pending) > 0 ? round(($distribution_done / ($distribution_done + $distribution_pending)) * 100) : 0;
$stock_risk_rate = $stock_products > 0 ? round(($stock_low / $stock_products) * 100) : 0;

$requisition_chart_labels = array_column($monthly_requisitions, 'label');
$requisition_chart_total = array_map('intval', array_column($monthly_requisitions, 'total'));
$requisition_chart_approved = array_map('intval', array_column($monthly_requisitions, 'approved'));
$purchase_chart_labels = array_column($monthly_purchases, 'label');
$purchase_chart_total = array_map('floatval', array_column($monthly_purchases, 'total'));
$store_chart_labels = array_map(function ($row) { return !empty($row['label']) ? $row['label'] : 'Unknown Store'; }, $store_distribution_rows);
$store_chart_total = array_map('intval', array_column($store_distribution_rows, 'total'));
$project_chart_labels = array_map(function ($row) { return !empty($row['label']) ? $row['label'] : 'Unassigned Project'; }, $top_project_rows);
$project_chart_total = array_map('intval', array_column($top_project_rows, 'total'));
$stock_chart_labels = array_map(function ($row) { return !empty($row['label']) ? $row['label'] : 'Unknown Product'; }, $stock_risk_rows);
$stock_chart_total = array_map('floatval', array_column($stock_risk_rows, 'total'));
$status_chart_labels = ['Draft', 'Pending Approval', 'Approved', 'Distribution Pending', 'Distributed', 'Receive Pending'];
$status_chart_total = [$draft_requisition, $pending_approval, $approved_requisition, $distribution_pending, $distribution_done, $received_pending];

$quick_actions = [
    ['label' => 'New Material Draft', 'href' => '?Requisition_Draft_Create/Requisition', 'icon' => 'fa-file-medical', 'show' => dashboardCanAccess($dashboard_menu_access, 'Requisition Draft')],
    ['label' => 'Distribution Pending', 'href' => '?Distribution_Pending', 'icon' => 'fa-truck-loading', 'show' => dashboardCanAccess($dashboard_menu_access, 'Distribution Pending')],
    ['label' => 'Receive Material', 'href' => '?Material_Received_Status', 'icon' => 'fa-clipboard-check', 'show' => dashboardCanAccess($dashboard_menu_access, 'Material Received Status')],
    ['label' => 'Product Lifecycle', 'href' => '?Product_Lifecycle_Report/Report', 'icon' => 'fa-chart-line', 'show' => (!empty($_SESSION['USER_TYPE']) && $_SESSION['USER_TYPE'] == 'Admin')],
];
?>

<style>
.dashboard-shell {
  padding: 0 8px 22px;
}
.dashboard-hero {
  display: grid;
  grid-template-columns: minmax(0, 1fr) auto;
  gap: 18px;
  align-items: center;
  background: #ffffff;
  border: 1px solid #e3e9f1;
  border-radius: 8px;
  padding: 22px;
  box-shadow: 0 12px 28px rgba(15, 23, 42, .06);
  margin-bottom: 18px;
}
.dashboard-hero h2 {
  margin: 0 0 6px;
  color: #172033;
  font-size: 24px;
  font-weight: 800;
  letter-spacing: 0;
}
.dashboard-hero p {
  margin: 0;
  color: #637083;
  font-size: 14px;
}
.dashboard-hero-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  justify-content: flex-end;
}
.dashboard-action {
  min-height: 38px;
  border-radius: 8px;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 9px 13px;
  background: #f7fafc;
  border: 1px solid #dce5ef;
  color: #263747;
  font-weight: 700;
  text-decoration: none;
}
.dashboard-action:hover {
  background: #eef6f8;
  color: #0f766e;
  text-decoration: none;
}
.dashboard-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(180px, 1fr));
  gap: 14px;
  margin-bottom: 18px;
}
.dashboard-card {
  background: #ffffff;
  border: 1px solid #e3e9f1;
  border-radius: 8px;
  padding: 16px;
  min-height: 132px;
  box-shadow: 0 10px 24px rgba(15, 23, 42, .05);
  position: relative;
  overflow: hidden;
}
.dashboard-card:before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: var(--card-accent, #2563eb);
}
.dashboard-card-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 12px;
}
.dashboard-card-title {
  color: #64748b;
  font-size: 13px;
  font-weight: 800;
  margin: 0;
}
.dashboard-card-icon {
  width: 38px;
  height: 38px;
  border-radius: 8px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: color-mix(in srgb, var(--card-accent, #2563eb) 12%, white);
  color: var(--card-accent, #2563eb);
}
.dashboard-card-value {
  color: #162033;
  font-size: 30px;
  font-weight: 800;
  line-height: 1;
  margin-bottom: 8px;
}
.dashboard-card-foot {
  color: #64748b;
  font-size: 13px;
  margin: 0;
}
.dashboard-metrics {
  display: grid;
  grid-template-columns: repeat(3, minmax(220px, 1fr));
  gap: 14px;
  margin-bottom: 18px;
}
.dashboard-metric {
  background: #ffffff;
  border: 1px solid #e3e9f1;
  border-radius: 8px;
  padding: 15px 16px;
  box-shadow: 0 10px 24px rgba(15, 23, 42, .05);
}
.dashboard-metric-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 10px;
}
.dashboard-metric-label {
  margin: 0;
  color: #52616f;
  font-size: 13px;
  font-weight: 800;
}
.dashboard-metric-value {
  color: #172033;
  font-size: 24px;
  font-weight: 900;
}
.dashboard-progress {
  height: 8px;
  border-radius: 8px;
  overflow: hidden;
  background: #edf2f7;
}
.dashboard-progress span {
  display: block;
  height: 100%;
  border-radius: inherit;
  background: var(--metric-accent, #2563eb);
}
.dashboard-progress-note {
  margin: 9px 0 0;
  color: #728197;
  font-size: 12px;
  font-weight: 600;
}
.dashboard-chart-grid {
  display: grid;
  grid-template-columns: minmax(0, 1.35fr) minmax(330px, .65fr);
  gap: 16px;
  margin-bottom: 16px;
}
.dashboard-chart-grid.compact {
  grid-template-columns: repeat(3, minmax(0, 1fr));
}
.dashboard-chart-body {
  position: relative;
  height: 310px;
  padding: 15px 16px 18px;
}
.dashboard-chart-body.compact {
  height: 250px;
}
.dashboard-chart-body canvas {
  width: 100% !important;
  height: 100% !important;
}
.dashboard-panels {
  display: grid;
  grid-template-columns: minmax(0, 1.15fr) minmax(320px, .85fr);
  gap: 16px;
}
.dashboard-panel {
  background: #ffffff;
  border: 1px solid #e3e9f1;
  border-radius: 8px;
  box-shadow: 0 10px 24px rgba(15, 23, 42, .05);
  overflow: hidden;
}
.dashboard-panel-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  padding: 15px 16px;
  border-bottom: 1px solid #edf2f7;
}
.dashboard-panel-header h3 {
  margin: 0;
  color: #172033;
  font-size: 16px;
  font-weight: 800;
}
.dashboard-panel-header a {
  color: #2563eb;
  font-weight: 700;
  font-size: 13px;
}
.dashboard-table-wrap {
  overflow-x: auto;
}
.dashboard-table {
  width: 100%;
  border-collapse: collapse;
}
.dashboard-table th {
  background: #f8fafc;
  color: #52616f;
  font-size: 12px;
  font-weight: 800;
  padding: 11px 14px;
  border-bottom: 1px solid #edf2f7;
  white-space: nowrap;
}
.dashboard-table td {
  color: #263747;
  font-size: 13px;
  padding: 12px 14px;
  border-bottom: 1px solid #edf2f7;
  vertical-align: middle;
}
.dashboard-table tr:last-child td {
  border-bottom: 0;
}
.dashboard-table a {
  color: #1d4ed8;
  font-weight: 800;
}
.dashboard-muted {
  color: #728197;
}
.dashboard-status {
  display: inline-flex;
  align-items: center;
  min-height: 26px;
  border-radius: 8px;
  padding: 5px 9px;
  font-size: 12px;
  font-weight: 800;
  background: #edf2f7;
  color: #334155;
}
.dashboard-status.pending {
  background: #fff7e6;
  color: #a16207;
}
.dashboard-status.approved,
.dashboard-status.approve,
.dashboard-status.complete {
  background: #eaf8f0;
  color: #15803d;
}
.dashboard-status.partial {
  background: #eef6ff;
  color: #1d4ed8;
}
.dashboard-empty {
  padding: 28px 18px;
  text-align: center;
  color: #728197;
}
.dashboard-list {
  padding: 8px 12px 14px;
}
.dashboard-list-item {
  display: grid;
  grid-template-columns: 38px minmax(0, 1fr) auto;
  gap: 10px;
  align-items: center;
  padding: 12px 4px;
  border-bottom: 1px solid #edf2f7;
}
.dashboard-list-item:last-child {
  border-bottom: 0;
}
.dashboard-list-icon {
  width: 38px;
  height: 38px;
  border-radius: 8px;
  background: #eef6f8;
  color: #0f766e;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
.dashboard-list-title {
  margin: 0 0 3px;
  color: #172033;
  font-size: 13px;
  font-weight: 800;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.dashboard-list-sub {
  margin: 0;
  color: #728197;
  font-size: 12px;
}
@media screen and (max-width: 1199.98px) {
  .dashboard-grid {
    grid-template-columns: repeat(2, minmax(180px, 1fr));
  }
  .dashboard-metrics,
  .dashboard-chart-grid,
  .dashboard-chart-grid.compact {
    grid-template-columns: 1fr;
  }
  .dashboard-panels {
    grid-template-columns: 1fr;
  }
}
@media screen and (max-width: 767.98px) {
  .dashboard-shell {
    padding: 0 0 18px;
  }
  .dashboard-hero {
    grid-template-columns: 1fr;
    padding: 17px;
  }
  .dashboard-hero-actions {
    justify-content: stretch;
  }
  .dashboard-action {
    flex: 1 1 100%;
    justify-content: center;
  }
  .dashboard-grid {
    grid-template-columns: 1fr;
  }
  .dashboard-metrics {
    grid-template-columns: 1fr;
  }
  .dashboard-card {
    min-height: 112px;
  }
  .dashboard-chart-body,
  .dashboard-chart-body.compact {
    height: 260px;
  }
  .dashboard-table {
    min-width: 720px;
  }
}
</style>

<section class="content dashboard-shell">
  <div class="container-fluid">
    <div class="dashboard-hero">
      <div>
        <h2>Operations Overview</h2>
        <p>Live summary for requisition, approval, distribution, receiving, purchase, and stock activity.</p>
      </div>
      <div class="dashboard-hero-actions">
        <?php foreach ($quick_actions as $action) { if (!empty($action['show'])) { ?>
          <a class="dashboard-action" href="<?php echo $action['href']; ?>">
            <i class="fas <?php echo $action['icon']; ?>"></i>
            <?php echo $action['label']; ?>
          </a>
        <?php } } ?>
      </div>
    </div>

    <div class="dashboard-grid">
      <div class="dashboard-card" style="--card-accent:#2563eb;">
        <div class="dashboard-card-head">
          <p class="dashboard-card-title">Total Requisition</p>
          <span class="dashboard-card-icon"><i class="fas fa-file-invoice"></i></span>
        </div>
        <div class="dashboard-card-value"><?php echo $total_requisition; ?></div>
        <p class="dashboard-card-foot"><?php echo $draft_requisition; ?> draft waiting to submit</p>
      </div>

      <div class="dashboard-card" style="--card-accent:#f59e0b;">
        <div class="dashboard-card-head">
          <p class="dashboard-card-title">Approval Work</p>
          <span class="dashboard-card-icon"><i class="fas fa-user-check"></i></span>
        </div>
        <div class="dashboard-card-value"><?php echo $my_pending_task; ?></div>
        <p class="dashboard-card-foot"><?php echo $pending_approval; ?> requisition pending approval</p>
      </div>

      <div class="dashboard-card" style="--card-accent:#0f9f6e;">
        <div class="dashboard-card-head">
          <p class="dashboard-card-title">Distribution</p>
          <span class="dashboard-card-icon"><i class="fas fa-truck"></i></span>
        </div>
        <div class="dashboard-card-value"><?php echo $distribution_done; ?></div>
        <p class="dashboard-card-foot"><?php echo $distribution_pending; ?> approved requisition waiting</p>
      </div>

      <div class="dashboard-card" style="--card-accent:#dc2626;">
        <div class="dashboard-card-head">
          <p class="dashboard-card-title">Receive Pending</p>
          <span class="dashboard-card-icon"><i class="fas fa-clipboard-list"></i></span>
        </div>
        <div class="dashboard-card-value"><?php echo $received_pending; ?></div>
        <p class="dashboard-card-foot">Partial and pending project receive</p>
      </div>

      <div class="dashboard-card" style="--card-accent:#169b9b;">
        <div class="dashboard-card-head">
          <p class="dashboard-card-title">Approved</p>
          <span class="dashboard-card-icon"><i class="fas fa-check-circle"></i></span>
        </div>
        <div class="dashboard-card-value"><?php echo $approved_requisition; ?></div>
        <p class="dashboard-card-foot">Ready or completed for distribution flow</p>
      </div>

      <div class="dashboard-card" style="--card-accent:#334155;">
        <div class="dashboard-card-head">
          <p class="dashboard-card-title">Stock Products</p>
          <span class="dashboard-card-icon"><i class="fas fa-warehouse"></i></span>
        </div>
        <div class="dashboard-card-value"><?php echo $stock_products; ?></div>
        <p class="dashboard-card-foot"><?php echo $stock_low; ?> product at low stock level</p>
      </div>

      <div class="dashboard-card" style="--card-accent:#0891b2;">
        <div class="dashboard-card-head">
          <p class="dashboard-card-title">System Health</p>
          <span class="dashboard-card-icon"><i class="fas fa-chart-line"></i></span>
        </div>
        <div class="dashboard-card-value"><?php echo ($total_requisition + $distribution_done + $purchase_total); ?></div>
        <p class="dashboard-card-foot">Total operational records tracked</p>
      </div>
    </div>

    <div class="dashboard-metrics">
      <div class="dashboard-metric" style="--metric-accent:#2563eb;">
        <div class="dashboard-metric-top">
          <p class="dashboard-metric-label">Approval Conversion</p>
          <span class="dashboard-metric-value"><?php echo $approval_rate; ?>%</span>
        </div>
        <div class="dashboard-progress"><span style="width:<?php echo min(100, $approval_rate); ?>%;"></span></div>
        <p class="dashboard-progress-note"><?php echo $approved_requisition; ?> approved from <?php echo $total_requisition; ?> requisitions</p>
      </div>

      <div class="dashboard-metric" style="--metric-accent:#0f9f6e;">
        <div class="dashboard-metric-top">
          <p class="dashboard-metric-label">Distribution Throughput</p>
          <span class="dashboard-metric-value"><?php echo $distribution_rate; ?>%</span>
        </div>
        <div class="dashboard-progress"><span style="width:<?php echo min(100, $distribution_rate); ?>%;"></span></div>
        <p class="dashboard-progress-note"><?php echo $distribution_done; ?> distributed, <?php echo $distribution_pending; ?> waiting</p>
      </div>

      <div class="dashboard-metric" style="--metric-accent:#dc2626;">
        <div class="dashboard-metric-top">
          <p class="dashboard-metric-label">Stock Risk</p>
          <span class="dashboard-metric-value"><?php echo $stock_risk_rate; ?>%</span>
        </div>
        <div class="dashboard-progress"><span style="width:<?php echo min(100, $stock_risk_rate); ?>%;"></span></div>
        <p class="dashboard-progress-note"><?php echo $stock_low; ?> low-stock products from <?php echo $stock_products; ?> tracked</p>
      </div>
    </div>

    <div class="dashboard-chart-grid">
      <div class="dashboard-panel">
        <div class="dashboard-panel-header">
          <h3>Requisition Trend</h3>
          <a href="?Requestion">Open requisitions</a>
        </div>
        <div class="dashboard-chart-body">
          <canvas id="dashboardRequisitionChart"></canvas>
        </div>
      </div>

      <div class="dashboard-panel">
        <div class="dashboard-panel-header">
          <h3>Workflow Mix</h3>
          <a href="?Distribution_Pending">Resolve pending</a>
        </div>
        <div class="dashboard-chart-body">
          <canvas id="dashboardStatusChart"></canvas>
        </div>
      </div>
    </div>

    <div class="dashboard-chart-grid compact">
      <div class="dashboard-panel">
        <div class="dashboard-panel-header">
          <h3>Purchase Value</h3>
          <a href="?Purchase_History">View purchases</a>
        </div>
        <div class="dashboard-chart-body compact">
          <canvas id="dashboardPurchaseChart"></canvas>
        </div>
      </div>

      <div class="dashboard-panel">
        <div class="dashboard-panel-header">
          <h3>Distribution by Store</h3>
          <a href="?Distribution_History">View history</a>
        </div>
        <div class="dashboard-chart-body compact">
          <canvas id="dashboardStoreChart"></canvas>
        </div>
      </div>

      <div class="dashboard-panel">
        <div class="dashboard-panel-header">
          <h3>Lowest Stock Items</h3>
          <a href="?Stock">Open stock</a>
        </div>
        <div class="dashboard-chart-body compact">
          <canvas id="dashboardStockChart"></canvas>
        </div>
      </div>
    </div>

    <div class="dashboard-chart-grid">
      <div class="dashboard-panel">
        <div class="dashboard-panel-header">
          <h3>Top Projects by Requisition</h3>
          <a href="?Project_Wise_Distribution/Report">Project report</a>
        </div>
        <div class="dashboard-chart-body">
          <canvas id="dashboardProjectChart"></canvas>
        </div>
      </div>

      <div class="dashboard-panel">
        <div class="dashboard-panel-header">
          <h3>Operating Snapshot</h3>
          <a href="?Product_Lifecycle_Report/Report">Lifecycle</a>
        </div>
        <div class="dashboard-chart-body">
          <canvas id="dashboardSnapshotChart"></canvas>
        </div>
      </div>
    </div>

    <div class="dashboard-panels">
      <div class="dashboard-panel">
        <div class="dashboard-panel-header">
          <h3>Recent Requisitions</h3>
          <a href="?Requestion">View all</a>
        </div>
        <div class="dashboard-table-wrap">
          <?php if (!empty($recent_requisitions)) { ?>
          <table class="dashboard-table">
            <thead>
              <tr>
                <th>Invoice</th>
                <th>Date</th>
                <th>Project</th>
                <th>Store</th>
                <th>Approval</th>
                <th>Distribution</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recent_requisitions as $row) { ?>
              <tr>
                <td><a href="?Requestion_History_Detail/<?php echo $row['invoice_id']; ?>"><?php echo $row['invoice_id']; ?></a></td>
                <td><?php echo dashboardDate($row['date']); ?></td>
                <td><?php echo !empty($row['project_name']) ? $row['project_name'] : '-'; ?></td>
                <td><?php echo !empty($row['store_name']) ? $row['store_name'] : '-'; ?></td>
                <td><span class="dashboard-status <?php echo dashboardStatusClass($row['approval_status']); ?>"><?php echo !empty($row['approval_status']) ? $row['approval_status'] : 'Draft'; ?></span></td>
                <td><span class="dashboard-status <?php echo dashboardStatusClass($row['distribution_status']); ?>"><?php echo !empty($row['distribution_status']) ? $row['distribution_status'] : 'Pending'; ?></span></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
          <?php } else { ?>
            <div class="dashboard-empty">No requisition activity found.</div>
          <?php } ?>
        </div>
      </div>

      <div class="dashboard-panel">
        <div class="dashboard-panel-header">
          <h3>Recent Distribution</h3>
          <a href="?Distribution_History">View all</a>
        </div>
        <div class="dashboard-list">
          <?php if (!empty($recent_distributions)) { foreach ($recent_distributions as $row) { ?>
          <div class="dashboard-list-item">
            <span class="dashboard-list-icon"><i class="fas fa-truck"></i></span>
            <div>
              <p class="dashboard-list-title"><?php echo !empty($row['project_name']) ? $row['project_name'] : 'Distribution'; ?></p>
              <p class="dashboard-list-sub">Invoice <?php echo $row['invoice_id']; ?> &middot; <?php echo dashboardDate($row['date']); ?></p>
            </div>
            <a href="?Distribution_History_Indivisual/<?php echo $row['invoice_id']; ?>/<?php echo $row['distribution_id']; ?>" class="dashboard-status <?php echo dashboardStatusClass($row['received_status']); ?>">
              <?php echo !empty($row['received_status']) ? $row['received_status'] : 'Pending'; ?>
            </a>
          </div>
          <?php } } else { ?>
            <div class="dashboard-empty">No distribution activity found.</div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
$(function () {
  if (typeof Chart === 'undefined') {
    return;
  }

  Chart.defaults.global.defaultFontFamily = "'Source Sans Pro', 'Helvetica Neue', Arial, sans-serif";
  Chart.defaults.global.defaultFontColor = '#52616f';

  var chartColors = {
    blue: '#2563eb',
    teal: '#169b9b',
    green: '#0f9f6e',
    amber: '#f59e0b',
    red: '#dc2626',
    cyan: '#0891b2',
    slate: '#334155'
  };

  function canvas(id) {
    var element = document.getElementById(id);
    return element ? element.getContext('2d') : null;
  }

  function axisOptions(horizontal) {
    var xAxis = {
      gridLines: {
        display: false
      },
      ticks: {
        autoSkip: false,
        maxRotation: 0,
        callback: function (value) {
          return String(value).length > 16 ? String(value).substring(0, 16) + '...' : value;
        }
      }
    };
    var yAxis = {
      ticks: {
        beginAtZero: true,
        precision: 0
      },
      gridLines: {
        color: '#edf2f7',
        zeroLineColor: '#dce5ef'
      }
    };

    if (horizontal) {
      xAxis = {
        ticks: {
          beginAtZero: true,
          precision: 0
        },
        gridLines: {
          color: '#edf2f7',
          zeroLineColor: '#dce5ef'
        }
      };
      yAxis = {
        gridLines: {
          display: false
        },
        ticks: {
          callback: function (value) {
            return String(value).length > 18 ? String(value).substring(0, 18) + '...' : value;
          }
        }
      };
    }

    return {
      responsive: true,
      maintainAspectRatio: false,
      legend: {
        labels: {
          boxWidth: 10,
          fontStyle: 'bold'
        }
      },
      tooltips: {
        mode: 'index',
        intersect: false
      },
      scales: {
        xAxes: [xAxis],
        yAxes: [yAxis]
      }
    };
  }

  var requisitionCtx = canvas('dashboardRequisitionChart');
  if (requisitionCtx) {
    new Chart(requisitionCtx, {
      type: 'bar',
      data: {
        labels: <?php echo dashboardJson($requisition_chart_labels); ?>,
        datasets: [{
          label: 'Total',
          backgroundColor: 'rgba(37, 99, 235, .78)',
          borderColor: chartColors.blue,
          borderWidth: 1,
          data: <?php echo dashboardJson($requisition_chart_total); ?>
        }, {
          label: 'Approved',
          backgroundColor: 'rgba(15, 159, 110, .78)',
          borderColor: chartColors.green,
          borderWidth: 1,
          data: <?php echo dashboardJson($requisition_chart_approved); ?>
        }]
      },
      options: axisOptions(false)
    });
  }

  var statusCtx = canvas('dashboardStatusChart');
  if (statusCtx) {
    new Chart(statusCtx, {
      type: 'doughnut',
      data: {
        labels: <?php echo dashboardJson($status_chart_labels); ?>,
        datasets: [{
          data: <?php echo dashboardJson($status_chart_total); ?>,
          backgroundColor: [chartColors.slate, chartColors.amber, chartColors.green, chartColors.red, chartColors.blue, chartColors.cyan],
          borderColor: '#ffffff',
          borderWidth: 3
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutoutPercentage: 66,
        legend: {
          position: 'bottom',
          labels: {
            boxWidth: 10,
            fontStyle: 'bold'
          }
        }
      }
    });
  }

  var purchaseCtx = canvas('dashboardPurchaseChart');
  if (purchaseCtx) {
    new Chart(purchaseCtx, {
      type: 'line',
      data: {
        labels: <?php echo dashboardJson($purchase_chart_labels); ?>,
        datasets: [{
          label: 'Amount',
          data: <?php echo dashboardJson($purchase_chart_total); ?>,
          borderColor: chartColors.teal,
          backgroundColor: 'rgba(22, 155, 155, .12)',
          pointBackgroundColor: chartColors.teal,
          pointRadius: 3,
          borderWidth: 3,
          fill: true,
          lineTension: .32
        }]
      },
      options: axisOptions(false)
    });
  }

  var storeCtx = canvas('dashboardStoreChart');
  if (storeCtx) {
    new Chart(storeCtx, {
      type: 'horizontalBar',
      data: {
        labels: <?php echo dashboardJson($store_chart_labels); ?>,
        datasets: [{
          label: 'Distributions',
          data: <?php echo dashboardJson($store_chart_total); ?>,
          backgroundColor: 'rgba(37, 99, 235, .78)',
          borderColor: chartColors.blue,
          borderWidth: 1
        }]
      },
      options: axisOptions(true)
    });
  }

  var stockCtx = canvas('dashboardStockChart');
  if (stockCtx) {
    new Chart(stockCtx, {
      type: 'horizontalBar',
      data: {
        labels: <?php echo dashboardJson($stock_chart_labels); ?>,
        datasets: [{
          label: 'Stock',
          data: <?php echo dashboardJson($stock_chart_total); ?>,
          backgroundColor: 'rgba(220, 38, 38, .76)',
          borderColor: chartColors.red,
          borderWidth: 1
        }]
      },
      options: axisOptions(true)
    });
  }

  var projectCtx = canvas('dashboardProjectChart');
  if (projectCtx) {
    new Chart(projectCtx, {
      type: 'horizontalBar',
      data: {
        labels: <?php echo dashboardJson($project_chart_labels); ?>,
        datasets: [{
          label: 'Requisitions',
          data: <?php echo dashboardJson($project_chart_total); ?>,
          backgroundColor: 'rgba(245, 158, 11, .78)',
          borderColor: chartColors.amber,
          borderWidth: 1
        }]
      },
      options: axisOptions(true)
    });
  }

  var snapshotCtx = canvas('dashboardSnapshotChart');
  if (snapshotCtx) {
    new Chart(snapshotCtx, {
      type: 'radar',
      data: {
        labels: ['Requisition', 'Approved', 'Distributed', 'Purchase', 'Stock', 'Tasks'],
        datasets: [{
          label: 'Volume',
          data: [
            <?php echo (int)$total_requisition; ?>,
            <?php echo (int)$approved_requisition; ?>,
            <?php echo (int)$distribution_done; ?>,
            <?php echo (int)$purchase_total; ?>,
            <?php echo (int)$stock_products; ?>,
            <?php echo (int)$my_pending_task; ?>
          ],
          backgroundColor: 'rgba(8, 145, 178, .16)',
          borderColor: chartColors.cyan,
          pointBackgroundColor: chartColors.cyan,
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
          display: false
        },
        scale: {
          ticks: {
            beginAtZero: true,
            precision: 0
          },
          gridLines: {
            color: '#edf2f7'
          },
          pointLabels: {
            fontStyle: 'bold'
          }
        }
      }
    });
  }
});
</script>
