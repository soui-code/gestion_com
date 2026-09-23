<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs Système - Admin</title>
    <!-- Icon pwa -->
     <link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#1e293b">
<link rel="apple-touch-icon" href="/assets/icons/icon-192.png">

    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/favicon.png">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/line-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .log-container {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            max-height: 600px;
            overflow-y: auto;
        }
        .log-line {
            padding: 4px 0;
            border-bottom: 1px solid #2d2d2d;
        }
        .log-line:hover {
            background: #2d2d2d;
        }
        .log-info { color: #4ec9b0; }
        .log-warning { color: #dcdcaa; }
        .log-error { color: #f48771; }
        .log-date { color: #569cd6; }
        .filter-buttons .btn {
            margin-right: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="main-wrapper">
        <?php include './config/dashboard.php'; ?>

        <div class="page-wrapper">
            <div class="content container-fluid">

                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title">
                                <i class="la la-file-text"></i> Logs Système
                            </h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= url('/utilisateur/dashboard') ?>">Dashboard</a></li>
                                <li class="breadcrumb-item active">Logs</li>
                            </ul>
                        </div>
                        <div class="col-auto float-right ml-auto">
                            <button onclick="location.reload()" class="btn btn-primary">
                                <i class="fa fa-refresh"></i> Actualiser
                            </button>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <!-- Stats -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h4 class="text-muted">Total Lignes</h4>
                                <h2 class="text-info"><?= number_format($total) ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h4 class="text-muted">Taille Fichier</h4>
                                <h2 class="text-primary"><?= round($file_size / 1024, 2) ?> KB</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h4 class="text-muted">Info</h4>
                                <h2 class="text-success" id="count-info">-</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h4 class="text-muted">Erreurs</h4>
                                <h2 class="text-danger" id="count-error">-</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtres -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="filter-buttons">
                            <button class="btn btn-sm btn-outline-secondary" onclick="filterLogs('all')">
                                <i class="fa fa-list"></i> Tout
                            </button>
                            <button class="btn btn-sm btn-outline-success" onclick="filterLogs('info')">
                                <i class="fa fa-info-circle"></i> Info
                            </button>
                            <button class="btn btn-sm btn-outline-warning" onclick="filterLogs('warning')">
                                <i class="fa fa-exclamation-triangle"></i> Warning
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="filterLogs('error')">
                                <i class="fa fa-times-circle"></i> Error
                            </button>
                            <input type="text" id="searchInput" class="form-control d-inline-block" style="width: 300px; margin-left: 20px;" placeholder="Rechercher...">
                        </div>
                    </div>
                </div>

                <!-- Logs -->
                <div class="card">
                    <div class="card-body">
                        <div class="log-container" id="logContainer">
                            <?php if (!empty($logs)): ?>
                                <?php foreach ($logs as $index => $line): ?>
                                    <?php
                                    $class = 'log-line';
                                    $type = 'info';
                                    if (stripos($line, '[info]') !== false) {
                                        $class .= ' log-info';
                                        $type = 'info';
                                    } elseif (stripos($line, '[warning]') !== false) {
                                        $class .= ' log-warning';
                                        $type = 'warning';
                                    } elseif (stripos($line, '[error]') !== false) {
                                        $class .= ' log-error';
                                        $type = 'error';
                                    }
                                    ?>
                                    <div class="<?= $class ?>" data-type="<?= $type ?>">
                                        <?= htmlspecialchars($line) ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center text-muted py-5">
                                    <i class="la la-inbox" style="font-size: 60px;"></i>
                                    <p>Aucun log disponible</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-footer text-muted text-center">
                        <small>
                            <i class="fa fa-clock-o"></i>
                            Dernière modification : <?= date('d/m/Y H:i:s', $last_modified) ?>
                        </small>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="<?= url('') ?>/assets/js/jquery-3.5.1.min.js"></script>

    <!-- Bootstrap Core JS -->
    <script src="<?= url('') ?>/assets/js/popper.min.js"></script>
    <script src="<?= url('') ?>/assets/js/bootstrap.min.js"></script>

    <!-- Custom JS -->
    <script src="<?= url('') ?>/assets/js/app.js"></script>

    <script>
        // Compter les logs par type
        function countLogs() {
            const infoCount = document.querySelectorAll('[data-type="info"]').length;
            const errorCount = document.querySelectorAll('[data-type="error"]').length;

            document.getElementById('count-info').textContent = infoCount;
            document.getElementById('count-error').textContent = errorCount;
        }

        // Filtrer les logs
        function filterLogs(type) {
            const lines = document.querySelectorAll('.log-line');
            lines.forEach(line => {
                if (type === 'all' || line.dataset.type === type) {
                    line.style.display = 'block';
                } else {
                    line.style.display = 'none';
                }
            });
        }

        // Recherche
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const search = e.target.value.toLowerCase();
            const lines = document.querySelectorAll('.log-line');

            lines.forEach(line => {
                const text = line.textContent.toLowerCase();
                if (text.includes(search)) {
                    line.style.display = 'block';
                } else {
                    line.style.display = 'none';
                }
            });
        });

        // Compter au chargement
        countLogs();
    </script>
</body>
</html>
