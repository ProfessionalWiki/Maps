<?php

/**
 * Internationalization file for the Maps extension
 *
 * @file Maps.i18n.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
*/

$messages = array();

/** English
 * @author Jeroen De Dauw
 */
$messages['en'] = array(
	// General
	'maps_name' => 'Maps',
	'maps_desc' => "Provides the ability to display coordinate data in maps, and geocode addresses ([http://wiki.bn2vs.com/wiki/Maps demo]).
Available mapping services: $1",
	'maps_map' => 'Map',

	// Coordinate errors
	'maps_coordinates_missing' => 'No coordinates provided for the map.',
	'maps_geocoding_failed' => 'The following {{PLURAL:$2|address|addresses}} could not be geocoded: $1.',
	'maps_geocoding_failed_for' => 'The following {{PLURAL:$2|address|addresses}} could not be geocoded and {{PLURAL:$2|has|have}} been omitted from the map:
$1',
	'maps_unrecognized_coords' => 'The following coordinates were not recognized: $1.',
	'maps_unrecognized_coords_for' => 'The following {{PLURAL:$2|coordinate was|coordinates were}} not recognized and {{PLURAL:$2|has|have}} been omitted from the map:
$1',
	'maps_map_cannot_be_displayed' => 'The map cannot be displayed.',

	// Parameter errors. Used when strict parameter validation is turned on.
	'maps_error_parameters' => 'The following errors have been detected in your syntax',
	'maps_error_invalid_argument' => 'The value $1 is not valid for parameter $2.',
	'maps_error_empty_argument' => 'Parameter $1 can not have an empty value.',
	'maps_error_required_missing' => 'The required parameter $1 is not provided.',
	'maps_error_must_be_number' => 'Parameter $1 can only be a number.',
	'maps_error_ivalid_range' => 'Parameter $1 must be between $2 and $3.',

	// Mapping services
	'maps_googlemaps' => 'Google Maps',
	'maps_yahoomaps' => 'Yahoo! Maps',
	'maps_openlayers' => 'OpenLayers', 
	'maps_osm' => 'OpenStreetMaps', 

	// Google Maps overlays
	'maps_overlays' => 'Overlays',	
	'maps_photos' => 'Photos',
	'maps_videos' => 'Videos',
	'maps_wikipedia' => 'Wikipedia',
	'maps_webcams' => 'Webcams'
);

/** Message documentation (Message documentation)
 * @author EugeneZelenko
 * @author Purodha
 * @author Raymond
 */
$messages['qqq'] = array(
	'maps_name' => '{{Optional}}',
	'maps_desc' => '{{desc}}

* $1: a list of available map services',
	'maps_geocoding_failed_for' => '* $1 is a list
* $2 is the number of list items for PLURAL use.',
	'maps_map' => '{{Identical|Map}}',
);

/** Afrikaans (Afrikaans)
 * @author Naudefj
 */
$messages['af'] = array(
	'maps_map' => 'Kaart',
);

/** Arabic (العربية)
 * @author Meno25
 */
$messages['ar'] = array(
	'maps_desc' => 'يعطي إمكانية عرض معلومات التنسيق في الخرائط وعناوين الترميز الجغرافي ([http://wiki.bn2vs.com/wiki/Maps تجربة]).
خدمات الخرائط المتوفرة: $1',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 * @author Jim-by
 */
$messages['be-tarask'] = array(
	'maps_desc' => 'Забясьпечвае магчымасьць адлюстраваньня каардынатных зьвестак на мапах і геаграфічнага кадаваньня адрасоў ([http://wiki.bn2vs.com/wiki/Maps дэманстрацыя]). Даступныя геаграфічныя сэрвісы: $1',
	'maps_coordinates_missing' => 'Каардынаты для мапы не пазначаныя.',
	'maps_geocoding_failed' => '{{PLURAL:$2|Наступны адрас ня можа быць геакадаваны|Наступныя адрасы ня могуць быць геакадаваныя}}: $1.
Мапа ня можа быць паказана.',
	'maps_geocoding_failed_for' => '{{PLURAL:$2|Наступны адрас ня можа быць геакадаваны і быў выдалены|Наступныя адрасы ня могуць быць геакадаваны і былі выдаленыя}} з мапы:
$1',
	'maps_map' => 'Мапа',
);

/** Bulgarian (Български)
 * @author DCLXVI
 */
$messages['bg'] = array(
	'maps_map' => 'Карта',
);

/** Breton (Brezhoneg)
 * @author Fulup
 */
$messages['br'] = array(
	'maps_desc' => "Talvezout a ra da embann daveennoù ha chomlec'hioù geokod war kartennoù ([http://wiki.bn2vs.com/wiki/Maps demo]). Servijoù kartennaouiñ hegerz : $1",
	'maps_coordinates_missing' => "N'eus bet spisaet daveenn ebet evit ar gartenn.",
	'maps_geocoding_failed' => "N'eus ket bet gallet douarkodañ ar {{PLURAL:$2|chomlec'h|chomlec'h}} da-heul : $1.
N'haller ket diskwel ar gartenn.",
	'maps_geocoding_failed_for' => "N'eus ket bet gallet douarkodañ ar {{PLURAL:$2|chomlec'h|chomlec'h}} da-heul, setu {{PLURAL:$2|n'eo|n'int}} ket bet lakaet war ar gartenn : 
$1",
	'maps_map' => 'Kartenn',
);

/** Bosnian (Bosanski)
 * @author CERminator
 */
$messages['bs'] = array(
	'maps_desc' => 'Daje mogućnost prikazivanja podataka koordinata na mapama i geocode adresa ([http://wiki.bn2vs.com/wiki/Maps demo]).
Dostupne usluge mapa: $1',
	'maps_coordinates_missing' => 'Za mapu nisu navedene koordinate.',
	'maps_geocoding_failed' => '{{PLURAL:$2|Slijedeća adresa nije mogla biti geokodirana|Slijedeće adrese nisu mogle biti geokodirane}}: $1.
Mapa se ne može prikazati.',
	'maps_geocoding_failed_for' => '{{PLURAL:$2|Slijedeća adresa nije|Slijedeće adrese nisu}} mogle biti geokodiranje i {{PLURAL:$2|izostavljena je|izostavljene su}} iz mape:
$1',
	'maps_map' => 'Mapa',
);

/** Catalan (Català)
 * @author Paucabot
 */
$messages['ca'] = array(
	'maps_coordinates_missing' => "No s'han proporcionat coordenades pel mapa.",
);

/** German (Deutsch)
 * @author Imre
 */
$messages['de'] = array(
	'maps_coordinates_missing' => 'Es wurden keine Koordinaten für die Karte angegeben.',
);

/** Lower Sorbian (Dolnoserbski)
 * @author Michawiki
 */
$messages['dsb'] = array(
	'maps_desc' => 'Bitujo móžnosć koordinatowe daty w geografiskich kórtach a geokodowe adrese zwobrazniś. ([http://wiki.bn2vs.com/wiki/Maps demo]).
K dispoziciji stojece kórtowe słužby: $1',
	'maps_map' => 'Karta',
	'maps_coordinates_missing' => 'Za kórtu njejsu koordinaty pódane.',
	'maps_geocoding_failed' => 'Geokoděrowanje {{PLURAL:$2|slědujuceje adrese|slědujuceju adresowu|slědujucych adresow|slědujucych adresow}} njejo móžno było: $1. Kórta njedajo se zwobrazniś.',
	'maps_geocoding_failed_for' => 'Geokoděrowanje {{PLURAL:$2|slědujuceje adrese|slědujuceju adresowu|slědujucych adresow|slědujucych adresow}} njejo móžno było a togodla toś {{PLURAL:$2|ta adresa wuwóstaja|tej adresy wuwóstajotej|te adrese wuwóstajaju|te adresy wuwóstajaju}} se na kórśe: $1',
	'maps_unrecognized_coords' => 'Slědujuce koordinaty njejsu se spóznali: $1.',
	'maps_unrecognized_coords_for' => '{{PLURAL:$2|Slědujuca koordinata njejo se spóznała|Slědujucej koordinaśe stej se spóznałej|Slědujuce koordinaty su se spóznali|Slědujuce koordinaty su se spóznali}} a {{PLURAL:$2|njejo se wuwóstajiła|njejstej se wuwóstajiłej|njejsu wuwóstajili|njejsu se wuwóstajili}} na kórśe: $1',
	'maps_map_cannot_be_displayed' => 'Kórta njedajo se zwobrazniś.',
	'maps_error_parameters' => 'Slědujuce zmólki su se namakali w twójej syntaksy:',
	'maps_error_invalid_argument' => 'Gódnota $1 njejo płaśiwa za parameter $2.',
	'maps_error_empty_argument' => 'Parameter $1 njamóžo proznu gódnotu měś.',
	'maps_error_required_missing' => 'Trěbny parameter $1 njejo pódany.',
	'maps_error_must_be_number' => 'Parameter $1 móžo jano licba byś.',
	'maps_error_ivalid_range' => 'Parameter $1 musy mjazy $2 a $3 byś.',
	'maps_overlays' => 'Pśekšyśa',
	'maps_photos' => 'Fota',
	'maps_videos' => 'Wideo',
	'maps_wikipedia' => 'Wikipedija',
	'maps_webcams' => 'Webcamy',
);

/** Greek (Ελληνικά)
 * @author Omnipaedista
 */
$messages['el'] = array(
	'maps_coordinates_missing' => 'Καμία συντεταγμένη δεν παρασχέθηκε για τον χάρτη.',
);

/** Spanish (Español)
 * @author Crazymadlover
 * @author Translationista
 */
$messages['es'] = array(
	'maps_map' => 'Mapa',
	'maps_coordinates_missing' => 'Sin coordenadas provistas para el mapa.',
	'maps_geocoding_failed' => 'Las siguientes {{PLURAL:$2|dirección|direcciones}}  no han podido ser geocodificadas: $1.
No se puede mostrar el mapa.',
	'maps_photos' => 'Fotos',
	'maps_videos' => 'Videos',
	'maps_webcams' => 'Cámaras Web',
);

/** Basque (Euskara)
 * @author Kobazulo
 */
$messages['eu'] = array(
	'maps_coordinates_missing' => 'Ez dago koordenaturik maparentzat.',
);

/** Finnish (Suomi)
 * @author Cimon Avaro
 * @author Crt
 */
$messages['fi'] = array(
	'maps_coordinates_missing' => 'Karttaa varten ei tarjottu koordinaatteja.',
	'maps_geocoding_failed' => '{{PLURAL:$2|Seuraavaa osoitetta|Seuraavia osoitteita}} ei voitu geokoodata: $1.
Karttaa ei voida näyttää.',
	'maps_geocoding_failed_for' => '{{PLURAL:$2|Seuraavaa osoitetta|Seuraavia osoitteita}} ei voitu geokoodata ja {{PLURAL:$2|on|ovat}} jätetty kartalta: $1',
);

/** French (Français)
 * @author Crochet.david
 * @author IAlex
 * @author PieRRoMaN
 * @author Verdy p
 */
$messages['fr'] = array(
	'maps_name' => 'Cartes',
	'maps_desc' => 'Permet d’afficher des coordonnées dans des cartes, ainsi que des adresses géocodées ([http://wiki.bn2vs.com/wiki/Maps démonstration]).
Services de cartographie disponibles : $1',
	'maps_map' => 'Carte',
	'maps_coordinates_missing' => "Aucune coordonnée n'a été fournie pour le plan.",
	'maps_geocoding_failed' => "{{PLURAL:$2|L′adresse suivante n'as pu être géocodée|Les adresses suivantes n'ont pas pu être géocodées}} : $1.
Le plan ne peut pas être affiché.",
	'maps_geocoding_failed_for' => '{{PLURAL:$2|L′adresse suivante n’as pu être géocodée|Les adresses suivantes n’ont pas pu être géocodées}} et {{PLURAL:$2|n’est pas affichée|ne sont pas affichées}} sur le plan : $1',
	'maps_unrecognized_coords' => "Les coordonnées suivantes n'ont pas été reconnues : $1.",
	'maps_unrecognized_coords_for' => "Les coordonnées suivantes n'ont pas été reconnues et {{PLURAL:$2|a été omise|ont été omises}} sur la carte : $1.",
	'maps_map_cannot_be_displayed' => 'La carte ne peut pas être affichée.',
	'maps_error_parameters' => 'Les erreurs suivantes ont été détectées dans votre syntaxe',
	'maps_error_invalid_argument' => "La valeur $1 n'est pas valide pour le paramètre $2.",
	'maps_error_empty_argument' => 'Le paramètre $1 ne peut pas avoir une valeur vide.',
	'maps_error_required_missing' => "Le paramètre requis $1 n'est pas fourni.",
	'maps_error_must_be_number' => 'Le paramètre $1 peut être uniquement un nombre.',
	'maps_error_ivalid_range' => 'Le paramètre $1 doit être entre $2 et $3.',
	'maps_overlays' => 'Superpositions',
	'maps_photos' => 'Photos',
	'maps_videos' => 'Vidéos',
	'maps_wikipedia' => 'Wikipédia',
	'maps_webcams' => 'Webcams',
);

/** Friulian (Furlan)
 * @author Klenje
 */
$messages['fur'] = array(
	'maps_desc' => 'Al furnìs la possibilitât di mostrâ i dâts de coordinadis e lis direzions geocodificadis intune mape ([http://wiki.bn2vs.com/wiki/Maps demo]).
Servizis di mapis disponibii: $1',
	'maps_coordinates_missing' => 'Nissune coordenade furnide pe mape.',
	'maps_geocoding_failed' => '{{PLURAL:$2|La direzion ca sot no pues jessi geocodificade|Lis direzions ca sot no puedin jessi geocodificadis}}: $1.
La mape no pues jessi mostrade.',
	'maps_geocoding_failed_for' => '{{PLURAL:$2|La direzion|Lis direzions}} ca sot no {{PLURAL:$2|pues|puedin}} jessi {{PLURAL:$2|geocodificade|geocodificadis}} e  {{PLURAL:$2|no je mostrade|no son mostradis}} te mape:
$1',
	'maps_map' => 'Mape',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'maps_desc' => 'Proporciona a capacidade de mostrar datos de coordenadas en mapas, e enderezos xeocodificados ([http://wiki.bn2vs.com/wiki/Maps demostración]).
Servizos de mapeamento dispoñibles: $1',
	'maps_map' => 'Mapa',
	'maps_coordinates_missing' => 'Non se proporcionou ningunha coordenada para o mapa.',
	'maps_geocoding_failed' => '{{PLURAL:$2|O seguinte enderezo non se puido xeocodificar|Os seguintes enderezos non se puideron xeocodificar}}: $1.
O mapa non se pode mostrar.',
	'maps_geocoding_failed_for' => '{{PLURAL:$2|O seguinte enderezo non se puido xeocodificar|Os seguintes enderezos non se puideron xeocodificar}} e {{PLURAL:$2|omitiuse|omitíronse}} no mapa: $1.',
	'maps_unrecognized_coords' => 'Non se recoñeceron as seguintes coordenadas: $1.',
	'maps_unrecognized_coords_for' => 'Non se {{PLURAL:$2|recoñeceu a seguinte coordenada|recoñeceron as seguintes coordenadas}} e {{PLURAL:$2|foi omitida|foron omitidas}} do mapa:
$1',
	'maps_map_cannot_be_displayed' => 'O mapa non se pode mostrar.',
	'maps_error_parameters' => 'Detectáronse os seguintes erros na sintaxe empregada',
	'maps_error_invalid_argument' => 'O valor $1 non é válido para o parámetro $2.',
	'maps_error_empty_argument' => 'O parámetro $1 non pode ter un valor baleiro.',
	'maps_error_required_missing' => 'Non se proporcionou o parámetro $1 necesario.',
	'maps_error_must_be_number' => 'O parámetro $1 só pode ser un número.',
	'maps_error_ivalid_range' => 'O parámetro $1 debe estar entre $2 e $3.',
	'maps_overlays' => 'Sobreposicións',
	'maps_photos' => 'Fotos',
	'maps_videos' => 'Vídeos',
	'maps_wikipedia' => 'Wikipedia',
	'maps_webcams' => 'Cámaras web',
);

/** Swiss German (Alemannisch)
 * @author Als-Holder
 */
$messages['gsw'] = array(
	'maps_desc' => 'Ergänzt d Megligkeit Koordinatedate in Charte un Geocodeadrässe aazzeige. Verfiegbari Chartedienscht: $1. [http://www.mediawiki.org/wiki/Extension:Maps Dokumäntation]. [http://wiki.bn2vs.com/wiki/Maps Demo]',
	'maps_map' => 'Charte',
	'maps_coordinates_missing' => 'S git kei Koordinate fir die Charte.',
	'maps_geocoding_failed' => 'Die {{PLURAL:$2|Adräss het|Adräss hän}} nit chenne georeferänziert wäre: $1. D Charte cha nit aazeigt wäre.',
	'maps_geocoding_failed_for' => 'Die {{PLURAL:$2|Adräss het|Adrässe hän}} nit chenne georeferänziert wäre un {{PLURAL:$2|isch|sin}} us dr Charte uusegnuu wore: $1',
	'maps_unrecognized_coords' => 'Die Koordinate sin nit erkannt wore: $1.',
	'maps_unrecognized_coords_for' => '{{PLURAL:$2|Die Koordinate isch nit erkannt wore un isch|Die Koordinate sin nit erkannt wore un sin}} wäge däm uusegnuu wore us dr Charte:
$1',
	'maps_map_cannot_be_displayed' => 'D Charte cha nit aazeigt wäre.',
	'maps_error_parameters' => 'Die Fähler sin in Dyyre Syntax gfunde wore',
	'maps_error_invalid_argument' => 'Dr Wärt $1 isch nit giltig fir dr Parameter $2.',
	'maps_error_empty_argument' => 'Dr Parameter $1 cha kei lääre Wärt haa.',
	'maps_error_required_missing' => 'Dr Paramter $1, wu aagforderet woren isch, wird nit z Verfiegig gstellt.',
	'maps_error_must_be_number' => 'Dr Parameter $1 cha nume ne Zahl syy.',
	'maps_error_ivalid_range' => 'Dr Parameter $1 muess zwische $2 un $3 syy.',
	'maps_overlays' => 'Overlay',
	'maps_photos' => 'Foto',
	'maps_videos' => 'Video',
	'maps_wikipedia' => 'Wikipedia',
	'maps_webcams' => 'Webcam',
);

/** Hebrew (עברית)
 * @author Rotemliss
 * @author YaronSh
 */
$messages['he'] = array(
	'maps_desc' => 'הוספת האפשרות להצגת נתוני קואורדינטות במפות וכתובות geocode ([http://wiki.bn2vs.com/wiki/Maps demo]).
שירותי המיפוי הזמינים: $1',
	'maps_coordinates_missing' => 'לא סופקו קואורדינטות למפה.',
	'maps_geocoding_failed' => 'לא ניתן לייצר geocode עבור {{PLURAL:$2|הכתובת הבאה|הכתובות הבאות}}: $1.
לא ניתן להציג את המפה.',
	'maps_geocoding_failed_for' => 'לא ניתן לייצר geocode עבור {{PLURAL:$2|הכתובת הבאה|הכתובות הבאות}}, ולכן {{PLURAL:$2|היא הושמטה|הן הושמטו}} מהמפה:
$1',
	'maps_map' => 'מפה',
);

/** Croatian (Hrvatski)
 * @author Suradnik13
 */
$messages['hr'] = array(
	'maps_desc' => 'Pruža mogućnost prikaza podataka o koordinatama na kartama, te geokodiranih adresa ([http://wiki.bn2vs.com/wiki/Maps demo]). Dostupne usluge kartiranja: $1',
	'maps_coordinates_missing' => 'Za kartu nisu dostupne koordinate.',
	'maps_geocoding_failed' => '{{PLURAL:$2|Sljedeća adresa ne može biti geokodirana|Sljedeće adrese ne mogu biti geokodirane}}: $1.
Karta ne može biti prikazana.',
	'maps_geocoding_failed_for' => '{{PLURAL:$2|Sljedeća adresa ne može biti geokodirana|Sljedeće adrese ne mogu biti geokodirane}} i {{PLURAL:$2|izostavljena je|izostavljene su}} iz karte:
$1',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'maps_desc' => 'Skići móžnosć koordinatowe daty w geografiskich kartach a geokodne adresy zwobraznić ([http://wiki.bn2vs.com/wiki/Maps demo]). 
K dispoziciji stejace kartowe słužby: $1',
	'maps_map' => 'Karta',
	'maps_coordinates_missing' => 'Za kartu njejsu koordinaty podate.',
	'maps_geocoding_failed' => 'Geokodowanje {{PLURAL:$2|slědowaceje adresy|slědowaceju adresow|slědowacych adresow|slědowacych adresow}} njebě móžno: $1. Karta njeda so zwobraznić.',
	'maps_geocoding_failed_for' => 'Geokodowanje {{PLURAL:$2|slědowaceje adresy|slědowaceju adresow|slědowacych adresow|slědowacych adresow}} njebě móžno a {{PLURAL:$2|tuta adresa|tutej adresy|tute adresy|tute adresy}} so na karće {{PLURAL:$2|wuwostaja|wuwostajetej|wuwostajeja|wuwostajeja}}: $1',
	'maps_unrecognized_coords' => 'Slědowace koordinaty njebuchu spóznane: $1.',
	'maps_unrecognized_coords_for' => '{{PLURAL:$2|Slědowaca koordinata njebu spóznana|Slědowacej koordinaće njebuštej spóznanej|Slědowace koordinaty njebuchu spóznane|Slědowace koordinaty njebuchu spóznane}} a {{PLURAL:$2|bu na karće wuwostajena|buštej na karće wuwostajenej|buchu na karće wuwostajene|buchu na karće wuwostajene}}: $1',
	'maps_map_cannot_be_displayed' => 'Karta njeda so zwobraznić.',
	'maps_error_parameters' => 'Slědowace zmylki buchu w twojej syntaksy wotkryli:',
	'maps_error_invalid_argument' => 'Hódnota $1 njeje płaćiwa za parameter $2.',
	'maps_error_empty_argument' => 'Parameter $1 njemóže prózdnu hódnotu měć.',
	'maps_error_required_missing' => 'Trěbny parameter $1 njeje podaty.',
	'maps_error_must_be_number' => 'Parameter $1 móže jenož ličba być.',
	'maps_error_ivalid_range' => 'Parameter $1 dyrbi mjez $2 a $3 być.',
	'maps_overlays' => 'Naworštowanja',
	'maps_photos' => 'Fota',
	'maps_videos' => 'Wideja',
	'maps_wikipedia' => 'Wikipedija',
	'maps_webcams' => 'Webcamy',
);

/** Hungarian (Magyar)
 * @author Dani
 * @author Glanthor Reviol
 */
$messages['hu'] = array(
	'maps_desc' => 'Lehetővé teszi koordinátaadatok és geokódolt címek megjelenítését térképeken ([http://wiki.bn2vs.com/wiki/Maps demó]). Elérhető térképszolgáltatások: $1',
	'maps_map' => 'Térkép',
	'maps_coordinates_missing' => 'Nincsenek megadva koordináták a térképhez.',
	'maps_geocoding_failed' => 'A következő {{PLURAL:$2|cím|címek}} nem geokódolhatók: $1.
A térképet nem lehet megjeleníteni.',
	'maps_geocoding_failed_for' => 'A következő {{PLURAL:$2|cím nem geokódolható|címek nem geokódolhatóak}}, és nem {{PLURAL:$2|szerepel|szerepelnek}} a térképen:
$1',
	'maps_unrecognized_coords' => 'A következő koordinátákat nem sikerült felismerni: $1.',
	'maps_unrecognized_coords_for' => 'A következő koordinátákat nem sikerült felismerni, és {{PLURAL:$2|el|el}} lettek távolítva a térképről: $1',
	'maps_map_cannot_be_displayed' => 'A térképet nem sikerült megjeleníteni.',
	'maps_error_parameters' => 'A következő hibák találhatóak a szintaxisodban',
	'maps_error_invalid_argument' => 'A(z) $1 érték nem érvényes a(z) $2 paraméterhez.',
	'maps_error_empty_argument' => 'A(z) $1 paraméter értéke nem lehet üres.',
	'maps_error_required_missing' => 'A(z) $1 kötelező paraméter nem lett megadva.',
	'maps_error_must_be_number' => 'A(z) $1 paraméter csak szám lehet.',
	'maps_error_ivalid_range' => 'A(z) $1 paraméter értékének $2 és $3 között kell lennie.',
	'maps_overlays' => 'Rétegek',
	'maps_photos' => 'Fényképek',
	'maps_videos' => 'Videók',
	'maps_wikipedia' => 'Wikipédia',
	'maps_webcams' => 'Webkamerák',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'maps_desc' => 'Permitte monstrar datos de coordinatas in mappas, e adresses geocodice ([http://wiki.bn2vs.com/wiki/Maps demo]).
Servicios cartographic disponibile: $1',
	'maps_coordinates_missing' => 'Nulle coordinata providite pro le mappa.',
	'maps_geocoding_failed' => 'Le sequente {{PLURAL:$2|adresse|adresses}} non poteva esser geocodificate: $1.
Le mappa non pote esser monstrate.',
	'maps_geocoding_failed_for' => 'Le sequente {{PLURAL:$2|adresse|adresses}} non poteva esser geocodificate e ha essite omittite del mappa:
$1',
	'maps_error_parameters' => 'Le sequente errores ha essite detegite in tu syntaxe',
	'maps_error_invalid_argument' => 'Le valor $1 non es valide pro le parametro $2.',
	'maps_error_empty_argument' => 'Le parametro $1 non pote haber un valor vacue.',
	'maps_error_required_missing' => 'Le parametro requisite $1 non ha essite fornite.',
	'maps_error_must_be_number' => 'Le parametro $1 pote solmente esser un numero.',
	'maps_error_ivalid_range' => 'Le parametro $1 debe esser inter $2 e $3.',
	'maps_map' => 'Carta',
);

/** Indonesian (Bahasa Indonesia)
 * @author Bennylin
 * @author Irwangatot
 * @author IvanLanin
 */
$messages['id'] = array(
	'maps_desc' => "Memampukan tampilan data koordinat pada peta, dan alamat ''geocode'' ([http://wiki.bn2vs.com/wiki/Maps demo]). 
Layanan pemetaan yang tersedia: $1",
	'maps_map' => 'Peta',
	'maps_coordinates_missing' => 'Tidak koordinat yang disediakan bagi peta.',
	'maps_geocoding_failed' => '{{PLURAL:$2|alamat|alamat}} berikut tidak dapat di Geocode: $1. 
Peta tidak dapat ditampilkan.',
	'maps_geocoding_failed_for' => '{{PLURAL:$2|alamat|alamat}} berikut tidak dapat di Geocode dan  {{PLURAL:$2|telah|telah}} dihilangkan dari peta: $1',
	'maps_unrecognized_coords' => 'Koordinat berikut tidak dikenali: $1.',
	'maps_unrecognized_coords_for' => 'Koordinat berikut tidak dikenali dan {{PLURAL:$2|telah|telah}} diabaikan dari peta:
$1',
	'maps_map_cannot_be_displayed' => 'Peta tak dapat ditampilkan.',
	'maps_error_parameters' => 'Kesalahan berikut telah dideteksi pada sintaksis Anda',
	'maps_error_invalid_argument' => 'Nilai $1 tidak valid untuk parameter $2.',
	'maps_error_empty_argument' => 'Parameter $1 tidak dapat bernilai kosong.',
	'maps_error_required_missing' => 'Parameter $1 yang diperlukan tidak diberikan.',
	'maps_error_must_be_number' => 'Parameter $1 hanya dapat berupa angka.',
	'maps_error_ivalid_range' => 'Parameter $1 harus antara $2 dan $3.',
	'maps_overlays' => 'Hamparan',
	'maps_photos' => 'Foto',
	'maps_videos' => 'Video',
	'maps_wikipedia' => 'Wikipedia',
	'maps_webcams' => 'Kamera web',
);

/** Japanese (日本語)
 * @author Aotake
 * @author Fryed-peach
 */
$messages['ja'] = array(
	'maps_desc' => '地図上に座標データを表示し、住所を座標データに変換する機能を提供する ([http://wiki.bn2vs.com/wiki/Maps 実演])。次の地図サービスに対応します: $1',
	'maps_map' => '地図',
	'maps_coordinates_missing' => '地図に座標が指定されていません。',
	'maps_geocoding_failed' => '指定された{{PLURAL:$2|住所}}の座標への変換に失敗しました。 $1。地図は表示できません。',
	'maps_geocoding_failed_for' => '指定された{{PLURAL:$2|住所|複数の住所}}の座標への変換に失敗したため、それらを地図から除外して表示します。$1',
	'maps_unrecognized_coords' => '以下の座標は認識されませんでした: $1',
	'maps_unrecognized_coords_for' => '以下の座標は認識されなかったため、地図から省かれて{{PLURAL:$2|います}}:
$1',
	'maps_map_cannot_be_displayed' => 'この地図は表示できません。',
	'maps_error_parameters' => 'あなたの入力から以下のエラーが検出されました',
	'maps_error_invalid_argument' => '値「$1」は引数「$2」として妥当ではありません。',
	'maps_error_empty_argument' => '引数「$1」は空の値をとることができません。',
	'maps_error_required_missing' => '必須の引数「$1」が入力されていません。',
	'maps_error_must_be_number' => '引数「$1」は数値でなければなりません。',
	'maps_error_ivalid_range' => '引数「$1」は $2 と $3 の間の値でなければなりません。',
	'maps_overlays' => 'オーバーレイ',
	'maps_photos' => '写真',
	'maps_videos' => '動画',
	'maps_wikipedia' => 'ウィキペディア',
	'maps_webcams' => 'ウェブカメラ',
);

/** Ripoarisch (Ripoarisch)
 * @author Purodha
 */
$messages['ksh'] = array(
	'maps_desc' => 'Määt et müjjelesch, Koodinaate en Landkaate aanzezeije, un Addräße en Koodinaate op de Ääd ömzerääschne. (E [http://wiki.bn2vs.com/wiki/Maps Beispöll]). He di Deenste för Landkaat(e) ham_mer ze beede: $1',
	'maps_coordinates_missing' => 'Mer han kein Koodinaate för di Kaat.',
	'maps_geocoding_failed' => '{{PLURAL:$2|Di Koodinaat|De Koodinaate|Kein Koodinaat}} om Jlobus för di {{PLURAL:$2|aanjejovve Adräß wohr|aanjejovve Adräße wohre|kein aanjejovve Adräß wohr}} Kappes: $1. Di Kaat künne mer su nit aanzeije.',
	'maps_geocoding_failed_for' => 'De Koodinaate om Jlobus för {{PLURAL:$2|ein|paa|kein}} vun dä aanjejovve Adräße {{PLURAL:$2|es|wohre|Fähler!}} Kappes. Di {{PLURAL:$2|es|sin|Fähler!}} dröm nit op dä Kaat. De fottjelohße {{PLURAL:$2|es|sin|Fähler!}}: $1',
	'maps_map' => 'Kaat',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'maps_desc' => "Gëtt d'Méiglechkeet fir d'Date vun de Koordinaten op Kaarten an Geocode Adressen ze weisen. Disponibel mapping Servicer: $1 [http://www.mediawiki.org/wiki/Extension:Maps Dokumentatioun]. [http://wiki.bn2vs.com/wiki/Maps Démo]",
	'maps_coordinates_missing' => "Et goufe keng Koordinate fir d'Kaart uginn.",
	'maps_geocoding_failed_for' => 'Dës {{PLURAL:$2|Adress|Adresse}} konnten net geocodéiert ginn an {{PLURAL:$2|huet|hu}} missen op der Kaart ewechgelooss ginn:
$1',
	'maps_map' => 'Kaart',
);

/** Macedonian (Македонски)
 * @author Bjankuloski06
 */
$messages['mk'] = array(
	'maps_desc' => 'Дава можност за приказ на координатни податоци во картите, и геокодирање на адреси ([http://wiki.bn2vs.com/wiki/Maps демо]).
Картографски служби на располагање: $1',
	'maps_map' => 'Карта',
	'maps_coordinates_missing' => 'Нема координати за картата.',
	'maps_geocoding_failed' => '{{PLURAL:$2|Следнава адреса не можеше да се геокодира|Следниве адреси не можеа да се геокодираат}}: $1.
Картата не може да се прикаже.',
	'maps_geocoding_failed_for' => '{{PLURAL:$2|Следнава адреса|Следниве адреси}} не можеа да се геокодираат и затоа {{PLURAL:$2|е испуштена|се испуштени}} од картата:
$1',
	'maps_unrecognized_coords' => 'Следниве координати не беа препознаени: $1.',
	'maps_unrecognized_coords_for' => 'Следниве координати не беа препознаени, и затоа {{PLURAL:$2|тие|тие}} не се прикажани на картата:
$1',
	'maps_map_cannot_be_displayed' => 'Картата не може да се прикаже.',
	'maps_error_parameters' => 'Откриени се следниве грешки во вашата синтакса',
	'maps_error_invalid_argument' => 'Вредноста $1 е неважечка за параметарот $2.',
	'maps_error_empty_argument' => 'Параметарот $1 не може да има празна вредност.',
	'maps_error_required_missing' => 'Бараниот параметар $1 не е наведен.',
	'maps_error_must_be_number' => 'Параметарот $1 може да биде само број.',
	'maps_error_ivalid_range' => 'Параметарот $1 мора да изнесува помеѓу $2 и $3.',
	'maps_overlays' => 'Слоеви',
	'maps_photos' => 'Фотографии',
	'maps_videos' => 'Видеа',
	'maps_wikipedia' => 'Википедија',
	'maps_webcams' => 'Веб-камери',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'maps_desc' => 'Biedt de mogelijkheid om locatiegegevens weer te geven op kaarten en adressen om te zetten naar coordinaten ([http://wiki.bn2vs.com/wiki/Semantic_Maps demo]).
Beschikbare kaartdiensten: $1',
	'maps_map' => 'Kaart',
	'maps_coordinates_missing' => 'Er zijn geen coördinaten opgegeven voor de kaart.',
	'maps_geocoding_failed' => 'Voor {{PLURAL:$2|het volgende adres|de volgende adressen}} was geocodering niet mogelijk: $1
De kaart kan niet worden weergegeven.',
	'maps_geocoding_failed_for' => 'Voor {{PLURAL:$2|het volgende adres|de volgende adressen}} was geocodering niet mogelijk en {{PLURAL:$2|dit is|deze zijn}} weggelaten uit de kaart:
$1',
	'maps_unrecognized_coords' => 'De volgende coördinaten zijn niet herkend: $1.',
	'maps_unrecognized_coords_for' => 'De volgende {{PLURAL:$2|coördinaat is niet herkend en is|coördinaten zijn niet herkend en zijn}} weggelaten uit de kaart:
$1.',
	'maps_map_cannot_be_displayed' => 'De kaart kan niet weergegeven worden.',
	'maps_error_parameters' => 'In uw syntaxis zijn de volgende fouten gedetecteerd',
	'maps_error_invalid_argument' => 'De waarde $1 is niet geldig voor de parameter $2.',
	'maps_error_empty_argument' => 'De parameter $1 mag niet leeg zijn.',
	'maps_error_required_missing' => 'De verplichte parameter $1 is niet opgegeven.',
	'maps_error_must_be_number' => 'De parameter $1 mag alleen een getal zijn.',
	'maps_error_ivalid_range' => 'De parameter $1 moet tussen $2 en $3 liggen.',
	'maps_googlemaps' => 'Google Maps',
	'maps_yahoomaps' => 'Yahoo! Maps',
	'maps_openlayers' => 'OpenLayers',
	'maps_overlays' => "Overlay's",
	'maps_photos' => "Foto's",
	'maps_videos' => 'Video',
	'maps_wikipedia' => 'Wikipedia',
	'maps_webcams' => 'Webcams',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'maps_desc' => "Permet d'afichar de coordenadas dins de mapas, e mai d'adreça geocodadas
([http://www.mediawiki.org/wiki/Extension:Maps documentacion], [http://wiki.bn2vs.com/wiki/Maps demonstracion]). 
Servicis de cartografia disponibles : $1",
	'maps_coordinates_missing' => 'Cap de coordenada es pas estada provesida pel plan.',
	'maps_geocoding_failed' => "{{PLURAL:$2|L'adreça seguenta a pas pogut èsser geoencodada|Las adreças seguentas an pas pogut èsser geoencodadas}} : $1.
Lo plan pòt pas èsser afichat.",
	'maps_geocoding_failed_for' => "{{PLURAL:$2|L'adreça seguenta a pas pogut èsser geoencodada|Las adreças seguentas an pas pogut èsser geoencodadas}} e {{PLURAL:$2|es pas afichada|son pas afichadas}} sul plan : $1",
	'maps_map' => 'Mapa',
);

/** Polish (Polski)
 * @author Sp5uhe
 */
$messages['pl'] = array(
	'maps_desc' => 'Umożliwia wyświetlanie na mapach współrzędnych oraz adresów geograficznych ([http://wiki.bn2vs.com/wiki/Maps demo]). Dostępne serwisy mapowe: $1',
	'maps_coordinates_missing' => 'Brak współrzędnych dla mapy.',
	'maps_geocoding_failed' => '{{PLURAL:$2|Następującego adresu nie można odnaleźć na mapie|Następujących adresów nie można odnaleźć na mapie:}} $1.
Mapa nie może zostać wyświetlona.',
	'maps_geocoding_failed_for' => '{{PLURAL:$2|Następujący adres został pominięty, ponieważ nie można go odnaleźć na mapie|Następujące adresy zostały pominięte, ponieważ nie można ich odnaleźć na mapie:}} $1.',
);

/** Piedmontese (Piemontèis)
 * @author Borichèt
 * @author Dragonòt
 */
$messages['pms'] = array(
	'maps_desc' => "A dà la possibilità ëd visualisé le coordinà ant le mape, e j'adrësse geocode ([http://wiki.bn2vs.com/wiki/Maps demo]). Sërvissi ëd mapatura disponìbil: $1",
	'maps_map' => 'Pian',
	'maps_coordinates_missing' => 'Pa gnun-e coordinà dàite për la mapa.',
	'maps_geocoding_failed' => "{{PLURAL:$2|L'adrëssa|J'adrësse}} sì sota a peulo pa esse sota geocode: $1.
La mapa a peul pa esse visualisà.",
	'maps_geocoding_failed_for' => "{{PLURAL:$2|L'adrëssa|J'adrësse}} sì sota a peula pa esse sota geocode e a {{PLURAL:$2|l'é pa stàita|son pa stàite}}  butà ant la mapa: $1",
	'maps_unrecognized_coords' => 'Le coordinà sota a son pa stàite arconossùe: $1.',
	'maps_unrecognized_coords_for' => 'Le coordinà sota a son pa stàite arconossùe e a {{PLURAL:$2|son|son}} stàite pa butà ant la carta: $1',
	'maps_map_cannot_be_displayed' => 'La carta a peul pa esse mostrà.',
	'maps_error_parameters' => "J'eror sì sota a son ëstàit trovà an toa sintassi",
	'maps_error_invalid_argument' => "Ël valor $1 a l'é pa bon për ël paràmetr $2.",
	'maps_error_empty_argument' => 'Ël paràmetr $1 a peul pa avèj un valor veuid.',
	'maps_error_required_missing' => "Ël paràmetr obligatòri $1 a l'é pa dàit.",
	'maps_error_must_be_number' => 'Ël paràmetr $1 a peul mach esse un nùmer.',
	'maps_error_ivalid_range' => 'Ël paràmetr $1 a deuv esse an tra $2 e $3.',
	'maps_overlays' => 'Sovraposission',
	'maps_photos' => 'Fòto',
	'maps_videos' => 'Filmà',
	'maps_wikipedia' => 'Wikipedia',
	'maps_webcams' => 'Webcam',
);

/** Portuguese (Português)
 * @author Hamilton Abreu
 */
$messages['pt'] = array(
	'maps_desc' => 'Permite apresentar dados de coordenadas em mapas e endereços por geocódigo ([http://wiki.bn2vs.com/wiki/Maps demonstração]).
Serviços de cartografia disponíveis: $1',
	'maps_coordinates_missing' => 'Não foram fornecidas coordenadas para o mapa.',
	'maps_geocoding_failed' => 'Não foi possível geocodificar {{PLURAL:$2|o seguinte endereço|os seguintes endereços}}: $1.
O mapa não pode ser apresentado.',
	'maps_geocoding_failed_for' => 'Não foi possível geocodificar {{PLURAL:$2|o seguinte endereço, que foi omitido|os seguintes endereços, que foram omitidos}} do mapa:
$1.',
	'maps_error_parameters' => 'Foram detectados os seguintes erros sintácticos',
	'maps_error_invalid_argument' => 'O valor $1 não é válido para o parâmetro $2.',
	'maps_error_empty_argument' => 'O parâmetro $1 não pode estar vazio.',
	'maps_error_required_missing' => 'O parâmetro obrigatório $1 não foi fornecido.',
	'maps_error_must_be_number' => 'O parâmetro $1 só pode ser numérico.',
	'maps_error_ivalid_range' => 'O parâmetro $1 tem de ser entre $2 e $3.',
	'maps_map' => 'Mapa',
);

/** Brazilian Portuguese (Português do Brasil)
 * @author Eduardo.mps
 */
$messages['pt-br'] = array(
	'maps_desc' => 'Provê a possibilidade de exibir dados de coordenadas em mapas e endereços em geocódigo. ([http://wiki.bn2vs.com/wiki/Maps demonstração]).
Serviços de mapeamento disponíveis: $1',
	'maps_coordinates_missing' => 'Nenhuma coordenada fornecida para o mapa',
	'maps_geocoding_failed' => '{{PLURAL:$2|O seguinte endereço não pode|Os seguintes endereços não puderam}} ser {{PLURAL:$2|geocodificado|geocodificados}}: $1.
O mapa não pode ser exibido.',
	'maps_geocoding_failed_for' => '{{PLURAL:$2|O seguinte endereço não pode|Os seguintes endereços não puderam}} ser {{PLURAL:$2|geocodificado e foi omitido|geocodificados e foram omitidos}} do mapa:
$1',
);

/** Romanian (Română)
 * @author KlaudiuMihaila
 * @author Minisarm
 */
$messages['ro'] = array(
	'maps_desc' => 'Asigură capacitatea de a afişa coordonate pe hărţi şi adrese geocode ([http://wiki.bn2vs.com/wiki/Maps demonstraţie]).
Servici de cartografiere disponibile: $1',
	'maps_coordinates_missing' => 'Nici o coordonată oferită pentru hartă.',
	'maps_geocoding_failed' => '{{PLURAL:$2|Următoarea|Următoarele}} {{PLURAL:$2|adresă|adrese}} nu {{PLURAL:$2|a|au}} putut fi {{PLURAL:$2|geocodificată|geocodificate}}: $1.
Harta nu poate fi afişată.',
	'maps_geocoding_failed_for' => '{{PLURAL:$2|Următoarea|Următoarele}} {{PLURAL:$2|adresă|adrese}} nu {{PLURAL:$2|a|au}} putut fi {{PLURAL:$2|geocodificată|geocodificate}} şi {{PLURAL:$2|a|au}} fost {{PLURAL:$2|omisă|omise}} de pe hartă:
$1',
	'maps_map' => 'Hartă',
);

/** Tarandíne (Tarandíne)
 * @author Joetaras
 */
$messages['roa-tara'] = array(
	'maps_desc' => "Dè l'abbilità a fà vedè le coordinate jndr'à le mappe e le indirizze geocodificate ([http://wiki.bn2vs.com/wiki/Maps demo]). Disponibbile le servizie de mappe: $1",
);

/** Russian (Русский)
 * @author Lockal
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'maps_desc' => 'Обеспечивает возможность отображения координатных данных на картах и геокодирование адресов ([http://wiki.bn2vs.com/wiki/Maps демонстрация]).
Доступные картографические службы: $1',
	'maps_map' => 'Карта',
	'maps_coordinates_missing' => 'Не указаны координаты для карты.',
	'maps_geocoding_failed' => '{{PLURAL:$2|Следующий адрес не может быть геокодирован|Следующие адреса не могут быть геокодированы}}: $1.
Карта не может быть отображена.',
	'maps_geocoding_failed_for' => '{{PLURAL:$2|Следующий адрес не может быть геокодирован и был удалён|Следующие адреса не могут быть геокодированы и были удалены}} с карты:
$1',
	'maps_unrecognized_coords' => 'Следующие координаты не были опознаны: $1.',
	'maps_unrecognized_coords_for' => 'Следующие координаты не были опознаны, {{PLURAL:$2|они|они}} не показаны на карте:
$1',
	'maps_map_cannot_be_displayed' => 'Карта не может быть показана.',
	'maps_error_parameters' => 'Обнаружены следующие ошибки в синтаксисе',
	'maps_error_invalid_argument' => 'Значение $1 не является допустимым параметром $2',
	'maps_error_empty_argument' => 'Параметр $1 не может принимать пустое значение.',
	'maps_error_required_missing' => 'Не указан обязательный параметр $1.',
	'maps_error_must_be_number' => 'Значением параметра $1 могут быть только числа.',
	'maps_error_ivalid_range' => 'Параметр $1 должен быть от $2 до $3.',
	'maps_overlays' => 'Слои',
	'maps_photos' => 'Фото',
	'maps_videos' => 'Видео',
	'maps_wikipedia' => 'Википедия',
	'maps_webcams' => 'Веб-камеры',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'maps_desc' => 'Poskytuje možnosť zobrazovať údaje súradníc na mapách a tvoriť geografické adresy lokalít ([http://wiki.bn2vs.com/wiki/Semantic_Maps demo]).
Dostupné mapovacie služby: $1',
	'maps_coordinates_missing' => 'Neboli poskytnuté žiadne súradnice.',
	'maps_geocoding_failed' => 'Nebolo možné určiť súradnice {{PLURAL:$2|nasledovnej adresy|nasledovných adries}}: $1.',
	'maps_geocoding_failed_for' => 'Nebolo možné určiť súradnice {{PLURAL:$2|nasledovnej adresy|nasledovných adries}} a {{PLURAL:$2|bola vynechaná|boli vynechané}} z mapy: $1.',
	'maps_map' => 'Mapa',
);

/** Swedish (Svenska)
 * @author Fluff
 * @author Per
 */
$messages['sv'] = array(
	'maps_desc' => 'Ger möjlighet till att visa koordinater på kartor och geokodade adresser ([http://wiki.bn2vs.com/wiki/Maps demo]).
Tillgängliga karttjänster: $1',
	'maps_coordinates_missing' => 'Inga koordinater angivna för kartan.',
	'maps_geocoding_failed' => 'Följande {{PLURAL:$2|adress|adresser}} kunde inte geokodas: $1.
Kartan kan inte visas.',
	'maps_geocoding_failed_for' => 'Följande {{PLURAL:$2|adress|adresser}}kunde inte geokodas och {{PLURAL:$2|har|har}} uteslutits från kartan: $1',
);

/** Thai (ไทย)
 * @author Woraponboonkerd
 */
$messages['th'] = array(
	'maps_desc' => 'ให้ความสามารถในการแสดงพิกัดในแผนที่ และที่อยู่ที่เป็นรหัสทางภูมิศาสตร์([http://wiki.bn2vs.com/wiki/Maps demo]).
<br />บริการแผนที่ที่มีอยู่: $1',
	'maps_coordinates_missing' => 'ไม่ได้กำหนดพิกัดของแผนที่มาให้',
);

/** Vietnamese (Tiếng Việt)
 * @author Minh Nguyen
 * @author Vinhtantran
 */
$messages['vi'] = array(
	'maps_name' => 'Bản đồ',
	'maps_desc' => 'Cung cấp khả năng hiển thị dữ liệu tọa độ trên bản đồ và địa chỉ mã địa lý ([http://wiki.bn2vs.com/wiki/Maps thử xem]).
Các dịch vụ bản đồ có sẵn: $1',
	'maps_coordinates_missing' => 'Chưa định rõ tọa độ cho bản đồ.',
	'maps_geocoding_failed' => 'Không thể tính ra mã địa lý của {{PLURAL:$2|địa chỉ|các địa chỉ}} sau: $1.
Không thể hiển thị bản đồ.',
	'maps_geocoding_failed_for' => 'Không thể tính ra mã địa lý của {{PLURAL:$2|địa chỉ|các địa chỉ}} sau nên bản đồ bỏ qua nó:
$1',
	'maps_osm' => 'OpenStreetMap',
);

