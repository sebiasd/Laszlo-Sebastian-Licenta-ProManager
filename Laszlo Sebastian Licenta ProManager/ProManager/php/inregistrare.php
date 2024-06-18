<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $company = $_POST['company'];

    $sql = "INSERT INTO users (first_name, last_name, email, password, company) VALUES (?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssss", $first_name, $last_name, $email, $password, $company);
        if ($stmt->execute()) {
            header("Location: ../index.html?inregistrare=succes");
        } else {
            echo "Eroare: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Eroare: " . $conn->error;
    }
    $conn->close();
}
?>
