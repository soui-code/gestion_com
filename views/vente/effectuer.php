<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Point de Vente</title>
    <!-- Icon pwa -->
     <link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#1e293b">
<link rel="apple-touch-icon" href="/assets/icons/icon-192.png">

    <link rel="shortcut icon" href="../assets/img/favicon.png">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/line-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .page-wrapper { min-height: 100vh; }
        .pos-container { min-height: calc(100vh - 140px); display: flex; }

        /* Section produits */
        .products-section { flex: 1; padding: 15px; max-height: calc(100vh - 140px); overflow-y: auto; }

        /* Section panier - Plus large */
        .cart-section {
            width: 450px;
            background: #fff;
            border-left: 2px solid #ddd;
            display: flex;
            flex-direction: column;
            max-height: calc(100vh - 140px);
            position: sticky;
            top: 70px;
        }
        .cart-header {
            padding: 18px 20px;
            background: linear-gradient(135deg, #ff9b44 0%, #fc7c0e 100%);
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .cart-items {
            flex: 1;
            overflow-y: auto;
            padding: 12px;
            min-height: 250px;
            max-height: 400px;
        }
        .cart-footer {
            border-top: 2px solid #eee;
            padding: 18px;
            background: #f8f9fa;
        }

        /* ===== Cards produits (rectangles larges, peu hautes) ===== */
        .product-card {
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
            background: #fff;
            position: relative;
            display: flex;
            flex-direction: row;
            align-items: stretch;
            height: 112px;
            margin-bottom: 15px;
        }
        .product-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            border-color: #ff9b44;
        }
        .product-card:active { transform: translateY(-1px); }

        /* Carte en rupture : désactivée visuellement */
        .product-card.out-of-stock {
            cursor: not-allowed;
            opacity: 0.55;
            filter: grayscale(40%);
        }
        .product-card.out-of-stock:hover {
            transform: none;
            box-shadow: none;
            border-color: #e0e0e0;
        }

        /* Petit "pulse" quand un produit est ajouté au panier */
        @keyframes addPulse {
            0%   { transform: scale(1); }
            45%  { transform: scale(0.97); }
            100% { transform: scale(1); }
        }
        .product-card.just-added {
            animation: addPulse 0.35s ease;
            border-color: #28a745 !important;
            box-shadow: 0 0 0 3px rgba(40,167,69,0.25);
        }

        .product-img {
            position: relative;
            width: 112px;
            min-width: 112px;
            background: linear-gradient(180deg, #f8f9fa 0%, #f1f2f4 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            border-right: 1px solid #eee;
            overflow: hidden;
        }
        .product-img i { font-size: 40px; color: #ccc; }
        .product-img img { width: 100%; height: 100%; object-fit: cover; }

        /* Overlay "+" au survol pour signifier l'ajout au panier */
        .product-add-overlay {
            position: absolute;
            inset: 0;
            background: rgba(255,155,68,0.88);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.2s ease;
            pointer-events: none;
        }
        .product-card:not(.out-of-stock):hover .product-add-overlay { opacity: 1; }
        .product-add-overlay i { font-size: 22px; color: #fff; }

        .product-body {
            flex: 1;
            min-width: 0;
            padding: 8px 14px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .product-title {
            font-size: 13.5px;
            font-weight: 600;
            margin-bottom: 6px;
            color: #333;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            line-height: 1.35;
        }
        .product-price { font-size: 16px; color: #ff9b44; font-weight: 700; }

        /* Badge stock coloré selon le niveau (positionné sur la vignette image) */
        .product-stock {
            position: absolute;
            top: 6px;
            right: 6px;
            color: #fff;
            padding: 3px 7px;
            border-radius: 5px;
            font-size: 10px;
            font-weight: 600;
            z-index: 2;
            box-shadow: 0 2px 5px rgba(0,0,0,0.15);
        }
        .product-stock.stock-ok  { background: rgba(0,0,0,0.75); }
        .product-stock.stock-low { background: #fc7c0e; }
        .product-stock.stock-out { background: #dc3545; }

        /* Badge quantité déjà présente dans le panier (sur la vignette image) */
        .cart-qty-badge {
            position: absolute;
            top: 6px;
            left: 6px;
            background: #28a745;
            color: #fff;
            min-width: 20px;
            height: 20px;
            padding: 0 5px;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            box-shadow: 0 2px 6px rgba(0,0,0,0.25);
            z-index: 3;
        }

        .cart-item {
            border-bottom: 1px solid #f0f0f0;
            padding: 12px 0;
        }
        .cart-item:last-child { border-bottom: none; }
        .qty-controls { display: flex; align-items: center; gap: 8px; }
        .qty-btn {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: 1px solid #ddd;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
            transition: all 0.2s;
        }
        .qty-btn:hover { background: #ff9b44; color: #fff; border-color: #ff9b44; }
        .qty-input {
            width: 55px;
            height: 32px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
        }

        .tax-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #e3f2fd;
            color: #1976d2;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            margin: 3px;
            font-weight: 500;
        }
        .tax-badge .remove-tax {
            cursor: pointer;
            font-weight: bold;
            color: #d32f2f;
            font-size: 16px;
            line-height: 1;
        }
        .tax-badge .remove-tax:hover { color: #b71c1c; }

        .search-box {
            position: sticky;
            top: 0;
            background: #f8f9fa;
            padding: 12px 0;
            margin-bottom: 15px;
            z-index: 10;
        }
        .search-input {
            border-radius: 25px;
            padding: 12px 20px;
            border: 2px solid #e0e0e0;
            width: 100%;
            font-size: 15px;
        }
        .search-input:focus {
            border-color: #ff9b44;
            outline: none;
            box-shadow: 0 0 0 3px rgba(255,155,68,0.1);
        }

        .totals-box {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            margin-bottom: 12px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
            color: #666;
        }
        .grand-total {
            font-size: 20px;
            font-weight: 800;
            color: #333;
            border-top: 2px solid #ddd;
            padding-top: 12px;
            margin-top: 12px;
        }

        /* Responsive */
        @media (max-width: 1199px) {
            .cart-section { width: 400px; }
        }

        @media (max-width: 991px) {
            .pos-container { flex-direction: column; }
            .cart-section {
                width: 100%;
                max-height: none;
                position: relative;
                top: 0;
                margin-top: 20px;
                border-left: none;
                border-top: 2px solid #ddd;
            }
            .products-section { max-height: none; }
        }

        @media (max-width: 768px) {
            .product-card { height: 96px; }
            .product-img { width: 90px; min-width: 90px; }
            .product-img i { font-size: 32px; }
            .product-title { font-size: 12.5px; }
            .product-price { font-size: 14px; }
        }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #aaa; }
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
                        <h3 class="page-title"><i class="la la-shopping-cart text-warning"></i> Point de Vente</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= url('/utilisateur/dashboard') ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active">POS</li>
                        </ul>
                    </div>
                    <div class="col-auto">
                        <span class="badge badge-primary px-3 py-2" style="font-size: 13px;">
                            <i class="la la-user"></i> <?= htmlspecialchars($_SESSION['nom'] ?? 'Vendeur') ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Corps POS -->
            <div class="pos-container">
                <!-- PRODUITS -->
                <div class="products-section">
                    <div class="search-box">
                        <input type="text" id="searchInput" class="search-input" placeholder="🔍 Rechercher un produit par nom ou référence..." autocomplete="off">
                    </div>

                    <div class="row" id="productsGrid">
                        <?php if(!empty($data['produits'])): ?>
                            <?php foreach($data['produits'] as $p): ?>
                            <?php
                                $stock = (int)$p['stock'];
                                if ($stock <= 0) {
                                    $stockClass = 'stock-out';
                                    $stockLabel = 'Rupture';
                                    $cardClass  = 'out-of-stock';
                                } elseif ($stock <= 5) {
                                    $stockClass = 'stock-low';
                                    $stockLabel = $stock . ' restant' . ($stock > 1 ? 's' : '');
                                    $cardClass  = '';
                                } else {
                                    $stockClass = 'stock-ok';
                                    $stockLabel = $stock;
                                    $cardClass  = '';
                                }
                                $clickAction = $stock > 0
                                    ? "addToCart(" . json_encode($p["reference"]) . ", " . json_encode($p["designation"]) . ", " . $p["prix_vente"] . ", " . $p["stock"] . ")"
                                    : "alert('⚠️ Produit en rupture de stock')";
                            ?>
                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12 product-item"
                                 data-name="<?= strtolower($p['designation']) ?>"
                                 data-ref="<?= strtolower($p['reference']) ?>">
                                <div class="product-card <?= $cardClass ?>"
                                     data-refval="<?= htmlspecialchars($p['reference'], ENT_QUOTES) ?>"
                                     onclick="<?= htmlspecialchars($clickAction, ENT_QUOTES) ?>">
                                    <div class="product-img">
                                        <span class="cart-qty-badge">0</span>
                                        <span class="product-stock <?= $stockClass ?>"><?= $stockLabel ?></span>
                                        <?php if(!empty($p['photo'])): ?>
                                            <img src="data:<?= $p['photo_type'] ?>;base64,<?= base64_encode($p['photo']) ?>" alt="">
                                        <?php else: ?>
                                            <i class="la la-cube"></i>
                                        <?php endif; ?>
                                        <?php if ($stock > 0): ?>
                                        <div class="product-add-overlay"><i class="la la-plus-circle"></i></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="product-body">
                                        <div class="product-title"><?= htmlspecialchars($p['designation']) ?></div>
                                        <div class="product-price"><?= number_format($p['prix_vente'], 0, ',', ' ') ?> F</div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12 text-center mt-5">
                                <i class="la la-box la-4x text-muted mb-3"></i>
                                <p class="text-muted">Aucun produit disponible</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- PANIER -->
                <div class="cart-section">
                    <div class="cart-header">
                        <h5 class="mb-0 font-weight-bold"><i class="la la-shopping-basket"></i> MON PANIER</h5>
                        <button class="btn btn-sm btn-light px-3" onclick="clearCart()" style="border-radius: 20px;">
                            <i class="la la-trash"></i> Vider
                        </button>
                    </div>

                    <div class="cart-items" id="cartItems">
                        <div class="text-center text-muted" id="emptyMsg" style="padding: 80px 20px;">
                            <i class="la la-shopping-basket" style="font-size: 70px; opacity:0.2; display:block; margin-bottom:20px;"></i>
                            <p style="font-size: 16px; margin-bottom: 10px;">Panier vide</p>
                            <small style="color: #999;">Cliquez sur un produit pour l'ajouter</small>
                        </div>
                    </div>

                    <div class="cart-footer">
                        <div class="mb-3">
                            <label class="small text-muted mb-1 font-weight-600">Client</label>
                            <select id="client-select" class="form-control">
                                <option value="">Client de passage</option>
                                <?php foreach ($data['clients'] as $c): ?>
                                    <option value="<?= $c['code_client'] ?>"><?= htmlspecialchars($c['nom_prenom_client']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted mb-1 font-weight-600">Ajouter une taxe</label>
                            <select id="taxe-select" class="form-control">
                                <option value="">-- Sélectionner --</option>
                                <?php foreach ($data['taxes'] as $t): ?>
                                    <option value="<?= $t['code_taxe'] ?>"
                                            data-titre="<?= htmlspecialchars($t['titre_taxe']) ?>"
                                            data-taux="<?= $t['taux_taxe'] ?>"
                                            data-action="<?= $t['action_taxe'] ?>">
                                        <?= htmlspecialchars($t['titre_taxe']) ?> (<?= $t['taux_taxe'] ?>%)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div id="taxesBadges" class="mt-2"></div>
                        </div>

                        <div class="totals-box">
                            <div class="totals-row">
                                <span>Total HT</span>
                                <strong id="totalHT">0 F</strong>
                            </div>
                            <div class="totals-row">
                                <span>Taxes</span>
                                <strong id="totalTaxes" class="text-info">0 F</strong>
                            </div>
                            <div class="totals-row">
                                <span>Remise</span>
                                <input type="number" id="remise" class="text-right border-0 bg-transparent p-0" style="width:90px; font-weight:600;" value="0" min="0">
                            </div>
                            <div class="totals-row grand-total">
                                <span>NET À PAYER</span>
                                <span id="totalTTC" class="text-success">0 F</span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-5 pr-1">
                                <label class="small text-muted mb-1">Mode</label>
                                <select id="modePaiement" class="form-control">
                                    <option value="Espèces">💵 Espèces</option>
                                    <option value="Mobile Money">📱 MOMO</option>
                                    <option value="Carte bancaire">💳 Carte</option>
                                    <option value="Virement">🏦 Virement</option>
                                </select>
                            </div>
                            <div class="col-7 pl-1">
                                <label class="small text-muted mb-1">Montant reçu</label>
                                <input type="number" id="montantPaye" class="form-control text-center font-weight-bold" placeholder="0" style="font-size:15px;">
                            </div>
                        </div>

                        <div id="monnaieDiv" class="text-center mb-3" style="display:none;">
                            <div id="monnaieText" class="badge w-100 p-2" style="font-size: 13px;"></div>
                        </div>

                        <button id="btnValider" class="btn btn-block py-3 font-weight-bold shadow" style="background: linear-gradient(135deg, #ff9b44 0%, #fc7c0e 100%); border:none; color:#fff; font-size:16px; border-radius:8px;">
                            <i class="la la-check-circle"></i> ENCAISSER MAINTENANT
                        </button>
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
const taxes = [];

// Recherche en temps réel
$('#searchInput').on('input', function() {
    const val = $(this).val().toLowerCase().trim();

    $('.product-item').each(function() {
        const name = $(this).attr('data-name') || '';
        const ref = $(this).attr('data-ref') || '';

        if (val === '' || name.includes(val) || ref.includes(val)) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
});

function addToCart(ref, nom, prix, stock) {
    const exist = panier.find(p => p.ref === ref);
    if (exist) {
        if (exist.qte < stock) {
            exist.qte++;
        } else {
            alert('⚠️ Stock maximum atteint');
            return;
        }
    } else {
        panier.push({ ref, nom, prix: parseFloat(prix), qte: 1, stock: parseInt(stock) });
    }
    renderCart();
    pulseCard(ref);
}

// Petit effet visuel sur la card quand le produit est ajouté au panier
function pulseCard(ref) {
    if (typeof CSS !== 'undefined' && CSS.escape) {
        const card = $(`.product-card[data-refval="${CSS.escape(ref)}"]`);
        card.addClass('just-added');
        setTimeout(() => card.removeClass('just-added'), 350);
    }
}

// Met à jour le badge de quantité affiché sur chaque card produit
function updateProductBadges() {
    $('.product-card').each(function() {
        const ref = $(this).data('refval');
        const item = panier.find(p => String(p.ref) === String(ref));
        const badge = $(this).find('.cart-qty-badge');
        if (item) {
            badge.text(item.qte).css('display', 'flex');
        } else {
            badge.hide();
        }
    });
}

function renderCart() {
    const container = $('#cartItems');
    const emptyMsg = $('#emptyMsg');

    if (panier.length === 0) {
        emptyMsg.show();
        container.find('.cart-item').remove();
        updateTotals();
        updateProductBadges();
        return;
    }

    emptyMsg.hide();
    container.find('.cart-item').remove();

    panier.forEach((item, idx) => {
        const html = `
            <div class="cart-item">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <strong style="font-size:14px; flex:1; line-height:1.3;">${item.nom}</strong>
                    <i class="la la-times text-danger ml-2" style="cursor:pointer; font-size:20px;" onclick="removeItem(${idx})"></i>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="qty-controls">
                        <button class="qty-btn" onclick="updateQty(${idx}, -1)">−</button>
                        <input type="number" class="qty-input" value="${item.qte}" min="1" max="${item.stock}" onchange="setQty(${idx}, this.value)">
                        <button class="qty-btn" onclick="updateQty(${idx}, 1)">+</button>
                    </div>
                    <strong style="color:#ff9b44; font-size:15px;">${(item.prix * item.qte).toLocaleString()} F</strong>
                </div>
            </div>
        `;
        container.append(html);
    });

    updateTotals();
    updateProductBadges();
}

function updateQty(idx, delta) {
    const item = panier[idx];
    const newQty = item.qte + delta;
    if (newQty > 0 && newQty <= item.stock) {
        item.qte = newQty;
        renderCart();
    } else if (newQty > item.stock) {
        alert('⚠️ Stock maximum : ' + item.stock);
    }
}

function setQty(idx, val) {
    const qty = parseInt(val);
    const item = panier[idx];
    if (qty > 0 && qty <= item.stock) {
        item.qte = qty;
        renderCart();
    } else {
        alert('⚠️ Quantité invalide');
        renderCart();
    }
}

function removeItem(idx) {
    panier.splice(idx, 1);
    renderCart();
}

function clearCart() {
    if (panier.length && confirm('Vider le panier ?')) {
        panier.length = 0;
        renderCart();
    }
}

// Gestion taxes multiples
$('#taxe-select').on('change', function() {
    const code = $(this).val();
    if (!code) return;

    const opt = $(this).find('option:selected');
    const titre = opt.data('titre');
    const taux = parseFloat(opt.data('taux'));
    const action = opt.data('action');

    if (taxes.find(t => t.code === code)) {
        alert('⚠️ Taxe déjà appliquée');
        $(this).val('');
        return;
    }

    taxes.push({ code, titre, taux, action });
    $(this).val('');
    renderTaxes();
    updateTotals();
});

function renderTaxes() {
    const container = $('#taxesBadges');
    container.empty();

    taxes.forEach((tax, idx) => {
        const badge = `
            <span class="tax-badge">
                ${tax.titre} (${tax.taux}%)
                <span class="remove-tax" onclick="removeTax(${idx})" title="Retirer">✕</span>
            </span>
        `;
        container.append(badge);
    });
}

function removeTax(idx) {
    taxes.splice(idx, 1);
    renderTaxes();
    updateTotals();
}

function updateTotals() {
    const ht = panier.reduce((sum, item) => sum + (item.prix * item.qte), 0);

    let totalTaxes = 0;
    taxes.forEach(tax => {
        const calc = ht * (tax.taux / 100);
        totalTaxes += tax.action === 'soustraire' ? -calc : calc;
    });

    const remise = parseFloat($('#remise').val()) || 0;
    const ttc = ht + totalTaxes - remise;

    $('#totalHT').text(Math.round(ht).toLocaleString() + ' F');
    $('#totalTaxes').text(Math.round(totalTaxes).toLocaleString() + ' F');
    $('#totalTTC').text(Math.round(ttc).toLocaleString() + ' F');

    calculerMonnaie();
}

$('#remise, #montantPaye').on('input', updateTotals);

function calculerMonnaie() {
    const paye = parseFloat($('#montantPaye').val()) || 0;
    const ht = panier.reduce((sum, item) => sum + (item.prix * item.qte), 0);
    const totalTaxesCalc = taxes.reduce((sum, tax) => {
        const calc = ht * (tax.taux / 100);
        return sum + (tax.action === 'soustraire' ? -calc : calc);
    }, 0);
    const remise = parseFloat($('#remise').val()) || 0;
    const ttc = ht + totalTaxesCalc - remise;

    const reste = ttc - paye;
    const div = $('#monnaieDiv');
    const text = $('#monnaieText');

    if (paye > 0) {
        div.show();
        if (reste > 0) {
            text.removeClass('badge-success badge-warning').addClass('badge-danger');
            text.text(`Reste à payer : ${Math.round(reste).toLocaleString()} F`);
        } else if (reste < 0) {
            text.removeClass('badge-danger badge-warning').addClass('badge-success');
            text.text(`Monnaie à rendre : ${Math.round(Math.abs(reste)).toLocaleString()} F`);
        } else {
            text.removeClass('badge-danger badge-success').addClass('badge-warning');
            text.text('✓ Montant exact');
        }
    } else {
        div.hide();
    }
}

// Validation vente
$('#btnValider').on('click', function() {
    if (panier.length === 0) {
        alert('⚠️ Le panier est vide');
        return;
    }

    const paye = parseFloat($('#montantPaye').val()) || 0;
    if (paye <= 0) {
        alert('⚠️ Veuillez saisir le montant payé');
        $('#montantPaye').focus();
        return;
    }

    const data = {
        action: 'valider_vente',
        panier: JSON.stringify(panier),
        client_id: $('#client-select').val(),
        taxes_array: JSON.stringify(taxes),  // Envoyer toutes les taxes comme array
        remise: $('#remise').val() || 0,
        montant_paye: paye,
        mode_paiement: $('#modePaiement').val()
    };

    $(this).prop('disabled', true).html('<i class="la la-spinner la-spin"></i> Traitement...');

    const basePath = window.location.pathname.includes('gestion_commerciale') ? '/gestion_commerciale' : '';
    $.post(basePath + '/vente/valider', data, function(res) {
        if (res.success) {
            // Vider le panier
            panier.length = 0;
            taxes.length = 0;
            $('#remise').val(0);
            $('#montantPaye').val('');
            renderCart();
            renderTaxes();

            // Ouvrir la Facture Normalisée Électronique (FNE)
            const factureUrl = basePath + '/facture/imprimer_fne?id=' + encodeURIComponent(res.facture);
            const printWindow = window.open(factureUrl, '_blank', 'width=900,height=800');

            // Message de confirmation avec numéro fiscal
            setTimeout(function() {
                let message = '✅ Vente enregistrée avec succès !\n\n';
                message += 'Facture : ' + res.facture + '\n';
                if (res.numero_fiscal) {
                    message += 'N° Fiscal FNE : ' + res.numero_fiscal + '\n';
                }
                message += '\nLa Facture Normalisée Électronique s\'ouvre automatiquement.';
                alert(message);
            }, 300);
        } else {
            alert('❌ Erreur : ' + (res.error || 'Erreur inconnue'));
        }
        $('#btnValider').prop('disabled', false).html('<i class="la la-check-circle"></i> ENCAISSER MAINTENANT');
    }, 'json').fail(function() {
        alert('❌ Erreur de communication avec le serveur');
        $('#btnValider').prop('disabled', false).html('<i class="la la-check-circle"></i> ENCAISSER MAINTENANT');
    });
});
</script>

</body>
</html>