<?php
require 'config.php';
$message = "";

// Vérifier si la catégorie existe avant d'ajouter ou de modifier
if (isset($_POST['ajouter']) || isset($_POST['modifier'])) {
    $nom = $_POST['nom'];
    $image = $_FILES['image']['name'];

    // Vérification de l'existence de la catégorie
    if (isset($_POST['ajouter'])) {
        // Ajouter une catégorie
        $target = "uploads/" . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        $stmt = $pdo->prepare("INSERT INTO categories (nom, image) VALUES (?, ?)");
        $stmt->execute([$nom, $image]);
        $message = "Catégorie ajoutée avec succès.";
    }

    if (isset($_POST['modifier'])) {
        // Modifier une catégorie
        $id = $_POST['id'];
        if ($image) {
            $stmt = $pdo->prepare("UPDATE categories SET nom=?, image=? WHERE id=?");
            $stmt->execute([$nom, $image, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE categories SET nom=? WHERE id=?");
            $stmt->execute([$nom, $id]);
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Supprimer une catégorie
if (isset($_GET['supprimer'])) {
    $id = $_GET['supprimer'];
    $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);
    $message = "Catégorie supprimée.";
}

// Liste des catégories
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

// Édition de la catégorie
$editCategorie = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editCategorie = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Catégories</title>
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
    <h2>Gestion des Catégories</h2>

    <div class="card mb-4 card-form">
        <div class="card-body">
            <h4><?= $editCategorie ? "Modifier une catégorie" : "Ajouter une catégorie" ?></h4>
            <form method="POST" enctype="multipart/form-data">
                <?php if ($editCategorie): ?>
                    <input type="hidden" name="id" value="<?= $editCategorie['id'] ?>">
                <?php endif; ?>
                <div class="mb-3">
                    <label>Nom</label>
                    <input type="text" class="form-control" name="nom" value="<?= $editCategorie['nom'] ?? '' ?>" required>
                </div>
                <div class="mb-3">
                    <label>Image</label>
                    <input type="file" class="form-control" name="image">
                </div>
                <button type="submit" name="<?= $editCategorie ? 'modifier' : 'ajouter' ?>" class="<?= $editCategorie ? 'btn-modifier' : 'btn-ajouter' ?>">
                    <?= $editCategorie ? 'Modifier' : 'Ajouter' ?>
                </button>
            </form>
        </div>
    </div>

    <!-- Liste des catégories -->
    <div class="card card-table">
        <div class="card-body">
            <h4>Liste des catégories</h4>
            <table class="table table-bordered text-center mt-3 table-light">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $categorie): ?>
                        <tr>
                            <td><?= htmlspecialchars($categorie['nom']) ?></td>
                            <td><img src="uploads/<?= htmlspecialchars($categorie['image']) ?>" alt="<?= htmlspecialchars($categorie['nom']) ?>" width="50"></td>
                            <td>
                                <a href="?edit=<?= $categorie['id'] ?>" class="btn btn-sm btn-primary">Modifier</a>
                                <a href="?supprimer=<?= $categorie['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cette catégorie ?')">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
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
