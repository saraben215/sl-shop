<?php
require 'config.php';
$message = "";

// Ajouter ou modifier un utilisateur
if (isset($_POST['ajouter']) || isset($_POST['modifier'])) {
    $name = $_POST['name']; // Update this to 'name' instead of 'nom'
    $email = $_POST['email'];
    $role = $_POST['role'];

    if (isset($_POST['ajouter'])) {
        // Vérifier si l'email existe déjà
        $check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetchColumn() > 0) {
            $message = "❌ Cet email est déjà utilisé.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, role) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $role]);
            $message = "✅ Utilisateur ajouté avec succès.";
        }
    }
    
    if (isset($_POST['modifier'])) {
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        $name = $_POST['name'];
        $email = $_POST['email'];
        $role = $_POST['role'];
    
        // Vérifier si un autre utilisateur a déjà ce mail
        $check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND id != ?");
        $check->execute([$email, $id]);
    
        if ($check->fetchColumn() > 0) {
            $message = "❌ Cet email est déjà utilisé par un autre utilisateur.";
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, role=? WHERE id=?");
            $stmt->execute([$name, $email, $role, $id]);
            $message = "✅ Utilisateur modifié avec succès.";
        }
    }
    
    
}

// Supprimer un utilisateur
if (isset($_GET['supprimer'])) {
    $id = $_GET['supprimer'];
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
    $message = "Utilisateur supprimé.";
}

// Liste des utilisateurs
$utilisateurs = $pdo->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);

$roles = ['Admin', 'Client']; // Exemple de rôles

// Édition de l'utilisateur
$editUtilisateur = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editUtilisateur = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Activer / Désactiver un utilisateur
if (isset($_GET['desactiver'])) {
    $stmt = $pdo->prepare("UPDATE users SET status = 'inactive' WHERE id = ?");
    $stmt->execute([$_GET['desactiver']]);
}
if (isset($_GET['activer'])) {
    $stmt = $pdo->prepare("UPDATE users SET status = 'active' WHERE id = ?");
    $stmt->execute([$_GET['activer']]);
}

// Récupérer tous les utilisateurs
$utilisateurs = $pdo->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);

// Séparer selon le rôle
$admins = array_filter($utilisateurs, fn($u) => $u['role'] == 'admin');
$users = array_filter($utilisateurs, fn($u) => $u['role'] == 'client');

$editUtilisateur = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editUtilisateur = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Utilisateurs</title>
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
    <h2>Gestion des Utilisateurs</h2>

    <div class="card mb-4 card-form">
        <div class="card-body">
            <h4><?= $editUtilisateur ? "Modifier un utilisateur" : "Ajouter un utilisateur" ?></h4>
            <form method="POST">
                <?php if ($editUtilisateur): ?>
                    <input type="hidden" name="id" value="<?= $editUtilisateur['id'] ?>">
                <?php endif; ?>
                <div class="mb-3">
                    <label>Nom</label>
                    <input type="text" class="form-control" name="name" value="<?= $editUtilisateur['name'] ?? '' ?>" required>
                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" value="<?= $editUtilisateur['email'] ?? '' ?>" required>
                </div>
                <div class="mb-3">
                    <label>Rôle</label>
                    <select class="form-select" name="role" required>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role ?>" <?= isset($editUtilisateur) && $editUtilisateur['role'] == $role ? 'selected' : '' ?>>
                                <?= htmlspecialchars($role) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" name="<?= $editUtilisateur ? 'modifier' : 'ajouter' ?>" class="<?= $editUtilisateur ? 'btn-modifier' : 'btn-ajouter' ?>">
                    <?= $editUtilisateur ? 'Modifier' : 'Ajouter' ?>
                </button>
            </form>
        </div>
    </div>

    <!-- Liste des utilisateurs -->
    <div class="card card-table">
        <div class="card-body">
            <h4>Liste des utilisateurs</h4>
            <table class="table table-bordered text-center mt-3 table-light">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $admin): ?>
                        <tr>
                            <td><?= htmlspecialchars($admin['name']) ?></td>
                            <td><?= htmlspecialchars($admin['email']) ?></td>
                            <td><?= htmlspecialchars($admin['role']) ?></td>
                            <td>
                                <?php if ($admin['status'] == 'active'): ?>
                                    <span class="badge bg-success">Actif</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inactif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="?edit=<?= $admin['id'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                                <a href="?supprimer=<?= $admin['id'] ?>" onclick="return confirm('Êtes-vous sûr ?')" class="btn btn-danger btn-sm">Supprimer</a>
                                <?php if ($admin['status'] == 'active'): ?>
                                    <a href="?desactiver=<?= $admin['id'] ?>" class="btn btn-secondary btn-sm">Désactiver</a>
                                <?php else: ?>
                                    <a href="?activer=<?= $admin['id'] ?>" class="btn btn-success btn-sm">Activer</a>
                                <?php endif; ?>
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