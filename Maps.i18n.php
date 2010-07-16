<?php

/**
 * Internationalization file for the Maps extension.
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
	// TODO: update demo link to the new wiki, once it has 0.6.x running.
	'maps_desc' => "Provides the ability to display coordinate data in maps, and geocode addresses ([http://wiki.bn2vs.com/wiki/Maps demo]).
Available mapping services: $1",
	'maps_map' => 'Map',
	'maps-loading-map' => 'Loading map...',
	'maps-markers' => 'Markers',

	// Coordinate handling
	'maps-abb-north' => 'N',
	'maps-abb-east' => 'E',
	'maps-abb-south' => 'S',
	'maps-abb-west' => 'W',
	'maps-latitude'  => 'Latitude:',
	'maps-longitude' => 'Longitude:',

	// Coordinate errors
	'maps-invalid-coordinates' => 'The value $1 was not recognized as a valid set of coordinates.',
	'maps_coordinates_missing' => 'No coordinates provided for the map.',
	'maps_geocoding_failed' => 'The following {{PLURAL:$2|address|addresses}} could not be geocoded: $1.',
	'maps_geocoding_failed_for' => 'The following {{PLURAL:$2|address|addresses}} could not be geocoded and {{PLURAL:$2|has|have}} been omitted from the map:
$1',
	'maps_unrecognized_coords' => 'The following {{PLURAL:$2|coordinate was|coordinates were}} not recognized: $1.',
	'maps_unrecognized_coords_for' => 'The following {{PLURAL:$2|coordinate was|coordinates were}} not recognized and {{PLURAL:$2|has|have}} been omitted from the map:
$1',
	'maps_map_cannot_be_displayed' => 'The map cannot be displayed.',

	// Distance
	'maps_invalid_distance' => 'The value $1 is not a valid distance.',

	// Geocoding
	'maps-geocoder-not-available' => 'The geocoding feature of Maps is not available. Your location can not be geocoded.',

	// Mapping services
	'maps_googlemaps2' => 'Google Maps v2',
	'maps_googlemaps3' => 'Google Maps v3',
	'maps_yahoomaps' => 'Yahoo! Maps',
	'maps_openlayers' => 'OpenLayers',
	'maps_osm' => 'OpenStreetMap',

	// Static maps
	'maps_click_to_activate' => 'Click to activate map',
	'maps_centred_on' => 'Map centered on $1, $2.',

	// Google Maps v2 overlays
	'maps_overlays' => 'Overlays',
	'maps_photos' => 'Photos',
	'maps_videos' => 'Videos',
	'maps_wikipedia' => 'Wikipedia',
	'maps_webcams' => 'Webcams'
);

/** Message documentation (Message documentation)
 * @author EugeneZelenko
 * @author Fryed-peach
 * @author Purodha
 * @author Raymond
 */
$messages['qqq'] = array(
	'maps_name' => '{{Optional}}',
	'maps_desc' => '{{desc}}

* $1: a list of available map services',
	'maps_map' => '{{Identical|Map}}',
	'maps-latitude' => '{{Identical|Latitude}}',
	'maps-longitude' => '{{Identical|Longitude}}',
	'maps_geocoding_failed_for' => '* $1 is a list
* $2 is the number of list items for PLURAL use.',
	'maps_centred_on' => '$1 and $2 are latitude and longitude.',
);

/** Bahasa Banjar (Bahasa Banjar)
 * @author Ezagren
 */
$messages['bjn'] = array(
	'maps-abb-north' => 'U',
	'maps-abb-east' => 'T',
	'maps-abb-south' => 'S',
	'maps-abb-west' => 'B',
	'maps_wikipedia' => 'Wikipidia',
);

/** Afrikaans (Afrikaans)
 * @author Naudefj
 */
$messages['af'] = array(
	'maps_map' => 'Kaart',
	'maps-abb-north' => 'N',
	'maps-abb-east' => 'O',
	'maps-abb-south' => 'S',
	'maps-abb-west' => 'W',
	'maps-latitude' => 'Breedte:',
	'maps-longitude' => 'Lengte:',
	'maps_coordinates_missing' => 'Geen koördinate is vir die kaart verskaf nie.',
	'maps_unrecognized_coords' => 'Die volgende koördinate is nie herken nie: $1.',
	'maps_unrecognized_coords_for' => 'Die volgende {{PLURAL:$2|koördinaat|koördinate}} is nie herken nie en is uit die kaart weggelaat:
$1.',
	'maps_map_cannot_be_displayed' => 'Die kaart kan nie vertoon word nie.',
	'maps_click_to_activate' => 'Kliek om die kaart te aktiveer',
	'maps_centred_on' => 'Kaart gesentreer op $1, $2.',
	'maps_photos' => "Foto's",
	'maps_videos' => "Video's",
	'maps_wikipedia' => 'Wikipedia',
	'maps_webcams' => 'Webkameras',
);

/** Gheg Albanian (Gegë)
 * @author Mdupont
 */
$messages['aln'] = array(
	'maps_desc' => 'Ofron mundësinë për të shfaqur koordinimin e të dhënave në harta, dhe adresat geocode ([http://wiki.bn2vs.com/wiki/Maps demo]). Hartës shërbimet në dispozicion: $1',
	'maps_map' => 'Hartë',
	'maps-loading-map' => 'Loading Harta ...',
	'maps-abb-north' => 'N',
	'maps-abb-east' => 'E',
	'maps-abb-south' => 'S',
	'maps-abb-west' => 'W',
	'maps-latitude' => 'Latitude:',
	'maps-longitude' => 'Gjatësi:',
	'maps-invalid-coordinates' => 'Vlera $1 nuk është njohur si një grup të vlefshme të kordinatave.',
	'maps_coordinates_missing' => 'Nuk ka koordinon parashikuara në hartë.',
	'maps_geocoding_failed' => 'Më poshtë {{PLURAL:$2|Adresa|adresat}} nuk mund të geocoded: $1.',
);

/** Arabic (العربية)
 * @author Meno25
 */
$messages['ar'] = array(
	'maps_name' => 'خرائط',
	'maps_desc' => 'يعطي إمكانية عرض معلومات التنسيق في الخرائط وعناوين الترميز الجغرافي ([http://wiki.bn2vs.com/wiki/Maps تجربة]).
خدمات الخرائط المتوفرة: $1',
	'maps_map' => 'خريطة',
	'maps-abb-north' => 'شم',
	'maps-abb-east' => 'شر',
	'maps-abb-south' => 'ج',
	'maps-abb-west' => 'غ',
	'maps-latitude' => 'دائرة العرض:',
	'maps-longitude' => 'خط الطول:',
	'maps_coordinates_missing' => 'لا إحداثيات موفرة للخريطة.',
	'maps_geocoding_failed' => '{{PLURAL:$2|العنوان التالي|العناوين التالية}} لم يمكن تكويدها جغرافيا: $1.',
	'maps_geocoding_failed_for' => '{{PLURAL:$2|العنوان التالي|العناوين التالية}} لم يمكن تكويدها جغرافيا و {{PLURAL:$2|تمت|تمت}} إزالتها من الخريطة:
$1',
	'maps_unrecognized_coords' => 'الإحداثيات التالية لم يتم التعرف عليها: $1.',
	'maps_unrecognized_coords_for' => '{{PLURAL:$2|الإحداثي التالي|الإحداثيات التالية}} لم يتم التعرف عليها و {{PLURAL:$2|تمت|تمت}} إزالتها من الخريطة:
$1',
	'maps_map_cannot_be_displayed' => 'الخريطة لا يمكن عرضها.',
	'maps_googlemaps2' => 'خرائط جوجل',
	'maps_yahoomaps' => 'خرائط ياهو!',
	'maps_openlayers' => 'أوبن لايرز',
	'maps_osm' => 'أوبن ستريت ماب',
	'maps_click_to_activate' => 'اضغط لتنشيط الخريطة',
	'maps_centred_on' => 'الخريطة مركزها في $1، $2.',
	'maps_overlays' => 'الطبقات الإضافية',
	'maps_photos' => 'صور',
	'maps_videos' => 'فيديوهات',
	'maps_wikipedia' => 'ويكيبيديا',
	'maps_webcams' => 'كاميرات الويب',
);

/** Aramaic (ܐܪܡܝܐ) */
$messages['arc'] = array(
	'maps-abb-north' => 'ܓܪܒܝܐ',
	'maps-abb-east' => 'ܡܕܢܚܐ',
	'maps-abb-south' => 'ܬܝܡܢܐ',
	'maps-abb-west' => 'ܡܥܪܒܐ',
);

/** Egyptian Spoken Arabic (مصرى)
 * @author Meno25
 */
$messages['arz'] = array(
	'maps_name' => 'خرايط',
	'maps-abb-north' => 'شمال',
	'maps-abb-east' => 'شرق',
	'maps-abb-south' => 'جنوب',
	'maps-abb-west' => 'غرب',
	'maps-latitude' => 'دوائر العرض:',
	'maps-longitude' => 'خطوط الطول:',
	'maps_googlemaps2' => 'خرايط جوجل',
	'maps_yahoomaps' => 'خرايط ياهو',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 * @author Jim-by
 * @author Wizardist
 */
$messages['be-tarask'] = array(
	'maps_desc' => 'Забясьпечвае магчымасьць адлюстраваньня каардынатных зьвестак на мапах і геаграфічнага кадаваньня адрасоў ([http://wiki.bn2vs.com/wiki/Maps дэманстрацыя]). Даступныя геаграфічныя сэрвісы: $1',
	'maps_map' => 'Мапа',
	'maps-loading-map' => 'Загрузка мапы…',
	'maps-markers' => 'Пазнакі',
	'maps-abb-north' => 'Пн.',
	'maps-abb-east' => 'У.',
	'maps-abb-south' => 'Пд.',
	'maps-abb-west' => 'З.',
	'maps-latitude' => 'Шырата:',
	'maps-longitude' => 'Даўгата:',
	'maps-invalid-coordinates' => 'Значэньне $1 зьяўляецца недапушчальным наборам каардынатаў.',
	'maps_coordinates_missing' => 'Каардынаты для мапы не пазначаныя.',
	'maps_geocoding_failed' => '{{PLURAL:$2|Наступны адрас ня можа быць геакадаваны|Наступныя адрасы ня могуць быць геакадаваныя}}: $1.
Мапа ня можа быць паказана.',
	'maps_geocoding_failed_for' => '{{PLURAL:$2|Наступны адрас ня можа быць геакадаваны і быў выдалены|Наступныя адрасы ня могуць быць геакадаваны і былі выдаленыя}} з мапы:
$1',
	'maps_unrecognized_coords' => 'Наступныя {{PLURAL:$2|каардыната не была распазнаная|каардынаты не былі распазнаныя}}: $1.',
	'maps_unrecognized_coords_for' => '{{PLURAL:$2|Наступная каардыната не была апазнаная|Наступныя каардынаты не былі апазнаныя}} і {{PLURAL:$2|яна не паказаная|яны не паказаныя}}:
$1',
	'maps_map_cannot_be_displayed' => 'Мапа ня можа быць паказаная.',
	'maps_invalid_distance' => 'Значэньне $1 — няслушная адлегласьць.',
	'maps-geocoder-not-available' => 'Магчымасьць геаграфічнага кадаваньня для мапаў недаступная. Вашае месцазнаходжаньне ня можа быць геаграфічна закадаванае.',
	'maps_click_to_activate' => 'Націсьніце для актывацыі мапы',
	'maps_centred_on' => 'Цэнтар мапы — $1, $2.',
	'maps_overlays' => 'Слаі',
	'maps_photos' => 'Фотаздымкі',
	'maps_videos' => 'Відэа',
	'maps_wikipedia' => 'Вікіпэдыя',
	'maps_webcams' => 'Ўэб-камэры',
);

/** Bulgarian (Български)
 * @author DCLXVI
 */
$messages['bg'] = array(
	'maps_map' => 'Карта',
	'maps-abb-north' => 'С',
	'maps-abb-east' => 'И',
	'maps-abb-south' => 'Ю',
	'maps-abb-west' => 'З',
	'maps_photos' => 'Снимки',
	'maps_wikipedia' => 'Уикипедия',
	'maps_webcams' => 'Уебкамери',
);

/** Breton (Brezhoneg)
 * @author Fohanno
 * @author Fulup
 * @author Y-M D
 */
$messages['br'] = array(
	'maps_desc' => "Talvezout a ra da embann daveennoù ha chomlec'hioù geokod war kartennoù ([http://wiki.bn2vs.com/wiki/Maps demo]). Servijoù kartennaouiñ hegerz : $1",
	'maps_map' => 'Kartenn',
	'maps-loading-map' => 'O kargañ ar gartenn...',
	'maps-abb-north' => 'N',
	'maps-abb-east' => 'R',
	'maps-abb-south' => 'S',
	'maps-abb-west' => 'K',
	'maps-latitude' => 'Ledred :',
	'maps-longitude' => 'Hedred :',
	'maps-invalid-coordinates' => "N'eo ket bet anavezet an talvoudenn $1 evel ul stroll a c'henurzhiennoù reizh.",
	'maps_coordinates_missing' => "N'eus bet spisaet daveenn ebet evit ar gartenn.",
	'maps_geocoding_failed' => "N'eus ket bet gallet douarkodañ ar {{PLURAL:$2|chomlec'h|chomlec'h}} da-heul : $1.
N'haller ket diskwel ar gartenn.",
	'maps_geocoding_failed_for' => "N'eus ket bet gallet douarkodañ ar {{PLURAL:$2|chomlec'h|chomlec'h}} da-heul, setu {{PLURAL:$2|n'eo|n'int}} ket bet lakaet war ar gartenn : 
$1",
	'maps_unrecognized_coords' => "N'eo ket bet anavezet an {{PLURAL:$2|daveenn|daveennoù}} da-heul : $1.",
	'maps_unrecognized_coords_for' => "N'eo ket bet anavezet an {{PLURAL:$2|daveenn|daveennoù}} da-heul ha {{PLURAL:$2|n'eo|n'int}} ket bet lakaet war ar gartenn :
$1",
	'maps_map_cannot_be_displayed' => "Ar gartenn ne c'hall ket bezañ diskwelet.",
	'maps_invalid_distance' => "An talvoud $1 n'eo ket un hed reizh anezhañ.",
	'maps-geocoder-not-available' => "N'haller ket ober gant arc'hwel geokodiñ ar c'hartennoù. N'haller ket geokodiñ ho lec'hiadur.",
	'maps_click_to_activate' => 'Klikañ evit gweredekaat  ar gartenn',
	'maps_centred_on' => 'Kartenn kreizet war $1, $2.',
	'maps_overlays' => 'Dreistlakadennoù',
	'maps_photos' => "Luc'hskeudennoù",
	'maps_videos' => 'Videoioù',
	'maps_wikipedia' => 'Wikipedia',
	'maps_webcams' => 'Kameraoù web',
);

/** Bosnian (Bosanski)
 * @author CERminator
 */
$messages['bs'] = array(
	'maps_desc' => 'Daje mogućnost prikazivanja podataka koordinata na mapama i geocode adresa ([http://wiki.bn2vs.com/wiki/Maps demo]).
Dostupne usluge mapa: $1',
	'maps_map' => 'Mapa',
	'maps-abb-north' => 'S',
	'maps-abb-east' => 'I',
	'maps-abb-south' => 'J',
	'maps-abb-west' => 'Z',
	'maps-latitude' => 'Geografska širina:',
	'maps-longitude' => 'Geografska dužina:',
	'maps_coordinates_missing' => 'Za mapu nisu navedene koordinate.',
	'maps_geocoding_failed' => '{{PLURAL:$2|Slijedeća adresa nije mogla biti geokodirana|Slijedeće adrese nisu mogle biti geokodirane}}: $1.
Mapa se ne može prikazati.',
	'maps_geocoding_failed_for' => '{{PLURAL:$2|Slijedeća adresa nije|Slijedeće adrese nisu}} mogle biti geokodiranje i {{PLURAL:$2|izostavljena je|izostavljene su}} iz mape:
$1',
	'maps_unrecognized_coords' => '{{PLURAL:$2|Slijedeća koordinata nije prepoznata|Slijedeće koordinate nisu prepoznate}}: $1.',
	'maps_unrecognized_coords_for' => '{{PLURAL:$2|Slijedeća koordinata nije|Slijedeće koordinate nisu}} prepoznate i {{PLURAL:$2|ignorirana je|ignorirane su}} na karti:
$1',
	'maps_map_cannot_be_displayed' => 'Karta se ne može prikazati.',
	'maps_click_to_activate' => 'Kliknite da aktivirate kartu',
	'maps_centred_on' => 'Karta centrirana na $1, $2.',
	'maps_overlays' => 'Slojevi',
	'maps_photos' => 'Fotografije',
	'maps_videos' => 'Video snimci',
	'maps_wikipedia' => 'Wikipedia',
	'maps_webcams' => 'Web kamere',
);

/** Catalan (Català)
 * @author Paucabot
 * @author PerroVerd
 */
$messages['ca'] = array(
	'maps_map' => 'Mapa',
	'maps-abb-north' => 'N',
	'maps-abb-east' => 'E',
	'maps-abb-south' => 'S',
	'maps-abb-west' => 'O',
	'maps-latitude' => 'Latitud:',
	'maps-longitude' => 'Longitud:',
	'maps_coordinates_missing' => "No s'han proporcionat coordenades pel mapa.",
	'maps_centred_on' => 'Mapa centrat en $1, $2.',
	'maps_overlays' => 'Capes addicionals',
	'maps_photos' => 'Fotos',
	'maps_videos' => 'Videos',
	'maps_wikipedia' => 'Wikipedia',
	'maps_webcams' => 'Cámeres web',
);

/** Chechen (Нохчийн)
 * @author Sasan700
 */
$messages['ce'] = array(
	'maps_photos' => 'Сурт',
	'maps_wikipedia' => 'Википедийа',
);

/** Czech (Česky)
 * @author Mormegil
 */
$messages['cs'] = array(
	'maps-abb-north' => 'S',
	'maps-abb-east' => 'V',
	'maps-abb-south' => 'J',
	'maps-abb-west' => 'Z',
	'maps-latitude' => 'Zeměpisná šířka:',
	'maps-longitude' => 'Zeměpisná délka:',
	'maps_overlays' => 'Překryvné vrstvy',
);

/** German (Deutsch)
 * @author DaSch
 * @author Imre
 * @author Kghbln
 * @author The Evil IP address
 */
$messages['de'] = array(
	'maps_desc' => 'Ermöglicht es, Koordinaten auf Karten anzuzeigen und Adressen zu geokodieren ([http://wiki.bn2vs.com/wiki/Maps Demonstration]).
Verfügbare Kartografie-Dienste: $1',
	'maps_map' => 'Karte',
	'maps-loading-map' => 'Karte wird geladen …',
	'maps-markers' => 'Markierungen',
	'maps-abb-north' => 'N',
	'maps-abb-east' => 'O',
	'maps-abb-south' => 'S',
	'maps-abb-west' => 'W',
	'maps-latitude' => 'Breitengrad:',
	'maps-longitude' => 'Längengrad:',
	'maps-invalid-coordinates' => 'Der Wert $1 bezeichnet kein gültiges Koordinatenpaar.',
	'maps_coordinates_missing' => 'Es wurden keine Koordinaten für die Karte angegeben.',
	'maps_geocoding_failed' => 'Die {{PLURAL:$2|folgende Adresse|folgenden Adressen}} konnten nicht geokodiert werden: $1.',
	'maps_geocoding_failed_for' => 'Die {{PLURAL:$2|folgende Adresse konnte|folgenden Adressen konnten}} nicht geokodiert werden und {{PLURAL:$2|wurde|wurden}} auf der Karte nicht berücksichtigt:
$1',
	'maps_unrecognized_coords' => 'Folgende {{PLURAL:$2|Koordinate wurde|Koordinaten wurden}} nicht erkannt: $1.',
	'maps_unrecognized_coords_for' => 'Die {{PLURAL:$2|folgende Koordinate wurde|folgenden Koordinaten wurden}} nicht erkannt und {{PLURAL:$2|wurde|wurden}} auf der Karte nicht berücksichtigt:
$1',
	'maps_map_cannot_be_displayed' => 'Diese Karte kann nicht angezeigt werden.',
	'maps_invalid_distance' => 'Der Wert $1 ist keine gültige Distanz.',
	'maps-geocoder-not-available' => 'Die Funktion Geokodierung von Karten ist nicht verfügbar. Dein Standort kann nicht geokodiert werden.',
	'maps_click_to_activate' => 'Klicken, um die Karte zu aktivieren.',
	'maps_centred_on' => 'Karte ist auf $1, $2 zentriert.',
	'maps_overlays' => 'Einblendungen',
	'maps_photos' => 'Fotos',
	'maps_videos' => 'Videos',
	'maps_wikipedia' => 'Wikipedia',
	'maps_webcams' => 'Webcams',
);

/** German (formal address) (Deutsch (Sie-Form)) */
$messages['de-formal'] = array(
	'maps-geocoder-not-available' => 'Die Funktion Geokodierung von Karten ist nicht verfügbar. Ihr Standort kann nicht geokodiert werden.',
);

/** Lower Sorbian (Dolnoserbski)
 * @author Michawiki
 */
$messages['dsb'] = array(
	'maps_desc' => 'Bitujo móžnosć koordinatowe daty w geografiskich kórtach a geokodowe adrese zwobrazniś. ([http://wiki.bn2vs.com/wiki/Maps demo]).
K dispoziciji stojece kórtowe słužby: $1',
	'maps_map' => 'Karta',
	'maps-loading-map' => 'Kórta se zacytujo...',
	'maps-markers' => 'Marki',
	'maps-abb-north' => 'PP',
	'maps-abb-east' => 'PZ',
	'maps-abb-south' => 'PD',
	'maps-abb-west' => 'PW',
	'maps-latitude' => 'Šyrina:',
	'maps-longitude' => 'Dlinina:',
	'maps-invalid-coordinates' => 'Gódnota $1 njejo se spóznała ako płaśiwa sajźba koordinatow.',
	'maps_coordinates_missing' => 'Za kórtu njejsu koordinaty pódane.',
	'maps_geocoding_failed' => 'Geokoděrowanje {{PLURAL:$2|slědujuceje adrese|slědujuceju adresowu|slědujucych adresow|slědujucych adresow}} njejo móžno było: $1. Kórta njedajo se zwobrazniś.',
	'maps_geocoding_failed_for' => 'Geokoděrowanje {{PLURAL:$2|slědujuceje adrese|slědujuceju adresowu|slědujucych adresow|slědujucych adresow}} njejo móžno było a togodla toś {{PLURAL:$2|ta adresa wuwóstaja|tej adresy wuwóstajotej|te adrese wuwóstajaju|te adresy wuwóstajaju}} se na kórśe: $1',
	'maps_unrecognized_coords' => '{{PLURAL:$2|Slědujuca koordinata njejo se spóznała|Slědujucej koordinaśe njejstej se spóznałej|Slědujuce koordinaty njejsu se spóznali|Slědujuce koordinaty njejsu se spóznali}}: $1.',
	'maps_unrecognized_coords_for' => '{{PLURAL:$2|Slědujuca koordinata njejo se spóznała|Slědujucej koordinaśe stej se spóznałej|Slědujuce koordinaty su se spóznali|Slědujuce koordinaty su se spóznali}} a {{PLURAL:$2|njejo se wuwóstajiła|njejstej se wuwóstajiłej|njejsu wuwóstajili|njejsu se wuwóstajili}} na kórśe: $1',
	'maps_map_cannot_be_displayed' => 'Kórta njedajo se zwobrazniś.',
	'maps_invalid_distance' => 'Gódnota $1 njejo płaśiwa distanca.',
	'maps-geocoder-not-available' => 'Funkcija geokoděrowanja Kórtow njestoj k dispoziciji, twójo městno njedajo se geokoděrowaś.',
	'maps_click_to_activate' => 'Klikni, aby kórtu aktiwěrował',
	'maps_centred_on' => 'Kórta na $1, $2 centrěrowana.',
	'maps_overlays' => 'Pśekšyśa',
	'maps_photos' => 'Fota',
	'maps_videos' => 'Wideo',
	'maps_wikipedia' => 'Wikipedija',
	'maps_webcams' => 'Webcamy',
);

/** Greek (Ελληνικά)
 * @author Omnipaedista
 * @author ZaDiak
 */
$messages['el'] = array(
	'maps-abb-north' => 'Β',
	'maps-abb-east' => 'Α',
	'maps-abb-south' => 'Ν',
	'maps-abb-west' => 'Δ',
	'maps-latitude' => 'Γεωγραφικό πλάτος:',
	'maps-longitude' => 'Γεωγραφικό μήκος:',
	'maps_coordinates_missing' => 'Καμία συντεταγμένη δεν παρασχέθηκε για τον χάρτη.',
	'maps_photos' => 'Φωτογραφίες',
	'maps_videos' => 'Βίντεο',
	'maps_wikipedia' => 'Βικιπαίδεια',
);

/** British English (British English)
 * @author Bruce89
 * @author Reedy
 */
$messages['en-gb'] = array(
	'maps_desc' => 'Provides the ability to display coordinate data in maps, and geocode addresses ([http://wiki.bn2vs.com/wiki/Maps demo]).
Available mapping services: $1',
	'maps_map' => 'Map',
	'maps-loading-map' => 'Loading map...',
	'maps-abb-north' => 'N',
	'maps-abb-east' => 'E',
	'maps-abb-south' => 'S',
	'maps-abb-west' => 'W',
	'maps-latitude' => 'Latitude:',
	'maps-longitude' => 'Longitude:',
	'maps-invalid-coordinates' => 'The value $1 was not recognised as a valid set of coordinates.',
	'maps_coordinates_missing' => 'No coordinates provided for the map.',
	'maps_geocoding_failed' => 'The following {{PLURAL:$2|address|addresses}} could not be geocoded: $1.',
	'maps_geocoding_failed_for' => 'The following {{PLURAL:$2|address|addresses}} could not be geocoded and {{PLURAL:$2|has|have}} been omitted from the map:
$1',
	'maps_unrecognized_coords' => 'The following {{PLURAL:$2|coordinate was|coordinates were}} not recognised: $1.',
	'maps_unrecognized_coords_for' => 'The following {{PLURAL:$2|coordinate was|coordinates were}} not recognised and {{PLURAL:$2|has|have}} been omitted from the map:
$1',
	'maps_map_cannot_be_displayed' => 'The map cannot be displayed.',
	'maps-geocoder-not-available' => 'The geocoding feature of Maps is not available. Your location can not be geocoded.',
	'maps_click_to_activate' => 'Click to activate map',
	'maps_centred_on' => 'Map centred on $1, $2.',
	'maps_overlays' => 'Overlays',
	'maps_photos' => 'Photos',
	'maps_videos' => 'Videos',
	'maps_wikipedia' => 'Wikipedia',
	'maps_webcams' => 'Webcams',
);

/** Esperanto (Esperanto)
 * @author Yekrats
 */
$messages['eo'] = array(
	'maps-abb-north' => 'N',
	'maps-abb-east' => 'O',
	'maps-abb-south' => 'S',
	'maps-abb-west' => 'U',
	'maps-latitude' => 'Latitudo:',
	'maps-longitude' => 'Longitudo:',
	'maps_map_cannot_be_displayed' => 'La mapo ne esti montrebla.',
	'maps_click_to_activate' => 'Klaku aktivigi mapon',
	'maps_photos' => 'Fotoj',
	'maps_wikipedia' => 'Vikipedio',
);

/** Spanish (Español)
 * @author Crazymadlover
 * @author Dferg
 * @author Diego Grez
 * @author Imre
 * @author Locos epraix
 * @author Pertile
 * @author Peter17
 * @author Translationista
 */
$messages['es'] = array(
	'maps_desc' => 'Proporciona la capacidad de mostrar los datos de coordenadas en los mapas y geocodifica direcciones ([http://wiki.bn2vs.com/wiki/Maps demo]). Servicios de mapeo disponibles: $1',
	'maps_map' => 'Mapa',
	'maps-loading-map' => 'Cargando mapa...',
	'maps-markers' => 'Marcadores',
	'maps-abb-north' => 'N',
	'maps-abb-east' => 'E',
	'maps-abb-south' => 'S',
	'maps-abb-west' => 'O',
	'maps-latitude' => 'Latitud:',
	'maps-longitude' => 'Longitud:',
	'maps-invalid-coordinates' => 'El valor $1 no fue reconocido como un conjunto válido de coordenadas.',
	'maps_coordinates_missing' => 'Sin coordenadas provistas para el mapa.',
	'maps_geocoding_failed' => 'Las siguientes {{PLURAL:$2|dirección|direcciones}}  no han podido ser geocodificadas: $1.
No se puede mostrar el mapa.',
	'maps_geocoding_failed_for' => 'No fue posible geocodificar {{PLURAL:$2|la siguiente dirección, que ha sido omitida|las siguientes direcciones, que han sido omitidas}} del mapa:$1.',
	'maps_unrecognized_coords' => '{{PLURAL:$2|La siguiente coordenada no fue reconocida|Las siguientes coordenadas no fueron reconocidas}}: $1.',
	'maps_unrecognized_coords_for' => '{{PLURAL:$2|La coordenada siguiente no es reconocida|Las coordenadas siguientes no son reconocidas}} y PLURAL:$2|{{han sido omitidas|han sido omitidas}} del mapa :$1',
	'maps_map_cannot_be_displayed' => 'No se puede mostrar el mapa.',
	'maps_invalid_distance' => 'El valor $1 no es una distancia válida.',
	'maps-geocoder-not-available' => 'La funcionalidad de geocodificación de Maps no está disponible. Su ubicación no puede ser geocodificada.',
	'maps_click_to_activate' => 'Haz clic para activar el mapa',
	'maps_centred_on' => 'Mapa centrado en $1, $2.',
	'maps_overlays' => 'Superposiciones',
	'maps_photos' => 'Fotos',
	'maps_videos' => 'Videos',
	'maps_wikipedia' => 'Wikipedia',
	'maps_webcams' => 'Cámaras Web',
);

/** Estonian (Eesti)
 * @author Hendrik
 */
$messages['et'] = array(
	'maps-abb-north' => 'N',
	'maps-abb-east' => 'E',
	'maps-abb-south' => 'S',
	'maps-abb-west' => 'W',
	'maps-latitude' => 'Laiuskraad:',
	'maps-longitude' => 'Pikkuskraad:',
);

/** Basque (Euskara)
 * @author Kobazulo
 */
$messages['eu'] = array(
	'maps-abb-north' => 'I',
	'maps-abb-east' => 'E',
	'maps-abb-south' => 'H',
	'maps-abb-west' => 'M',
	'maps-latitude' => 'Latitudea:',
	'maps-longitude' => 'Longitudea:',
	'maps_coordinates_missing' => 'Ez dago koordenaturik maparentzat.',
);

/** Finnish (Suomi)
 * @author Cimon Avaro
 * @author Crt
 * @author Str4nd
 * @author ZeiP
 */
$messages['fi'] = array(
	'maps_desc' => 'Mahdollistaa koordinaattitiedon esittämisen karttoina ja osoitteiden geokoodauksen ([http://wiki.bn2vs.com/wiki/Maps demo]).
Käytettävissä olevat karttapalvelut: $1',
	'maps_map' => 'Kartta',
	'maps-loading-map' => 'Ladataan karttaa...',
	'maps-abb-north' => 'Pohj.',
	'maps-abb-east' => 'It.',
	'maps-abb-south' => 'Etel.',
	'maps-abb-west' => 'Länt.',
	'maps-latitude' => 'Korkeusaste:',
	'maps-longitude' => 'Pituuspiiri:',
	'maps-invalid-coordinates' => 'Arvoa $1 ei tunnistettu oikeaksi koordinaattisarjaksi.',
	'maps_coordinates_missing' => 'Karttaa varten ei tarjottu koordinaatteja.',
	'maps_geocoding_failed' => '{{PLURAL:$2|Seuraavaa osoitetta|Seuraavia osoitteita}} ei voitu geokoodata: $1.
Karttaa ei voida näyttää.',
	'maps_geocoding_failed_for' => '{{PLURAL:$2|Seuraavaa osoitetta|Seuraavia osoitteita}} ei voitu geokoodata ja {{PLURAL:$2|on|ovat}} jätetty kartalta: $1',
	'maps_unrecognized_coords' => '{{PLURAL:$2|Seuraavaa koordinaattia|Seuraavia koordinaatteja}} ei tunnistettu: $1.',
	'maps_unrecognized_coords_for' => '{{PLURAL:$2|Seuraavaa koordinaattia|Seuraavia koordinaatteja}} ei tunnistettu ja {{PLURAL:$2|se|ne}} on jätetty pois kartasta:
$1',
	'maps_map_cannot_be_displayed' => 'Karttaa ei voida näyttää.',
	'maps_click_to_activate' => 'Napsauta aktivoidaksesi kartan',
	'maps_centred_on' => 'Kartta keskitetty kohtaan $1, $2.',
	'maps_overlays' => 'Kerrokset',
	'maps_photos' => 'Kuvat',
	'maps_videos' => 'Videot',
	'maps_wikipedia' => 'Wikipedia',
	'maps_webcams' => 'Web-kamerat',
);

/** French (Français)
 * @author Crochet.david
 * @author IAlex
 * @author Jean-Frédéric
 * @author McDutchie
 * @author Peter17
 * @author PieRRoMaN
 * @author Verdy p
 */
$messages['fr'] = array(
	'maps_name' => 'Cartes',
	'maps_desc' => 'Permet d’afficher des coordonnées dans des cartes, ainsi que des adresses géocodées ([http://wiki.bn2vs.com/wiki/Maps démonstration]).
Services de cartographie disponibles : $1',
	'maps_map' => 'Carte',
	'maps-loading-map' => 'Chargement de la carte...',
	'maps-markers' => 'Marqueurs',
	'maps-abb-north' => 'N',
	'maps-abb-east' => 'E',
	'maps-abb-south' => 'S',
	'maps-abb-west' => 'O',
	'maps-latitude' => 'Latitude :',
	'maps-longitude' => 'Longitude :',
	'maps-invalid-coordinates' => "La valeur $1 n'a pas été reconnue comme un ensemble de coordonnées valable.",
	'maps_coordinates_missing' => "Aucune coordonnée n'a été fournie pour le plan.",
	'maps_geocoding_failed' => "{{PLURAL:$2|L′adresse suivante n'as pu être géocodée|Les adresses suivantes n'ont pas pu être géocodées}} : $1.
Le plan ne peut pas être affiché.",
	'maps_geocoding_failed_for' => '{{PLURAL:$2|L′adresse suivante n’as pu être géocodée|Les adresses suivantes n’ont pas pu être géocodées}} et {{PLURAL:$2|n’est pas affichée|ne sont pas affichées}} sur le plan : $1',
	'maps_unrecognized_coords' => "{{PLURAL:$2|La coordonnée suivante n'a pas été reconnue|Les coordonnées suivantes n'ont pas été reconnues}} : $1.",
	'maps_unrecognized_coords_for' => "{{PLURAL:$2|La coordonnée suivante n'a pas été reconnue|Les coordonnées suivantes n'ont pas été reconnues}} et {{PLURAL:$2|a été omise|ont été omises}} sur la carte :
$1",
	'maps_map_cannot_be_displayed' => 'La carte ne peut pas être affichée.',
	'maps_invalid_distance' => 'La valeur $1 n’est pas une distance valide.',
	'maps-geocoder-not-available' => "La fonctionnalité géocodage des cartes n'est pas disponible. Votre emplacement ne peut être géocodé.",
	'maps_click_to_activate' => 'Cliquer pour activer la carte',
	'maps_centred_on' => 'Carte centrée sur $1, $2.',
	'maps_overlays' => 'Superpositions',
	'maps_photos' => 'Photos',
	'maps_videos' => 'Vidéos',
	'maps_wikipedia' => 'Wikipédia',
	'maps_webcams' => 'Webcams',
);

/** Franco-Provençal (Arpetan) */
$messages['frp'] = array(
	'maps-abb-north' => 'B',
	'maps-abb-east' => 'L',
	'maps-abb-south' => 'M',
	'maps-abb-west' => 'P',
	'maps-latitude' => 'Latituda :',
	'maps-longitude' => 'Longituda :',
);

/** Friulian (Furlan)
 * @author Klenje
 */
$messages['fur'] = array(
	'maps_desc' => 'Al furnìs la possibilitât di mostrâ i dâts de coordinadis e lis direzions geocodificadis intune mape ([http://wiki.bn2vs.com/wiki/Maps demo]).
Servizis di mapis disponibii: $1',
	'maps_map' => 'Mape',
	'maps_coordinates_missing' => 'Nissune coordenade furnide pe mape.',
	'maps_geocoding_failed' => '{{PLURAL:$2|La direzion ca sot no pues jessi geocodificade|Lis direzions ca sot no puedin jessi geocodificadis}}: $1.
La mape no pues jessi mostrade.',
	'maps_geocoding_failed_for' => '{{PLURAL:$2|La direzion|Lis direzions}} ca sot no {{PLURAL:$2|pues|puedin}} jessi {{PLURAL:$2|geocodificade|geocodificadis}} e  {{PLURAL:$2|no je mostrade|no son mostradis}} te mape:
$1',
);

/** Galician (Galego)
 * @author Gallaecio
 * @author Toliño
 */
$messages['gl'] = array(
	'maps_desc' => 'Proporciona a capacidade de mostrar datos de coordenadas en mapas, e enderezos xeocodificados ([http://wiki.bn2vs.com/wiki/Maps demostración]).
Servizos de cartografía dispoñibles: $1',
	'maps_map' => 'Mapa',
	'maps-loading-map' => 'Cargando o mapa...',
	'maps-markers' => 'Marcadores',
	'maps-abb-north' => 'N',
	'maps-abb-east' => 'L',
	'maps-abb-south' => 'S',
	'maps-abb-west' => 'O',
	'maps-latitude' => 'Latitude:',
	'maps-longitude' => 'Lonxitude:',
	'maps-invalid-coordinates' => 'O valor $1 non foi recoñecido como un conxunto de coordenadas válido.',
	'maps_coordinates_missing' => 'Non se proporcionou ningunha coordenada para o mapa.',
	'maps_geocoding_failed' => '{{PLURAL:$2|O seguinte enderezo non se puido xeocodificar|Os seguintes enderezos non se puideron xeocodificar}}: $1.
O mapa non se pode mostrar.',
	'maps_geocoding_failed_for' => '{{PLURAL:$2|O seguinte enderezo non se puido xeocodificar|Os seguintes enderezos non se puideron xeocodificar}} e {{PLURAL:$2|omitiuse|omitíronse}} no mapa: $1.',
	'maps_unrecognized_coords' => 'Non se {{PLURAL:$2|recoñeceu a seguinte coordenada|recoñeceron as seguintes coordenadas}}: $1.',
	'maps_unrecognized_coords_for' => 'Non se {{PLURAL:$2|recoñeceu a seguinte coordenada|recoñeceron as seguintes coordenadas}} e {{PLURAL:$2|foi omitida|foron omitidas}} do mapa:
$1',
	'maps_map_cannot_be_displayed' => 'O mapa non se pode mostrar.',
	'maps_invalid_distance' => 'O valor $1 non é unha distancia válida.',
	'maps-geocoder-not-available' => 'A funcionalidade de xeocodificación de mapas non está dispoñible; non se pode xeocodificar a súa situación.',
	'maps_click_to_activate' => 'Prema para activar o mapa',
	'maps_centred_on' => 'Mapa centrado en $1, $2.',
	'maps_overlays' => 'Sobreposicións',
	'maps_photos' => 'Fotos',
	'maps_videos' => 'Vídeos',
	'maps_wikipedia' => 'Wikipedia',
	'maps_webcams' => 'Cámaras web',
);

/** Ancient Greek (Ἀρχαία ἑλληνικὴ) */
$messages['grc'] = array(
	'maps-abb-north' => 'Β',
	'maps-abb-east' => 'Α',
	'maps-abb-south' => 'Ν',
	'maps-abb-west' => 'Δ',
	'maps-latitude' => 'Πλάτος γεωγραφικόν:',
	'maps-longitude' => 'Μῆκος γεωγραφικόν:',
);

/** Swiss German (Alemannisch)
 * @author Als-Holder
 */
$messages['gsw'] = array(
	'maps_desc' => 'Ergänzt d Megligkeit Koordinatedate in Charte un Geocodeadrässe aazzeige. Verfiegbari Chartedienscht: $1. [http://www.mediawiki.org/wiki/Extension:Maps Dokumäntation]. [http://wiki.bn2vs.com/wiki/Maps Demo]',
	'maps_map' => 'Charte',
	'maps-loading-map' => 'Am Lade vu dr Charte ...',
	'maps-markers' => 'Markierige',
	'maps-abb-north' => 'N',
	'maps-abb-east' => 'O',
	'maps-abb-south' => 'S',
	'maps-abb-west' => 'W',
	'maps-latitude' => 'Breiti:',
	'maps-longitude' => 'Lengi:',
	'maps-invalid-coordinates' => 'Dr Wärt $1 isch nit erkännt wore as giltige Satz vu Koordinate.',
	'maps_coordinates_missing' => 'S git kei Koordinate fir die Charte.',
	'maps_geocoding_failed' => 'Die {{PLURAL:$2|Adräss het|Adräss hän}} nit chenne georeferänziert wäre: $1. D Charte cha nit aazeigt wäre.',
	'maps_geocoding_failed_for' => 'Die {{PLURAL:$2|Adräss het|Adrässe hän}} nit chenne georeferänziert wäre un {{PLURAL:$2|isch|sin}} us dr Charte uusegnuu wore: $1',
	'maps_unrecognized_coords' => 'Die {{PLURAL:$2|Koordinate isch|Koordinate sin}} nit erkannt wore: $1.',
	'maps_unrecognized_coords_for' => '{{PLURAL:$2|Die Koordinate isch nit erkannt wore un isch|Die Koordinate sin nit erkannt wore un sin}} wäge däm uusegnuu wore us dr Charte:
$1',
	'maps_map_cannot_be_displayed' => 'D Charte cha nit aazeigt wäre.',
	'maps_invalid_distance' => 'Dr Wärt $1 isch kei giltige Abstand.',
	'maps-geocoder-not-available' => 'S Geokodierigs-Feature vu däre Charte isch nit verfiegbar, Dyy Ort cha nit geokodiert wäre.',
	'maps_click_to_activate' => 'Klick go d Charte aktiviere',
	'maps_centred_on' => 'Charte zäntriert uf $1, $2.',
	'maps_overlays' => 'Overlay',
	'maps_photos' => 'Foto',
	'maps_videos' => 'Video',
	'maps_wikipedia' => 'Wikipedia',
	'maps_webcams' => 'Webcam',
);

/** Hebrew (עברית)
 * @author Rotemliss
 * @author YaronSh
 * @author Yonidebest
 */
$messages['he'] = array(
	'maps_desc' => 'הוספת האפשרות להצגת נתוני קואורדינטות במפות וכתובות geocode ([http://wiki.bn2vs.com/wiki/Maps demo]).
שירותי המיפוי הזמינים: $1',
	'maps_map' => 'מפה',
	'maps-loading-map' => 'המפה נטענת...',
	'maps-abb-north' => "צפ'",
	'maps-abb-east' => "מז'",
	'maps-abb-south' => "דר'",
	'maps-abb-west' => "מע'",
	'maps-latitude' => 'קו רוחב:',
	'maps-longitude' => 'קו אורך:',
	'maps-invalid-coordinates' => 'הערך $1 לא זוהה כסדרת קואורדינטות תקינה.',
	'maps_coordinates_missing' => 'לא סופקו קואורדינטות למפה.',
	'maps_geocoding_failed' => 'לא ניתן לייצר geocode עבור {{PLURAL:$2|הכתובת הבאה|הכתובות הבאות}}: $1.
לא ניתן להציג את המפה.',
	'maps_geocoding_failed_for' => 'לא ניתן לייצר geocode עבור {{PLURAL:$2|הכתובת הבאה|הכתובות הבאות}}, ולכן {{PLURAL:$2|היא הושמטה|הן הושמטו}} מהמפה:
$1',
	'maps_unrecognized_coords' => '{{PLURAL:$2|הקואורדינטה הבאה אינה מזוהה|הקואורדינטות הבאות אינן מזוהות}}: $1.',
	'maps_map_cannot_be_displayed' => 'לא ניתן להציג את המפה.',
	'maps_invalid_distance' => 'הערך $1 הוא לא מרחק חוקי.',
	'maps-geocoder-not-available' => 'הקידוד הגיאוקרטוגרפי של מפות אינו זמין. לא ניתן לקודד את המיקום שנבחר.',
	'maps_click_to_activate' => 'יש ללחוץ כדי להפעיל את המפה',
	'maps_centred_on' => 'המפה ממורכזת סביב $1,$2',
	'maps_overlays' => 'שכבות',
	'maps_photos' => 'תמונות',
	'maps_videos' => 'סרטוני וידאו',
	'maps_wikipedia' => 'ויקיפדיה',
	'maps_webcams' => 'מצלמות אינטרנט',
);

/** Hindi (हिन्दी) */
$messages['hi'] = array(
	'maps-abb-north' => 'N',
	'maps-abb-east' => 'E',
	'maps-abb-south' => 'S',
	'maps-abb-west' => 'W',
	'maps-latitude' => 'अक्षांश:',
	'maps-longitude' => 'रेखांश:',
);

/** Croatian (Hrvatski)
 * @author Ex13
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
	'maps-loading-map' => 'Karta so začituje...',
	'maps-markers' => 'Marki',
	'maps-abb-north' => 'S',
	'maps-abb-east' => 'W',
	'maps-abb-south' => 'J',
	'maps-abb-west' => 'Z',
	'maps-latitude' => 'Šěrina:',
	'maps-longitude' => 'Dołhosć:',
	'maps-invalid-coordinates' => 'Hódnota $1 njebu jako płaćiwu sadźbu koordinatow spóznata.',
	'maps_coordinates_missing' => 'Za kartu njejsu koordinaty podate.',
	'maps_geocoding_failed' => 'Geokodowanje {{PLURAL:$2|slědowaceje adresy|slědowaceju adresow|slědowacych adresow|slědowacych adresow}} njebě móžno: $1. Karta njeda so zwobraznić.',
	'maps_geocoding_failed_for' => 'Geokodowanje {{PLURAL:$2|slědowaceje adresy|slědowaceju adresow|slědowacych adresow|slědowacych adresow}} njebě móžno a {{PLURAL:$2|tuta adresa|tutej adresy|tute adresy|tute adresy}} so na karće {{PLURAL:$2|wuwostaja|wuwostajetej|wuwostajeja|wuwostajeja}}: $1',
	'maps_unrecognized_coords' => '{{PLURAL:$2|Slědowaca koordinata njebu spóznana|Slědowacej koordinaće njebuštej spóznanej|Slědowace koordinaty njebuchu spóznane|Slědowace koordinaty njebuchu spóznane}}: $1.',
	'maps_unrecognized_coords_for' => '{{PLURAL:$2|Slědowaca koordinata njebu spóznana|Slědowacej koordinaće njebuštej spóznanej|Slědowace koordinaty njebuchu spóznane|Slědowace koordinaty njebuchu spóznane}} a {{PLURAL:$2|bu na karće wuwostajena|buštej na karće wuwostajenej|buchu na karće wuwostajene|buchu na karće wuwostajene}}: $1',
	'maps_map_cannot_be_displayed' => 'Karta njeda so zwobraznić.',
	'maps_invalid_distance' => 'Hódnota $1 płaćiwa distanca njeje.',
	'maps-geocoder-not-available' => 'Funkcija geokodowanja Kartow k dispoziciji njesteji, twoje městno njehodźi so geokodować.',
	'maps_click_to_activate' => 'Klikń, zo by kartu aktiwizował',
	'maps_centred_on' => 'Karta na $1, $2 centrowana.',
	'maps_overlays' => 'Naworštowanja',
	'maps_photos' => 'Fota',
	'maps_videos' => 'Wideja',
	'maps_wikipedia' => 'Wikipedija',
	'maps_webcams' => 'Webcamy',
);

/** Haitian (Kreyòl ayisyen) */
$messages['ht'] = array(
	'maps-abb-north' => 'N',
	'maps-abb-east' => 'E',
	'maps-abb-south' => 'S',
	'maps-abb-west' => 'W',
);

/** Hungarian (Magyar)
 * @author Dani
 * @author Glanthor Reviol
 */
$messages['hu'] = array(
	'maps_desc' => 'Lehetővé teszi koordinátaadatok és geokódolt címek megjelenítését térképeken ([http://wiki.bn2vs.com/wiki/Maps demó]). Elérhető térképszolgáltatások: $1',
	'maps_map' => 'Térkép',
	'maps-loading-map' => 'Térkép betöltése…',
	'maps-abb-north' => 'É',
	'maps-abb-east' => 'K',
	'maps-abb-south' => 'D',
	'maps-abb-west' => 'Ny',
	'maps-latitude' => 'Földrajzi szélesség:',
	'maps-longitude' => 'Földrajzi hosszúság:',
	'maps-invalid-coordinates' => 'A(z) „$1” érték nem érvényes koordinátacsoport.',
	'maps_coordinates_missing' => 'Nincsenek megadva koordináták a térképhez.',
	'maps_geocoding_failed' => 'A következő {{PLURAL:$2|cím|címek}} nem geokódolhatók: $1.
A térképet nem lehet megjeleníteni.',
	'maps_geocoding_failed_for' => 'A következő {{PLURAL:$2|cím nem geokódolható|címek nem geokódolhatóak}}, és nem {{PLURAL:$2|szerepel|szerepelnek}} a térképen:
$1',
	'maps_unrecognized_coords' => 'A következő {{PLURAL:$2|koordinátát|koordinátákat}} nem sikerült felismerni: $1.',
	'maps_unrecognized_coords_for' => 'A következő {{PLURAL:$2|koordinátát|koordinátákat}} nem sikerült felismerni, és el {{PLURAL:$2|lett|lettek}} távolítva a térképről: $1',
	'maps_map_cannot_be_displayed' => 'A térképet nem sikerült megjeleníteni.',
	'maps-geocoder-not-available' => 'A térképek kiterjesztés geokódoló funkciója nem elérhető. A tartózkodási helyed nem geokódolható.',
	'maps_click_to_activate' => 'Kattints a térkép aktiválásához',
	'maps_centred_on' => 'Térkép középre igazítva a következő koordináták alapján: $1, $2.',
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
	'maps_map' => 'Carta',
	'maps-loading-map' => 'Cargamento del carta…',
	'maps-markers' => 'Marcatores',
	'maps-abb-north' => 'N',
	'maps-abb-east' => 'E',
	'maps-abb-south' => 'S',
	'maps-abb-west' => 'W',
	'maps-latitude' => 'Latitude:',
	'maps-longitude' => 'Longitude:',
	'maps-invalid-coordinates' => 'Le valor $1 non es recognoscite qua coordinatas valide.',
	'maps_coordinates_missing' => 'Nulle coordinata providite pro le mappa.',
	'maps_geocoding_failed' => 'Le sequente {{PLURAL:$2|adresse|adresses}} non poteva esser geocodificate: $1.
Le mappa non pote esser monstrate.',
	'maps_geocoding_failed_for' => 'Le sequente {{PLURAL:$2|adresse|adresses}} non poteva esser geocodificate e ha essite omittite del mappa:
$1',
	'maps_unrecognized_coords' => 'Le sequente {{PLURAL:$2|coordinata|coordinatas}} non esseva recognoscite: $1.',
	'maps_unrecognized_coords_for' => 'Le sequente {{PLURAL:$2|coordinata|coordinatas}} non esseva recognoscite e ha essite omittite del carta:
$1',
	'maps_map_cannot_be_displayed' => 'Le carta on pote esser monstrate.',
	'maps_invalid_distance' => 'Le valor $1 non es un distantia valide.',
	'maps-geocoder-not-available' => 'Le function de geocodification de Maps non es disponibile; tu loco non pote esser geocodificate.',
	'maps_click_to_activate' => 'Clicca pro activar le carta',
	'maps_centred_on' => 'Carta centrate super $1, $2.',
	'maps_overlays' => 'Superpositiones',
	'maps_photos' => 'Photos',
	'maps_videos' => 'Videos',
	'maps_wikipedia' => 'Wikipedia',
	'maps_webcams' => 'Cameras web',
);

/** Indonesian (Bahasa Indonesia)
 * @author Bennylin
 * @author Farras
 * @author Irwangatot
 * @author IvanLanin
 */
$messages['id'] = array(
	'maps_desc' => "Memampukan tampilan data koordinat pada peta, dan alamat ''geocode'' ([http://wiki.bn2vs.com/wiki/Maps demo]). 
Layanan pemetaan yang tersedia: $1",
	'maps_map' => 'Peta',
	'maps-abb-north' => 'U',
	'maps-abb-east' => 'T',
	'maps-abb-south' => 'S',
	'maps-abb-west' => 'B',
	'maps-latitude' => 'Lintang:',
	'maps-longitude' => 'Bujur:',
	'maps_coordinates_missing' => 'Tidak koordinat yang disediakan bagi peta.',
	'maps_geocoding_failed' => '{{PLURAL:$2|alamat|alamat}} berikut tidak dapat di Geocode: $1. 
Peta tidak dapat ditampilkan.',
	'maps_geocoding_failed_for' => '{{PLURAL:$2|alamat|alamat}} berikut tidak dapat di Geocode dan  {{PLURAL:$2|telah|telah}} dihilangkan dari peta: $1',
	'maps_unrecognized_coords' => 'Koordinat berikut tidak dikenali: $1.',
	'maps_unrecognized_coords_for' => 'Koordinat berikut tidak dikenali dan {{PLURAL:$2|telah|telah}} diabaikan dari peta:
$1',
	'maps_map_cannot_be_displayed' => 'Peta tak dapat ditampilkan.',
	'maps_click_to_activate' => 'Klik untuk mengaktifkan peta',
	'maps_overlays' => 'Hamparan',
	'maps_photos' => 'Foto',
	'maps_videos' => 'Video',
	'maps_wikipedia' => 'Wikipedia',
	'maps_webcams' => 'Kamera web',
);

/** Igbo (Igbo)
 * @author Ukabia
 */
$messages['ig'] = array(
	'maps_map' => 'Otú Uzọr',
	'maps_photos' => 'Onyònyò',
	'maps_videos' => 'Enyónyó-na-jé',
	'maps_wikipedia' => 'Wikipedia',
);

/** Italian (Italiano)
 * @author Civvì
 * @author HalphaZ
 */
$messages['it'] = array(
	'maps_desc' => "Fornisce la possibilità di visualizzare i dati di coordinate su mappe e la geocodifica di indirizzi ([http://wiki.bn2vs.com/wiki/Demo dell'estensione Maps]).
Servizi di cartografia disponibili: $1",
	'maps_map' => 'Mappa',
	'maps-loading-map' => 'Caricamento mappa ...',
	'maps-abb-north' => 'N',
	'maps-abb-east' => 'E',
	'maps-abb-south' => 'S',
	'maps-abb-west' => 'O',
	'maps-latitude' => 'Latitudine:',
	'maps-longitude' => 'Longitudine:',
	'maps-invalid-coordinates' => 'Il valore $1 non è stato riconosciuto come un set di coordinate valido.',
	'maps_coordinates_missing' => 'Non sono state fornite coordinate per la mappa',
	'maps_geocoding_failed' => 'Non è stato possibile effettuare la geocodifica per {{PLURAL:$2|il seguente indirizzo|i seguenti indirizzi}}: $1.',
	'maps_geocoding_failed_for' => 'Non è stato possibile effettuare la geocodifica {{PLURAL:$2|del seguente indirizzo|dei seguenti indirizzi}} che {{PLURAL:$2|è stato omesso|sono stati omessi}} dalla mappa: $1.',
	'maps_unrecognized_coords' => '{{PLURAL:$2|La seguente coordinata|Le seguenti coordinate}} non sono state riconosciute: $1.',
	'maps_unrecognized_coords_for' => '{{PLURAL:$2|La seguente coordinata|Le seguenti coordinate}} {{PLURAL:$2|non è stata riconosciuta ed è stata omessa|non sono state riconosciute e sono state omesse}} dalla mappa: $1.',
	'maps_map_cannot_be_displayed' => 'La mappa non può essere visualizzata.',
	'maps_invalid_distance' => 'Il valore $1 non è una distanza valida.',
	'maps-geocoder-not-available' => "La funzionalità di geocodifica dell'estensione Maps non è disponibile. La tua posizione non può essere geocodificata.",
	'maps_click_to_activate' => 'Clicca per attivare la mappa.',
	'maps_centred_on' => 'Mappa centrata su $1, $2.',
	'maps_overlays' => 'Overlay',
	'maps_photos' => 'Foto',
	'maps_videos' => 'Video',
	'maps_wikipedia' => 'Wikipedia',
	'maps_webcams' => 'Webcam',
);

/** Japanese (日本語)
 * @author Aotake
 * @author Fryed-peach
 * @author 青子守歌
 */
$messages['ja'] = array(
	'maps_desc' => '地図上に座標データを表示し、住所を座標データに変換する機能を提供する ([http://wiki.bn2vs.com/wiki/Maps 実演])。次の地図サービスに対応します: $1',
	'maps_map' => '地図',
	'maps-loading-map' => '地図を読み込み中…',
	'maps-abb-north' => '北',
	'maps-abb-east' => '東',
	'maps-abb-south' => '南',
	'maps-abb-west' => '西',
	'maps-latitude' => '緯度:',
	'maps-longitude' => '経度:',
	'maps-invalid-coordinates' => '値 $1 は座標の有効な組み合わせとして認識されませんでした。',
	'maps_coordinates_missing' => '地図に座標が指定されていません。',
	'maps_geocoding_failed' => '指定された{{PLURAL:$2|住所}}の座標への変換に失敗しました。 $1。地図は表示できません。',
	'maps_geocoding_failed_for' => '指定された{{PLURAL:$2|住所|複数の住所}}の座標への変換に失敗したため、それらを地図から除外して表示します。$1',
	'maps_unrecognized_coords' => '以下の{{PLURAL:$2|座標}}は認識されませんでした: $1',
	'maps_unrecognized_coords_for' => '以下の{{PLURAL:$2|座標}}は認識されなかったため、地図から省かれています:
$1',
	'maps_map_cannot_be_displayed' => 'この地図は表示できません。',
	'maps_invalid_distance' => '値 $1 は距離として有効ではありません。',
	'maps-geocoder-not-available' => '地図のジオコーディング機能は利用できません。指定した位置をジオコーディングできません。',
	'maps_click_to_activate' => 'クリックして地図をアクティブに',
	'maps_centred_on' => '地図の中心は $1、$2。',
	'maps_overlays' => 'オーバーレイ',
	'maps_photos' => '写真',
	'maps_videos' => '動画',
	'maps_wikipedia' => 'ウィキペディア',
	'maps_webcams' => 'ウェブカメラ',
);

/** Javanese (Basa Jawa) */
$messages['jv'] = array(
	'maps-abb-north' => 'L',
	'maps-abb-east' => 'W',
	'maps-abb-south' => 'Kdl',
	'maps-abb-west' => 'Kln',
	'maps-latitude' => 'Latituda:',
	'maps-longitude' => 'Longituda:',
);

/** Georgian (ქართული)
 * @author Temuri rajavi
 */
$messages['ka'] = array(
	'maps_map' => 'რუკა',
	'maps_videos' => 'ვიდეოები',
);

/** Khmer (ភាសាខ្មែរ) */
$messages['km'] = array(
	'maps-abb-north' => 'ជ',
	'maps-abb-east' => 'ក',
	'maps-abb-south' => 'ត្ប',
	'maps-abb-west' => 'ល',
	'maps-latitude' => 'រយះទទឹង៖',
	'maps-longitude' => 'រយះបណ្តោយ៖',
);

/** Kannada (ಕನ್ನಡ)
 * @author Nayvik
 */
$messages['kn'] = array(
	'maps_wikipedia' => 'ವಿಕಿಪೀಡಿಯ',
);

/** Colognian (Ripoarisch)
 * @author Purodha
 */
$messages['ksh'] = array(
	'maps_desc' => 'Määt et müjjelesch, Koodinaate en Landkaate aanzezeije, un Addräße en Koodinaate op de Ääd ömzerääschne. (E [http://wiki.bn2vs.com/wiki/Maps Beispöll]). He di Deenste för Landkaat(e) ham_mer ze beede: $1',
	'maps_map' => 'Kaat',
	'maps-abb-north' => 'N',
	'maps-abb-east' => 'O',
	'maps-abb-south' => 'S',
	'maps-abb-west' => 'W',
	'maps-latitude' => 'Breedt om Jlobus:',
	'maps-longitude' => 'Längde om Jlobus:',
	'maps_coordinates_missing' => 'Mer han kein Koodinaate för di Kaat.',
	'maps_geocoding_failed' => '{{PLURAL:$2|Di Koodinaat|De Koodinaate|Kein Koodinaat}} om Jlobus för di {{PLURAL:$2|aanjejovve Adräß wohr|aanjejovve Adräße wohre|kein aanjejovve Adräß wohr}} Kappes: $1. Di Kaat künne mer su nit aanzeije.',
	'maps_geocoding_failed_for' => 'De Koodinaate om Jlobus för {{PLURAL:$2|ein|paa|kein}} vun dä aanjejovve Adräße {{PLURAL:$2|es|wohre|Fähler!}} Kappes. Di {{PLURAL:$2|es|sin|Fähler!}} dröm nit op dä Kaat. De fottjelohße {{PLURAL:$2|es|sin|Fähler!}}: $1',
	'maps_unrecognized_coords' => 'He di Koordinate kunnte mer nit verschtonn: $1.',
	'maps_unrecognized_coords_for' => 'He di {{PLURAL:$2|Koordinat kunnt|Koordinate kunnte}} mer nit verschtonn un dröm {{PLURAL:$2|es|sin}} se nit en de Kaat opjenumme woode:
$1',
	'maps_map_cannot_be_displayed' => 'Di Kaat künne mer nit aanzeije.',
	'maps_click_to_activate' => 'Donn klecke, öm op di Kaat ze jonn',
	'maps_overlays' => 'Enbländunge',
	'maps_photos' => 'Fottos',
	'maps_videos' => 'Viddejos',
	'maps_wikipedia' => 'Wikipedia',
	'maps_webcams' => 'Webkammeras',
);

/** Ladino (Ladino)
 * @author Universal Life
 */
$messages['lad'] = array(
	'maps_wikipedia' => 'Vikipeddia',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'maps_desc' => "Gëtt d'Méiglechkeet fir d'Date vun de Koordinaten op Kaarten an Geocode Adressen ze weisen. Disponibel mapping Servicer: $1 [http://www.mediawiki.org/wiki/Extension:Maps Dokumentatioun]. [http://wiki.bn2vs.com/wiki/Maps Démo]",
	'maps_map' => 'Kaart',
	'maps-loading-map' => "D'Kaart gëtt gelueden…",
	'maps-abb-north' => 'N',
	'maps-abb-east' => 'O',
	'maps-abb-south' => 'S',
	'maps-abb-west' => 'W',
	'maps-latitude' => 'Breedegrad:',
	'maps-longitude' => 'Längtegrad:',
	'maps-invalid-coordinates' => 'De Wäert $1 gouf net als valabel Set vu Koordinaten erkannt.',
	'maps_coordinates_missing' => "Et goufe keng Koordinate fir d'Kaart uginn.",
	'maps_geocoding_failed' => 'Dës {{PLURAL:$2|Adress konnt|Adresse konnten}} net geocodéiert ginn: $1',
	'maps_geocoding_failed_for' => 'Dës {{PLURAL:$2|Adress|Adresse}} konnten net geocodéiert ginn an {{PLURAL:$2|huet|hu}} missen op der Kaart ewechgelooss ginn:
$1',
	'maps_unrecognized_coords' => '{{PLURAL:$2|Dëse Koordinate gouf|Dës Koordinate goufen}} net erkannt: $1',
	'maps_unrecognized_coords_for' => 'Dës {{PLURAL:$2|Koordinate|Koordinate}} goufen net erkannt a vun der Kaart ignoréiert:
$1',
	'maps_map_cannot_be_displayed' => "D'Kaart kann net gewise ginn.",
	'maps_invalid_distance' => 'De Wäert $1 ass keng valabel Distanz.',
	'maps_click_to_activate' => "Klickt fir d'kaart z'aktivéieren",
	'maps_centred_on' => "D'Kaart ass zentréiert op $1, $2",
	'maps_overlays' => 'Ablendungen',
	'maps_photos' => 'Fotoen',
	'maps_videos' => 'Videoen',
	'maps_wikipedia' => 'Wikipedia',
	'maps_webcams' => 'Web-Kameraen',
);

/** Lithuanian (Lietuvių)
 * @author Hugo.arg
 */
$messages['lt'] = array(
	'maps_desc' => 'Suteikia galimybę atvaizduoti koordinačių duomenis žemėlapiuose ir geografinio kodavimo adresus ([http://wiki.bn2vs.com/wiki/Maps demo]).
Katrografavimo paslaugos pasiekiamos: $1',
	'maps_map' => 'Žemėlapis',
	'maps-loading-map' => 'Kraunamas žemėlapis ...',
	'maps-abb-north' => 'Š',
	'maps-abb-east' => 'R',
	'maps-abb-south' => 'P',
	'maps-abb-west' => 'V',
	'maps-latitude' => 'Platuma:',
	'maps-longitude' => 'Ilguma:',
	'maps-invalid-coordinates' => 'Vertė $ 1 nepripažįstama kaip galiojanti koordinatė.',
	'maps_coordinates_missing' => 'Nesudarytos koordinatės žemėlapiui.',
	'maps_geocoding_failed' => '{{PLURAL:$2|Šis adresas|Šie adresai}} negali būti kartografuoti: $1.',
	'maps_unrecognized_coords' => '{{PLURAL:$2|Ši koordinatė|Šios koordinatės}} nebuvo atpažintos: $1.',
	'maps_map_cannot_be_displayed' => 'Žemėlapis negal būti parodytas.',
	'maps_click_to_activate' => 'Spustelėkite, norėdami įjungti žemėlapį',
	'maps_centred_on' => 'Žemėlapis centruotas link $1, $2.',
	'maps_photos' => 'Nuotraukos',
	'maps_videos' => 'Vaizdo klipai',
	'maps_wikipedia' => 'Vikipedija',
	'maps_webcams' => 'Interneto kameros',
);

/** Macedonian (Македонски)
 * @author Bjankuloski06
 * @author McDutchie
 */
$messages['mk'] = array(
	'maps_desc' => 'Дава можност за приказ на координатни податоци во картите, и геокодирање на адреси ([http://wiki.bn2vs.com/wiki/Maps демо]).
Картографски служби на располагање: $1',
	'maps_map' => 'Карта',
	'maps-loading-map' => 'Ја вчитувам картата...',
	'maps-markers' => 'Обележувачи',
	'maps-abb-north' => 'С',
	'maps-abb-east' => 'И',
	'maps-abb-south' => 'Ј',
	'maps-abb-west' => 'З',
	'maps-latitude' => 'Геог. ширина',
	'maps-longitude' => 'Геог. должина:',
	'maps-invalid-coordinates' => 'Вредноста $1 не беше препознаена како правилен збир координати.',
	'maps_coordinates_missing' => 'Нема координати за картата.',
	'maps_geocoding_failed' => '{{PLURAL:$2|Следнава адреса не можеше да се геокодира|Следниве адреси не можеа да се геокодираат}}: $1.
Картата не може да се прикаже.',
	'maps_geocoding_failed_for' => '{{PLURAL:$2|Следнава адреса не можеше да се геокодира|Следниве адреси не можеа да се геокодираат}} и затоа {{PLURAL:$2|беше изоставена|беа изоставени}} од картата:
$1',
	'maps_unrecognized_coords' => '{{PLURAL:$2|Следнава координата не е препознаена|Следниве координати не се препознаени}}: $1.',
	'maps_unrecognized_coords_for' => '{{PLURAL:$2|Следнава координата не беше препознаена|Следниве координати не беа препознаени}} и {{PLURAL:$2|беше изоставена|беа изоставени}} од картата:
$1',
	'maps_map_cannot_be_displayed' => 'Картата не може да се прикаже.',
	'maps_invalid_distance' => 'Вредноста $1 не претставува важечко растојание.',
	'maps-geocoder-not-available' => 'Функцијата за геокодирање на Карти е недостапна. Вашата локација не може да се геокодира.',
	'maps_click_to_activate' => 'Кликнете за активирање на картата',
	'maps_centred_on' => 'Средиште на картата во $1, $2.',
	'maps_overlays' => 'Слоеви',
	'maps_photos' => 'Фотографии',
	'maps_videos' => 'Видеа',
	'maps_wikipedia' => 'Википедија',
	'maps_webcams' => 'Мреж. камери',
);

/** Malayalam (മലയാളം) */
$messages['ml'] = array(
	'maps-abb-north' => 'വടക്ക്',
	'maps-abb-east' => 'കിഴക്ക്',
	'maps-abb-south' => 'തെക്ക്',
	'maps-abb-west' => 'പടിഞ്ഞാറ്‌',
	'maps-latitude' => 'അക്ഷാംശം:',
	'maps-longitude' => 'രേഖാംശം:',
);

/** Marathi (मराठी) */
$messages['mr'] = array(
	'maps-abb-north' => 'N',
	'maps-abb-east' => 'E',
	'maps-abb-south' => 'S',
	'maps-abb-west' => 'W',
	'maps-latitude' => 'अक्षांश:',
	'maps-longitude' => 'रेखांश:',
);

/** Erzya (Эрзянь) */
$messages['myv'] = array(
	'maps-abb-north' => 'Веньэльйонкс',
	'maps-abb-east' => 'Чилисемайонкс',
	'maps-abb-south' => 'Чиньэльйонкс',
	'maps-abb-west' => 'Чивалгомайонкс',
	'maps-latitude' => 'Келезэ:',
	'maps-longitude' => 'Кувалмозо:',
);

/** Nahuatl (Nāhuatl) */
$messages['nah'] = array(
	'maps-abb-north' => 'M',
	'maps-abb-east' => 'T',
	'maps-abb-south' => 'H',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'maps_desc' => 'Biedt de mogelijkheid om locatiegegevens weer te geven op kaarten en adressen om te zetten naar coordinaten ([http://wiki.bn2vs.com/wiki/Semantic_Maps demo]).
Beschikbare kaartdiensten: $1',
	'maps_map' => 'Kaart',
	'maps-loading-map' => 'Bezig met het laden van de kaart...',
	'maps-markers' => 'Markeringen',
	'maps-abb-north' => 'N',
	'maps-abb-east' => 'O',
	'maps-abb-south' => 'Z',
	'maps-abb-west' => 'W',
	'maps-latitude' => 'Breedte:',
	'maps-longitude' => 'Lengte:',
	'maps-invalid-coordinates' => 'De waarde "$1" is niet herkend als geldige coördinaten.',
	'maps_coordinates_missing' => 'Er zijn geen coördinaten opgegeven voor de kaart.',
	'maps_geocoding_failed' => 'Voor {{PLURAL:$2|het volgende adres|de volgende adressen}} was geocodering niet mogelijk: $1
De kaart kan niet worden weergegeven.',
	'maps_geocoding_failed_for' => 'Voor {{PLURAL:$2|het volgende adres|de volgende adressen}} was geocodering niet mogelijk en {{PLURAL:$2|dit is|deze zijn}} weggelaten uit de kaart:
$1',
	'maps_unrecognized_coords' => 'De volgende {{PLURAL:$2|coördinaat is|coördinaten zijn}} niet herkend: $1.',
	'maps_unrecognized_coords_for' => 'De volgende {{PLURAL:$2|coördinaat is niet herkend en is|coördinaten zijn niet herkend en zijn}} weggelaten uit de kaart:
$1.',
	'maps_map_cannot_be_displayed' => 'De kaart kan niet weergegeven worden.',
	'maps_invalid_distance' => 'De waarde $1 is geen geldige afstand.',
	'maps-geocoder-not-available' => 'Geocoderen via Maps is niet beschikbaar. Het geocoderen van uw locatie is niet mogelijk.',
	'maps_googlemaps2' => 'Google Maps v2',
	'maps_yahoomaps' => 'Yahoo! Maps',
	'maps_openlayers' => 'OpenLayers',
	'maps_click_to_activate' => 'Klik om de kaart te activeren',
	'maps_centred_on' => 'Kaart gecentreerd op $1, $2.',
	'maps_overlays' => "Overlay's",
	'maps_photos' => "Foto's",
	'maps_videos' => "Video's",
	'maps_wikipedia' => 'Wikipedia',
	'maps_webcams' => 'Webcams',
);

/** Norwegian Nynorsk (‪Norsk (nynorsk)‬)
 * @author Harald Khan
 */
$messages['nn'] = array(
	'maps_map' => 'Kart',
	'maps-abb-north' => 'N',
	'maps-abb-east' => 'A',
	'maps-abb-south' => 'S',
	'maps-abb-west' => 'V',
	'maps-latitude' => 'Breiddegrad:',
	'maps-longitude' => 'Lengdegrad:',
	'maps_coordinates_missing' => 'Ingen koordinatar vart oppgjevne for kartet.',
	'maps_unrecognized_coords' => 'Dei fylgjande koordinatane vart ikkje kjende att: $1.',
	'maps_map_cannot_be_displayed' => 'Kartet kan ikkje verta vist.',
	'maps_click_to_activate' => 'Trykk for å aktivera kartet',
	'maps_centred_on' => 'Kart sentrert på $1, $2.',
	'maps_photos' => 'Bilete',
	'maps_videos' => 'Videoar',
	'maps_wikipedia' => 'Wikipedia',
	'maps_webcams' => 'Webkamera',
);

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 * @author Nghtwlkr
 */
$messages['no'] = array(
	'maps_desc' => 'Gir mulighet for å vise koorinatdata i kart og geokodeadresser ([http://wiki.bn2vs.com/wiki/Maps demo]).
Tilgjengelige karttjenester: $1',
	'maps_map' => 'Kart',
	'maps-loading-map' => 'Laster kart...',
	'maps-markers' => 'Markører',
	'maps-abb-north' => 'N',
	'maps-abb-east' => 'Ø',
	'maps-abb-south' => 'S',
	'maps-abb-west' => 'V',
	'maps-latitude' => 'Breddegrad:',
	'maps-longitude' => 'Lengdegrad:',
	'maps-invalid-coordinates' => 'Verdien $1 ble ikke gjenkjent som et gyldig sett med koordinater.',
	'maps_coordinates_missing' => 'Ingen koordinater oppgitt for kartet.',
	'maps_geocoding_failed' => 'Følgende {{PLURAL:$2|adresse|adresser}} kunne ikke geokodes: $1.',
	'maps_geocoding_failed_for' => 'Følgende {{PLURAL:$2|adresse|adresser}} kunne ikke geokodes og har blitt utelatt fra kartet:
$1',
	'maps_unrecognized_coords' => 'Følgende {{PLURAL:$2|koordinat|koordinat}} ble ikke gjenkjent: $1.',
	'maps_unrecognized_coords_for' => 'Følgende {{PLURAL:$2|koordinat|koordinater}} ble ikke gjenkjent og har blitt utelatt fra kartet:
$1',
	'maps_map_cannot_be_displayed' => 'Kartet kan ikke vises.',
	'maps_invalid_distance' => 'Verdien $1 er ikke en gyldig avstand.',
	'maps-geocoder-not-available' => 'Geokodingsfunksjonen i Maps er ikke tilgjengelig. Din plassering kan ikke geokodes.',
	'maps_click_to_activate' => 'Klikk for å aktivere kartet',
	'maps_centred_on' => 'Kart sentrert om $1, $2.',
	'maps_overlays' => 'Transparenter',
	'maps_photos' => 'Foto',
	'maps_videos' => 'Videoer',
	'maps_wikipedia' => 'Wikipedia',
	'maps_webcams' => 'Webkamera',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'maps_desc' => "Permet d'afichar de coordenadas dins de mapas, e mai d'adreça geocodadas
([http://www.mediawiki.org/wiki/Extension:Maps documentacion], [http://wiki.bn2vs.com/wiki/Maps demonstracion]). 
Servicis de cartografia disponibles : $1",
	'maps_map' => 'Mapa',
	'maps-abb-north' => 'N',
	'maps-abb-east' => 'E',
	'maps-abb-south' => 'S',
	'maps-abb-west' => 'O',
	'maps-latitude' => 'Latitud :',
	'maps-longitude' => 'Longitud :',
	'maps_coordinates_missing' => 'Cap de coordenada es pas estada provesida pel plan.',
	'maps_geocoding_failed' => "{{PLURAL:$2|L'adreça seguenta a pas pogut èsser geoencodada|Las adreças seguentas an pas pogut èsser geoencodadas}} : $1.
Lo plan pòt pas èsser afichat.",
	'maps_geocoding_failed_for' => "{{PLURAL:$2|L'adreça seguenta a pas pogut èsser geoencodada|Las adreças seguentas an pas pogut èsser geoencodadas}} e {{PLURAL:$2|es pas afichada|son pas afichadas}} sul plan : $1",
	'maps_unrecognized_coords' => 'Las coordenadas seguentas son pas estadas reconegudas : $1.',
	'maps_unrecognized_coords_for' => '{{PLURAL:$2|La coordenada seguenta es pas estada reconeguda|Las coordenadas seguentas son pas estadas reconegudas}} e {{PLURAL:$2|es estada omesa|son estadas omesas}} sus la mapa :
$1',
	'maps_map_cannot_be_displayed' => 'La mapa pòt pas èsser afichada.',
	'maps_click_to_activate' => 'Clicar per activar la mapa',
	'maps_centred_on' => 'Mapa centrada sus $1, $2.',
	'maps_overlays' => 'Superposicions',
	'maps_photos' => 'Fòtos',
	'maps_videos' => 'Vidèos',
	'maps_wikipedia' => 'Wikipèdia',
	'maps_webcams' => 'Webcams',
);

/** Deitsch (Deitsch) */
$messages['pdc'] = array(
	'maps-abb-north' => 'N',
	'maps-abb-south' => 'S',
	'maps-abb-west' => 'W',
);

/** Polish (Polski)
 * @author Sp5uhe
 * @author Yarl
 */
$messages['pl'] = array(
	'maps_desc' => 'Umożliwia wyświetlanie na mapach współrzędnych oraz adresów geograficznych ([http://wiki.bn2vs.com/wiki/Maps demo]). Dostępne serwisy mapowe: $1',
	'maps_map' => 'Mapa',
	'maps-loading-map' => 'Wczytywanie mapy…',
	'maps-markers' => 'Zaznaczenia',
	'maps-abb-north' => 'N',
	'maps-abb-east' => 'E',
	'maps-abb-south' => 'S',
	'maps-abb-west' => 'W',
	'maps-latitude' => 'Szerokość geograficzna',
	'maps-longitude' => 'Długość geograficzna',
	'maps-invalid-coordinates' => 'Wartość $1 nie została rozpoznana jako prawidłowe współrzędne.',
	'maps_coordinates_missing' => 'Brak współrzędnych dla mapy.',
	'maps_geocoding_failed' => '{{PLURAL:$2|Następującego adresu nie można odnaleźć na mapie|Następujących adresów nie można odnaleźć na mapie:}} $1.
Mapa nie może zostać wyświetlona.',
	'maps_geocoding_failed_for' => '{{PLURAL:$2|Następujący adres został pominięty, ponieważ nie można go odnaleźć na mapie|Następujące adresy zostały pominięte, ponieważ nie można ich odnaleźć na mapie:}} $1.',
	'maps_unrecognized_coords' => '{{PLURAL:$2|Następująca współrzędna nie została rozpoznana –|Następujące współrzędne nie zostały rozpoznane:}} $1.',
	'maps_unrecognized_coords_for' => '{{PLURAL:$2|Następującą współrzędną|Następujące współrzędne}} pominięto, ponieważ nie {{PLURAL:$2|została rozpoznana|zostały rozpoznane}}:
$1',
	'maps_map_cannot_be_displayed' => 'Mapa nie może zostać wyświetlona.',
	'maps_invalid_distance' => 'Wartość $1 nie jest poprawną odległością.',
	'maps-geocoder-not-available' => 'Funkcja geokodowania map nie jest dostępna. Lokalizacja nie może zostać zakodowana.',
	'maps_click_to_activate' => 'Kliknij, aby aktywować mapę',
	'maps_centred_on' => 'Środek mapy – $1, $2.',
	'maps_overlays' => 'Nakładki',
	'maps_photos' => 'Zdjęcia',
	'maps_videos' => 'Filmy',
	'maps_wikipedia' => 'Wikipedia',
	'maps_webcams' => 'Kamery internetowe',
);

/** Piedmontese (Piemontèis)
 * @author Borichèt
 * @author Dragonòt
 * @author McDutchie
 */
$messages['pms'] = array(
	'maps_desc' => "A dà la possibilità ëd visualisé le coordinà ant le mape, e j'adrësse geocode ([http://wiki.bn2vs.com/wiki/Maps demo]). Sërvissi ëd mapatura disponìbil: $1",
	'maps_map' => 'Pian',
	'maps-loading-map' => 'Cariament ëd la carta...',
	'maps-markers' => 'Marcador',
	'maps-abb-north' => 'N',
	'maps-abb-east' => 'E',
	'maps-abb-south' => 'S',
	'maps-abb-west' => 'W',
	'maps-latitude' => 'Latitùdin:',
	'maps-longitude' => 'Longitùdin:',
	'maps-invalid-coordinates' => "Ël valor $1 a l'é pa stàit arconossù con n'ansema bon ëd coordinà.",
	'maps_coordinates_missing' => 'Pa gnun-e coordinà dàite për la mapa.',
	'maps_geocoding_failed' => "{{PLURAL:$2|L'adrëssa|J'adrësse}} sì sota a peulo pa esse sota geocode: $1.
La mapa a peul pa esse visualisà.",
	'maps_geocoding_failed_for' => "{{PLURAL:$2|L'adrëssa|J'adrësse}} sì sota a peula pa esse sota geocode e a {{PLURAL:$2|l'é pa stàita|son pa stàite}}  butà ant la mapa: $1",
	'maps_unrecognized_coords' => "{{PLURAL:$2|La coordinà sota a l'é pa stàita arconossùa|Le coordinà sota a son pa stàite arconossùe}}: $1.",
	'maps_unrecognized_coords_for' => "{{PLURAL:$2|La coordinatà sota a l'é pa stàita arconossùa|Le coordinà sota a son pa stàite arconossùe}}  e a {{PLURAL:$2|l'é stàita|a son stàite}} pa butà ant la carta: 
$1",
	'maps_map_cannot_be_displayed' => 'La carta a peul pa esse mostrà.',
	'maps_invalid_distance' => "Ël valor $1 a l'é pa na distansa bon-a.",
	'maps-geocoder-not-available' => "La possibilità ëd geocodìfica dle carte a l'é pa disponìbil. Soa locassion a peul pa esse geocodificà.",
	'maps_click_to_activate' => 'Sgnaca për ativé la carta',
	'maps_centred_on' => 'Carta sentrà su $1, $2.',
	'maps_overlays' => 'Sovraposission',
	'maps_photos' => 'Fòto',
	'maps_videos' => 'Filmà',
	'maps_wikipedia' => 'Wikipedia',
	'maps_webcams' => 'Webcam',
);

/** Pashto (پښتو)
 * @author Ahmed-Najib-Biabani-Ibrahimkhel
 */
$messages['ps'] = array(
	'maps_map' => 'نخشه',
	'maps-abb-north' => 'سهـ',
	'maps-abb-east' => 'خ',
	'maps-abb-south' => 'سو',
	'maps-abb-west' => 'ل',
	'maps_photos' => 'انځورونه',
	'maps_videos' => 'ويډيوګانې',
	'maps_wikipedia' => 'ويکيپېډيا',
	'maps_webcams' => 'وېبکامرې',
);

/** Portuguese (Português)
 * @author Giro720
 * @author Hamilton Abreu
 */
$messages['pt'] = array(
	'maps_desc' => 'Permite apresentar dados de coordenadas em mapas e endereços por geocódigo ([http://wiki.bn2vs.com/wiki/Maps demonstração]).
Serviços de cartografia disponíveis: $1',
	'maps_map' => 'Mapa',
	'maps-loading-map' => 'A carregar o mapa...',
	'maps-markers' => 'Marcadores',
	'maps-abb-north' => 'N',
	'maps-abb-east' => 'E',
	'maps-abb-south' => 'S',
	'maps-abb-west' => 'O',
	'maps-latitude' => 'Latitude:',
	'maps-longitude' => 'Longitude:',
	'maps-invalid-coordinates' => 'O valor $1 não foi reconhecido como um conjunto de coordenadas válido.',
	'maps_coordinates_missing' => 'Não foram fornecidas coordenadas para o mapa.',
	'maps_geocoding_failed' => 'Não foi possível geocodificar {{PLURAL:$2|o seguinte endereço|os seguintes endereços}}: $1.
O mapa não pode ser apresentado.',
	'maps_geocoding_failed_for' => 'Não foi possível geocodificar {{PLURAL:$2|o seguinte endereço, que foi omitido|os seguintes endereços, que foram omitidos}} do mapa:
$1.',
	'maps_unrecognized_coords' => '{{PLURAL:$2|A seguinte coordenada não foi reconhecida|As seguintes coordenadas não foram reconhecidas}}: $1.',
	'maps_unrecognized_coords_for' => '{{PLURAL:$2|A seguinte coordenada não foi reconhecida e foi omitida|As seguintes coordenadas não foram reconhecidas e foram omitidas}} do mapa:
$1',
	'maps_map_cannot_be_displayed' => 'Não é possível apresentar o mapa.',
	'maps_invalid_distance' => 'O valor $1 não é uma distância válida.',
	'maps-geocoder-not-available' => 'A funcionalidade de georeferenciação do Mapas está indisponível; a sua localização não pode ser georeferenciada.',
	'maps_click_to_activate' => 'Clique para activar o mapa',
	'maps_centred_on' => 'Mapa centrado nas coordenadas $1, $2.',
	'maps_overlays' => 'Sobreposições',
	'maps_photos' => 'Fotografias',
	'maps_videos' => 'Vídeos',
	'maps_wikipedia' => 'Wikipédia',
	'maps_webcams' => 'Câmaras Web',
);

/** Brazilian Portuguese (Português do Brasil)
 * @author Eduardo.mps
 * @author Giro720
 * @author Luckas Blade
 */
$messages['pt-br'] = array(
	'maps_desc' => 'Provê a possibilidade de exibir dados de coordenadas em mapas e endereços em geocódigo. ([http://wiki.bn2vs.com/wiki/Maps demonstração]).
Serviços de mapeamento disponíveis: $1',
	'maps_map' => 'Mapa',
	'maps-loading-map' => 'Carregando mapa...',
	'maps-markers' => 'Marcadores',
	'maps-abb-north' => 'N',
	'maps-abb-east' => 'L',
	'maps-abb-south' => 'S',
	'maps-abb-west' => 'O',
	'maps-latitude' => 'Latitude:',
	'maps-longitude' => 'Longitude:',
	'maps-invalid-coordinates' => 'O valor $1 não foi reconhecido como um conjunto de coordenadas válido.',
	'maps_coordinates_missing' => 'Nenhuma coordenada fornecida para o mapa',
	'maps_geocoding_failed' => '{{PLURAL:$2|O seguinte endereço não pode|Os seguintes endereços não puderam}} ser {{PLURAL:$2|geocodificado|geocodificados}}: $1.
O mapa não pode ser exibido.',
	'maps_geocoding_failed_for' => '{{PLURAL:$2|O seguinte endereço não pode|Os seguintes endereços não puderam}} ser {{PLURAL:$2|geocodificado e foi omitido|geocodificados e foram omitidos}} do mapa:
$1',
	'maps_unrecognized_coords' => '{{PLURAL:$2|A seguinte coordenada não foi reconhecida|As seguintes coordenadas não foram reconhecidas}}: $1.',
	'maps_unrecognized_coords_for' => '{{PLURAL:$2|A seguinte coordenada não foi reconhecida e foi omitida|As seguintes coordenadas não foram reconhecidas e foram omitidas}} do mapa:
$1',
	'maps_map_cannot_be_displayed' => 'O mapa não pode ser mostrado.',
	'maps_invalid_distance' => 'O valor $1 não é uma distância válida.',
	'maps-geocoder-not-available' => 'A funcionalidade de georeferenciação do Mapas está indisponível; a sua localização não pode ser georeferenciada.',
	'maps_click_to_activate' => 'Clique para ativar o mapa',
	'maps_centred_on' => 'Mapa centrado nas coordenadas $1, $2.',
	'maps_overlays' => 'Sobreposições',
	'maps_photos' => 'Fotos',
	'maps_videos' => 'Vídeos',
	'maps_wikipedia' => 'Wikipédia',
	'maps_webcams' => 'Webcams',
);

/** Romanian (Română)
 * @author Firilacroco
 * @author KlaudiuMihaila
 * @author Minisarm
 */
$messages['ro'] = array(
	'maps_desc' => 'Asigură capacitatea de a afișa coordonate pe hărți și adrese geocode ([http://wiki.bn2vs.com/wiki/Maps demonstrație]).
Servici de cartografiere disponibile: $1',
	'maps_map' => 'Hartă',
	'maps-loading-map' => 'Se încarcă harta...',
	'maps-markers' => 'Marcatori',
	'maps-abb-north' => 'N',
	'maps-abb-east' => 'E',
	'maps-abb-south' => 'S',
	'maps-abb-west' => 'V',
	'maps-latitude' => 'Latitudine:',
	'maps-longitude' => 'Longitudine:',
	'maps-invalid-coordinates' => 'Valoarea $1 nu a fost recunoscută ca un set valid de coordonate.',
	'maps_coordinates_missing' => 'Nu au fost furnizate coordonate pentru hartă.',
	'maps_geocoding_failed' => '{{PLURAL:$2|Următoarea|Următoarele}} {{PLURAL:$2|adresă|adrese}} nu {{PLURAL:$2|a|au}} putut fi {{PLURAL:$2|geocodificată|geocodificate}}: $1.
Harta nu poate fi afișată.',
	'maps_geocoding_failed_for' => '{{PLURAL:$2|Următoarea|Următoarele}} {{PLURAL:$2|adresă|adrese}} nu {{PLURAL:$2|a|au}} putut fi {{PLURAL:$2|geocodificată|geocodificate}} și {{PLURAL:$2|a|au}} fost {{PLURAL:$2|omisă|omise}} de pe hartă:
$1',
	'maps_unrecognized_coords' => '{{PLURAL:$2|Următorul|Următoarele}} {{PLURAL:$2|set|seturi}} de coordonate nu {{PLURAL:$2|a|au}} putut fi {{PLURAL:$2|recunoscut|recunoscute}}: $1.',
	'maps_unrecognized_coords_for' => '{{PLURAL:$2|Următorul|Următoarele}} {{PLURAL:$2|set|seturi}} de coordonate nu {{PLURAL:$2|a|au}} putut fi {{PLURAL:$2|recunoscut|recunoscute}} și {{PLURAL:$2|a|au}} fost {{PLURAL:$2|omis|omise}}: $1',
	'maps_map_cannot_be_displayed' => 'Harta nu poate fi afișată.',
	'maps_invalid_distance' => 'Valoarea $1 nu reprezintă o distanță validă.',
	'maps-geocoder-not-available' => 'Opțiunea de geocodare pentru Hărți nu este disponibilă. Locația dumneavoastră nu a putut fi geocodată.',
	'maps_click_to_activate' => 'Apăsați pentru a activa harta',
	'maps_centred_on' => 'Hartă centrată la $1, $2.',
	'maps_overlays' => 'Straturi',
	'maps_photos' => 'Fotografii',
	'maps_videos' => 'Filme',
	'maps_wikipedia' => 'Wikipedia',
	'maps_webcams' => 'Camere web',
);

/** Tarandíne (Tarandíne)
 * @author Joetaras
 */
$messages['roa-tara'] = array(
	'maps_desc' => "Dè l'abbilità a fà vedè le coordinate jndr'à le mappe e le indirizze geocodificate ([http://wiki.bn2vs.com/wiki/Maps demo]). Disponibbile le servizie de mappe: $1",
);

/** Russian (Русский)
 * @author Lockal
 * @author McDutchie
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'maps_desc' => 'Обеспечивает возможность отображения координатных данных на картах и геокодирование адресов ([http://wiki.bn2vs.com/wiki/Maps демонстрация]).
Доступные картографические службы: $1',
	'maps_map' => 'Карта',
	'maps-loading-map' => 'Идёт загрузка карты…',
	'maps-markers' => 'Отметки',
	'maps-abb-north' => 'С',
	'maps-abb-east' => 'В',
	'maps-abb-south' => 'Ю',
	'maps-abb-west' => 'З',
	'maps-latitude' => 'Широта:',
	'maps-longitude' => 'Долгота:',
	'maps-invalid-coordinates' => 'Значение $1 не признано допустимым набором координат.',
	'maps_coordinates_missing' => 'Не указаны координаты для карты.',
	'maps_geocoding_failed' => '{{PLURAL:$2|Следующий адрес не может быть геокодирован|Следующие адреса не могут быть геокодированы}}: $1.
Карта не может быть отображена.',
	'maps_geocoding_failed_for' => '{{PLURAL:$2|Следующий адрес не может быть геокодирован и был удалён|Следующие адреса не могут быть геокодированы и были удалены}} с карты:
$1',
	'maps_unrecognized_coords' => 'Следующие {{PLURAL:$2|координаты|координаты}} не были опознаны: $1.',
	'maps_unrecognized_coords_for' => 'Следующие координаты не были опознаны, {{PLURAL:$2|они|они}} не показаны на карте:
$1',
	'maps_map_cannot_be_displayed' => 'Карта не может быть показана.',
	'maps_invalid_distance' => 'Значение $1 не является допустимым расстоянием.',
	'maps-geocoder-not-available' => 'Функция геокодирования карт недоступна, ваше местоположение не может быть геокодировано.',
	'maps_click_to_activate' => 'Нажмите для активации карты',
	'maps_centred_on' => 'Центр карты — $1, $2.',
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
	'maps_map' => 'Mapa',
	'maps-abb-north' => 'S',
	'maps-abb-east' => 'V',
	'maps-abb-south' => 'J',
	'maps-abb-west' => 'Z',
	'maps-latitude' => 'Zem. dĺžka:',
	'maps-longitude' => 'Zem. šírka:',
	'maps_coordinates_missing' => 'Neboli poskytnuté žiadne súradnice.',
	'maps_geocoding_failed' => 'Nebolo možné určiť súradnice {{PLURAL:$2|nasledovnej adresy|nasledovných adries}}: $1.',
	'maps_geocoding_failed_for' => 'Nebolo možné určiť súradnice {{PLURAL:$2|nasledovnej adresy|nasledovných adries}} a {{PLURAL:$2|bola vynechaná|boli vynechané}} z mapy: $1.',
);

/** Serbian Cyrillic ekavian (Српски (ћирилица))
 * @author Михајло Анђелковић
 */
$messages['sr-ec'] = array(
	'maps_map' => 'Мапа',
	'maps-loading-map' => 'Учитавање мапе...',
	'maps-abb-north' => 'С',
	'maps-abb-east' => 'И',
	'maps-abb-south' => 'Ј',
	'maps-abb-west' => 'З',
	'maps-latitude' => 'Географска ширина:',
	'maps-longitude' => 'Географска дужина:',
	'maps_photos' => 'Фотографије',
	'maps_videos' => 'Видео снимци',
	'maps_webcams' => 'Веб-камере',
);

/** Serbian Latin ekavian (Srpski (latinica)) */
$messages['sr-el'] = array(
	'maps_map' => 'Mapa',
	'maps-loading-map' => 'Učitavanje mape...',
	'maps-abb-north' => 'S',
	'maps-abb-east' => 'I',
	'maps-abb-south' => 'J',
	'maps-abb-west' => 'Z',
	'maps-latitude' => 'Geografska širina:',
	'maps-longitude' => 'Geografska dužina:',
	'maps_photos' => 'Fotografije',
	'maps_videos' => 'Video snimci',
	'maps_webcams' => 'Veb-kamere',
);

/** Swedish (Svenska)
 * @author Dafer45
 * @author Fluff
 * @author Per
 */
$messages['sv'] = array(
	'maps_desc' => 'Ger möjlighet till att visa koordinater på kartor och geokodade adresser ([http://wiki.bn2vs.com/wiki/Maps demo]).
Tillgängliga karttjänster: $1',
	'maps_map' => 'Karta',
	'maps-loading-map' => 'Laddar karta ...',
	'maps-abb-north' => 'N',
	'maps-abb-east' => 'Ö',
	'maps-abb-south' => 'S',
	'maps-abb-west' => 'V',
	'maps-latitude' => 'Breddgrad:',
	'maps-longitude' => 'Längdgrad:',
	'maps_coordinates_missing' => 'Inga koordinater angivna för kartan.',
	'maps_geocoding_failed' => 'Följande {{PLURAL:$2|adress|adresser}} kunde inte geokodas: $1.
Kartan kan inte visas.',
	'maps_geocoding_failed_for' => 'Följande {{PLURAL:$2|adress|adresser}}kunde inte geokodas och {{PLURAL:$2|har|har}} uteslutits från kartan: $1',
	'maps_unrecognized_coords' => 'Följande koordinater kändes inte igen: $1.',
	'maps_unrecognized_coords_for' => 'Följande {{PLURAL:$2|koordinat|koordinater}} kändes inte igen och {{PLURAL:$2|har|har}} utelämnats från kartan:
$1',
	'maps_map_cannot_be_displayed' => 'Kartan kan inte visas.',
	'maps_click_to_activate' => 'Klicka för att aktivera karta',
	'maps_photos' => 'Foton',
	'maps_wikipedia' => 'Wikipedia',
	'maps_webcams' => 'Webbkameror',
);

/** Telugu (తెలుగు)
 * @author Veeven
 */
$messages['te'] = array(
	'maps_map' => 'పటం',
	'maps-abb-north' => 'ఉ',
	'maps-abb-east' => 'తూ',
	'maps-abb-south' => 'ద',
	'maps-abb-west' => 'ప',
	'maps-latitude' => 'అక్షాంశం:',
	'maps-longitude' => 'రేఖాంశం:',
	'maps_photos' => 'చిత్రాలు',
	'maps_videos' => 'దృశ్యకాలు',
	'maps_wikipedia' => 'వికీపీడియా',
);

/** Thai (ไทย)
 * @author Woraponboonkerd
 */
$messages['th'] = array(
	'maps_desc' => 'ให้ความสามารถในการแสดงพิกัดในแผนที่ และที่อยู่ที่เป็นรหัสทางภูมิศาสตร์([http://wiki.bn2vs.com/wiki/Maps demo]).
<br />บริการแผนที่ที่มีอยู่: $1',
	'maps_coordinates_missing' => 'ไม่ได้กำหนดพิกัดของแผนที่มาให้',
);

/** Tagalog (Tagalog)
 * @author AnakngAraw
 */
$messages['tl'] = array(
	'maps_desc' => 'Nagbibigay ng kakayahang ipakita ang dato ng tugmaang-pampook sa loob ng mga mapa, at mga adres ([http://wiki.bn2vs.com/wiki/Maps pagpapakita]).
Makukuhang mga paglilingkod na pangpagmamapa: $1',
	'maps_map' => 'Mapa',
	'maps-loading-map' => 'Ikinakarga ang mapa...',
	'maps-abb-north' => 'H',
	'maps-abb-east' => 'S',
	'maps-abb-south' => 'T',
	'maps-abb-west' => 'K',
	'maps-latitude' => 'Latitud:',
	'maps-longitude' => 'Longhitud:',
	'maps-invalid-coordinates' => 'Hindi kinilala ang halagang $1 bilang isang tanggap na pangkat ng mga tugmaang-pampook.',
	'maps_coordinates_missing' => 'Walang mga tugmaang-pampook na ibinigay para sa mapa.',
	'maps_geocoding_failed' => 'Hindi mageokodigo ang sumusunod na {{PLURAL:$2|tirahan|mga tirahan}}:  $1.',
	'maps_geocoding_failed_for' => 'Hindi mageokodigo ang sumusunod na {{PLURAL:$2|tirahan|mga tirahan}} at {{PLURAL:$2|tinanggal|mga tinanggal}} na mula sa mapa:
$1',
	'maps_unrecognized_coords' => 'Hindi kinilala ang sumusunod na {{PLURAL:$2|tugmaan|mga tugmaan}}: $1.',
	'maps_unrecognized_coords_for' => 'Hindi nakilala ang sumusunod na {{PLURAL:$2|tugmaang pampook|mga tugmaang pampook}} at {{PLURAL:$2|inalis|mga inalis}} na mula sa mapa:
$1',
	'maps_map_cannot_be_displayed' => 'Hindi maipapakita ang mapa.',
	'maps_invalid_distance' => 'Hindi isang tanggap na layo ang halagang $1.',
	'maps-geocoder-not-available' => 'Wala ang katangiang-kasangkapang pang-geokodigo ng Mga Mapa.  Hindi mageokodigo ang lokasyon mo.',
	'maps_click_to_activate' => 'Pindutin upang mabuhay ang mapa',
	'maps_centred_on' => 'Nakagitna ang mapa sa $1, $2.',
	'maps_overlays' => 'Mga patong',
	'maps_photos' => 'Mga larawan',
	'maps_videos' => 'Mga bidyo',
	'maps_wikipedia' => 'Wikipedia',
	'maps_webcams' => 'Mga webkam',
);

/** Turkish (Türkçe)
 * @author Joseph
 * @author Manco Capac
 * @author Vito Genovese
 */
$messages['tr'] = array(
	'maps_map' => 'Harita',
	'maps-loading-map' => 'Harita yükleniyor...',
	'maps-abb-north' => 'K',
	'maps-abb-east' => 'D',
	'maps-abb-south' => 'G',
	'maps-abb-west' => 'B',
	'maps-latitude' => 'Enlem:',
	'maps-longitude' => 'Boylam:',
	'maps-invalid-coordinates' => '$1 değeri geçerli bir koordinat olarak algılanmadı.',
	'maps_coordinates_missing' => 'Harita için koordinat girilmedi.',
	'maps_geocoding_failed' => 'Takip eden {{PLURAL:$2|adres|adresler}} coğrafi olarak kodlanmaıyor: $1',
	'maps_unrecognized_coords' => 'Şu {{PLURAL:$2|koordinat|koordinatlar}} tanınamadı: $1',
	'maps_map_cannot_be_displayed' => 'Harita görüntülenemiyor.',
	'maps_invalid_distance' => '$1 değeri geçerli bir mesafe değeri değildir.',
	'maps-geocoder-not-available' => 'Haritanın coğrafi kodlama özelliği etkin değil. Konumunuz kodlanamıyor.',
	'maps_click_to_activate' => 'Haritayı etkinleştirmek için tıkla',
	'maps_centred_on' => 'Harita, $1 $2 koordinatlarında ortalandı.',
	'maps_overlays' => 'Katmanlar',
	'maps_photos' => 'Fotoğraflar',
	'maps_videos' => 'Videolar',
	'maps_wikipedia' => 'Vikipedi',
	'maps_webcams' => 'Web kameraları',
);

/** Ukrainian (Українська)
 * @author Тест
 */
$messages['uk'] = array(
	'maps-latitude' => 'Широта:',
	'maps-longitude' => 'Довгота:',
);

/** Veps (Vepsan kel') */
$messages['vep'] = array(
	'maps-abb-north' => 'Pohj.',
	'maps-abb-east' => 'Päivl.',
	'maps-abb-south' => 'Suvi',
	'maps-abb-west' => 'Päivn.',
	'maps-latitude' => 'Leveduz:',
	'maps-longitude' => 'Piduz:',
);

/** Vietnamese (Tiếng Việt)
 * @author Minh Nguyen
 * @author Vinhtantran
 */
$messages['vi'] = array(
	'maps_name' => 'Bản đồ',
	'maps_desc' => 'Cung cấp khả năng hiển thị dữ liệu tọa độ trên bản đồ và địa chỉ mã địa lý ([http://wiki.bn2vs.com/wiki/Maps thử xem]).
Các dịch vụ bản đồ có sẵn: $1',
	'maps_map' => 'Bản đồ',
	'maps-loading-map' => 'Đang tải bản đồ…',
	'maps-abb-north' => 'B',
	'maps-abb-east' => 'Đ',
	'maps-abb-south' => 'N',
	'maps-abb-west' => 'T',
	'maps-latitude' => 'Vĩ độ:',
	'maps-longitude' => 'Kinh độ:',
	'maps-invalid-coordinates' => 'Giá trị $1 không được nhận ra là tọa độ hợp lệ.',
	'maps_coordinates_missing' => 'Chưa định rõ tọa độ cho bản đồ.',
	'maps_geocoding_failed' => 'Không thể tính ra mã địa lý của {{PLURAL:$2|địa chỉ|các địa chỉ}} sau: $1.
Không thể hiển thị bản đồ.',
	'maps_geocoding_failed_for' => 'Không thể tính ra mã địa lý của {{PLURAL:$2|địa chỉ|các địa chỉ}} sau nên bản đồ bỏ qua nó:
$1',
	'maps_unrecognized_coords' => 'Không thể nhận ra {{PLURAL:$2|tọa độ|các tọa độ}} sau: $1.',
	'maps_unrecognized_coords_for' => 'Không thể nhận ra {{PLURAL:$2|tọa độ|các tọa độ}} sau nên bản đồ bỏ qua nó:
$1',
	'maps_map_cannot_be_displayed' => 'Không thể hiển thị bản đồ.',
	'maps_invalid_distance' => 'Giá trị $1 không phải là khoảng cách hợp lệ.',
	'maps-geocoder-not-available' => 'Không thể mã hóa vị trí của bạn vì tính năng mã hóa địa lý của Bản đồ không có sẵn.',
	'maps_osm' => 'OpenStreetMap',
	'maps_click_to_activate' => 'Nhấn chuột vào bản đồ để kích hoạt',
	'maps_centred_on' => 'Bản đồ với trung tậm tại $1, $2.',
	'maps_overlays' => 'Lớp',
	'maps_photos' => 'Hình ảnh',
	'maps_videos' => 'Video',
	'maps_wikipedia' => 'Wikipedia',
	'maps_webcams' => 'Webcam',
);

/** Volapük (Volapük) */
$messages['vo'] = array(
	'maps-abb-north' => 'N',
	'maps-abb-east' => 'L',
	'maps-abb-south' => 'S',
	'maps-abb-west' => 'V',
	'maps-latitude' => 'Videt:',
	'maps-longitude' => 'Lunet:',
);

/** Chinese (China) (‪中文(中国大陆)‬) */
$messages['zh-cn'] = array(
	'maps-latitude' => '纬度：',
	'maps-longitude' => '经度：',
);

/** Simplified Chinese (‪中文(简体)‬) */
$messages['zh-hans'] = array(
	'maps-abb-north' => '北',
	'maps-abb-east' => '东',
	'maps-abb-south' => '南',
	'maps-abb-west' => '西',
	'maps-latitude' => '纬度：',
	'maps-longitude' => '经度：',
);

/** Traditional Chinese (‪中文(繁體)‬) */
$messages['zh-hant'] = array(
	'maps-abb-north' => '北',
	'maps-abb-east' => '東',
	'maps-abb-south' => '南',
	'maps-abb-west' => '西',
	'maps-latitude' => '緯度：',
	'maps-longitude' => '經度：',
);

/** Chinese (Taiwan) (‪中文(台灣)‬) */
$messages['zh-tw'] = array(
	'maps-abb-north' => '北',
	'maps-abb-east' => '東',
	'maps-abb-south' => '南',
	'maps-abb-west' => '西',
	'maps-latitude' => '緯度：',
	'maps-longitude' => '經度：',
);

