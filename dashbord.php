<?php
require 'config.php';

$usersCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$produitsCount = $pdo->query("SELECT COUNT(*) FROM produits")->fetchColumn();
$commandesCount = $pdo->query("SELECT COUNT(*) FROM commandes")->fetchColumn();
$chiffreAffaires = $pdo->query("SELECT SUM(total) FROM commandes")->fetchColumn();
$adminCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
$clientCount = $usersCount - $adminCount;

$salesData = $pdo->query("SELECT MONTH(created_at) as month, SUM(total) as sales FROM commandes GROUP BY MONTH(created_at)")->fetchAll(PDO::FETCH_ASSOC);
$orderData = $pdo->query("SELECT MONTH(created_at) as month, COUNT(*) as orders FROM commandes GROUP BY MONTH(created_at)")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet" />
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
        }.card-form, .card-table {
    background: #ffffff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 8px 18px rgba(0, 119, 204, 0.2); 
    transition: all 0.3s ease-in-out;
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
    <h2>Dashboard</h2>

    <div class="row g-4 mb-4">
        <div class="col-md-3" data-aos="fade-up">
            <div class="card text-center">
                <h4>Utilisateurs</h4>
                <h3><?= $usersCount ?></h3>
            </div>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
            <div class="card text-center">
                <h4>Produits</h4>
                <h3><?= $produitsCount ?></h3>
            </div>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
            <div class="card text-center">
                <h4>Commandes</h4>
                <h3><?= $commandesCount ?></h3>
            </div>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
            <div class="card text-center">
                <h4>Chiffre d'Affaires</h4>
                <h3><?= number_format($chiffreAffaires, 2) ?> €</h3>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Répartition des utilisateurs -->
        <div class="col-md-4" data-aos="zoom-in">
            <div class="chart-container">
                <h4>Répartition des Utilisateurs</h4>
                <canvas id="rolesChart" height="160"></canvas>
            </div>
        </div>

        <!-- Ventes mensuelles -->
        <div class="col-md-4" data-aos="zoom-in">
            <div class="chart-container">
                <h4>Ventes Mensuelles</h4>
                <canvas id="salesChart" height="200"></canvas>
            </div>
        </div>

        <!-- Commandes mensuelles -->
        <div class="col-md-4" data-aos="zoom-in">
            <div class="chart-container">
                <h4>Commandes Mensuelles</h4>
                <canvas id="orderChart" height="200"></canvas>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
    AOS.init();

    const rolesChart = new Chart(document.getElementById('rolesChart'), {
        type: 'pie',
        data: {
            labels: ['Admin', 'Client'],
            datasets: [{
                data: [<?= $adminCount ?>, <?= $clientCount ?>],
                backgroundColor: ['#33aaff', '#ff88cc'],
                borderWidth: 1,
                radius: '50%',
                hoverOffset: 6
            }]
        },
        options: {
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    const salesChart = new Chart(document.getElementById('salesChart'), {
        type: 'line',
        data: {
            labels: [<?php foreach($salesData as $data) echo "'" . date('F', mktime(0,0,0, $data['month'], 10)) . "', "; ?>],
            datasets: [{
                label: 'Ventes mensuelles (€)',
                data: [<?php foreach($salesData as $data) echo $data['sales'] . ", "; ?>],
                borderColor: '#005fa3',
                tension: 0.3,
                fill: false,
                pointBackgroundColor: '#005fa3'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true, position: 'bottom' }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    const orderChart = new Chart(document.getElementById('orderChart'), {
        type: 'bar',
        data: {
            labels: [<?php foreach($orderData as $data) echo "'" . date('F', mktime(0,0,0, $data['month'], 10)) . "', "; ?>],
            datasets: [{
                label: 'Commandes mensuelles',
                data: [<?php foreach($orderData as $data) echo $data['orders'] . ", "; ?>],
                backgroundColor: '#88c0d0'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true, position: 'bottom' }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
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
    document.getElementById("toggleDark").addEventListener("click", function () {
        document.body.classList.toggle("dark-mode");
    });
</script>
</body>
</html>
