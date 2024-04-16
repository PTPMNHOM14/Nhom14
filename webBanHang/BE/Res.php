<?php
header('Content-Type: application/json');
require "connectDB.php";

// Lấy data y/c POST
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

$response = array();

if (
    isset($input["username"]) &&
    isset($input["email"]) &&
    isset($input["phone_number"]) &&
    isset($input["address"]) &&
    isset($input["password"]) &&
    !empty($input["username"]) &&
    !empty($input["email"]) &&
    !empty($input["phone_number"]) &&
    !empty($input["address"]) &&
    !empty($input["password"])
) {
    // Lấy dữ thông tin dữ liệu
    $username = $input["username"];
    $email = $input["email"];
    $phone_number = $input["phone_number"];
    $address = $input["address"];
    $password = $input["password"];
    //kiểm tra email
    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $response["message"] = "Email đã tồn tại";
    } else {
        $stmt = $conn->prepare("INSERT INTO user (username, email, phone_number, address, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $email, $phone_number, $address, $password);
        if ($stmt->execute()) {
            $response["success"] = true;
            $response["message"] = "Đăng ký thành công";
            $response["redirect"] = "login.html";
        } else {
            $response["success"] = false;
            $response["message"] = "Đã xảy ra lỗi";
        }
    }
    $stmt->close();
} else {
    $response["message"] = "Vui lòng điền đầy đủ thông tin";
}

$conn->close();
echo json_encode($response);
?>
