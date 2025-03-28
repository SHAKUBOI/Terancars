<?php
// Inclusion du fichier d'initialisation
require_once ROOT_PATH . '/includes/init.php';

// Variables de la page
$pageTitle = "TeranCar - Vente et Location de Véhicules";
$pageDescription = "Découvrez notre sélection de véhicules de qualité pour la vente et la location.";
$currentPage = 'home';

// Fonction de conversion FCFA → EUR
function convertToEUR($prixFCFA)
{
    $rate = 655; // 1 EUR = 655 FCFA
    return number_format($prixFCFA / $rate, 2, ',', ' ') . ' €';
}

// Début de la mise en mémoire tampon
ob_start();
?>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<!-- Section Hero -->
<section class="hero">
    <div class="hero-content">
        <h1>Bienvenue chez TeranCar</h1>
        <p class="hero-subtitle">Votre partenaire de confiance pour l'achat et la location de véhicules</p>
        <div class="hero-buttons">
            <a href="<?= url('catalogue/') ?>" class="btn btn-primary">
                <i class="fas fa-car"></i>
                Voir nos véhicules
            </a>
            <a href="<?= url('contact/') ?>" class="btn btn-secondary">
                <i class="fas fa-envelope"></i>
                Nous contacter
            </a>
        </div>
    </div>
</section>

<!-- Section Marques Populaires -->
<section class="popular-brands">
    <div class="container">
        <h2 class="section-title">Nos marques populaires</h2>
        <div class="brands-grid">
            <?php
            $query = "SELECT DISTINCT marque FROM vehicules ORDER BY marque";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $marques = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $logosPaths = [
                'Toyota' => 'toyota.png',
                'BMW' => 'bmw.png',
                'Mercedes' => 'mercedes.png',
                'Audi' => 'audi.png',
                'Tesla' => 'tesla.png',
                'Volkswagen' => 'volkswagen.png',
                'Ford' => 'ford.png',
                'Peugeot' => 'peugeot.png',
                'Citroën' => 'citroen.png',
                'Hyundai' => 'hyundai.png',
                'Renault' => 'renault.png'
            ];

            foreach ($marques as $marque) {
                $logoPath = isset($logosPaths[$marque])
                    ? asset('images/brands/' . $logosPaths[$marque])
                    : asset('images/brands/default-brand.png');
            ?>
                <a href="<?= url('catalogue/?marque=' . urlencode($marque)) ?>" class="brand-logo">
                    <img src="<?= $logoPath ?>"
                        alt="Logo <?= htmlspecialchars($marque) ?>"
                        title="Voir les véhicules <?= htmlspecialchars($marque) ?>"
                        onerror="this.src='<?= asset('images/brands/default-brand.png') ?>'">
                </a>
            <?php } ?>
        </div>
    </div>
</section>

<!-- Section Véhicules -->
<section class="vehicles">
    <div class="container">
        <h2 class="section-title">Nos véhicules</h2>
        <div class="vehicles-grid">
            <?php foreach (getVehicles(8) as $vehicule): ?>
                <?php
                $imageUrl = getVehicleMainImage($vehicule['id_vehicule']);
                ?>
                <div class="vehicle-card">
                    <img src="<?= $imageUrl ?>"
                        alt="<?= htmlspecialchars($vehicule['nom'] ?? 'Voiture') ?>">
                    <h3><?= htmlspecialchars($vehicule['nom']) ?></h3>
                    <p class="price">
                        <?= htmlspecialchars($vehicule['prix']) ?> FCFA
                        <span class="price-eur">(≈ <?= convertToEUR($vehicule['prix']) ?>)</span>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php
// Récupération du contenu mis en mémoire tampon
$pageContent = ob_get_clean();

// Inclusion du template
require_once ROOT_PATH . '/includes/template.php';
?>