<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, produit-scalable=0">
    <title>Data Tables - HRMS admin template</title>

    <!-- Favicon / CSS (inchangés) -->
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
                            <h3 class="page-title">Produits</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                                <li class="breadcrumb-item active">Produits</li>
                            </ul>
                        </div>
                        <div class="col-auto float-right ml-auto">
                            <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_produit">
                                <i class="fa fa-plus"></i> Ajouter un produit
                            </a>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <div class="row">
                    <div class="col-sm-12">
                        <div class="card mb-0">
                            <div class="card-body">

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="font-weight-bold">Filtrer par catégorie :</label>
                                        <select id="filtre_categorie" class="form-control select">
                                            <option value="">Toutes les catégories</option>
                                            <?php foreach ($categories as $cat) { ?>
                                                <option value="<?= htmlspecialchars($cat['nom_categorie']) ?>"><?= htmlspecialchars($cat['nom_categorie']) ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="datatables table table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Désignation</th>
                                                <th>Catégorie</th>
                                                <th>Fournisseur</th>
                                                <th>Prix achat</th>
                                                <th>Prix vente</th>
                                                <th>Stock</th>
                                                <th>Stock alert</th>
                                                <th>Photo</th>
                                                <th>Etat</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($result)) {
                                                foreach ($result as $key => $p) { ?>
                                            <tr>
                                                <td><?= htmlspecialchars($p['reference']) ?></td>
                                                <td>
    <a href="#" class="text-success font-weight-bold approvisionnement-product"
       data-reference="<?= htmlspecialchars($p['reference']) ?>"
       data-designation="<?= htmlspecialchars($p['designation']) ?>"
       data-stock="<?= $p['stock'] ?>">
        <i class="fa fa-plus-circle"></i> <?= htmlspecialchars($p['designation']) ?>
    </a>
</td>
                                                <td><?= htmlspecialchars($p['nom_categorie'] ?? 'Aucune') ?></td>
                                                <td><?= htmlspecialchars($p['nom_fournisseur'] ?? 'Aucun') ?></td>
                                                <td><?= htmlspecialchars($p['prix_achat']) ?> Fr</td>
                                                <td><?= htmlspecialchars($p['prix_vente']) ?> Fr</td>
                                                <td><?= htmlspecialchars($p['stock']) ?></td>
                                                <td><?= htmlspecialchars($p['stock_alert']) ?></td>
                                                <td>
                                                    <?php if ($p['photo']) { ?>
                                                        <img src="data:<?= $p['photo_type'] ?>;base64,<?= base64_encode($p['photo']) ?>" width="50" height="50" alt="Produit">
                                                    <?php } else { echo "Pas de photo"; } ?>
                                                </td>
                                                <td><?= htmlspecialchars($p['etat']) ?></td>
                                                <td>
                                                                                                       <a href="#" class="text-secondary mr-3 edit-product"
                                                       data-reference="<?= htmlspecialchars($p['reference']) ?>"
                                                       data-designation="<?= htmlspecialchars($p['designation']) ?>"
                                                       data-id_categorie="<?= htmlspecialchars($p['id_categorie']) ?>"
                                                       data-id_fournisseur="<?= htmlspecialchars($p['id_fournisseur']) ?>"
                                                       data-prix_achat="<?= htmlspecialchars($p['prix_achat']) ?>"
                                                       data-prix_vente="<?= htmlspecialchars($p['prix_vente']) ?>"
                                                       data-stock="<?= htmlspecialchars($p['stock']) ?>"
                                                       data-stock_alert="<?= htmlspecialchars($p['stock_alert']) ?>"
                                                       data-description="<?= htmlspecialchars($p['description']) ?>"
                                                       data-etat="<?= htmlspecialchars($p['etat']) ?>">
                                                        <i class="la la-edit"></i> Modifier
                                                    </a>
                                                    <br>
                                                
                                                    <a href="#" class="text-danger delete-product"
                                                       data-reference="<?= $p['reference'] ?>"
                                                       data-designation="<?= htmlspecialchars($p['designation']) ?>">
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

    <!-- MODAL APPROVISIONNEMENT -->
<div class="modal fade" id="approvisionnementModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="" method="POST">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                         Approvisionner le stock
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <h4>Réapprovisionner :</h4>
                        <p class="lead font-weight-bold text-primary" id="modal-designation">Produit</p>
                        <p>Stock actuel : <span id="modal-stock-actuel">0</span></p>
                    </div>

                    <div class="form-group">
                        <label for="quantite_ajoutee"><strong>Quantité à ajouter</strong></label>
                        <input type="number" 
                               name="sai_quantite" 
                               id="quantite_ajoutee" 
                               class="form-control form-control-lg text-center" 
                               min="1" 
                               value="1" 
                               required 
                               placeholder="Ex: 50">
                    </div>

                    <!-- Champ caché pour la référence -->
                    <input type="hidden" name="sai_reference" id="modal-reference">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times"></i> Annuler
                    </button>
                    <button type="submit" name="btn-approvisionner" class="btn btn-success btn-lg">
                        <i class="fa fa-check"></i> Confirmer l'approvisionnement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
    <!--  MODAL AJOUT PRODUIT  -->
<div class="modal fade" id="add_produit" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Ajouter un nouveau produit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Désignation <span class="text-danger">*</span></label>
                                <input type="text" name="sai_designation" class="form-control" required placeholder="Nom du produit">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Catégorie</label>
                                <select name="sai_categorie" class="form-control select">
                                    <option value="">-- Sélectionner --</option>
                                    <?php foreach ($categories as $cat) { ?>
                                        <option value="<?= $cat['id_categorie'] ?>"><?= htmlspecialchars($cat['nom_categorie']) ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fournisseur</label>
                                <select name="sai_fournisseur" class="form-control select">
                                    <option value="">-- Sélectionner --</option>
                                    <?php foreach ($fournisseurs as $fr) { ?>
                                        <option value="<?= $fr['id_fournisseur'] ?>"><?= htmlspecialchars($fr['nom_fournisseur']) ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Prix d'achat (Fr) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="sai_prix_achat" class="form-control" required min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Prix de vente (Fr) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="sai_prix_vente" class="form-control" required min="0">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Stock initial</label>
                                <input type="number" name="sai_stock" class="form-control" value="0" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Seuil alerte stock</label>
                                <input type="number" name="sai_stock_alert" class="form-control" value="10" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="sai_description" class="form-control" rows="3" placeholder="Description facultative..."></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>État</label>
                                <select name="sai_etat" class="form-control">
                                    <option value="actif" selected>Actif</option>
                                    <option value="inactif">Inactif</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Photo du produit</label>
                                <input type="file" name="sai_photo" accept="image/*" class="form-control">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" name="btn-ajouter" class="btn btn-danger">
                        <i class="fa fa-plus"></i> Ajouter le produit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL MODIFICATION -->
<div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Modifier le produit
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <!-- Référence cachée -->
                    <input type="hidden" name="sai_reference" id="edit_reference">

                    <!-- Désignation -->
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>Désignation <span class="text-danger">*</span></label>
                                <input type="text" name="sai_designation" id="edit_designation" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <!-- Categories et Fournisseurs -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Catégorie</label>
                                <select name="sai_categorie" id="edit_categorie" class="form-control select">
                                    <option value="">-- Sélectionner --</option>
                                    <?php foreach ($categories as $cat) { ?>
                                        <option value="<?= $cat['id_categorie'] ?>"><?= htmlspecialchars($cat['nom_categorie']) ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fournisseur</label>
                                <select name="sai_fournisseur" id="edit_fournisseur" class="form-control select">
                                    <option value="">-- Sélectionner --</option>
                                    <?php foreach ($fournisseurs as $fr) { ?>
                                        <option value="<?= $fr['id_fournisseur'] ?>"><?= htmlspecialchars($fr['nom_fournisseur']) ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Prix -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Prix d'achat <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="sai_prix_achat" id="edit_prix_achat" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Prix de vente <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="sai_prix_vente" id="edit_prix_vente" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <!-- Stock -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Stock actuel</label>
                                <input type="number" name="sai_stock" id="edit_stock" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Seuil alerte stock</label>
                                <input type="number" name="sai_stock_alert" id="edit_stock_alert" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="sai_description" id="edit_description" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- État + Photo -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>État</label>
                                <select name="sai_etat" id="edit_etat" class="form-control">
                                    <option value="actif">Actif</option>
                                    <option value="inactif">Inactif</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nouvelle photo <small class="text-muted">(facultatif)</small></label>
                                <input type="file" name="sai_photo" accept="image/*" class="form-control">
                                <small class="text-muted">Laisser vide pour conserver l'actuelle</small>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.modal-body -->

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times"></i> Annuler
                    </button>
                    <button type="submit" name="btn-modifier" class="btn btn-primary">
                        <i class="fa fa-save"></i> Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- MODAL SUPPRESSION  -->
    <div class="modal fade" id="deleteProductModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="" method="POST">
                    <div class="modal-body text-center">
                        <h4>Confirmer la suppression</h4>
                        <p>Êtes-vous sûr de vouloir supprimer le produit <strong id="delete_designation"></strong> ?</p>
                        <input type="hidden" name="sai_reference" id="delete_reference">
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button  class="btn btn-danger" name="btn-supprimer">Supprimer définitivement</button>
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
            var table = $('.datatables').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json' }
            });

            $('#filtre_categorie').on('change', function () {
                var search = this.value ? '^' + $.fn.dataTable.util.escapeRegex(this.value) + '$' : '';
                table.column(2).search(search, true, false).draw();
            });
            
            // --- Modal Modification ---
            $('.edit-product').on('click', function (e) {
                e.preventDefault();
                const btn = $(this);

                $('#edit_reference').val(btn.data('reference'));
                $('#edit_designation').val(btn.data('designation'));
                $('#edit_categorie').val(btn.data('id_categorie'));
                $('#edit_fournisseur').val(btn.data('id_fournisseur'));
                
                // Si select2 est utilisé dans le template, on le rafraichit
                if($.fn.select2) {
                    $('#edit_categorie').trigger('change');
                    $('#edit_fournisseur').trigger('change');
                }

                $('#edit_prix_achat').val(btn.data('prix_achat'));
                $('#edit_prix_vente').val(btn.data('prix_vente'));
                $('#edit_stock').val(btn.data('stock'));
                $('#edit_stock_alert').val(btn.data('stock_alert'));
                $('#edit_description').val(btn.data('description'));
                $('#edit_etat').val(btn.data('etat'));

                $('#editProductModal').modal('show');
            });

            // --- Modal Suppression ---
            $('.delete-product').on('click', function (e) {
                e.preventDefault();
                const btn = $(this);

                $('#delete_reference').val(btn.data('reference'));
                $('#delete_designation').text(btn.data('designation'));

                $('#deleteProductModal').modal('show');
            });

            $('.approvisionnement-product').on('click', function (e) {
        e.preventDefault();
        
        const ref = $(this).data('reference');
        const designation = $(this).data('designation');
        const stock = $(this).data('stock');

        $('#modal-reference').val(ref);
        $('#modal-designation').text(designation);
        $('#modal-stock-actuel').text(stock);

        $('#approvisionnementModal').modal('show');
    });
        });
    </script>
</body>
</html>