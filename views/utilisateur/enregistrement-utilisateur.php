<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Utilisateurs</title>
    <!-- Icon pwa -->
     <link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#1e293b">
<link rel="apple-touch-icon" href="/assets/icons/icon-192.png">

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/favicon.png">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
    <!-- Lineawesome CSS -->
    <link rel="stylesheet" href="../assets/css/line-awesome.min.css">
    <!-- Datatable CSS -->
    <link rel="stylesheet" href="../assets/css/dataTables.bootstrap4.min.css">
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="../assets/css/select2.min.css">
    <!-- Main CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Main Wrapper -->
    <div class="main-wrapper">

        <?php include './config/dashboard.php'; ?>    

        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <div class="content container-fluid">

                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title">Utilisateurs</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/utilisateur/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item active">Utilisateurs</li>
                            </ul>
                        </div>
                        <div class="col-auto float-right ml-auto">
                            <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_user">
                                <i class="fa fa-plus"></i> Ajouter un utilisateur
                            </a>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <!-- Search Filter -->
                <!-- <div class="row filter-row">
                    <div class="col-sm-6 col-md-3"></div>
                    <div class="col-sm-6 col-md-3"></div>
                    <div class="col-sm-6 col-md-3">
                        <form action="" method="POST">
                            <div class="form-group form-focus">
                                <input type="text" class="form-control floating" name="sai_recherche" placeholder=" ">
                                <label class="focus-label">Nom</label>
                            </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <button name="btn-recherche" class="btn btn-success btn-block" name="btn-rechercher" >Rechercher</button>
                    </div>
                </div>
                </form> -->
                <!-- /Search Filter -->

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped custom-table datatables">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nom</th>
                                        <th>Login</th>
                                        <th>Numéro</th>
                                        <th>Rôle</th>
                                        <th>Etat</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($result)) {
                                        foreach ($result as $u) { ?>
                                            <tr>
                                                <td><?php echo $u['utilisateur_id']; ?></td>
                                                <td><?php echo htmlspecialchars($u['nom']); ?></td>
                                                <td><?php echo htmlspecialchars($u['login']); ?></td>
                                                <td><?php echo htmlspecialchars($u['numero']); ?></td>
                                                <td><?php echo htmlspecialchars($u['role']); ?></td>
                                                <td>
                                                    <span class="badge bg-inverse-<?php if ($u['etat']=="actif") {
                                                        echo "success";
                                                    } else{
                                                        echo "danger";
                                                    } ?>">
                                                        <?php echo ucfirst($u['etat']); ?>
                                                    </span>
                                                </td>
                                                <td class="text-right">
                                                    <!-- Bouton Modifier dynamique -->
                                                    <a href="#" class="btn btn-sm btn-secondary edit-user-btn"
                                                       data-toggle="modal" data-target="#edit_user"
                                                       data-id="<?php echo $u['utilisateur_id']; ?>"
                                                       data-nom="<?php echo htmlspecialchars($u['nom']); ?>"
                                                       data-login="<?php echo htmlspecialchars($u['login']); ?>"
                                                       data-numero="<?php echo htmlspecialchars($u['numero']); ?>"
                                                       data-role="<?php echo $u['role']; ?>"
                                                       data-etat="<?php echo $u['etat']; ?>">
                                                        <i class="fa fa-pencil"></i> Modifier
                                                    </a>

                                                    <!-- Bouton Supprimer avec confirmation -->
                                                    <a href="#" class="btn btn-sm btn-danger delete-user-btn"
                                                       data-toggle="modal" data-target="#delete_user"
                                                       data-id="<?php echo $u['utilisateur_id']; ?>"
                                                       data-nom="<?php echo htmlspecialchars($u['nom']); ?>">
                                                        <i class="fa fa-trash"></i> Supprimer
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php }
                                    } else { ?>
                                        <tr><td colspan="6" class="text-center">Aucun utilisateur trouvé</td></tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Page Wrapper -->
    </div>
    <!-- /Main Wrapper -->

    <!-- ADD USER MODAL -->
    <div id="add_user" class="modal custom-modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter un utilisateur</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST">
                        <?= $csrf_field ?? '' ?>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Nom <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="sai_nom" value="<?= e(old('sai_nom')) ?>" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Numéro <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="sai_numero" value="<?= e(old('sai_numero')) ?>" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Login <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="sai_login" value="<?= e(old('sai_login')) ?>" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Mot de passe <span class="text-danger">*</span></label>
                                    <input class="form-control" type="password" name="sai_mdp" minlength="6" required>
                                    <small class="form-text text-muted">Minimum 6 caractères</small>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Rôle <span class="text-danger">*</span></label>
                                    <select class="select form-control" name="sai_role" id="add_role" required>
                                        <option value="">-- Sélectionner un rôle --</option>
                                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                        <option value="admin" <?= old('sai_role') == 'admin' ? 'selected' : '' ?>>Admin</option>
                                        <option value="manager" <?= old('sai_role') == 'manager' ? 'selected' : '' ?>>Manager</option>
                                        <?php endif; ?>
                                        <option value="caissier" <?= old('sai_role') == 'caissier' ? 'selected' : '' ?>>Caissier</option>
                                    </select>
                                </div>
                            </div>

                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <!-- Admin : Choisir la société -->
                            <div class="col-sm-6" id="add_societe_field">
                                <div class="form-group">
                                    <label>Société <span class="text-danger">*</span></label>
                                    <select class="select form-control" name="id_societe" required>
                                        <option value="">-- Sélectionner une société --</option>
                                        <?php if (!empty($societes)) {
                                            foreach ($societes as $s) { ?>
                                                <option value="<?= $s['id_societe'] ?>"><?= e($s['nom_societe']) ?></option>
                                            <?php }
                                        } ?>
                                    </select>
                                    <small class="form-text text-muted">Société à laquelle l'utilisateur sera rattaché</small>
                                </div>
                            </div>
                            <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'manager'): ?>
                            <!-- Manager : Choisir la boutique -->
                            <div class="col-sm-6" id="add_boutique_field">
                                <div class="form-group">
                                    <label>Boutique <span class="text-danger">*</span></label>
                                    <select class="select form-control" name="id_boutique" required>
                                        <option value="">-- Sélectionner une boutique --</option>
                                        <?php if (!empty($boutiques)) {
                                            foreach ($boutiques as $b) { ?>
                                                <option value="<?= $b['code_boutique'] ?>"><?= e($b['nom_boutique']) ?></option>
                                            <?php }
                                        } ?>
                                    </select>
                                    <small class="form-text text-muted">Boutique où le caissier travaillera</small>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="submit-section">
                            <button class="btn btn-primary submit-btn" type="submit" name="btn-ajouter">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- EDIT USER MODAL -->
    <div id="edit_user" class="modal custom-modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier l'utilisateur</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST">
                        <?= $csrf_field ?? '' ?>
                        <input type="hidden" name="id_utilisateur" value="" id="edit_id">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Nom <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="sai_nom" id="edit_nom" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Numéro <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="sai_numero" id="edit_numero" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Login <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="sai_login" id="edit_login" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Rôle <span class="text-danger">*</span></label>
                                    <select class="select form-control" name="sai_role" id="edit_role" required>
                                        <option value="caissier">Caissier</option>
                                        <option value="manager">Manager</option>
                                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                        <option value="admin">Admin</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>État <span class="text-danger">*</span></label>
                                    <select class="select form-control" name="sai_etat" id="edit_etat" required>
                                        <option value="actif">Actif</option>
                                        <option value="inactif">Inactif</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Nouveau mot de passe</label>
                                    <input class="form-control" type="password" name="sai_nouveau_mdp" minlength="6">
                                    <small class="form-text text-muted">Laisser vide pour ne pas changer</small>
                                </div>
                            </div>
                        </div>
                        <div class="submit-section">
                            <button type="submit" name="btn-modifier" class="btn btn-primary submit-btn">
                                Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- DELETE USER MODAL -->
    <div class="modal custom-modal fade" id="delete_user" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="" method="POST">
                    <?= $csrf_field ?? '' ?>
                    <input type="hidden" name="id_utilisateur" id="delete_user_id">
                    <div class="modal-body">
                        <div class="form-header">
                            <h3>Suppression de l'utilisateur</h3>
                            <p>Êtes-vous sûr de vouloir supprimer <strong id="delete_user_name"></strong> ?</p>
                        </div>
                        <div class="modal-btn delete-action">
                            <div class="row">
                                <div class="col-6">
                                    <button type="submit" name="btn-supprimer" class="btn btn-danger continue-btn btn-block">Supprimer</button>
                                </div>
                                <div class="col-6">
                                    <button type="button" data-dismiss="modal" class="btn btn-secondary cancel-btn btn-block">Annuler</button>
                                </div>
                            </div>
                        </div>
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
    <script src="../assets/js/select2.min.js"></script>
    <script src="../assets/js/moment.min.js"></script>
    <script src="../assets/js/bootstrap-datetimepicker.min.js"></script>
    <script src="../assets/js/jquery.dataTables.min.js"></script>
    <script src="../assets/js/dataTables.bootstrap4.min.js"></script>
    <script src="../assets/js/app.js"></script>


    <script>
    $(document).ready(function() {
      $('.datatables').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json' }
            });
        // Modal Modifier
        $('.edit-user-btn').on('click', function() {
            var id     = $(this).data('id');
            var nom    = $(this).data('nom');
            var login  = $(this).data('login');
            var numero = $(this).data('numero');
            var role   = $(this).data('role');
            var etat   = $(this).data('etat');

            $('#edit_id').val(id);
            $('#edit_nom').val(nom);
            $('#edit_login').val(login);
            $('#edit_numero').val(numero);
            $('#edit_role').val(role).trigger('change');
            $('#edit_etat').val(etat).trigger('change');
        });

        // Modal Supprimer
        $('.delete-user-btn').on('click', function() {
            var id  = $(this).data('id');
            var nom = $(this).data('nom');

            $('#delete_user_name').text(nom);
            $('#delete_user_id').val(id);
        });
    });
    </script>
</body>
</html>