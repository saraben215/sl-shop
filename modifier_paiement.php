<?php
require 'config.php';
$message = "";

// Récupérer les paramètres de la base de données
$stmt = $pdo->prepare("SELECT * FROM parametres WHERE nom IN ('paiement_methode', 'paiement_details')");
$stmt->execute();
$params = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['modifier'])) {
    $methode = $_POST['methode'];
    $details = $_POST['details'];

    // Mise à jour des paramètres
    $stmt = $pdo->prepare("UPDATE parametres SET valeur = ? WHERE nom = 'paiement_methode'");
    $stmt->execute([$methode]);

    $stmt = $pdo->prepare("UPDATE parametres SET valeur = ? WHERE nom = 'paiement_details'");
    $stmt->execute([$details]);

    $message = "✅ Méthode de paiement mise à jour avec succès.";
}

// Initialisation des valeurs par défaut si elles existent
$methode = $params[0]['valeur'] ?? '';
$details = $params[1]['valeur'] ?? '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Paiement</title>
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
    <h2 class="text-center text-primary mb-4">Modifier la méthode de paiement</h2>
    <div class="card">
        <form method="POST">
            <div class="mb-3">
                <label for="methode" class="form-label">Méthode</label>
                <input type="text" class="form-control" id="methode" name="methode" value="<?= htmlspecialchars($methode) ?>" required>
            </div>
            <div class="mb-3">
                <label for="details" class="form-label">Détails</label>
                <textarea class="form-control" id="details" name="details" rows="4" required><?= htmlspecialchars($details) ?></textarea>
            </div>
            <button type="submit" name="modifier" class="btn-modifier">Enregistrer</button>
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
