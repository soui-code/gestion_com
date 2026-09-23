<?php
require_once 'models/produit.php';

class ProduitController extends BaseController{

    function liste(){
        require_once './config/paginator.php';

        $produit = new ProduitModel();
        $produit->id_societe = getSocieteActive();
        $produit->id_boutique = getBoutiqueActive();

        try {
            $paginator = new Paginator('produits p');
            $paginator->select('p.*, c.nom_categorie, f.nom_fournisseur')
                ->join('categories c', 'p.id_categorie = c.id_categorie', 'LEFT')
                ->join('fournisseurs f', 'p.id_fournisseur = f.id_fournisseur', 'LEFT')
                ->where('1=1');

            if (!empty($produit->id_societe)) {
                $paginator->where('p.id_societe = ?', $produit->id_societe);
            }
            if (!empty($produit->id_boutique)) {
                $paginator->where('p.id_boutique = ?', $produit->id_boutique);
            }

            $paginator->orderBy('p.reference DESC')->perPage(20);
            $paginatedResult = $paginator->paginate();

            $result = $paginatedResult['data'];
            $pagination = $paginatedResult;

        } catch (Exception $e) {
            Logger::error('Erreur lors du chargement des produits', ['error' => $e->getMessage()]);
            setFlash('danger', 'Erreur lors du chargement des produits');
            $result = [];
            $pagination = null;
        }

        require_once 'models/categorie.php';
        require_once 'models/fournisseur.php';
        $catModel = new CategorieModel();
        $catModel->id_societe = getSocieteActive();
        $catModel->id_boutique = getBoutiqueActive();
        $fournModel = new FournisseurModel();
        $fournModel->id_societe = getSocieteActive();
        $fournModel->id_boutique = getBoutiqueActive();
        
        try {
            $categories = $catModel->getActiveCategories();
            $fournisseurs = $fournModel->getActiveFournisseurs();
        } catch (Exception $e) {
            $categories = [];
            $fournisseurs = [];
        }

        if (isset($_POST['btn-ajouter'])) {
            try {
                $validator = $this->validate($_POST, [
                    'sai_designation' => 'required|min:3|max:150',
                    'sai_prix_achat' => 'required|numeric|minValue:0',
                    'sai_prix_vente' => 'required|numeric|minValue:0',
                    'sai_stock' => 'required|integer|minValue:0',
                    'sai_stock_alert' => 'required|integer|minValue:0',
                    'sai_etat' => 'required|in:actif,inactif'
                ]);

                if ($validator) {
                    saveOldInput();
                    setFlash('danger', $validator->firstError());
                    redirect('/produit/liste');
                }

                $prix_achat = (float)$_POST['sai_prix_achat'];
                $prix_vente = (float)$_POST['sai_prix_vente'];

                if ($prix_vente < $prix_achat) {
                    Logger::warning('Prix de vente inférieur au prix d\'achat', [
                        'produit' => $_POST['sai_designation'],
                        'prix_achat' => $prix_achat,
                        'prix_vente' => $prix_vente,
                        'utilisateur_id' => $_SESSION['utilisateur_id']
                    ]);
                    setFlash('warning', 'Attention: Le prix de vente est inférieur au prix d\'achat');
                }

                $produit->reference = $_POST['sai_reference'] ?? '';
                $produit->designation = trim($_POST['sai_designation']);
                $produit->id_categorie = !empty($_POST['sai_categorie']) ? (int)$_POST['sai_categorie'] : null;
                $produit->id_fournisseur = !empty($_POST['sai_fournisseur']) ? (int)$_POST['sai_fournisseur'] : null;
                $produit->prix_achat = $prix_achat;
                $produit->prix_vente = $prix_vente;
                $produit->stock = max(0, (int)$_POST['sai_stock']);
                $produit->stock_alert = max(0, (int)$_POST['sai_stock_alert']);
                $produit->description = trim($_POST['sai_description']);
                $produit->etat = $_POST['sai_etat'];
                $produit->utilisateur_id = $_SESSION['utilisateur_id'] ?? null;

                // IMPORTANT : on stocke le contenu BINAIRE de l'image (comme en modification),
                // pas un chemin de fichier, car les vues (POS, liste produits, etc.)
                // font systématiquement base64_encode($p['photo']) pour l'affichage.
                if (!empty($_FILES['sai_photo']['name']) && $_FILES['sai_photo']['error'] === UPLOAD_ERR_OK) {
                    try {
                        $produit->photo = file_get_contents($_FILES['sai_photo']['tmp_name']);
                        $produit->photo_type = $_FILES['sai_photo']['type'];
                    } catch (Exception $e) {
                        Logger::error('Erreur upload photo produit', ['error' => $e->getMessage()]);
                        setFlash('warning', 'Produit ajouté mais erreur lors de l\'upload de la photo');
                        $produit->photo_type = "";
                        $produit->photo = "";
                    }
                } else {
                    $produit->photo_type = "";
                    $produit->photo = "";
                }

                $produit->AjouterProduit();
                clearOldInput();
                Logger::info('Produit ajouté', [
                    'reference' => $produit->reference,
                    'designation' => $produit->designation,
                    'utilisateur_id' => $_SESSION['utilisateur_id']
                ]);
                setFlash('success', 'Produit ajouté avec succès !');
                redirect('/produit/liste');
            } catch (Exception $e) {
                Logger::logException($e);
                setFlash('danger', isDebug() ? $e->getMessage() : 'Une erreur est survenue lors de l\'ajout du produit');
            }
        }

        if (isset($_POST['btn-modifier'])) {
            try {
                $prix_achat = (float)$_POST['sai_prix_achat'];
                $prix_vente = (float)$_POST['sai_prix_vente'];

                if ($prix_achat < 0 || $prix_vente < 0) {
                    setFlash('danger', 'Les prix ne peuvent pas être négatifs');
                } elseif ($prix_vente < $prix_achat) {
                    setFlash('warning', 'Attention: Le prix de vente est inférieur au prix d\'achat');
                }

                $produit->reference = $_POST['sai_reference'] ?? '';
                $produit->designation = trim($_POST['sai_designation']);
                $produit->id_categorie = !empty($_POST['sai_categorie']) ? (int)$_POST['sai_categorie'] : null;
                $produit->id_fournisseur = !empty($_POST['sai_fournisseur']) ? (int)$_POST['sai_fournisseur'] : null;
                $produit->prix_achat = $prix_achat;
                $produit->prix_vente = $prix_vente;
                $produit->stock_alert = max(0, (int)$_POST['sai_stock_alert']);
                $produit->description = trim($_POST['sai_description']);
                $produit->etat = $_POST['sai_etat'];
                
                if (!empty($_FILES['sai_photo']['name'])) {
                    $produit->photo_type=$_FILES['sai_photo']['type'];
                    $produit->photo=file_get_contents($_FILES['sai_photo']['tmp_name']);   
                } else {
                    $produit->photo_type="";
                    $produit->photo="";
                }
                $produit->ModifierProduit();
                setFlash('success', 'Produit modifié avec succès !');
                redirect('/produit/liste');
            } catch (Exception $e) {
                setFlash('danger', $e->getMessage());
            }
        }

        if (isset($_POST['btn-supprimer'])) {
            try {
                $produit->reference=$_POST['sai_reference'];
                $produit->SupprimerProduit();
                setFlash('success', 'Produit supprimé avec succès !');
            } catch (Exception $e) {
                setFlash('danger', $e->getMessage());
            }
            redirect('/produit/liste');
        }

        if (isset($_POST['btn-approvisionner'])) {
            try {
                $qte = (int)$_POST['sai_quantite'];
                if ($qte <= 0) {
                    setFlash('danger', 'Quantité invalide');
                } else {
                    $produit->stock = $qte;
                    $produit->reference = $_POST['sai_reference'];
                    $produit->utilisateur_id = $_SESSION['utilisateur_id'] ?? null;
                    $produit->ApprovisionnerProduit();
                    setFlash('success', 'Stock approvisionné avec succès !');
                }
            } catch (Exception $e) {
                setFlash('danger', $e->getMessage());
            }
            redirect('/produit/liste');
        }

        $this->render('produit/enregistrement-produit',[
            'result' => $result,
            'categories' => $categories,
            'fournisseurs' => $fournisseurs,
            'pagination' => $pagination ?? null
        ]);
    }
}

// Alias pour compatibilité avec le router
class_alias('ProduitController', 'produit');