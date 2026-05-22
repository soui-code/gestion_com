-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 12 mars 2026 à 09:46
-- Version du serveur : 9.1.0
-- Version de PHP : 8.4.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestion_commerciale`
--

-- --------------------------------------------------------

--
-- Structure de la table `boutiques`
--

DROP TABLE IF EXISTS `boutiques`;
CREATE TABLE IF NOT EXISTS `boutiques` (
  `code_boutique` int NOT NULL AUTO_INCREMENT,
  `nom_boutique` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `telephone_boutique` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `produit_json` json DEFAULT NULL,
  `etat_boutique` enum('active','inactive') COLLATE utf8mb4_general_ci DEFAULT 'active',
  PRIMARY KEY (`code_boutique`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `boutiques`
--

INSERT INTO `boutiques` (`code_boutique`, `nom_boutique`, `telephone_boutique`, `latitude`, `longitude`, `produit_json`, `etat_boutique`) VALUES
(1, 'Sococe', '2250778638987', 7.28230000, 2.32130000, '[{\"etat\": \"actif\", \"photo\": null, \"stock\": \"34\", \"reference\": \"5\", \"photo_type\": null, \"prix_achat\": \"300.00\", \"prix_vente\": \"500.00\", \"description\": \"Boisson gazeuse\", \"designation\": \"Coca Cola 50cl\", \"stock_alert\": \"20\"}, {\"etat\": \"actif\", \"photo\": null, \"stock\": \"59\", \"reference\": \"10\", \"photo_type\": null, \"prix_achat\": \"800.00\", \"prix_vente\": \"1200.00\", \"description\": \"Savon doux\", \"designation\": \"Savon Liquide 500ml\", \"stock_alert\": \"20\"}]', 'active');

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `code_client` int NOT NULL AUTO_INCREMENT,
  `nom_prenom_client` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `telephone_client` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email_client` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `adresse_client` text COLLATE utf8mb4_general_ci,
  `etat_client` enum('actif','inactif') COLLATE utf8mb4_general_ci DEFAULT 'actif',
  PRIMARY KEY (`code_client`),
  KEY `idx_clients_nom` (`nom_prenom_client`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `clients`
--

INSERT INTO `clients` (`code_client`, `nom_prenom_client`, `telephone_client`, `email_client`, `adresse_client`, `etat_client`) VALUES
(3, 'Client/Contoire', 'N/A', 'N/A', 'N/A', 'actif'),
(4, 'Koffi Jean', '+225 07 89 12 34 56', 'koffi@gmail.com', 'Abidjan Cocody Angré', 'actif'),
(5, 'Aminata Traoré', '+225 05 55 44 33 22', 'aminata@orange.ci', 'Yopougon Siporex', 'actif'),
(6, 'Konan Pierre', '+225 01 23 45 67 89', NULL, 'Marcory Zone 4', 'actif'),
(7, 'Fatou Diarra', '+225 07 77 88 99 00', 'fatou@yahoo.fr', 'Treichville Avenue 10', 'actif'),
(8, 'Client Occasionnel', '', '', '', 'inactif');

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

DROP TABLE IF EXISTS `commandes`;
CREATE TABLE IF NOT EXISTS `commandes` (
  `numero_commande` int NOT NULL AUTO_INCREMENT,
  `date_commande` date NOT NULL DEFAULT (curdate()),
  `heure_commande` time NOT NULL DEFAULT (curtime()),
  `prix_achat` decimal(10,2) NOT NULL,
  `prix_vente` decimal(10,2) NOT NULL,
  `prix_commande` decimal(10,2) NOT NULL,
  `quantite_commande` int NOT NULL,
  `montant_commande` decimal(12,2) GENERATED ALWAYS AS ((`prix_commande` * `quantite_commande`)) STORED,
  `reference` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `code_client` int DEFAULT NULL,
  `numero_facture` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `utilisateur_id` int NOT NULL,
  `statut_commande` enum('Vente','Achat','Approvisionnement') COLLATE utf8mb4_general_ci NOT NULL,
  `etat_commande` enum('validée','annulée','en_attente') COLLATE utf8mb4_general_ci DEFAULT 'validée',
  PRIMARY KEY (`numero_commande`),
  KEY `reference` (`reference`),
  KEY `code_client` (`code_client`),
  KEY `numero_facture` (`numero_facture`),
  KEY `utilisateur_id` (`utilisateur_id`),
  KEY `idx_commandes_date` (`date_commande`),
  KEY `idx_commandes_statut` (`statut_commande`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commandes`
--

INSERT INTO `commandes` (`numero_commande`, `date_commande`, `heure_commande`, `prix_achat`, `prix_vente`, `prix_commande`, `quantite_commande`, `reference`, `code_client`, `numero_facture`, `utilisateur_id`, `statut_commande`, `etat_commande`) VALUES
(1, '2025-12-10', '14:40:22', 50.00, 100.00, 100.00, 2, '21312', 1, '12121', 10, 'Vente', 'validée'),
(2, '2025-09-10', '10:30:00', 300.00, 500.00, 500.00, 12, '1', 1, 'FACT-20250910-0001', 1, 'Vente', 'validée'),
(3, '2025-09-15', '14:20:00', 250.00, 400.00, 400.00, 20, '2', 2, 'FACT-20250915-0002', 1, 'Vente', 'validée'),
(4, '2025-09-20', '09:15:00', 1200.00, 1800.00, 1800.00, 5, '3', 3, NULL, 1, 'Vente', 'validée'),
(5, '2025-10-05', '11:45:00', 3800.00, 4500.00, 4500.00, 3, '4', 1, 'FACT-20251005-0003', 1, 'Vente', 'validée'),
(6, '2025-10-12', '16:30:00', 900.00, 1300.00, 1300.00, 15, '5', 4, NULL, 1, 'Vente', 'validée'),
(7, '2025-10-18', '13:10:00', 150.00, 300.00, 300.00, 50, '7', 2, 'FACT-20251018-0004', 1, 'Vente', 'validée'),
(8, '2025-11-03', '08:55:00', 1400.00, 2000.00, 2000.00, 8, '8', 1, NULL, 1, 'Vente', 'validée'),
(9, '2025-11-10', '17:20:00', 300.00, 500.00, 500.00, 30, '1', 3, 'FACT-20251110-0005', 1, 'Vente', 'validée'),
(10, '2025-11-25', '12:40:00', 250.00, 400.00, 400.00, 25, '2', 4, NULL, 1, 'Vente', 'validée'),
(11, '2025-12-01', '10:15:00', 1200.00, 1800.00, 1800.00, 10, '3', 1, 'FACT-20251201-0006', 1, 'Vente', 'validée'),
(12, '2025-12-05', '15:30:00', 3800.00, 4500.00, 4500.00, 2, '4', 2, NULL, 1, 'Vente', 'validée'),
(13, '2025-12-08', '11:20:00', 900.00, 1300.00, 1300.00, 18, '5', 3, 'FACT-20251208-0007', 1, 'Vente', 'validée'),
(14, '2025-12-10', '14:45:00', 150.00, 300.00, 300.00, 80, '7', 1, NULL, 1, 'Vente', 'validée'),
(15, '2025-12-11', '07:07:15', 0.00, 500.00, 500.00, 10, '5', 3, 'FACT-20251211-0001', 11, 'Vente', 'validée'),
(16, '2025-12-11', '09:14:25', 0.00, 300.00, 300.00, 2, '11', 3, 'FACT-20251211-0002', 11, 'Vente', 'validée'),
(17, '2025-12-11', '10:55:30', 0.00, 500.00, 500.00, 1, '5', 3, 'FACT-20251211-0003', 11, 'Vente', 'validée'),
(18, '2025-12-11', '10:55:30', 0.00, 1200.00, 1200.00, 1, '10', 3, 'FACT-20251211-0003', 11, 'Vente', 'validée'),
(19, '2025-12-11', '14:42:15', 0.00, 500.00, 500.00, 10, '5', 3, 'FACT-20251211-0004', 13, 'Vente', 'validée'),
(20, '2025-12-11', '15:27:28', 0.00, 500.00, 500.00, 2, '5', 3, 'FACT-20251211-0005', 11, 'Vente', 'validée');

-- --------------------------------------------------------

--
-- Structure de la table `factures`
--

DROP TABLE IF EXISTS `factures`;
CREATE TABLE IF NOT EXISTS `factures` (
  `numero_facture` int NOT NULL AUTO_INCREMENT,
  `titre_facture` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `date_facture` date NOT NULL,
  `type_facture` enum('vente','achat','avoir','proforma') COLLATE utf8mb4_general_ci NOT NULL,
  `montant_ht` decimal(12,2) NOT NULL DEFAULT '0.00',
  `code_taxe` int DEFAULT NULL,
  `montant_ttc` decimal(12,2) NOT NULL DEFAULT '0.00',
  `code_client` int NOT NULL,
  `etat_facture` enum('brouillon','validée','annulée','payée') COLLATE utf8mb4_general_ci DEFAULT 'brouillon',
  PRIMARY KEY (`numero_facture`),
  KEY `code_taxe` (`code_taxe`),
  KEY `idx_factures_client` (`code_client`),
  KEY `idx_factures_date` (`date_facture`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `factures`
--

INSERT INTO `factures` (`numero_facture`, `titre_facture`, `date_facture`, `type_facture`, `montant_ht`, `code_taxe`, `montant_ttc`, `code_client`, `etat_facture`) VALUES
(1, 'Bic', '2025-12-10', 'vente', 100.00, 1, 100.00, 200, 'payée'),
(2, 'Vente Coca + Eau', '2025-09-10', 'vente', 8000.00, 1, 9440.00, 1, 'payée'),
(3, 'Vente Lait + Huile', '2025-10-05', 'vente', 13500.00, 1, 15930.00, 1, 'payée'),
(4, 'Vente Divers', '2025-12-01', 'vente', 18000.00, 1, 21240.00, 2, 'payée'),
(5, 'Vente directe', '2025-12-11', 'vente', 5000.00, 7, 5800.00, 3, 'payée'),
(6, 'Vente directe', '2025-12-11', 'vente', 600.00, 6, 282.00, 3, 'payée'),
(7, 'Vente directe', '2025-12-11', 'vente', 1700.00, 7, 2006.00, 3, 'payée'),
(8, 'Vente directe', '2025-12-11', 'vente', 5000.00, 7, 5900.00, 3, 'payée'),
(9, 'Vente directe', '2025-12-11', 'vente', 1000.00, 7, 1080.00, 3, 'payée');

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id_categorie` int NOT NULL AUTO_INCREMENT,
  `nom_categorie` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `etat` enum('actif','inactif') COLLATE utf8mb4_general_ci DEFAULT 'actif',
  PRIMARY KEY (`id_categorie`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `categories` (`id_categorie`, `nom_categorie`, `description`, `etat`) VALUES
(1, 'Boissons', 'Toutes les boissons liquides', 'actif'),
(2, 'Hygiène', 'Produits d\'entretien et savons', 'actif');

-- --------------------------------------------------------

--
-- Structure de la table `fournisseurs`
--

DROP TABLE IF EXISTS `fournisseurs`;
CREATE TABLE IF NOT EXISTS `fournisseurs` (
  `id_fournisseur` int NOT NULL AUTO_INCREMENT,
  `nom_fournisseur` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `adresse` text COLLATE utf8mb4_general_ci,
  `etat` enum('actif','inactif') COLLATE utf8mb4_general_ci DEFAULT 'actif',
  PRIMARY KEY (`id_fournisseur`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `fournisseurs` (`id_fournisseur`, `nom_fournisseur`, `telephone`, `email`, `adresse`, `etat`) VALUES
(1, 'Fournisseur A', '0102030405', 'fournA@test.com', 'Adresse Fournisseur A', 'actif'),
(2, 'Fournisseur B', '0504030201', 'fournB@test.com', 'Adresse Fournisseur B', 'actif');

-- --------------------------------------------------------

--
-- Structure de la table `mouvements_stock`
--

DROP TABLE IF EXISTS `mouvements_stock`;
CREATE TABLE IF NOT EXISTS `mouvements_stock` (
  `id_mouvement` int NOT NULL AUTO_INCREMENT,
  `reference_produit` int NOT NULL,
  `type_mouvement` enum('entree','sortie') COLLATE utf8mb4_general_ci NOT NULL,
  `quantite` int NOT NULL,
  `date_mouvement` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `utilisateur_id` int DEFAULT NULL,
  `motif` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_mouvement`),
  KEY `fk_mouv_produit` (`reference_produit`),
  KEY `fk_mouv_utilisateur` (`utilisateur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

DROP TABLE IF EXISTS `produits`;
CREATE TABLE IF NOT EXISTS `produits` (
  `reference` int NOT NULL AUTO_INCREMENT,
  `designation` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `id_categorie` int DEFAULT NULL,
  `id_fournisseur` int DEFAULT NULL,
  `prix_achat` decimal(10,2) NOT NULL DEFAULT '0.00',
  `prix_vente` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stock` int NOT NULL DEFAULT '0',
  `stock_alert` int NOT NULL DEFAULT '5',
  `photo` longblob,
  `photo_type` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `etat` enum('actif','inactif') COLLATE utf8mb4_general_ci DEFAULT 'actif',
  PRIMARY KEY (`reference`),
  KEY `idx_produits_designation` (`designation`),
  KEY `fk_categorie` (`id_categorie`),
  KEY `fk_fournisseur` (`id_fournisseur`),
  CONSTRAINT `fk_categorie` FOREIGN KEY (`id_categorie`) REFERENCES `categories` (`id_categorie`) ON DELETE SET NULL,
  CONSTRAINT `fk_fournisseur` FOREIGN KEY (`id_fournisseur`) REFERENCES `fournisseurs` (`id_fournisseur`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`reference`, `designation`, `id_categorie`, `id_fournisseur`, `prix_achat`, `prix_vente`, `stock`, `stock_alert`, `photo`, `photo_type`, `description`, `etat`) VALUES
(5, 'Coca Cola 50cl', 1, 1, 300.00, 500.00, 22, 20, NULL, NULL, 'Boisson gazeuse', 'actif'),
(10, 'Savon Liquide 500ml', 2, 2, 800.00, 1200.00, 11, 10, NULL, NULL, 'Savon doux', 'actif');

-- --------------------------------------------------------

--
-- Structure de la table `taxes`
--

DROP TABLE IF EXISTS `taxes`;
CREATE TABLE IF NOT EXISTS `taxes` (
  `code_taxe` int NOT NULL AUTO_INCREMENT,
  `titre_taxe` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `taux_taxe` decimal(5,2) NOT NULL DEFAULT '0.00',
  `action_taxe` enum('ajouter','soustraire') COLLATE utf8mb4_general_ci DEFAULT 'ajouter',
  `etat_taxe` enum('active','inactive') COLLATE utf8mb4_general_ci DEFAULT 'active',
  PRIMARY KEY (`code_taxe`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `taxes`
--

INSERT INTO `taxes` (`code_taxe`, `titre_taxe`, `taux_taxe`, `action_taxe`, `etat_taxe`) VALUES
(1, 'TVA', 19.00, 'ajouter', 'active'),
(7, 'TVA 18%', 18.00, 'ajouter', 'active'),
(6, 'Molestias nostrud no', 53.00, 'soustraire', 'active'),
(8, 'Taxe Douanière', 5.00, 'ajouter', 'active'),
(9, 'Réduction Promo', 10.00, 'soustraire', 'active');

-- --------------------------------------------------------

--
-- Structure de la table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
CREATE TABLE IF NOT EXISTS `transactions` (
  `numero_transaction` int NOT NULL AUTO_INCREMENT,
  `date_transaction` date NOT NULL DEFAULT (curdate()),
  `heure_transaction` time NOT NULL DEFAULT (curtime()),
  `montant_transaction` decimal(12,2) NOT NULL,
  `mode_reglement` enum('Espèces','Carte bancaire','Chèque','Virement','Mobile Money','Autres') COLLATE utf8mb4_general_ci NOT NULL,
  `numero_facture` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `utilisateur_id` int NOT NULL,
  `etat_transaction` enum('validée','annulée','en_attente') COLLATE utf8mb4_general_ci DEFAULT 'validée',
  PRIMARY KEY (`numero_transaction`),
  KEY `numero_facture` (`numero_facture`),
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `transactions`
--

INSERT INTO `transactions` (`numero_transaction`, `date_transaction`, `heure_transaction`, `montant_transaction`, `mode_reglement`, `numero_facture`, `utilisateur_id`, `etat_transaction`) VALUES
(1, '2025-12-10', '00:00:00', 31000.00, 'Espèces', '12121', 10, 'validée'),
(2, '2025-12-11', '06:38:12', 6000.00, 'Espèces', 'FACT-20250910-0001', 1, 'validée'),
(3, '2025-12-11', '06:38:12', 8000.00, 'Mobile Money', 'FACT-20250915-0002', 1, 'validée'),
(4, '2025-12-11', '06:38:12', 13500.00, 'Espèces', 'FACT-20251005-0003', 1, 'validée'),
(5, '2025-12-11', '06:38:12', 18000.00, 'Mobile Money', 'FACT-20251201-0006', 1, 'validée'),
(6, '2025-12-11', '06:38:12', 23400.00, 'Espèces', 'FACT-20251208-0007', 1, 'validée'),
(7, '2025-12-11', '07:07:15', 6000.00, 'Espèces', 'FACT-20251211-0001', 11, 'validée'),
(8, '2025-12-11', '09:14:25', 500.00, 'Espèces', 'FACT-20251211-0002', 11, 'validée'),
(9, '2025-12-11', '10:55:30', 3100.00, 'Espèces', 'FACT-20251211-0003', 11, 'validée'),
(10, '2025-12-11', '14:42:15', 6000.00, 'Espèces', 'FACT-20251211-0004', 13, 'validée'),
(11, '2025-12-11', '15:27:28', 1100.00, 'Mobile Money', 'FACT-20251211-0005', 11, 'validée');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `utilisateur_id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `numero` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `login` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `mdp` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `etat` enum('actif','inactif') COLLATE utf8mb4_general_ci DEFAULT 'actif',
  PRIMARY KEY (`utilisateur_id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`utilisateur_id`, `nom`, `numero`, `login`, `mdp`, `role`, `etat`) VALUES
(11, 'Idriss', '0778638987', '1', '356a192b7913b04c54574d18c28d46e6395428ab', 'manager', 'actif'),
(13, 'Ben', '0778638987', '2', 'e66bb2fabbe7b86def4cb857aa62748ce7880394', 'caissier', 'actif');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
