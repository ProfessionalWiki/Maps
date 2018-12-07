<?php

namespace Maps\SemanticMW\ResultPrinters;

use Html;
use Linker;
use Maps\Elements\Location;
use Maps\MapsFunctions;
use Maps\SemanticMW\DataValues\CoordinateValue;
use SMWDataValue;
use SMWDIGeoCoord;
use SMWPrintRequest;
use SMWQueryResult;
use SMWResultArray;
use SMWWikiPageValue;
use Title;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class QueryHandler {

	/**
	 * The global icon.
	 * @var string
	 */
	public $icon = '';

	/**
	 * The global text.
	 * @var string
	 */
	public $text = '';

	/**
	 * The global title.
	 * @var string
	 */
	public $title = '';

	/**
	 * Make a separate link to the title or not?
	 * @var boolean
	 */
	public $titleLinkSeparate = false;

	private $queryResult;

	private $outputMode;

	/**
	 * The template to use for the text, or false if there is none.
	 * @var string|boolean false
	 */
	private $template = false;

	/**
	 * Should link targets be made absolute (instead of relative)?
	 * @var boolean
	 */
	private $linkAbsolute;

	/**
	 * The text used for the link to the page (if it's created). $1 will be replaced by the page name.
	 * @var string
	 */
	private $pageLinkText = '$1';

	/**
	 * A separator to use between the subject and properties in the text field.
	 * @var string
	 */
	private $subjectSeparator = '<hr />';

	/**
	 * Show the subject in the text or not?
	 * @var boolean
	 */
	private $showSubject = true;

	/**
	 * Hide the namespace or not.
	 * @var boolean
	 */
	private $hideNamespace = false;

	/**
	 * Defines which article names in the result are hyperlinked, all normally is the default
	 * none, subject, all
	 */
	private $linkStyle = 'all';

	/*
	 * Show headers (with links), show headers (just text) or hide them. show is default
	 * show, plain, hide
	 */
	private $headerStyle = 'show';

	/**
	 * Marker icon to show when marker equals active page
	 * @var string|null
	 */
	private $activeIcon = null;

	/**
	 * @var string
	 */
	private $userParam = '';

	public function __construct( SMWQueryResult $queryResult, int $outputMode, bool $linkAbsolute = false ) {
		$this->queryResult = $queryResult;
		$this->outputMode = $outputMode;
		$this->linkAbsolute = $linkAbsolute;
	}

	public function setTemplate( string $template ) {
		$this->template = $template === '' ? false : $template;
	}

	public function setUserParam( string $userParam ) {
		$this->userParam = $userParam;
	}

	/**
	 * Sets the global icon.
	 */
	public function setIcon( string $icon ) {
		$this->icon = $icon;
	}

	/**
	 * Sets the global title.
	 */
	public function setTitle( string $title ) {
		$this->title = $title;
	}

	/**
	 * Sets the global text.
	 */
	public function setText( string $text ) {
		$this->text = $text;
	}

	public function setSubjectSeparator( string $subjectSeparator ) {
		$this->subjectSeparator = $subjectSeparator;
	}

	public function setShowSubject( bool $showSubject ) {
		$this->showSubject = $showSubject;
	}

	/**
	 * Sets the text for the link to the page when separate from the title.
	 */
	public function setPageLinkText( string $text ) {
		$this->pageLinkText = $text;
	}

	public function setLinkStyle( string $link ) {
		$this->linkStyle = $link;
	}

	public function setHeaderStyle( string $headers ) {
		$this->headerStyle = $headers;
	}

	/**
	 * @return Location[]
	 */
	public function getLocations(): iterable {
		while ( ( $row = $this->queryResult->getNext() ) !== false ) {
			yield from $this->handlePageResult( $row );
		}
	}

	/**
	 * @param SMWResultArray[] $row
	 * @return Location[]
	 */
	private function handlePageResult( array $row ): array {
		[ $title, $text ] = $this->getTitleAndText( $row[0] );
		[ $locations, $properties ] = $this->getLocationsAndProperties( $row );

		if ( $properties !== [] && $text !== '' ) {
			$text .= $this->subjectSeparator;
		}

		$icon = $this->getLocationIcon( $row );

		return $this->buildLocationsList(
			$locations,
			$text,
			$icon,
			$properties,
			Title::newFromText( $title )
		);
	}

	private function getTitleAndText( SMWResultArray $resultArray ): array {
		while ( ( $dataValue = $resultArray->getNextDataValue() ) !== false ) {
			if ( $dataValue instanceof SMWWikiPageValue ) {
				return [
					$dataValue->getLongText( $this->outputMode, null ),
					$this->getResultSubjectText( $dataValue )
				];
			}

			if ( $dataValue->getTypeID() == '_str' ) {
				return [
					$dataValue->getLongText( $this->outputMode, null ),
					$dataValue->getLongText( $this->outputMode, smwfGetLinker() )
				];
			}
		}

		return [ '', '' ];
	}

	/**
	 * @param SMWResultArray[] $row
	 * @return array
	 */
	private function getLocationsAndProperties( array $row ): array {
		$locations = [];
		$properties = [];

		// Loop through all fields of the record.
		foreach ( $row as $i => $resultArray ) {
			if ( $i === 0 ) {
				continue;
			}

			// Loop through all the parts of the field value.
			while ( ( $dataValue = $resultArray->getNextDataValue() ) !== false ) {
				if ( $dataValue instanceof \SMWRecordValue ) {
					foreach ( $dataValue->getDataItems() as $dataItem ) {
						if ( $dataItem instanceof \SMWDIGeoCoord ) {
							$locations[] = $this->locationFromDataItem( $dataItem );
						}
					}
				} elseif ( $dataValue instanceof CoordinateValue ) {
					$locations[] = $this->locationFromDataItem( $dataValue->getDataItem() );
				}
				else {
					$properties[] = $this->handleResultProperty(
						$dataValue,
						$resultArray->getPrintRequest()
					);
				}
			}
		}

		return [ $locations, $properties ];
	}

	private function locationFromDataItem( SMWDIGeoCoord $dataItem ): Location {
		return Location::newFromLatLon(
			$dataItem->getLatitude(),
			$dataItem->getLongitude()
		);
	}

	/**
	 * Handles a SMWWikiPageValue subject value.
	 * Gets the plain text title and creates the HTML text with headers and the like.
	 *
	 * @param SMWWikiPageValue $object
	 *
	 * @return string
	 */
	private function getResultSubjectText( SMWWikiPageValue $object ): string {
		if ( !$this->showSubject ) {
			return '';
		}

		if ( $this->showArticleLink() ) {
			if ( !$this->titleLinkSeparate && $this->linkAbsolute ) {
				$text = Html::element(
					'a',
					[ 'href' => $object->getTitle()->getFullUrl() ],
					$this->hideNamespace ? $object->getText() : $object->getTitle()->getFullText()
				);
			} else {
				if ( $this->hideNamespace ) {
					$text = $object->getShortHTMLText( smwfGetLinker() );
				} else {
					$text = $object->getLongHTMLText( smwfGetLinker() );
				}
			}
		} else {
			$text = $this->hideNamespace ? $object->getText() : $object->getTitle()->getFullText();
		}

		$text = '<b>' . $text . '</b>';

		if ( !$this->titleLinkSeparate ) {
			return $text;
		}

		$txt = $object->getTitle()->getText();

		if ( $this->pageLinkText !== '' ) {
			$txt = str_replace( '$1', $txt, $this->pageLinkText );
		}

		return $text . Html::element(
			'a',
			[ 'href' => $object->getTitle()->getFullUrl() ],
			$txt
		);
	}

	private function showArticleLink() {
		return $this->linkStyle !== 'none';
	}

	/**
	 * Handles a single property (SMWPrintRequest) to be displayed for a record (SMWDataValue).
	 */
	private function handleResultProperty( SMWDataValue $object, SMWPrintRequest $printRequest ): string {
		if ( $this->hasTemplate() ) {
			if ( $object instanceof SMWWikiPageValue ) {
				return $object->getTitle()->getPrefixedText();
			}

			return $object->getLongText( SMW_OUTPUT_WIKI, null );
		}

		$propertyName = $this->getPropertyName( $printRequest );
		return $propertyName . ( $propertyName === '' ? '' : ': ' ) . $this->getPropertyValue( $object );
	}

	private function getPropertyName( SMWPrintRequest $printRequest ): string {
		if ( $this->headerStyle === 'hide' ) {
			return '';
		}

		if ( $this->linkAbsolute ) {
			$titleText = $printRequest->getText( null );
			$t = Title::newFromText( $titleText, SMW_NS_PROPERTY );

			if ( $t instanceof Title && $t->exists() ) {
				return  Html::element(
					'a',
					[ 'href' => $t->getFullUrl() ],
					$printRequest->getHTMLText( null )
				);
			}

			return $titleText;
		}

		return $printRequest->getHTMLText( $this->getPropertyLinker() );
	}

	private function getPropertyLinker(): ?Linker {
		return $this->headerStyle === 'show' && $this->linkStyle !== 'none' ? smwfGetLinker() : null;
	}

	private function getValueLinker(): ?Linker {
		return $this->linkStyle === 'all' ? smwfGetLinker() : null;
	}

	private function getPropertyValue( SMWDataValue $object ): string {
		if ( !$this->linkAbsolute ) {
			return $object->getLongHTMLText(
				$this->getValueLinker()
			);
		}

		if ( $this->hasPage( $object ) ) {
			return Html::element(
				'a',
				[
					'href' => Title::newFromText(
						$object->getLongText( $this->outputMode, null ),
						NS_MAIN
					)->getFullUrl()
				],
				$object->getLongText( $this->outputMode, null )
			);
		}

		return $object->getLongText( $this->outputMode, null );
	}

	private function hasPage( SMWDataValue $object ): bool {
		$hasPage = $object->getTypeID() == '_wpg';

		if ( $hasPage ) {
			$t = Title::newFromText( $object->getLongText( $this->outputMode, null ), NS_MAIN );
			$hasPage = $t !== null && $t->exists();
		}

		return $hasPage;
	}

	private function hasTemplate() {
		return is_string( $this->template );
	}

	/**
	 * Get the icon for a row.
	 *
	 * @param array $row
	 *
	 * @return string
	 */
	private function getLocationIcon( array $row ) {
		$icon = '';
		$legendLabels = [];

		//Check for activeicon parameter

		if ( $this->shouldGetActiveIconUrlFor( $row[0]->getResultSubject()->getTitle() ) ) {
			$icon = MapsFunctions::getFileUrl( $this->activeIcon );
		}

		// Look for display_options field, which can be set by Semantic Compound Queries
		// the location of this field changed in SMW 1.5
		$display_location = method_exists( $row[0], 'getResultSubject' ) ? $row[0]->getResultSubject() : $row[0];

		if ( property_exists( $display_location, 'display_options' ) && is_array(
				$display_location->display_options
			) ) {
			$display_options = $display_location->display_options;
			if ( array_key_exists( 'icon', $display_options ) ) {
				$icon = $display_options['icon'];

				// This is somewhat of a hack - if a legend label has been set, we're getting it for every point, instead of just once per icon
				if ( array_key_exists( 'legend label', $display_options ) ) {

					$legend_label = $display_options['legend label'];

					if ( !array_key_exists( $icon, $legendLabels ) ) {
						$legendLabels[$icon] = $legend_label;
					}
				}
			}
		} // Icon can be set even for regular, non-compound queries If it is, though, we have to translate the name into a URL here
		elseif ( $this->icon !== '' ) {
			$icon = MapsFunctions::getFileUrl( $this->icon );
		}

		return $icon;
	}

	private function shouldGetActiveIconUrlFor( Title $title ) {
		global $wgTitle;

		return isset( $this->activeIcon ) && is_object( $wgTitle )
			&& $wgTitle->equals( $title );
	}

	/**
	 * Builds a set of locations with the provided title, text and icon.
	 *
	 * @param Location[] $locations
	 * @param string $text
	 * @param string $icon
	 * @param array $properties
	 * @param Title|null $title
	 *
	 * @return Location[]
	 */
	private function buildLocationsList( array $locations, $text, $icon, array $properties, Title $title = null ): array {
		if ( !$this->hasTemplate() ) {
			$text .= implode( '<br />', $properties );
		}

		$titleOutput = $this->getTitleOutput( $title );

		foreach ( $locations as &$location ) {
			if ( $this->hasTemplate() ) {
				$segments = array_merge(
					[
						$this->template,
						'title=' . $titleOutput,
						'latitude=' . $location->getCoordinates()->getLatitude(),
						'longitude=' . $location->getCoordinates()->getLongitude(),
						'userparam=' . $this->userParam
					],
					$properties
				);

				$text .= $this->getParser()->recursiveTagParseFully(
					'{{' . implode( '|', $segments ) . '}}'
				);
			}

			$location->setTitle( $titleOutput );
			$location->setText( $text );
			$location->setIcon( trim( $icon ) );
		}

		return $locations;
	}

	private function getTitleOutput( Title $title = null ) {
		if ( $title === null ) {
			return '';
		}

		return $this->hideNamespace ? $title->getText() : $title->getFullText();
	}

	/**
	 * @return \Parser
	 */
	private function getParser() {
		return $GLOBALS['wgParser'];
	}

	/**
	 * @return boolean
	 */
	public function getHideNamespace() {
		return $this->hideNamespace;
	}

	/**
	 * @param boolean $hideNamespace
	 */
	public function setHideNamespace( $hideNamespace ) {
		$this->hideNamespace = $hideNamespace;
	}

	/**
	 * @return string
	 */
	public function getActiveIcon() {
		return $this->activeIcon;
	}

	/**
	 * @param string $activeIcon
	 */
	public function setActiveIcon( $activeIcon ) {
		$this->activeIcon = $activeIcon;
	}

}
