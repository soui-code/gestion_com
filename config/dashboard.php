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
    'facture/liste'              => 'factures',
    'commande/liste'             => 'commandes',
    'transaction/liste'          => 'transactions'
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
$is_caissier = ($role === 'caissier');
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

<!-- Ta barre de navigation avec la cloche -->
<li class="nav-item dropdown">
    <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
        <i class="fa fa-bell-o"></i>
        <?php if ($alertCount > 0): ?>
            <span class="badge badge-pill bg-danger"><?= $alertCount ?></span>
        <?php endif; ?>
    </a>
    <div class="dropdown-menu notifications">
        <div class="topnav-dropdown-header">
            <span class="notification-title">Alertes Stock Faible</span>
        </div>
        <div class="noti-content">
            <ul class="notification-list">
                <?php if ($alertCount > 0): ?>
                    <?php foreach ($stockAlerts as $a): ?>
                    <li class="notification-message">
                        <a href="<?php if(substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])),-1) =="/"){ echo (substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])), 0,-1)); }else{ echo ((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"]));} ?>/produit/liste">
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
                <?php else: ?>
                    <li class="text-center text-muted p-3">Aucune alerte de stock</li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="topnav-dropdown-footer">
            <a href="<?php if(substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])),-1) =="/"){ echo (substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])), 0,-1)); }else{ echo ((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"]));} ?>/produit/liste">Voir tous les produits</a>
        </div>
    </div>
</li>

					<li class="nav-item dropdown has-arrow main-drop">
						<a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
							<span><i class="la la-user"></i>  <?= $_SESSION['nom'] ?></span>
							<span class="status online"></span>
						</a>
						<div class="dropdown-menu">
							<a class="dropdown-item" href="?deconnexion=1">Se déconnecter</a>
						</div>
					</li>
				</ul>
				<!-- /Header Menu -->
				
				<!-- Mobile Menu -->
				<div class="dropdown mobile-user-menu">
					<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
					<div class="dropdown-menu dropdown-menu-right">
						<a class="dropdown-item" href="?deconnexion=1">Se déconnecter</a>
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
                <li class="menu-title"><span>Bar de navigation</span></li>

                <!-- TOUJOURS AFFICHÉ : Tableau de bord -->
                <li class="<?= $active_key === 'dashboard' ? 'active' : '' ?>">
                    <a href="<?php if(substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])),-1) =="/"){ echo (substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])), 0,-1)); }else{ echo ((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"]));} ?>/utilisateur/dashboard">
                        <i class="la la-dashboard"></i> <span>Tableau de bord</span>
                    </a>
                </li>

                <!-- TOUJOURS AFFICHÉ : Vente -->
                <li class="<?= $active_key === 'vente' ? 'active' : '' ?>">
                    <a href="<?php if(substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])),-1) =="/"){ echo (substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])), 0,-1)); }else{ echo ((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"]));} ?>/vente/effectuer">
                        <i class="la la-send-o"></i> <span>Vente</span>
                    </a>
                </li>

                <?php if (!$is_caissier): ?>
                    <!-- === MENU ADMIN UNIQUEMENT === -->
                    <li class="<?= $active_key === 'produits' ? 'active' : '' ?>">
                        <a href="<?php if(substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])),-1) =="/"){ echo (substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])), 0,-1)); }else{ echo ((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"]));} ?>/produit/liste">
                            <i class="la la-cube"></i> <span>Produits</span>
                        </a>
                    </li>

                    <li class="<?= $active_key === 'taxes' ? 'active' : '' ?>">
                        <a href="<?php if(substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])),-1) =="/"){ echo (substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])), 0,-1)); }else{ echo ((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"]));} ?>/taxe/enregistrement">
                            <i class="la la-gift"></i> <span>Taxes</span>
                        </a>
                    </li>

                    <li class="<?= $active_key === 'categories' ? 'active' : '' ?>">
                        <a href="<?php if(substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])),-1) =="/"){ echo (substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])), 0,-1)); }else{ echo ((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"]));} ?>/categorie/liste">
                            <i class="la la-tags"></i> <span>Catégories</span>
                        </a>
                    </li>

                    <li class="<?= $active_key === 'fournisseurs' ? 'active' : '' ?>">
                        <a href="<?php if(substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])),-1) =="/"){ echo (substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])), 0,-1)); }else{ echo ((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"]));} ?>/fournisseur/liste">
                            <i class="la la-truck"></i> <span>Fournisseurs</span>
                        </a>
                    </li>

                    <li class="<?= $active_key === 'utilisateurs' ? 'active' : '' ?>">
                        <a href="<?php if(substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])),-1) =="/"){ echo (substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])), 0,-1)); }else{ echo ((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"]));} ?>/utilisateur/enregistrement">
                            <i class="la la-user-plus"></i> <span>Utilisateurs</span>
                        </a>
                    </li>

                    <li class="<?= $active_key === 'clients' ? 'active' : '' ?>">
                        <a href="<?php if(substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])),-1) =="/"){ echo (substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])), 0,-1)); }else{ echo ((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"]));} ?>/client/enregistrement">
                            <i class="la la-users"></i> <span>Clients</span>
                        </a>
                    </li>

                    <!-- Boutiques (admin only) -->
                    <li class="submenu">
                        <a href="#" class="<?= $is_boutique_open ? 'subdrop' : '' ?>">
                            <i class="la la-store"></i> <span>Boutiques</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul style="display: <?= $is_boutique_open ? 'block' : 'none' ?>;">
                            <li class="<?= strpos($current_path, 'boutique/enregistrement') !== false ? 'active' : '' ?>">
                                <a href="<?php if(substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])),-1) =="/"){ echo (substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])), 0,-1)); }else{ echo ((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"]));} ?>/boutique/enregistrement">Enregistrement</a>
                            </li>
                            <li><a href="<?php if(substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])),-1) =="/"){ echo (substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])), 0,-1)); }else{ echo ((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"]));} ?>/boutique/map">Localisation des boutiques</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- Historiques : AFFICHÉ POUR TOUT LE MONDE (y compris caissier) -->
                <li class="submenu">
                    <a href="#" class="<?= $is_historique_open ? 'subdrop' : '' ?>">
                        <i class="la la-history"></i> <span>Historiques</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul style="display: <?= $is_historique_open ? 'block' : 'none' ?>;">
                        <li class="<?= $active_key === 'factures' ? 'active' : '' ?>">
                            <a href="<?php if(substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])),-1) =="/"){ echo (substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])), 0,-1)); }else{ echo ((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"]));} ?>/facture/liste">Factures</a>
                        </li>
                        <li class="<?= $active_key === 'commandes' ? 'active' : '' ?>">
                            <a href="<?php if(substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])),-1) =="/"){ echo (substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])), 0,-1)); }else{ echo ((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"]));} ?>/commande/liste">Commandes</a>
                        </li>
                        <li class="<?= $active_key === 'transactions' ? 'active' : '' ?>">
                            <a href="<?php if(substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])),-1) =="/"){ echo (substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])), 0,-1)); }else{ echo ((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"]));} ?>/transaction/liste">Transactions</a>
                        </li>
                        <li class="<?= $active_key === 'mouvements' ? 'active' : '' ?>">
                            <a href="<?php if(substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])),-1) =="/"){ echo (substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])), 0,-1)); }else{ echo ((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"]));} ?>/mouvement/liste">Mouvements de Stock</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>