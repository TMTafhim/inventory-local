<?php
require_once __DIR__ . '/BDB/DBConnEction.php';

header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['LoginReGiSterSession'])) {
    http_response_code(401);
    echo json_encode(array('error' => 'Your session has expired. Please log in again.'));
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(array('error' => 'Invalid request.'));
    exit;
}

$entity = trim($_POST['entity'] ?? '');
$definitions = array(
    'product' => array(
        'table' => 'product_information',
        'label' => 'Product',
        'fields' => array('product_category', 'code_no', 'name', 'code', 'unit', 'description'),
        'required' => array('product_category', 'name', 'unit'),
        'text_field' => 'name',
        'value_type' => 'text',
    ),
    'supplier' => array(
        'table' => 'supplier_information',
        'label' => 'Supplier',
        'fields' => array('organization', 'name', 'mobile', 'email', 'address'),
        'required' => array('organization', 'name', 'mobile'),
        'text_field' => 'organization',
    ),
    'product_category' => array(
        'table' => 'product_category',
        'label' => 'Product Category',
        'fields' => array('name', 'code'),
        'required' => array('name', 'code'),
        'text_field' => 'name',
    ),
    'product_unit' => array(
        'table' => 'product_unit',
        'label' => 'Product Unit',
        'fields' => array('name'),
        'required' => array('name'),
        'text_field' => 'name',
        'value_type' => 'text',
    ),
    'project' => array(
        'table' => 'project_information',
        'label' => 'Project',
        'fields' => array('name', 'location'),
        'required' => array('name', 'location'),
        'text_field' => 'name',
    ),
    'department' => array(
        'table' => 'hr_department',
        'label' => 'Department',
        'fields' => array('name'),
        'required' => array('name'),
        'text_field' => 'name',
    ),
    'designation' => array(
        'table' => 'hr_designation',
        'label' => 'Designation',
        'fields' => array('name'),
        'required' => array('name'),
        'text_field' => 'name',
    ),
    'leave_type' => array(
        'table' => 'hr_leave_type',
        'label' => 'Leave Type',
        'fields' => array('name', 'number_of_days'),
        'required' => array('name', 'number_of_days'),
        'text_field' => 'name',
        'value_type' => 'text',
    ),
);

if (!isset($definitions[$entity])) {
    http_response_code(400);
    echo json_encode(array('error' => 'Unsupported master record type.'));
    exit;
}

$definition = $definitions[$entity];
$values = array();
foreach ($definition['fields'] as $field) {
    $values[$field] = trim($_POST[$field] ?? '');
}
foreach ($definition['required'] as $field) {
    if ($values[$field] === '') {
        http_response_code(422);
        echo json_encode(array('error' => ucwords(str_replace('_', ' ', $field)) . ' is required.'));
        exit;
    }
}
if ($entity === 'leave_type' && (!is_numeric($values['number_of_days']) || (float)$values['number_of_days'] <= 0)) {
    http_response_code(422);
    echo json_encode(array('error' => 'Number of days must be greater than zero.'));
    exit;
}
if ($entity === 'supplier' && !preg_match('/^[0-9]{11}$/', $values['mobile'])) {
    http_response_code(422);
    echo json_encode(array('error' => 'The mobile number must contain exactly 11 digits.'));
    exit;
}
if ($entity === 'supplier' && $values['email'] !== '' && !filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(array('error' => 'Please enter a valid email address.'));
    exit;
}

$table = $definition['table'];
$textField = $definition['text_field'];
$duplicateStatement = $pdo->prepare(
    "SELECT id, `$textField` AS text
     FROM `$table`
     WHERE LOWER(TRIM(`$textField`)) = LOWER(TRIM(:text_value))
       AND deleted_at IS NULL
     ORDER BY id DESC
     LIMIT 1"
);
$duplicateStatement->execute(array(':text_value' => $values[$textField]));
$existing = $duplicateStatement->fetch();
if ($existing) {
    $existing['value'] = isset($definition['value_type']) && $definition['value_type'] === 'text' ? $existing['text'] : $existing['id'];
    echo json_encode(array('created' => false, 'item' => $existing));
    exit;
}
if ($entity === 'supplier') {
    $mobileStatement = $pdo->prepare(
        "SELECT organization FROM supplier_information
         WHERE mobile = :mobile AND deleted_at IS NULL
         ORDER BY id DESC LIMIT 1"
    );
    $mobileStatement->execute(array(':mobile' => $values['mobile']));
    $mobileSupplier = $mobileStatement->fetch();
    if ($mobileSupplier) {
        http_response_code(409);
        echo json_encode(array('error' => 'This mobile number is already used by ' . $mobileSupplier['organization'] . '.'));
        exit;
    }
}

$columns = $definition['fields'];
$placeholders = array_map(function ($field) { return ':' . $field; }, $columns);
$sql = "INSERT INTO `$table` (`" . implode('`,`', $columns) . "`, `created_by`, `created_at`)
        VALUES (" . implode(',', $placeholders) . ", :created_by, :created_at)";
$parameters = array(':created_by' => $_SESSION['LoginReGiSterSession'], ':created_at' => $current_time);
foreach ($values as $field => $value) {
    $parameters[':' . $field] = $value;
}
$insertStatement = $pdo->prepare($sql);
$insertStatement->execute($parameters);

echo json_encode(array(
    'created' => true,
    'item' => array(
        'id' => $pdo->lastInsertId(),
        'value' => isset($definition['value_type']) && $definition['value_type'] === 'text' ? $values[$textField] : $pdo->lastInsertId(),
        'text' => $values[$textField],
    ),
));
