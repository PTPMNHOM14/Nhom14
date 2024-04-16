<?php
header('Content-Type: application/json');
require "config/dbconnect.php";
// Lấy dữ liệu yêu cầu POST
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

$response = array();
//var_dump($input);

if (
    isset($input["username"]) &&
    isset($input["password"]) &&
    !empty($input["username"]) &&
    !empty($input["password"])
) {
    // Lấy dữ liệu từ biến POST
    $username = $input["username"];
    $password = $input["password"];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Chỉ lấy thông tin của người dùng đầu tiên
        $user = $result->fetch_assoc();
        $db_password = $user["password"];
        if ($db_password == $password ) {
            $response["success"] = true;
            $response["message"] = "Đăng nhập thành công!";
            $response["data"] = array("username" => $username);
            $response["redirect"] = "index.php";

        } else {
            $response["success"] = false;
            $response["message"] = "Ten nguoi dung hoac mat khau khong dung";
        }
        $stmt->close();
    } else {
        $response["success"] = false;
        $response["message"] = "Tên người dùng không tồn tại!";
    }
} else {
    $response["success"] = false;
    $response["message"] = "Vui lòng nhập tên người dùng và mật khẩu!";
}

// Trả về dữ liệu dưới dạng JSON
echo json_encode($response);
?>
