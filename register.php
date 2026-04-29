<?php
include 'db.php';

$errors = [];

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $name = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $contact = trim($_POST['contact'] ?? '');
    $pass = trim($_POST['password'] ?? '');
    $confirm = trim($_POST['confirm'] ?? '');

    // VALIDATION
    if($name === "" || !preg_match('/^[A-Za-z ]+$/', $name)){
        $errors['name'] = "Please enter your full name";
    }

    if($username === ""){
        $errors['username'] = "Please enter your username";
    }

    if($email === ""){
        $errors['email'] = "Please enter your email address";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors['email'] = "Please enter a valid email address";
    }

    if($contact === "" || !preg_match('/^[0-9]+$/', $contact)){
        $errors['contact'] = "Please enter numbers only";
    }

    if(strlen($pass) < 6){
        $errors['password'] = "Password must be at least 6 characters";
    }

    if($pass !== $confirm){
        $errors['confirm'] = "Passwords do not match";
    }

    // IF NO ERRORS → SAVE
    if(empty($errors)){

        $hashed = password_hash($pass, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users(name,username,email,contact,password) VALUES(?,?,?,?,?)");
        $stmt->bind_param("sssss", $name, $username, $email, $contact, $hashed);

        if($stmt->execute()){
            header("Location: login.php");
            exit();
        } else {
            $errors['general'] = "Registration failed (maybe duplicate email)";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="login-body">

<div class="login-card">

    <h2>Register</h2>

    <!-- GENERAL ERROR -->
    <?php if(isset($errors['general'])): ?>
        <p class="error"><?php echo $errors['general']; ?></p>
    <?php endif; ?>

    <form method="POST">

        <!-- NAME -->
        <input type="text" name="name" placeholder="Full Name"
        value="<?php echo $_POST['name'] ?? '' ?>">
        <?php if(isset($errors['name'])) echo "<p class='input-error-msg'>{$errors['name']}</p>"; ?>

        <!-- USERNAME -->
        <input type="text" name="username" placeholder="Username"
        value="<?php echo $_POST['username'] ?? '' ?>">
        <?php if(isset($errors['username'])) echo "<p class='input-error-msg'>{$errors['username']}</p>"; ?>

        <!-- EMAIL -->
        <input type="email" name="email" placeholder="Email Address"
        value="<?php echo $_POST['email'] ?? '' ?>">
        <?php if(isset($errors['email'])) echo "<p class='input-error-msg'>{$errors['email']}</p>"; ?>

        <!-- CONTACT -->
        <input type="text" name="contact" placeholder="Contact Number"
        value="<?php echo $_POST['contact'] ?? '' ?>">
        <?php if(isset($errors['contact'])) echo "<p class='input-error-msg'>{$errors['contact']}</p>"; ?>

        <!-- PASSWORD -->
        <input type="password" name="password" placeholder="Password">
        <?php if(isset($errors['password'])) echo "<p class='input-error-msg'>{$errors['password']}</p>"; ?>

        <!-- CONFIRM -->
        <input type="password" name="confirm" placeholder="Confirm Password">
        <?php if(isset($errors['confirm'])) echo "<p class='input-error-msg'>{$errors['confirm']}</p>"; ?>

        <button type="submit">Register</button>

    </form>

</div>

</body>
</html>