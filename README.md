# Cabinet Athéna Dentaire

Site one page Symfony pour le cabinet dentaire Athéna Dentaire, avec panel d'administration custom et design Figma (desktop + mobile).

## Stack

- Symfony 8.1, PHP 8.4.1+
- MySQL 8.4, Doctrine ORM
- Tailwind CSS 4 (build npm)
- Docker (PHP-FPM, Nginx, MySQL, Mailpit)
- Asset Mapper + Stimulus

## Démarrage rapide (Docker)

```bash
cp .env.docker.example .env.docker
make dev-up
make dev-fixtures   # première installation uniquement
```

- Site : http://localhost:8081
- Mailpit : http://localhost:8026
- Admin : http://localhost:8081/admin/login

### Comptes de démo (fixtures)

| Email | Mot de passe | Rôle |
|-------|--------------|------|
| admin@athena-dentaire.fr | AdminAthena2026! | Administrateur |
| editor@athena-dentaire.fr | EditorAthena2026! | Éditeur |

## Développement local (sans Docker)

```bash
composer install
npm install
npm run build:css
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
symfony server:start
```

## Tailwind CSS

```bash
npm run build:css   # production
npm run watch:css   # développement
```

Le fichier compilé est `assets/styles/app.built.css`.

## Administration

Le panel `/admin` permet de gérer :

- Paramètres du cabinet (coordonnées, images, textes globaux)
- Sections textuelles (hero, urgences, mentions légales…)
- Horaires d'ouverture
- Plateau technique
- Pôles de compétences
- Équipe
- Messages de contact
- Utilisateurs (admin uniquement)

## Production

```bash
cp .env.prod.example .env.prod
# Modifier APP_SECRET, mots de passe MySQL, MAILER_DSN
make prod-up
```

## Installation sur Plesk (hébergement traditionnel)

Pas de Docker : le site tourne sur l'hébergement Plesk avec PHP, MySQL et le serveur web fournis par Plesk. Le CSS est déjà compilé dans le dépôt (`assets/styles/app.built.css`), Node.js n'est donc pas nécessaire sur le serveur.

### Prérequis

- Un abonnement Plesk avec accès SSH
- PHP **8.4** activé pour le domaine, avec les extensions `pdo_mysql`, `intl`, `opcache`, `mbstring`, `xml` (ce sont les extensions de base proposées par Plesk)
- Une base MySQL 8 (ou MariaDB récente) créée dans Plesk
- Composer disponible sur le serveur (Plesk l'intègre dans l'onglet *Composer*, ou via SSH)

### 1. Déployer le code

Dans Plesk, ouvrir le domaine → **Git** (ou SSH) et cloner le dépôt **en dehors** du dossier web, par exemple :

```bash
cd /var/www/vhosts/athena-dentaire.fr
git clone <url-du-depot> app
cd app
```

Le dossier web du domaine (`httpdocs`) doit pointer vers le dossier **`public/`** du projet. Dans Plesk : **Hébergement web → Dossier racine du site** (ou *Document root*) → `app/public`.

### 2. Installer les dépendances

```bash
composer install --no-dev --optimize-autoloader --no-interaction
```

### 3. Configurer l'environnement

Créer le fichier `.env.local` à la racine du projet (il n'est pas versionné) :

```dotenv
APP_ENV=prod
APP_SECRET=<valeur longue et aléatoire>
DEFAULT_URI=https://www.athena-dentaire.fr
DATABASE_URL="mysql://<user>:<mot_de_passe>@127.0.0.1:3306/<base>?serverVersion=8.4&charset=utf8mb4"
MAILER_DSN=smtp://<user>:<mot_de_passe>@<serveur-smtp>:587
SECURITY_EMAIL_FROM=security@athena-dentaire.fr
```

- `APP_SECRET` : `php -r "echo bin2hex(random_bytes(32));"`
- `DATABASE_URL` : utiliser l'utilisateur et la base créés dans Plesk (**Bases de données**). Adapter `serverVersion` à la version réelle de MySQL/MariaDB.
- `MAILER_DSN` : le SMTP de la messagerie du domaine. Sans lui, les codes de double authentification ne partent pas.

Compiler la configuration pour la production (évite de lire les fichiers `.env` à chaque requête) :

```bash
composer dump-env prod
```

### 4. Créer les tables et le compte administrateur

```bash
php bin/console doctrine:migrations:migrate --no-interaction --env=prod
php bin/console app:create-admin "admin@athena-dentaire.fr" "Nom Prénom" --env=prod
```

Le mot de passe est demandé ensuite en saisie masquée (12 caractères minimum).

**Ne pas charger les fixtures en production** : elles créent des comptes de démonstration aux mots de passe publics.

### 5. Droits et cache

Le serveur web doit pouvoir écrire dans `var/` et `public/uploads/` (images envoyées depuis l'admin) :

```bash
chmod -R ug+rwX var public/uploads
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod
```

Avec `var/` et `public/uploads/` en écriture, les images sont conservées entre les mises à jour.

### 6. Rediriger les URL vers `index.php`

Symfony a besoin que toutes les URL passent par `public/index.php`.

- **Serveur Apache** : créer `public/.htaccess` :

  ```apache
  FallbackResource /index.php
  ```

  Le dépôt ne contient pas ce fichier pour l'instant. Il faut donc le créer sur le serveur, ou l'ajouter au dépôt.

- **Nginx seul (Plesk)** : dans le domaine → **Apache & nginx Settings** → **Directives supplémentaires nginx** :

  ```nginx
  location / {
      try_files $uri /index.php$is_args$args;
  }
  ```

### 7. HTTPS

Dans Plesk, activer le certificat **Let's Encrypt** du domaine et la redirection HTTP → HTTPS. Vérifier ensuite :

```bash
curl -I https://www.athena-dentaire.fr/
```

La page doit répondre en `200`, et `https://www.athena-dentaire.fr/admin/login` doit afficher la page de connexion.

### Mise à jour

```bash
cd /var/www/vhosts/athena-dentaire.fr/app
git pull
composer install --no-dev --optimize-autoloader --no-interaction
composer dump-env prod
php bin/console doctrine:migrations:migrate --no-interaction --env=prod
php bin/console cache:clear --env=prod
```

### Points d'attention

- **Sauvegardes** : sauvegarder régulièrement la base depuis Plesk (**Outils → Sauvegarde**, ou `mysqldump`), ainsi que `public/uploads/` et `.env.local`.
- **En-têtes de sécurité** : la configuration Nginx du dépôt (`docker/nginx/default.prod.conf`) ajoute des en-têtes (Content-Security-Policy, HSTS, X-Frame-Options…). Ils ne s'appliquent pas sur un hébergement Plesk classique : les reprendre dans les *Directives supplémentaires nginx* ou dans `public/.htaccess` (`Header set ...`).
- **`.env.local`** : contient des secrets. Ne jamais le committer (il est déjà ignoré par Git).

## Structure

- `templates/pages/home.html.twig` — page publique one page
- `src/Controller/Admin/` — panel d'administration
- `public/images/` — assets visuels (Figma)
- `docker/` — configuration Docker (adaptée depuis UNAPL Normandie)
