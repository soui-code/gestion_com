<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Clients</title>
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

                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title">Clients</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Clients</li>
                            </ul>
                        </div>
                        <div class="col-auto float-right ml-auto">
                            <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_client">
                                <i class="fa fa-plus"></i> Ajouter un client
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
                                                <th>Nom & Prénom</th>
                                                <th>Téléphone</th>
                                                <th>Email</th>
                                                <th>Adresse</th>
                                                <th>État</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($result)) {
                                                foreach ($result as $c) { ?>
                                            <tr>
                                                <td><?= htmlspecialchars($c['code_client']) ?></td>
                                                <td><strong><?= htmlspecialchars($c['nom_prenom_client']) ?></strong></td>
                                                <td><?= htmlspecialchars($c['telephone_client'] ?? '-') ?></td>
                                                <td>
                                                    <?php if ($c['email_client']) { ?>
                                                        <a href="mailto:<?= htmlspecialchars($c['email_client']) ?>">
                                                            <?= htmlspecialchars($c['email_client']) ?>
                                                        </a>
                                                    <?php } else { echo '-'; } ?>
                                                </td>
                                                <td>
                                                    <?= $c['adresse_client'] ? nl2br(htmlspecialchars($c['adresse_client'])) : '<em class="text-muted">Non renseignée</em>' ?>
                                                </td>
                                                <td>
                                                    <span class="badge badge-<?= $c['etat_client'] == 'actif' ? 'success' : 'secondary' ?>">
                                                        <?= $c['etat_client'] == 'actif' ? 'Actif' : 'Inactif' ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="#" class="text-info mr-3 edit-client"
                                                       data-code="<?= $c['code_client'] ?>"
                                                       data-nom="<?= htmlspecialchars($c['nom_prenom_client']) ?>"
                                                       data-telephone="<?= htmlspecialchars($c['telephone_client'] ?? '') ?>"
                                                       data-email="<?= htmlspecialchars($c['email_client'] ?? '') ?>"
                                                       data-adresse="<?= htmlspecialchars($c['adresse_client'] ?? '') ?>"
                                                       data-etat="<?= $c['etat_client'] ?>">
                                                       <i class="la la-edit"></i> Modifier
                                                    </a>
                                                    <br>
                                                    <a href="#" class="text-danger delete-client"
                                                       data-code="<?= $c['code_client'] ?>"
                                                       data-nom="<?= htmlspecialchars($c['nom_prenom_client']) ?>">
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

    <!-- MODAL AJOUT CLIENT -->
    <div class="modal fade" id="add_client" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="" method="POST">
                    <?= $csrf_field ?? '' ?>
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter un client</h5>
                        <button type="button" class="close" data-dismiss="modal">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nom & Prénom <span class="text-danger">*</span></label>
                                    <input type="text" name="sai_nom_prenom_client" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Téléphone</label>
                                    <input type="text" name="sai_telephone_client" class="form-control" placeholder="Ex: +225 01 02 03 04 05">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="texte" name="sai_email_client" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>État</label>
                                    <select name="sai_etat_client" class="form-control">
                                        <option value="actif">Actif</option>
                                        <option value="inactif">Inactif</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Adresse complète</label>
                            <textarea name="sai_adresse_client" class="form-control" rows="3" placeholder="Rue, quartier, ville..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" name="btn-ajouter-client" class="btn btn-success">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL MODIFICATION CLIENT -->
    <div class="modal fade" id="editClientModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="" method="POST">
                    <?= $csrf_field ?? '' ?>
                    <input type="hidden" name="sai_code_client" id="edit_code">

                    <div class="modal-header">
                        <h5 class="modal-title">Modifier le client</h5>
                        <button type="button" class="close" data-dismiss="modal">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nom & Prénom <span class="text-danger">*</span></label>
                                    <input type="text" name="sai_nom_prenom_client" id="edit_nom" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Téléphone</label>
                                    <input type="text" name="sai_telephone_client" id="edit_telephone" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="sai_email_client" id="edit_email" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>État</label>
                                    <select name="sai_etat_client" id="edit_etat" class="form-control">
                                        <option value="actif">Actif</option>
                                        <option value="inactif">Inactif</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Adresse complète</label>
                            <textarea name="sai_adresse_client" id="edit_adresse" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" name="btn-modifier-client" class="btn btn-primary">
                            Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL SUPPRESSION CLIENT -->
    <div class="modal fade" id="deleteClientModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="" method="POST">
                    <?= $csrf_field ?? '' ?>
                    <input type="hidden" name="sai_code_client" id="delete_code">
                    <div class="modal-body text-center py-4">
                        <h5>Confirmer la suppression</h5>
                        <p>Voulez-vous vraiment supprimer le client <strong id="delete_nom"></strong> ?</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" name="btn-supprimer-client" class="btn btn-danger">Supprimer</button>
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

            // === MODIFICATION ===
            $('.edit-client').on('click', function (e) {
                e.preventDefault();
                const btn = $(this);

                $('#edit_code').val(btn.data('code'));
                $('#edit_nom').val(btn.data('nom'));
                $('#edit_telephone').val(btn.data('telephone'));
                $('#edit_email').val(btn.data('email'));
                $('#edit_adresse').val(btn.data('adresse'));
                $('#edit_etat').val(btn.data('etat'));

                $('#editClientModal').modal('show');
            });

            // === SUPPRESSION ===
            $('.delete-client').on('click', function (e) {
                e.preventDefault();
                const btn = $(this);

                $('#delete_code').val(btn.data('code'));
                $('#delete_nom').text(btn.data('nom'));

                $('#deleteClientModal').modal('show');
            });
        });
    </script>
</body>
</html>