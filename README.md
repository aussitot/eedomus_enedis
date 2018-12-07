[![GitHub release](https://img.shields.io/github/release/aussitot/eedomus_enedis.svg?style=flat-square)](https://github.com/aussitot/eedomus_netatmo_welcome/releases)
![GitHub license](https://img.shields.io/github/license/aussitot/eedomus_enedis.svg?style=flat-square)
![Status](https://img.shields.io/badge/Status-Complete-brightgreen.svg?style=flat-square)
[![Twitter](https://img.shields.io/badge/twitter-@havok-blue.svg?style=flat-square)](http://twitter.com/havok)
# eedomus_enedis
Scripts permettant de récupérer la consommation electrique sur le site enedis
script cree par twitter:@Havok pour la eedomus

NB : Script à installer sur un serveur web/php autre que l'eedomus elle-même

# INSTALLATION
Bonjour,

Voici un  script pour intégrer dans l'interface eedomus les consommations mesurées par un serveur linky depuis le site enedis.
La consommation n'est remontée QU'UNE fois par jour.

**Prérequis**
- Il faut disposer d'un serveur web/php autre que l'eedomus elle-même.  
- Il faut avoir un compteur linky (sinon ce ne sont que des estimations)
- Il faut avoir créer son compte client sur le site enedis.fr

**Ce que ca fait** : Ca va vous permettre de
- afficher la consommation en Kwh du J-1
- afficher le cumul (index) de la consommation en Kwh

**Ce qu'on va faire**
- On va créer un actionneur http afin de programmer les mises à jours
- On va créer un etat pour afficher la consommation (J-1)
- On va créer un état pour afficher le cumul de la consommation

## Etape 1
- Copiez les fichiers du projet dans le répertoire "enedis" sur votre serveur.

## Etape 2
Modifiez le fichier linkyEedomus.php pour y saisir vos login et password enedis ainsi que l'api user et api secret eedomus

```php
//--------------------------------------------- Paramètres enedis
$enedis_user = 'login enedis'; //votre login pour le site https://espace-client-particuliers.enedis.fr/group/espace-particuliers/accueil
$enedis_pass = 'password enedis'; //votre password pour le site https://espace-client-particuliers.enedis.fr/group/espace-particuliers/accueil
//--------------------------------------------- Paramètres eedomus
$api_user = 'wwwwwwwwww'; //api_user eedomus
$api_secret = 'zzzzzzzzzzzzzzzz'; //api_secret eedomus
```
## Etape 3
Créez 2 périphériques "état" dans l'eedomus Configuration/Ajouter ou supprimer un périphérique/Ajouter un autre type de périphérique/Etat
C'est eux qui vont stocker les données de consommation

- Etat 1 : Nom : Consommation (J-1), Usage : Compteur d'électricité, Unité : kWh, Type de données : Nombre décimal
- Etat 2 : Nom : Consommation (cumul), Usage : Compteur d'électricité, Unité : kWh, Type de données : Nombre décimal

Récupérez les valeurs du code API des 2 états générés automatiquement par eedomus.
Modifiez le fichier linkyEedomus.php pour y reporter ces valeurs (en ne confondant pas l'access_token et le refresh_token).
```PHP
//------- Etats de sauvegarde
$capteurCumul = '0123456'; //code api eedomus de l etat cumul
$capteurConso = '7890123'; //code api eedomus de l etat conso
```
## Etape 4
On va maintenant automatiser tout ca.
Dans eedomus créez un actionneur http :
- Nom : Consommation (maj Enedis)

Avec une valeur :
- Valeur brute : 0
- Description : maj
- URL : http://votreserveur.com/enedis/linkyEedomus.php
- Type : GET

et enfin une règle :
- Tous les jours à 6h et 0m -> Consommation (maj Enedis) Maj

## Version history

#### v0.1 (2018-02-25)
- Première version !

## COPYRIGHT
  Librairie php Linky : https://github.com/KiboOst/php-LinkyAPI

## License

The MIT License (MIT)

Copyright (c) 2018 KiboOst

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
