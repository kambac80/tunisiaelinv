# 🇹🇳 Dolibarr Tunisia e-Invoice (TEIF)

Ce module gratuit pour Dolibarr permet de générer des factures électroniques au format XML conformes aux spécifications techniques de la **TTN (Tunisie TradeNet)** et du standard **UBL 2.1 / TEIF**.

## 🚀 Fonctionnalités principales
- **Génération XML** : Export en un clic depuis la fiche facture Dolibarr.
- **Conformité Fiscale** : Gestion automatique de la TVA (19%) et du Timbre Fiscal (1.000 DT).
- **Contrôle de sécurité** : Vérification de la présence de la Matricule Fiscale (émetteur et client) avant génération.
- **Nommage Standard** : Les fichiers sont nommés selon le format `TEIF_MatriculeFiscale_RefFacture.xml`.

## 🛠 Installation
1. Téléchargez le fichier `.zip` depuis la section [Releases](https://github.com/kambac80/tunisiaelinv/releases).
2. Décompressez-le dans le dossier `custom/` de votre installation Dolibarr.
3. Allez dans **Configuration > Modules** et activez le module **Tunisiaelinv**.

## 🔐 Signature et Envoi (Procédure TTN)
**Important :** Ce module génère le fichier technique, mais il ne le signe pas automatiquement. Pour être légal en Tunisie, vous devez :
1. Télécharger le XML généré.
2. Le signer numériquement avec votre certificat **TunTrust** (Digigo ou clé USB).
3. Utiliser l'utilitaire de la **TTN** pour finaliser la signature.
4. Soumettre le fichier signé sur le portail **El Fatoora**.

## 🤝 Support & Communauté
Ce module est **Open Source** et gratuit. Si vous souhaitez contribuer à son amélioration ou si vous avez besoin d'aide pour l'installation :

[![WhatsApp](https://img.shields.io/badge/WhatsApp-25D366?style=for-the-badge&logo=whatsapp&logoColor=white)](https://wa.me/21650233333?text=Bonjour%2C%20j%27utilise%20votre%20module%20Dolibarr%20Tunisie%20et%20j%27aimerais%20avoir%20des%20informations.)

**Développé par : Kamel BACCOURI**
*Soutenez la digitalisation des entreprises tunisiennes !*

---
Licence : GNU GPL v3.0
