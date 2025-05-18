<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'phpmailer/src/PHPMailer.php';
    require 'phpmailer/src/SMTP.php';
    require 'phpmailer/src/Exception.php';

    $email = $_POST['email'];

    // Connexion à la base de données
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=ecommerce", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Générer un token sécurisé
            $token = bin2hex(random_bytes(32));
            $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

            // Enregistrer le token et l’expiration dans la base
            $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expiry = ? WHERE email = ?");
            $stmt->execute([$token, $expiry, $email]);

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'sarabensehil23@gmail.com'; // ton email
            $mail->Password = 'mhsh usqv spcp efzn';   // ton mot de passe Gmail App
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('sarabensehil23@gmail.com', 'eCommerce');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Réinitialisation du mot de passe';
            $mail->Body = '<p>Bonjour,</p>
                           <p>Cliquez sur le lien suivant pour réinitialiser votre mot de passe :</p>
                           <p><a href="http://localhost/projet_ecomerce/reset_password.php?token=' . $token . '">Réinitialiser le mot de passe</a></p>
                           <p>Ce lien expirera dans 1 heure.</p>';

            $mail->send();
            $success = "Le lien de réinitialisation a été envoyé avec succès.";
        } else {
            $error = "Adresse e-mail non trouvée.";
        }
    } catch (Exception $e) {
        $error = "Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialisation du mot de passe</title>
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
        }

        .login-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 0 20px rgba(0,0,0,0.15);
            animation: slideFade 0.5s ease;
        }

        .login-card h2 {
            font-family: 'Roboto Slab', serif;
            font-size: 2rem;
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
        }

        .btn-primary:hover {
            background-color: #4078a0;
        }

        .alert {
            margin-bottom: 15px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        @keyframes slideFade {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
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
    <h2>Réinitialisation du mot de passe</h2>

    <?php if (!empty($success)) : ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)) : ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">Adresse Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="admin@example.com" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Envoyer le lien</button>
    </form>

    <div class="text-center mt-3">
        <a href="login.php" style="color: #5a92b7;">Retour à la connexion</a>
    </div>
</div>

</body>
</html>
