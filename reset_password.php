<?php
require 'config.php';
$success = '';
$error = '';
$showForm = false;

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_expiry > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        $showForm = true;

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE id = ?");
            $stmt->execute([$new_password, $user['id']]);
            $success = "✅ Mot de passe changé avec succès. Vous pouvez maintenant vous connecter.";
            $showForm = false;
        }
    } else {
        $error = "❌ Le lien est invalide ou a expiré.";
    }
} else {
    $error = "❌ Aucun jeton de réinitialisation fourni.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialiser le mot de passe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
        }

        .login-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 0 20px rgba(0,0,0,0.15);
        }

        .login-card h2 {
            font-family: 'Roboto Slab', serif;
            font-size: 2.5rem;
            text-align: center;
            color: #5a92b7;
            margin-bottom: 30px;
        }

        .form-control {
            border-radius: 12px;
        }

        .btn-primary {
            background-color: #5a92b7;
            border: none;
            border-radius: 12px;
            font-weight: bold;
            transition: 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #49729b;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .dark-mode {
            background-color: #2f3640;
            color: #f5f6fa;
        }

        .login-card.dark-mode {
            background: #353b48;
            color: #f5f6fa;
        }

        .form-control.dark-mode {
            background-color: #2d3a45;
            color: #fff;
            border: none;
        }

        .btn-primary.dark-mode {
            background-color: #5a92b7;
        } /* Bubbles effect */
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

        .login-card {
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
<div class="login-card">
    <h2>Réinitialiser le mot de passe</h2>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php elseif (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($showForm): ?>
        <form method="POST">
            <div class="mb-3">
                <label for="password" class="form-label">Nouveau mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Changer le mot de passe</button>
        </form>
    <?php endif; ?>

    <div class="text-center mt-3">
        <a href="login.php" style="color: #5a92b7;">Retour à la connexion</a>
    </div>
</div>

<!-- Dark mode toggle -->
<button id="dark-mode-toggle" class="btn btn-secondary" style="position: absolute; top: 20px; right: 20px;">🌙</button>

<script>
    const toggle = document.getElementById('dark-mode-toggle');
    toggle.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');
        document.querySelector('.login-card').classList.toggle('dark-mode');
        document.querySelectorAll('.form-control').forEach(el => el.classList.toggle('dark-mode'));
    });
</script>

</body>
</html>
