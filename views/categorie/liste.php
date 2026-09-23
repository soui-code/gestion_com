<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Boutiques</title>
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
<?php include 'config/dashboard.php'; ?>

<!-- Page Wrapper -->
<div class="page-wrapper">
    <!-- Page Content -->
    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Catégories</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php if(substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])),-1) =="/"){ echo (substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])), 0,-1)); }else{ echo ((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"]));} ?>/utilisateur/dashboard">Tableau de bord</a></li>
                        <li class="breadcrumb-item active">Catégories</li>
                    </ul>
                </div>
                <div class="col-auto float-right ml-auto">
                    <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_categorie"><i class="fa fa-plus"></i> Ajouter une catégorie</a>
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
                                <th>Description</th>
                                <th>Etat</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $cat) { ?>
                            <tr>
                                <td><?= $cat['id_categorie'] ?></td>
                                <td><?= htmlspecialchars($cat['nom_categorie']) ?></td>
                                <td><?= htmlspecialchars($cat['description']) ?></td>
                                <td>
                                    <?php if ($cat['etat'] == 'actif') { ?>
                                        <span class="badge bg-inverse-success">Actif</span>
                                    <?php } else { ?>
                                        <span class="badge bg-inverse-danger">Inactif</span>
                                    <?php } ?>
                                </td>
                                <td class="text-right">
                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item btn-modifier" href="#" data-toggle="modal" data-target="#edit_categorie" data-id="<?= $cat['id_categorie'] ?>" data-nom="<?= htmlspecialchars($cat['nom_categorie']) ?>" data-desc="<?= htmlspecialchars($cat['description']) ?>" data-etat="<?= $cat['etat'] ?>"><i class="fa fa-pencil m-r-5"></i> Modifier</a>
                                            <a class="dropdown-item btn-supprimer" href="#" data-toggle="modal" data-target="#delete_categorie" data-id="<?= $cat['id_categorie'] ?>"><i class="fa fa-trash-o m-r-5"></i> Supprimer</a>
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
    <div id="add_categorie" class="modal custom-modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter une Catégorie</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <?= $csrf_field ?? '' ?>
                        <div class="form-group">
                            <label>Nom de la catégorie <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="sai_nom" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="sai_description" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Etat</label>
                            <select class="select form-control" name="sai_etat">
                                <option value="actif">Actif</option>
                                <option value="inactif">Inactif</option>
                            </select>
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
    <div id="edit_categorie" class="modal custom-modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier la Catégorie</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <?= $csrf_field ?? '' ?>
                        <input type="hidden" name="sai_id" id="edit_id">
                        <div class="form-group">
                            <label>Nom <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="sai_nom" id="edit_nom" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="sai_description" id="edit_description" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Etat</label>
                            <select class="select form-control" name="sai_etat" id="edit_etat">
                                <option value="actif">Actif</option>
                                <option value="inactif">Inactif</option>
                            </select>
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
    <div class="modal custom-modal fade" id="delete_categorie" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-header">
                        <h3>Supprimer la catégorie</h3>
                        <p>Êtes-vous sûr de vouloir supprimer cette catégorie ? (Les produits liés perdront leur catégorie)</p>
                    </div>
                    <div class="modal-btn delete-action">
                        <form method="POST" action="">
                            <?= $csrf_field ?? '' ?>
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
    <script src="../assets/js/jquery.dataTables.min.js"></script>
    <script src="../assets/js/dataTables.bootstrap4.min.js"></script>
    <script src="../assets/js/app.js"></script>

<script>
$(document).ready(function() {
    // Initialiser DataTables avec recherche et pagination
    $('.datatable').DataTable({
        "language": {
            "search": "Rechercher :",
            "lengthMenu": "Afficher _MENU_ éléments",
            "info": "Affichage de _START_ à _END_ sur _TOTAL_ catégories",
            "infoEmpty": "Aucune catégorie",
            "infoFiltered": "(filtré de _MAX_ catégories au total)",
            "paginate": {
                "first": "Premier",
                "last": "Dernier",
                "next": "Suivant",
                "previous": "Précédent"
            },
            "zeroRecords": "Aucune catégorie trouvée",
            "emptyTable": "Aucune donnée disponible"
        },
        "pageLength": 10,
        "ordering": true,
        "order": [[1, 'asc']],
        "columnDefs": [
            { "orderable": false, "targets": [4] }
        ]
    });

    $('.btn-modifier').on('click', function() {
        $('#edit_id').val($(this).data('id'));
        $('#edit_nom').val($(this).data('nom'));
        $('#edit_description').val($(this).data('desc'));
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
