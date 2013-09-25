<?php

/**
 * Class for handling geographical SMW queries.
 *
 * @since 0.7.3
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
use Maps\Elements\Location;

class SMQueryHandler {

	protected $queryResult;
	protected $outputmode;

	/**
	 * @since 2.0
	 *
	 * @var array
	 */
	protected $geoShapes = array(
		'lines' => array(),
		'locations' => array(),
		'polygons' => array()
	);

	/**
	 * The template to use for the text, or false if there is none.
	 *
	 * @since 0.7.3
	 *
	 * @var string|boolean false
	 */
	protected $template = false;

	/**
	 * The global icon.
	 *
	 * @since 0.7.3
	 *
	 * @var string
	 */
	public $icon = '';

	/**
	 * The global text.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	public $text = '';

	/**
	 * The global title.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * Make a separate link to the title or not?
	 *
	 * @since 0.7.3
	 *
	 * @var boolean
	 */
	public $titleLinkSeparate;

	/**
	 * Should link targets be made absolute (instead of relative)?
	 *
	 * @since 1.0
	 *
	 * @var boolean
	 */
	protected $linkAbsolute;

	/**
	 * The text used for the link to the page (if it's created). $1 will be replaced by the page name.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	protected $pageLinkText;

	/**
	 * A separator to use between the subject and properties in the text field.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	protected $subjectSeparator = '<hr />';

	/**
	 * Make the subject in the text bold or not?
	 *
	 * @since 1.0
	 *
	 * @var boolean
	 */
	protected $boldSubject = true;

	/**
	 * Show the subject in the text or not?
	 *
	 * @since 1.0
	 *
	 * @var boolean
	 */
	protected $showSubject = true;

	/**
	 * Hide the namespace or not.
	 *
	 * @var boolean
	 */
	protected $hideNamespace = false;


	/**
	 * Defines which article names in the result are hyperlinked, all normally is the default
	 * none, subject, all
	 */
	protected $linkStyle = 'all';

	/*
	 * Show headers (with links), show headers (just text) or hide them. show is default
	 * show, plain, hide
	 */
	protected $headerStyle = 'show';

	/**
	 * Marker icon to show when marker equals active page
	 *
	 * @var string
	 */
	protected $activeIcon;

	/**
	 * Constructor.
	 *
	 * @since 0.7.3
	 *
	 * @param SMWQueryResult $queryResult
	 * @param integer $outputmode
	 * @param boolean $linkAbsolute
	 * @param string $pageLinkText
	 * @param boolean $titleLinkSeparate
	 * @param string $activeIcon
	 */
	public function __construct( SMWQueryResult $queryResult, $outputmode, $linkAbsolute = false, $pageLinkText = '$1', $titleLinkSeparate = false, $hideNamespace = false, $activeIcon = null ) {
		$this->queryResult = $queryResult;
		$this->outputmode = $outputmode;

		$this->linkAbsolute = $linkAbsolute;
		$this->pageLinkText = $pageLinkText;
		$this->titleLinkSeparate = $titleLinkSeparate;
		$this->hideNamespace = $hideNamespace;
		$this->activeIcon = $activeIcon;
	}

	/**
	 * Sets the template.
	 *
	 * @since 1.0
	 *
	 * @param string $template
	 */
	public function setTemplate( $template ) {
		$this->template = $template === '' ? false : $template;
	}

	/**
	 * Sets the global icon.
	 *
	 * @since 1.0
	 *
	 * @param string $icon
	 */
	public function setIcon( $icon ) {
		$this->icon = $icon;
	}

	/**
	 * Sets the global title.
	 *
	 * @since 1.0
	 *
	 * @param string $title
	 */
	public function setTitle( $title ) {
		$this->title = $title;
	}

	/**
	 * Sets the global text.
	 *
	 * @since 1.0
	 *
	 * @param string $text
	 */
	public function setText( $text ) {
		$this->text = $text;
	}

	/**
	 * Sets the subject separator.
	 *
	 * @since 1.0
	 *
	 * @param string $subjectSeparator
	 */
	public function setSubjectSeparator( $subjectSeparator ) {
		$this->subjectSeparator = $subjectSeparator;
	}

	/**
	 * Sets if the subject should be made bold in the text.
	 *
	 * @since 1.0
	 *
	 * @param string $boldSubject
	 */
	public function setBoldSubject( $boldSubject ) {
		$this->boldSubject = $boldSubject;
	}

	/**
	 * Sets if the subject should shown in the text.
	 *
	 * @since 1.0
	 *
	 * @param string $showSubject
	 */
	public function setShowSubject( $showSubject ) {
		$this->showSubject = $showSubject;
	}

	/**
	 * Sets the text for the link to the page when separate from the title.
	 *
	 * @since 1.0
	 *
	 * @param string $text
	 */
	public function setPageLinkText( $text ) {
		$this->pageLinkText = $text;
	}

	/**
	 *
	 * @since 2.0.1
	 *
	 * @param boolean $link
	 */
	public function setLinkStyle ( $link ) {
		$this->linkStyle = $link;
	}

	/**
	 *
	 * @since 2.0.1
	 *
	 * @param boolean $headers
	 */
	public function setHeaderStyle ( $headers ) {
		$this->headerStyle = $headers;
	}

	/**
	 * @since 2.0
	 *
	 * @return array
	 */
	public function getShapes() {
		$this->findShapes();
		return $this->geoShapes;
	}

	/**
	 * @since 2.0
	 */
	protected function findShapes() {
		while ( ( $row = $this->queryResult->getNext() ) !== false ) {
			$this->handleResultRow( $row );
		}
	}

	/**
	 * Returns the locations found in the provided result row.
	 *
	 * @since 0.7.3
	 *
	 * @param SMWResultArray[] $row
	 */
	protected function handleResultRow( array $row ) {
		$locations = array();
		$properties = array();

		$title = '';
		$text = '';

		// Loop through all fields of the record.
		foreach ( $row as $i => $resultArray ) {
			/* SMWPrintRequest */ $printRequest = $resultArray->getPrintRequest();

			// Loop through all the parts of the field value.
			while ( ( /* SMWDataValue */ $dataValue = $resultArray->getNextDataValue() ) !== false ) {
				if ( $dataValue->getTypeID() == '_wpg' && $i == 0 ) {
					list( $title, $text ) = $this->handleResultSubject( $dataValue );
				}
				else if ( $dataValue->getTypeID() == '_str' && $i == 0 ) {
					$title = $dataValue->getLongText( $this->outputmode, null );
					$text = $dataValue->getLongText( $this->outputmode, smwfGetLinker() );
				}
				else if ( $dataValue->getTypeID() == '_gpo' ) {
					$dataItem = $dataValue->getDataItem();
					$polyHandler = new PolygonHandler ( $dataItem->getString() );
					$this->geoShapes[ $polyHandler->getGeoType() ][] = $polyHandler->shapeFromText();
				}
				else if ( $dataValue->getTypeID() != '_geo' && $i != 0 && !$this->isHeadersHide()) {
					$properties[] = $this->handleResultProperty( $dataValue, $printRequest );
				}
				else if ( $printRequest->getMode() == SMWPrintRequest::PRINT_PROP && $printRequest->getTypeID() == '_geo' ) {
					$dataItem = $dataValue->getDataItem();

					$location = Location::newFromLatLon( $dataItem->getLatitude(), $dataItem->getLongitude() );

					$locations[] = $location;
				}
			}
		}

		if ( count( $properties ) > 0 && $text !== '' ) {
			$text .= $this->subjectSeparator;
		}

		$icon = $this->getLocationIcon( $row );

		$this->geoShapes['locations'] = array_merge(
			$this->geoShapes['locations'],
			$this->buildLocationsList(
				$locations,
				$text,
				$icon,
				$properties,
				Title::newFromText( $title )
			)
		);
	}

	/**
	 * Handles a SMWWikiPageValue subject value.
	 * Gets the plain text title and creates the HTML text with headers and the like.
	 *
	 * @since 1.0
	 *
	 * @param SMWWikiPageValue $object
	 *
	 * @return array with title and text
	 */
	protected function handleResultSubject( SMWWikiPageValue $object ) {
		$title = $object->getLongText( $this->outputmode, null );
		$text = '';

		if ( $this->showSubject ) {
			if( !$this->showArticleLink()){
				$text = $this->hideNamespace ? $object->getText() : $object->getTitle()->getFullText();
			}else if ( !$this->titleLinkSeparate && $this->linkAbsolute ) {
				$text = Html::element(
					'a',
					array( 'href' => $object->getTitle()->getFullUrl() ),
					$this->hideNamespace ? $object->getText() : $object->getTitle()->getFullText()
				);
			}
			else {
				if($this->hideNamespace){
					$text = $object->getShortHTMLText(smwfGetLinker());
				}else{
					$text = $object->getLongHTMLText( smwfGetLinker() );
				}
			}

			if ( $this->boldSubject ) {
				$text = '<b>' . $text . '</b>';
			}

			if ( $this->titleLinkSeparate ) {
				$txt = $object->getTitle()->getText();

				if ( $this->pageLinkText !== '' ) {
					$txt = str_replace( '$1', $txt, $this->pageLinkText );
				}
				$text .= Html::element(
					'a',
					array( 'href' => $object->getTitle()->getFullUrl() ),
					$txt
				);
			}
		}

		return array( $title, $text );
	}

	protected function showArticleLink(){
		return $this->linkStyle !== 'none';
	}

	/**
	 * Handles a single property (SMWPrintRequest) to be displayed for a record (SMWDataValue).
	 *
	 * @since 1.0
	 *
	 * @param SMWDataValue $object
	 * @param SMWPrintRequest $printRequest
	 *
	 * @return string
	 */
	protected function handleResultProperty( SMWDataValue $object, SMWPrintRequest $printRequest ) {
		if($this->isHeadersHide()){
			return '';
		}

		if ( $this->template ) {
			if ( $object instanceof SMWWikiPageValue ) {
				return $object->getTitle()->getPrefixedText();
			} else {
				return $object->getLongText( SMW_OUTPUT_WIKI, null );
			}
		}

		if ( $this->linkAbsolute ) {
			$titleText = $printRequest->getText( null );
			$t = Title::newFromText($titleText , SMW_NS_PROPERTY );

			if ($this->isHeadersShow() && $t instanceof Title && $t->exists() ) {
				$propertyName = $propertyName = Html::element(
					'a',
					array( 'href' => $t->getFullUrl() ),
					$printRequest->getHTMLText( null )
				);
			}
			else {
				$propertyName = $titleText;
			}
		}
		else {
			if($this->isHeadersShow()){
				$propertyName = $printRequest->getHTMLText( smwfGetLinker() );
			}else if($this->isHeadersPlain()){
				$propertyName = $printRequest->getText(null);
			}
		}

		if ( $this->linkAbsolute ) {
			$hasPage = $object->getTypeID() == '_wpg';

			if ( $hasPage ) {
				$t = Title::newFromText( $object->getLongText( $this->outputmode, null ), NS_MAIN );
				$hasPage = $t !== null && $t->exists();
			}

			if ( $hasPage ) {
				$propertyValue = Html::element(
					'a',
					array( 'href' => $t->getFullUrl() ),
					$object->getLongText( $this->outputmode, null )
				);
			}
			else {
				$propertyValue = $object->getLongText( $this->outputmode, null );
			}
		}
		else {
			$propertyValue = $object->getLongText( $this->outputmode, smwfGetLinker() );
		}

		return $propertyName . ( $propertyName === '' ? '' : ': ' ) . $propertyValue;
	}


	protected function isHeadersShow(){
		return $this->headerStyle === 'show';
	}

	protected function isHeadersHide(){
		return $this->headerStyle === 'hide';
	}

	protected function isHeadersPlain(){
		return $this->headerStyle === 'plain';
	}

	/**
	 * Builds a set of locations with the provided title, text and icon.
	 *
	 * @since 1.0
	 *
	 * @param Location[] $locations
	 * @param string $text
	 * @param string $icon
	 * @param array $properties
	 * @param Title|null $title
	 *
	 * @return Location[]
	 */
	protected function buildLocationsList( array $locations, $text, $icon, array $properties, Title $title = null ) {
		if ( $this->template ) {
			global $wgParser;
			$parser = clone $wgParser;
		}
		else {
			$text .= implode( '<br />', $properties );
		}

		if ( $title === null ) {
			$titleOutput = '';
		}
		else {
			$titleOutput = $this->hideNamespace ? $title->getText() : $title->getFullText();
		}

		foreach ( $locations as &$location ) {
			if ( $this->template ) {
				$segments = array_merge(
					array(
						$this->template,
						'title=' . $titleOutput,
						'latitude=' . $location->getCoordinates()->getLatitude(),
						'longitude=' . $location->getCoordinates()->getLongitude(),
					),
					$properties
				);

				$text .= $parser->parse( '{{' . implode( '|', $segments ) . '}}', $parser->getTitle(), new ParserOptions() )->getText();
			}

			$location->setTitle( $titleOutput );
			$location->setText( $text );
			$location->setIcon( $icon );
		}

		return $locations;
	}

	/**
	 * Get the icon for a row.
	 *
	 * @since 0.7.3
	 *
	 * @param array $row
	 *
	 * @return string
	 */
	protected function getLocationIcon( array $row ) {
		global $wgTitle;
		$icon = '';
		$legend_labels = array();

		//Check for activeicon parameter

		if( isset( $this->activeIcon )&& $wgTitle->equals( $row[0]->getResultSubject()->getTitle() ) ){
			$icon = MapsMapper::getFileUrl( $this->activeIcon );
		}

		// Look for display_options field, which can be set by Semantic Compound Queries
		// the location of this field changed in SMW 1.5
		$display_location = method_exists( $row[0], 'getResultSubject' ) ? $row[0]->getResultSubject() : $row[0];

		if ( property_exists( $display_location, 'display_options' ) && is_array( $display_location->display_options ) ) {
			$display_options = $display_location->display_options;
			if ( array_key_exists( 'icon', $display_options ) ) {
				$icon = $display_options['icon'];

				// This is somewhat of a hack - if a legend label has been set, we're getting it for every point, instead of just once per icon
				if ( array_key_exists( 'legend label', $display_options ) ) {

					$legend_label = $display_options['legend label'];

					if ( ! array_key_exists( $icon, $legend_labels ) ) {
						$legend_labels[$icon] = $legend_label;
					}
				}
			}
		} // Icon can be set even for regular, non-compound queries If it is, though, we have to translate the name into a URL here
		elseif ( $this->icon !== '' ) {
			$icon = MapsMapper::getFileUrl( $this->icon );
		}

		return $icon;
	}

	/**
	 * @param boolean $hideNamespace
	 */
	public function setHideNamespace( $hideNamespace ) {
		$this->hideNamespace = $hideNamespace;
	}

	/**
	 * @return boolean
	 */
	public function getHideNamespace() {
		return $this->hideNamespace;
	}

	/**
	 * @param string $activeIcon
	 */
	public function setActiveIcon( $activeIcon ){
		$this->activeIcon = $activeIcon;
	}

	/**
	 * @return string
	 */
	public function getActiveIcon( ){
		return $this->activeIcon;
	}

}
