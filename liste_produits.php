<?php
require 'config.php';



$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

$produits_par_categorie = [];

foreach ($categories as $cat) {
    $stmt = $pdo->prepare("
        SELECT p.*, pr.reduction 
        FROM produits p
        LEFT JOIN promotions pr ON p.id = pr.produit_id
        WHERE p.categorie_id = ?
    ");
    $stmt->execute([$cat['id']]);
    $produits_par_categorie[$cat['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil | Boutique</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Parisienne&family=Quicksand:wght@400;700;900&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet" />

    <style>
    :root {
        --bg-light: #f0f8ff; /* Bleu clair pour le fond */
        --bg-dark: #001f3d; /* Bleu foncé pour le mode sombre */
        --text-light: #ffffff; /* Texte clair */
        --text-dark: #dcdcdc; /* Texte sombre en mode sombre */
        --accent: #007bff; /* Bleu pour les accents */
        --gradient-start: #5a92b7; /* Bleu dégradé */
        --gradient-end: #5a92b7;
        --card-dark: #2c3e50; /* Card background foncé */
    }

    body {
        background-color: var(--bg-light);
        color: var(--text-dark);
        font-family: 'Quicksand', sans-serif; /* Utilisation de la police Quicksand */
        transition: background-color 0.4s ease, color 0.4s ease;
    }
    
    h1, h2 {
        font-size: 50px;
        color: #007bff; /* Bleu pour les titres */
        font-family: 'Parisienne', cursive;
    }

    h5 {
        font-size: 30px;
        color: #007bff; /* Bleu pour les sous-titres */
        font-family: 'Parisienne', cursive;
    }

    .limited-title {
        color: #007bff;
        font-family: 'Parisienne', cursive;
    }

    .dark-mode {
        background-color: var(--bg-dark);
        color: var(--text-dark);
    }

    .toggle-dark {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
    }

    .toggle-dark button {
        border-radius: 50px;
        padding: 10px 20px;
        font-size: 18px;
        background-color: #5a92b7; /* Bleu pour le bouton */
        color: #ffffff;
    }

    .hero-banner {
        background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
        border-radius: 30px;
        padding: 60px 20px;
        position: relative;
        margin-bottom: 60px;
        overflow: hidden;
    }

    .hero-banner::before {
        content: '';
        position: absolute;
        top: -30%;
        right: -30%;
        width: 400px;
        height: 400px;
        background:rgb(240, 251, 255);
        border-radius: 100%;
        animation: float 6s ease-in-out infinite;
        z-index: 0;
    }

    @keyframes float {
        0% { transform: translateY(0); }
        50% { transform: translateY(20px); }
        100% { transform: translateY(0); }
    }

    .hero-banner .text-white {
        position: relative;
        z-index: 1;
    }

    .limited-title {
        font-size: 5rem;
        font-weight: 700;
        color:rgb(0, 200, 255);
        text-shadow: 0 0 10px rgba(0, 4, 255, 0.6);
        animation: shimmer 3s infinite alternate;
    }

    @keyframes shimmer {
        0% {
            text-shadow: 0 0 5px rgb(243, 245, 255), 0 0 10px rgb(182, 199, 255);
        }
        100% {
            text-shadow: 0 0 25px rgb(0, 123, 255), 0 0 35px rgb(0, 41, 82);
        }
    }

    .hero-banner .lead {
        font-size: 2rem;
        color: #001f3d;
    }

    .product-card {
        border: none;
        width: 335px;
        font-size: 20px;
        border-radius: 40px;
        box-shadow: 0 4px 15px #007bff;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 20px rgb(0, 123, 255);
    }

    .category-circle img {
        box-shadow: 0 4px 15px rgb(0, 123, 255);
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
    }

    .category-circle img:hover {
        transform: scale(1.1);
    }

    .section-title {
        font-weight: 700;
        margin-bottom: 30px;
        color: #007bff;
    }

    .btn-outline-warning {
        border-color: #007bff;
        border-radius: 25px;
        padding: 10px 20px;
        font-weight: bold;
        color: #007bff;
    }

    .btn-outline-warning:hover {
        background-color: #007bff;
        color: white;
    }

    .product-img {
        border-top-left-radius: 40px;
        border-top-right-radius: 40px;
        object-fit: cover;
        height: 250px;
        width: 100%;
    }
</style>

</head>
<body>

<!-- Toggle Dark Mode -->
<div class="toggle-dark">
    <button class="btn btn-outline-warning" id="toggleDark">🌙</button>
</div>

<!-- Hero Section -->
<section class="hero-banner container" data-aos="fade-up">
    <div class="text-center text-white">
        <h1 class="limited-title">Limited Edition</h1>
        <p class="lead">For Queen Styles Fashion</p>
        <p class="fs-5">Découvrez notre collection exclusive pour femmes modernes et élégantes.</p>
    </div>
</section>

<!-- Categories -->
<section class="py-5 text-center" data-aos="fade-up">
    <div class="container">
        <h2 class="section-title">Best For Your Categories</h2>
        <div class="d-flex flex-wrap justify-content-center gap-4">
            <?php foreach ($categories as $cat): ?>
                <?php
                    $imgPath = !empty($cat['image']) && file_exists("uploads/" . $cat['image']) ? "uploads/" . $cat['image'] : "https://via.placeholder.com/120";
                ?>
                <div class="category-circle text-center" data-aos="zoom-in-up">
                    <a href="#" class="text-decoration-none text-dark">
                        <img src="<?= $imgPath ?>" alt="<?= htmlspecialchars($cat['nom']) ?>">
                        <p class="mt-2"><?= htmlspecialchars($cat['nom']) ?></p>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Products par Catégorie -->
<?php foreach ($categories as $categorie): ?>
    <?php 
        $cat_id = $categorie['id']; 
        $produits = isset($produits_par_categorie[$cat_id]) ? $produits_par_categorie[$cat_id] : [];
    ?>
    <section class="mb-5">
        <h2 class="mb-3"><?= htmlspecialchars($categorie['nom']) ?></h2>
        <div class="row">
            <?php if (empty($produits)): ?>
                <p class="text-muted">Aucun produit dans cette catégorie.</p>
            <?php else: ?>
                <?php foreach ($produits as $produit): 
                    $imageName = htmlspecialchars($produit['image'] ?? '');
                    $imagePath = "uploads/" . $imageName;
                    if (!file_exists($imagePath) || empty($imageName)) {
                        $imagePath = "https://via.placeholder.com/600x300?text=Pas+de+photo";
                    }

                    $prix_original = $produit['prix'];
                    $reduction = $produit['reduction'] ?? 0;
                    $prix_final = $prix_original;
                    if ($reduction > 0) {
                        $prix_final = $prix_original - ($prix_original * $reduction / 100);
                    }
                ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="<?= $imagePath ?>" class="card-img-top" alt="Image produit">
                        <div class="card-body text-center">
                            <h5 class="card-title"><?= htmlspecialchars($produit['nom']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($produit['description']) ?></p>
                            <?php if ($reduction > 0): ?>
                                <div>
                                    <span class="text-decoration-line-through text-muted"><?= number_format($prix_original, 2) ?> DA</span>
                                    <span class="fw-bold text-success ms-2"><?= number_format($prix_final, 2) ?> DA</span>
                                </div>
                                <small class="text-danger">-<?= $reduction ?>% de réduction</small>
                            <?php else: ?>
                                <div><strong><?= number_format($prix_original, 2) ?> DA</strong></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
<?php endforeach; ?>




<!-- Admin Buttons -->
<section class="py-5 bg-light" data-aos="fade-up">
    <div class="container text-center">
        <a href="dashbord.php" class="btn btn-primary btn-lg">Accéder au Panneau Admin</a>
    </div>
</section>

<!-- Bootstrap JS, AOS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init();

    // Toggle dark mode
    document.getElementById('toggleDark').addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');
    });
</script>

</body>
</html>
