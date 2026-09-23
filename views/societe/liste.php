<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Sociétés - Admin</title>
    <!-- Icon pwa -->
     <link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#1e293b">
<link rel="apple-touch-icon" href="/assets/icons/icon-192.png">

    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/favicon.png">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/line-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .societe-card { border-left: 4px solid #ff9b44; }
        .stat-badge { font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="main-wrapper">
        <?php include './config/dashboard.php'; ?>

        <div class="page-wrapper">
            <div class="content container-fluid">
                <!-- Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title"><i class="la la-building"></i> Gestion des Sociétés</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= url('/utilisateur/dashboard') ?>">Dashboard</a></li>
                                <li class="breadcrumb-item active">Sociétés</li>
                            </ul>
                        </div>
                        <div class="col-auto float-right ml-auto">
                            <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_societe">
                                <i class="fa fa-plus"></i> Nouvelle société
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Statistiques globales -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="stats-info text-center">
                                            <h4><?= count($result) ?></h4>
                                            <p class="text-muted">Sociétés</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stats-info text-center">
                                            <h4><?= array_sum(array_column($result, 'nb_boutiques')) ?></h4>
                                            <p class="text-muted">Boutiques totales</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stats-info text-center">
                                            <h4><?= array_sum(array_column($result, 'nb_utilisateurs')) ?></h4>
                                            <p class="text-muted">Utilisateurs totaux</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stats-info text-center">
                                            <h4 class="text-success"><i class="fa fa-check-circle"></i></h4>
                                            <p class="text-muted">Système actif</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Liste des sociétés -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card mb-0">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="datatables table table-hover table-bordered mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th width="60">#</th>
                                                <th>Société</th>
                                                <th width="100">Logo</th>
                                                <th width="120">Boutiques</th>
                                                <th width="120">Utilisateurs</th>
                                                <th width="180">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($result)) {
                                                foreach ($result as $s) { ?>
                                            <tr>
                                                <td class="text-center"><strong><?= e($s['id_societe']) ?></strong></td>
                                                <td>
                                                    <strong><?= e($s['nom_societe']) ?></strong>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($s['logo_societe']) { ?>
                                                        <img src="../<?= e($s['logo_societe']) ?>" width="50" height="50" class="rounded" alt="Logo" style="object-fit: cover;">
                                                    <?php } else { ?>
                                                        <span class="badge badge-secondary">Aucun</span>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-info stat-badge">
                                                        <i class="la la-store"></i> <?= (int)$s['nb_boutiques'] ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-primary stat-badge">
                                                        <i class="la la-users"></i> <?= (int)$s['nb_utilisateurs'] ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <a href="#" class="btn btn-sm btn-info edit-societe mr-1"
                                                       data-id="<?= e($s['id_societe']) ?>"
                                                       data-nom="<?= e($s['nom_societe']) ?>"
                                                       data-logo="<?= e($s['logo_societe']) ?>"
                                                       title="Modifier">
                                                        <i class="la la-edit"></i>
                                                    </a>
                                                    <a href="#" class="btn btn-sm btn-danger delete-societe"
                                                       data-id="<?= e($s['id_societe']) ?>"
                                                       data-nom="<?= e($s['nom_societe']) ?>"
                                                       data-boutiques="<?= (int)$s['nb_boutiques'] ?>"
                                                       title="Supprimer">
                                                        <i class="la la-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php }
                                            } else { ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">
                                                    <i class="la la-inbox la-3x"></i><br>
                                                    Aucune société enregistrée
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL AJOUT -->
    <div class="modal fade" id="add_societe" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="POST" enctype="multipart/form-data">
                    <?= $csrf_field ?? '' ?>
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="la la-plus-circle"></i> Ajouter une société</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nom de la société <span class="text-danger">*</span></label>
                            <input type="text" name="sai_nom_societe" class="form-control" value="<?= e(old('sai_nom_societe')) ?>" required autofocus>
                            <small class="form-text text-muted">Nom complet de la société</small>
                        </div>
                        <div class="form-group">
                            <label>Logo <small class="text-muted">(Optionnel)</small></label>
                            <input type="file" name="sai_logo" accept="image/*" class="form-control-file">
                            <small class="form-text text-muted">Formats acceptés : JPG, PNG, GIF, WEBP (Max 5 MB)</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" name="btn-ajouter" class="btn btn-primary">
                            <i class="fa fa-save"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL MODIFICATION -->
    <div class="modal fade" id="editSocieteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="POST" enctype="multipart/form-data">
                    <?= $csrf_field ?? '' ?>
                    <input type="hidden" name="id_societe" id="edit_id">
                    <input type="hidden" name="logo_existant" id="edit_logo_existant">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title"><i class="la la-edit"></i> Modifier la société</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nom <span class="text-danger">*</span></label>
                            <input type="text" name="sai_nom_societe" id="edit_nom" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Logo actuel</label>
                            <div id="edit_logo_preview" class="mb-2"></div>
                        </div>
                        <div class="form-group">
                            <label>Nouveau logo <small class="text-muted">(Laisser vide pour conserver)</small></label>
                            <input type="file" name="sai_logo" accept="image/*" class="form-control-file">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" name="btn-modifier" class="btn btn-info">
                            <i class="fa fa-save"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL SUPPRESSION -->
    <div class="modal fade" id="deleteSocieteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="" method="POST">
                    <?= $csrf_field ?? '' ?>
                    <input type="hidden" name="id_societe" id="delete_id">
                    <div class="modal-body text-center py-4">
                        <div class="mb-3">
                            <i class="la la-warning la-3x text-warning"></i>
                        </div>
                        <h5>Confirmer la suppression</h5>
                        <p class="mb-2">Voulez-vous vraiment supprimer :</p>
                        <p><strong id="delete_nom" class="text-danger"></strong> ?</p>
                        <div class="alert alert-warning" id="delete_warning" style="display:none;">
                            <i class="fa fa-exclamation-triangle"></i> Cette société a <strong id="delete_boutiques_count"></strong> boutique(s).
                            La suppression sera refusée.
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" name="btn-supprimer" class="btn btn-danger" id="btn_confirm_delete">
                            <i class="fa fa-trash"></i> Supprimer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../assets/js/jquery-3.5.1.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/jquery.slimscroll.min.js"></script>
    <script src="../assets/js/jquery.dataTables.min.js"></script>
    <script src="../assets/js/dataTables.bootstrap4.min.js"></script>
    <script src="../assets/js/app.js"></script>

    <script>
        $(document).ready(function () {
            // DataTable
            $('.datatables').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json' },
                order: [[0, 'asc']]
            });

            // Modal Modifier
            $('.edit-societe').on('click', function (e) {
                e.preventDefault();
                const btn = $(this);
                $('#edit_id').val(btn.data('id'));
                $('#edit_nom').val(btn.data('nom'));
                $('#edit_logo_existant').val(btn.data('logo'));

                const logo = btn.data('logo');
                if (logo) {
                    $('#edit_logo_preview').html('<img src="../' + logo + '" width="100" class="rounded shadow-sm">');
                } else {
                    $('#edit_logo_preview').html('<span class="badge badge-secondary">Aucun logo</span>');
                }

                $('#editSocieteModal').modal('show');
            });

            // Modal Supprimer
            $('.delete-societe').on('click', function (e) {
                e.preventDefault();
                const btn = $(this);
                const nbBoutiques = parseInt(btn.data('boutiques'));

                $('#delete_id').val(btn.data('id'));
                $('#delete_nom').text(btn.data('nom'));

                if (nbBoutiques > 0) {
                    $('#delete_boutiques_count').text(nbBoutiques);
                    $('#delete_warning').show();
                    $('#btn_confirm_delete').prop('disabled', true).addClass('disabled');
                } else {
                    $('#delete_warning').hide();
                    $('#btn_confirm_delete').prop('disabled', false).removeClass('disabled');
                }

                $('#deleteSocieteModal').modal('show');
            });
        });
    </script>
</body>
</html>
