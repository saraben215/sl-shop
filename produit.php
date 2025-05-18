<?php
require 'config.php';
$message = "";

// Ajouter un produit
if (isset($_POST['ajouter'])) {
    $nom = $_POST['nom'];
    $prix = $_POST['prix'];
    $description = $_POST['description'];
    $image = $_FILES['image']['name'];
    $categorie_id = $_POST['categorie_id'];

    $target = "uploads/" . basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target);

    $stmt = $pdo->prepare("INSERT INTO produits (nom, prix, description, categorie_id, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nom, $prix, $description, $categorie_id, $image]);
    $message = "Produit ajouté avec succès.";
}

// Supprimer un produit
if (isset($_GET['supprimer'])) {
    $id = $_GET['supprimer'];

    // Supprimer d'abord les promotions associées à ce produit
    $pdo->prepare("DELETE FROM promotions WHERE produit_id = ?")->execute([$id]);

    // Ensuite supprimer le produit
    $pdo->prepare("DELETE FROM produits WHERE id = ?")->execute([$id]);

    $message = "Produit supprimé.";
}


// Modifier un produit
if (isset($_POST['modifier'])) {
    $id = $_POST['id'];
    $nom = $_POST['nom'];
    $prix = $_POST['prix'];
    $description = $_POST['description'];
    $categorie_id = $_POST['categorie_id'];
    $image = $_FILES['image']['name'];

    if ($image) {
        $target = "uploads/" . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        $stmt = $pdo->prepare("UPDATE produits SET nom=?, prix=?, description=?, categorie_id=?, image=? WHERE id=?");
        $stmt->execute([$nom, $prix, $description, $categorie_id, $image, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE produits SET nom=?, prix=?, description=?, categorie_id=? WHERE id=?");
        $stmt->execute([$nom, $prix, $description, $categorie_id, $id]);
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Requête modifiée : jointure pour récupérer le nom de la catégorie
$produits = $pdo->query("
    SELECT p.*, c.nom AS nom_categorie 
    FROM produits p 
    JOIN categories c ON p.categorie_id = c.id
")->fetchAll(PDO::FETCH_ASSOC);

// Récupération des catégories pour le formulaire
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

// Si modification d'un produit
$editProduit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editProduit = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Produits</title>
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

        #paramSubMenu a {
            font-size: 14px;
            color: #dcefff;
            margin-left: 10px;
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
            max-width: 800px;
            margin: 0 auto;
            padding: 15px 25px;
            background: #ffffff;
            animation: fadeIn 0.8s ease-in-out;
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
    <h2>Gestion des Produits</h2>

    <div class="card mb-4 card-form">
        <div class="card-body">
            <h4><?= $editProduit ? "Modifier un produit" : "Ajouter un produit" ?></h4>
            <form method="POST" enctype="multipart/form-data">
                <?php if ($editProduit): ?>
                    <input type="hidden" name="id" value="<?= $editProduit['id'] ?>">
                <?php endif; ?>
                <div class="mb-3">
                    <label>Nom</label>
                    <input type="text" class="form-control" name="nom" value="<?= $editProduit['nom'] ?? '' ?>" required>
                </div>
                <div class="mb-3">
                    <label>Prix</label>
                    <input type="number" step="0.01" class="form-control" name="prix" value="<?= $editProduit['prix'] ?? '' ?>" required>
                </div>
                <div class="mb-3">
                    <label>Description</label>
                    <textarea class="form-control" name="description" required><?= $editProduit['description'] ?? '' ?></textarea>
                </div>
                <div class="mb-3">
                    <label>Catégorie</label>
                    <select class="form-select" name="categorie_id" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= (isset($editProduit) && $editProduit['categorie_id'] == $cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Image</label>
                    <input type="file" class="form-control" name="image" accept="image/*">
                </div>
                <button type="submit" name="<?= $editProduit ? 'modifier' : 'ajouter' ?>" class="btn <?= $editProduit ? 'btn-modifier' : 'btn-ajouter' ?>">
                    <?= $editProduit ? "Modifier le produit" : "Ajouter le produit" ?>
                </button>
            </form>
        </div>
    </div>

    <div class="card card-table">
        <div class="card-body">
            <h4>Liste des Produits</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prix</th>
                        <th>Description</th>
                        <th>Catégorie</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produits as $produit): ?>
                        <tr>
                            <td><?= $produit['id'] ?></td>
                            <td><?= htmlspecialchars($produit['nom']) ?></td>
                            <td><?= htmlspecialchars($produit['prix']) ?> DA</td>
                            <td><?= htmlspecialchars($produit['description']) ?></td>
                            <td><?= htmlspecialchars($produit['nom_categorie']) ?></td>
                            <td>
                                <img src="uploads/<?= htmlspecialchars($produit['image']) ?>" alt="<?= htmlspecialchars($produit['nom']) ?>" width="50">
                            </td>
                            <td>
                                <a href="?edit=<?= $produit['id'] ?>" class="btn btn-sm btn-primary">Modifier</a>
                                <a href="?supprimer=<?= $produit['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
 
    // Basculement entre le mode sombre et le mode clair
document.getElementById('toggleDark').addEventListener('click', function() {
    document.body.classList.toggle('dark-mode');  // Ajoute ou retire la classe 'dark-mode' du body
});

// Fonction pour afficher ou masquer le sous-menu des paramètres
function toggleSubMenu() {
    const subMenu = document.getElementById('paramSubMenu');
    const link = document.getElementById('paramLink');
    if (subMenu.style.display === 'none' || subMenu.style.display === '') {
        subMenu.style.display = 'block'; // Affiche le sous-menu
        link.innerHTML = 'Paramètres ⯅'; // Change l'icône pour refléter que le menu est ouvert
    } else {
        subMenu.style.display = 'none'; // Masque le sous-menu
        link.innerHTML = 'Paramètres ⯆'; // Change l'icône pour refléter que le menu est fermé
    }
}

// Fonction pour confirmer la suppression d'un produit
function confirmDelete(productId) {
    const confirmation = confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');
    if (confirmation) {
        // Effectuer l'action de suppression ici, par exemple, rediriger vers une URL de suppression
        window.location.href = `?supprimer=${productId}`;
    }
}

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

