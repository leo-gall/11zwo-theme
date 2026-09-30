<?php
/**
 * Fest im Code hinterlegte Einsatzstichwörter nach der Alarmierungs-
 * bekanntmachung (215-I-2279-A001, Anlage). Jeder zuweisbare Begriff heißt
 * "Stichwort Kategorie Schlagwort" (leere Teile entfallen), z. B.
 * "RD HILFE / SONSTIGE Sonstige Lotsenfahrt durch RD-Fahrzeuge"; das
 * Stichwort selbst ist die übergeordnete Gruppe.
 *
 * Die Taxonomie "einsatzstichwort" bleibt als Speicher (Zuordnung zum
 * Einsatz, Admin-Spalte, Archiv-URLs) erhalten, lässt sich im Backend aber
 * nicht mehr bearbeiten (siehe elfzwo_register_taxonomies()). Änderungen
 * erfolgen ausschließlich hier; elfzwo_sync_einsatzstichwoerter() gleicht
 * die Begriffe in der Datenbank beim nächsten Seitenaufruf an.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Stichwort => Liste aus array( Kategorie, Schlagwort ). */
function elfzwo_einsatzstichwoerter() {
	return array(
		'B 1' => array(
			array( 'im Freien', 'Brandgeruch' ),
			array( 'im Freien', 'Rauchentwicklung' ),
			array( 'im Freien', 'Freifläche klein (< 100 m²)' ),
			array( 'im Freien', 'Abfall-, Müll-, Papiercontainer' ),
			array( 'im Freien', 'Kleinbrand' ),
			array( 'im Gebäude', 'Nachschau' ),
			array( 'Verkehr', 'Motorrad' ),
		),
		'B 2' => array(
			array( 'im Freien', 'Wald, klein (< 1000 m²)' ),
			array( 'im Freien', 'Freifläche groß (> 100 m²)' ),
			array( 'im Freien', 'Bahndamm' ),
			array( 'im Freien', 'Bau-, Wohncontainer' ),
			array( 'im Freien', 'Gartenhütte, Schuppen' ),
			array( 'im Gebäude', 'Kamin' ),
			array( 'im Gebäude', 'überhitzter Ofen / Ölofen' ),
			array( 'Verkehr', 'PKW' ),
			array( 'Verkehr', 'PKW auf BAB' ),
			array( 'Verkehr', 'LKW / Bus innerorts' ),
			array( 'Alarmstufenerhöhung', 'auf B 2' ),
		),
		'B 2 PERSON' => array(
			array( 'im Freien', 'Bau-, Wohncontainer (Person in Gefahr)' ),
			array( 'im Freien', 'Gartenhütte / Schuppen (Person in Gefahr)' ),
			array( 'im Freien', 'Person' ),
			array( 'Verkehr', 'PKW (Person in Gefahr)' ),
			array( 'Verkehr', 'PKW auf BAB (Person in Gefahr)' ),
			array( 'Alarmstufenerhöhung', 'auf B 2 Person' ),
		),
		'B 3' => array(
			array( 'im Freien', 'am Gebäude' ),
			array( 'im Gebäude', 'Brandgeruch' ),
			array( 'im Gebäude', 'Dachstuhl' ),
			array( 'im Gebäude', 'Dehnfuge' ),
			array( 'im Gebäude', 'Garage' ),
			array( 'im Gebäude', 'Keller' ),
			array( 'im Gebäude', 'Rauchentwicklung' ),
			array( 'im Gebäude', 'Zimmer' ),
			array( 'im Gebäude', 'Berghütte' ),
			array( 'Landwirtschaft', 'Fahrzeug / Maschine' ),
			array( 'Verkehr', 'LKW / Bus außerorts' ),
			array( 'Verkehr', 'LKW / Bus auf BAB' ),
			array( 'Alarmstufenerhöhung', 'auf B 3' ),
			array( 'im Gebäude', 'überhitzter Heustock' ),
		),
		'B 3 PERSON' => array(
			array( 'im Gebäude', 'Dachstuhl (Person in Gefahr)' ),
			array( 'im Gebäude', 'Garage (Person in Gefahr)' ),
			array( 'im Gebäude', 'Keller (Person in Gefahr)' ),
			array( 'im Gebäude', 'Rauchentwicklung (Person in Gefahr)' ),
			array( 'im Gebäude', 'Zimmer (Person in Gefahr)' ),
			array( 'Verkehr', 'LKW (Person in Gefahr)' ),
			array( 'Verkehr', 'LKW auf BAB (Person in Gefahr)' ),
			array( 'Alarmstufenerhöhung', 'auf B 3 Person' ),
		),
		'B 4' => array(
			array( 'im Gebäude', 'ausgedehnt / hoch bis 6. OG' ),
			array( 'im Gebäude', 'Tiefgarage' ),
			array( 'Gebäude hohe Personenzahl', 'Wohnheim' ),
			array( 'Gebäude hohe Personenzahl', 'Behinderteneinrichtung' ),
			array( 'Gebäude hohe Personenzahl', 'Hochhaus ab 7. OG' ),
			array( 'Gebäude hohe Personenzahl', 'Supermarkt' ),
			array( 'Gebäude hohe Personenzahl', 'Kindergarten' ),
			array( 'Gebäude hohe Personenzahl', 'Kino' ),
			array( 'Gebäude hohe Personenzahl', 'Kirche' ),
			array( 'Gebäude hohe Personenzahl', 'Schule' ),
			array( 'Gebäude hohe Personenzahl', 'Theater' ),
			array( 'Gebäude hohe Personenzahl', 'Zirkus' ),
			array( 'Gebäude hohe Personenzahl', 'Hotel' ),
			array( 'Gewerbe / Industrie', 'Sägewerk / Schreinerei' ),
			array( 'Gewerbe / Industrie', 'Lagerhalle' ),
			array( 'Gewerbe / Industrie', 'Silo (kein Gefahrstoff)' ),
			array( 'Gewerbe / Industrie', 'große Höhe – Turm' ),
			array( 'Gewerbe / Industrie', 'große Höhe – Windrad' ),
			array( 'Gewerbe / Industrie', 'Industriegebäude' ),
			array( 'Landwirtschaft', 'Bauernhof' ),
			array( 'Landwirtschaft', 'Stall / Scheune' ),
			array( 'Landwirtschaft', 'Aussiedlerhof' ),
			array( 'Alarmstufenerhöhung', 'auf B 4' ),
		),
		'B 5' => array(
			array( 'Gebäude hohe Personenzahl', 'Pflege-/Altenheim' ),
			array( 'Gebäude hohe Personenzahl', 'Kaufhaus' ),
			array( 'Gebäude hohe Personenzahl', 'Krankenhaus' ),
			array( 'Alarmstufenerhöhung', 'auf B 5' ),
		),
		'B 6' => array(
			array( 'Alarmstufenerhöhung', 'auf B 6' ),
		),
		'B 7' => array(
			array( 'Alarmstufenerhöhung', 'auf B 7' ),
		),
		'B 8' => array(
			array( 'Alarmstufenerhöhung', 'auf B 8' ),
		),
		'B BMA' => array(
			array( 'Meldeanlage', 'Brandmeldeanlage' ),
			array( 'Meldeanlage', 'Rauchwarnmelder über Hausnotruf' ),
			array( 'Meldeanlage', 'Rauchwarnmelder' ),
		),
		'B BOOT' => array(
			array( 'Verkehr', 'Boot / Yacht / Floß' ),
		),
		'B ELEKTROANLAGE' => array(
			array( 'Gewerbe / Industrie', 'Elektroanlage / Trafo' ),
		),
		'B SCHIENENTUNNEL' => array(
			array( 'Verkehr', 'Zug im Tunnel' ),
			array( 'Verkehr', 'S-Bahn im Tunnel' ),
			array( 'Verkehr', 'U-Bahn im Tunnel' ),
		),
		'B SCHIFF' => array(
			array( 'Verkehr', 'Passagierschiff' ),
			array( 'Verkehr', 'Frachtschiff' ),
		),
		'B STRAßENTUNNEL' => array(
			array( 'Verkehr', 'Tunnel' ),
		),
		'B WALD' => array(
			array( 'im Freien', 'Wald groß (> 1000 m²)' ),
			array( 'im Freien', 'Bergwald' ),
		),
		'B ZUG' => array(
			array( 'Verkehr', 'Personenzug' ),
			array( 'Verkehr', 'Güterzug' ),
			array( 'Verkehr', 'Zug nur Lokomotive' ),
			array( 'Verkehr', 'Straßenbahn' ),
			array( 'Verkehr', 'U-Bahn im Freien' ),
			array( 'Verkehr', 'S-Bahn im Freien' ),
		),
		'THL AMOK FW' => array(
			array( 'Bombe / Amok', 'Amoklage' ),
		),
		'THL BELEUCHTUNG' => array(
			array( 'klein', 'Einsatzstelle ausleuchten' ),
		),
		'THL BOMBENDROHUNG' => array(
			array( 'Bombe / Amok', 'Bombendrohung' ),
		),
		'THL BOMBENFUND' => array(
			array( 'Bombe / Amok', 'Bombenfund' ),
		),
		'THL ERKUNDUNG' => array(
			array( 'klein', 'Erkundung' ),
		),
		'THL FIRST RESPONDER' => array(
			array( 'RD', 'First Responder' ),
		),
		'THL GEBÄUDEEINSTURZ' => array(
			array( 'Einsturz / Umsturz', 'Gebäude eingestürzt' ),
		),
		'THL GROßTIERRETTUNG' => array(
			array( 'Tier', 'Rettung Großtier (z. B. Kuh, Pferd)' ),
		),
		'THL HUBSCHRAUBERLANDUNG' => array(
			array( 'Rettung', 'Hubschrauberlandung sichern' ),
		),
		'THL P AUFZUG' => array(
			array( 'Rettung', 'Aufzug öffnen akut' ),
		),
		'THL P RETTUNG H / T' => array(
			array( 'Absturz / Höhe', 'Person droht zu springen' ),
			array( 'Absturz / Höhe', 'Person absturzgefährdet' ),
			array( 'Absturz / Höhe', 'Person in Höhe' ),
			array( 'Absturz / Höhe', 'Person aus Tiefe / Schacht' ),
			array( 'Absturz / Höhe', 'schwergewichtiger Patient' ),
			array( 'Absturz / Höhe', 'Person auf Windrad / Kran' ),
			array( 'Absturz / Höhe', 'Paraglider / Fallschirmspringer / Drachenflieger abgestürzt' ),
		),
		'THL P STRAßENBAHN' => array(
			array( 'VU', 'Person unter Straßenbahn' ),
			array( 'VU', 'Straßenbahn' ),
		),
		'THL P STROM' => array(
			array( 'Rettung', 'Person Stromunfall' ),
		),
		'THL P U-BAHN' => array(
			array( 'VU', 'Person unter U-Bahn' ),
		),
		'THL P VERSCHÜTTET' => array(
			array( 'Rettung', 'Person verschüttet / Tiefbauunfall' ),
			array( 'Rettung', 'Person in Silo' ),
		),
		'THL P EINGESCHLOSSEN' => array(
			array( 'Rettung', 'Wohnung öffnen akut' ),
			array( 'Rettung', 'Fahrzeug öffnen akut' ),
		),
		'THL P ZUG' => array(
			array( 'VU', 'Person unter Zug' ),
			array( 'VU', 'Person unter S-Bahn' ),
			array( 'VU', 'Person vom Zug erfasst' ),
		),
		'THL RETTUNGSKORB' => array(
			array( 'RD', 'Drehleiter' ),
		),
		'THL 1' => array(
			array( 'klein', 'allgemein' ),
			array( 'klein', 'Baum auf Straße' ),
			array( 'klein', 'Baum auf Schiene' ),
			array( 'klein', 'Dach räumen' ),
			array( 'klein', 'Fahrzeug öffnen' ),
			array( 'klein', 'Fahrzeug sichern' ),
			array( 'klein', 'Wohnung öffnen' ),
			array( 'klein', 'Gebäude sichern' ),
			array( 'klein', 'Gegenstand / Teil sichern' ),
			array( 'klein', 'Straße reinigen' ),
			array( 'klein', 'Straße überschwemmt' ),
			array( 'klein', 'Wasser im Keller' ),
			array( 'klein', 'Wasser in Gebäude' ),
			array( 'RD', 'Unterstützung' ),
			array( 'Rettung', 'Personensuche' ),
			array( 'VU', 'mit Motorrad' ),
			array( 'VU', 'mit PKW' ),
			array( 'Tier', 'Insekten (Gefahr für Personen)' ),
			array( 'Tier', 'Rettung Kleintier' ),
			array( 'Tier', 'Bergung Kleintier' ),
			array( 'Tier', 'Bergung Großtier (z. B. Kuh, Pferd)' ),
			array( 'Rettung', 'Waldunfall ohne eingeklemmte Person' ),
			array( 'klein', 'Verkehrslenkung' ),
		),
		'THL 2' => array(
			array( 'VU', 'mehrere PKW' ),
			array( 'VU', 'LKW / Bus (leer), ohne eingeklemmte Personen' ),
		),
		'THL 3' => array(
			array( 'Rettung', 'Person eingeklemmt (nicht VU)' ),
			array( 'VU', '1 oder 2 PKW, Person eingeklemmt' ),
			array( 'VU', 'Bus (besetzt)' ),
			array( 'Einsturz / Umsturz', 'Gerüst umgestürzt' ),
			array( 'Einsturz / Umsturz', 'Stromleitungsmast umgestürzt' ),
			array( 'Einsturz / Umsturz', 'Kran umgestürzt' ),
			array( 'Rettung', 'Waldunfall mit eingeklemmter Person' ),
		),
		'THL 4' => array(
			array( 'VU', 'mehrere PKW, Personen eingeklemmt' ),
			array( 'VU', 'LKW / Bus (leer), Person eingeklemmt' ),
		),
		'THL 5' => array(
			array( 'VU', 'Massenkarambolage, Personen eingeklemmt' ),
			array( 'VU', 'Bus besetzt mit eingeklemmten Personen' ),
			array( 'VU', 'mehrere LKW mit eingeklemmten Personen' ),
		),
		'THL SCHIENE' => array(
			array( 'klein', 'Hilfeleistung Straßenbahn' ),
			array( 'klein', 'Hilfeleistung S-Bahn' ),
			array( 'klein', 'Hilfeleistung U-Bahn' ),
		),
		'THL WASSER' => array(
			array( 'Wasser', 'Bergung Sache / Leiche' ),
			array( 'Wasser', 'Rettung Tier' ),
			array( 'Wasser', 'Rettung Person' ),
			array( 'Wasser', 'Tauchereinsatz ohne Rettung' ),
		),
		'THL TRAGEHILFE' => array(
			array( 'RD', 'Tragehilfe' ),
		),
		'THL UNWETTER' => array(
			array( 'Unwetter', 'Baum / Ast droht zu fallen' ),
			array( 'Unwetter', 'Baum / Ast auf Fahrbahn' ),
			array( 'Unwetter', 'Baum / Ast auf Schiene' ),
			array( 'Unwetter', 'Baum / Ast auf Gebäude' ),
			array( 'Unwetter', 'Baum / Ast auf Stromleitung' ),
			array( 'Unwetter', 'Baum / Ast auf PKW / LKW' ),
			array( 'Unwetter', 'Baum umgestürzt' ),
			array( 'Unwetter', 'Bauteil / Gegenstand droht zu fallen' ),
			array( 'Unwetter', 'Gebäude sichern' ),
			array( 'Unwetter', 'Bauzaun sichern' ),
			array( 'Unwetter', 'Fahrbahn / Gehweg überschwemmt' ),
			array( 'Unwetter', 'Gebäude unter Wasser' ),
			array( 'Unwetter', 'Keller unter Wasser' ),
			array( 'Unwetter', 'Fahrzeug / sonstigen Gegenstand sichern' ),
			array( 'Unwetter', 'Erkundung nicht zeitkritisch' ),
			array( 'Unwetter', 'sonstiger Schaden' ),
		),
		'THL VU FLUGZEUG 1' => array(
			array( 'Luft', 'Notlandung' ),
			array( 'Luft', 'Ballon' ),
			array( 'Luft', 'Hubschrauber' ),
			array( 'Luft', 'Kleinflugzeug' ),
		),
		'THL VU FLUGZEUG 2' => array(
			array( 'Luft', 'Frachtflugzeug' ),
			array( 'Luft', 'Passagierflugzeug' ),
			array( 'Luft', 'Militärflugzeug' ),
		),
		'THL VU SCHIFF KOLLISION' => array(
			array( 'Wasser', 'Kollision Passagierschiff' ),
			array( 'Wasser', 'Kollision Frachtschiff' ),
			array( 'Wasser', 'Kollision Boot / Yacht / Floß' ),
		),
		'THL VU SCHIFF LECK' => array(
			array( 'Wasser', 'Schiff leck Passagierschiff' ),
			array( 'Wasser', 'Schiff leck Frachtschiff' ),
		),
		'THL VU ZUG' => array(
			array( 'VU', 'Zug' ),
		),
		'ABC 1' => array(
			array( 'Geruch', 'undefinierbarer Geruch' ),
			array( 'Geruch', 'Geruch nach Ammoniak' ),
			array( 'Geruch', 'Geruch nach Chlor' ),
			array( 'Geruch', 'Geruch nach Erdgas / Gas' ),
		),
		'ABC KRAFTSTOFF' => array(
			array( 'Gefahrstoff', 'auslaufender Kraftstoff (z. B. Benzin, Diesel)' ),
		),
		'ABC 2' => array(
			array( 'Gefahrstoff', 'verdächtiger Stoff' ),
			array( 'Gefahrstoff', 'undefinierbare Flüssigkeit' ),
			array( 'Gefahrstoff', 'undefinierbarer Gegenstand' ),
			array( 'Gefahrstoff', 'kleine Menge' ),
			array( 'Gefahrstoff', 'undefinierbares Pulver' ),
			array( 'Gefahrstoff', 'Gasaustritt im Freien' ),
		),
		'ABC 3' => array(
			array( 'Gefahrstoff', 'große Menge' ),
			array( 'Gefahrstoff', 'Gasaustritt brennbar' ),
			array( 'Gefahrstoff', 'Gasaustritt im Gebäude' ),
		),
		'ABC B ATOM' => array(
			array( 'Gefahrstoff', 'Brand Atom im Gebäude' ),
			array( 'Gefahrstoff', 'Brand Atom im Freien' ),
			array( 'Gefahrstoff', 'Brand Atom PKW / LKW' ),
			array( 'Gefahrstoff', 'Brand Atomkraftwerk (AKW)' ),
		),
		'ABC B' => array(
			array( 'Gefahrstoff', 'Brand Tankstelle' ),
			array( 'Gefahrstoff', 'Brand Biogasanlage' ),
			array( 'Gefahrstoff', 'Brand Raffinerie' ),
			array( 'Gefahrstoff', 'Brand Tanklager' ),
			array( 'Gefahrstoff', 'Brand Tankwagen' ),
		),
		'ABC B BIO / CHEMIE' => array(
			array( 'Gefahrstoff', 'Brand Bio im Gebäude' ),
			array( 'Gefahrstoff', 'Brand Bio im Freien' ),
			array( 'Gefahrstoff', 'Brand Bio PKW / LKW' ),
			array( 'Gefahrstoff', 'Brand Chemie im Gebäude' ),
			array( 'Gefahrstoff', 'Brand Chemie im Freien' ),
			array( 'Gefahrstoff', 'Brand Chemie Zug' ),
			array( 'Gefahrstoff', 'Brand Chemie LKW' ),
		),
		'ABC THL ATOM' => array(
			array( 'Gefahrstoff', 'THL Atom Austritt im Gebäude' ),
			array( 'Gefahrstoff', 'THL Atom Austritt im Freien' ),
			array( 'Gefahrstoff', 'THL Atom PKW / LKW' ),
			array( 'Gefahrstoff', 'THL VU Atom PKW / LKW' ),
		),
		'ABC THL BIO / CHEMIE' => array(
			array( 'Gefahrstoff', 'THL Bio Austritt im Freien' ),
			array( 'Gefahrstoff', 'THL Bio Austritt im Gebäude' ),
			array( 'Gefahrstoff', 'THL Bio PKW / LKW' ),
			array( 'Gefahrstoff', 'THL Chemie Austritt im Gebäude' ),
			array( 'Gefahrstoff', 'THL Chemie Austritt im Freien' ),
			array( 'Gefahrstoff', 'THL Chemie PKW / LKW' ),
			array( 'Gefahrstoff', 'THL VU Bio PKW / LKW' ),
			array( 'Gefahrstoff', 'THL VU Chemie PKW / LKW' ),
			array( 'Gefahrstoff', 'THL VU Chemie Zug' ),
		),
		'ABC EXPLOSION' => array(
			array( 'Gefahrstoff', 'Explosion / Verpuffung' ),
		),
		'ABC ÖL WASSER' => array(
			array( 'Gefahrstoff', 'Öl auf fließendem Gewässer' ),
			array( 'Gefahrstoff', 'Öl auf stehendem Gewässer' ),
		),
		'ABC ÖL LAND' => array(
			array( 'Gefahrstoff', 'undichter Heizöltank' ),
			array( 'Gefahrstoff', 'ausgedehnter Ölschaden' ),
		),
		'ABC GEFAHRSTOFFMELDEANLAGE' => array(
			array( 'GMA', 'Meldeanlage Ammoniak' ),
			array( 'GMA', 'Meldeanlage Chlor' ),
			array( 'GMA', 'Meldeanlage Stickstoff' ),
			array( 'GMA', 'Meldeanlage CO2' ),
			array( 'GMA', 'Meldeanlage Butan' ),
			array( 'GMA', 'Meldeanlage Propan' ),
			array( 'GMA', 'Meldeanlage undefiniert' ),
		),
		'RD 1' => array(
			array( 'Bewusstsein', 'Bewusstsein' ),
			array( 'Atmung', 'Atmung' ),
			array( 'Herz/Kreislauf', 'Herz/Kreislauf' ),
			array( '', 'Schmerzen' ),
			array( 'Neuro/Psych', 'Neuro' ),
			array( 'Neuro/Psych', 'Psych' ),
			array( 'Trauma', 'Trauma' ),
			array( 'Trauma', 'Verkehrsunfall (VU) nur RD' ),
			array( 'Kind', '(bis 12 Jahre) Erkrankt' ),
			array( 'Kind', '(bis 12 Jahre) Trauma' ),
			array( 'Kind', 'Inkubator – Intensiv' ),
			array( 'Kind', 'Neugeborenen-Holdienst (NHD)' ),
			array( 'Verlegung', 'Verlegung – Notfalltransport mit RTW' ),
			array( 'Sonstige', 'Sonstiges Ereignis/Zustand' ),
			array( 'Sonstige', 'Sonstiges Ereignis/Zustand – Intoxikation' ),
			array( 'Sonstige', 'Sonstiges Ereignis/Zustand – Geburt/Entbindung' ),
			array( 'Ärger', 'Ärger' ),
			array( 'Sonstige', 'Hausnotruf aktiver Alarm' ),
		),
		'RD 2' => array(
			array( 'Bewusstsein', 'vitale Bedrohung' ),
			array( 'Bewusstsein', 'Nachforderung NA' ),
			array( 'Atmung', 'vitale Bedrohung' ),
			array( 'Atmung', 'Nachforderung NA' ),
			array( 'Herz/Kreislauf', 'vitale Bedrohung' ),
			array( 'Herz/Kreislauf', 'Kreislaufstillstand/Reanimation' ),
			array( 'Herz/Kreislauf', 'Nachforderung NA' ),
			array( '', 'Schmerzen – stark' ),
			array( '', 'Schmerzen – Nachforderung NA' ),
			array( 'Neuro/Psych', 'Neuro – vitale Bedrohung' ),
			array( 'Neuro/Psych', 'Neuro – Nachforderung NA' ),
			array( 'Neuro/Psych', 'Psych – vitale Bedrohung' ),
			array( 'Neuro/Psych', 'Psych – vitale Bedrohung – mit Polizei' ),
			array( 'Neuro/Psych', 'Psych – Nachforderung NA' ),
			array( 'Trauma', 'vitale Bedrohung – Person verletzt schwer' ),
			array( 'Trauma', 'vitale Bedrohung – Verkehrsunfall (VU) nur RD' ),
			array( 'Trauma', 'vitale Bedrohung – Arbeitsunfall' ),
			array( 'Trauma', 'vitale Bedrohung – Starke Blutung' ),
			array( 'Trauma', 'vitale Bedrohung – Unfall (Schule, Kindergarten, Kita)' ),
			array( 'Trauma', 'Nachforderung NA' ),
			array( 'Verlegung', 'Verlegung – Notfalltransport mit NA' ),
			array( 'Sonstige', 'Sonstiges Ereignis/Zustand – vitale Bedrohung' ),
			array( 'Sonstige', 'Sonstiges Ereignis/Zustand – vitale Bedrohung – Intoxikation' ),
			array( 'Sonstige', 'Sonstiges Ereignis/Zustand – Geburt/Entbindung akut' ),
			array( 'Sonstige', 'Sonstiges Ereignis/Zustand – vitale Bedrohung – Nachforderung NA' ),
			array( 'Ärger', 'Ärger – vitale Bedrohung' ),
			array( 'Ärger', 'Ärger – Geiselnahme' ),
			array( 'Ärger', 'Ärger – Schuss, Hieb- und Stichverletzung(en)' ),
			array( 'Ärger', 'Ärger – vitale Bedrohung – Nachforderung NA' ),
		),
		'RD 2-KIND' => array(
			array( 'Kind', '(bis 12 Jahre) Erkrankt – vitale Bedrohung' ),
			array( 'Kind', '(bis 12 Jahre) Trauma – vitale Bedrohung' ),
			array( 'Kind', '(bis 12 Jahre) Kreislaufstillstand/Reanimation' ),
			array( 'Kind', '(bis 1 Jahr) Kreislaufstillstand/Reanimation' ),
			array( 'Kind', '(bis 12 Jahre) Nachforderung NA' ),
		),
		'RD BERGRETTUNG' => array(
			array( 'Bergrettung', 'Erkundung/Vermisstensuche' ),
			array( 'Bergrettung', 'Rettungsdiensteinsatz in unwegsamen Gelände' ),
			array( 'Bergrettung', 'Höhlenunfall' ),
			array( 'Bergrettung', 'Bergrettung – Lawinenunfall' ),
			array( 'Bergrettung', 'Bergrettung – Canyonunfall' ),
			array( 'Bergrettung', 'Bergrettung – Skiunfall' ),
			array( 'Bergrettung', 'Seilbahnevakuierung' ),
			array( 'Bergrettung', 'Gleitschirm/Drachen in Baum' ),
			array( 'Bergrettung', 'fachliche Unterstützung für Andere' ),
		),
		'RD WASSERNOT 0' => array(
			array( 'Wasserrettung', 'Hilfeleistung' ),
		),
		'RD WASSERNOT 1' => array(
			array( 'Wasserrettung', 'gekentertes Boot' ),
		),
		'RD WASSERNOT 2' => array(
			array( 'Wasserrettung', 'mehrere gekenterte Boote' ),
			array( 'Wasserrettung', 'Vermisstensuche' ),
		),
		'RD WASSERNOT 3' => array(
			array( 'Wasserrettung', '1 Person in Wassernot' ),
		),
		'RD WASSERNOT 4' => array(
			array( 'Wasserrettung', '2 bis 3 Personen in Wassernot' ),
		),
		'RD WASSERNOT 5' => array(
			array( 'Wasserrettung', 'ab 4 Personen in Wassernot' ),
		),
		'RD TAUCHUNFALL' => array(
			array( 'Wasserrettung', 'Wasserrettung – Tauchunfall' ),
		),
		'RD EISUNFALL 1' => array(
			array( 'Wasserrettung', 'auf dem Eis verletzt/erkrankt' ),
		),
		'RD EISUNFALL 2' => array(
			array( 'Wasserrettung', '1 bis 2 Person(en) im Eis eingebrochen' ),
		),
		'RD EISUNFALL 3' => array(
			array( 'Wasserrettung', 'ab 3 Personen im Eis eingebrochen' ),
		),
		'RD KTP' => array(
			array( 'KTP', 'KTP – Transport zum Krankenhaus' ),
			array( 'KTP', 'KTP – Verlegung' ),
			array( 'KTP', 'KTP – Heimfahrt' ),
			array( 'KTP', 'KTP – Ambulanzfahrt' ),
			array( 'KTP', 'KTP – Unterbringung' ),
			array( 'KTP', 'KTP – Inkubator' ),
			array( 'KTP', 'KTP – Dialyse' ),
			array( 'KTP', 'KTP – nicht disponibel (Prio 2)' ),
			array( 'KTP', 'KTP – Wohnungswechsel' ),
			array( 'KTP', 'KTP – sonstiger Transport' ),
			array( 'Verlegung', 'Verlegung – nicht disponibel mit KTW (Prio 1)' ),
		),
		'RD KTP/RTW' => array(
			array( 'KTP', 'KTP – Übernahme Landeplatz' ),
			array( 'KTP', 'KTP – mit RTW' ),
			array( 'KTP', 'KTP – Schwergewichtiger Patient' ),
		),
		'RD INFEKT GR4 / E' => array(
			array( 'KTP', 'Infekt Gr. 4/E' ),
		),
		'RD VEF' => array(
			array( 'Verlegung', 'Verlegung – VEF' ),
			array( 'Verlegung', 'Verlegung – VEF und RTW' ),
		),
		'RD ITW' => array(
			array( 'Verlegung', 'Verlegung – ITW' ),
		),
		'RD ITH' => array(
			array( 'Verlegung', 'Verlegung – ITH' ),
		),
		'RD AMOK RD' => array(
			array( 'Ärger', 'Ärger – Amok' ),
		),
		'RD 3' => array(
			array( 'Sonstige', '2 oder 3 verletzte/erkrankte Personen' ),
		),
		'RD 4' => array(
			array( 'Sonstige', '4 oder 5 verletzte/erkrankte Personen' ),
		),
		'RD 5' => array(
			array( 'Sonstige', '6 bis 9 verletzte/erkrankte Personen' ),
		),
		'RD MANV 10 – 15' => array(
			array( 'Sonstige', '10 bis 15 verletzte/erkrankte Personen' ),
		),
		'RD MANV 16 – 25' => array(
			array( 'Sonstige', '16 bis 25 verletzte/erkrankte Personen' ),
		),
		'RD MANV 26 – 50' => array(
			array( 'Sonstige', '26 bis 50 verletzte/erkrankte Personen' ),
		),
		'RD MANV 51 – 100' => array(
			array( 'Sonstige', '51 bis 100 verletzte/erkrankte Personen' ),
		),
		'RD MANV ab 100' => array(
			array( 'Sonstige', 'mehr als 100 verletzte/erkrankte Personen' ),
		),
		'RD ABSICHERUNG' => array(
			array( 'Sonstige', 'Bereitstellung Rettungsmittel' ),
			array( 'Sonstige', 'Dienstfahrt' ),
		),
		'RD SONSTIGE' => array(
			array( 'Sonstige', 'Werkstattfahrt' ),
			array( 'Sonstige', 'Gebietsabsicherung' ),
		),
		'RD BETREUUNG' => array(
			array( 'Sonstige', 'Betreuung von Personen Anzahl kleiner 50' ),
			array( 'Sonstige', 'Betreuung von Personen Anzahl größer 50' ),
		),
		'RD HILFE / SONSTIGE' => array(
			array( 'Sonstige', 'Hilfeleistung – nicht zeitkritisch' ),
			array( 'Sonstige', 'Lotsenfahrt durch RD-Fahrzeuge' ),
			array( 'Sonstige', 'Transport – von Transplantat / Organ / Blutkonserven / med. Gerät' ),
		),
		'RD ÜBERÖRTLICH' => array(
			array( 'Sonstige', 'Anforderung RD von Fremd-ILS Bayern' ),
			array( 'Sonstige', 'Anforderung RD von Fremd-ILS nicht Bayern' ),
		),
		'SON BELEUCHTUNG' => array(
			array( 'Sonstige', '' ),
		),
		'SON EINGLEISEN' => array(
			array( 'Sonstige', '' ),
		),
		'SON HILFE / SONSTIGES FW' => array(
			array( 'Sonstige', '' ),
		),
		'SON HUBSCHRAUBERLANDUNG' => array(
			array( 'Sonstige', '' ),
		),
		'SON MOTORRADSTREIFE' => array(
			array( 'Sonstige', 'Motorradstreife' ),
		),
		'SON PSNV (B)' => array(
			array( 'PSNV', 'Betroffene' ),
		),
		'SON PSNV (E)' => array(
			array( 'PSNV', 'Einsatzkräfte' ),
		),
		'SON THW BEREITSCHAFT' => array(
			array( '', '' ),
		),
		'SON TRAGEHILFE' => array(
			array( 'Sonstige', 'Tragehilfe für den Rettungsdienst' ),
		),
		'SON ÜBERÖRTLICHER EINSATZ' => array(
			array( 'Sonstige', '' ),
		),
		'INF ABNAHME BMA' => array(
			array( 'BMA', 'Abnahme BMA' ),
		),
		'INF APOTHEKENAUSKUNFT' => array(
			array( 'Auskunft', 'Apothekenauskunft' ),
		),
		'INF AUSFALL' => array(
			array( 'Ausfall', 'Ausfall – Fahrzeugdefekt' ),
			array( 'Ausfall', 'Ausfall – Gerätedefekt' ),
			array( 'Ausfall', 'Ausfall – Eigenunfall' ),
			array( 'Ausfall', 'Ausfall – Planungsfehler' ),
			array( 'Ausfall', 'Ausfall – Personalausfall RD-Personal' ),
			array( 'Ausfall', 'Ausfall – Personalverschulden' ),
			array( 'Ausfall', 'Ausfall – Personalausfall Arzt' ),
			array( 'Ausfall', 'Ausfall – Witterung' ),
			array( 'Ausfall', 'Ausfall – Desinfektion' ),
		),
		'INF BMA PROBE' => array(
			array( 'BMA', 'BMA Probe' ),
		),
		'INF BMA STÖRUNG' => array(
			array( 'BMA', 'BMA Störung' ),
		),
		'INF EIGENUNFALL' => array(
			array( 'Benachrichtigung', 'Eigenunfall' ),
		),
		'INF GIFTNOTRUF' => array(
			array( 'INF', 'Giftnotruf' ),
		),
		'INF HOCHWASSERMELDUNG' => array(
			array( 'Benachrichtigung', 'Hochwassermeldung' ),
		),
		'INF KASSENÄRZTLICHER BEREITSCHAFTSDIENST' => array(
			array( 'Auskunft', 'Vermittlung KVB-Arzt' ),
		),
		'INF LUFTBEOBACHTUNG' => array(
			array( 'Sonstiges', 'Luftbeobachtung' ),
		),
		'INF ÖFFENTLICHKEITSARBEIT' => array(
			array( 'Sonstiges', 'Öffentlichkeitsarbeit' ),
		),
		'INF PROBEALARM' => array(
			array( 'Sonstiges', 'Probealarm' ),
		),
		'INF SAN-DIENST' => array(
			array( 'Sonstiges', 'Sanitätsdienst' ),
		),
		'INF SICHERHEITSWACHE' => array(
			array( 'Sonstiges', 'Sicherheitswache' ),
		),
		'INF UNWETTERWARNUNG' => array(
			array( 'Benachrichtigung', 'Unwetterwarnung' ),
		),
		'INF VERKEHRSSICHERUNG' => array(
			array( 'Sonstiges', 'Verkehrssicherung' ),
		),
		'INF WACHBESETZUNG' => array(
			array( 'Sonstiges', 'Wachbesetzung' ),
		),
		'INF ZAHNARZTNOTDIENST' => array(
			array( 'Auskunft', 'Zahnarztnotdienst' ),
		),
	);
}

/** Begriffsname aus Stichwort, Kategorie und Schlagwort. */
function elfzwo_einsatzstichwort_name( $stichwort, $kategorie, $schlagwort ) {
	return implode( ' ', array_filter( array( $stichwort, $kategorie, $schlagwort ), 'strlen' ) );
}

/** Alle gültigen Begriffsnamen => Name des Eltern-Begriffs ('' für die Stichwort-Gruppen). */
function elfzwo_einsatzstichwort_names() {
	static $names = null;
	if ( null !== $names ) {
		return $names;
	}
	$names = array();
	foreach ( elfzwo_einsatzstichwoerter() as $stichwort => $eintraege ) {
		$names[ $stichwort ] = '';
	}
	foreach ( elfzwo_einsatzstichwoerter() as $stichwort => $eintraege ) {
		foreach ( $eintraege as $eintrag ) {
			$name = elfzwo_einsatzstichwort_name( $stichwort, $eintrag[0], $eintrag[1] );
			if ( ! isset( $names[ $name ] ) ) {
				$names[ $name ] = $stichwort;
			}
		}
	}
	return $names;
}

/** Nur Begriffe aus der festen Liste dürfen einem Einsatz zugewiesen werden. */
function elfzwo_einsatzstichwort_is_valid_term( $term_id ) {
	$term = get_term( (int) $term_id, 'einsatzstichwort' );
	return $term && ! is_wp_error( $term ) && isset( elfzwo_einsatzstichwort_names()[ wp_specialchars_decode( $term->name, ENT_QUOTES ) ] );
}

/** Vergleichsform alter Begriffsnamen: nur Buchstaben/Ziffern, ohne "Regionalschlagwort". */
function elfzwo_einsatzstichwort_normalize( $name ) {
	$name = mb_strtolower( wp_specialchars_decode( $name, ENT_QUOTES ) );
	$name = str_replace( 'regionalschlagwort', '', $name );
	return preg_replace( '/[^\p{L}\p{N}]+/u', '', $name );
}

/**
 * Bringt die Begriffe in der Datenbank auf den Stand der festen Liste.
 * Läuft nur, wenn sich die Liste seit dem letzten Abgleich geändert hat.
 *
 * - Fehlende Begriffe werden angelegt, falsche Eltern korrigiert.
 * - Nicht mehr gelistete, unbenutzte Begriffe werden gelöscht.
 * - Nicht mehr gelistete, aber zugewiesene Begriffe werden auf den
 *   passenden neuen Begriff umgestellt (gleiche Vergleichsform oder eine
 *   ist Anfang der anderen, z. B. "SON HILFE / SONSTIGES FW Sonstige Hilfe
 *   / Sonstiges FW" → "SON HILFE / SONSTIGES FW Sonstige"); der Titel der
 *   betroffenen Einsätze wird nachgezogen. Ohne passenden Begriff bleibt
 *   die Zuordnung unangetastet, damit keine Einsatzdaten verloren gehen.
 */
function elfzwo_sync_einsatzstichwoerter() {
	$wanted  = elfzwo_einsatzstichwort_names();
	$version = md5( wp_json_encode( $wanted ) );
	if ( get_option( 'elfzwo_einsatzstichwoerter_version' ) === $version ) {
		return;
	}

	$existing = get_terms(
		array(
			'taxonomy'   => 'einsatzstichwort',
			'hide_empty' => false,
		)
	);
	if ( is_wp_error( $existing ) ) {
		return;
	}

	wp_defer_term_counting( true );

	$ids      = array(); // Begriffsname => term_id
	$obsolete = array();
	foreach ( $existing as $term ) {
		$name = wp_specialchars_decode( $term->name, ENT_QUOTES );
		if ( isset( $wanted[ $name ] ) && ! isset( $ids[ $name ] ) ) {
			$ids[ $name ] = (int) $term->term_id;
		} else {
			$obsolete[] = $term;
		}
	}

	$touched_posts = array();
	$unmapped      = array();
	foreach ( $obsolete as $term ) {
		if ( ! $term->count ) {
			wp_delete_term( $term->term_id, 'einsatzstichwort' );
			continue;
		}

		$old    = elfzwo_einsatzstichwort_normalize( $term->name );
		$target = '';
		$best   = 0;
		foreach ( $wanted as $name => $parent ) {
			$new = elfzwo_einsatzstichwort_normalize( $name );
			if ( $new === $old ) {
				$target = $name;
				break;
			}
			if ( '' !== $parent && strlen( $new ) > $best && ( 0 === strpos( $old, $new ) || 0 === strpos( $new, $old ) ) ) {
				$target = $name;
				$best   = strlen( $new );
			}
		}
		if ( '' === $target ) {
			$unmapped[] = (int) $term->term_id;
			continue;
		}

		$posts = get_objects_in_term( $term->term_id, 'einsatzstichwort' );
		$posts = is_wp_error( $posts ) ? array() : $posts;
		$touched_posts = array_merge( $touched_posts, $posts );
		if ( isset( $ids[ $target ] ) ) {
			foreach ( $posts as $post_id ) {
				wp_set_object_terms( (int) $post_id, array( $ids[ $target ] ), 'einsatzstichwort', false );
			}
			wp_delete_term( $term->term_id, 'einsatzstichwort' );
		} else {
			wp_update_term( $term->term_id, 'einsatzstichwort', array( 'name' => $target, 'slug' => '' ) );
			$ids[ $target ] = (int) $term->term_id;
		}
	}

	// Gruppen zuerst, damit die Kinder ihren Eltern-Begriff finden.
	foreach ( array( true, false ) as $groups ) {
		foreach ( $wanted as $name => $parent ) {
			if ( ( '' === $parent ) !== $groups ) {
				continue;
			}
			$parent_id = '' === $parent ? 0 : ( $ids[ $parent ] ?? 0 );
			if ( isset( $ids[ $name ] ) ) {
				$term = get_term( $ids[ $name ], 'einsatzstichwort' );
				if ( $term && ! is_wp_error( $term ) && (int) $term->parent !== $parent_id ) {
					wp_update_term( $ids[ $name ], 'einsatzstichwort', array( 'parent' => $parent_id ) );
				}
				continue;
			}
			$result = wp_insert_term( $name, 'einsatzstichwort', array( 'parent' => $parent_id ) );
			if ( ! is_wp_error( $result ) ) {
				$ids[ $name ] = (int) $result['term_id'];
			}
		}
	}

	wp_defer_term_counting( false );
	clean_taxonomy_cache( 'einsatzstichwort' );

	foreach ( array_unique( array_map( 'intval', $touched_posts ) ) as $post_id ) {
		elfzwo_autogenerate_einsatz_title( $post_id );
	}

	update_option( 'elfzwo_einsatzstichwoerter_unmapped', $unmapped, false );
	update_option( 'elfzwo_einsatzstichwoerter_version', $version );
}
add_action( 'init', 'elfzwo_sync_einsatzstichwoerter', 20 );

/**
 * Hinweis im Backend, falls Einsätze noch alte Begriffe tragen, für die es
 * in der festen Liste keine Entsprechung gibt -- diese müssen am jeweiligen
 * Einsatz neu gewählt werden.
 */
function elfzwo_einsatzstichwoerter_unmapped_notice() {
	$unmapped = array_filter( (array) get_option( 'elfzwo_einsatzstichwoerter_unmapped', array() ) );
	if ( ! $unmapped || ! current_user_can( 'edit_posts' ) ) {
		return;
	}
	$names = array();
	foreach ( $unmapped as $term_id ) {
		$term = get_term( $term_id, 'einsatzstichwort' );
		if ( $term && ! is_wp_error( $term ) && $term->count ) {
			$names[] = $term->name;
		}
	}
	if ( ! $names ) {
		return;
	}
	printf(
		'<div class="notice notice-warning"><p><strong>Veraltete Einsatzstichwörter:</strong> Folgende Begriffe sind nicht mehr in der Alarmierungsbekanntmachung enthalten und noch Einsätzen zugewiesen – bitte dort ein aktuelles Stichwort wählen: %s</p></div>',
		esc_html( implode( ', ', $names ) )
	);
}
add_action( 'admin_notices', 'elfzwo_einsatzstichwoerter_unmapped_notice' );
