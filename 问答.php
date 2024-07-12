下面是我的涉及到的四个文件，你再看看：
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




下面是我的search_code.php文件：
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
    $properties = array_map(function($prop) {
        return $prop . ': ' . (strpos($prop, ':') !== false ? substr($prop, strpos($prop, ':') + 1) : '');
    }, $properties);

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





下面是get_record_number.php文件

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





下面是delete_code.php文件
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




这是现在我点击搜索后在搜索结果中展示的效果：
编码: T37001:刘 类型: 电阻 子类型: 贴片电阻

属性:

0: 刘
1: 2024-07-11
2: 900欧
3: ±20%
4: 600w
5: X7R
6: 0603
7: 50V
8: 23



下面是我想要的效果：
编码: T380213456 类型: 电阻 子类型: 贴片电阻

属性:

申请人：刘  
申请时间20240711
阻值: 900欧
精度: ±20%
功率：600W
温度系数：X7R
封装尺寸: 0603
最大工作电压：50V
绝缘电阻性能：23


现在我想在当我点击搜索出现的搜素结果中，每个属性值前面加上对应的属性名称，就比如申请人：刘永良，阻值：100欧，封装信息：0603其余的也类似。谢谢，这可能涉及到多个文件的改动，请务必仔细。

这可能涉及到编码的保存文件以及搜索文件的改动毕竟要保存属性名称，搜索时后端获取到属性名称和属性值也要向前端返回属性名称和属性值，然后前端获取到属性名称和属性值进行显示。这涉及到多个文件的改动，我这里说的属性名称就是像阻值以及封装信息这一类，属性值就是100欧、0603这些。