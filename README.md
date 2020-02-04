# CV-CENTER

> Programme version 1.1.0 - version php 7.4 - Ubuntu 18.04 - Navigateur Chrome.

### Installation
Installation des dépendances shell pour l'utilisation du projet: 
```shell
sudo apt-get install -y antiword
```


Installation de PHP 7.4 si une version php n'est pas déjà installé: 
```shell
sudo apt-get update
sudo apt -y install software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt-get update
sudo apt -y install php7.4
```

Installation de l'installateur de composant php.
Ne pas suivre le site officiel https://getcomposer.org/download  
l'installer de cette façon [download + install]: 
```shell
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === 'c5b9b6d368201a9db6f74e2611495f369991b72d9c8cbd3ffbc63edff210eb73d46ffbfce88669ad33695ef77dc76976') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
```

Ajouter les extensions php mbstring, zip, mysql et xml: 
```shell
sudo apt-get install php7.4-mbstring php7.4-zip php7.4-mysql php7.4-xml  
```

Téléchargement du projet dans le dossier de votre choix.
```shell
git clone https://github.com/3wrh-codeurh24/cv-center.git 
cd cv-center
```


installer les composants du dossier vendor 
```shell
composer update
```

Dans le dossier du projet ajouter le fichier config/mysql.php et configurer le. (voir exemple config/mysql.example.php)

Creer la table

```sql
CREATE TABLE `cv` (
  `id` int(11) 
    NOT NULL,
  `original_filename` varchar(255) COLLATE utf8mb4_unicode_ci 
    NOT NULL,
  `md5_file` varchar(255) COLLATE utf8mb4_unicode_ci 
    NOT NULL COMMENT 'signature numérique pour doublons',
  `content` text COLLATE utf8mb4_unicode_ci 
    NOT NULL COMMENT 'texte du cv',
  `size` int(11) 
    NOT NULL COMMENT 'taille en octet',
  `file_type` varchar(4) COLLATE utf8mb4_unicode_ci 
    NOT NULL COMMENT 'extension prévu pour lire ce fichier',
  `date_last_access` datetime 
    NOT NULL COMMENT 'dernier acces de consultation'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

Démarrer le petit serveur php: 
```shell
php -S 192.168.0.39:3950 -d display_errors=1 
```
Adapter l'ip et le port à votre convenance. Eviter d'afficher les erreurs si en démo (prod).

### Utilisation

Remplir le dossier cv par des fichiers cv de type doc, docx, pdf, odt.

Racine du projet 192.168.0.39:3950/  
Permet de mettre en base de données les cv

Dossier search 192.168.0.39:3950/search  
Permet de rechercher des mots parmis tout les cv mis en base de données.