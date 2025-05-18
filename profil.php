<?php
require 'config.php';
session_start();

// Simuler un utilisateur connecté (à adapter à votre système d'authentification)
$user_id = $_SESSION['user_id'] ?? 1;

$message = "";

// Récupérer les infos de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Mettre à jour les infos
if (isset($_POST['update'])) {
    $name = $_POST['nom'];
    $email = $_POST['email'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['password'];

    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
    $stmt->execute([$name, $email, $password, $user_id]);

    $message = "Informations mises à jour.";
    $user['name'] = $name;
    $user['email'] = $email;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil Administrateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4faff;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
        }
        .dark-mode {
            background-color: #1c1c2b;
            color: #ffffff;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #5a92b7;
            position: fixed;
            left: 0;
            top: 0;
            padding: 30px 20px;
            box-shadow: 2px 0 8px rgba(0,0,0,0.1);
        }
        .sidebar h2 {
            font-size: 24px;
            color: #ffffff;
            margin-bottom: 30px;
        }
        .sidebar a {
            display: block;
            color: #e0f0ff;
            text-decoration: none;
            margin-bottom: 15px;
            font-weight: 500;
        }
        .sidebar a:hover {
            color: #ffffff;
        }
        .content {
            margin-left: 260px;
            padding: 40px;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #005fa3;
        }
        .card {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 8px 18px rgba(0, 119, 204, 0.2);
        }
        .form-control {
            border-radius: 8px;
        }
        .btn-ajouter {
            background-color: #0077cc;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 25px;
        }
        .btn-ajouter:hover {
            background-color: #005fa3;
        }
        .dark-mode .card {
            background: #2c2c3c;
            color: #fff;
        }
        .dark-mode .btn-ajouter {
            background-color: #33aaff;
        }
        #toggleDark {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #0077cc;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 8px;
            cursor: pointer;
        }
        #paramSubMenu a {
            font-size: 14px;
            color: #dcefff;
            margin-left: 10px;
        }
    </style>
</head>
<body>

<?php if ($message): ?>
    <div class="alert alert-success text-center"><?= $message ?></div>
<?php endif; ?>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="profil.php">Mon profil</a>
    <a href="dashbord.php">Tableau de bord</a>
    <a href="produit.php">Produits</a>
    <a href="catégorie.php">Catégories</a>
    <a href="commande.php">Commande</a>
    <a href="liste_produits.php" target="_blank">Voir la boutique</a>
    <a href="gestion_utilisateurs.php">Gérer les Utilisateurs</a>
    <a href="javascript:void(0);" onclick="toggleSubMenu()" id="paramLink">Paramètres ⯆</a>
    <div id="paramSubMenu" style="display: none; padding-left: 15px;">
        <a href="parametres.php">Modifier les Paramètres</a>
        <a href="paiement.php">Méthodes de Paiement</a>
        <a href="livraison.php">Options de Livraison</a>
        <a href="promotions.php">Ajouter des Promotions</a>
    </div>
    <a href="logout.php">Se Déconnecter</a>
</div>

<button id="toggleDark">🌙</button>

<div class="content">
    <h2>Mon Profil</h2>

    <div class="card">
        <form method="POST">
            <div class="mb-3">
                <label>Nom</label>
                <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div class="mb-3">
                <label>Mot de passe (laisser vide pour ne pas modifier)</label>
                <input type="password" name="password" class="form-control">
            </div>
            <button type="submit" name="update" class="btn-ajouter">Mettre à jour</button>
        </form>
    </div>
</div>

<script>
    document.getElementById("toggleDark").addEventListener("click", function () {
        document.body.classList.toggle("dark-mode");
    });

    function toggleSubMenu() {
        const menu = document.getElementById('paramSubMenu');
        const link = document.getElementById('paramLink');
        if (menu.style.display === "none") {
            menu.style.display = "block";
            link.innerHTML = 'Paramètres ⯅';
        } else {
            menu.style.display = "none";
            link.innerHTML = 'Paramètres ⯆';
        }
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
