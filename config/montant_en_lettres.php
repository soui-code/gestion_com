<?php

// Guard contre les inclusions multiples
if (!function_exists('montantEnLettres')) {

/**
 * Convertit un montant numérique en lettres (français, FCFA)
 *
 * @param float $montant Le montant à convertir
 * @return string Le montant en lettres
 */
function montantEnLettres($montant)
{
    if (!is_numeric($montant)) {
        return '';
    }

    $montant = floatval($montant);

    if ($montant == 0) {
        return 'Zéro franc CFA';
    }

    $negatif = false;
    if ($montant < 0) {
        $negatif = true;
        $montant = abs($montant);
    }

    // Séparer partie entière et décimale
    $partieEntiere = floor($montant);
    $partieDecimale = round(($montant - $partieEntiere) * 100);

    $resultat = convertirPartieEntiere($partieEntiere);

    // Ajouter "francs CFA"
    if ($partieEntiere > 1) {
        $resultat .= ' francs CFA';
    } else {
        $resultat .= ' franc CFA';
    }

    // Ajouter les centimes si présents
    if ($partieDecimale > 0) {
        $resultat .= ' et ' . convertirPartieEntiere($partieDecimale);
        if ($partieDecimale > 1) {
            $resultat .= ' centimes';
        } else {
            $resultat .= ' centime';
        }
    }

    if ($negatif) {
        $resultat = 'Moins ' . $resultat;
    }

    return ucfirst($resultat);
}

} // Fin guard montantEnLettres

if (!function_exists('convertirPartieEntiere')) {

/**
 * Convertit la partie entière d'un nombre en lettres
 *
 * @param int $nombre
 * @return string
 */
function convertirPartieEntiere($nombre)
{
    if ($nombre == 0) {
        return 'zéro';
    }

    $unites = ['', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf'];
    $dizaines = ['', 'dix', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante', 'quatre-vingt', 'quatre-vingt'];
    $exceptions = [10 => 'dix', 11 => 'onze', 12 => 'douze', 13 => 'treize', 14 => 'quatorze', 15 => 'quinze', 16 => 'seize'];

    $resultat = '';

    // Milliards
    if ($nombre >= 1000000000) {
        $milliards = floor($nombre / 1000000000);
        if ($milliards == 1) {
            $resultat .= 'un milliard ';
        } else {
            $resultat .= convertirPartieEntiere($milliards) . ' milliards ';
        }
        $nombre %= 1000000000;
    }

    // Millions
    if ($nombre >= 1000000) {
        $millions = floor($nombre / 1000000);
        if ($millions == 1) {
            $resultat .= 'un million ';
        } else {
            $resultat .= convertirPartieEntiere($millions) . ' millions ';
        }
        $nombre %= 1000000;
    }

    // Milliers
    if ($nombre >= 1000) {
        $milliers = floor($nombre / 1000);
        if ($milliers == 1) {
            $resultat .= 'mille ';
        } else {
            $resultat .= convertirPartieEntiere($milliers) . ' mille ';
        }
        $nombre %= 1000;
    }

    // Centaines
    if ($nombre >= 100) {
        $centaines = floor($nombre / 100);
        if ($centaines == 1) {
            $resultat .= 'cent ';
        } else {
            $resultat .= $unites[$centaines] . ' cent ';
        }
        $nombre %= 100;

        // Accord de "cent"
        if ($nombre == 0 && $centaines > 1) {
            $resultat = rtrim($resultat) . 's ';
        }
    }

    // Dizaines et unités
    if ($nombre >= 17) {
        $dizaine = floor($nombre / 10);
        $unite = $nombre % 10;

        $resultat .= $dizaines[$dizaine];

        if ($unite == 1 && $dizaine < 8) {
            $resultat .= ' et un';
        } elseif ($unite == 1 && $dizaine == 8) {
            $resultat .= '-un';
        } elseif ($unite == 11 && $dizaine == 7) {
            $resultat .= ' et onze';
        } elseif ($unite == 11 && $dizaine == 9) {
            $resultat .= '-onze';
        } elseif ($unite > 0 && $dizaine == 7) {
            $resultat .= '-' . $exceptions[10 + $unite];
        } elseif ($unite > 0 && $dizaine == 9) {
            $resultat .= '-' . $exceptions[10 + $unite];
        } elseif ($unite > 0) {
            $resultat .= '-' . $unites[$unite];
        }

        // "Quatre-vingts" prend un "s" si rien après
        if ($dizaine == 8 && $unite == 0) {
            $resultat .= 's';
        }
    } elseif ($nombre >= 10) {
        $resultat .= $exceptions[$nombre];
    } elseif ($nombre > 0) {
        $resultat .= $unites[$nombre];
    }

    return trim($resultat);
}

} // Fin guard convertirPartieEntiere

if (!function_exists('genererNumeroFiscal')) {

/**
 * Génère un numéro fiscal normalisé
 * Format: CI123456789-2026-001234
 *
 * @param string $compteContribuable
 * @param int $idSociete
 * @return string
 */
function genererNumeroFiscal($compteContribuable, $idSociete)
{
    if (empty($compteContribuable)) {
        return null;
    }

    try {
        $con = getDatabaseConnection();
        $annee = date('Y');

        // Récupérer ou créer la séquence pour cette année
        $stmt = $con->prepare("SELECT derniere_sequence FROM sequences_fiscales WHERE id_societe = ? AND annee = ? FOR UPDATE");
        $stmt->execute([$idSociete, $annee]);
        $sequence = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($sequence) {
            // Incrémenter
            $nouvelleSequence = $sequence['derniere_sequence'] + 1;
            $con->prepare("UPDATE sequences_fiscales SET derniere_sequence = ? WHERE id_societe = ? AND annee = ?")
                ->execute([$nouvelleSequence, $idSociete, $annee]);
        } else {
            // Créer première séquence
            $nouvelleSequence = 1;
            $con->prepare("INSERT INTO sequences_fiscales (id_societe, annee, derniere_sequence) VALUES (?, ?, ?)")
                ->execute([$idSociete, $annee, $nouvelleSequence]);
        }

        // Format: CI123456789-2026-001234
        $numeroFiscal = $compteContribuable . '-' . $annee . '-' . str_pad($nouvelleSequence, 6, '0', STR_PAD_LEFT);

        return $numeroFiscal;
    } catch (Exception $e) {
        Logger::error('Erreur génération numéro fiscal', ['error' => $e->getMessage()]);
        return null;
    }
}

} // Fin guard genererNumeroFiscal

if (!function_exists('genererQRCodeFacture')) {

/**
 * Génère un QR Code pour la facture
 * Contient : Numéro fiscal, montant TTC, date, client
 *
 * @param array $factureData
 * @return string Base64 du QR code
 */
function genererQRCodeFacture($factureData)
{
    // Données à encoder dans le QR code
    $data = sprintf(
        "FNE-CI\nN°: %s\nDate: %s\nMontant: %s FCFA\nClient: %s",
        $factureData['numero_fiscal'] ?? 'N/A',
        date('d/m/Y H:i', strtotime($factureData['date_emission'] ?? 'now')),
        number_format($factureData['montant_ttc'] ?? 0, 0, ',', ' '),
        $factureData['nom_client'] ?? 'Client de passage'
    );

    // URL de l'API QR Code (Google Charts)
    $qrCodeUrl = 'https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=' . urlencode($data) . '&choe=UTF-8';

    return $qrCodeUrl;
}

} // Fin guard genererQRCodeFacture
