
# GSB-Frais

Gestion des frais professionnels pour les visiteurs médicaux de GSB (Galaxy Swiss Bourdin).

## 📚 Présentation

GSB-Frais est une application web réalisée avec **Symfony** permettant aux visiteurs médicaux de saisir leurs frais professionnels mensuellement (repas, déplacements, hébergements, etc.).  
Elle permet également à un comptable de valider les fiches de frais soumises par les visiteurs.

Projet développé dans le cadre du **BTS SIO SLAM** — Épreuve E6.

## 🚀 Fonctionnalités principales

- Création d'une fiche de frais pour un mois donné
- Ajout de frais forfaitaires (repas, nuits, kilomètres…)
- Ajout de frais hors forfait (autres dépenses professionnelles)
- Modification et suppression des frais
- Validation des fiches par un comptable
- Consultation de l'historique des fiches de frais
- Authentification sécurisée des utilisateurs

## 🛠️ Technologies utilisées

- PHP 8+
- Symfony 6
- Doctrine ORM
- Twig (moteur de templates)
- MySQL (base de données)
- Bootstrap (mise en forme responsive)
- Composer (gestion des dépendances)

## 📦 Installation

1. **Cloner le dépôt GitHub**  
   ```bash
   git clone https://github.com/votre-utilisateur/gsb-frais.git
   cd gsb-frais
   ```

2. **Installer les dépendances PHP**  
   ```bash
   composer install
   ```

3. **Configurer l'environnement**  
   Copier `.env` en `.env.local` et modifier les informations de connexion à la base de données :  
   ```bash
   cp .env .env.local
   # Editer DATABASE_URL dans .env.local
   ```

4. **Créer la base de données**  
   ```bash
   php bin/console doctrine:database:create
   ```

5. **Exécuter les migrations**  
   ```bash
   php bin/console doctrine:migrations:migrate
   ```

6. **(Optionnel) Charger des données de test**  
   ```bash
   php bin/console doctrine:fixtures:load
   ```

7. **Démarrer le serveur de développement**  
   ```bash
   symfony server:start
   ```

8. **Accéder à l'application**  
   Ouvrir [http://127.0.0.1:8000](http://127.0.0.1:8000) dans votre navigateur.

## 🔒 Accès et rôles

- **Visiteur médical** : saisie et gestion de ses propres frais.
- **Comptable** : validation et gestion globale des frais.

Les utilisateurs et leurs rôles sont configurables directement en base de données ou via des fixtures.

## 📂 Structure du projet

- `src/Controller/` — Contrôleurs (logique des pages)
- `src/Entity/` — Entités (modèle de données)
- `src/Repository/` — Requêtes personnalisées sur les entités
- `templates/` — Vues HTML avec Twig
- `config/` — Configuration de Symfony
- `public/` — Racine publique du site
- `migrations/` — Fichiers de migration Doctrine

## 🎯 Objectifs pédagogiques

- Développer une application web sécurisée avec Symfony
- Maîtriser la persistance des données avec Doctrine ORM
- Mettre en place des contrôleurs, formulaires et validations
- Gérer les droits utilisateurs et la sécurité d'accès
- Structurer un projet de manière professionnelle

## 📄 Licence

Ce projet est réalisé dans un but éducatif.  
© 2025 — Projet BTS SIO — Développement par [Ton Nom].
