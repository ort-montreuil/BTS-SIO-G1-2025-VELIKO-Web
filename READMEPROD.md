# Projet veliko pour les SISR
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

### Etape 3: Configuration du fichier .env en mode prod
Renommer le fichier .env.exemple en .env et le mettre en mode porduction
```
APP_ENV=prod
```
Et retirer la ligne :
```
APP_ENV=dev
```
### Etape 4: Création de la base de donnée
Migrer la base de donnée.
### Etape 5: Vider le cache
````
php bin/console cache:clear
````
```
php bin/console cache:warmup
```