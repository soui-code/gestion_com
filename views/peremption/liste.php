<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Péremptions</title>
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
</head>
<body>
    <div class="main-wrapper">
        <?php include './config/dashboard.php'; ?>

        <div class="page-wrapper">
            <div class="content container-fluid">
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title">Péremptions</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Péremptions</li>
                            </ul>
                        </div>
                        <div class="col-auto float-right ml-auto">
                            <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_peremption">
                                <i class="fa fa-plus"></i> Ajouter
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="card mb-0">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="datatables table table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Produit</th>
                                                <th>Date fabrication</th>
                                                <th>Date péremption</th>
                                                <th>Quantité</th>
                                                <th>Statut</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($result)) {
                                                foreach ($result as $p) {
                                                    $statut = '';
                                                    $badge = '';
                                                    if ($p['date_peremption'] <= date('Y-m-d')) {
                                                        $statut = 'Périmé';
                                                        $badge = 'danger';
                                                    } elseif ($p['date_peremption'] <= date('Y-m-d', strtotime('+30 days'))) {
                                                        $statut = 'Expire bientôt';
                                                        $badge = 'warning';
                                                    } else {
                                                        $statut = 'OK';
                                                        $badge = 'success';
                                                    }
                                                ?>
                                            <tr>
                                                <td><?= $p['id'] ?></td>
                                                <td><?= htmlspecialchars($p['produit_designation'] ?? 'N/A') ?></td>
                                                <td><?= $p['date_fabrication'] ? date('d/m/Y', strtotime($p['date_fabrication'])) : '-' ?></td>
                                                <td><?= date('d/m/Y', strtotime($p['date_peremption'])) ?></td>
                                                <td><?= $p['nombre'] ?></td>
                                                <td><span class="badge badge-<?= $badge ?>"><?= $statut ?></span></td>
                                                <td>
                                                    <a href="#" class="text-info mr-3 edit-peremption"
                                                       data-id="<?= $p['id'] ?>"
                                                       data-id_article="<?= $p['id_article'] ?>"
                                                       data-date_fabrication="<?= $p['date_fabrication'] ?>"
                                                       data-date_peremption="<?= $p['date_peremption'] ?>"
                                                       data-nombre="<?= $p['nombre'] ?>">
                                                        <i class="la la-edit"></i> Modifier
                                                    </a>
                                                    <br>
                                                    <a href="#" class="text-danger delete-peremption"
                                                       data-id="<?= $p['id'] ?>">
                                                        <i class="la la-trash"></i> Supprimer
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php }
                                            } ?>
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
    <div class="modal fade" id="add_peremption" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="POST">
                    <?= $csrf_field ?? '' ?>
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter une péremption</h5>
                        <button type="button" class="close" data-dismiss="modal">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Produit <span class="text-danger">*</span></label>
                            <select name="id_article" class="form-control select" required>
                                <option value="">-- Sélectionner --</option>
                                <?php foreach ($produits as $pr) { ?>
                                    <option value="<?= $pr['reference'] ?>"><?= htmlspecialchars($pr['designation']) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Date de fabrication</label>
                            <input type="date" name="date_fabrication" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Date de péremption <span class="text-danger">*</span></label>
                            <input type="date" name="date_peremption" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Quantité</label>
                            <input type="number" name="nombre" class="form-control" value="1" min="1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" name="btn-ajouter" class="btn btn-success">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL MODIFICATION -->
    <div class="modal fade" id="editPeremptionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="POST">
                    <?= $csrf_field ?? '' ?>
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Modifier la péremption</h5>
                        <button type="button" class="close" data-dismiss="modal">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Produit <span class="text-danger">*</span></label>
                            <select name="id_article" id="edit_id_article" class="form-control select" required>
                                <option value="">-- Sélectionner --</option>
                                <?php foreach ($produits as $pr) { ?>
                                    <option value="<?= $pr['reference'] ?>"><?= htmlspecialchars($pr['designation']) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Date de fabrication</label>
                            <input type="date" name="date_fabrication" id="edit_date_fabrication" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Date de péremption <span class="text-danger">*</span></label>
                            <input type="date" name="date_peremption" id="edit_date_peremption" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Quantité</label>
                            <input type="number" name="nombre" id="edit_nombre" class="form-control" min="1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" name="btn-modifier" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL SUPPRESSION -->
    <div class="modal fade" id="deletePeremptionModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="" method="POST">
                    <?= $csrf_field ?? '' ?>
                    <input type="hidden" name="id" id="delete_id">
                    <div class="modal-body text-center py-4">
                        <h5>Confirmer la suppression</h5>
                        <p>Voulez-vous vraiment supprimer cette péremption ?</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" name="btn-supprimer" class="btn btn-danger">Supprimer</button>
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
            $('.datatables').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json' },
                order: [[3, 'asc']]
            });

            $('.edit-peremption').on('click', function (e) {
                e.preventDefault();
                const btn = $(this);
                $('#edit_id').val(btn.data('id'));
                $('#edit_id_article').val(btn.data('id_article')).trigger('change');
                $('#edit_date_fabrication').val(btn.data('date_fabrication'));
                $('#edit_date_peremption').val(btn.data('date_peremption'));
                $('#edit_nombre').val(btn.data('nombre'));
                $('#editPeremptionModal').modal('show');
            });

            $('.delete-peremption').on('click', function (e) {
                e.preventDefault();
                $('#delete_id').val($(this).data('id'));
                $('#deletePeremptionModal').modal('show');
            });
        });
    </script>
</body>
</html>
