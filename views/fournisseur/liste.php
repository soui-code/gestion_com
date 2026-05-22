<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Boutiques</title>

    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/favicon.png">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/line-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="main-wrapper">

<?php include 'config/dashboard.php'; ?>

<!-- Page Wrapper -->
<div class="page-wrapper">
    <!-- Page Content -->
    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Fournisseurs</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php if(substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])),-1) =="/"){ echo (substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])), 0,-1)); }else{ echo ((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"]));} ?>/utilisateur/dashboard">Tableau de bord</a></li>
                        <li class="breadcrumb-item active">Fournisseurs</li>
                    </ul>
                </div>
                <div class="col-auto float-right ml-auto">
                    <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_fournisseur"><i class="fa fa-plus"></i> Ajouter un fournisseur</a>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped custom-table datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nom</th>
                                <th>Téléphone</th>
                                <th>Email</th>
                                <th>Adresse</th>
                                <th>Etat</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($fournisseurs as $f) { ?>
                            <tr>
                                <td><?= $f['id_fournisseur'] ?></td>
                                <td><?= htmlspecialchars($f['nom_fournisseur']) ?></td>
                                <td><?= htmlspecialchars($f['telephone']) ?></td>
                                <td><?= htmlspecialchars($f['email']) ?></td>
                                <td><?= htmlspecialchars($f['adresse']) ?></td>
                                <td>
                                    <?php if ($f['etat'] == 'actif') { ?>
                                        <span class="badge bg-inverse-success">Actif</span>
                                    <?php } else { ?>
                                        <span class="badge bg-inverse-danger">Inactif</span>
                                    <?php } ?>
                                </td>
                                <td class="text-right">
                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item btn-modifier" href="#" data-toggle="modal" data-target="#edit_fournisseur" data-id="<?= $f['id_fournisseur'] ?>" data-nom="<?= htmlspecialchars($f['nom_fournisseur']) ?>" data-tel="<?= htmlspecialchars($f['telephone']) ?>" data-email="<?= htmlspecialchars($f['email']) ?>" data-adresse="<?= htmlspecialchars($f['adresse']) ?>" data-etat="<?= $f['etat'] ?>"><i class="fa fa-pencil m-r-5"></i> Modifier</a>
                                            <a class="dropdown-item btn-supprimer" href="#" data-toggle="modal" data-target="#delete_fournisseur" data-id="<?= $f['id_fournisseur'] ?>"><i class="fa fa-trash-o m-r-5"></i> Supprimer</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- /Page Content -->

    <!-- Add Modal -->
    <div id="add_fournisseur" class="modal custom-modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter un Fournisseur</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Nom du fournisseur <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="sai_nom" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Téléphone</label>
                                    <input class="form-control" type="text" name="sai_tel">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input class="form-control" type="email" name="sai_email">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Etat</label>
                                    <select class="select form-control" name="sai_etat">
                                        <option value="actif">Actif</option>
                                        <option value="inactif">Inactif</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Adresse</label>
                                    <textarea class="form-control" name="sai_adresse" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="submit-section">
                            <button class="btn btn-primary submit-btn" type="submit" name="btn-ajouter">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Add Modal -->

    <!-- Edit Modal -->
    <div id="edit_fournisseur" class="modal custom-modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier le Fournisseur</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <input type="hidden" name="sai_id" id="edit_id">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Nom <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="sai_nom" id="edit_nom" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Téléphone</label>
                                    <input class="form-control" type="text" name="sai_tel" id="edit_tel">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input class="form-control" type="email" name="sai_email" id="edit_email">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Etat</label>
                                    <select class="select form-control" name="sai_etat" id="edit_etat">
                                        <option value="actif">Actif</option>
                                        <option value="inactif">Inactif</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Adresse</label>
                                    <textarea class="form-control" name="sai_adresse" id="edit_adresse" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="submit-section">
                            <button class="btn btn-primary submit-btn" type="submit" name="btn-modifier">Sauvegarder</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Edit Modal -->

    <!-- Delete Modal -->
    <div class="modal custom-modal fade" id="delete_fournisseur" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-header">
                        <h3>Supprimer le fournisseur</h3>
                        <p>Êtes-vous sûr de vouloir supprimer ce fournisseur ?</p>
                    </div>
                    <div class="modal-btn delete-action">
                        <form method="POST" action="">
                            <input type="hidden" name="sai_id" id="delete_id">
                            <div class="row">
                                <div class="col-6">
                                    <button type="submit" name="btn-supprimer" class="btn btn-primary continue-btn w-100">Supprimer</button>
                                </div>
                                <div class="col-6">
                                    <a href="javascript:void(0);" data-dismiss="modal" class="btn btn-primary cancel-btn w-100">Annuler</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Delete Modal -->

</div>
<!-- /Page Wrapper -->
<?php // require 'config/pieds.php'; ?>
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
$(document).ready(function() {
    $('.btn-modifier').on('click', function() {
        $('#edit_id').val($(this).data('id'));
        $('#edit_nom').val($(this).data('nom'));
        $('#edit_tel').val($(this).data('tel'));
        $('#edit_email').val($(this).data('email'));
        $('#edit_adresse').val($(this).data('adresse'));
        $('#edit_etat').val($(this).data('etat'));
        $('#edit_etat').select2().trigger('change');
    });

    $('.btn-supprimer').on('click', function() {
        $('#delete_id').val($(this).data('id'));
    });
});
</script>
</body>
</html>
