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

// 获取前端发送的数据
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['type']) || !isset($data['sub_type']) || !isset($data['properties']) || !isset($data['code'])) {
    echo json_encode(['status' => 'error', 'message' => '缺少必要的数据字段']);
    exit;
}

$type = $data['type'];
$sub_type = $data['sub_type'];
$properties = json_encode($data['properties']);
$code = $data['code'];

// 检查是否存在相同的物料信息
$checkSql = "SELECT * FROM material_codes WHERE type = ? AND sub_type = ? AND properties = ?";
$stmtCheck = $conn->prepare($checkSql);
$stmtCheck->bind_param("sss", $type, $sub_type, $properties);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($resultCheck->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => '物料信息已存在，保存失败']);
    exit;
}

// 检查编码是否已存在
$checkCodeSql = "SELECT * FROM material_codes WHERE code = ?";
$stmtCheckCode = $conn->prepare($checkCodeSql);
$stmtCheckCode->bind_param("s", $code);
$stmtCheckCode->execute();
$resultCheckCode = $stmtCheckCode->get_result();

if ($resultCheckCode->num_rows > 0) {
    // 如果编码已存在，更新记录
    $updateSql = "UPDATE material_codes SET type = ?, sub_type = ?, properties = ? WHERE code = ?";
    $stmtUpdate = $conn->prepare($updateSql);
    if ($stmtUpdate->bind_param("ssss", $type, $sub_type, $properties, $code) && $stmtUpdate->execute()) {
        $response = json_encode(['status' => 'success', 'message' => '编码信息已更新']);
        error_log('Response: ' . $response); // 添加这行日志
        echo $response;
    } else {
        echo json_encode(['status' => 'error', 'message' => '更新编码信息时出错: ' . $stmtUpdate->error]);
    }
} else {
    // 如果编码不存在，插入新记录
    $insertSql = "INSERT INTO material_codes (type, sub_type, properties, code) VALUES (?, ?, ?, ?)";
    $stmtInsert = $conn->prepare($insertSql);
    if ($stmtInsert->bind_param("ssss", $type, $sub_type, $properties, $code) && $stmtInsert->execute()) {
        $response = json_encode(['status' => 'success', 'message' => '编码已成功保存']);
        error_log('Response: ' . $response); // 添加这行日志
        echo $response;
    } else {
        echo json_encode(['status' => 'error', 'message' => '执行插入时出错: ' . $stmtInsert->error]);
    }
}

if (isset($stmtCheck)) {
    $stmtCheck->close();
}
if (isset($stmtCheckCode)) {
    $stmtCheckCode->close();
}
if (isset($stmtUpdate)) {
    $stmtUpdate->close();
}
if (isset($stmtInsert)) {
    $stmtInsert->close();
}
$conn->close();
?>
