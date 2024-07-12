<?php
header('Content-Type: application/json');

// 连接数据库
$servername = "localhost";
$username = "material_codes";
$password = "yrmkks2PNNbnxdyR";
$dbname = "material_codes";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => '数据库连接失败: ' . $conn->connect_error]));
}

// 获取前端发送的搜索关键字
$searchInput = $_GET['searchInput'];

// 从数据库中检索数据
$sql = "SELECT * FROM material_codes WHERE code LIKE ? OR type LIKE ? OR sub_type LIKE ? OR properties LIKE ?";
$stmt = $conn->prepare($sql);
$searchInput = '%' . $searchInput . '%';
$stmt->bind_param("ssss", $searchInput, $searchInput, $searchInput, $searchInput);
$stmt->execute();
$result = $stmt->get_result();

$searchResults = [];
while ($row = $result->fetch_assoc()) {
    $properties = explode('-', $row['code']);
    array_shift($properties); // 移除编码前缀部分
    // $properties = array_map(function($prop) {
    //     return $prop . ': ' . (strpos($prop, ':') !== false ? substr($prop, strpos($prop, ':') + 1) : '');
    // }, $properties);

    $searchResults[] = [
        'code' => $row['code'],
        'type' => $row['type'],
        'sub_type' => $row['sub_type'] ? $row['sub_type'] : '',
        'properties' => $properties
    ];
}

echo json_encode(['status' => 'success', 'data' => $searchResults]);

$stmt->close();
$conn->close();
?>
