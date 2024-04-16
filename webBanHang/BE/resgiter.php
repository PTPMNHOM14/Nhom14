<?php 
header('Content-Type: application/json');
require 'connectDb.php';


$sql = "SELECT * FROM users";
$result = $conn->query($sql);

// Tạo một mảng để lưu trữ dữ liệu
$data = array();

// Đọc kết quả truy vấn và thêm vào mảng
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Đóng kết nối
$conn->close();

// Trả về dữ liệu dưới dạng JSON
header('Content-Type: application/json');
echo json_encode($data);
?>
