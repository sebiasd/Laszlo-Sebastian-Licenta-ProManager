<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT id, password FROM users WHERE email = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                session_start();
                $_SESSION['user_id'] = $id;
                header("Location: dashboard.php"); // Redirecționare corectă
                exit();
            } else {
                echo "Parolă invalidă.";
            }
        } else {
            echo "Niciun utilizator găsit cu acest email.";
        }
        $stmt->close();
    } else {
        echo "Eroare: " . $conn->error;
    }
    $conn->close();
}
?>
