<?php
require 'config.php';
$message = "";

// Ajouter une promotion
if (isset($_POST['ajouter'])) {
    $produit_id = $_POST['produit_id'];
    $reduction = $_POST['reduction'];

    // Vérifier si une promotion existe déjà pour ce produit
    $check = $pdo->prepare("SELECT * FROM promotions WHERE produit_id = ?");
    $check->execute([$produit_id]);
    if ($check->rowCount() > 0) {
        $stmt = $pdo->prepare("UPDATE promotions SET reduction = ? WHERE produit_id = ?");
        $stmt->execute([$reduction, $produit_id]);
        $message = "Promotion mise à jour.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO promotions (produit_id, reduction) VALUES (?, ?)");
        $stmt->execute([$produit_id, $reduction]);
        $message = "Promotion ajoutée.";
    }
}

// Supprimer une promotion
if (isset($_GET['supprimer'])) {
    $id = $_GET['supprimer'];
    $pdo->prepare("DELETE FROM promotions WHERE id = ?")->execute([$id]);
    $message = "Promotion supprimée.";
}

// Récupération des produits et promotions
$produits = $pdo->query("SELECT * FROM produits")->fetchAll(PDO::FETCH_ASSOC);
$promotions = $pdo->query("
    SELECT promotions.id, produits.nom AS produit_nom, produits.prix, promotions.reduction 
    FROM promotions 
    JOIN produits ON promotions.produit_id = produits.id
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Promotions</title>
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
        .form-control, .form-select {
            border-radius: 8px;
        }
        .btn-ajouter {
            background-color: #0077cc;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 25px;
            margin-top: 10px;
        }
        .btn-ajouter:hover {
            background-color: #005fa3;
        }
        table {
            font-size: 14px;
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
    <h2>Gestion des Promotions</h2>

    <div class="card mb-4">
        <form method="POST">
            <div class="mb-3">
                <label>Produit</label>
                <select class="form-select" name="produit_id" required>
                    <?php foreach ($produits as $produit): ?>
                        <option value="<?= $produit['id'] ?>"><?= htmlspecialchars($produit['nom']) ?> - <?= number_format($produit['prix'], 2) ?> €</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label>Réduction (%)</label>
                <input type="number" class="form-control" name="reduction" min="1" max="99" required>
            </div>
            <button type="submit" name="ajouter" class="btn-ajouter">Ajouter / Modifier</button>
        </form>
    </div>

    <div class="card">
        <h5 class="mb-3">Promotions Actuelles</h5>
        <table class="table table-bordered text-center table-light">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Prix Original</th>
                    <th>Réduction</th>
                    <th>Prix Après Réduction</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($promotions as $promo): 
                    $prix_final = $promo['prix'] * (1 - $promo['reduction'] / 100);
                ?>
                <tr>
                    <td><?= htmlspecialchars($promo['produit_nom']) ?></td>
                    <td><?= number_format($promo['prix'], 2) ?> €</td>
                    <td><?= $promo['reduction'] ?>%</td>
                    <td><strong><?= number_format($prix_final, 2) ?> €</strong></td>
                    <td><a href="?supprimer=<?= $promo['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cette promotion ?')">Supprimer</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
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
