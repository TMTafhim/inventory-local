<?php

function activityAuditSanitizeValue($value, $key = '', $depth = 0)
{
    if ($depth > 5) {
        return '[DEPTH LIMIT]';
    }

    $sensitivePattern = '/password|passwd|pwd|token|secret|api[_-]?key|authorization|cookie|csrf/i';
    if ($key !== '' && preg_match($sensitivePattern, $key)) {
        return '[REDACTED]';
    }

    if (is_array($value)) {
        $clean = array();
        $count = 0;
        foreach ($value as $childKey => $childValue) {
            if ($count >= 100) {
                $clean['_truncated'] = 'Additional values omitted';
                break;
            }
            $clean[$childKey] = activityAuditSanitizeValue($childValue, (string) $childKey, $depth + 1);
            $count++;
        }
        return $clean;
    }

    if (is_object($value)) {
        return '[OBJECT]';
    }

    $text = trim((string) $value);
    if (strlen($text) > 1000) {
        return substr($text, 0, 1000) . '... [TRUNCATED]';
    }
    return $text;
}

function activityAuditSanitizeFiles($files)
{
    $clean = array();
    foreach ((array) $files as $field => $file) {
        $clean[$field] = array(
            'name' => activityAuditSanitizeValue(isset($file['name']) ? $file['name'] : '', 'file_name'),
            'type' => activityAuditSanitizeValue(isset($file['type']) ? $file['type'] : '', 'file_type'),
            'size' => isset($file['size']) ? $file['size'] : null,
            'error' => isset($file['error']) ? $file['error'] : null
        );
    }
    return $clean;
}

function activityAuditJson($value)
{
    if (empty($value)) {
        return null;
    }
    $json = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        return json_encode(array('error' => 'Payload could not be encoded'));
    }
    return strlen($json) > 65535 ? substr($json, 0, 65535) . '... [TRUNCATED]' : $json;
}

function activityAuditRequestContext()
{
    $method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';
    $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    $route = strtok($uri, '#');
    $routeText = ltrim((string) parse_url($route, PHP_URL_QUERY), '?');
    if ($routeText === '') {
        $path = (string) parse_url($route, PHP_URL_PATH);
        $routeText = pathinfo($path, PATHINFO_FILENAME);
    }
    $routeSegments = explode('/', $routeText);
    $module = !empty($routeSegments[0]) ? preg_replace('/[^A-Za-z0-9_-]/', '', $routeSegments[0]) : 'Dashboard';
    if ($module === '' || in_array(strtolower($module), array('index', 'inventory-local'), true)) {
        $module = 'Dashboard';
    }

    $eventType = $method === 'POST' ? 'write' : 'view';
    $eventName = $method === 'POST' ? 'Form submitted' : 'Page viewed';
    if ($method === 'GET' && preg_match('/delete|logout/i', $routeText, $routeActionMatch)) {
        $eventType = 'write';
        $eventName = stripos($routeText, 'logout') !== false ? 'Logout requested' : 'Delete requested';
    }
    if ($method === 'POST') {
        if (isset($_POST['audit_filter']) || isset($_POST['audit_page'])) {
            $eventType = 'view';
            $eventName = 'Audit timeline filtered';
        }
        foreach (array_keys($_POST) as $postKey) {
            if ($eventType === 'view') {
                break;
            }
            if (preg_match('/insert|edit|delete|save|approve|approval|acknowledge|create|update|login|logout|receive|return|distribution|submit/i', $postKey)) {
                $eventName = trim(preg_replace('/[_-]+/', ' ', $postKey));
                break;
            }
        }
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            $eventType = 'ajax';
        }
    }

    return array(
        'method' => $method,
        'route' => substr($route, 0, 1000),
        'module' => substr($module, 0, 150),
        'event_type' => $eventType,
        'event_name' => substr($eventName, 0, 255)
    );
}

function activityAuditInsert($pdo, $data)
{
    try {
        $statement = $pdo->prepare(
            "INSERT INTO system_activity_log
             (actor_id,actor_name,store_id,event_type,event_name,module,route,request_method,request_payload,file_payload,ip_address,user_agent,session_hash,response_code,outcome,duration_ms,created_at)
             VALUES
             (:actor_id,:actor_name,:store_id,:event_type,:event_name,:module,:route,:request_method,:request_payload,:file_payload,:ip_address,:user_agent,:session_hash,:response_code,:outcome,:duration_ms,NOW())"
        );
        $statement->execute($data);
    } catch (Throwable $error) {
        error_log('Activity audit write failed: ' . $error->getMessage());
    }
}

function activityAuditPruneOldRecords($pdo, $retentionDays = 15)
{
    $retentionDays = max(1, (int) $retentionDays);
    $cutoffIntervalDays = max(0, $retentionDays - 1);

    try {
        $lock = $pdo->query("SELECT GET_LOCK('system_activity_audit_prune', 1) AS acquired")->fetch();
        if (empty($lock['acquired'])) {
            return;
        }

        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS system_maintenance_state (
                state_key VARCHAR(100) NOT NULL,
                state_value VARCHAR(255) NULL,
                updated_at DATETIME NOT NULL,
                PRIMARY KEY (state_key)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $stateStatement = $pdo->prepare("SELECT state_value FROM system_maintenance_state WHERE state_key = 'activity_audit_last_pruned_at' LIMIT 1");
        $stateStatement->execute();
        $lastPrunedAt = $stateStatement->fetchColumn();
        if ($lastPrunedAt && strtotime($lastPrunedAt) > strtotime('-15 days')) {
            $pdo->query("SELECT RELEASE_LOCK('system_activity_audit_prune')");
            return;
        }

        do {
            $deleteStatement = $pdo->prepare("DELETE FROM system_activity_log WHERE created_at < DATE_SUB(CURDATE(), INTERVAL " . $cutoffIntervalDays . " DAY) LIMIT 5000");
            $deleteStatement->execute();
            $deletedRows = $deleteStatement->rowCount();
        } while ($deletedRows === 5000);

        $updateState = $pdo->prepare(
            "INSERT INTO system_maintenance_state (state_key,state_value,updated_at)
             VALUES ('activity_audit_last_pruned_at', NOW(), NOW())
             ON DUPLICATE KEY UPDATE state_value=VALUES(state_value), updated_at=VALUES(updated_at)"
        );
        $updateState->execute();
        $pdo->query("SELECT RELEASE_LOCK('system_activity_audit_prune')");
    } catch (Throwable $error) {
        try {
            $pdo->query("SELECT RELEASE_LOCK('system_activity_audit_prune')");
        } catch (Throwable $releaseError) {
        }
        error_log('Activity audit prune failed: ' . $error->getMessage());
    }
}

function activityAuditRecordImmediate($pdo, $actorId, $eventType, $eventName, $module, $payload = array(), $storeId = null)
{
    activityAuditInsert($pdo, array(
        ':actor_id' => (int) $actorId,
        ':actor_name' => isset($_SESSION['LOGINNAME']) ? $_SESSION['LOGINNAME'] : null,
        ':store_id' => $storeId,
        ':event_type' => substr($eventType, 0, 30),
        ':event_name' => substr($eventName, 0, 255),
        ':module' => substr($module, 0, 150),
        ':route' => isset($_SERVER['REQUEST_URI']) ? substr($_SERVER['REQUEST_URI'], 0, 1000) : '',
        ':request_method' => isset($_SERVER['REQUEST_METHOD']) ? substr(strtoupper($_SERVER['REQUEST_METHOD']), 0, 10) : 'POST',
        ':request_payload' => activityAuditJson(activityAuditSanitizeValue($payload)),
        ':file_payload' => null,
        ':ip_address' => isset($_SERVER['REMOTE_ADDR']) ? substr($_SERVER['REMOTE_ADDR'], 0, 45) : null,
        ':user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 1000) : null,
        ':session_hash' => session_id() !== '' ? hash('sha256', session_id()) : null,
        ':response_code' => 200,
        ':outcome' => 'success',
        ':duration_ms' => 0
    ));
}

function activityAuditRegister($pdo)
{
    static $registered = false;
    if ($registered || PHP_SAPI === 'cli' || empty($_SESSION['LoginReGiSterSession'])) {
        return;
    }
    $registered = true;

    activityAuditPruneOldRecords($pdo, 15);

    $startedAt = microtime(true);
    $context = activityAuditRequestContext();
    $actorId = (int) $_SESSION['LoginReGiSterSession'];
    $actorName = isset($_SESSION['LOGINNAME']) ? (string) $_SESSION['LOGINNAME'] : null;
    $storeId = isset($_SESSION['STORE_ID']) ? (int) $_SESSION['STORE_ID'] : null;
    $requestPayload = activityAuditJson(activityAuditSanitizeValue($_POST));
    $filePayload = activityAuditJson(activityAuditSanitizeFiles($_FILES));
    $ipAddress = isset($_SERVER['REMOTE_ADDR']) ? substr($_SERVER['REMOTE_ADDR'], 0, 45) : null;
    $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 1000) : null;
    $sessionHash = session_id() !== '' ? hash('sha256', session_id()) : null;

    register_shutdown_function(function () use ($pdo, $startedAt, $context, $actorId, $actorName, $storeId, $requestPayload, $filePayload, $ipAddress, $userAgent, $sessionHash) {
        $responseCode = http_response_code();
        if (!$responseCode) {
            $responseCode = 200;
        }
        $lastError = error_get_last();
        $fatalTypes = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR);
        $hasFatalError = $lastError && in_array($lastError['type'], $fatalTypes, true);
        $hasBusinessFailure = !empty($_SESSION['warning_message']);
        $outcome = ($responseCode >= 400 || $hasFatalError || $hasBusinessFailure) ? 'failed' : 'success';

        activityAuditInsert($pdo, array(
            ':actor_id' => $actorId,
            ':actor_name' => $actorName,
            ':store_id' => $storeId,
            ':event_type' => $context['event_type'],
            ':event_name' => $context['event_name'],
            ':module' => $context['module'],
            ':route' => $context['route'],
            ':request_method' => $context['method'],
            ':request_payload' => $requestPayload,
            ':file_payload' => $filePayload,
            ':ip_address' => $ipAddress,
            ':user_agent' => $userAgent,
            ':session_hash' => $sessionHash,
            ':response_code' => $responseCode,
            ':outcome' => $outcome,
            ':duration_ms' => round((microtime(true) - $startedAt) * 1000, 3)
        ));
    });
}
