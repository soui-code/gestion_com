<?php

/**
 * Configuration et intégration API e-MECeF (DGI Côte d'Ivoire)
 * Documentation : https://emecef.ci/api/docs
 */

class ApiDGI
{
    private $apiUrl;
    private $apiKey;
    private $ifu;
    private $nim;
    private $mode; // 'test' ou 'production'

    public function __construct($idSociete = null)
    {
        // Récupérer les credentials depuis la table societes
        $con = getDatabaseConnection();

        $idSociete = $idSociete ?? ($_SESSION['id_societe'] ?? null);

        if ($idSociete) {
            $stmt = $con->prepare("
                SELECT dgi_api_key, dgi_ifu, dgi_nim, dgi_mode
                FROM societes
                WHERE id_societe = ?
            ");
            $stmt->execute([$idSociete]);
            $config = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($config) {
                $this->apiKey = $config['dgi_api_key'];
                $this->ifu = $config['dgi_ifu'];
                $this->nim = $config['dgi_nim'];
                $this->mode = $config['dgi_mode'] ?? 'test';
            }
        }

        // URLs API selon le mode
        $this->apiUrl = $this->mode === 'production'
            ? 'https://api.emecef.ci/v1'
            : 'https://api-test.emecef.ci/v1';
    }

    /**
     * Certifier une facture auprès de la DGI
     *
     * @param array $factureData Données de la facture
     * @return array|false Données de certification ou false
     */
    public function certifierFacture($factureData)
    {
        try {
            // 1. Préparer les données au format DGI
            $payload = [
                'ifu' => $this->ifu,
                'type' => 'FV', // Facture de Vente
                'items' => $factureData['items'],
                'client' => [
                    'ifu' => $factureData['client_ifu'] ?? null,
                    'name' => $factureData['client_name'],
                    'contact' => $factureData['client_phone'] ?? null
                ],
                'payment' => [
                    'type' => $factureData['mode_paiement'] ?? 'cash',
                    'amount' => $factureData['montant_ttc']
                ],
                'operator' => [
                    'id' => $factureData['utilisateur_id'],
                    'name' => $factureData['utilisateur_nom']
                ]
            ];

            // 2. Appeler l'API DGI
            $response = $this->callApi('/invoices/confirm', $payload);

            if ($response && $response['success']) {
                return [
                    'success' => true,
                    'cachet_fiscal' => $response['data']['uid'], // Hash unique DGI
                    'signature_electronique' => $response['data']['signature'],
                    'qr_code_dgi' => $response['data']['qrCode'],
                    'numero_mecef' => $response['data']['nim'],
                    'date_certification' => date('Y-m-d H:i:s'),
                    'statut_dgi' => 'Validée'
                ];
            }

            return false;

        } catch (Exception $e) {
            Logger::error('Erreur certification DGI', [
                'error' => $e->getMessage(),
                'facture' => $factureData['numero_fiscal'] ?? 'N/A'
            ]);
            return false;
        }
    }

    /**
     * Appel générique à l'API DGI
     */
    private function callApi($endpoint, $data)
    {
        $ch = curl_init($this->apiUrl . $endpoint);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
                'Accept: application/json'
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => $this->mode === 'production'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("API DGI erreur HTTP $httpCode : $response");
        }

        return json_decode($response, true);
    }

    /**
     * Vérifier si l'API DGI est configurée et active
     */
    public function estActif()
    {
        return !empty($this->apiKey)
            && !empty($this->ifu)
            && !empty($this->nim);
    }

    /**
     * Vérifier une facture via QR Code
     */
    public function verifierFacture($cachetFiscal)
    {
        try {
            $response = $this->callApi('/invoices/verify', [
                'uid' => $cachetFiscal
            ]);

            return $response['valid'] ?? false;

        } catch (Exception $e) {
            return false;
        }
    }
}
