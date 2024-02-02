# Installation
### requirements:
- apache2
- php >=8.1
- composer >=2
- node >= 16
- npm >=8

 ### installer le code source:
`git clone [url_du_repository] dir`

### installer les dépendances PHP
```
cd dir
composer install
```

### configurer le .env
`nano .env`
remplir les paramètres de connexion à la base de données
et optionnellement changer la nomenclature de l'appli

### construire la base de donnée
`php bin/console doctrine:schema:update --force`

### créer un admin
`php bin/console create:user`
remplir le rôle avec 'ROLE_ADMIN'

### installer les dépendances Javascript
```
npm ci (ou npm install si erreur)
npm run prod
```

### (option) customiser le CSS
éditer le fichier `./assets/css/style.scss`
relancer la compilation avec `npm run prod`

### configurer apache/nginx
configurer le vhost sur le dossier `./public/`

### droits écritures
ouvrir les droits d'écriture pour l'utilisateur apache (www-data) sur les dossiers:
- `./var/log`
- `./var/cache`
- `./public/media`


### Fin

# Troubleshoot
### CORS:
Si problème de CORS Allow-Origin, configurer le paramêtre CORS_ALLOW_ORIGIN dans `.e
nv`
(pour plus de configuration voir fichier `./config/packages/nelmio_cors.yaml`)

# Commands
Pour ajouter un utilisateur :
`php bin/console create:user`
Pour change un mot de passe:
`php bin/console change:password`
Pour voir la liste des utilisateurs
`php bin/console print:user`

# altération des séquences (Changer la valeur)
```
alter sequence application_id_seq 	START with 1000000 ;
alter sequence media_id_seq		START with 1000000 ;
alter sequence fiche_suppl_id_seq 	START with 1000000 ;
alter sequence questions_id_seq 	START with 1000000 ;
alter sequence user_id_seq	 	START with 1000000 ;
```
