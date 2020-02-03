# CV-CENTER

> Programme version 1.0.0 - version php 7.4 - Ubuntu 18.04 - Navigateur Chrome.

### Installation
Installation des dépendances shell: 
```shell
sudo apt-get install -y antiword
```

Installation de l'installateur php https://getcomposer.org/download  
Télécharger seulement composer-setup.php pour ensuite l'installer de cette façon: 
```shell
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
```

Ajouter les extensions php mbstring, zip et xml: 
```shell
sudo apt-get install php7.4-mbstring php7.4-xml php7.4-zip
```

installer les composants du dossier vendor 
```shell
composer update
```

Dans le dossier du projet ajouter le fichier config/mysql.php et configurer le. (voir exemple config/mysql.example.php)

Creer la table

```sql
CREATE TABLE cv 
( id int(11) 
    NOT NULL, 
  original_filename varchar(255) CHARACTER SET latin1 
    NOT NULL,
  md5 varchar(255) CHARACTER SET latin1 
    NOT NULL COMMENT 'signature numérique pour doublons',
  content text CHARACTER SET latin1 
    NOT NULL COMMENT 'texte du cv',  
  size int(11) 
    NOT NULL COMMENT 'taille en octet',  
  producer varchar(255) CHARACTER SET latin1 
    NOT NULL COMMENT 'logiciel qui produit le document',  
  keywords varchar(255) CHARACTER SET latin1 
    NOT NULL COMMENT 'match, mot clé',  
  date_last_access datetime 
    NOT NULL COMMENT 'dernier acces de consultation' 
) 
    
ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

Démarrer le petit serveur php: 
```shell
php -S 192.168.0.39:3950 -d display_errors=1 
```
Adapter l'ip et le port à votre convenance. Eviter d'afficher les erreurs si en démo (prod).

### Utilisation
Racine du projet 192.168.0.39:3950/  
Permet de mettre en base de données les cv

Dossier search 192.168.0.39:3950/search  
Permet de rechercher des mots parmis tout les cv mis en base de données.