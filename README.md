# Cabinet Athéna Dentaire

Site one page Symfony pour le cabinet dentaire Athéna Dentaire, avec panel d'administration custom et design Figma (desktop + mobile).

## Stack

- Symfony 8.1, PHP 8.4.1+
- PostgreSQL 16, Doctrine ORM
- Tailwind CSS 4 (build npm)
- Docker (PHP-FPM, Nginx, PostgreSQL, Mailpit)
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
# Modifier APP_SECRET, mots de passe PostgreSQL, MAILER_DSN
make prod-up
```

## Structure

- `templates/pages/home.html.twig` — page publique one page
- `src/Controller/Admin/` — panel d'administration
- `public/images/` — assets visuels (Figma)
- `docker/` — configuration Docker (adaptée depuis UNAPL Normandie)
