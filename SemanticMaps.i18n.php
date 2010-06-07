<?php

/**
 * Internationalization file for the Semantic Maps extension
 *
 * @file SemanticMaps.i18n.php
 * @ingroup Semantic Maps
 *
 * @author Jeroen De Dauw
 */

$messages = array();

/** English
 * @author Jeroen De Dauw
 */

$messages['en'] = array(
	// General
	'semanticmaps_name' => 'Semantic Maps',
	// TODO: update demo link to the new wiki, once it has 0.6.x running.
	'semanticmaps_desc' => "Provides the ability to view and edit coordinate data stored through the Semantic MediaWiki extension ([http://wiki.bn2vs.com/wiki/Semantic_Maps demo]).
Available map services: $1",
	'semanticmaps-unrecognizeddistance' => 'The value $1 is not a valid distance.',

	// Forms
	'semanticmaps_lookupcoordinates' 	=> 'Look up coordinates',
	'semanticmaps_enteraddresshere' 	=> 'Enter address here',
	'semanticmaps_notfound' 			=> 'not found',
	
	// Parameter descriptions
	'semanticmaps_paramdesc_format' 	=> 'The mapping service used to generate the map',
	'semanticmaps_paramdesc_geoservice' => 'The geocoding service used to turn addresses into coordinates',
	'semanticmaps_paramdesc_height' 	=> 'The height of the map, in pixels (default is $1)',
	'semanticmaps_paramdesc_width' 		=> 'The width of the map, in pixels (default is $1)',
	'semanticmaps_paramdesc_zoom' 		=> 'The zoom level of the map',
	'semanticmaps_paramdesc_centre' 	=> 'The coordinates of the maps\' centre',
	'semanticmaps_paramdesc_controls' 	=> 'The user controls placed on the map',
	'semanticmaps_paramdesc_types' 		=> 'The map types available on the map',
	'semanticmaps_paramdesc_type' 		=> 'The default map type for the map',
	'semanticmaps_paramdesc_overlays' 	=> 'The overlays available on the map',
	'semanticmaps_paramdesc_autozoom' 	=> 'If zoom in and out by using the mouse scroll wheel is enabled',
	'semanticmaps_paramdesc_layers' 	=> 'The layers available on the map',
);

/** Message documentation (Message documentation)
 * @author Fryed-peach
 * @author Purodha
 * @author Raymond
 */
$messages['qqq'] = array(
	'semanticmaps_desc' => '{{desc}}

* $1: a list of available map services',
	'semanticmaps_paramdesc_overlays' => 'An "overlay" is a map layer, containing icons or images, or whatever, to enrich, in this case, the map. Could for example be a layer with speed cameras, or municipality borders.',
);

/** Afrikaans (Afrikaans)
 * @author Naudefj
 */
$messages['af'] = array(
	'semanticmaps_desc' => 'Bied die vermoë om koördinaatdata met behulp van die Semantiese MediaWiki-uitbreiding te sien en te wysig ([http://wiki.bn2vs.com/wiki/Semantic_Maps demo]).
Beskikbare kaartdienste: $1',
	'semanticmaps_lookupcoordinates' => 'Soek koördinate op',
	'semanticmaps_enteraddresshere' => 'Voer adres hier in',
	'semanticmaps_notfound' => 'nie gevind nie',
	'semanticmaps_paramdesc_format' => 'Die kaartdiens wat die kaart lewer',
	'semanticmaps_paramdesc_geoservice' => 'Die geokoderingsdiens gebruik om adresse na koördinate om te skakel',
	'semanticmaps_paramdesc_height' => 'Die hoogte van die kaart in spikkels (standaard is $1)',
	'semanticmaps_paramdesc_width' => 'Die breedte van die kaart in spikkels (standaard is $1)',
	'semanticmaps_paramdesc_zoom' => 'Die zoom-vlak van die kaart',
	'semanticmaps_paramdesc_centre' => 'Die koördinate van die middel van die kaart',
	'semanticmaps_paramdesc_controls' => 'Die gebruikerskontroles op die kaart geplaas',
	'semanticmaps_paramdesc_types' => 'Die kaarttipes beskikbaar op die kaart',
	'semanticmaps_paramdesc_type' => 'Die standaard kaarttipe vir die kaart',
	'semanticmaps_paramdesc_overlays' => 'Die oorleggings beskikbaar op die kaart',
	'semanticmaps_paramdesc_autozoom' => 'Of in- en uitzoom met die muis se wiel moontlik is',
	'semanticmaps_paramdesc_layers' => 'Die lae beskikbaar op die kaart',
);

/** Gheg Albanian (Gegë)
 * @author Mdupont
 */
$messages['aln'] = array(
	'semanticmaps_paramdesc_zoom' => 'Shkalla e zmadhimit Harta',
	'semanticmaps_paramdesc_centre' => "Koordinatat e qendrës hartave '",
	'semanticmaps_paramdesc_controls' => 'Perdoruesi kontrolleve të vendosura në hartë',
	'semanticmaps_paramdesc_types' => 'Llojet Harta dispozicion në hartë',
	'semanticmaps_paramdesc_type' => 'Harta default lloji për hartën',
	'semanticmaps_paramdesc_overlays' => 'Overlays në dispozicion në hartë',
	'semanticmaps_paramdesc_autozoom' => 'Nëse zoom brenda dhe jashtë duke përdorur rrotëzën miut është i aktivizuar',
	'semanticmaps_paramdesc_layers' => 'Shtresat në dispozicion në hartë',
);

/** Arabic (العربية)
 * @author Meno25
 * @author OsamaK
 */
$messages['ar'] = array(
	'semanticmaps_name' => 'خرائط دلالية',
	'semanticmaps_desc' => 'يقدم إمكانية عرض وتعديل بيانات التنسيق التي خزنها امتداد سيمانتيك ميدياويكي ([http://wiki.bn2vs.com/wiki/Semantic_Maps تجربة]).
خدمات الخرائط المتوفرة: $1',
	'semanticmaps_lookupcoordinates' => 'ابحث عن الإحداثيات',
	'semanticmaps_enteraddresshere' => 'أدخل العنوان هنا',
	'semanticmaps_notfound' => 'لم يوجد',
	'semanticmaps_paramdesc_format' => 'خدمة الخرائط المستخدمة لتوليد الخريطة',
	'semanticmaps_paramdesc_geoservice' => 'خدمة التكويد الجغرافي المستخدمة لتحويل العناوين إلى إحداثيات',
	'semanticmaps_paramdesc_height' => 'ارتفاع الخريطة، بالبكسل (افتراضيا $1)',
	'semanticmaps_paramdesc_width' => 'عرض الخريطة، بالبكسل (افتراضيا $1)',
	'semanticmaps_paramdesc_zoom' => 'مستوى التقريب للخريطة',
	'semanticmaps_paramdesc_centre' => 'إحداثيات وسط الخريطة',
	'semanticmaps_paramdesc_controls' => 'متحكمات المستخدم موضوعة على الخريطة',
	'semanticmaps_paramdesc_types' => 'أنواع الخرائط المتوفرة على الخريطة',
	'semanticmaps_paramdesc_type' => 'نوع الخريطة الافتراضي للخريطة',
	'semanticmaps_paramdesc_overlays' => 'الطبقات الفوقية متوفرة على الخريطة',
	'semanticmaps_paramdesc_autozoom' => 'لو أن التقريب والابتعاد بواسطة استخدام عجلة تدحرج الفأرة مفعلة',
	'semanticmaps_paramdesc_layers' => 'الطبقات المتوفرة على الخريطة',
);

/** Egyptian Spoken Arabic (مصرى)
 * @author Ghaly
 * @author Meno25
 */
$messages['arz'] = array(
	'semanticmaps_name' => 'خرائط دلالية',
	'semanticmaps_lookupcoordinates' => 'ابحث عن الإحداثيات',
	'semanticmaps_enteraddresshere' => 'أدخل العنوان هنا',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 * @author Jim-by
 */
$messages['be-tarask'] = array(
	'semanticmaps_name' => 'Сэмантычныя мапы',
	'semanticmaps_desc' => 'Забясьпечвае магчымасьць прагляду і рэдагаваньня зьвестак пра каардынаты, якія захоўваюцца з дапамогай пашырэньня Semantic MediaWiki ([http://wiki.bn2vs.com/wiki/Semantic_Maps дэманстрацыя]). Даступныя сэрвісы мапаў: $1',
	'semanticmaps-unrecognizeddistance' => 'Значэньне $1 — няслушная адлегласьць.',
	'semanticmaps_lookupcoordinates' => 'Пошук каардынатаў',
	'semanticmaps_enteraddresshere' => 'Увядзіце тут адрас',
	'semanticmaps_notfound' => 'ня знойдзена',
	'semanticmaps_paramdesc_format' => 'Картаграфічны сэрвіс, які выкарыстоўваецца для стварэньня мапаў',
	'semanticmaps_paramdesc_geoservice' => 'Сэрвіс геаграфічнага кадаваньня, які выкарыстоўваецца для пераўтварэньня адрасоў ў каардынаты',
	'semanticmaps_paramdesc_height' => 'Вышыня мапы ў піксэлях (па змоўчваньні $1)',
	'semanticmaps_paramdesc_width' => 'Шырыня мапы ў піксэлях (па змоўчваньні $1)',
	'semanticmaps_paramdesc_zoom' => 'Маштаб мапы',
	'semanticmaps_paramdesc_centre' => 'Каардынаты цэнтру мапы',
	'semanticmaps_paramdesc_controls' => 'Элемэнты кіраваньня на мапе',
	'semanticmaps_paramdesc_types' => 'Тыпы мапы даступныя на мапе',
	'semanticmaps_paramdesc_type' => 'Тып мапы па змоўчваньні',
	'semanticmaps_paramdesc_overlays' => 'Даступныя слаі на мапе',
	'semanticmaps_paramdesc_autozoom' => 'Калі ўключана зьмяншэньне ці павялічэньне маштабу праз кола пракруткі мышы',
	'semanticmaps_paramdesc_layers' => 'Даступныя слаі на мапе',
);

/** Breton (Brezhoneg)
 * @author Fohanno
 * @author Fulup
 * @author Y-M D
 */
$messages['br'] = array(
	'semanticmaps_desc' => 'Talvezout a ra da welet ha da gemmañ roadennoù stoket dre an astenn Semantic MediaWiki ([http://wiki.bn2vs.com/wiki/Semantic_Maps demo]). Servijoù kartennoù hegerz : $1',
	'semanticmaps-unrecognizeddistance' => "An talvoud $1 n'eo ket un hed reizh anezhañ.",
	'semanticmaps_lookupcoordinates' => 'Istimañ an daveennoù',
	'semanticmaps_enteraddresshere' => "Merkit ar chomlec'h amañ",
	'semanticmaps_notfound' => "N'eo ket bet kavet",
	'semanticmaps_paramdesc_format' => 'Ar servij kartennaouiñ implijet da grouiñ ar gartenn',
	'semanticmaps_paramdesc_geoservice' => "Ar servij geokodiñ implijet da dreiñ ar chomlec'hioù e daveennoù",
	'semanticmaps_paramdesc_height' => 'Uhelder ar gartenn, e pikseloù ($1 dre izouer)',
	'semanticmaps_paramdesc_width' => 'Ledander ar gartenn, e pikseloù ($1 dre izouer)',
	'semanticmaps_paramdesc_zoom' => 'Live zoum ar gartenn',
	'semanticmaps_paramdesc_centre' => 'Daveennoù kreiz ar gartenn',
	'semanticmaps_paramdesc_controls' => "Ar c'hontrolloù implijer lakaet war ar gartenn",
	'semanticmaps_paramdesc_types' => "Ar seurtoù kartennoù a c'haller kaout war ar gartenn",
	'semanticmaps_paramdesc_type' => 'Ar seurt kartenn dre ziouer evit ar gartenn',
	'semanticmaps_paramdesc_overlays' => "Ar gwiskadoù a c'haller da gaout war ar gartenn",
	'semanticmaps_paramdesc_autozoom' => 'Mard eo gweredekaet ar zoumañ hag an dizoumañ gant rodig al logodenn',
	'semanticmaps_paramdesc_layers' => 'Ar gwiskadoù zo da gaout war ar gartenn',
);

/** Bosnian (Bosanski)
 * @author CERminator
 */
$messages['bs'] = array(
	'semanticmaps_desc' => 'Daje mogućnost pregleda i uređivanja podataka koordinata koji su spremljeni putem Semantic MediaWiki proširenja ([http://wiki.bn2vs.com/wiki/Semantic_Maps demo]).
Dostupne usluge mapa: $1',
	'semanticmaps_lookupcoordinates' => 'Nađi koordinate',
	'semanticmaps_enteraddresshere' => 'Unesite adresu ovdje',
	'semanticmaps_notfound' => 'nije pronađeno',
	'semanticmaps_paramdesc_format' => 'Usluga kartiranja korištena za generiranje karte',
	'semanticmaps_paramdesc_geoservice' => 'Usluga geokodiranja korištena za pretvaranje adresa u koordinate',
	'semanticmaps_paramdesc_height' => 'Visina mape, u pikselima (pretpostavljeno je $1)',
	'semanticmaps_paramdesc_width' => 'Širina mape, u pikselima (pretpostavljeno je $1)',
	'semanticmaps_paramdesc_zoom' => 'Nivo zumiranja mape',
	'semanticmaps_paramdesc_centre' => 'Koordinate centra karte',
	'semanticmaps_paramdesc_controls' => 'Korisničke kontrole postavljene na kartu',
	'semanticmaps_paramdesc_types' => 'Tipovi karti dostupnih na mapi',
	'semanticmaps_paramdesc_type' => 'Pretpostavljeni tip karte za kartu',
	'semanticmaps_paramdesc_overlays' => 'Slojevi dostupni na karti',
	'semanticmaps_paramdesc_autozoom' => 'Ako je zumiranje i odaljavanje putem kotačića na mišu omogućeno',
	'semanticmaps_paramdesc_layers' => 'Slojevi dostupni na mapi',
);

/** Catalan (Català)
 * @author Paucabot
 * @author Solde
 */
$messages['ca'] = array(
	'semanticmaps_notfound' => "no s'ha trobat",
);

/** German (Deutsch)
 * @author DaSch
 * @author Imre
 * @author Pill
 * @author The Evil IP address
 * @author Umherirrender
 */
$messages['de'] = array(
	'semanticmaps_desc' => 'Ergänzt eine Möglichkeit zum Ansehen und Bearbeiten von Koordinaten, die im Rahmen der Erweiterung „Semantisches MediaWiki“ gespeichert wurden ([http://wiki.bn2vs.com/wiki/Semantic_Maps Demo]).
Unterstützte Kartendienste: $1',
	'semanticmaps-unrecognizeddistance' => 'Der Wert $1 ist keine gültige Distanz.',
	'semanticmaps_lookupcoordinates' => 'Koordinaten nachschlagen',
	'semanticmaps_enteraddresshere' => 'Adresse hier eingeben',
	'semanticmaps_notfound' => 'nicht gefunden',
	'semanticmaps_paramdesc_format' => 'Der Kartographiedienst zum Generieren der Karte',
	'semanticmaps_paramdesc_geoservice' => 'Der Geokodierungsdienst, um Adressen in Koordinaten umzuwandeln',
	'semanticmaps_paramdesc_height' => 'Die Höhe der Karte in Pixeln (Standard ist $1)',
	'semanticmaps_paramdesc_width' => 'Die Breite der Karte in Pixeln (Standard ist $1)',
	'semanticmaps_paramdesc_zoom' => 'Die Vergrößerungsstufe der Karte',
	'semanticmaps_paramdesc_centre' => 'Die Koordinaten der Kartenmitte',
	'semanticmaps_paramdesc_controls' => 'Die Benutzerkontrollen, die sich auf der Karte befinden',
	'semanticmaps_paramdesc_types' => 'Die verfügbaren Kartentypen für die Karte',
	'semanticmaps_paramdesc_type' => 'Der Standard-Kartentyp für die Karte',
	'semanticmaps_paramdesc_overlays' => 'Die auf der Karte verfügbaren Overlays',
	'semanticmaps_paramdesc_autozoom' => 'Wenn Vergrößerung und Verkleinerung mit dem Maus-Scrollrad aktiviert ist',
	'semanticmaps_paramdesc_layers' => 'Die auf der Karte verfügbaren Ebenen',
);

/** Lower Sorbian (Dolnoserbski)
 * @author Michawiki
 */
$messages['dsb'] = array(
	'semanticmaps_desc' => 'Bitujo zmóžnosć se koordinatowe daty pśez rozšyrjenje Semantic MediaWiki woglědaś a wobźěłaś ([http://wiki.bn2vs.com/wiki/Semantic_Maps demo]).
K dispoziciji stojece kórtowe słužby: $1.',
	'semanticmaps-unrecognizeddistance' => 'Gódnota $1 njejo płaśiwa distanca.',
	'semanticmaps_lookupcoordinates' => 'Za koordinatami póglědaś',
	'semanticmaps_enteraddresshere' => 'Zapódaj how adresu',
	'semanticmaps_notfound' => 'njenamakany',
	'semanticmaps_paramdesc_format' => 'Kartěrowańska słužba, kótaraž se wužywa, aby napórała kórtu',
	'semanticmaps_paramdesc_geoservice' => 'Geokoděrowańska słužba, kótaraž se wužywa, aby pśetwóriła adrese do koordinatow',
	'semanticmaps_paramdesc_height' => 'Wusokosć kórty, w pikselach (standard jo $1)',
	'semanticmaps_paramdesc_width' => 'Šyrokosć kórty, w pikselach (standard jo $1)',
	'semanticmaps_paramdesc_zoom' => 'Skalěrowański schóźeńk kórty',
	'semanticmaps_paramdesc_centre' => 'Koordinaty srjejźišća kórty',
	'semanticmaps_paramdesc_controls' => 'Wužywarske elementy na kórśe',
	'semanticmaps_paramdesc_types' => 'Kórtowe typy, kótarež stoje za kórtu k dispoziciji',
	'semanticmaps_paramdesc_type' => 'Standardny kórtowy typ za kórtu',
	'semanticmaps_paramdesc_overlays' => 'Pśewarstowanja, kótarež stoje za kórtu k dispoziciji',
	'semanticmaps_paramdesc_autozoom' => 'Jolic pówětšenje a pómjeńšenje z pomocu kólaska myški jo zmóžnjone',
	'semanticmaps_paramdesc_layers' => 'Warsty, kótarež stoje za kórtu k dispoziciji',
);

/** Greek (Ελληνικά)
 * @author ZaDiak
 */
$messages['el'] = array(
	'semanticmaps_lookupcoordinates' => 'Επιθεώρηση συντεταγμένων',
	'semanticmaps_enteraddresshere' => 'Εισαγωγή διεύθυνσης εδώ',
	'semanticmaps_notfound' => 'δεν βρέθηκε',
);

/** Esperanto (Esperanto)
 * @author Yekrats
 */
$messages['eo'] = array(
	'semanticmaps_lookupcoordinates' => 'Rigardi koordinatojn',
	'semanticmaps_enteraddresshere' => 'Enigu adreson ĉi tie',
	'semanticmaps_notfound' => 'ne trovita',
);

/** Spanish (Español)
 * @author Crazymadlover
 * @author Imre
 * @author Locos epraix
 * @author Translationista
 */
$messages['es'] = array(
	'semanticmaps_desc' => 'Proporciona la capacidad de ver y editar los datos coordinados almacenados a través de la extensión Semantic MediaWiki ([http://wiki.bn2vs.com/wiki/Semantic_Maps demo]).
Servicios de mapas disponibles: $1',
	'semanticmaps-unrecognizeddistance' => 'El valor $1 no esuna distancia válida.',
	'semanticmaps_lookupcoordinates' => 'Busque las coordenadas',
	'semanticmaps_enteraddresshere' => 'Ingresar dirección aquí',
	'semanticmaps_notfound' => 'no encontrado',
	'semanticmaps_paramdesc_format' => 'El servicio cartográfico usado para generar el mapa',
	'semanticmaps_paramdesc_geoservice' => 'El servicio de geocodificación para convertir direcciones en coordenadas',
	'semanticmaps_paramdesc_height' => 'Alto del mapa en píxeles (el predeterminado es $1)',
	'semanticmaps_paramdesc_width' => 'Ancho del mapa en píxeles (el predeterminado es $1)',
	'semanticmaps_paramdesc_zoom' => 'Nivel de acercamiento del mapa',
	'semanticmaps_paramdesc_centre' => 'Las coordenadas del centro del mapa',
	'semanticmaps_paramdesc_controls' => 'Los controles de usuario ubicados en el mapa',
	'semanticmaps_paramdesc_types' => 'Los tipos de mapa disponibles en el mapa',
	'semanticmaps_paramdesc_type' => 'El tipo de mapa predeterminado para el mapa',
	'semanticmaps_paramdesc_overlays' => 'FUZZY!!! Las capas disponibles en el mapa',
	'semanticmaps_paramdesc_autozoom' => 'En caso de que el acercamiento y alejamiento mediante la rueda del ratón esté habilitado',
	'semanticmaps_paramdesc_layers' => 'Las capas disponibles en el mapa',
);

/** Basque (Euskara)
 * @author An13sa
 */
$messages['eu'] = array(
	'semanticmaps_lookupcoordinates' => 'Koordenatuak bilatu',
);

/** Finnish (Suomi)
 * @author Crt
 * @author Str4nd
 */
$messages['fi'] = array(
	'semanticmaps_enteraddresshere' => 'Kirjoita osoite tähän',
	'semanticmaps_notfound' => 'ei löytynyt',
	'semanticmaps_paramdesc_height' => 'Kartan korkeus pikseleinä (oletus on $1)',
	'semanticmaps_paramdesc_width' => 'Kartan leveys pikseleinä (oletus on $1)',
	'semanticmaps_paramdesc_zoom' => 'Kartan suurennostaso',
	'semanticmaps_paramdesc_centre' => 'Kartan keskipisteen koordinaatit',
);

/** French (Français)
 * @author Crochet.david
 * @author Grondin
 * @author IAlex
 * @author Jean-Frédéric
 * @author PieRRoMaN
 * @author Urhixidur
 */
$messages['fr'] = array(
	'semanticmaps_desc' => 'Permet de voir et modifier les données de coordonnées stockées à travers l’extension Semantic MediaWiki ([http://www.mediawiki.org/wiki/Extension:Semantic_Maps Documentation]. [http://wiki.bn2vs.com/wiki/Semantic_Maps Démo]). Services de cartes disponibles : $1.',
	'semanticmaps-unrecognizeddistance' => "La valeur $1 n'est pas une distance valide",
	'semanticmaps_lookupcoordinates' => 'Estimer les coordonnées',
	'semanticmaps_enteraddresshere' => 'Entrez ici l’adresse',
	'semanticmaps_notfound' => 'pas trouvé',
	'semanticmaps_paramdesc_format' => 'Le service de cartographie utilisé pour générer la carte',
	'semanticmaps_paramdesc_geoservice' => 'Le service de géocodage utilisé pour transformer les adresses en coordonnées',
	'semanticmaps_paramdesc_height' => 'La hauteur de la carte, en pixels ($1 par défaut)',
	'semanticmaps_paramdesc_width' => 'La largeur de la carte, en pixels ($1 par défaut)',
	'semanticmaps_paramdesc_zoom' => 'Le niveau d’agrandissement de la carte',
	'semanticmaps_paramdesc_centre' => 'Les coordonnées du centre de la carte',
	'semanticmaps_paramdesc_controls' => 'Les contrôles utilisateurs placés sur la carte',
	'semanticmaps_paramdesc_types' => 'Les types de cartes disponibles sur la carte',
	'semanticmaps_paramdesc_type' => 'Le type de carte par défaut pour la carte',
	'semanticmaps_paramdesc_overlays' => 'Les revêtements disponibles sur la carte',
	'semanticmaps_paramdesc_autozoom' => 'Si le zoom avant et arrière en utilisant la molette de la souris est activé',
	'semanticmaps_paramdesc_layers' => 'Les revêtements disponibles sur la carte',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'semanticmaps_desc' => 'Proporciona a capacidade de visualizar e modificar os datos de coordenadas gardados a través da extensión Semantic MediaWiki ([http://wiki.bn2vs.com/wiki/Semantic_Maps demostración]).
Servizos de mapa dispoñibles: $1',
	'semanticmaps-unrecognizeddistance' => 'O valor $1 non é unha distancia válida.',
	'semanticmaps_lookupcoordinates' => 'Ver as coordenadas',
	'semanticmaps_enteraddresshere' => 'Introduza o enderezo aquí',
	'semanticmaps_notfound' => 'non se atopou',
	'semanticmaps_paramdesc_format' => 'O servizo de cartografía utilizado para xerar o mapa',
	'semanticmaps_paramdesc_geoservice' => 'O servizo de xeocodificación usado para transformar enderezos en coordenadas',
	'semanticmaps_paramdesc_height' => 'A altura do mapa, en píxeles (por defecto, $1)',
	'semanticmaps_paramdesc_width' => 'O largo do mapa, en píxeles (por defecto, $1)',
	'semanticmaps_paramdesc_zoom' => 'O nivel de zoom do mapa',
	'semanticmaps_paramdesc_centre' => 'As coordenadas do centro do mapa',
	'semanticmaps_paramdesc_controls' => 'Os controis de usuario situados no mapa',
	'semanticmaps_paramdesc_types' => 'Os tipos de mapa dispoñibles no mapa',
	'semanticmaps_paramdesc_type' => 'O tipo de mapa por defecto para o mapa',
	'semanticmaps_paramdesc_overlays' => 'As sobreposicións dispoñibles no mapa',
	'semanticmaps_paramdesc_autozoom' => 'Activa o achegamento e afastamento coa roda do rato',
	'semanticmaps_paramdesc_layers' => 'As capas dispoñibles no mapa',
);

/** Swiss German (Alemannisch)
 * @author Als-Holder
 */
$messages['gsw'] = array(
	'semanticmaps_desc' => 'Ergänzt e Megligkeit zum Aaluege un Bearbeite vu Koordinate, wu im Ramme vu dr Erwyterig „Semantisch MediaWiki“ gspycheret wore sin. Unterstitzti Chartedienscht: $1. [http://www.mediawiki.org/wiki/Extension:Semantic_Maps Dokumäntation]. [http://wiki.bn2vs.com/wiki/Semantic_Maps Demo]',
	'semanticmaps-unrecognizeddistance' => 'Dr Wert $1 isch kei giltigi Dischtanz.',
	'semanticmaps_lookupcoordinates' => 'Koordinate nooluege',
	'semanticmaps_enteraddresshere' => 'Doo Adräss yygee',
	'semanticmaps_notfound' => 'nit gfunde',
	'semanticmaps_paramdesc_format' => 'Dr Chartedienscht, wu brucht wäre soll zum Erzyyge vu dr Charte',
	'semanticmaps_paramdesc_geoservice' => 'Dr Geokodierigs-Service, wu brucht wäre soll zum umwandle vu Adrässe in Koordinate',
	'semanticmaps_paramdesc_height' => 'D Hechi vu dr Charte, in Pixel (Standard: $1)',
	'semanticmaps_paramdesc_width' => 'D Breiti vu dr Charte, in Pixel (Standard: $1)',
	'semanticmaps_paramdesc_zoom' => 'S Zoom-Level vu dr Charte',
	'semanticmaps_paramdesc_centre' => 'D Koordinate vum Mittelpunkt vu dr Charte',
	'semanticmaps_paramdesc_controls' => 'D Hilfsmittel, wu in d Charte yygfiegt sin',
	'semanticmaps_paramdesc_types' => 'D Chartetype, wu fir d Charte verfiegbar sin',
	'semanticmaps_paramdesc_type' => 'Dr Standard-Chartetyp fir d Charte',
	'semanticmaps_paramdesc_overlays' => 'D Overlays, wu fir d Charte verfiegbar sin',
	'semanticmaps_paramdesc_autozoom' => 'Eb mer e Charte cha vergreßere oder verchleinere mit em Muusrad',
	'semanticmaps_paramdesc_layers' => 'D Lage, wu fir Charte verfiegbar sin',
);

/** Hebrew (עברית)
 * @author Rotemliss
 * @author YaronSh
 */
$messages['he'] = array(
	'semanticmaps_desc' => 'הוספת האפשרות לצפייה ולעריכה בנתוני קואורדינטה המאוחסנים דרך הרחבת המדיה־ויקי הסמנטי ([http://wiki.bn2vs.com/wiki/Semantic_Maps הדגמה]).
שירותי מפה זמינים: $1',
	'semanticmaps_lookupcoordinates' => 'חיפוש קואורדינטות',
	'semanticmaps_enteraddresshere' => 'כתבו כתובת כאן',
	'semanticmaps_notfound' => 'לא נמצאה',
	'semanticmaps_paramdesc_format' => 'שירות המיפוי המשמש להכנת המפה',
	'semanticmaps_paramdesc_height' => 'גובה המפה, בפיקסלים (ברירת המחדל היא $1)',
	'semanticmaps_paramdesc_width' => 'רוחב המפה, בפיקסלים (ברירת המחדל היא $1)',
	'semanticmaps_paramdesc_centre' => 'קואורדינטות מרכז המפה',
	'semanticmaps_paramdesc_controls' => 'פקדי המשתמש ממוקמים על המפה',
	'semanticmaps_paramdesc_types' => 'צורות המפה הזמינות על המפה',
	'semanticmaps_paramdesc_type' => 'סוג ברירת המחדל של המפה עבור המפה',
	'semanticmaps_paramdesc_layers' => 'השכבות הזמינות במפה',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'semanticmaps_desc' => 'Skići móžnosć koordinatowe daty, kotrež buchu přez rozšěrjenje Semantic MediaWiki składowane, sej wobhladać a změnić. ([http://wiki.bn2vs.com/wiki/Semantic_Maps demo]). K dispoziciji stejace kartowe słužby: $1',
	'semanticmaps-unrecognizeddistance' => 'Hódnota $1 płaćiwa distanca njeje.',
	'semanticmaps_lookupcoordinates' => 'Za koordinatami hladać',
	'semanticmaps_enteraddresshere' => 'Zapodaj tu adresu',
	'semanticmaps_notfound' => 'njenamakany',
	'semanticmaps_paramdesc_format' => 'Kartěrowanska słužba, kotraž so wužiwa, zo by kartu wutworiła',
	'semanticmaps_paramdesc_geoservice' => 'Geokodowanska słužba, kotraž so wužiwa, zo by adresy do koordinatow přetworiła',
	'semanticmaps_paramdesc_height' => 'Wysokosć karty, w pikselach (standard je $1)',
	'semanticmaps_paramdesc_width' => 'Šěrokosć karty, w pikselach (standard je $1)',
	'semanticmaps_paramdesc_zoom' => 'Skalowanski schodźenk karty',
	'semanticmaps_paramdesc_centre' => 'Koordinaty srjedźišća karty',
	'semanticmaps_paramdesc_controls' => 'Wužiwarske elementy na karće',
	'semanticmaps_paramdesc_types' => 'Kartowe typy, kotrež za kartu k dispoziciji steja',
	'semanticmaps_paramdesc_type' => 'Standardny kartowy typ za kartu',
	'semanticmaps_paramdesc_overlays' => 'Naworštowanja, kotrež za kartu k dispoziciji steja',
	'semanticmaps_paramdesc_autozoom' => 'Jeli powjetšenje a pomjenšenje z pomocu kolesko myški je zmóžnjene',
	'semanticmaps_paramdesc_layers' => 'Woršty, kotrež za kartu k dispoziciji steja',
);

/** Hungarian (Magyar)
 * @author Glanthor Reviol
 */
$messages['hu'] = array(
	'semanticmaps_desc' => 'Lehetővé teszi a szemantikus MediaWiki kiterjesztés segítségével tárolt koordinátaadatok megtekintését és szerkesztését ([http://wiki.bn2vs.com/wiki/Semantic_Maps demo]).
Elérhető térképszolgáltatók: $1',
	'semanticmaps_lookupcoordinates' => 'Koordináták felkeresése',
	'semanticmaps_enteraddresshere' => 'Add meg a címet itt',
	'semanticmaps_notfound' => 'nincs találat',
	'semanticmaps_paramdesc_height' => 'A térkép magassága, képpontban (alapértelmezetten $1)',
	'semanticmaps_paramdesc_width' => 'A térkép szélessége, képpontban (alapértelmezetten $1)',
	'semanticmaps_paramdesc_zoom' => 'A térkép nagyítása',
	'semanticmaps_paramdesc_centre' => 'A térkép középpontjának koordinátái',
	'semanticmaps_paramdesc_types' => 'A térképen elérhető térképtípusok',
	'semanticmaps_paramdesc_type' => 'A térkép alapértelmezett térképtípusa',
	'semanticmaps_paramdesc_layers' => 'A térképen elérhető rétegek',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'semanticmaps_desc' => 'Permitte vider e modificar datos de coordinatas immagazinate per le extension Semantic MediaWiki
([http://wiki.bn2vs.com/wiki/Semantic_Maps demo]).
Servicios cartographic disponibile: $1',
	'semanticmaps-unrecognizeddistance' => 'Le valor $1 non es un distantia valide.',
	'semanticmaps_lookupcoordinates' => 'Cercar coordinatas',
	'semanticmaps_enteraddresshere' => 'Entra adresse hic',
	'semanticmaps_notfound' => 'non trovate',
	'semanticmaps_paramdesc_format' => 'Le servicio cartographic usate pro generar le carta',
	'semanticmaps_paramdesc_geoservice' => 'Le servicio de geocodification usate pro converter adresses in coordinatas',
	'semanticmaps_paramdesc_height' => 'Le altitude del carta, in pixeles (predefinition es $1)',
	'semanticmaps_paramdesc_width' => 'Le latitude del carta, in pixeles (predefinition es $1)',
	'semanticmaps_paramdesc_zoom' => 'Le nivello de zoom del carta',
	'semanticmaps_paramdesc_centre' => 'Le coordinatas del centro del carta',
	'semanticmaps_paramdesc_controls' => 'Le buttones de adjustamento placiate in le carta',
	'semanticmaps_paramdesc_types' => 'Le typos de carta disponibile in le carta',
	'semanticmaps_paramdesc_type' => 'Le typo de carta predefinite pro le carta',
	'semanticmaps_paramdesc_overlays' => 'Le superpositiones disponibile in le carta',
	'semanticmaps_paramdesc_autozoom' => 'Si le zoom avante e retro con le rota de rolamento del mouse es active',
	'semanticmaps_paramdesc_layers' => 'Le stratos disponibile in le carta',
);

/** Indonesian (Bahasa Indonesia)
 * @author Bennylin
 * @author Farras
 */
$messages['id'] = array(
	'semanticmaps_desc' => 'Memampukan penampilan dan penyuntingan data koordinat yang disimpan melalui pengaya MediaWiki Semantic ([http://wiki.bn2vs.com/wiki/Semantic_Maps demo]). 
Layanan peta yang tersedia: $1',
	'semanticmaps_lookupcoordinates' => 'Cari koordinat',
	'semanticmaps_enteraddresshere' => 'Masukkan alamat di sini',
	'semanticmaps_notfound' => 'tidak ditemukan',
	'semanticmaps_paramdesc_format' => 'Layanan pemetaan untuk membuat peta',
	'semanticmaps_paramdesc_geoservice' => 'Layanan kode geo untuk mengubah alamat menjadi koordinat',
	'semanticmaps_paramdesc_height' => 'Tinggi peta, dalam piksel (umumnya $1)',
	'semanticmaps_paramdesc_width' => 'Lebar peta, dalam piksel (umumnya $1)',
	'semanticmaps_paramdesc_zoom' => 'Tingkat zum peta',
	'semanticmaps_paramdesc_centre' => 'Koordinat bagian tengah peta',
	'semanticmaps_paramdesc_controls' => 'Kontrol pengguna yang diletakkan di peta',
	'semanticmaps_paramdesc_types' => 'Jenis peta tersedia di peta',
	'semanticmaps_paramdesc_type' => 'Jenis peta biasa untuk peta ini',
	'semanticmaps_paramdesc_overlays' => 'Lapisan yang tersedia di peta',
	'semanticmaps_paramdesc_autozoom' => 'Bila ingin zum dekat dan jauh menggunakan mouse, gunakan roda gulung',
	'semanticmaps_paramdesc_layers' => 'Lapisan tersedia di peta',
);

/** Italian (Italiano)
 * @author Civvì
 * @author Darth Kule
 */
$messages['it'] = array(
	'semanticmaps_desc' => "Offre la possibilità di visualizzare e modificare le coordinate memorizzate attraverso l'estensione Semantic MediaWiki ([http://wiki.bn2vs.com/wiki/Semantic_Maps demo]). Servizi di mappe disponibili: $1",
	'semanticmaps_lookupcoordinates' => 'Cerca coordinate',
	'semanticmaps_enteraddresshere' => 'Inserisci indirizzo qui',
	'semanticmaps_notfound' => 'non trovato',
	'semanticmaps_paramdesc_format' => 'Il servizio di mapping utilizzato per generare la mappa',
	'semanticmaps_paramdesc_geoservice' => 'Il servizio di geocoding utilizzato per trasformare gli indirizzi in coordinate',
	'semanticmaps_paramdesc_height' => "L'altezza della mappa in pixel (il valore di default è $1)",
	'semanticmaps_paramdesc_width' => 'La larghezza della mappa in pixel (il valore di default è $1)',
	'semanticmaps_paramdesc_zoom' => 'Il livello di zoom della mappa',
	'semanticmaps_paramdesc_centre' => 'Le coordinate del centro della mappa',
	'semanticmaps_paramdesc_controls' => 'I controlli utente posizionati sulla mappa',
	'semanticmaps_paramdesc_types' => 'I tipi di mappa disponibili sulla mappa',
	'semanticmaps_paramdesc_type' => 'Il tipo mappa predefinito per la mappa',
	'semanticmaps_paramdesc_overlays' => 'Gli overlay disponibili sulla mappa',
	'semanticmaps_paramdesc_autozoom' => 'Se sono attivati lo zoom avanti e indietro utilizzando la rotellina del mouse',
	'semanticmaps_paramdesc_layers' => 'Gli strati (layer) disponibili sulla mappa',
);

/** Japanese (日本語)
 * @author Fryed-peach
 * @author Mizusumashi
 * @author 青子守歌
 */
$messages['ja'] = array(
	'semanticmaps_desc' => 'Semantic MediaWiki 拡張機能を通して格納された座標データを表示・編集する機能を提供する ([http://wiki.bn2vs.com/wiki/Semantic_Maps 実演])。次の地図サービスに対応します：$1',
	'semanticmaps-unrecognizeddistance' => '値$1は有効な距離ではありません。',
	'semanticmaps_lookupcoordinates' => '座標を調べる',
	'semanticmaps_enteraddresshere' => '住所をここに入力します',
	'semanticmaps_notfound' => '見つかりません',
	'semanticmaps_paramdesc_format' => '地図の生成に利用されている地図サービス',
	'semanticmaps_paramdesc_geoservice' => '住所の座標への変換に利用されているジオコーディングサービス',
	'semanticmaps_paramdesc_height' => '地図の縦幅 (単位はピクセル、既定は$1)',
	'semanticmaps_paramdesc_width' => '地図の横幅 (単位はピクセル、既定は$1)',
	'semanticmaps_paramdesc_zoom' => '地図の拡大度',
	'semanticmaps_paramdesc_centre' => '地図の中心の座標',
	'semanticmaps_paramdesc_controls' => 'この地図上に設置するユーザーコントロール',
	'semanticmaps_paramdesc_types' => 'この地図で利用できる地図タイプ',
	'semanticmaps_paramdesc_type' => 'この地図のデフォルト地図タイプ',
	'semanticmaps_paramdesc_overlays' => 'この地図で利用できるオーバーレイ',
	'semanticmaps_paramdesc_autozoom' => 'マウスのスクロールホイールを使ったズームインやアウトを有効にするか',
	'semanticmaps_paramdesc_layers' => 'この地図で利用できるレイヤー',
);

/** Khmer (ភាសាខ្មែរ)
 * @author Thearith
 */
$messages['km'] = array(
	'semanticmaps_lookupcoordinates' => 'ក្រឡេក​មើល​កូអរដោនេ',
);

/** Colognian (Ripoarisch)
 * @author Purodha
 */
$messages['ksh'] = array(
	'semanticmaps_desc' => 'Määt et müjjelesch, Koodinaate ze beloore un ze ändere, di per Semantesch Mediawiki faßjehallde woodte. (E [http://wiki.bn2vs.com/wiki/Semantic_Maps Beijshpöll]) Deenste för Kaate ham_mer di heh: $1',
	'semanticmaps_lookupcoordinates' => 'Koordinate nohkike',
	'semanticmaps_enteraddresshere' => 'Donn hee de Address enjäve',
	'semanticmaps_notfound' => 'nit jefonge',
	'semanticmaps_paramdesc_format' => 'Dä Deens för Kaate ußzejävve, woh heh di Kaat vun kütt',
	'semanticmaps_paramdesc_geoservice' => "Dä Deens för Adräße en Ko'odinaate öm_ze_wandelle",
	'semanticmaps_paramdesc_height' => 'De Hühde vun heh dä Kaat en Pixelle — schtandattmääßesch {{PLURAL:$1|$1 Pixel|$1 Pixelle|nix}}',
	'semanticmaps_paramdesc_width' => 'De Breedt vun heh dä Kaat en Pixelle — schtandattmääßesch {{PLURAL:$1|$1 Pixel|$1 Pixelle|nix}}',
	'semanticmaps_paramdesc_zoom' => 'Wi doll dä Zoom fö heh di Kaat es',
	'semanticmaps_paramdesc_centre' => "De Ko'odinaate op de Ääd, vun de Medde vun heh dä Kaat",
	'semanticmaps_paramdesc_controls' => 'De Knöppe för de Bedeenung, di op di Kaat jemohlt wäääde',
	'semanticmaps_paramdesc_types' => 'De Kaate-Zoote di mer för heh di Kaat ußsöhke kann',
	'semanticmaps_paramdesc_type' => 'De Schtandatt Kaate-Zoot för heh di Kaat',
	'semanticmaps_paramdesc_overlays' => 'De zohsäzlijje Eijnzelheijte, di mer op di Kaat drop bränge kann',
	'semanticmaps_paramdesc_autozoom' => 'Falls et erin un eruß zoome met däm Kompjuter singe Muuß ierem Rättsche aanjeschalldt es, dann:',
	'semanticmaps_paramdesc_layers' => 'De Nivohs, di för di Kaat ze han sin',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'semanticmaps_lookupcoordinates' => 'Koordinaten nokucken',
	'semanticmaps_enteraddresshere' => 'Adress hei aginn',
	'semanticmaps_notfound' => 'net fonnt',
	'semanticmaps_paramdesc_format' => "De Kartographie-Service dee fir d'generéiere vun der Kaart benotzt gëtt",
	'semanticmaps_paramdesc_height' => "D'Héicht vun der Kaart, a Pixelen (Standard ass $1)",
	'semanticmaps_paramdesc_width' => "D'Breet vun der Kaart, a Pixelen (Standard ass $1)",
	'semanticmaps_paramdesc_zoom' => 'DenNiveau vum Zoom vun der Kaart',
	'semanticmaps_paramdesc_centre' => "D'Koordinate vum zentrum vun der Kaart",
	'semanticmaps_paramdesc_controls' => "D'Benotzerkontrollen déi op der Kaart plazéiert sinn",
);

/** Macedonian (Македонски)
 * @author Bjankuloski06
 */
$messages['mk'] = array(
	'semanticmaps_desc' => 'Дава можност за гледање и уредување на податоци со координати складирани преку проширувањето Semantic MediaWiki ([http://wiki.bn2vs.com/wiki/Semantic_Maps демо]).
Картографски служби на располагање: $1',
	'semanticmaps-unrecognizeddistance' => 'Вредноста $1 не претставува важечко растојание.',
	'semanticmaps_lookupcoordinates' => 'Побарај координати',
	'semanticmaps_enteraddresshere' => 'Внесете адреса тука',
	'semanticmaps_notfound' => 'не е најдено ништо',
	'semanticmaps_paramdesc_format' => 'Картографската служба со која се создава картата',
	'semanticmaps_paramdesc_geoservice' => 'Службата за геокодирање со која адресите се претвораат во координати',
	'semanticmaps_paramdesc_height' => 'Висината на картата во пиксели ($1 по основно)',
	'semanticmaps_paramdesc_width' => 'Ширината на картата во пиксели ($1 по основно)',
	'semanticmaps_paramdesc_zoom' => 'Размерот на картата',
	'semanticmaps_paramdesc_centre' => 'Координатите на средиштето на картата',
	'semanticmaps_paramdesc_controls' => 'Корисничките контроли за на картата',
	'semanticmaps_paramdesc_types' => 'Типови на карти, достапни за картата',
	'semanticmaps_paramdesc_type' => 'Основно зададениот тип на карта',
	'semanticmaps_paramdesc_overlays' => 'Достапните облоги за картата',
	'semanticmaps_paramdesc_autozoom' => 'Ако е овозможено приближување и оддалечување со тркалцето на глушецот',
	'semanticmaps_paramdesc_layers' => 'Слоевите достапни на картата',
);

/** Dutch (Nederlands)
 * @author Jeroen De Dauw
 * @author Siebrand
 */
$messages['nl'] = array(
	'semanticmaps_desc' => 'Biedt de mogelijkheid om locatiegegevens die zijn opgeslagen met behulp van de uitbreiding Semantic MediaWiki te bekijken en aan te passen ([http://wiki.bn2vs.com/wiki/Semantic_Maps demo]).
Beschikbare kaartdiensten: $1',
	'semanticmaps-unrecognizeddistance' => 'De waarde "$1" is geen geldige afstand.',
	'semanticmaps_lookupcoordinates' => 'Coördinaten opzoeken',
	'semanticmaps_enteraddresshere' => 'Voer hier het adres in',
	'semanticmaps_notfound' => 'niet gevonden',
	'semanticmaps_paramdesc_format' => 'De kaartdienst die de kaart levert',
	'semanticmaps_paramdesc_geoservice' => 'De geocoderingsdienst die adressen in coördinaten converteert',
	'semanticmaps_paramdesc_height' => 'De hoogte van de kaart in pixels (standaard is $1)',
	'semanticmaps_paramdesc_width' => 'De breedte van de kaart in pixels (standaard is $1)',
	'semanticmaps_paramdesc_zoom' => 'Het zoomniveau van de kaart',
	'semanticmaps_paramdesc_centre' => 'De coördinaten van het midden van de kaart',
	'semanticmaps_paramdesc_controls' => 'De op de kaart te plaatsen hulpmiddelen',
	'semanticmaps_paramdesc_types' => 'De voor de kaart beschikbare kaarttypen',
	'semanticmaps_paramdesc_type' => 'Het standaard kaarttype voor de kaart',
	'semanticmaps_paramdesc_overlays' => 'De voor de kaart beschikbare overlays',
	'semanticmaps_paramdesc_autozoom' => 'Of in- en uitzoomen met het scrollwiel van de muis mogelijk is',
	'semanticmaps_paramdesc_layers' => 'De lagen die beschikbaar zijn voor de kaart',
);

/** Norwegian Nynorsk (‪Norsk (nynorsk)‬)
 * @author Harald Khan
 */
$messages['nn'] = array(
	'semanticmaps_lookupcoordinates' => 'Sjekk koordinatar',
	'semanticmaps_enteraddresshere' => 'Skriv inn adressa her',
);

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 * @author Nghtwlkr
 */
$messages['no'] = array(
	'semanticmaps_desc' => 'Tilbyr muligheten til å se og endre koordinatdata lagret gjennom Semantic MediaWiki-utvidelsen ([http://wiki.bn2vs.com/wiki/Semantic_Maps demo]).
Tilgjengelige karttjenester: $1',
	'semanticmaps_lookupcoordinates' => 'Sjekk koordinater',
	'semanticmaps_enteraddresshere' => 'Skriv inn adressen her',
	'semanticmaps_notfound' => 'ikke funnet',
	'semanticmaps_paramdesc_format' => 'Karttjenesten brukt for å generere kart',
	'semanticmaps_paramdesc_geoservice' => 'Geokodetjenesten brukt for å gjøre adresser om til koordinater',
	'semanticmaps_paramdesc_height' => 'Høyden til kartet, i pixler (standard er $1)',
	'semanticmaps_paramdesc_width' => 'Bredden til kartet, i pixler (standard er $1)',
	'semanticmaps_paramdesc_zoom' => 'Zoomnivået til kartet',
	'semanticmaps_paramdesc_centre' => 'Koordinatene til kartets senter',
	'semanticmaps_paramdesc_controls' => 'Brukerkontrollene plassert på kartet',
	'semanticmaps_paramdesc_types' => 'Karttypene tilgjengelig for kartet',
	'semanticmaps_paramdesc_type' => 'Standard karttype for kartet',
	'semanticmaps_paramdesc_overlays' => 'Overlag tilgjengelig for kartet',
	'semanticmaps_paramdesc_autozoom' => 'Dersom zooming ved bruk av musehjulet er slått på',
	'semanticmaps_paramdesc_layers' => 'Lagene tilgjengelig på kartet',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'semanticmaps_desc' => "Permet de veire e modificar las donadas de coordenadas estocadas a travèrs l'extension Semantic MediaWiki. Servicis de mapas disponibles : $1. [http://www.mediawiki.org/wiki/Extension:Semantic_Maps Documentacion]. [http://wiki.bn2vs.com/wiki/Semantic_Maps Demo]",
	'semanticmaps_lookupcoordinates' => 'Estimar las coordenadas',
	'semanticmaps_enteraddresshere' => 'Picatz aicí l’adreça',
	'semanticmaps_notfound' => 'pas trobat',
);

/** Polish (Polski)
 * @author Deejay1
 * @author Derbeth
 * @author Leinad
 * @author Odder
 * @author Sp5uhe
 */
$messages['pl'] = array(
	'semanticmaps_desc' => 'Daje możliwość przeglądania oraz edytowania współrzędnych zapisanych przez rozszerzenie Semantic MediaWiki ([http://wiki.bn2vs.com/wiki/Semantic_Maps demo]).
Dostępne serwisy mapowe: $1',
	'semanticmaps-unrecognizeddistance' => 'Wartość $1 nie jest poprawną odległością.',
	'semanticmaps_lookupcoordinates' => 'Wyszukaj współrzędne',
	'semanticmaps_enteraddresshere' => 'Podaj adres',
	'semanticmaps_notfound' => 'nie odnaleziono',
	'semanticmaps_paramdesc_height' => 'Wysokość mapy w pikselach (domyślnie $1)',
	'semanticmaps_paramdesc_width' => 'Szerokość mapy w pikselach (domyślnie $1)',
	'semanticmaps_paramdesc_layers' => 'Warstwy dostępne na mapie',
);

/** Piedmontese (Piemontèis)
 * @author Borichèt
 * @author Dragonòt
 */
$messages['pms'] = array(
	'semanticmaps_desc' => 'A dà la possibilità ëd visualisé e modìfiché le coordinà memorisà con le estension Semantic mediaWiki ([http://wiki.bn2vs.com/wiki/Semantic_Maps demo]).
Sërvissi ëd mapa disponìbij: $1',
	'semanticmaps-unrecognizeddistance' => "Ël valor $1 a l'é pa na distansa bon-a.",
	'semanticmaps_lookupcoordinates' => 'Serca coordinà',
	'semanticmaps_enteraddresshere' => 'Ansëriss adrëssa sì',
	'semanticmaps_notfound' => 'pa trovà',
	'semanticmaps_paramdesc_format' => 'Ël servissi ëd cartografìa dovrà për generé la carta',
	'semanticmaps_paramdesc_geoservice' => "Ël servissi ëd geocodìfica dovrà për trasformé j'adrësse an coordinà",
	'semanticmaps_paramdesc_height' => "L'autëssa dla carta, an pontin (lë stàndard a l'é $1)",
	'semanticmaps_paramdesc_width' => "La larghëssa dla carta, an pontin (lë stàndard a l'é $1)",
	'semanticmaps_paramdesc_zoom' => "Ël livel d'angrandiment ëd la carta",
	'semanticmaps_paramdesc_centre' => 'Le coordinà dël sènter ëd la carta',
	'semanticmaps_paramdesc_controls' => 'Ij contròj utent piassà an sla carta',
	'semanticmaps_paramdesc_types' => 'Le sòrt ëd carte disponìbij an sla carta',
	'semanticmaps_paramdesc_type' => 'Ël tipo ëd carta stàndard për la carta',
	'semanticmaps_paramdesc_overlays' => 'Le dzor-posission disponìbij an sla carta',
	'semanticmaps_paramdesc_autozoom' => "Se l'angrandiment anans e andré an dovrand la roëtta dël rat a l'é abilità",
	'semanticmaps_paramdesc_layers' => 'Ij livej disponìbij an sla carta',
);

/** Pashto (پښتو)
 * @author Ahmed-Najib-Biabani-Ibrahimkhel
 */
$messages['ps'] = array(
	'semanticmaps_notfound' => 'و نه موندل شو',
);

/** Portuguese (Português)
 * @author Hamilton Abreu
 * @author Indech
 * @author Malafaya
 */
$messages['pt'] = array(
	'semanticmaps_desc' => 'Permite ver e editar dados de coordenadas, armazenados através da extensão MediaWiki Semântico ([http://wiki.bn2vs.com/wiki/Semantic_Maps demonstração]).
Serviços de cartografia disponíveis: $1',
	'semanticmaps-unrecognizeddistance' => 'O valor $1 não é uma distância válida.',
	'semanticmaps_lookupcoordinates' => 'Pesquisar coordenadas',
	'semanticmaps_enteraddresshere' => 'Introduza um endereço aqui',
	'semanticmaps_notfound' => 'não encontrado',
	'semanticmaps_paramdesc_format' => 'O serviço de cartografia usado para gerar o mapa',
	'semanticmaps_paramdesc_geoservice' => 'O serviço de geocódigos usado para transformar endereços em coordenadas',
	'semanticmaps_paramdesc_height' => 'A altura do mapa, em pixels (por omissão, $1)',
	'semanticmaps_paramdesc_width' => 'A largura do mapa, em pixels (por omissão, $1)',
	'semanticmaps_paramdesc_zoom' => 'O nível de aproximação do mapa',
	'semanticmaps_paramdesc_centre' => 'As coordenadas do centro do mapa',
	'semanticmaps_paramdesc_controls' => 'Os controles colocados no mapa',
	'semanticmaps_paramdesc_types' => 'Os tipos de mapa disponíveis no mapa',
	'semanticmaps_paramdesc_type' => 'O tipo do mapa, por omissão',
	'semanticmaps_paramdesc_overlays' => 'As sobreposições disponíveis no mapa',
	'semanticmaps_paramdesc_autozoom' => 'Possibilitar a aproximação e afastamento usando a roda de deslizamento do rato',
	'semanticmaps_paramdesc_layers' => 'As camadas disponíveis no mapa',
);

/** Brazilian Portuguese (Português do Brasil)
 * @author Eduardo.mps
 * @author Luckas Blade
 */
$messages['pt-br'] = array(
	'semanticmaps_desc' => 'Provê a possibilidade de ver e editar dados de coordenadas armazenados através da extensão Semantic MediaWiki. ([http://wiki.bn2vs.com/wiki/Semantic_Maps demonstração]).
Serviços de mapeamento disponíveis: $1',
	'semanticmaps_lookupcoordinates' => 'Pesquisar coordenadas',
	'semanticmaps_enteraddresshere' => 'Introduza um endereço aqui',
	'semanticmaps_notfound' => 'Não encontrado',
);

/** Romanian (Română)
 * @author Firilacroco
 */
$messages['ro'] = array(
	'semanticmaps_enteraddresshere' => 'Introduceți adresa aici',
	'semanticmaps_notfound' => 'nu a fost găsit',
);

/** Tarandíne (Tarandíne)
 * @author Joetaras
 */
$messages['roa-tara'] = array(
	'semanticmaps_desc' => "Dè l'abbilità a fà vedè e cangià le coordinate reggistrate cu l'estenzione Semandiche de MediaUicchi ([http://wiki.bn2vs.com/wiki/Semantic_Maps demo]).
Disponibbile le servizie de mappe: $1",
	'semanticmaps_lookupcoordinates' => 'Ingroce le coordinate',
	'semanticmaps_enteraddresshere' => "Scaffe l'indirizze aqquà",
	'semanticmaps_notfound' => 'no acchiate',
);

/** Russian (Русский)
 * @author Eugene Mednikov
 * @author Lockal
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'semanticmaps_desc' => 'Предоставляет возможность просмотра и редактирования данных о координатах, хранящихся посредством расширения Semantic MediaWiki ([http://wiki.bn2vs.com/wiki/Semantic_Maps демонстрация]).
Доступные службы карт: $1',
	'semanticmaps-unrecognizeddistance' => 'Значение $1 не является допустимым расстоянием.',
	'semanticmaps_lookupcoordinates' => 'Найти координаты',
	'semanticmaps_enteraddresshere' => 'Введите адрес',
	'semanticmaps_notfound' => 'не найдено',
	'semanticmaps_paramdesc_format' => 'Картографическая служба, используемая для создания карт',
	'semanticmaps_paramdesc_geoservice' => 'Служба геокодирования используется для преобразования адреса в координаты',
	'semanticmaps_paramdesc_height' => 'Высота карты в пикселях (по умолчанию $1)',
	'semanticmaps_paramdesc_width' => 'Ширина карты в пикселях (по умолчанию $1)',
	'semanticmaps_paramdesc_zoom' => 'Масштаб карты',
	'semanticmaps_paramdesc_centre' => 'Координаты центра карты',
	'semanticmaps_paramdesc_controls' => 'Элементы управления на карте',
	'semanticmaps_paramdesc_types' => 'Типы карты, доступные на карте',
	'semanticmaps_paramdesc_type' => 'Тип карты по умолчанию',
	'semanticmaps_paramdesc_overlays' => 'Доступные наложения',
	'semanticmaps_paramdesc_autozoom' => 'Если включено увеличение и уменьшение масштаб с помощью колеса прокрутки мыши',
	'semanticmaps_paramdesc_layers' => 'Доступные на карте слои',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'semanticmaps_desc' => 'Poskytuje schopnosť zobrazovať a upravovať údaje súradníc uložené prostredníctvom rozšírenia Semantic MediaWiki ([http://wiki.bn2vs.com/wiki/Semantic_Maps demo]).
Dostupné mapové služby: $1',
	'semanticmaps_lookupcoordinates' => 'Vyhľadať súradnice',
	'semanticmaps_enteraddresshere' => 'Sem zadajte emailovú adresu',
	'semanticmaps_notfound' => 'nenájdené',
	'semanticmaps_paramdesc_format' => 'Služba použitá na tvorbu mapy',
	'semanticmaps_paramdesc_geoservice' => 'Služba použitá na vyhľadanie súradníc na základe adresy',
	'semanticmaps_paramdesc_height' => 'Výška mapy v pixloch (predvolené je $1)',
	'semanticmaps_paramdesc_width' => 'Šírka mapy v pixloch (predvolené je $1)',
	'semanticmaps_paramdesc_zoom' => 'Úroveň priblíženia mapy',
	'semanticmaps_paramdesc_centre' => 'Súradnice stredu mapy',
	'semanticmaps_paramdesc_controls' => 'Používateľské ovládacie prvky umiestnené na mape',
	'semanticmaps_paramdesc_types' => 'Typy máp dostupné na mape',
	'semanticmaps_paramdesc_type' => 'Predvolený typ mapy na mape',
	'semanticmaps_paramdesc_overlays' => 'Vrstvy dostupné na mape',
	'semanticmaps_paramdesc_autozoom' => 'Či je povolené približovanie a odďaľovanie mapy kolieskom myši',
	'semanticmaps_paramdesc_layers' => 'Dostupné vrstvy mapy',
);

/** Serbian Cyrillic ekavian (Српски (ћирилица))
 * @author Михајло Анђелковић
 */
$messages['sr-ec'] = array(
	'semanticmaps-unrecognizeddistance' => 'Вредност $1 није исправно растојање.',
	'semanticmaps_enteraddresshere' => 'Унеси адресу овде',
	'semanticmaps_notfound' => 'није нађено',
	'semanticmaps_paramdesc_height' => 'Висина мапе у пикселима (подразумевано је $1)',
	'semanticmaps_paramdesc_width' => 'Ширина мапе у пикселима (подразумевано је $1)',
	'semanticmaps_paramdesc_zoom' => 'Ниво увећања мапе',
	'semanticmaps_paramdesc_centre' => 'Координате центра мапе',
);

/** Serbian Latin ekavian (Srpski (latinica))
 * @author Michaello
 */
$messages['sr-el'] = array(
	'semanticmaps_enteraddresshere' => 'Unesi adresu ovde',
	'semanticmaps_notfound' => 'nije nađeno',
);

/** Swedish (Svenska)
 * @author Boivie
 * @author Najami
 * @author Per
 */
$messages['sv'] = array(
	'semanticmaps_desc' => 'Ger möjligheten att titta på och ändra koordinatdata sparad genom Semantic MediaWiki-utvidgningen ([http://wiki.bn2vs.com/wiki/Semantic_Maps demo]).

Tillgängliga karttjänster: $1',
	'semanticmaps_lookupcoordinates' => 'Kolla upp koordinater',
	'semanticmaps_enteraddresshere' => 'Skriv in adress här',
	'semanticmaps_notfound' => 'hittades inte',
	'semanticmaps_paramdesc_height' => 'Höjden på kartan i pixlar (standard är $1)',
	'semanticmaps_paramdesc_width' => 'Bredden på kartan i pixlar (standard är $1)',
	'semanticmaps_paramdesc_zoom' => 'Zoomnivån för kartan',
	'semanticmaps_paramdesc_centre' => 'Koordinaterna för kartans mittpunkt',
	'semanticmaps_paramdesc_type' => 'Standard karttyp för kartan',
	'semanticmaps_paramdesc_layers' => 'Lagren tillgängliga för kartan',
);

/** Telugu (తెలుగు)
 * @author Veeven
 */
$messages['te'] = array(
	'semanticmaps_notfound' => 'కనబడలేదు',
);

/** Tagalog (Tagalog)
 * @author AnakngAraw
 */
$messages['tl'] = array(
	'semanticmaps_desc' => 'Nagbibigay ng kakayahang makita at baguhin ang dato ng tugmaang nakatabi sa pamamagitan ng dugtong na Semantikong MediaWiki ([http://wiki.bn2vs.com/wiki/Semantic_Maps pagpapakita]).
Makukuhang mga palingkurang pangmapa: $1',
	'semanticmaps-unrecognizeddistance' => 'Hindi isang tanggap na layo ang halagang $1.',
	'semanticmaps_lookupcoordinates' => "Hanapin ang mga tugmaang-pampook (''coordinate'')",
	'semanticmaps_enteraddresshere' => 'Ipasok ang adres dito',
	'semanticmaps_notfound' => 'hindi natagpuan',
	'semanticmaps_paramdesc_format' => 'Ang palingkurang pangpagmamapa na ginamit sa paglikha ng mapa',
	'semanticmaps_paramdesc_geoservice' => 'Ang paglingkurang pang-geokodigo na ginagamit upang maging mga tugmaang-pampook ang mga direksyon',
	'semanticmaps_paramdesc_height' => 'Ang taas ng mapa, sa piksels ($1 ang likas na nakatakda)',
	'semanticmaps_paramdesc_width' => 'Ang lapad ng mapa, sa piksels ($1 ang likas na nakatakda)',
	'semanticmaps_paramdesc_zoom' => 'Ang antas ng paglapit-tutok ng mapa',
	'semanticmaps_paramdesc_centre' => 'Ang mga tugmaang-pampook ng gitna ng mga mapa',
	'semanticmaps_paramdesc_controls' => 'Ang mga pangtaban ng tagagamit na inilagay sa ibabaw ng mapa',
	'semanticmaps_paramdesc_types' => 'Ang mga uri ng mapang makukuha na nasa ibabaw ng mapa',
	'semanticmaps_paramdesc_type' => 'Ang likas na nakatakdang uri ng mapa na para sa mapa',
	'semanticmaps_paramdesc_overlays' => 'Ang makukuhang mga patong na nasa ibabaw ng mapa',
	'semanticmaps_paramdesc_autozoom' => 'Kapag pinagana ang pagtutok-lapit at paglayo sa pamamagitan ng pang-ikid ng maws',
	'semanticmaps_paramdesc_layers' => 'Ang makukuhang mga patong na nasa ibabaw ng mapa',
);

/** Turkish (Türkçe)
 * @author Vito Genovese
 */
$messages['tr'] = array(
	'semanticmaps_lookupcoordinates' => 'Koordinat ara',
	'semanticmaps_enteraddresshere' => 'Adresi buraya girin',
	'semanticmaps_notfound' => 'bulunamadı',
	'semanticmaps_paramdesc_zoom' => 'Haritanın yakınlaşma seviyesi',
	'semanticmaps_paramdesc_layers' => 'Haritada mevcut olan katmanlar',
);

/** Veps (Vepsan kel')
 * @author Игорь Бродский
 */
$messages['vep'] = array(
	'semanticmaps_notfound' => 'ei voi löuta',
);

/** Vietnamese (Tiếng Việt)
 * @author Minh Nguyen
 * @author Vinhtantran
 */
$messages['vi'] = array(
	'semanticmaps_desc' => 'Cung cấp khả năng xem và sửa đổi dữ liệu tọa độ được lưu bởi phần mở rộng Semantic MediaWiki ([http://wiki.bn2vs.com/wiki/Semantic_Maps thử xem]).
Các dịch vụ bản đồ có sẵn: $1',
	'semanticmaps_lookupcoordinates' => 'Tra tọa độ',
	'semanticmaps_enteraddresshere' => 'Nhập địa chỉ vào đây',
	'semanticmaps_notfound' => 'không tìm thấy',
);

/** Volapük (Volapük)
 * @author Smeira
 */
$messages['vo'] = array(
	'semanticmaps_lookupcoordinates' => 'Tuvön koordinatis',
);

/** Simplified Chinese (‪中文(简体)‬)
 * @author Gzdavidwong
 */
$messages['zh-hans'] = array(
	'semanticmaps_lookupcoordinates' => '查找坐标',
);

/** Traditional Chinese (‪中文(繁體)‬)
 * @author Gzdavidwong
 * @author Sheepy
 * @author Wrightbus
 */
$messages['zh-hant'] = array(
	'semanticmaps_lookupcoordinates' => '尋找座標',
);

