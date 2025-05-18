<?php
require 'config.php';
$message = "";

// Ajouter ou modifier une commande
if (isset($_POST['ajouter']) || isset($_POST['modifier'])) {
    $client_id = $_POST['client_id'];
    $statut = $_POST['statut'];
    $total = $_POST['total'];

    if (isset($_POST['ajouter'])) {
        $stmt = $pdo->prepare("INSERT INTO commandes (client_id, statut, total) VALUES (?, ?, ?)");
        $stmt->execute([$client_id, $statut, $total]);
        $message = "Commande ajoutée avec succès.";
    }

    if (isset($_POST['modifier'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("UPDATE commandes SET client_id=?, statut=?, total=? WHERE id=?");
        $stmt->execute([$client_id, $statut, $total, $id]);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Supprimer une commande
if (isset($_GET['supprimer'])) {
    $id = $_GET['supprimer'];
    $pdo->prepare("DELETE FROM commandes WHERE id = ?")->execute([$id]);
    $message = "Commande supprimée.";
}

// Liste des commandes avec jointure pour récupérer les informations du client
$stmt = $pdo->prepare("SELECT commandes.id, commandes.statut, commandes.total, commandes.created_at, commandes.updated_at, users.name AS nom_client 
                       FROM commandes
                       JOIN users ON commandes.client_id = users.id");
$stmt->execute();
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Édition d'une commande
$editCommande = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM commandes WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editCommande = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Charger les clients
$clients = $pdo->query("SELECT id, name FROM users WHERE role = 'client'")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Commandes</title>
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

        h2, h4 {
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
        }

        .card-form {
            max-width: 600px;
            margin: 0 auto;
            padding: 15px 25px;
        }

        .card-table {
            max-width: 1000px;
            margin: 0 auto;
            padding: 15px 25px;
            background: #ffffff;
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-control, .form-select {
            border-radius: 8px;
        }

        .btn-ajouter, .btn-modifier {
            background-color: #0077cc;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 25px;
            display: block;
            margin: 20px auto 0;
        }

        .btn-ajouter:hover, .btn-modifier:hover {
            background-color: #005fa3;
        }

        table {
            font-size: 14px;
        }

        .dark-mode .card,
        .dark-mode .card-table {
            background: #2c2c3c;
            color: #fff;
        }

        .dark-mode .btn-ajouter,
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

        .table thead {
            background-color:rgb(253, 253, 253);
            color: white;
        }

        .dark-mode .table thead {
            background-color: #005fa3;
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
    <h2>Gestion des Commandes</h2>

    <div class="card mb-4 card-form">
        <div class="card-body">
            <h4><?= $editCommande ? "Modifier une commande" : "Ajouter une commande" ?></h4>
            <form method="POST">
                <?php if ($editCommande): ?>
                    <input type="hidden" name="id" value="<?= $editCommande['id'] ?>">
                <?php endif; ?>
                <div class="mb-3">
                    <label>Client</label>
                    <select class="form-select" name="client_id" required>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?= $client['id'] ?>" <?= isset($editCommande) && $editCommande['client_id'] == $client['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($client['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Statut</label>
                    <select class="form-select" name="statut" required>
                        <option value="en attente" <?= isset($editCommande) && $editCommande['statut'] == 'en attente' ? 'selected' : '' ?>>En attente</option>
                        <option value="validée" <?= isset($editCommande) && $editCommande['statut'] == 'validée' ? 'selected' : '' ?>>Validée</option>
                        <option value="expédiée" <?= isset($editCommande) && $editCommande['statut'] == 'expédiée' ? 'selected' : '' ?>>Expédiée</option>
                        <option value="livrée" <?= isset($editCommande) && $editCommande['statut'] == 'livrée' ? 'selected' : '' ?>>Livrée</option>
                        <option value="annulée" <?= isset($editCommande) && $editCommande['statut'] == 'annulée' ? 'selected' : '' ?>>Annulée</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Total</label>
                    <input type="number" step="0.01" class="form-control" name="total" value="<?= $editCommande['total'] ?? '' ?>" required>
                </div>
                <button type="submit" name="<?= $editCommande ? 'modifier' : 'ajouter' ?>" class="<?= $editCommande ? 'btn-modifier' : 'btn-ajouter' ?>">
                    <?= $editCommande ? 'Modifier' : 'Ajouter' ?>
                </button>
            </form>
        </div>
    </div>

    <div class="card card-table">
        <h4>Liste des Commandes</h4>
        <table class="table table-bordered text-center mt-3">
            <thead>
                <tr>
                    <th>Nom du client</th>
                    <th>Statut</th>
                    <th>Total</th>
                    <th>Date création</th>
                    <th>Dernière modification</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($commandes as $commande): ?>
                    <tr>
                        <td><?= htmlspecialchars($commande['nom_client']) ?></td>
                        <td><?= htmlspecialchars($commande['statut']) ?></td>
                        <td><?= htmlspecialchars($commande['total']) ?> €</td>
                        <td><?= htmlspecialchars($commande['created_at']) ?></td>
                        <td><?= htmlspecialchars($commande['updated_at']) ?></td>
                        <td>
                            <a href="?edit=<?= $commande['id'] ?>" class="btn btn-sm btn-primary">Modifier</a>
                            <a href="?supprimer=<?= $commande['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cette commande ?')">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function toggleSubMenu() {
        var submenu = document.getElementById('paramSubMenu');
        submenu.style.display = (submenu.style.display === "none" || submenu.style.display === "") ? "block" : "none";
    }

    document.getElementById("toggleDark").addEventListener("click", function() {
        document.body.classList.toggle("dark-mode");
    });
</script>
</body>
</html>
