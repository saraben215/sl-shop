<?php
require 'config.php';
$message = "";

// Traitement de l'ajout ou de la modification d'une méthode de paiement
if (isset($_POST['modifier'])) {
    $methode = $_POST['methode'];
    $details = $_POST['details'];

    // Modifier une méthode de paiement existante
    if (isset($_POST['id']) && $_POST['id'] != "") {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("UPDATE paiement SET methode = ?, details = ? WHERE id = ?");
        $stmt->execute([$methode, $details, $id]);
        $message = "✅ La méthode de paiement a été mise à jour avec succès.";
    }
    // Ajouter une nouvelle méthode de paiement
    else {
        $stmt = $pdo->prepare("INSERT INTO paiement (methode, details) VALUES (?, ?)");
        $stmt->execute([$methode, $details]);
        $message = "✅ Une nouvelle méthode de paiement a été ajoutée avec succès.";
    }
}

// Suppression d'une méthode de paiement
if (isset($_GET['supprimer'])) {
    $id = $_GET['supprimer'];
    $stmt = $pdo->prepare("DELETE FROM paiement WHERE id = ?");
    $stmt->execute([$id]);
    $message = "✅ La méthode de paiement a été supprimée avec succès.";
}

// Activation/Désactivation d'une méthode de paiement
if (isset($_GET['activer']) || isset($_GET['desactiver'])) {
    $id = $_GET['activer'] ?? $_GET['desactiver'];
    $actif = isset($_GET['activer']) ? 1 : 0;
    $stmt = $pdo->prepare("UPDATE paiement SET actif = ? WHERE id = ?");
    $stmt->execute([$actif, $id]);
    $message = $actif ? "✅ La méthode de paiement a été activée." : "✅ La méthode de paiement a été désactivée.";
}

// Récupérer toutes les méthodes de paiement
$stmt = $pdo->query("SELECT * FROM paiement");
$paiements = $stmt->fetchAll();

// Si une modification d'une méthode de paiement est demandée
if (isset($_GET['modifier'])) {
    $id = $_GET['modifier'];
    $stmt = $pdo->prepare("SELECT * FROM paiement WHERE id = ?");
    $stmt->execute([$id]);
    $paiement = $stmt->fetch();
    if (!$paiement) {
        $message = "❌ La méthode de paiement n'existe pas.";
        header("Location: paiement.php");
        exit();
    }
    $methode = $paiement['methode'];
    $details = $paiement['details'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Méthodes de Paiement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4faff;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            transition: 0.3s;
        }
         h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #005fa3;
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
    <h2 class="text-center text-primary mb-4">Gestion des Méthodes de Paiement</h2>

    <!-- Formulaire d'ajout ou de modification -->
    <div class="card">
        <form method="POST">
            <input type="hidden" name="id" value="<?= isset($paiement) ? $paiement['id'] : '' ?>">
            <div class="mb-3">
                <label for="methode" class="form-label">Méthode</label>
                <input type="text" class="form-control" id="methode" name="methode" value="<?= htmlspecialchars($methode ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label for="details" class="form-label">Détails</label>
                <textarea class="form-control" id="details" name="details" rows="4" required><?= htmlspecialchars($details ?? '') ?></textarea>
            </div>
            <button type="submit" name="modifier" class="btn-modifier">Enregistrer</button>
        </form>
    </div>

    <h2 class="text-center text-primary mt-5">Liste des Méthodes de Paiement</h2>
    <table class="table table-striped mt-4">
        <thead>
            <tr>
                <th>#</th>
                <th>Méthode</th>
                <th>Détails</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($paiements as $paiement): ?>
            <tr>
                <td><?= $paiement['id'] ?></td>
                <td><?= htmlspecialchars($paiement['methode']) ?></td>
                <td><?= htmlspecialchars($paiement['details']) ?></td>
                <td><?= $paiement['actif'] ? 'Actif' : 'Désactivé' ?></td>
                <td>
                    <a href="paiement.php?modifier=<?= $paiement['id'] ?>" class="btn btn-primary btn-sm">Modifier</a>
                    <a href="paiement.php?activer=<?= $paiement['id'] ?>" class="btn btn-success btn-sm">Activer</a>
                    <a href="paiement.php?desactiver=<?= $paiement['id'] ?>" class="btn btn-warning btn-sm">Désactiver</a>
                    <a href="paiement.php?supprimer=<?= $paiement['id'] ?>" class="btn btn-danger btn-sm">Supprimer</a>
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
