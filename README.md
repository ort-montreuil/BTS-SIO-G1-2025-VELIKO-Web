# Projet Veliko
## Installation
### Etape 1 : Cloner le projet
```bash
git clone git@github.com:ort-montreuil/BTS-SIO-G1-2025-VELIKO-Web.git
```
### Etape 2: Installation des dependances
Installation des dependances avec composer (vendor)
```
composer install
```
### Etape 3: Comfiguration du fichier .env
Créer un fichier .env à la racine du projet et renommer le en .env puis modifier la ligne 3 "!ChangeMe!" remplacer par votre id et votre mot de passe de votre base de donnée.
### Etape 4: Création de la base de donnée
````
docker compose up -d
````
### Etape 5: Migration de la base de donnée
````
php bin/console doctrine:migrations:migrate
````

### Etape 6: Mettre les stations dans la base de donnée
````
php bin/console app:fetch-stations  
````
### Etape 7: Lancer le serveur
````
symfony serve:start
````
### Etape 8: Avoir un admin pour accéder à la partie admin
````
symfony console d:f:l
````
#### Lien vers le notion du projet : https://reinvented-entrance-efd.notion.site/PROJET-VELIKO-139d0db2706880c4b25bf548397991ba?pvs=4