<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 *
 * @package share
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

$arrLang = &$GLOBALS['TL_LANG']['tl_module'];

/**
 * Fields
 */
$arrLang['addShare'][0] = 'Syndikationen aktivieren';
$arrLang['addShare'][1] = 'Hier legen Sie fest, ob der Beitrag gedruckt werden kann, als PDF heruntergeladen, oder in Sozialen Netzwerken geteilt werden kann.';

$arrLang['share_buttons'][0] = 'Syndikaktionen';
$arrLang['share_buttons'][1] = 'Bitte wählen Sie die Syndikationen aus.';

$arrLang['share_pdfRenderer'][0] = 'PDF-Renderer auswählen';
$arrLang['share_pdfRenderer'][1] = 'Bitte wählen Sie eine Bibliothek zur Erzeugung der PDF-Datei aus.';

$arrLang['share_pdfShowInline'][0] = 'PDF-Inline darstellen';
$arrLang['share_pdfShowInline'][1] = 'Wenn Sie diese Option auswählen, wird die PDF direkt im Browser angezeigt, ansonsten wird ein Download gestartet.';

$arrLang['share_pdfLogoSRC'][0] = 'PDF-Logo Quelldatei';
$arrLang['share_pdfLogoSRC'][1] = 'Bitte wählen Sie eine Datei aus der Dateiübersicht aus.';

$arrLang['share_pdfCssSRC'][0] = 'PDF-CSS Quelldatei';
$arrLang['share_pdfCssSRC'][1] = 'Bitte wählen Sie eine Css-Datei aus der Dateiübersicht aus.';

$arrLang['share_pdfFontSRC'][0] = 'PDF-Schriften Quelldateien';
$arrLang['share_pdfFontSRC'][1] = 'Bitte wählen Sie Dateien aus der Dateiübersicht aus.';

$arrLang['share_pdfFontSize'][0] = 'PDF-Schriftgröße';
$arrLang['share_pdfFontSize'][1] = 'Bitte geben Sie die Standard Schriftgröße als Ganzzahl ein.';

$arrLang['share_pdfLogoSize'][0] = 'PDF-Logo Breite/Höhe';
$arrLang['share_pdfLogoSize'][1] = 'Hier können Sie die Breite und Höhe des PDF-Logo angeben.';

$arrLang['share_pdfFooterText'][0] = 'Fußzeile';
$arrLang['share_pdfFooterText'][1] = 'Hier können Sie einen Text für die Fußzeile angeben.';

$arrLang['share_customPrintTpl'][0] = 'Druck- & PDF-Template auswählen';
$arrLang['share_customPrintTpl'][1] = 'Wählen Sie ein Template für den Druck und die PDF-Generierung aus, in das der Inhalt des aktuellen Moduls ausgegeben wird.';

$arrLang['share_pdfUsername'][0] = 'Authentifizierungsbenutzername';
$arrLang['share_pdfUsername'][1] = 'Falls Sie Daten (Bilder,CSS) von einer Seite laden wollen, welche mit einen Passwort (HTTP Basic Authentication) geschützt ist, können Sie hier die Benutzerdaten eintragen.';

$arrLang['share_pdfPassword'][0] = 'Authentifizierungspassword';
$arrLang['share_pdfPassword'][1] = 'Siehe Authentifizierungsbenutzername.';

$arrLang['share_mailtoSubject'][0] = 'Betreff (mailto)';
$arrLang['share_mailtoSubject'][1] = 'Geben Sie hier den Betreff ein, der dem mailto-Link hinzugefügt werden soll. Wenn Sie nichts eingeben, wird der Titel der geteilten Entität genutzt. Sie können im Text mit "%s" auch den Titel der geteilten Entität referenzieren.';

$arrLang['share_feedbackEmail'][0] = 'E-Mail-Adresse (Feedback)';
$arrLang['share_feedbackEmail'][1] = 'Geben Sie hier die E-Mail-Adresse ein, an die das Feedback geschickt werden soll.';

$arrLang['share_feedbackSubject'][0] = 'Betreff (Feedback)';
$arrLang['share_feedbackSubject'][1] = 'Geben Sie hier den Betreff ein, der dem Feedback-mailto-Link hinzugefügt werden soll. Wenn Sie nichts eingeben, wird der Titel der geteilten Entität genutzt. Sie können im Text mit "%s" auch den Titel der geteilten Entität referenzieren.';

$arrLang['share_addTemplateLinks'][0] = 'Share Url zum Template hinzufügen';
$arrLang['share_addTemplateLinks'][1] = 'Fügt eine zusätzliche Template-Variable zum Template hinzu, welche die Share Urls enthält.';

/**
 * Legends
 */
$arrLang['share_legend'] = 'Share-Einstellungen';

/**
 * References
 */
$arrLang['references']['share_buttons']['pdfButton']            = 'Beitrag als PDF';
$arrLang['references']['share_buttons']['printButton']          = 'Beitrag Drucken';
$arrLang['references']['share_buttons']['printWithoutTemplate'] = 'Beitrag Drucken ohne Template';
$arrLang['references']['share_buttons']['facebook']             = 'Auf Facebook teilen';
$arrLang['references']['share_buttons']['twitter']              = 'Auf Twitter teilen';
$arrLang['references']['share_buttons']['linkedin']             = 'Auf LinkedIn teilen';
$arrLang['references']['share_buttons']['whatsapp']             = 'Auf WhatsApp teilen';
$arrLang['references']['share_buttons']['mailto']               = 'Per E-Mail teilen';
$arrLang['references']['share_buttons']['feedback']             = 'Feedback senden';