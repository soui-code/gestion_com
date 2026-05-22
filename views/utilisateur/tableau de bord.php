<!DOCTYPE html>
<html lang="fr">
<head>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Tableau de Bord - Gestion Commerciale</title>

    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/favicon.png">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/line-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dataTables.bootstrap4.min.css">
</head>
<body>
    <div class="main-wrapper">
        <?php include './config/dashboard.php'; ?>

        <div class="page-wrapper">
            <div class="content container-fluid">

                <!-- Page Header -->
                <div class="page-header">
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="page-title">Tableau de bord</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item active">Accueil</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <!-- Cartes statistiques -->
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                        <div class="card dash-widget">
                            <div class="card-body">
                                <span class="dash-widget-icon bg-info"><i class="fa fa-cubes"></i></span>
                                <div class="dash-widget-info">
                                    <h3><?= $stats['produits'] ?></h3>
                                    <span>Produits</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                        <div class="card dash-widget">
                            <div class="card-body">
                                <span class="dash-widget-icon bg-success"><i class="fa fa-users"></i></span>
                                <div class="dash-widget-info">
                                    <h3><?= $stats['clients'] ?></h3>
                                    <span>Clients</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                        <div class="card dash-widget">
                            <div class="card-body">
                                <span class="dash-widget-icon bg-info"><i class="fa fa-home"></i></span>
                                <div class="dash-widget-info">
                                    <h3><?= $stats['boutiques'] ?></h3>
                                    <span>Boutiques</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                        <div class="card dash-widget">
                            <div class="card-body">
                                <span class="dash-widget-icon bg-warning"><i class="fa fa-shopping-cart"></i></span>
                                <div class="dash-widget-info">
                                    <h3><?= $stats['ventes'] ?></h3>
                                    <span>Ventes</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chiffre d'affaires -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h4 class="text-muted">Chiffre d'affaires total</h4>
                                <h2 class="text-success"><?= $stats['ca_total'] ?> FCFA</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h4 class="text-muted">Chiffre d'affaires du mois </h4>
                                <h2 class="text-primary"><?= $stats['ca_mois'] ?> FCFA</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h4 class="text-muted">Valeur globale du stock</h4>
                                <h2 class="text-warning"><?= $stats['valeur_stock'] ?> FCFA</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Dernières ventes -->
                    <div class="col-md-6 d-flex">
                        <div class="card card-table flex-fill">
                            <div class="card-header">
                                <h3 class="card-title mb-0">Dernières ventes</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Client</th>
                                                <th>Montant</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($stats['ventes_recentes']) > 0): ?>
                                                <?php foreach ($stats['ventes_recentes'] as $v): ?>
                                                <tr>
                                                    <td><?= date('d/m/Y', strtotime($v['date_commande'])) ?></td>
                                                    <td><?= htmlspecialchars($v['nom_prenom_client'] ?? 'Client occasionnel') ?></td>
                                                    <td><strong><?= number_format($v['montant_commande'], 0, ',', ' ') ?> FCFA</strong></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr><td colspan="3" class="text-center text-muted">Aucune vente récente</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer text-center">
                                <a href="<?= BASE_URL ?>/transaction/liste">Voir toutes les ventes</a>
                            </div>
                        </div>
                    </div>

                    <!-- Produits en alerte stock -->
                    <div class="col-md-6 d-flex">
                        <div class="card card-table flex-fill">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title mb-0">Alertes stock</h3>
                                <?php if (count($stats['produits_alerte']) > 0): ?>
                                    <span class="badge badge-danger"><?= count($stats['produits_alerte']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Produit</th>
                                                <th>Stock restant</th>
                                                <th>Alerte à</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($stats['produits_alerte']) > 0): ?>
                                                <?php foreach ($stats['produits_alerte'] as $p): ?>
                                                <tr class="table-warning">
                                                    <td><?= htmlspecialchars($p['designation']) ?></td>
                                                    <td><strong class="text-danger"><?= $p['stock'] ?></strong></td>
                                                    <td><?= $p['stock_alert'] ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr><td colspan="3" class="text-center text-success">Tous les stocks sont OK !</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer text-center">
                                <a href="<?= BASE_URL ?>/produit/liste">Gérer les produits</a>
                            </div>
                        </div>
                    </div>
                </div>

<!-- Graphiques -->
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Évolution mensuelle - <?= date('Y') ?></h4>
                <canvas id="ventesMensuellesChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <!-- Graphique 2 : Répartition par mode de paiement -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Modes de paiement</h4>
                <canvas id="modePaiementChart" height="230"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Derniers mouvements -->
    <div class="col-md-12 d-flex">
        <div class="card card-table flex-fill">
            <div class="card-header">
                <h3 class="card-title mb-0">Derniers Mouvements de Stock</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Date & Heure</th>
                                <th>Produit</th>
                                <th>Mouvement</th>
                                <th>Quantité</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($stats['mouvements_recents']) > 0): ?>
                                <?php foreach ($stats['mouvements_recents'] as $m): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($m['date_mouvement'])) ?></td>
                                    <td><strong><?= htmlspecialchars($m['designation']) ?></strong></td>
                                    <td>
                                        <?php if ($m['type_mouvement'] == 'entree'): ?>
                                            <span class="badge bg-inverse-success"><i class="fa fa-arrow-down mr-1"></i> Entrée</span>
                                        <?php else: ?>
                                            <span class="badge bg-inverse-danger"><i class="fa fa-arrow-up mr-1"></i> Sortie</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?= $m['quantite'] ?></strong></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center text-muted">Aucun mouvement récent</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-center">
                <a href="<?= BASE_URL ?>/mouvement/liste">Voir tout l'historique</a>
            </div>
        </div>
    </div>
</div>


            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../assets/js/jquery-3.5.1.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/jquery.slimscroll.min.js"></script>
    <script src="../assets/js/app.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
fetch('<?= BASE_URL ?>/utilisateur/ventes_mensuelles')
    .then(r => r.json())
    .then(data => {
        new Chart(document.getElementById('ventesMensuellesChart'), {
            type: 'line',
            data: {
                labels: data.mois, // Jan, Fév, etc.
                datasets: [
                    {
                        label: 'Ventes',
                        data: data.ventes,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5
                    },
                    {
                        label: 'Approvisionnements',
                        data: data.approvisionnements,
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5
                    },
                    {
                        label: 'Achats',
                        data: data.achats,
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: ctx => `${ctx.dataset.label}: ${ctx.parsed.y.toLocaleString()} FCFA`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => value.toLocaleString() + ' FCFA'
                        }
                    }
                }
            }
        });
    })
    .catch(err => console.error('Erreur graphique:', err));
</script>
    <script>
// === Graphique 2 : Répartition par mode de paiement (doughnut) ===
fetch('<?= BASE_URL ?>/utilisateur/modes_paiement')
    .then(response => response.json())
    .then(data => {
        new Chart(document.getElementById('modePaiementChart'), {
            type: 'doughnut',
            data: {
                labels: data.modes,
                datasets: [{
                    data: data.montants,
                    backgroundColor: [
                        '#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: ctx => `${ctx.label}: ${ctx.parsed.toLocaleString()} FCFA`
                        }
                    }
                }
            }
        });
    });
</script>
	
</body>
</html>