# Drop Market

🐘 PHP 8.2 . 🎼 Symfony 7.1 . 🗃️ Mysql 8.0 . 🛒 Caddy (latest) . 📡 Dunglas\Mercure . 🔎 PhpMyAdmin . 📫 MailDev

## Présentation

J'ai créé ce projet pour travailler mes compétences. Ce projet prend la forme d'un e-commerce des plus simples. L'objectif n'étant pas d'aboutir à un produit fini, chaque fonctionnalité implantée est complète, mais certaines fonctionnalités incontournables d'un e-commerce sont consciemment absentes.

### Objectifs

**Symfony 7** : Adepte de ce framework, j'ai appliqué une organisation visant à mettre en œuvre les principes SOLID et à faciliter la maintenabilité ainsi que l'évolutivité du projet.

**Docker** : Adepte de Docker, j'ai choisi d'intégrer différentes images pour créer un environnement de développement prévisible et autonome.

**Symfony Turbo et Twig** : Utilisation de Turbo Drive ainsi que de Turbo Stream couplé à Mercure pour une interface en temps réel. J'ai choisi l'approche Atomic Design pour l'architecture des interfaces.

**Caddy** : Adepte d'Apache et ayant déjà utilisé Nginx, j'ai souhaité essayer ce serveur web, qui gagne en popularité et offre des facilités avec Mercure.

**Dunglas\Mercure** : J'étais curieux de créer une application en temps réel afin de comprendre les tenants et aboutissants d'un tel système. Mercure et son système de SSE sont une superbe découverte que je souhaiterais remettre en pratique.

**Robustesse** : Soucieux de la robustesse de mon code, j'ai créé des plans de tests unitaires et E2E. J'ai ponctuellement adopté une approche TDD.

**Qualité** : Soucieux de la qualité de mon code, je vérifie ce dernier à chaque commit à l'aide de GrumPHP, PHPCS et PHPStan.

**Automatisation** : Adepte de l'automatisation et de l'optimisation, j'ai créé différents scripts shell pour faciliter l'exécution des plans de tests et la gestion des bases de données.

## Installer l'environnement

```bash
docker-compose up --build --remove-orphans
```

### Services disponibles

- http://localhost:8080 - Projet Symfony
- http://localhost:8081 - PhpMyAdmin
- http://localhost:8082 - Mercure
- http://localhost:1080 - MailDev

## Démarrer le projet

Une fois l'environnement Docker installé, vous devez initialiser le projet Symfony (uniquement à la première installation).

Les commandes suivantes s'exécutent dans le conteneur PHP contenant le projet. Elles utilisent des scripts pour réinitialiser les bases de données (voir la section sur les commandes).

```bash
docker exec symfony composer db_dev_reset
docker exec symfony composer db_test_reset
```

Des utilisateurs préconfigurés sont disponibles. Consultez la section sur les utilisateurs pour plus de détails.

## Conteneurs

Les conteneurs suivants sont configurés pour assurer le fonctionnement de l'application :

- MySQL 8.0
- PhpMyAdmin
- Caddy (latest)
- Dunglas\Mercure
- PHP 8.2
- MailDev

Vous pouvez exécuter les commandes mentionnées dans la section sur les commandes directement depuis l'extérieur du conteneur ou en accédant au conteneur PHP.

```bash
docker exec symfony <votre_commande>      # Exécution depuis l'extérieur
docker exec -it symfony bash              # Accéder au conteneur PHP
docker exec -it database bash             # Accéder au conteneur MySQL
```

## Environnements

**Développement**

- Base de données MySQL 8 administrée par Doctrine.
- Fixtures dédiées au développement.

**Test**

- Base de données SQLite administrée par Doctrine.
- Fixtures dédiées aux tests.
- Tests E2E : utilisent le serveur de test fourni par Panther pour simuler un navigateur.

**Erreurs connues**
Lors de l'exécution d'un grand nombre de tests E2E, il peut arriver que le serveur Panther ne se ferme pas correctement, ce qui rend le port 9080 indisponible ou dysfonctionnel. Cela peut provoquer des erreurs lors des tests.

Pour éviter ces problèmes, le processus lié au serveur Panther sur le port 9080 est systématiquement arrêté avant chaque commande de test. Une commande dédiée est disponible à cet effet.

## Commandes

Les commandes suivantes doivent être exécutées dans le conteneur PHP. Consultez la section sur les conteneurs pour plus de détails.

| Commande                   | Description                                                                             | Env        |
| -------------------------- | --------------------------------------------------------------------------------------- | ---------- |
| composer db_dev_reset      | Réinitialise la base de données de développement.                                       | dev        |
| composer db_test_reset     | Réinitialise la base de données de test.                                                | test       |
| composer test_unique       | Propose une sélection de tests spécifiques à exécuter (depuis le container uniquement). | test       |
| composer test_full         | Exécute les tests unitaires et fonctionnels, et génère la couverture.                   | test       |
| composer test_reset_server | Arrête les processus du serveur de test Panther sur le port 9080.                       | test       |
| composer caches_clear      | Vide le cache des environnements de développement et de test.                           | dev & test |

## Utilisateurs

Les utilisateurs sont générés par les fixtures dans la base de données de développement.

| Email               | Mot de passe | Rôle             |
| ------------------- | ------------ | ---------------- |
| user@wgls.fr        | 123456       | ROLE_USER        |
| employee@wgls.fr    | 123456       | ROLE_EMPLOYEE    |
| owner@wgls.fr       | 123456       | ROLE_OWNER       |
| admin@wgls.fr       | 123456       | ROLE_ADMIN       |
| super_admin@wgls.fr | 123456       | ROLE_SUPER_ADMIN |
