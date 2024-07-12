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

// 从数据库中删除数据
$sql = "DELETE FROM material_codes WHERE code = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $code);
if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => '编码已成功删除']);
} else {
    echo json_encode(['status' => 'error', 'message' => '删除编码时出错: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>