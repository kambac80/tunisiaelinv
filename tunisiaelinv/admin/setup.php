<?php
// 1. Chargement de l'environnement Dolibarr (Chemin sécurisé)
if (!defined('NOCSRFCHECK')) define('NOCSRFCHECK', 1);
if (!defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL', 1);

// On remonte de deux niveaux pour atteindre le dossier racine de Dolibarr
$res = 0;
if (!$res && file_exists("../../main.inc.php")) $res = @include "../../main.inc.php";
if (!$res && file_exists("../../../main.inc.php")) $res = @include "../../../main.inc.php";

require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';

// 2. Sécurité
if (!$user->admin) accessforbidden();

// 3. Header
llxHeader('', "Configuration Tunisie e-Invoice");

// 4. Contenu
print load_fiche_titre("Configuration & Aide - Facture Électronique Tunisie");

print '<div class="info" style="background-color: #e7f3fe; border-left: 6px solid #2196F3; padding: 15px; margin: 10px 0;">';
print "<strong>Procédure de mise en conformité (Tunisie) :</strong><br><br>";
print "1. <strong>Génération :</strong> Cliquez sur le bouton 'Générer XML Tunisie' sur votre facture.<br>";
print "2. <strong>Téléchargement :</strong> Enregistrez le fichier XML sur votre PC.<br>";
print "3. <strong>Signature :</strong> Utilisez votre certificat <strong>TunTrust</strong> et l'utilitaire de la <strong>TTN</strong> pour signer le fichier.<br>";
print "4. <strong>Envoi :</strong> Déposez le fichier signé sur le portail <strong>El Fatoora</strong>.";
print '</div>';

// ... après le bloc print '</div>';

print '<br><div class="center" style="margin-top: 30px; border-top: 1px solid #ddd; padding-top: 20px;">';
print '<p style="color: #666; font-size: 0.9em;">';
print "<strong>Module de Facturation Électronique (Tunisie)</strong><br>";
print "Conformité TEIF / UBL 2.1 - TVA & Timbre Fiscal<br><br>";
print "Développé avec passion par : <strong>Kamel BACCOURI</strong><br>";

// Le lien WhatsApp
$whatsapp_url = "https://wa.me/21650233333?text=".urlencode("Bonjour, j'utilise votre module XML Tunisie et j'aimerais avoir des informations.");

print '<a href="'.$whatsapp_url.'" target="_blank" style="display: inline-block; margin-top: 10px; padding: 8px 15px; background-color: #25D366; color: white; border-radius: 5px; text-decoration: none; font-weight: bold;">';
print '<i class="fa fa-whatsapp"></i> Contactez-moi sur WhatsApp';
print '</a>';

print "</p>";
print '</div>';

llxFooter();
$db->close();