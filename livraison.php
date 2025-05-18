<?php
require 'config.php';
$message = "";

// Traitement de l'ajout ou de la modification d'une option de livraison
if (isset($_POST['modifier'])) {
    $mode = $_POST['mode'];
    $duree = $_POST['duree'];
    $prix = $_POST['prix'];

    // Modifier une option de livraison existante
    if (isset($_POST['id']) && $_POST['id'] != "") {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("UPDATE livraison SET mode = ?, duree = ?, prix = ? WHERE id = ?");
        $stmt->execute([$mode, $duree, $prix, $id]);
        $message = "✅ L'option de livraison a été mise à jour avec succès.";
    }
    // Ajouter une nouvelle option de livraison
    else {
        $stmt = $pdo->prepare("INSERT INTO livraison (mode, duree, prix) VALUES (?, ?, ?)");
        $stmt->execute([$mode, $duree, $prix]);
        $message = "✅ Une nouvelle option de livraison a été ajoutée avec succès.";
    }
}

// Suppression d'une option de livraison
if (isset($_GET['supprimer'])) {
    $id = $_GET['supprimer'];
    $stmt = $pdo->prepare("DELETE FROM livraison WHERE id = ?");
    $stmt->execute([$id]);
    $message = "✅ L'option de livraison a été supprimée avec succès.";
}

// Activation/Désactivation d'une option de livraison
if (isset($_GET['activer']) || isset($_GET['desactiver'])) {
    $id = $_GET['activer'] ?? $_GET['desactiver'];
    $actif = isset($_GET['activer']) ? 1 : 0;
    $stmt = $pdo->prepare("UPDATE livraison SET actif = ? WHERE id = ?");
    $stmt->execute([$actif, $id]);
    $message = $actif ? "✅ L'option de livraison a été activée." : "✅ L'option de livraison a été désactivée.";
}

// Récupérer toutes les options de livraison
$stmt = $pdo->query("SELECT * FROM livraison");
$livraisons = $stmt->fetchAll();

// Si une modification d'une option de livraison est demandée
if (isset($_GET['modifier'])) {
    $id = $_GET['modifier'];
    $stmt = $pdo->prepare("SELECT * FROM livraison WHERE id = ?");
    $stmt->execute([$id]);
    $livraison = $stmt->fetch();
    if (!$livraison) {
        $message = "❌ L'option de livraison n'existe pas.";
        header("Location: livraison.php");
        exit();
    }
    $mode = $livraison['mode'];
    $duree = $livraison['duree'];
    $prix = $livraison['prix'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Options de Livraison</title>
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
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #005fa3;
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
    <h2 class="text-center text-primary mb-4">Gestion des Options de Livraison</h2>

    <!-- Formulaire d'ajout ou de modification -->
    <div class="card">
        <form method="POST">
            <input type="hidden" name="id" value="<?= isset($livraison) ? $livraison['id'] : '' ?>">
            <div class="mb-3">
                <label for="mode" class="form-label">Mode de Livraison</label>
                <input type="text" class="form-control" id="mode" name="mode" value="<?= htmlspecialchars($mode ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label for="duree" class="form-label">Durée</label>
                <input type="text" class="form-control" id="duree" name="duree" value="<?= htmlspecialchars($duree ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label for="prix" class="form-label">Prix</label>
                <input type="number" class="form-control" id="prix" name="prix" value="<?= htmlspecialchars($prix ?? '') ?>" required>
            </div>
            <button type="submit" name="modifier" class="btn-modifier">Enregistrer</button>
        </form>
    </div>

    <h2 class="text-center text-primary mt-5">Liste des Options de Livraison</h2>
    <table class="table table-striped mt-4">
        <thead>
            <tr>
                <th>#</th>
                <th>Mode</th>
                <th>Durée</th>
                <th>Prix</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($livraisons as $livraison): ?>
            <tr>
                <td><?= $livraison['id'] ?></td>
                <td><?= htmlspecialchars($livraison['mode']) ?></td>
                <td><?= htmlspecialchars($livraison['duree']) ?></td>
                <td><?= htmlspecialchars($livraison['prix']) ?> DA</td>
                <td><?= $livraison['actif'] ? 'Actif' : 'Désactivé' ?></td>
                <td>
                    <a href="livraison.php?modifier=<?= $livraison['id'] ?>" class="btn btn-primary btn-sm">Modifier</a>
                    <a href="livraison.php?activer=<?= $livraison['id'] ?>" class="btn btn-success btn-sm">Activer</a>
                    <a href="livraison.php?desactiver=<?= $livraison['id'] ?>" class="btn btn-warning btn-sm">Désactiver</a>
                    <a href="livraison.php?supprimer=<?= $livraison['id'] ?>" class="btn btn-danger btn-sm">Supprimer</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
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
