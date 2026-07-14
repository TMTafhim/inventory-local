<?php
if (!authIsSuperAdmin($LoginReGiSterSession)) {
    if (!headers_sent()) {
        http_response_code(403);
    }
    echo '<section class="content"><div class="alert alert-danger"><i class="fas fa-lock mr-2"></i>Access denied. System Activity Audit is restricted to Employee ID 121.</div></section>';
    return;
}

function auditHtml($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function auditPrettyJson($json)
{
    if (!$json) {
        return 'No request details recorded.';
    }
    $decoded = json_decode($json, true);
    return $decoded === null ? $json : json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function auditDeviceLabel($userAgent)
{
    if (!$userAgent) {
        return 'Unknown device';
    }
    $browser = stripos($userAgent, 'Edg/') !== false ? 'Edge' : (stripos($userAgent, 'Chrome/') !== false ? 'Chrome' : (stripos($userAgent, 'Firefox/') !== false ? 'Firefox' : (stripos($userAgent, 'Safari/') !== false ? 'Safari' : 'Browser')));
    $platform = stripos($userAgent, 'Windows') !== false ? 'Windows' : (stripos($userAgent, 'Macintosh') !== false ? 'macOS' : (stripos($userAgent, 'Android') !== false ? 'Android' : (stripos($userAgent, 'iPhone') !== false ? 'iPhone' : (stripos($userAgent, 'Linux') !== false ? 'Linux' : 'Device'))));
    return $browser . ' on ' . $platform;
}

$dateFrom = isset($_POST['date_from']) && $_POST['date_from'] !== '' ? $_POST['date_from'] : date('Y-m-d', strtotime('-7 days'));
$dateTo = isset($_POST['date_to']) && $_POST['date_to'] !== '' ? $_POST['date_to'] : date('Y-m-d');
$actorFilter = isset($_POST['actor_id']) ? (int) $_POST['actor_id'] : 0;
$moduleFilter = isset($_POST['module']) ? trim($_POST['module']) : '';
$eventFilter = isset($_POST['event_type']) ? trim($_POST['event_type']) : '';
$outcomeFilter = isset($_POST['outcome']) ? trim($_POST['outcome']) : '';
$searchFilter = isset($_POST['audit_search']) ? trim($_POST['audit_search']) : '';
$page = isset($_POST['audit_page']) ? max(1, (int) $_POST['audit_page']) : 1;
$pageSize = 100;

$where = array('created_at >= :date_from', 'created_at < DATE_ADD(:date_to, INTERVAL 1 DAY)');
$params = array(':date_from' => $dateFrom, ':date_to' => $dateTo);
if ($actorFilter > 0) {
    $where[] = 'actor_id = :actor_id';
    $params[':actor_id'] = $actorFilter;
}
if ($moduleFilter !== '') {
    $where[] = 'module = :module';
    $params[':module'] = $moduleFilter;
}
if (in_array($eventFilter, array('view', 'write', 'ajax', 'authentication'), true)) {
    $where[] = 'event_type = :event_type';
    $params[':event_type'] = $eventFilter;
}
if (in_array($outcomeFilter, array('success', 'failed'), true)) {
    $where[] = 'outcome = :outcome';
    $params[':outcome'] = $outcomeFilter;
}
if ($searchFilter !== '') {
    $where[] = '(event_name LIKE :search_event OR module LIKE :search_module OR route LIKE :search_route OR actor_name LIKE :search_actor OR ip_address LIKE :search_ip)';
    $searchValue = '%' . $searchFilter . '%';
    $params[':search_event'] = $searchValue;
    $params[':search_module'] = $searchValue;
    $params[':search_route'] = $searchValue;
    $params[':search_actor'] = $searchValue;
    $params[':search_ip'] = $searchValue;
}
$whereSql = implode(' AND ', $where);

$countStatement = $pdo->prepare("SELECT COUNT(*) FROM system_activity_log WHERE $whereSql");
$countStatement->execute($params);
$totalRows = (int) $countStatement->fetchColumn();
$totalPages = max(1, (int) ceil($totalRows / $pageSize));
$page = min($page, $totalPages);
$offset = ($page - 1) * $pageSize;

$activityStatement = $pdo->prepare("SELECT * FROM system_activity_log WHERE $whereSql ORDER BY id DESC LIMIT $pageSize OFFSET $offset");
$activityStatement->execute($params);
$activities = $activityStatement->fetchAll();

$employees = $pdo->query("SELECT id,name_en FROM employee_information WHERE deleted_at IS NULL ORDER BY name_en")->fetchAll();
$modules = $pdo->query("SELECT DISTINCT module FROM system_activity_log WHERE module<>'' ORDER BY module")->fetchAll(PDO::FETCH_COLUMN);
$stats = $pdo->query("SELECT COUNT(*) total_count,SUM(created_at>=CURDATE()) today_count,SUM(event_type IN ('write','ajax','authentication') AND created_at>=DATE_SUB(NOW(),INTERVAL 7 DAY)) write_count,SUM(outcome='failed' AND created_at>=DATE_SUB(NOW(),INTERVAL 7 DAY)) failed_count FROM system_activity_log")->fetch();
$auditRetention = array('state_value' => null, 'updated_at' => null);
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS system_maintenance_state (state_key VARCHAR(100) NOT NULL,state_value VARCHAR(255) NULL,updated_at DATETIME NOT NULL,PRIMARY KEY (state_key)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $auditRetentionStatement = $pdo->prepare("SELECT state_value,updated_at FROM system_maintenance_state WHERE state_key='activity_audit_last_pruned_at' LIMIT 1");
    $auditRetentionStatement->execute();
    $auditRetentionRow = $auditRetentionStatement->fetch();
    if ($auditRetentionRow) {
        $auditRetention = $auditRetentionRow;
    }
} catch (Throwable $auditRetentionError) {
}
?>

<style>
.audit-hero { align-items: center; background: #111827; color: #fff; display: flex; justify-content: space-between; padding: 18px 20px; }
.audit-hero h3 { font-size: 18px; font-weight: 700; margin: 0; }
.audit-hero p { color: #cbd5e1; font-size: 13px; margin: 4px 0 0; }
.audit-hero-actions { align-items: flex-end; display: flex; flex-direction: column; gap: 8px; }
.audit-restricted,.audit-retention { font-size: 12px; font-weight: 700; padding: 7px 10px; }
.audit-restricted { background: #dcfce7; color: #166534; }
.audit-retention { background: #e0f2fe; color: #075985; }
.audit-stats { display: grid; gap: 1px; grid-template-columns: repeat(4,minmax(0,1fr)); background: #e2e8f0; border-bottom: 1px solid #e2e8f0; }
.audit-stat { background: #fff; padding: 14px 18px; }
.audit-stat span { color: #64748b; display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; }
.audit-stat strong { color: #0f172a; display: block; font-size: 22px; margin-top: 3px; }
.audit-filter-band { background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 15px 18px 5px; }
.audit-filter-grid { display: grid; gap: 12px; grid-template-columns: repeat(6,minmax(130px,1fr)); }
.audit-filter-grid .form-group { margin-bottom: 10px; }
.audit-filter-grid label { color: #475569; font-size: 11px; font-weight: 700; margin-bottom: 4px; text-transform: uppercase; }
.audit-search-row { align-items: flex-end; display: grid; gap: 12px; grid-template-columns: minmax(240px,1fr) auto; }
.audit-event { font-weight: 700; color: #1f2937; }
.audit-route { color: #64748b; display: block; font-family: monospace; font-size: 11px; max-width: 340px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.audit-badge { display: inline-block; font-size: 10px; font-weight: 800; padding: 4px 7px; text-transform: uppercase; }
.audit-badge-view { background: #e0f2fe; color: #075985; }
.audit-badge-write { background: #fef3c7; color: #92400e; }
.audit-badge-ajax { background: #ede9fe; color: #5b21b6; }
.audit-badge-authentication { background: #dcfce7; color: #166534; }
.audit-outcome-success { color: #15803d; font-weight: 700; }
.audit-outcome-failed { color: #b91c1c; font-weight: 700; }
.audit-pager { align-items: center; display: flex; gap: 8px; justify-content: flex-end; padding: 12px 16px; }
.audit-detail-grid { display: grid; gap: 12px; grid-template-columns: repeat(2,minmax(0,1fr)); }
.audit-detail-item { border-bottom: 1px solid #e5e7eb; padding-bottom: 8px; }
.audit-detail-item span { color: #64748b; display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; }
.audit-detail-item strong { color: #111827; display: block; margin-top: 3px; overflow-wrap: anywhere; }
.audit-json { background: #111827; color: #e5e7eb; font-size: 12px; max-height: 300px; overflow: auto; padding: 12px; white-space: pre-wrap; }
@media(max-width:991.98px){.audit-filter-grid{grid-template-columns:repeat(3,minmax(0,1fr));}.audit-stats{grid-template-columns:repeat(2,minmax(0,1fr));}}
@media(max-width:575.98px){.audit-hero{align-items:flex-start;flex-direction:column;gap:12px}.audit-hero-actions{align-items:flex-start}.audit-filter-grid{grid-template-columns:1fr}.audit-search-row{grid-template-columns:1fr}.audit-detail-grid{grid-template-columns:1fr}}
</style>

<section class="content">
  <div class="card">
    <div class="audit-hero">
      <div><h3><i class="fas fa-shield-alt mr-2"></i>System Activity Audit</h3><p>Read-only timeline of authenticated activity across the ERP.</p></div>
      <div class="audit-hero-actions">
        <div class="audit-restricted"><i class="fas fa-lock mr-1"></i>Employee ID 121 only</div>
        <div class="audit-retention"><i class="fas fa-broom mr-1"></i>Auto keeps last 15 days<?php echo !empty($auditRetention['updated_at']) ? ' - last cleaned ' . auditHtml(date('d-m-Y h:i A', strtotime($auditRetention['updated_at']))) : ''; ?></div>
      </div>
    </div>

    <div class="audit-stats">
      <div class="audit-stat"><span>All Recorded</span><strong><?php echo number_format((int) $stats['total_count']); ?></strong></div>
      <div class="audit-stat"><span>Today</span><strong><?php echo number_format((int) $stats['today_count']); ?></strong></div>
      <div class="audit-stat"><span>Actions - 7 Days</span><strong><?php echo number_format((int) $stats['write_count']); ?></strong></div>
      <div class="audit-stat"><span>Failed - 7 Days</span><strong><?php echo number_format((int) $stats['failed_count']); ?></strong></div>
    </div>

    <form method="post" action="?System_Activity_Audit/Administration">
      <div class="audit-filter-band">
        <div class="audit-filter-grid">
          <div class="form-group"><label for="date_from">From</label><input class="form-control" type="date" id="date_from" name="date_from" value="<?php echo auditHtml($dateFrom); ?>"></div>
          <div class="form-group"><label for="date_to">To</label><input class="form-control" type="date" id="date_to" name="date_to" value="<?php echo auditHtml($dateTo); ?>"></div>
          <div class="form-group"><label for="actor_id">Employee</label><select class="form-control select2" id="actor_id" name="actor_id"><option value="0">All employees</option><?php foreach ($employees as $employee) { ?><option value="<?php echo (int) $employee['id']; ?>"<?php echo $actorFilter === (int) $employee['id'] ? ' selected' : ''; ?>><?php echo auditHtml($employee['name_en'] . ' (#' . $employee['id'] . ')'); ?></option><?php } ?></select></div>
          <div class="form-group"><label for="module">Module</label><select class="form-control select2" id="module" name="module"><option value="">All modules</option><?php foreach ($modules as $module) { ?><option value="<?php echo auditHtml($module); ?>"<?php echo $moduleFilter === $module ? ' selected' : ''; ?>><?php echo auditHtml(str_replace('_', ' ', $module)); ?></option><?php } ?></select></div>
          <div class="form-group"><label for="event_type">Event</label><select class="form-control" id="event_type" name="event_type"><option value="">All events</option><?php foreach (array('view'=>'Page View','write'=>'Write Action','ajax'=>'AJAX Action','authentication'=>'Authentication') as $value=>$label) { ?><option value="<?php echo $value; ?>"<?php echo $eventFilter === $value ? ' selected' : ''; ?>><?php echo $label; ?></option><?php } ?></select></div>
          <div class="form-group"><label for="outcome">Outcome</label><select class="form-control" id="outcome" name="outcome"><option value="">All outcomes</option><option value="success"<?php echo $outcomeFilter === 'success' ? ' selected' : ''; ?>>Success</option><option value="failed"<?php echo $outcomeFilter === 'failed' ? ' selected' : ''; ?>>Failed</option></select></div>
        </div>
        <div class="audit-search-row">
          <div class="form-group"><label for="audit_search">Search timeline</label><input class="form-control" id="audit_search" name="audit_search" value="<?php echo auditHtml($searchFilter); ?>" placeholder="Employee, action, module, route or IP"></div>
          <div class="form-group"><button class="btn btn-primary" name="audit_filter" value="1"><i class="fas fa-filter mr-1"></i>Apply Filters</button></div>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered table-hover mb-0">
          <thead><tr><th>Time</th><th>Employee</th><th>Event</th><th>Module / Route</th><th>Device / IP</th><th>Outcome</th><th></th></tr></thead>
          <tbody>
          <?php if (!$activities) { ?><tr><td colspan="7" class="text-center text-muted py-4">No activity found for the selected filters.</td></tr><?php } ?>
          <?php foreach ($activities as $activity) { ?>
            <tr>
              <td class="text-nowrap"><strong><?php echo date('d-m-Y', strtotime($activity['created_at'])); ?></strong><br><small><?php echo date('h:i:s A', strtotime($activity['created_at'])); ?></small></td>
              <td><strong><?php echo auditHtml($activity['actor_name'] ?: 'Employee #' . $activity['actor_id']); ?></strong><br><small>ID <?php echo (int) $activity['actor_id']; ?></small></td>
              <td><span class="audit-badge audit-badge-<?php echo auditHtml($activity['event_type']); ?>"><?php echo auditHtml($activity['event_type']); ?></span><br><span class="audit-event"><?php echo auditHtml($activity['event_name']); ?></span></td>
              <td><strong><?php echo auditHtml(str_replace('_', ' ', $activity['module'])); ?></strong><span class="audit-route" title="<?php echo auditHtml($activity['route']); ?>"><?php echo auditHtml($activity['route']); ?></span></td>
              <td><?php echo auditHtml(auditDeviceLabel($activity['user_agent'])); ?><br><small><?php echo auditHtml($activity['ip_address'] ?: 'Unknown IP'); ?></small></td>
              <td><span class="audit-outcome-<?php echo auditHtml($activity['outcome']); ?>"><i class="fas <?php echo $activity['outcome'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> mr-1"></i><?php echo ucfirst(auditHtml($activity['outcome'])); ?></span><br><small>HTTP <?php echo (int) $activity['response_code']; ?> - <?php echo auditHtml($activity['duration_ms']); ?> ms</small></td>
              <td><button type="button" class="btn btn-sm btn-outline-primary audit-view-detail" title="View details"
                data-id="<?php echo (int) $activity['id']; ?>"
                data-actor="<?php echo auditHtml($activity['actor_name'] ?: 'Employee #' . $activity['actor_id']); ?>"
                data-event="<?php echo auditHtml($activity['event_name']); ?>"
                data-module="<?php echo auditHtml($activity['module']); ?>"
                data-route="<?php echo auditHtml($activity['route']); ?>"
                data-time="<?php echo auditHtml($activity['created_at']); ?>"
                data-ip="<?php echo auditHtml($activity['ip_address']); ?>"
                data-device="<?php echo auditHtml(auditDeviceLabel($activity['user_agent'])); ?>"
                data-session="<?php echo auditHtml(substr((string) $activity['session_hash'], 0, 16)); ?>"
                data-request="<?php echo base64_encode(auditPrettyJson($activity['request_payload'])); ?>"
                data-files="<?php echo base64_encode(auditPrettyJson($activity['file_payload'])); ?>"><i class="fas fa-eye"></i></button></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>

      <div class="audit-pager">
        <span class="text-muted mr-2"><?php echo number_format($totalRows); ?> record(s) - Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
        <button class="btn btn-sm btn-outline-secondary" name="audit_page" value="<?php echo max(1, $page - 1); ?>"<?php echo $page <= 1 ? ' disabled' : ''; ?>><i class="fas fa-chevron-left"></i></button>
        <button class="btn btn-sm btn-outline-secondary" name="audit_page" value="<?php echo min($totalPages, $page + 1); ?>"<?php echo $page >= $totalPages ? ' disabled' : ''; ?>><i class="fas fa-chevron-right"></i></button>
      </div>
    </form>
  </div>
</section>

<div class="modal fade" id="audit-detail-modal" tabindex="-1" role="dialog" aria-labelledby="audit-detail-title" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title" id="audit-detail-title"><i class="fas fa-fingerprint mr-2"></i>Activity Detail</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
    <div class="modal-body">
      <div class="audit-detail-grid mb-3">
        <div class="audit-detail-item"><span>Audit ID</span><strong id="audit-detail-id"></strong></div>
        <div class="audit-detail-item"><span>Employee</span><strong id="audit-detail-actor"></strong></div>
        <div class="audit-detail-item"><span>Event</span><strong id="audit-detail-event"></strong></div>
        <div class="audit-detail-item"><span>Module</span><strong id="audit-detail-module"></strong></div>
        <div class="audit-detail-item"><span>Time</span><strong id="audit-detail-time"></strong></div>
        <div class="audit-detail-item"><span>Device / IP</span><strong id="audit-detail-device"></strong></div>
        <div class="audit-detail-item"><span>Session Reference</span><strong id="audit-detail-session"></strong></div>
        <div class="audit-detail-item"><span>Route</span><strong id="audit-detail-route"></strong></div>
      </div>
      <label>Sanitized Request Data</label><pre class="audit-json" id="audit-detail-request"></pre>
      <label class="mt-3">Uploaded File Metadata</label><pre class="audit-json" id="audit-detail-files"></pre>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button></div>
  </div></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  function decodeAuditData(value) {
    try { return decodeURIComponent(escape(window.atob(value || ''))); } catch (error) { return 'Unable to decode details.'; }
  }
  $(document).on('click', '.audit-view-detail', function () {
    var button = $(this);
    $('#audit-detail-id').text(button.data('id'));
    $('#audit-detail-actor').text(button.data('actor'));
    $('#audit-detail-event').text(button.data('event'));
    $('#audit-detail-module').text(button.data('module'));
    $('#audit-detail-time').text(button.data('time'));
    $('#audit-detail-device').text(button.data('device') + ' / ' + (button.data('ip') || 'Unknown IP'));
    $('#audit-detail-session').text(button.data('session') || 'Not available');
    $('#audit-detail-route').text(button.data('route'));
    $('#audit-detail-request').text(decodeAuditData(button.attr('data-request')));
    $('#audit-detail-files').text(decodeAuditData(button.attr('data-files')));
    $('#audit-detail-modal').modal('show');
  });
});
</script>
