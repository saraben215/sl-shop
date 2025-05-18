<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "L'email est invalide.";
    } elseif ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Cet email est déjà utilisé.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')");
            if ($stmt->execute([$name, $email, $hashed_password])) {
                $_SESSION['admin_id'] = $pdo->lastInsertId();
                $_SESSION['admin_email'] = $email;
                $_SESSION['admin_name'] = $name;
                header("Location: dashbord.php");
                exit();
            } else {
                $error = "Erreur lors de l'inscription.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un compte admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #d0e8f2;
            font-family: 'Poppins', sans-serif;
            transition: background-color 0.3s;
        }

        .dark-mode {
            background-color: #2b2b3a;
            color: #fff;
        }

        .form-container {
            max-width: 500px;
            margin: 50px auto;
            background-color: #fff;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .dark-mode .form-container {
            background-color: #3a3a4d;
            color: #fff;
        }

        h2 {
            font-family: 'Roboto Slab', serif;
            font-size: 2.5rem; /* Increased font size */
            text-align: center;
            color: #5a92b7;
            margin-bottom: 30px;
        }

        .form-control {
            border-radius: 10px;
        }

        .btn-register {
            background-color: #5a92b7;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 10px;
            transition: background-color 0.3s;
        }

        .btn-register:hover {
            background-color: #5a92b7;
        }

        .toggle-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #5a92b7;
            color: white;
            border: none;
            padding: 10px 14px;
            border-radius: 50%;
            font-size: 18px;
            cursor: pointer;
        }

        .dark-mode .toggle-btn {
            background-color: #c87f8f;
        }

        a {
           
            color: #5a92b7;
            
        }

        a:hover {
            text-decoration: underline;
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

        .form-container {
            size: 100px;
            position: relative;
            z-index: 10;
    max-width: 500px;
    margin: 100px auto 50px auto; /* top, right, bottom, left */
   

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

<div class="form-container">
    <h2>Créer un compte Admin</h2>

    <?php if (isset($error)) : ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="name" class="form-label">Nom</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Votre nom" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Adresse email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="ex: admin@email.com" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="••••••" required>
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirmer mot de passe</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="••••••" required>
        </div>
        <button type="submit" class="btn btn-register w-100">S'inscrire</button>
        <div class="text-center mt-3">
            <a href="login.php">Vous avez déjà un compte ? Se connecter</a>
        </div>
    </form>
</div>

<button class="toggle-btn" id="toggleDark"><i class="fas fa-moon"></i></button>

<script>
    const toggle = document.getElementById('toggleDark');
    toggle.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
