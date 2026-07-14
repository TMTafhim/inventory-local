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

$organization = trim($_POST['organization'] ?? '');
$name = trim($_POST['name'] ?? '');
$mobile = trim($_POST['mobile'] ?? '');
$email = trim($_POST['email'] ?? '');
$address = trim($_POST['address'] ?? '');

if ($organization === '' || $name === '' || $mobile === '') {
    http_response_code(422);
    echo json_encode(array('error' => 'Organization, contact person, and mobile number are required.'));
    exit;
}
if (!preg_match('/^[0-9]{11}$/', $mobile)) {
    http_response_code(422);
    echo json_encode(array('error' => 'The mobile number must contain exactly 11 digits.'));
    exit;
}
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(array('error' => 'Please enter a valid email address.'));
    exit;
}

$duplicateStatement = $pdo->prepare(
    "SELECT id, organization, name, mobile
     FROM supplier_information
     WHERE deleted_at IS NULL
       AND LOWER(TRIM(organization)) = LOWER(TRIM(:organization))
     ORDER BY id DESC
     LIMIT 1"
);
$duplicateStatement->execute(array(':organization' => $organization));
$existingSupplier = $duplicateStatement->fetch();
if ($existingSupplier) {
    echo json_encode(array('created' => false, 'supplier' => $existingSupplier));
    exit;
}

$mobileStatement = $pdo->prepare(
    "SELECT organization
     FROM supplier_information
     WHERE mobile = :mobile
       AND deleted_at IS NULL
     ORDER BY id DESC
     LIMIT 1"
);
$mobileStatement->execute(array(':mobile' => $mobile));
$mobileSupplier = $mobileStatement->fetch();
if ($mobileSupplier) {
    http_response_code(409);
    echo json_encode(array('error' => 'This mobile number is already used by ' . $mobileSupplier['organization'] . '.'));
    exit;
}

$insertStatement = $pdo->prepare(
    "INSERT INTO supplier_information
        (organization, name, email, mobile, address, amount, created_by, created_at)
     VALUES
        (:organization, :name, :email, :mobile, :address, '0', :created_by, :created_at)"
);
$insertStatement->execute(array(
    ':organization' => $organization,
    ':name' => $name,
    ':email' => $email,
    ':mobile' => $mobile,
    ':address' => $address,
    ':created_by' => $_SESSION['LoginReGiSterSession'],
    ':created_at' => $current_time,
));

echo json_encode(array(
    'created' => true,
    'supplier' => array(
        'id' => $pdo->lastInsertId(),
        'organization' => $organization,
        'name' => $name,
        'mobile' => $mobile,
    ),
));
