<?php
require 'config.php';
$message = "";

// Récupérer les paramètres existants

$stmt = $pdo->query("SELECT * FROM parametres WHERE id = 1");
$parametre = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérification pour éviter les erreurs si aucun résultat n'est retourné
if (!$parametre) {
    $message = "⚠️ Aucun paramètre trouvé. Veuillez insérer une ligne dans la table.";
    $parametre = [
        'nom_site' => '',
        'currency' => '',
        'email_contact' => '',
        'telephone' => ''
    ];
}

if (isset($_POST['modifier'])) {
    // Traitement de la modification des paramètres
    $site_name = $_POST['nom_site'];
    $currency = $_POST['currency'];
    $email_contact = $_POST['email_contact'];
    $phone_contact = $_POST['telephone'];

    // Mise à jour des paramètres dans la base de données
    $stmt = $pdo->prepare("UPDATE parametres SET nom_site = ?, currency = ?, email_contact = ?, telephone = ? WHERE id = 1");
    $stmt->execute([$site_name, $currency, $email_contact, $phone_contact]);
    $message = "✅ Les paramètres ont été mis à jour avec succès.";
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier les Paramètres</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4faff;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            transition: 0.3s;
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
            transition: all 0.3s ease;
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
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            padding: 20px;
            max-width: 600px;
            margin: auto;
        }

        .form-control, .form-select {
            border-radius: 8px;
        }

        .btn-modifier {
            background-color: #0077cc;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 25px;
            display: block;
            margin: 20px auto 0;
        }

        .btn-modifier:hover {
            background-color: #005fa3;
        }

        .dark-mode .card {
            background: #2c2c3c;
            color: #fff;
        }

        .dark-mode .btn-modifier {
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
    <h2 class="text-center text-primary mb-4">Modifier les Paramètres</h2>

    <!-- Formulaire de modification des paramètres -->
    <div class="card">
        <form method="POST">
            <div class="mb-3">
                <label for="site_name" class="form-label">Nom du Site</label>
                <input type="text" class="form-control" id="site_name" name="nom_site" value="<?= htmlspecialchars($parametre['nom_site']) ?>" required>

            </div>
            <div class="mb-3">
                <label for="currency" class="form-label">Monnaie</label>
                <input type="text" class="form-control" id="currency" name="currency" value="<?= htmlspecialchars($parametre['currency']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="email_contact" class="form-label">Email de Contact</label>
                <input type="email" class="form-control" id="email_contact" name="email_contact" value="<?= htmlspecialchars($parametre['email_contact']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone_contact" class="form-label">Numéro de Téléphone</label>
                <input type="tel" class="form-control" id="phone_contact" name="telephone" value="<?= htmlspecialchars($parametre['telephone']) ?>" required>

            </div>
            <button type="submit" name="modifier" class="btn-modifier">Enregistrer les Modifications</button>
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
