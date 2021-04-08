# Projet OpenclassRooms : Créez un web service exposant une API

## Description du projet

Dans le cadre de la formation Développeur d'application - PHP / Symfony d'OpenClassRooms, voici le projet n°7 : Créez un web service exposant une API.

## Contexte
BileMo est une entreprise offrant toute une sélection de téléphones mobiles haut de gamme.

Vous êtes en charge du développement de la vitrine de téléphones mobiles de l’entreprise BileMo. 
Le business modèle de BileMo n’est pas de vendre directement ses produits sur le site web, mais de fournir à toutes les plateformes qui le souhaitent l’accès au catalogue via une API (Application Programming Interface).
Il s’agit donc de vente exclusivement en B2B (business to business).

Il va falloir que vous exposiez un certain nombre d’API pour que les applications des autres plateformes web puissent effectuer des opérations.

## Besoin client
Le premier client a enfin signé un contrat de partenariat avec BileMo ! 
C’est le branle-bas de combat pour répondre aux besoins de ce premier client qui va permettre de mettre en place l’ensemble des API et de les éprouver tout de suite.

Après une réunion dense avec le client, il a été identifié un certain nombre d’informations. Il doit être possible de :

- consulter la liste des produits BileMo ;
- consulter les détails d’un produit BileMo ;
- consulter la liste des utilisateurs inscrits liés à un client sur le site web ;
- consulter le détail d’un utilisateur inscrit lié à un client ;
- ajouter un nouvel utilisateur lié à un client ;
- supprimer un utilisateur ajouté par un client.

Seuls les clients référencés peuvent accéder aux API. 
Les clients de l’API doivent être authentifiés via OAuth ou JWT.

## Compétences évaluées

- Lancer une authentification à chaque requête HTTP
- Exposer une API REST avec Symfony
- Suivre la qualité d’un projet
- Produire une documentation technique
- Analyser et optimiser les performances d’une application
- Concevoir une architecture efficace et adaptée

## Pour commencer

### Prérequis

- Php 7.4
- the openssl extension
- Composer 2.0
- Une base de données mySQL 5.7
- Git

### Installation

- Cloner le projet en local
- Exécuter la commande composer :
```bash
composer install
```
- Intégrer les données de démo : Exécuter la commande composer : 
```bash
composer run-script prepare-db --dev
```
- Identifiants de test :
pseudo : BestOfTel 
mot de passe : passpass

- \doc pour accéder à la documentation

### Paramétrage

Modifier les informations de connexion dans un fichier /.env.local à mettre à la racine du projet.
- Base de données : doctrine/doctrine-bundle
- JWT pass : lexik/jwt-authentication-bundle

Configuer lexik/jwt-authentication-bundle :
- https://github.com/lexik/LexikJWTAuthenticationBundle/blob/master/Resources/doc/index.md#configuration
- Enregistrer les clés SSL dans un dossier /jwt/config

## Fabriqué avec

* Visual Studio Code
* PHP Sniffer
* PHP Intelephense
* PHP MessDetector
* MAMP
* Symnfony 5.2
* Doctrine
* LexikJWTAuthenticationBundle
* willdurand/hateoas-bundle
* nelmio/api-doc-bundle


## Versions
- V1.0.0 : First Version


## CodeClimate
<a href="https://codeclimate.com/github/Toasted201/OpenClassroom_P07/maintainability"><img src="https://api.codeclimate.com/v1/badges/f727388fb710c5a579ce/maintainability" /></a>

