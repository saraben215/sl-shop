<?php
session_start();
require 'config.php'; // Include database connection config

$error = ''; // Initialize error variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    try {
        // Database connection using PDO
        $pdo = new PDO('mysql:host=localhost;dbname=ecommerce;charset=utf8', 'root', ''); // Adjust connection settings
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // SQL query to fetch the user with the role 'admin'
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // User authenticated, store session variables
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_email'] = $user['email'];
            $_SESSION['admin_name'] = $user['name'];
            header("Location: dashbord.php"); // Redirect to dashboard
            exit;
        } else {
            // If credentials are incorrect
            $error = "Email ou mot de passe incorrect.";
        }
    } catch (PDOException $e) {
        // Handle database connection error
        $error = "Erreur de connexion à la base de données : " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500&family=Parisienne&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #d0e8f2;
            font-family: 'Quicksand', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            transition: background-color 0.3s ease;
        }

        .login-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 70px;
            width: 100%;
            max-width: 700px; /* Increased max-width */
            box-shadow: 0 0 20px rgba(0,0,0,0.15);
            animation: slideFade 0.5s ease;
        }

        .login-card h2 {
            font-family: 'Roboto Slab', serif;
            font-size: 2.5rem; /* Increased font size */
            text-align: center;
            color: #5a92b7;
            margin-bottom: 30px;
        }

        .form-control {
            border-radius: 40px;
        }

        .btn-primary {
            background-color: #5a92b7;
            border: none;
            border-radius: 12px;
            font-weight: bold;
            transition: 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #5a92b7;
        }

        .error-msg {
            color: red;
            margin-top: 15px;
            text-align: center;
        }

        @keyframes slideFade {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Dark mode */
        body.dark-mode {
            background-color: #2f3640;
            color: #f5f6fa;
        }

        .login-card.dark-mode {
            background: #353b48;
            color: #f5f6fa;
        }

        .form-control.dark-mode {
            background-color: rgb(255, 255, 255);
            color: #fff;
            border: none;
        }

        .btn-primary.dark-mode {
            background-color: #5a92b7;
        }

        /* Bubbles effect */
        .bubbles {
            position: fixed;
            width: 100%;
            height: 100%;
            overflow: hidden;
            top: 0;
            left: 0;
            z-index: 1;
            pointer-events: none;
        }

        .bubbles span {
            position: absolute;
            display: block;
            width: 25px;
            height: 25px;
            background: #0077cc;
            border-radius: 50%;
            animation: bubble 20s linear infinite;
            bottom: -150px;
            opacity: 0.3;
        }

        .bubbles span:nth-child(1) { left: 10%; width: 40px; height: 40px; animation-delay: 0s; }
        .bubbles span:nth-child(2) { left: 20%; width: 20px; height: 20px; animation-delay: 2s; animation-duration: 12s; }
        .bubbles span:nth-child(3) { left: 35%; animation-delay: 4s; }
        .bubbles span:nth-child(4) { left: 50%; width: 60px; height: 60px; animation-delay: 0s; animation-duration: 18s; }
        .bubbles span:nth-child(5) { left: 65%; animation-delay: 3s; }
        .bubbles span:nth-child(6) { left: 75%; width: 15px; height: 15px; animation-delay: 5s; }
        .bubbles span:nth-child(7) { left: 85%; width: 30px; height: 30px; animation-delay: 7s; }

        @keyframes bubble {
            0% { transform: translateY(0) scale(1); opacity: 0.3; }
            50% { opacity: 0.6; }
            100% { transform: translateY(-1000px) scale(1.5); opacity: 0; }
        }

        .login-container {
            size: 100px;
            position: relative;
            z-index: 10;
        }
    </style>
</head>
<body>
    <div class="bubbles">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
    </div>
    <div class="login-container">
        <div class="login-card">
            <h2>Connexion Admin</h2>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="email" class="form-label">Adresse Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="admin@example.com" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="********" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Se connecter</button>
            </form>
            <div class="text-center mt-3">
                <a href="forgot_password.php" class="text-decoration-none" style="color: #5a92b7;">Mot de passe oublié ?</a>
                <a href="register.php" class="text-decoration-none" style="color: #5a92b7;"> cree un compte?</a>
            </div>
            <?php if ($error) { echo "<p class='error-msg'>$error</p>"; } ?>
        </div>
    </div>

    <!-- Dark mode toggle button -->
    <button id="dark-mode-toggle" class="btn btn-secondary" style="position: absolute; top: 20px; right: 20px;">
        🌙
    </button>

    <script>
        // Toggle dark mode
        const darkModeToggle = document.getElementById('dark-mode-toggle');
        darkModeToggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            document.querySelector('.login-card').classList.toggle('dark-mode');
            document.querySelector('.form-control').classList.toggle('dark-mode');
            darkModeToggle.classList.toggle('dark-mode');
        });
    </script>
</body>
</html>
