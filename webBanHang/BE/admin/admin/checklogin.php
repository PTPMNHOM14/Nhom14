<?php
// Kết nối đến cơ sở dữ liệu
require "config/dbconnect.php";

// Xác định kiểu dữ liệu của đầu ra là JSON
header('Content-Type: application/json');

// Lấy thông tin từ yêu cầu POST
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

// Khởi tạo một mảng để chứa phản hồi
$response = array();

// Kiểm tra xem đã nhận được dữ liệu yêu cầu POST chưa
if (isset($input["info"]) && !empty($input["info"])) {
    // Lấy thông tin từ yêu cầu
    $info = $input["info"];

    // Thực hiện kiểm tra đăng nhập ở đây
    // Ví dụ: Kiểm tra thông tin cookie hoặc session
    // Trong ví dụ này, giả sử cookie có tên 'info'
    if ($info == $_COOKIE['info']) {
        // Đăng nhập thành công
        $response["success"] = true;
        $response["message"] = "Đăng nhập thành công!";
        
        // Lấy thông tin người dùng từ cơ sở dữ liệu
        $username = getUsernameFromCookie($info); // Lấy username từ cookie
        $userData = getUserDataFromDatabase($username); // Lấy thông tin người dùng từ cơ sở dữ liệu
        $response["user"] = $userData;
    } else {
        // Đăng nhập không thành công
        $response["success"] = false;
        $response["message"] = "Thông tin đăng nhập không chính xác!";
    }
} else {
    // Thiếu thông tin đăng nhập từ yêu cầu POST
    $response["success"] = false;
    $response["message"] = "Thiếu thông tin đăng nhập!";
}

// Trả về phản hồi dưới dạng JSON
echo json_encode($response);

// Hàm để lấy thông tin người dùng từ cơ sở dữ liệu
function getUserDataFromDatabase($username) {
    global $conn; // Biến kết nối tới cơ sở dữ liệu

    // Chuẩn bị câu truy vấn SQL
    $query = "SELECT * FROM users WHERE username = ? LIMIT 1";

    // Sử dụng prepared statement để tránh các vấn đề liên quan đến SQL injection
    $stmt = $conn->prepare($query);

    // Bind giá trị vào câu truy vấn và thực thi
    $stmt->bind_param("s", $username);
    $stmt->execute();

    // Lấy kết quả trả về từ câu truy vấn
    $result = $stmt->get_result();

    // Kiểm tra xem có kết quả hay không
    if ($result->num_rows > 0) {
        // Lấy thông tin người dùng từ kết quả trả về
        $user = $result->fetch_assoc();
        return $user; // Trả về thông tin người dùng dưới dạng mảng
    } else {
        // Trả về null nếu không tìm thấy người dùng
        return null;
    }

    // Đóng prepared statement
    $stmt->close();
}

// Hàm để lấy username từ cookie
function getUsernameFromCookie($cookieInfo) {
    // Giả sử cookieInfo chứa thông tin người dùng đã được mã hóa
    // Giải mã cookieInfo và trích xuất username từ đó
    $decodedInfo = json_decode(base64_decode($cookieInfo), true);
    
    // Kiểm tra xem có tồn tại trường 'username' trong cookie hay không
    if (isset($decodedInfo['username'])) {
        return $decodedInfo['username']; // Trả về username từ cookie
    } else {
        return null; // Trả về null nếu không tìm thấy trường 'username' trong cookie
    }
}
?>
