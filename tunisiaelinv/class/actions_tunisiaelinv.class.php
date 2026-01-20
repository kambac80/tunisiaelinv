<?php

class ActionsTunisiaelinv
{
    /**
     * Ajoute le bouton "Générer XML Tunisie" sur la fiche facture
     */
    public function addMoreActionsButtons($parameters, &$object, &$action, $hookmanager)
    {
        if ($parameters['currentcontext'] == 'invoicecard' && $object->id > 0) 
        {
            $url = $_SERVER["PHP_SELF"].'?id='.$object->id.'&action=generate_ttn_xml';
            
            echo '<div class="inline-block divButAction">';
            echo '<a class="butAction" href="'.$url.'"><span class="fa fa-file-code paddingrightonly"></span> Générer XML Tunisie</a>';
            echo '</div>';
        }
        return 0;
    }

    /**
     * Logique de génération du XML TEIF
     */
    public function doActions($parameters, &$object, &$action, $hookmanager)
    {
        if ($action == 'generate_ttn_xml') 
        {
            global $db, $conf, $mysoc;

            // 1. Chargement des données complètes
            $object->fetch($object->id);
            $object->fetch_thirdparty();

            // --- VÉRIFICATION DE CONFORMITÉ TUNISIE ---
            $error = 0;
            $msg_error = "";
            if (empty($mysoc->idprof1)) {
                $error++;
                $msg_error .= "Votre Matricule Fiscale est manquante (Configuration > Société). <br>";
            }
            if (empty($object->thirdparty->idprof1)) {
                $error++;
                $msg_error .= "La Matricule Fiscale du client est manquante sur sa fiche tiers. <br>";
            }
            if ($error > 0) {
                setEventMessages($msg_error, null, 'errors');
                $action = '';
                return 0;
            }

            // 2. Préparation du fichier
            $filename = "TEIF_" . $object->ref . ".xml";
            $dir = $conf->facture->dir_output . '/' . $object->ref;
            $file_path = $dir . '/' . $filename;

            // 3. Construction du contenu XML
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2">' . "\n";
            
            // Infos Facture
            $xml .= '  <cbc:ID>' . $object->ref . '</cbc:ID>' . "\n";
            $xml .= '  <cbc:IssueDate>' . dol_print_date($object->date, '%Y-%m-%d') . '</cbc:IssueDate>' . "\n";
            $xml .= '  <cbc:DocumentCurrencyCode>TND</cbc:DocumentCurrencyCode>' . "\n";

            // Infos Émetteur (Votre PME)
            $xml .= '  <cac:AccountingSupplierParty><cac:Party>' . "\n";
            $xml .= '    <cac:PartyTaxScheme><cbc:CompanyID>' . $mysoc->idprof1 . '</cbc:CompanyID></cac:PartyTaxScheme>' . "\n";
            $xml .= '    <cac:PartyName><cbc:Name>' . htmlspecialchars($mysoc->name) . '</cbc:Name></cac:PartyName>' . "\n";
            $xml .= '  </cac:Party></cac:AccountingSupplierParty>' . "\n";

            // Infos Client
            $xml .= '  <cac:AccountingCustomerParty><cac:Party>' . "\n";
            $xml .= '    <cac:PartyTaxScheme><cbc:CompanyID>' . $object->thirdparty->idprof1 . '</cbc:CompanyID></cac:PartyTaxScheme>' . "\n";
            $xml .= '    <cac:PartyName><cbc:Name>' . htmlspecialchars($object->thirdparty->name) . '</cbc:Name></cac:PartyName>' . "\n";
            $xml .= '  </cac:Party></cac:AccountingCustomerParty>' . "\n";

            // Bloc Taxes (TVA)
            $xml .= '  <cac:TaxTotal>' . "\n";
            $xml .= '    <cbc:TaxAmount currencyID="TND">' . $object->total_tva . '</cbc:TaxAmount>' . "\n";
            foreach($object->lines as $line) {
                if ($line->total_tva > 0) {
                    $xml .= '    <cac:TaxSubtotal>' . "\n";
                    $xml .= '      <cbc:TaxableAmount currencyID="TND">' . $line->total_ht . '</cbc:TaxableAmount>' . "\n";
                    $xml .= '      <cbc:TaxAmount currencyID="TND">' . $line->total_tva . '</cbc:TaxAmount>' . "\n";
                    $xml .= '      <cac:TaxCategory><cbc:Percent>' . $line->tva_tx . '</cbc:Percent></cac:TaxCategory>' . "\n";
                    $xml .= '    </cac:TaxSubtotal>' . "\n";
                }
            }
            $xml .= '  </cac:TaxTotal>' . "\n";

            // Bloc Totaux Légaux
            $xml .= '  <cac:LegalMonetaryTotal>' . "\n";
            $xml .= '    <cbc:LineExtensionAmount currencyID="TND">' . $object->total_ht . '</cbc:LineExtensionAmount>' . "\n";
            $xml .= '    <cbc:TaxExclusiveAmount currencyID="TND">' . $object->total_ht . '</cbc:TaxExclusiveAmount>' . "\n";
            $xml .= '    <cbc:TaxInclusiveAmount currencyID="TND">' . $object->total_ttc . '</cbc:TaxInclusiveAmount>' . "\n";
            $xml .= '    <cbc:PayableAmount currencyID="TND">' . $object->total_ttc . '</cbc:PayableAmount>' . "\n";
            $xml .= '  </cac:LegalMonetaryTotal>' . "\n";

            // Ajout du Timbre Fiscal (1.000 DT)
            $diff = $object->total_ttc - ($object->total_ht + $object->total_tva);
            if (round($diff, 2) >= 1.000) {
                $xml .= '  <cac:AllowanceCharge>' . "\n";
                $xml .= '    <cbc:ChargeIndicator>true</cbc:ChargeIndicator>' . "\n";
                $xml .= '    <cbc:Amount currencyID="TND">1.000</cbc:Amount>' . "\n";
                $xml .= '    <cbc:AllowanceChargeReason>Timbre Fiscal</cbc:AllowanceChargeReason>' . "\n";
                $xml .= '  </cac:AllowanceCharge>' . "\n";
            }

            $xml .= '</Invoice>';

            // 4. Écriture du fichier et Message de succès avec lien
            if (!file_exists($dir)) dol_mkdir($dir);
            $result = file_put_contents($file_path, $xml);

            if ($result !== false) {
                $download_url = DOL_URL_ROOT.'/document.php?modulepart=facture&file='.urlencode($object->ref.'/'.$filename);
                setEventMessages("Fichier XML généré avec succès. <a href='".$download_url."' target='_blank' style='font-weight:bold; text-decoration:underline;'>Cliquez ici pour télécharger le XML</a>", null, 'mesgs');
            } else {
                setEventMessages("Erreur lors de la création du fichier XML.", null, 'errors');
            }
            
            $action = ''; 
        }
        return 0;
    }
}