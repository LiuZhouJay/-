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

// 获取当前数据库中的记录数
$sql = "SELECT COUNT(*) AS count FROM material_codes";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $count = $row['count'];
    $recordNumber = str_pad($count + 1, 4, '0', STR_PAD_LEFT); // 生成四位数的备案号
    echo json_encode(['status' => 'success', 'recordNumber' => $recordNumber]);
} else {
    echo json_encode(['status' => 'error', 'message' => '获取记录数时出错']);
}

$conn->close();
?>
