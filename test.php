<?php
$chemin = __DIR__ . '/phpmailer/src/PHPMailer.php';

if (file_exists($chemin)) {
    echo "✅ PHPMailer est bien installé !";
} else {
    echo "❌ PHPMailer n'est PAS trouvé ! Vérifie son emplacement.";
}
?>
