<?php
session_start();
include 'db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){

        $user = $result->fetch_assoc();

        if(password_verify($password, $user['password'])){

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            if($user['role'] === "admin"){
                header("Location: admin/dashboard.php");
            } else {
                header("Location: client/dashboard.php");
            }

            exit();

        } else {
            echo "Wrong password";
        }

    } else {
        echo "Email not found";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="login-body">

<div class="login-card">

    <h2>Login</h2>

    <form method="POST">

        <?php if(!empty($error)) { ?>
            <div class="error"><?php echo $error; ?></div>
        <?php } ?>

        <input type="email" name="email" placeholder="Email">
        <input type="password" name="password" placeholder="Password">

        <button type="submit">Login</button>

    </form>

   <p style="margin-top: 18px; font-size: 16px;">
    Don't have an account? 
    <a href="register.php" style="color:#6a5ae0; text-decoration: none; font-weight: 500;">
        Register now
    </a>
</p>

<p style="margin-top: 10px; font-size: 15px;">
    <a href="#" style="color:#6a5ae0; text-decoration: none; font-weight: 500;">
        Forgot Password?
    </a>
</p>
</div>

</body>
</html>