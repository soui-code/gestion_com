# 🏪 Système de Gestion Commerciale

Un système complet de gestion commerciale multi-boutiques avec point de vente (POS), gestion des stocks, facturation et reporting.

## 🌟 Fonctionnalités

### Gestion Commerciale
- ✅ Point de vente (POS) moderne et intuitif
- ✅ Gestion des produits avec photos
- ✅ Gestion des stocks avec alertes automatiques
- ✅ Suivi des péremptions
- ✅ Gestion des catégories et fournisseurs
- ✅ Gestion des clients (B2B et B2C)

### Facturation & Paiements
- ✅ Génération automatique de factures
- ✅ Devis et factures proforma
- ✅ Gestion des taxes (TVA, douanes, etc.)
- ✅ Remises et promotions
- ✅ Multiples modes de paiement (Espèces, Carte, Mobile Money, etc.)
- ✅ Historique des transactions

### Multi-tenant & Multi-boutiques
- ✅ Gestion de plusieurs sociétés
- ✅ Gestion de plusieurs boutiques par société
- ✅ Localisation GPS des boutiques
- ✅ Isolation complète des données par société

### Sécurité & Gestion des Utilisateurs
- ✅ Système d'authentification sécurisé (bcrypt)
- ✅ Gestion des rôles (Admin, Manager, Caissier)
- ✅ Protection CSRF sur tous les formulaires
- ✅ Logging complet des actions
- ✅ Sessions sécurisées

### Reporting & Analytics
- ✅ Tableau de bord avec statistiques
- ✅ Historique des ventes
- ✅ Historique des commandes
- ✅ Mouvements de stock détaillés
- ✅ Export de données

## 🚀 Installation

### Prérequis

- PHP 8.0 ou supérieur
- MySQL 8.0 ou supérieur
- Serveur web (Apache/Nginx)
- Extension PHP PDO
- Extension PHP GD (pour les images)

### Étapes d'Installation

1. **Cloner le projet**
```bash
git clone [URL_DU_REPO]
cd gestion_commerciale
```

2. **Configurer la base de données**
```bash
# Créer la base de données
mysql -u root -p
CREATE DATABASE gestion_commerciale CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
exit;

# Importer le schéma
mysql -u root -p gestion_commerciale < database/gestion_commerciale.sql
```

3. **Configurer l'environnement**
```bash
# Copier le fichier d'exemple
cp .env.example .env

# Éditer .env avec vos paramètres
nano .env
```

Exemple de configuration `.env` :
```env
DB_HOST=localhost
DB_NAME=gestion_commerciale
DB_USER=root
DB_PASS=votre_mot_de_passe

APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost/gestion_commerciale

UPLOAD_MAX_SIZE=5242880
UPLOAD_DIR=uploads

SESSION_LIFETIME=7200

LOG_LEVEL=debug
LOG_PATH=logs/app.log
```

4. **Configurer les permissions**
```bash
# Linux/Mac
chmod 755 logs uploads cache
chmod 600 .env

# Windows (via l'explorateur)
# Vérifier que les dossiers logs, uploads, cache sont accessibles en écriture
```

5. **Configurer le serveur web**

**Apache** - Créer un VirtualHost :
```apache
<VirtualHost *:80>
    ServerName gestion.local
    DocumentRoot "C:/wamp64/www/gestion_commerciale"
    
    <Directory "C:/wamp64/www/gestion_commerciale">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "logs/gestion-error.log"
    CustomLog "logs/gestion-access.log" common
</VirtualHost>
```

**Nginx** :
```nginx
server {
    listen 80;
    server_name gestion.local;
    root /var/www/gestion_commerciale;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

6. **Accéder à l'application**
```
http://localhost/gestion_commerciale
ou
http://gestion.local
```

7. **Connexion par défaut**
```
Login: 1
Mot de passe: [voir dans la base de données]
```

## 📁 Structure du Projet

```
gestion_commerciale/
├── config/                 # Configuration de l'application
│   ├── config.php         # Configuration principale
│   ├── env.php            # Gestion des variables d'environnement
│   ├── csrf.php           # Protection CSRF
│   ├── logger.php         # Système de logging
│   ├── validator.php      # Validation des données
│   ├── paginator.php      # Système de pagination
│   ├── helpers.php        # Fonctions utilitaires
│   └── dashboard.php      # Template du dashboard
├── controllers/           # Contrôleurs MVC
│   ├── BaseController.php # Contrôleur de base
│   ├── UtilisateurController.php
│   ├── ProduitController.php
│   ├── VenteController.php
│   └── ...
├── models/                # Modèles de données
│   ├── utilisateur.php
│   ├── produit.php
│   ├── vente.php
│   └── ...
├── views/                 # Vues (templates)
│   ├── utilisateur/
│   ├── produit/
│   ├── vente/
│   └── ...
├── database/              # Base de données
│   ├── database.php       # Connexion PDO
│   └── gestion_commerciale.sql  # Schéma SQL
├── uploads/               # Fichiers uploadés
│   └── produits/         # Photos des produits
├── logs/                  # Logs applicatifs
├── cache/                 # Cache (futur)
├── libraries/             # Librairies tierces (FPDF, QRCode)
├── assets/                # Ressources statiques (CSS, JS, images)
├── .env                   # Variables d'environnement (non versionné)
├── .env.example           # Template de configuration
├── .gitignore            # Exclusions Git
├── router.php            # Routeur principal
├── index.php             # Point d'entrée
├── README.md             # Ce fichier
└── CHANGELOG.md          # Historique des modifications
```

## 🔐 Sécurité

### Bonnes Pratiques Implémentées

- ✅ **Hachage des mots de passe** avec bcrypt (cost 12)
- ✅ **Protection CSRF** sur tous les formulaires
- ✅ **Échappement XSS** sur toutes les sorties
- ✅ **Prepared statements** pour éviter les injections SQL
- ✅ **Validation** côté serveur de toutes les entrées
- ✅ **Logging** de toutes les actions sensibles
- ✅ **Régénération** de l'ID de session après connexion
- ✅ **Délai** après échec de connexion (anti brute-force)
- ✅ **Variables d'environnement** pour les credentials
- ✅ **Séparation** production/développement

### Recommandations pour la Production

1. **Activer HTTPS** obligatoire
2. **Configurer** `APP_ENV=production` et `APP_DEBUG=false`
3. **Sécuriser** le fichier `.env` (chmod 600)
4. **Désactiver** l'affichage des erreurs PHP
5. **Configurer** un firewall (fail2ban, etc.)
6. **Effectuer** des backups réguliers
7. **Mettre à jour** régulièrement PHP et MySQL
8. **Restreindre** l'accès au dossier `/logs`
9. **Configurer** des en-têtes de sécurité HTTP
10. **Auditer** régulièrement les logs

## 👥 Rôles & Permissions

### Admin
- Accès complet à toutes les fonctionnalités
- Gestion de toutes les sociétés
- Gestion de tous les utilisateurs
- Accès à toutes les boutiques

### Manager
- Gestion de sa société
- Gestion des produits, stocks, prix
- Gestion des utilisateurs de sa société
- Gestion des boutiques
- Accès aux rapports complets

### Caissier
- Point de vente uniquement
- Consultation des stocks
- Historique de ses ventes
- Pas d'accès à la gestion

## 🛠 Maintenance

### Logs

Les logs sont stockés dans `/logs/app.log` avec les niveaux suivants :
- **DEBUG** : Informations de débogage
- **INFO** : Informations générales
- **WARNING** : Avertissements
- **ERROR** : Erreurs
- **CRITICAL** : Erreurs critiques

Configuration dans `.env` :
```env
LOG_LEVEL=info        # En production, utiliser 'info' ou 'warning'
LOG_PATH=logs/app.log
```

### Rotation des Logs

Configurer la rotation automatique des logs (logrotate sous Linux) :

```
/var/www/gestion_commerciale/logs/*.log {
    daily
    rotate 7
    compress
    delaycompress
    missingok
    notifempty
    create 0644 www-data www-data
}
```

### Backup

Backup recommandé :
1. **Base de données** : Export quotidien avec `mysqldump`
2. **Fichiers uploadés** : `/uploads` (rsync ou similaire)
3. **Configuration** : `.env` (sauvegarde sécurisée)

Script de backup exemple :
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u root -p gestion_commerciale > backup_$DATE.sql
tar -czf uploads_$DATE.tar.gz uploads/
```

## 🐛 Dépannage

### Erreur de connexion à la base de données
- Vérifier les credentials dans `.env`
- Vérifier que MySQL est démarré
- Vérifier que la base de données existe

### Page blanche / Erreur 500
- Vérifier les logs dans `/logs/app.log`
- Vérifier les logs Apache/Nginx
- Activer temporairement `APP_DEBUG=true`

### Erreur CSRF
- Vider les cookies et sessions
- Vérifier que les sessions PHP fonctionnent
- Vérifier les permissions du dossier de sessions PHP

### Problème d'upload
- Vérifier les permissions du dossier `/uploads`
- Vérifier la configuration PHP (`upload_max_filesize`, `post_max_size`)
- Vérifier `UPLOAD_MAX_SIZE` dans `.env`

## 📞 Support

Pour toute question ou problème :
- Consulter la documentation dans `/docs` (si disponible)
- Vérifier les logs dans `/logs/app.log`
- Consulter le CHANGELOG.md pour les nouveautés
- Contacter l'équipe de développement

## 📜 Licence

Projet propriétaire - Tous droits réservés

## 🎯 Roadmap

### Version 2.1 (Prévue)
- [ ] API REST complète
- [ ] Application mobile
- [ ] Dashboard analytics avancé (Chart.js)
- [ ] Export Excel/PDF avancé
- [ ] Notifications par email/SMS

### Version 3.0 (Future)
- [ ] Multi-langue (FR, EN, AR)
- [ ] Mode hors-ligne (PWA)
- [ ] Intégration comptabilité
- [ ] Marketplace tiers
- [ ] IA pour prédictions de stock

## 🤝 Contribution

Pour contribuer au projet :
1. Fork le projet
2. Créer une branche (`git checkout -b feature/amazing-feature`)
3. Commit les changements (`git commit -m 'Add amazing feature'`)
4. Push vers la branche (`git push origin feature/amazing-feature`)
5. Ouvrir une Pull Request

## 👨‍💻 Auteurs

- **Équipe de développement** - Version initiale
- **Claude** - Améliorations sécurité & performance v2.0

---

**Version actuelle** : 2.0.0  
**Dernière mise à jour** : 2026-06-13
