<?php
// Inclusion du fichier d'initialisation
require_once ROOT_PATH . '/includes/init.php';

// Récupération de l'ID du véhicule depuis l'URL
$vehicleId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Traitement de l'ajout au panier
if (isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    $type = isset($_POST['type']) ? $_POST['type'] : 'achat';
    if (addToCart($vehicleId, $type)) {
        $_SESSION['success_message'] = 'Le véhicule a été ajouté au panier avec succès.';
    } else {
        $_SESSION['error_message'] = 'Une erreur est survenue lors de l\'ajout au panier.';
    }
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Récupération des données du véhicule
$vehicle = getVehicleById($vehicleId);

// Si le véhicule n'existe pas, redirection vers la page 404
if (!$vehicle) {
    header('Location: ' . url('pages/errors/404.php'));
    exit;
}

// Variables de la page
$pageTitle = $vehicle['marque'] . ' ' . $vehicle['modele'];
$pageDescription = "Découvrez les caractéristiques détaillées de la " . $vehicle['marque'] . ' ' . $vehicle['modele'] . ' ' . $vehicle['annee'];
$currentPage = 'vehicule';
$additionalCss = ['css/vehicule.css'];
$additionalJs = ['js/vehicule.js'];

// Début de la mise en mémoire tampon
ob_start();
?>

<div class="container">
    <!-- Messages de notification -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success_message'] ?>
            <?php unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error_message'] ?>
            <?php unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <!-- Bouton retour -->
    <div class="back-btn-container">
        <a href="<?= url('catalogue/') ?>" class="btn btn-link">
            <i class="fas fa-chevron-left"></i>
            Retour au catalogue
        </a>
    </div>

    <div class="car-details">
        <div class="car-image-container">
            <button class="favorite-btn" id="favoriteBtn">
                <i class="fas fa-heart"></i>
            </button>

            <div class="image-gallery">
                <div class="gallery-main">
                    <div class="gallery-slider">
                        <!-- Image principale par défaut -->
                        <img src="<?= asset('images/vehicules/' . $vehicleId . '/main.jpg') ?>" 
                             alt="<?= htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']) ?>" 
                             class="gallery-img active"
                             onerror="this.src='<?= asset('images/vehicules/default-car.jpg') ?>'">
                        <!-- Images supplémentaires -->
                        <img src="<?= asset('images/vehicules/' . $vehicleId . '/angle1.jpg') ?>" 
                             alt="<?= htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']) ?> - Vue latérale" 
                             class="gallery-img"
                             onerror="this.src='<?= asset('images/vehicules/default-car.jpg') ?>'">
                        <img src="<?= asset('images/vehicules/' . $vehicleId . '/angle2.jpg') ?>" 
                             alt="<?= htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']) ?> - Vue arrière" 
                             class="gallery-img"
                             onerror="this.src='<?= asset('images/vehicules/default-car.jpg') ?>'">
                        <img src="<?= asset('images/vehicules/' . $vehicleId . '/interior.jpg') ?>" 
                             alt="<?= htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']) ?> - Intérieur" 
                             class="gallery-img"
                             onerror="this.src='<?= asset('images/vehicules/default-car.jpg') ?>'">
                    </div>

                    <button class="gallery-nav prev" id="prevBtn">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="gallery-nav next" id="nextBtn">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>

                <div class="gallery-thumbs">
                    <div class="thumb active" data-index="0">
                        <img src="<?= asset('images/vehicules/' . $vehicleId . '/main.jpg') ?>" 
                             alt="Miniature 1"
                             onerror="this.src='<?= asset('images/vehicules/default-car.jpg') ?>'">
                    </div>
                    <div class="thumb" data-index="1">
                        <img src="<?= asset('images/vehicules/' . $vehicleId . '/angle1.jpg') ?>" 
                             alt="Miniature 2"
                             onerror="this.src='<?= asset('images/vehicules/default-car.jpg') ?>'">
                    </div>
                    <div class="thumb" data-index="2">
                        <img src="<?= asset('images/vehicules/' . $vehicleId . '/angle2.jpg') ?>" 
                             alt="Miniature 3"
                             onerror="this.src='<?= asset('images/vehicules/default-car.jpg') ?>'">
                    </div>
                    <div class="thumb" data-index="3">
                        <img src="<?= asset('images/vehicules/' . $vehicleId . '/interior.jpg') ?>" 
                             alt="Miniature 4"
                             onerror="this.src='<?= asset('images/vehicules/default-car.jpg') ?>'">
                    </div>
                </div>
            </div>
        </div>

        <div class="car-info">
            <div class="tag <?= $vehicle['stock'] > 0 ? 'available' : 'unavailable' ?>">
                <?= $vehicle['stock'] > 0 ? 'Disponible' : 'Indisponible' ?>
            </div>
            
            <h1><?= htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']) ?></h1>
            <h2 class="price"><?= number_format($vehicle['prix'], 2, ',', ' ') ?> €</h2>
            
            <?php if ($vehicle['disponible_location'] && $vehicle['tarif_location_journalier'] > 0): ?>
            <div class="rental-price">
                Location: <?= number_format($vehicle['tarif_location_journalier'], 2, ',', ' ') ?> € / jour
            </div>
            <?php endif; ?>

            <div class="dropdowns">
                <div class="dropdown-container">
                    <div class="label">Stock disponible</div>
                    <div class="value"><?= $vehicle['stock'] ?> unité(s)</div>
                </div>

                <div class="dropdown-container">
                    <div class="label">Année</div>
                    <div class="value"><?= $vehicle['annee'] ?></div>
                </div>
            </div>

            <div class="button-group">
                <?php if ($vehicle['stock'] > 0): ?>
                <form method="post" class="cart-form">
                    <input type="hidden" name="action" value="add_to_cart">
                    <input type="hidden" name="type" value="achat">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-shopping-cart"></i>
                        Ajouter à l'achat
                    </button>
                </form>
                <?php endif; ?>
                
                <?php if ($vehicle['disponible_location'] && $vehicle['stock'] > 0): ?>
                <form method="post" class="cart-form">
                    <input type="hidden" name="action" value="add_to_cart">
                    <input type="hidden" name="type" value="location">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-key"></i>
                        Ajouter à la location
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="characteristics">
        <h2>Caractéristiques</h2>
        <div class="characteristics-grid">
            <div class="characteristic-item">
                <div class="characteristic-label">Marque</div>
                <div class="characteristic-value"><?= htmlspecialchars($vehicle['marque']) ?></div>
            </div>

            <div class="characteristic-item">
                <div class="characteristic-label">Modèle</div>
                <div class="characteristic-value"><?= htmlspecialchars($vehicle['modele']) ?></div>
            </div>

            <div class="characteristic-item">
                <div class="characteristic-label">Année</div>
                <div class="characteristic-value"><?= $vehicle['annee'] ?></div>
            </div>

            <div class="characteristic-item">
                <div class="characteristic-label">Kilométrage</div>
                <div class="characteristic-value"><?= number_format($vehicle['kilometrage'], 0, ',', ' ') ?> km</div>
            </div>

            <div class="characteristic-item">
                <div class="characteristic-label">Carburant</div>
                <div class="characteristic-value"><?= htmlspecialchars(ucfirst($vehicle['carburant'])) ?></div>
            </div>

            <div class="characteristic-item">
                <div class="characteristic-label">Transmission</div>
                <div class="characteristic-value"><?= htmlspecialchars(ucfirst($vehicle['transmission'])) ?></div>
            </div>
        </div>
    </div>

    <div class="vehicle-description">
        <h2>Description du véhicule</h2>
        <?php if (isset($vehicle['description']) && !empty($vehicle['description'])): ?>
            <p><?= nl2br(htmlspecialchars($vehicle['description'])) ?></p>
        <?php else: ?>
            <p>Cette <?= htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']) ?> <?= $vehicle['annee'] ?> représente l'alliance parfaite entre performance et élégance. Dotée d'un moteur <?= strtolower($vehicle['carburant']) ?> et d'une transmission <?= strtolower($vehicle['transmission']) ?>, elle offre une expérience de conduite exceptionnelle.</p>
            
            <p>Avec son kilométrage de <?= number_format($vehicle['kilometrage'], 0, ',', ' ') ?> km, ce véhicule a été parfaitement entretenu et se trouve dans un excellent état. Son design moderne et ses lignes épurées ne manqueront pas d'attirer l'attention, tandis que ses équipements de dernière génération garantissent confort et sécurité optimaux.</p>
            
            <p>Que ce soit pour un usage quotidien ou des trajets plus longs, cette voiture saura répondre à toutes vos attentes. Son excellent rapport qualité-prix en fait une opportunité à ne pas manquer pour les amateurs de belles automobiles.</p>
        <?php endif; ?>
    </div>
</div>

<?php
// Récupération du contenu mis en mémoire tampon
$pageContent = ob_get_clean();

// Inclusion du template
require_once ROOT_PATH . '/includes/template.php';
?> 