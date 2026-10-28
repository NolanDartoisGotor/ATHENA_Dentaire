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

## Installation sur Plesk

Le site tourne en conteneurs Docker (PHP-FPM, Nginx, MySQL). Plesk occupe déjà les ports 80 et 443 : Nginx du conteneur écoute donc sur un port interne, et Plesk fait le proxy HTTPS vers lui.

### Prérequis

- Un abonnement Plesk avec accès SSH root ou sudo
- Docker et Docker Compose installés sur le serveur (extension Docker de Plesk, ou installation manuelle)
- Un domaine pointant vers le serveur (ex. `www.athena-dentaire.fr`)

### 1. Cloner le projet

```bash
cd /var/www/vhosts/athena-dentaire.fr
git clone <url-du-depot> athena
cd athena
```

### 2. Configurer l'environnement

```bash
cp .env.prod.example .env.prod
```

Dans `.env.prod`, modifier au minimum :

- `APP_SECRET` : `php -r "echo bin2hex(random_bytes(32));"`
- `DEFAULT_URI` : `https://www.athena-dentaire.fr`
- `MYSQL_PASSWORD` et le mot de passe dans `DATABASE_URL` (identiques), ainsi que `MYSQL_ROOT_PASSWORD`
- `MAILER_DSN` : serveur SMTP réel (ex. celui de la messagerie du domaine)
- `SECURITY_EMAIL_FROM` : adresse expéditrice des codes de double authentification
- `HTTP_PORT=8080` : port interne du conteneur Nginx (ne pas utiliser 80, occupé par Plesk)

### 3. Lancer les conteneurs

```bash
make prod-up
make prod-logs    # vérifier qu'il n'y a pas d'erreur au démarrage
```

Les migrations sont lancées automatiquement au démarrage (`RUN_MIGRATIONS=true`).

### 4. Créer le compte administrateur

Les fixtures ne doivent **pas** être chargées en production : elles créent des comptes de démonstration aux mots de passe publics. Créer le premier administrateur :

```bash
docker compose -f docker-compose.prod.yml --env-file .env.prod exec php php bin/console app:create-admin "admin@athena-dentaire.fr" "Nom Prénom"
```

Le mot de passe est demandé ensuite en saisie masquée (12 caractères minimum).

### 5. Relier le domaine à Nginx du conteneur

Dans Plesk, ouvrir le domaine → **Apache & nginx Settings** → **Directives supplémentaires nginx** et ajouter :

```nginx
location / {
    proxy_pass http://127.0.0.1:8080;
    proxy_set_header Host $host;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
}
```

Adapter `8080` à la valeur de `HTTP_PORT`. Activer ensuite le certificat Let's Encrypt et la redirection HTTPS dans Plesk, puis vérifier :

```bash
curl -I https://www.athena-dentaire.fr/
```

Le site doit répondre en `200`, et `https://www.athena-dentaire.fr/admin/login` doit afficher la page de connexion.

### Mise à jour

```bash
cd /var/www/vhosts/athena-dentaire.fr/athena
git pull
make prod-build
make prod-up
```

### Points d'attention

- **Images envoyées depuis l'admin** : elles sont écrites dans `public/uploads/` *à l'intérieur* du conteneur, sans volume. Elles sont perdues à la recréation du conteneur (`make prod-build`). Avant une mise à jour, sauvegarder ce dossier ou ajouter un volume pour `public/uploads` dans `docker-compose.prod.yml`.
- **Base de données** : le volume `db_data_mysql_prod` contient les données MySQL. Ne pas le supprimer (`docker compose down -v`). Prévoir une sauvegarde régulière, par exemple avec `mysqldump` :

  ```bash
  docker compose -f docker-compose.prod.yml --env-file .env.prod exec database sh -c 'mysqldump -u root -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"' > sauvegarde.sql
  ```

- **Fichier `.env.prod`** : il contient des secrets. Ne jamais le committer.

## Structure

- `templates/pages/home.html.twig` — page publique one page
- `src/Controller/Admin/` — panel d'administration
- `public/images/` — assets visuels (Figma)
- `docker/` — configuration Docker (adaptée depuis UNAPL Normandie)
