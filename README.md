![](public/images/IDTypTerres.png)

# Description du projet

La finalité du projet est d’améliorer l’intégration des données sols dans le conseil en agronomie, en favorisant l’accès à des données sol adaptées et harmonisées (les Typterres) pour répondre aux besoins des outils et de leurs utilisateurs. Il fait notamment appel à des clés de détermination pour permettre l'identification des sols observés sur le terrain par rapport à un référentiel de sols IDTypterre.

Le projet est décrit plus en détail ici https://sols-et-territoires.org/projets/idtypterres

L'Application IDTypterre est une application qui permet de saisir les clés de détermination et de les publier via une API et une applicaiton embarquée.

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


```
