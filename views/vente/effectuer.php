<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Point de Vente - Gestion Commerciale</title>
    <link rel="shortcut icon" href="../assets/img/favicon.png">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/line-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .pos-container { height: calc(100vh - 120px); overflow: hidden; margin-top:5px;}
        .products-section { height: 100%; overflow-y: auto; padding-right: 15px; padding-bottom: 50px; }
        .cart-section { height: 100%; display: flex; flex-direction: column; background: #fff; border-left: 1px solid #ddd; overflow: hidden; }
        .cart-items-wrapper { flex: 0; overflow-y: auto; overflow-x: hidden; min-height: 135px; border-bottom: 2px solid #f8f9fa; }
        
        .product-card {
            border: 1px solid #e3e3e3;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.2s;
            height: 100%;
            background: #fff;
            position: relative;
        }
        .product-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-color: #ff9b44;
        }
        .product-img-wrap {
            height: 100px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid #eee;
        }
        .product-img-wrap i { font-size: 30px; color: #ccc; }
        .product-img-wrap img { width: 100%; height: 100%; object-fit: cover; }
        .product-info { padding: 8px; }
        .product-title { font-size: 13px; font-weight: 600; margin-bottom: 3px; color: #333; line-height: 1.2; height: 32px; overflow: hidden; }
        .product-price { font-size: 14px; color: #ff9b44; font-weight: 700; }
        .product-stock { position: absolute; top: 5px; right: 5px; background: rgba(0,0,0,0.6); color: #fff; padding: 1px 5px; border-radius: 4px; font-size: 10px; z-index: 2;}
        
        .cart-item { border-bottom: 1px solid #f1f1f1; padding: 8px 10px; }
        .cart-item:hover { background: #fafafa; }
        .cart-item-title { font-weight: 600; font-size: 13px; margin-bottom: 3px; }
        .qty-controls { display: flex; align-items: center; }
        .qty-btn { width: 24px; height: 24px; border-radius: 4px; border: 1px solid #ddd; background: #fff; color: #333; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 10px; }
        .qty-btn:hover { background: #ff9b44; color: #fff; border-color: #ff9b44; }
        .qty-input { width: 35px; height: 24px; text-align: center; border: 1px solid #ddd; margin: 0 3px; border-radius: 4px; font-size: 12px; }
        .remove-icon { color: #ccc; cursor: pointer; font-size: 16px; transition: color 0.2s; }
        .remove-icon:hover { color: #dc3545; }

        .search-bar { position: sticky; top: 0; z-index: 100; background: #f8f9fa; padding-bottom: 10px; }
        .search-input { border-radius: 50px; padding-left: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border: 1px solid #ddd; }
        .search-input:focus { border-color: #ff9b44; box-shadow: 0 0 0 0.2rem rgba(255, 155, 68, 0.25); }

        .totals-box { background: #fcfcfc; padding: 10px 15px; border-radius: 0; border-top: 1px solid #eee; }
        .totals-row { display: flex; justify-content: space-between; margin-bottom: 4px; font-size: 13px; color: #666; }
        .grand-total { font-size: 18px; font-weight: 800; color: #333; border-top: 1px dotted #ccc; padding-top: 8px; margin-top: 8px; }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #aaa; }
    </style>
</head>
<body>
<div class="main-wrapper">
    <?php include './config/dashboard.php'; ?>
    
    <div class="page-wrapper" style="padding: 25px 20px; background: #f8f9fa; height: 100vh; overflow: hidden;"><br>
            <br> 
        <div class="row pos-container">
            <!-- SECTION PRODUITS (GAUCHE) -->
            <div class="col-lg-7 col-md-12 products-section">
                <!-- Header / Search -->
                <div class="search-bar">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h4 class="mb-0" style="font-weight: 700; color: #333;">Point de Vente</h4>
                        <span class="badge badge-outline-primary" style="color: #ff9b44; border: 1px solid #ff9b44; padding: 5px 10px; border-radius: 50px;">
                            <i class="la la-user"></i> <?= $_SESSION['nom'] ?? 'Vendeur' ?>
                        </span>
                    </div>
                    <div class="position-relative">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-right-0" style="border-radius: 50px 0 0 50px;"><i class="la la-search"></i></span>
                            </div>
                            <input type="text" id="searchInput" class="form-control form-control-lg search-input border-left-0" style="border-radius: 0 50px 50px 0;" placeholder="Rechercher un produit (nom, référence, code-barres)..." autocomplete="off">
                        </div>
                        <div id="searchResults" class="list-group position-absolute w-100 shadow-lg" style="z-index: 1050; display: none; max-height: 400px; overflow-y: auto; top: 100%; margin-top: 5px;"></div>
                    </div>
                </div>

                <!-- Grille de Produits -->
                <div class="row pt-2" id="productsGrid"> 
                    <?php if(!empty($data['produits'])): ?>
                        <?php foreach($data['produits'] as $p): ?>
                        <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-6 mb-3 product-grid-item" data-name="<?= strtolower(htmlspecialchars($p['designation'])) ?>" data-ref="<?= strtolower(htmlspecialchars($p['reference'])) ?>">
                            <div class="product-card" onclick="addToCart('<?= htmlspecialchars($p['reference']) ?>', '<?= addslashes(htmlspecialchars($p['designation'])) ?>', <?= $p['prix_vente'] ?>, <?= $p['stock'] ?>)">
                                <div class="product-stock"><?= $p['stock'] ?> en stock</div>
                                <div class="product-img-wrap">
                                    <?php if(!empty($p['photo'])): ?>
                                        <img src="data:<?= $p['photo_type'] ?>;base64,<?= base64_encode($p['photo']) ?>" alt="Produit">
                                    <?php else: ?>
                                        <i class="la la-image"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <div class="product-title"><?= htmlspecialchars($p['designation']) ?></div>
                                    <div class="product-price"><?= number_format($p['prix_vente'], 0, ',', ' ') ?> F</div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center mt-5 text-muted">
                            <i class="la la-box la-4x mb-3"></i>
                            <h5>Aucun produit disponible</h5>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- SECTION PANIER (DROITE) -->
            <div class="col-lg-5 col-md-12">
                <div class="card cart-section shadow-sm mb-0">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2 px-3">
                        <h5 class="card-title mb-0" style="font-weight:700;"><i class="la la-shopping-cart text-warning"></i> MON PANIER</h5>
                        <button class="btn btn-xs btn-link text-danger p-0" onclick="clearCart()"><i class="la la-trash"></i> Vider</button>
                    </div>
                    
                    <!-- Liste des articles (Scrollable area) -->
                    <div class="cart-items-wrapper" id="cartItems">
                        <div class="text-center text-muted my-5" id="emptyCartMsg">
                            <i class="la la-shopping-basket la-3x mb-2" style="opacity: 0.3;"></i>
                            <p>Votre panier est vide</p>
                        </div>
                    </div>

                    <!-- Zone fixe en bas (Paiement) -->
                    <div class="payment-section bg-light border-top" style="padding-bottom: 1px;">
                        <div class="p-3">
                            <div class="row no-gutters mb-2">
                                <div class="col-6 pr-1">
                                    <label class="small text-muted mb-0">Client</label>
                                    <select id="client-select" class="form-control form-control-sm">
                                        <option value="3" selected>Client de passage</option>
                                        <?php foreach ($data['clients'] as $c): ?>
                                            <option value="<?= $c['code_client'] ?>"><?= htmlspecialchars($c['nom_prenom_client']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-6 pl-1">
                                    <label class="small text-muted mb-0">Application Taxe</label>
                                    <select id="taxe-select" class="form-control form-control-sm">
                                        <option value="">Aucune taxe</option>
                                        <?php foreach ($data['taxes'] as $t): ?>
                                            <option value="<?= $t['code_taxe'] ?>" data-action="<?= $t['action_taxe'] ?>" data-taux="<?= $t['taux_taxe'] ?>">
                                                <?= htmlspecialchars($t['titre_taxe']) ?> (<?= floatval($t['taux_taxe']) ?>%)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="totals-box">
                                <div class="totals-row">
                                    <span>Total HT</span>
                                    <span id="total-ht">0 F</span>
                                </div>
                                <div class="totals-row">
                                    <span>Taxes</span>
                                    <span id="montant-taxe">0 F</span>
                                </div>
                                <div class="totals-row">
                                    <span>Remise</span>
                                    <input type="number" id="remise" class="text-right border-0 bg-transparent p-0" style="width: 80px; font-weight: 600;" value="0" min="0">
                                </div>
                                <div class="totals-row grand-total">
                                    <span>NET À PAYER</span>
                                    <span id="total-ttc" class="text-dark">0 F</span>
                                </div>
                            </div>

                            <div class="row no-gutters mt-2">
                                <div class="col-5 pr-1">
                                    <select id="mode-paiement" class="form-control form-control-sm h-100">
                                        <option value="Espèces">Espèces</option>
                                        <option value="Mobile Money">MOMO</option>
                                        <option value="Carte bancaire">CARTE</option>
                                    </select>
                                </div>
                                <div class="col-7 pl-1">
                                    <input type="number" id="montant-paye" class="form-control form-control-sm text-center font-weight-bold h-100" style="font-size: 16px;" placeholder="Somme reçue">
                                </div>
                            </div>
                            
                            <div class="text-center mt-2" id="reste-apayer-container" style="display:none;">
                                <div id="reste-apayer" class="badge p-2 w-100" style="font-size: 13px;"></div>
                            </div>

                            <button id="btn-valider" class="btn btn-warning btn-block font-weight-bold mt-2 py-2 shadow-sm" style="background: #ff9b44; border:none; color:#fff; font-size: 16px;">
                                <i class="la la-check-circle"></i> ENCAISSER MAINTENANT
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="../assets/js/jquery-3.5.1.min.js"></script>
<script src="../assets/js/popper.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/jquery.slimscroll.min.js"></script>
<script src="../assets/js/app.js"></script>

<script>
    const panier = [];
    let totalHT = 0;

    // Recherche AJAX des produits
    let searchTimeout;
    $('#searchInput').on('input', function() {
        clearTimeout(searchTimeout);
        const val = $(this).val().trim();
        const resultsContainer = $('#searchResults');
        
        if (val.length < 2) {
            resultsContainer.hide().empty();
            // Fallback: filtrer la grille locale si le terme est court
            const lowerVal = val.toLowerCase();
            $('.product-grid-item').each(function() {
                const name = $(this).data('name');
                const ref = $(this).data('ref');
                if (name.includes(lowerVal) || ref.includes(lowerVal)) $(this).show(); else $(this).hide();
            });
            return;
        }

        searchTimeout = setTimeout(() => {
            const basePath = window.location.pathname.includes('gestion_commerciale') ? '/gestion_commerciale' : '';
            $.get(basePath + '/vente/recherche', { term: val }, function(data) {
                resultsContainer.empty();
                if (data.length > 0) {
                    data.forEach(p => {
                        const item = $(`
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center py-2">
                                <div class="mr-3" style="width: 40px; height: 40px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                                    ${p.photo ? `<img src="data:${p.photo_type};base64,${p.photo_base64 || ''}" style="width: 100%; height: 100%; object-fit: cover;">` : '<i class="la la-image text-muted"></i>'}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="font-weight-bold mb-0" style="font-size: 14px;">${p.designation}</div>
                                    <small class="text-muted">${p.reference} | <span class="text-warning font-weight-bold">${Math.round(p.prix_vente).toLocaleString()} F</span></small>
                                </div>
                                <div class="badge badge-info">${p.stock} en stock</div>
                             </a>
                        `);
                        item.click(function(e) {
                            e.preventDefault();
                            addToCart(p.reference, p.designation, p.prix_vente, p.stock);
                            resultsContainer.hide().empty();
                            $('#searchInput').val('');
                        });
                        resultsContainer.append(item);
                    });
                    resultsContainer.show();
                } else {
                    resultsContainer.hide();
                }
            }, 'json');
        }, 300);
    });

    // Fermer les résultats si on clique ailleurs
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.position-relative').length) {
            $('#searchResults').hide();
        }
    });

    // Ajouter au panier
    function addToCart(ref, nom, prix, maxStock) {
        const exist = panier.find(p => p.ref == ref);
        if (exist) {
            if (exist.qte < maxStock) {
                exist.qte++;
            } else {
                alert("Stock maximum atteint pour ce produit !");
            }
        } else {
            if (maxStock > 0) {
                panier.push({ ref, nom, prix, qte: 1, maxStock });
            } else {
                alert("Rupture de stock !");
            }
        }
        renderCart();
    }

    function removeCartItem(index) {
        panier.splice(index, 1);
        renderCart();
    }

    function clearCart() {
        if (panier.length === 0) return;
        if (confirm("Voulez-vous vraiment vider le panier ?")) {
            panier.length = 0;
            renderCart();
        }
    }

    function changeQty(index, delta) {
        const item = panier[index];
        const newQte = item.qte + delta;
        
        if (newQte > 0 && newQte <= item.maxStock) {
            item.qte = newQte;
        } else if (newQte > item.maxStock) {
            alert("Stock maximum atteint !");
        } else if (newQte <= 0) {
            removeCartItem(index);
            return;
        }
        renderCart();
    }

    function renderCart() {
        const container = $('#cartItems');
        if (panier.length === 0) {
            container.html(`
                <div class="text-center text-muted my-5" id="emptyCartMsg">
                    <i class="la la-shopping-basket la-3x mb-2"></i>
                    <p>Le panier est vide</p>
                </div>
            `);
            calculateTotals();
            return;
        }

        let html = '';
        panier.forEach((item, index) => {
            const totalItem = item.prix * item.qte;
            html += `
            <div class="cart-item">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="cart-item-title text-truncate" style="max-width: 200px; font-size:13px;">${item.nom}</div>
                    <i class="la la-trash remove-icon" onclick="removeCartItem(${index})"></i>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted" style="font-size:12px;">${item.prix.toLocaleString()} F x ${item.qte}</div>
                    <div class="qty-controls">
                        <button class="qty-btn" onclick="changeQty(${index}, -1)"><i class="la la-minus"></i></button>
                        <input type="text" class="qty-input" value="${item.qte}" readonly>
                        <button class="qty-btn" onclick="changeQty(${index}, 1)"><i class="la la-plus"></i></button>
                    </div>
                </div>
                <div class="text-right mt-1 font-weight-bold" style="font-size:14px;">${totalItem.toLocaleString()} F</div>
            </div>`;
        });
        
        container.html(html);
        
        // Scroll to bottom of cart
        container.scrollTop(container[0].scrollHeight);
        
        calculateTotals();
    }

    function calculateTotals() {
        totalHT = panier.reduce((sum, item) => sum + (item.prix * item.qte), 0);
        
        const taxeSelect = $('#taxe-select option:selected');
        const taux = parseFloat(taxeSelect.data('taux')) || 0;
        const action = taxeSelect.data('action') || 'ajouter';
        let remise = parseFloat($('#remise').val()) || 0;

        let taxe = totalHT * (taux / 100);
        if (action === 'soustraire') taxe = -taxe;

        const ttc = totalHT + taxe - remise;
           const payeInput = $('#montant-paye').val();
        let paye = parseFloat(payeInput) || 0;
        let reste = ttc - paye;

        $('#total-ht').text(totalHT.toLocaleString() + ' F');
        $('#montant-taxe').text((taxe > 0 ? '+ ' : '') + taxe.toLocaleString() + ' F');
        $('#total-ttc').text(ttc.toLocaleString() + ' F');
        
        const changeContainer = $('#reste-apayer-container');
        const changeLabel = $('#reste-apayer');

        if (panier.length > 0) {
            changeContainer.show();
            if (reste > 0) {
                changeLabel.removeClass('badge-success').addClass('badge-danger');
                changeLabel.html('<i class="la la-exclamation-circle"></i> Reste à payer: ' + reste.toLocaleString() + ' F');
            } else if (reste < 0) {
                changeLabel.removeClass('badge-danger').addClass('badge-success');
                changeLabel.html('<i class="la la-check-circle"></i> Monnaie à rendre: ' + Math.abs(reste).toLocaleString() + ' F');
            } else {
                changeLabel.removeClass('badge-danger').addClass('badge-success');
                changeLabel.html('<i class="la la-check-circle"></i> Payé en totalité');
            }
        } else {
            changeContainer.hide();
        }
    }

    $('#taxe-select, #remise, #montant-paye').on('input change', calculateTotals);

    $('#btn-valider').click(function() {
        if (panier.length === 0) return alert('Le panier est vide !');

        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Traitement...');

        const data = {
            action: 'valider_vente',
            panier: JSON.stringify(panier),
            client_id: $('#client-select').val(),
            taxe_code: $('#taxe-select').val(),
            remise: $('#remise').val(),
            montant_paye: $('#montant-paye').val() || 0,
            mode_paiement: $('#mode-paiement').val()
        };

        const basePath = window.location.pathname.includes('gestion_commerciale') ? '/gestion_commerciale' : '';
        $.post(basePath + '/vente/valider', data, function(res) {
            if (res.success) {
                // Rediriger vers l'impression de la facture nouvellement ajoutée
                window.open(basePath + '/facture/imprimer?id=' + res.facture, '_blank');
                // Recharger la page (vider le panier)
                location.reload();
            } else {
                alert('Erreur: ' + res.error);
                btn.prop('disabled', false).html('<i class="la la-check-circle"></i> ENCAISSER');
            }
        }, 'json').fail(function() {
            alert("Erreur de communication avec le serveur.");
            btn.prop('disabled', false).html('<i class="la la-check-circle"></i> ENCAISSER');
        });
    });

</script>
</body>
</html>