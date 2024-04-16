<?php
// Mật khẩu mới
$password = "password123";

// Mã hóa mật khẩu
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Simulate user input
$userInput = "password123";

// Kiểm tra mật khẩu
if (password_verify($userInput, $hashedPassword)) {
    echo "Mật khẩu khớp!";
} else {
    echo "Mật khẩu không khớp!";
}
?>
