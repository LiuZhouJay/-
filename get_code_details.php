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

// 获取前端发送的编码
$code = $_GET['code'];

// 从数据库中检索数据
$sql = "SELECT * FROM material_codes WHERE code = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'SQL 预处理失败: ' . $conn->error]);
    exit;
}
$stmt->bind_param("s", $code);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // 解析属性字段，将其转换为键值对数组
    $properties = json_decode($row['properties'], true);
    $propertiesForDisplay = [];
    foreach ($properties as $key => $value) {
        $propertiesForDisplay[] = ['key' => $key, 'value' => $value];
    }

    echo json_encode(['status' => 'success', 'data' => [
        'code' => $row['code'],
        'type' => $row['type'],
        'sub_type' => $row['sub_type'],
        'properties' => $propertiesForDisplay
    ]]);
} else {
    echo json_encode(['status' => 'error', 'message' => '未找到编码信息']);
}

$stmt->close();
$conn->close();
?>
