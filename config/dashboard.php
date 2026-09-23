<?php
if (!isset($_SESSION['utilisateur_id'])) {
    redirect('utilisateur/connexion');
}
// === DÉTECTION DE LA PAGE ACTUELLE ===
$current_url = $_SERVER['REQUEST_URI'];

// === DÉTECTION DE LA PAGE ACTUELLE ===
$current_url = $_SERVER['REQUEST_URI'];
$current_path = trim(parse_url($current_url, PHP_URL_PATH), '/');

// Liste des pages pour l'activation du menu
$active_pages = [
    'utilisateur/dashboard'      => 'dashboard',
    'vente/index'                => 'vente',
    'produit/liste'              => 'produits',
    'taxe/enregistrement'        => 'taxes',
    'categorie/liste'            => 'categories',
    'fournisseur/liste'          => 'fournisseurs',
    'utilisateur/enregistrement' => 'utilisateurs',
    'client/enregistrement'      => 'clients',
    'boutique/enregistrement'    => 'boutiques',
    'boutique/transfert'         => 'transfert',
    'facture/liste'              => 'factures',
    'commande/liste'             => 'commandes',
    'transaction/liste'          => 'transactions',
    'peremption/liste'           => 'peremptions',
    'societe/liste'              => 'societes',
    'societe/parametres'         => 'parametres_fne',
    'societe/configuration_dgi'  => 'config_dgi'
];

$active_key = '';
foreach ($active_pages as $path => $key) {
    if (strpos($current_path, $path) !== false) {
        $active_key = $key;
        break;
    }
}

// Pour les sous-menus
$is_boutique_open   = (strpos($current_path, 'boutique') !== false);
$is_historique_open = (strpos($current_path, 'facture') !== false || 
                       strpos($current_path, 'commande') !== false || 
                       strpos($current_path, 'transaction') !== false);

$role = $_SESSION['role'] ?? 'invite';
$is_admin = ($role === 'admin');
$is_manager = ($role === 'manager');
$is_caissier = ($role === 'caissier');

// Déterminer le mode d'affichage du menu
$boutique_active = $_SESSION['boutique_active'] ?? null;
$mode_boutique = ($boutique_active !== null) || $is_caissier; // Mode boutique si boutique sélectionnée OU caissier
$mode_societe = $is_manager && !$boutique_active; // Mode société si manager SANS boutique active

// === ALERTES & NOTIFICATIONS ===
$alertCount = 0;
$peremptionAlerts = [];
$abonnementAlert = null;
$joursRestantsAbonnement = null;

try {
    $con = getDatabaseConnection();

    // 1. ALERTES PÉREMPTION (Manager/Caissier avec boutique active)
    if ($mode_boutique) {
        $id_boutique_alert = getBoutiqueActive();
        if ($id_boutique_alert) {
            $stmt = $con->prepare("
                SELECT p.designation, p.reference_produit, p.date_peremption
                FROM produits p
                WHERE p.id_boutique = ?
                  AND p.date_peremption IS NOT NULL
                  AND p.date_peremption <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                  AND p.quantite > 0
                ORDER BY p.date_peremption ASC
                LIMIT 10
            ");
            $stmt->execute([$id_boutique_alert]);
            $peremptionAlerts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $alertCount += count($peremptionAlerts);
        }
    }

    // 2. ALERTE ABONNEMENT (Manager/Caissier - pas Admin)
    if (!$is_admin) {
        $id_societe_alert = getSocieteActive();
        if ($id_societe_alert) {
            $stmt = $con->prepare("
                SELECT
                    s.nom_societe,
                    s.abonnement_expire_le,
                    s.numero_contact_abonnement,
                    DATEDIFF(s.abonnement_expire_le, CURDATE()) as jours_restants,
                    p.nom_plan
                FROM societes s
                LEFT JOIN plans p ON s.plan_actuel_id = p.id_plan
                WHERE s.id_societe = ? AND s.statut_abonnement = 'actif'
            ");
            $stmt->execute([$id_societe_alert]);
            $abon = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($abon && $abon['jours_restants'] !== null) {
                $joursRestantsAbonnement = intval($abon['jours_restants']);

                // Alerte si <= 7 jours
                if ($joursRestantsAbonnement <= 7 && $joursRestantsAbonnement >= 0) {
                    $abonnementAlert = $abon;
                    $alertCount++;
                }
            }
        }
    }
} catch (Exception $e) {
    Logger::error('Erreur chargement alertes dashboard', ['error' => $e->getMessage()]);
}
?>
<!-- Header -->
            <div class="header">
            <?php displayFlash(); ?>
            
				<!-- Logo -->
                <div class="header-left">
                    <!-- <a href="index.html" class="logo">
						<img src="../assets/img/logo.png" width="40" height="40" alt="">
					</a> -->
                </div>
				<!-- /Logo -->
				
				<a id="toggle_btn" href="javascript:void(0);">
					<span class="bar-icon">
						<span></span>
						<span></span>
						<span></span>
					</span>
				</a>
				
				<!-- Header Title -->
                <div class="page-title-box">
					<h3>Gestion Commerciale</h3>
                </div>
				<!-- /Header Title -->
				
				<a id="mobile_btn" class="mobile_btn" href="#sidebar"><i class="fa fa-bars"></i></a>
				
				
				<!-- Header Menu -->
				<ul class="nav user-menu">

<!-- Widget Boutique Active (Manager uniquement) -->
<?php if ($role === 'manager' && !empty($_SESSION['boutique_active'])): ?>
    <?php
    // Récupérer le nom de la boutique active
    $boutique_active_nom = $_SESSION['boutique_active_nom'] ?? 'Boutique';
    ?>
    <li class="nav-item dropdown">
        <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown" style="background: linear-gradient(135deg, #ff9b44 0%, #fc6075 100%); color: white; border-radius: 6px; padding: 8px 16px; margin-right: 15px; font-weight: 500; box-shadow: 0 2px 8px rgba(255, 155, 68, 0.3);">
            <i class="la la-store"></i> <?= htmlspecialchars($boutique_active_nom) ?>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
            <div class="dropdown-header">
                <strong><i class="la la-store text-primary"></i> Boutique active</strong>
            </div>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="<?= url('/boutique/selectionner') ?>">
                <i class="la la-exchange text-info"></i> Changer de boutique
            </a>
            <a class="dropdown-item" href="<?= url('/boutique/desactiver') ?>">
                <i class="la la-times-circle text-warning"></i> Retour menu société
            </a>
        </div>
    </li>
<?php endif; ?>

<!-- Widget Compteur Abonnement (Manager/Caissier) -->
<?php if (!$is_admin && $joursRestantsAbonnement !== null): ?>
    <?php
    // Couleur selon jours restants
    if ($joursRestantsAbonnement <= 3) {
        $bg_gradient = 'linear-gradient(135deg, #e74c3c 0%, #c0392b 100%)';
        $icon = 'la-exclamation-triangle';
    } elseif ($joursRestantsAbonnement <= 7) {
        $bg_gradient = 'linear-gradient(135deg, #f39c12 0%, #e67e22 100%)';
        $icon = 'la-clock';
    } else {
        $bg_gradient = 'linear-gradient(135deg, #3498db 0%, #2980b9 100%)';
        $icon = 'la-calendar-check';
    }
    ?>
    <li class="nav-item">
        <a href="<?= url('/plan/voir') ?>" class="nav-link" style="background: <?= $bg_gradient ?>; color: white; border-radius: 6px; padding: 8px 16px; margin-right: 15px; font-weight: 500; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
            <i class="la <?= $icon ?>"></i> <?= $joursRestantsAbonnement ?> jour<?= $joursRestantsAbonnement > 1 ? 's' : '' ?> restant<?= $joursRestantsAbonnement > 1 ? 's' : '' ?>
        </a>
    </li>
<?php endif; ?>

<!-- Ta barre de navigation avec la cloche -->
<li class="nav-item dropdown">
    <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
        <i class="fa fa-bell-o"></i>
        <?php if ($alertCount > 0): ?>
            <span class="badge badge-pill bg-danger"><?= $alertCount ?></span>
        <?php endif; ?>
    </a>
    <div class="dropdown-menu notifications" style="width: 350px; max-height: 500px; overflow-y: auto;">
        <div class="topnav-dropdown-header">
            <span class="notification-title">Notifications</span>
        </div>
        <div class="noti-content">
            <ul class="notification-list">
                <?php if ($alertCount > 0): ?>
                    <!-- Alerte Abonnement (priorité absolue) -->
                    <?php if ($abonnementAlert): ?>
                        <li class="notification-message" style="background: <?= $joursRestantsAbonnement <= 3 ? '#fff5f5' : '#fff8e1' ?>;">
                            <a href="<?= url('/plan/voir') ?>">
                                <div class="media">
                                    <span class="avatar <?= $joursRestantsAbonnement <= 3 ? 'bg-danger' : 'bg-warning' ?>">
                                        <i class="la la-exclamation-triangle text-white"></i>
                                    </span>
                                    <div class="media-body">
                                        <p class="noti-details">
                                            <strong>Abonnement <?= htmlspecialchars($abonnementAlert['nom_plan']) ?></strong>
                                        </p>
                                        <p class="noti-time" style="font-weight: 600; color: <?= $joursRestantsAbonnement <= 3 ? '#e74c3c' : '#f39c12' ?>;">
                                            <?php if ($joursRestantsAbonnement === 0): ?>
                                                ⚠️ Expire AUJOURD'HUI !
                                            <?php else: ?>
                                                ⚠️ Expire dans <?= $joursRestantsAbonnement ?> jour<?= $joursRestantsAbonnement > 1 ? 's' : '' ?>
                                            <?php endif; ?>
                                        </p>
                                        <p class="mb-0" style="font-size: 11px; color: #666;">
                                            <i class="la la-phone"></i> Contactez : <?= htmlspecialchars($abonnementAlert['numero_contact_abonnement']) ?>
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Alertes de péremption (priorité haute) -->
                    <?php if (!empty($peremptionAlerts)): ?>
                        <?php foreach ($peremptionAlerts as $p): ?>
                        <?php
                            $joursRestants = round((strtotime($p['date_peremption']) - time()) / 86400);
                            $bgColor = $joursRestants <= 3 ? 'bg-danger' : ($joursRestants <= 7 ? 'bg-warning' : 'bg-info');
                        ?>
                        <li class="notification-message">
                            <a href="<?= url('/peremption/liste') ?>">
                                <div class="media">
                                    <span class="avatar <?= $bgColor ?>">
                                        <i class="fa fa-clock-o text-white"></i>
                                    </span>
                                    <div class="media-body">
                                        <p class="noti-details">
                                            <strong><?= htmlspecialchars($p['designation'] ?? 'Produit') ?></strong>
                                        </p>
                                        <p class="noti-time text-danger" style="font-weight: 600;">
                                            ⚠️ Expire dans <?= $joursRestants ?> jour<?= $joursRestants > 1 ? 's' : '' ?>
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Alertes de stock faible -->
                    <?php if (!empty($stockAlerts)): ?>
                        <?php foreach ($stockAlerts as $a): ?>
                        <li class="notification-message">
                            <a href="<?= url('/produit/liste') ?>">
                                <div class="media">
                                    <span class="avatar bg-warning">
                                        <i class="fa fa-exclamation-triangle text-white"></i>
                                    </span>
                                    <div class="media-body">
                                        <p class="noti-details">
                                            <strong><?= htmlspecialchars($a['designation']) ?></strong>
                                        </p>
                                        <p class="noti-time text-danger">
                                            Stock : <?= $a['stock'] ?> (seuil : <?= $a['stock_alert'] ?>)
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php else: ?>
                    <li class="text-center text-muted p-3">
                        <i class="fa fa-check-circle" style="font-size: 40px; opacity: 0.3; display: block; margin-bottom: 10px;"></i>
                        <p>Aucune alerte</p>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
        <?php if ($alertCount > 0): ?>
        <div class="topnav-dropdown-footer">
            <div style="display: flex; justify-content: space-around; padding: 5px;">
                <?php if (!empty($peremptionAlerts)): ?>
                    <a href="<?= url('/peremption/liste') ?>" style="font-size: 12px;">
                        <i class="fa fa-clock-o"></i> Péremptions (<?= count($peremptionAlerts) ?>)
                    </a>
                <?php endif; ?>
                <?php if (!empty($stockAlerts)): ?>
                    <a href="<?= url('/produit/liste') ?>" style="font-size: 12px;">
                        <i class="fa fa-cubes"></i> Stocks (<?= count($stockAlerts) ?>)
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</li>

					<li class="nav-item dropdown has-arrow main-drop">
						<a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
							<span><i class="la la-user"></i>  <?= e($_SESSION['nom']) ?></span>
							<span class="status online"></span>
						</a>
						<div class="dropdown-menu">
							<a class="dropdown-item" href="<?= url('') ?>/utilisateur/motdepasse"><i class="fa fa-key"></i> Changer mot de passe</a>
							<div class="dropdown-divider"></div>
							<a class="dropdown-item" href="?deconnexion=1"><i class="fa fa-sign-out"></i> Se déconnecter</a>
						</div>
					</li>
				</ul>
				<!-- /Header Menu -->
				
				<!-- Mobile Menu -->
				<div class="dropdown mobile-user-menu">
					<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
					<div class="dropdown-menu dropdown-menu-right">
						<a class="dropdown-item" href="<?= url('') ?>/utilisateur/motdepasse"><i class="fa fa-key"></i> Changer mot de passe</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="?deconnexion=1"><i class="fa fa-sign-out"></i> Se déconnecter</a>
					</div>
				</div>
				<!-- /Mobile Menu -->
				
            </div>
			<!-- /Header -->
			
<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title"><span>Navigation</span></li>

                <!-- Dashboard -->
                <li class="<?= $active_key === 'dashboard' ? 'active' : '' ?>">
                    <a href="<?= url('/utilisateur/dashboard') ?>">
                        <i class="la la-dashboard"></i> <span>Tableau de bord</span>
                    </a>
                </li>

                <?php if ($is_admin): ?>
                    <!-- ========== MENU ADMIN ========== -->
                    <li class="menu-title"><span>Administration</span></li>

                    <li class="<?= $active_key === 'societes' ? 'active' : '' ?>">
                        <a href="<?= url('/societe/liste') ?>">
                            <i class="la la-building"></i> <span>Sociétés</span>
                        </a>
                    </li>

                    <li class="<?= strpos($current_path, 'abonnement') !== false ? 'active' : '' ?>">
                        <a href="<?= url('/abonnement/dashboard') ?>">
                            <i class="la la-calendar-check"></i> <span>Abonnements</span>
                        </a>
                    </li>

                    <li class="<?= strpos($current_path, 'plan') !== false && strpos($current_path, 'plan/voir') === false ? 'active' : '' ?>">
                        <a href="<?= url('/plan/liste') ?>">
                            <i class="la la-layer-group"></i> <span>Plans</span>
                        </a>
                    </li>

                    <li class="<?= $active_key === 'utilisateurs' ? 'active' : '' ?>">
                        <a href="<?= url('/utilisateur/enregistrement') ?>">
                            <i class="la la-user-plus"></i> <span>Managers</span>
                        </a>
                    </li>

                <?php elseif ($mode_societe): ?>
                    <!-- ========== MENU SOCIÉTÉ (Manager sans boutique active) ========== -->
                    <li class="menu-title"><span>Gestion de la Société</span></li>

                    <li class="<?= $active_key === 'utilisateurs' ? 'active' : '' ?>">
                        <a href="<?= url('/utilisateur/enregistrement') ?>">
                            <i class="la la-user-plus"></i> <span>Utilisateurs</span>
                        </a>
                    </li>

                    <!-- Boutiques -->
                    <li class="submenu">
                        <a href="#" class="<?= $is_boutique_open ? 'subdrop' : '' ?>">
                            <i class="la la-store"></i> <span>Boutiques</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul style="display: <?= $is_boutique_open ? 'block' : 'none' ?>;">
                            <li class="<?= strpos($current_path, 'boutique/enregistrement') !== false ? 'active' : '' ?>">
                                <a href="<?= url('/boutique/enregistrement') ?>">Enregistrement</a>
                            </li>
                            <li class="<?= strpos($current_path, 'boutique/transfert') !== false ? 'active' : '' ?>">
                                <a href="<?= url('/boutique/transfert') ?>">Transfert de Stock</a>
                            </li>
                            <li><a href="<?= url('/boutique/map') ?>">Localisation des boutiques</a></li>
                        </ul>
                    </li>

                    <!-- Paramètres FNE -->
                    <li class="<?= $active_key === 'parametres_fne' ? 'active' : '' ?>">
                        <a href="<?= url('/societe/parametres') ?>">
                            <i class="la la-building"></i> <span>Infos Société (FNE)</span>
                        </a>
                    </li>

                    <li class="<?= $active_key === 'config_dgi' ? 'active' : '' ?>">
                        <a href="<?= url('/societe/configuration_dgi') ?>">
                            <i class="la la-cog"></i> <span>Configuration DGI</span>
                        </a>
                    </li>

                    <!-- Plans & Abonnement -->
                    <li class="<?= strpos($current_path, 'plan/voir') !== false ? 'active' : '' ?>">
                        <a href="<?= url('/plan/voir') ?>">
                            <i class="la la-star"></i> <span>Plans & Abonnement</span>
                        </a>
                    </li>

                    <!-- Message pour sélectionner une boutique -->
                    <li class="menu-title" style="margin-top: 30px;">
                        <span>Boutiques</span>
                    </li>
                    <li>
                        <a href="<?= url('/boutique/selectionner') ?>" style="background: linear-gradient(135deg, #ff9b44 0%, #fc6075 100%); color: white; border-radius: 6px; margin: 0 10px; box-shadow: 0 2px 8px rgba(255, 155, 68, 0.3);">
                        <span><strong>Sélectionner boutique</strong></span>
                        </a>
                    </li>

                <?php elseif ($mode_boutique): ?>
                    <!-- ========== MENU BOUTIQUE (Manager avec boutique active OU Caissier) ========== -->
                    <li class="menu-title"><span>Gestion</span></li>

                    <li class="<?= $active_key === 'vente' ? 'active' : '' ?>">
                        <a href="<?= url('/vente/effectuer') ?>">
                            <i class="la la-send-o"></i> <span>Point de Vente</span>
                        </a>
                    </li>

                    <!-- Produits (LECTURE pour caissier, GESTION pour manager) -->
                    <li class="<?= $active_key === 'produits' ? 'active' : '' ?>">
                        <a href="<?= url('/produit/liste') ?>">
                            <i class="la la-cube"></i> <span>Produits</span>
                        </a>
                    </li>

                    <!-- Clients (Visible pour tous) -->
                    <li class="<?= $active_key === 'clients' ? 'active' : '' ?>">
                        <a href="<?= url('/client/enregistrement') ?>">
                            <i class="la la-users"></i> <span>Clients</span>
                        </a>
                    </li>

                    <!-- Taxes (Consultation pour caissier) -->
                    <li class="<?= $active_key === 'taxes' ? 'active' : '' ?>">
                        <a href="<?= url('/taxe/enregistrement') ?>">
                            <i class="la la-gift"></i> <span>Taxes</span>
                        </a>
                    </li>

                    <!-- Catégories (Consultation pour caissier) -->
                    <li class="<?= $active_key === 'categories' ? 'active' : '' ?>">
                        <a href="<?= url('/categorie/liste') ?>">
                            <i class="la la-tags"></i> <span>Catégories</span>
                        </a>
                    </li>

                    <!-- Fournisseurs (Consultation pour caissier) -->
                    <li class="<?= $active_key === 'fournisseurs' ? 'active' : '' ?>">
                        <a href="<?= url('/fournisseur/liste') ?>">
                            <i class="la la-truck"></i> <span>Fournisseurs</span>
                        </a>
                    </li>

                    <!-- Plans & Abonnement (Visible pour tous) -->
                    <li class="<?= strpos($current_path, 'plan/voir') !== false ? 'active' : '' ?>">
                        <a href="<?= url('/plan/voir') ?>">
                            <i class="la la-star"></i> <span>Plans & Abonnement</span>
                        </a>
                    </li>

                <?php if (!$is_caissier): ?>
                    <!-- === MENU MANAGER UNIQUEMENT === -->
                    <li class="<?= $active_key === 'peremptions' ? 'active' : '' ?>">
                        <a href="<?= url('/peremption/liste') ?>">
                            <i class="la la-clock-o"></i> <span>Péremptions</span>
                        </a>
                    </li>

                    <li class="<?= $active_key === 'utilisateurs' ? 'active' : '' ?>">
                        <a href="<?= url('/utilisateur/enregistrement') ?>">
                            <i class="la la-user-plus"></i> <span>Utilisateurs</span>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Historiques (MODE BOUTIQUE uniquement) -->
                <li class="submenu">
                    <a href="#" class="<?= $is_historique_open ? 'subdrop' : '' ?>">
                        <i class="la la-history"></i> <span>Historiques</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul style="display: <?= $is_historique_open ? 'block' : 'none' ?>;">
                        <li class="<?= $active_key === 'factures' ? 'active' : '' ?>">
                            <a href="<?= url('/facture/liste') ?>">Factures</a>
                        </li>
                        <li class="<?= $active_key === 'commandes' ? 'active' : '' ?>">
                            <a href="<?= url('/commande/liste') ?>">Commandes</a>
                        </li>
                        <li class="<?= $active_key === 'transactions' ? 'active' : '' ?>">
                            <a href="<?= url('/transaction/liste') ?>">Transactions</a>
                        </li>
                        <li class="<?= $active_key === 'mouvements' ? 'active' : '' ?>">
                            <a href="<?= url('/mouvement/liste') ?>">Mouvements de Stock</a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>