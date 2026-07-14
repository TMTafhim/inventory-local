<?php
include("BDB/DBConnEction.php");

$term = isset($_GET['term']) ? trim($_GET['term']) : '';
$params = array();
$where = "deleted_at IS NULL";
if ($term !== '') {
    $terms = preg_split('/\s+/', preg_replace('/[^A-Za-z0-9]+/', ' ', $term));
    $terms = array_values(array_filter($terms, function($value) { return $value !== ''; }));
    $phrasePart = "(name LIKE :term_name OR code LIKE :term_code OR description LIKE :term_description)";
    $parts = array();
    $params[':term_name'] = '%' . $term . '%';
    $params[':term_code'] = '%' . $term . '%';
    $params[':term_description'] = '%' . $term . '%';
    foreach ($terms as $index => $word) {
        $nameKey = ':word_name_' . $index;
        $codeKey = ':word_code_' . $index;
        $descriptionKey = ':word_description_' . $index;
        $parts[] = "(name LIKE $nameKey OR code LIKE $codeKey OR description LIKE $descriptionKey)";
        $params[$nameKey] = '%' . $word . '%';
        $params[$codeKey] = '%' . $word . '%';
        $params[$descriptionKey] = '%' . $word . '%';
    }
    $where .= " AND (" . $phrasePart . (!empty($parts) ? " OR (" . implode(' AND ', $parts) . ")" : "") . ")";
}

$statement = $pdo->prepare("SELECT id,name,code,unit FROM product_information WHERE $where ORDER BY name ASC LIMIT 25");
$statement->execute($params);
$response = array();
while ($row = $statement->fetch()) {
    $label = $row['name'] . (!empty($row['code']) ? ' | ' . $row['code'] : '') . (!empty($row['unit']) ? ' | ' . $row['unit'] : '');
    $response[] = array(
        'id' => $row['id'],
        'value' => $row['name'],
        'label' => $label
    );
}

header('Content-Type: application/json');
echo json_encode($response);
?>
