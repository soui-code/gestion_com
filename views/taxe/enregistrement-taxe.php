<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taxes</title>

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

                <!-- Page Header -->
                <div class="page-header">
                    <div class="row">
                        <div class="col">
                            <h3 class="page-title">Gestion des Taxes</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                                <li class="breadcrumb-item active">Taxes</li>
                            </ul>
                        </div>
                        <div class="col-auto float-right ml-auto">
                            <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_taxe">
                                <i class="fa fa-plus"></i> Ajouter une taxe
                            </a>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <div class="row">
                    <div class="col-sm-12">
                        <div class="card mb-0">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="datatables table table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Titre de la taxe</th>
                                                <th>Taux (%)</th>
                                                <th>Action</th>
                                                <th>État</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($result)) {
                                                foreach ($result as $taxe) { ?>
                                            <tr>
                                                <td><?= htmlspecialchars($taxe['code_taxe']) ?></td>
                                                <td><?= htmlspecialchars($taxe['titre_taxe']) ?></td>
                                                <td><?= number_format($taxe['taux_taxe'], 2) ?> %</td>
                                                <td>
                                                    <span class="badge badge-<?= $taxe['action_taxe'] == 'ajouter' ? 'success' : 'warning' ?>">
                                                        <?= $taxe['action_taxe'] == 'ajouter' ? 'Ajoutée au prix' : 'Soustraite du prix' ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-<?= $taxe['etat_taxe'] == 'active' ? 'primary' : 'secondary' ?>">
                                                        <?= $taxe['etat_taxe'] == 'active' ? 'Active' : 'Inactive' ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="#" class="text-secondary mr-3 edit-taxe"
                                                       data-code="<?= $taxe['code_taxe'] ?>"
                                                       data-titre="<?= htmlspecialchars($taxe['titre_taxe']) ?>"
                                                       data-taux="<?= $taxe['taux_taxe'] ?>"
                                                       data-action="<?= $taxe['action_taxe'] ?>"
                                                       data-etat="<?= $taxe['etat_taxe'] ?>">
                                                        <i class="la la-edit"></i> Modifier
                                                    </a>
                                                    <br>
                                                    <a href="#" class="text-danger delete-taxe"
                                                       data-code="<?= $taxe['code_taxe'] ?>"
                                                       data-titre="<?= htmlspecialchars($taxe['titre_taxe']) ?>">
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

    <!-- MODAL AJOUT TAXE -->
    <div class="modal fade" id="add_taxe" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter une nouvelle taxe</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Titre de la taxe <span class="text-danger">*</span></label>
                            <input type="text" name="sai_titre_taxe" class="form-control" required placeholder="ex: TVA, Taxe écologique...">
                        </div>
                        <div class="form-group">
                            <label>Taux (%) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" max="100" name="sai_taux_taxe" class="form-control" required value="0.00">
                        </div>
                        <div class="form-group">
                            <label>Action</label>
                            <select name="sai_action_taxe" class="form-control">
                                <option value="ajouter">Ajouter au prix HT</option>
                                <option value="soustraire">Soustraire du prix HT</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>État</label>
                            <select name="sai_etat_taxe" class="form-control">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" name="btn-ajouter-taxe" class="btn btn-success">
                            <i class="fa fa-plus"></i> Ajouter la taxe
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL MODIFICATION TAXE -->
    <div class="modal fade" id="editTaxeModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Modifier la taxe</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="sai_code_taxe" id="edit_code">

                        <div class="form-group">
                            <label>Titre de la taxe <span class="text-danger">*</span></label>
                            <input type="text" name="sai_titre_taxe" id="edit_titre" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Taux (%) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" max="100" name="sai_taux_taxe" id="edit_taux" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Action</label>
                            <select name="sai_action_taxe" id="edit_action" class="form-control">
                                <option value="ajouter">Ajouter au prix HT</option>
                                <option value="soustraire">Soustraire du prix HT</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>État</label>
                            <select name="sai_etat_taxe" id="edit_etat" class="form-control">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" name="btn-modifier-taxe" class="btn btn-primary">
                            <i class="fa fa-save"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL SUPPRESSION TAXE -->
    <div class="modal fade" id="deleteTaxeModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="" method="POST">
                    <input type="hidden" name="sai_code_taxe" id="delete_code">
                    <div class="modal-body text-center">
                        <h5>Confirmer la suppression</h5>
                        <p>Voulez-vous vraiment supprimer la taxe <strong id="delete_titre"></strong> ?</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" name="btn-supprimer" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" name="btn-supprimer-taxe" class="btn btn-danger">Supprimer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
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
                language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json' }
            });

            // Modification
            $('.edit-taxe').on('click', function (e) {
                e.preventDefault();
                const btn = $(this);

                $('#edit_code').val(btn.data('code'));
                $('#edit_titre').val(btn.data('titre'));
                $('#edit_taux').val(btn.data('taux'));
                $('#edit_action').val(btn.data('action'));
                $('#edit_etat').val(btn.data('etat'));

                $('#editTaxeModal').modal('show');
            });

            // Suppression
            $('.delete-taxe').on('click', function (e) {
                e.preventDefault();
                const btn = $(this);

                $('#delete_code').val(btn.data('code'));
                $('#delete_titre').text(btn.data('titre'));

                $('#deleteTaxeModal').modal('show');
            });
        });
    </script>
</body>
</html>