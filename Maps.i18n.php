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
	'maps_name' => 'Maps',
	'maps_desc' => "Provides the ability to display coordinate data in maps, and geocode addresses ([http://wiki.bn2vs.com/wiki/Maps demo]).
Available mapping services: $1",

	'maps_coordinates_missing' => 'No coordinates provided for the map.',
	'maps_geocoding_failed' => 'The following {{PLURAL:$2|address|addresses}} could not be geocoded: $1.
The map cannot be displayed.',
	'maps_geocoding_failed_for' => 'The following {{PLURAL:$2|address|addresses}} could not be geocoded and {{PLURAL:$2|has|have}} been omitted from the map:
$1',
);

/** Message documentation (Message documentation)
 * @author Purodha
 * @author Raymond
 */
$messages['qqq'] = array(
	'maps_name' => '{{Optional}}',
	'maps_desc' => '{{desc}}

* $1: a list of available map services',
	'maps_geocoding_failed_for' => '* $1 is a list
* $2 is the number of list items for PLURAL use.',
);

/** Arabic (العربية) */
$messages['ar'] = array(
	'maps_desc' => 'يعطي إمكانية عرض معلومات التنسيق في الخرائط وعناوين الترميز الجغرافي. خدمات الخرائط المتوفرة: $1',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 * @author Jim-by
 */
$messages['be-tarask'] = array(
	'maps_desc' => 'Забясьпечвае магчымасьць адлюстраваньня каардынатных зьвестак на мапах і геаграфічнага кадаваньня адрасоў ([http://wiki.bn2vs.com/wiki/Maps дэманстрацыя]). Даступныя геаграфічныя сэрвісы: $1',
);

/** Breton (Brezhoneg)
 * @author Fulup
 */
$messages['br'] = array(
	'maps_desc' => "Talvezout a ra da embann daveennoù ha chomlec'hioù geokod war kartennoù ([http://wiki.bn2vs.com/wiki/Maps demo]). Servijoù kartennaouiñ hegerz : $1",
);

/** Bosnian (Bosanski)
 * @author CERminator
 */
$messages['bs'] = array(
	'maps_desc' => 'Daje mogućnost prikazivanja podataka koordinata na mapama i geocode adresa. Dostupne usluge mapa: $1',
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
	'maps_coordinates_missing' => 'Za kórtu njejsu koordinaty pódane.',
	'maps_geocoding_failed' => 'Geokoděrowanje {{PLURAL:$2|slědujuceje adrese|slědujuceju adresowu|slědujucych adresow|slědujucych adresow}} njejo móžno było: $1. Kórta njedajo se zwobrazniś.',
	'maps_geocoding_failed_for' => 'Geokoděrowanje {{PLURAL:$2|slědujuceje adrese|slědujuceju adresowu|slědujucych adresow|slědujucych adresow}} njejo móžno było a togodla toś {{PLURAL:$2|ta adresa wuwóstaja|tej adresy wuwóstajotej|te adrese wuwóstajaju|te adresy wuwóstajaju}} se na kórśe: $1',
);

/** French (Français)
 * @author Crochet.david
 * @author IAlex
 * @author Verdy p
 */
$messages['fr'] = array(
	'maps_name' => 'Cartes',
	'maps_desc' => 'Permet d’afficher des coordonnées dans des cartes, ainsi que des adresses géocodées ([http://wiki.bn2vs.com/wiki/Maps démonstration]).
Services de cartographie disponibles : $1',
	'maps_coordinates_missing' => "Aucune coordonnée n'a été fournie pour le plan.",
	'maps_geocoding_failed' => "{{PLURAL:$2|L′adresse suivante n'as pu être géocodée|Les adresses suivantes n'ont pas pu être géocodées}} : $1.
Le plan ne peut pas être affiché.",
	'maps_geocoding_failed_for' => '{{PLURAL:$2|L′adresse suivante n’as pu être géocodée|Les adresses suivantes n’ont pas pu être géocodées}} et {{PLURAL:$2|n’est pas affichée|ne sont pas affichées}} sur le plan : $1',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'maps_desc' => 'Proporciona a capacidade de mostrar datos de coordenadas en mapas, e enderezos xeocodificados. Servizos de mapeamento dispoñibles: $1',
);

/** Swiss German (Alemannisch)
 * @author Als-Holder
 */
$messages['gsw'] = array(
	'maps_desc' => 'Ergänzt d Megligkeit Koordinatedate in Charte un Geocodeadrässe aazzeige. Verfiegbari Chartedienscht: $1. [http://www.mediawiki.org/wiki/Extension:Maps Dokumäntation]. [http://wiki.bn2vs.com/wiki/Maps Demo]',
	'maps_coordinates_missing' => 'S git kei Koordinate fir die Charte.',
	'maps_geocoding_failed' => 'Die {{PLURAL:$2|Adräss het|Adräss hän}} nit chenne georeferänziert wäre: $1. D Charte cha nit aazeigt wäre.',
	'maps_geocoding_failed_for' => 'Die {{PLURAL:$2|Adräss het|Adrässe hän}} nit chenne georeferänziert wäre un {{PLURAL:$2|isch|sin}} us dr Charte uusegnuu wore: $1',
);

/** Hebrew (עברית)
 * @author Rotemliss
 * @author YaronSh
 */
$messages['he'] = array(
	'maps_desc' => 'הוספת היכולת להצגת נתוני קואורדינטות במפות וכתובות geocode. שירותי המיפוי הזמינים: $1',
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
	'maps_coordinates_missing' => 'Za kartu njejsu koordinaty podate.',
	'maps_geocoding_failed' => 'Geokodowanje {{PLURAL:$2|slědowaceje adresy|slědowaceju adresow|slědowacych adresow|slědowacych adresow}} njebě móžno: $1. Karta njeda so zwobraznić.',
	'maps_geocoding_failed_for' => 'Geokodowanje {{PLURAL:$2|slědowaceje adresy|slědowaceju adresow|slědowacych adresow|slědowacych adresow}} njebě móžno a {{PLURAL:$2|tuta adresa|tutej adresy|tute adresy|tute adresy}} so na karće {{PLURAL:$2|wuwostaja|wuwostajetej|wuwostajeja|wuwostajeja}}: $1',
);

/** Hungarian (Magyar)
 * @author Glanthor Reviol
 */
$messages['hu'] = array(
	'maps_desc' => 'Lehetővé teszi koordinátaadatok és geokódolt címek megjelenítését térképeken. Elérhető térképszolgáltatások: $1',
);

/** Indonesian (Bahasa Indonesia)
 * @author Bennylin
 */
$messages['id'] = array(
	'maps_desc' => "Memampukan tampilan data koordinat pada peta, dan alamat ''geocode'' ([http://wiki.bn2vs.com/wiki/Maps demo]). 
Layanan pemetaan yang tersedia: $1",
);

/** Japanese (日本語)
 * @author Aotake
 * @author Fryed-peach
 */
$messages['ja'] = array(
	'maps_desc' => '地図上に座標データを表示し、住所を座標データに変換する機能を提供する（[http://wiki.bn2vs.com/wiki/Maps 実演]）。次の地図サービスに対応します：$1',
	'maps_coordinates_missing' => '地図に座標が指定されていません。',
	'maps_geocoding_failed' => '指定された{{PLURAL:$2|住所}}の座標への変換に失敗しました。 $1。地図は表示できません。',
	'maps_geocoding_failed_for' => '指定された{{PLURAL:$2|住所|複数の住所}}の座標への変換に失敗したため、それらを地図から除外して表示します。$1',
);

/** Ripoarisch (Ripoarisch)
 * @author Purodha
 */
$messages['ksh'] = array(
	'maps_desc' => 'Määt et müjjelesch, Koodinaate en Landkaate aanzezeije, un Addräße en Koodinaate op de Ääd ömzerääschne. (E [http://wiki.bn2vs.com/wiki/Maps Beispöll]). He di Deenste för Landkaat(e) ham_mer ze beede: $1',
	'maps_coordinates_missing' => 'Mer han kein Koodinaate för di Kaat.',
	'maps_geocoding_failed' => '{{PLURAL:$2|Di Koodinaat|De Koodinaate|Kein Koodinaat}} om Jlobus för di {{PLURAL:$2|aanjejovve Adräß wohr|aanjejovve Adräße wohre|kein aanjejovve Adräß wohr}} Kappes: $1. Di Kaat künne mer su nit aanzeije.',
	'maps_geocoding_failed_for' => 'De Koodinaate om Jlobus för {{PLURAL:$2|ein|paa|kein}} vun dä aanjejovve Adräße {{PLURAL:$2|es|wohre|Fähler!}} Kappes. Di {{PLURAL:$2|es|sin|Fähler!}} dröm nit op dä Kaat. De fottjelohße {{PLURAL:$2|es|sin|Fähler!}}: $1',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'maps_desc' => "Gëtt d'Méiglechkeet fir d'Date vun de Koordinaten op Kaarten an Geocode Adressen ze weisen. Disponibel mapping Servicer: $1 [http://www.mediawiki.org/wiki/Extension:Maps Dokumentatioun]. [http://wiki.bn2vs.com/wiki/Maps Démo]",
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'maps_desc' => 'Biedt de mogelijkheid om locatiegegevens weer te geven op kaarten en adressen om te zetten naar coordinaten ([http://wiki.bn2vs.com/wiki/Semantic_Maps demo]).
Beschikbare kaartdiensten: $1',
	'maps_coordinates_missing' => 'Er zijn geen coördinaten opgegeven voor de kaart.',
	'maps_geocoding_failed' => 'Voor {{PLURAL:$2|het volgende adres|de volgende adressen}} was geocodering niet mogelijk: $1
De kaart kan niet worden weergegeven.',
	'maps_geocoding_failed_for' => 'Voor {{PLURAL:$2|het volgende adres|de volgende adressen}} was geocodering niet mogelijk en {{PLURAL:$2|dit is|deze zijn}} weggelaten uit de kaart:
$1',
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
);

/** Polish (Polski)
 * @author Sp5uhe
 */
$messages['pl'] = array(
	'maps_desc' => 'Umożliwia wyświetlanie współrzędnych na mapach oraz adresów geograficznych. Dostępne serwisy mapowe: $1',
);

/** Piedmontese (Piemontèis)
 * @author Dragonòt
 */
$messages['pms'] = array(
	'maps_desc' => "A dà la possibilità ëd visualisé le coordinà ant le mape, e j'adrësse geocode. Sërvissi ëd mapatura disponìbil: $1",
);

/** Brazilian Portuguese (Português do Brasil)
 * @author Eduardo.mps
 */
$messages['pt-br'] = array(
	'maps_desc' => 'Provê a possibilidade de exibir dados de coordenadas em mapas e endereços em geocódigo. ([http://wiki.bn2vs.com/wiki/Maps demonstração]).
Serviços de mapeamento disponíveis: $1',
);

/** Romanian (Română)
 * @author KlaudiuMihaila
 */
$messages['ro'] = array(
	'maps_coordinates_missing' => 'Nici o coordonată oferită pentru hartă.',
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
	'maps_coordinates_missing' => 'Не указаны координаты для карты.',
	'maps_geocoding_failed' => '{{PLURAL:$2|Следующий адрес не может быть геокодирован|Следующие адреса не могут быть геокодированы}}: $1.
Карта не может быть отображена.',
	'maps_geocoding_failed_for' => '{{PLURAL:$2|Следующий адрес не может быть геокодирован и был удалён|Следующие адреса не могут быть геокодированы и были удалены}} с карты:
$1',
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
);

/** Vietnamese (Tiếng Việt)
 * @author Vinhtantran
 */
$messages['vi'] = array(
	'maps_desc' => 'Cung cấp khả năng hiển thị dữ liệu tọa độ trên bản đồ, và địa chỉ mã địa lý. Các dịch vụ bản đồ hiện có: $1',
);

