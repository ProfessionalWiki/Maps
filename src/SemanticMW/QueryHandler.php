<?php

declare( strict_types = 1 );

namespace Maps\SemanticMW;

use Html;
use Linker;
use Maps\LegacyModel\Location;
use Maps\MapsFunctions;
use MediaWiki\MediaWikiServices;
use SMW\Query\PrintRequest;
use SMW\Query\QueryResult;
use SMW\Query\Result\ResultArray;
use SMWDataValue;
use SMWDIGeoCoord;
use SMWWikiPageValue;
use Title;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class QueryHandler {

	/**
	 * The global icon.
	 */
	public string $icon = '';

	/**
	 * The global text.
	 */
	public string $text = '';

	/**
	 * The global title.
	 */
	public string $title = '';

	private $queryResult;

	private $outputMode;

	/**
	 * The template to use for the text, or false if there is none.
	 * @var string|boolean false
	 */
	private $template = false;

	/**
	 * Should link targets be made absolute (instead of relative)?
	 */
	private bool $linkAbsolute;

	/**
	 * A separator to use between the subject and properties in the text field.
	 */
	private string $subjectSeparator = '<hr />';

	/**
	 * Show the subject in the text or not?
	 */
	private bool $showSubject = true;

	/**
	 * Hide the namespace or not.
	 */
	private bool $hideNamespace = false;

	/**
	 * Defines which article names in the result are hyperlinked, all normally is the default
	 * none, subject, all
	 */
	private string $linkStyle = 'all';

	/*
	 * Show headers (with links), show headers (just text) or hide them. show is default
	 * show, plain, hide
	 */
	private string $headerStyle = 'show';

	/**
	 * Marker icon to show when marker equals active page
	 */
	private ?string $activeIcon = null;

	private string $userParam = '';

	public function __construct( QueryResult $queryResult, int $outputMode, bool $linkAbsolute = false ) {
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
	 * @param ResultArray[] $row
	 * @return Location[]
	 */
	private function handlePageResult( array $row ): array {
		[ $title, $text ] = $this->getTitleAndText( $row[0] );
		[ $locations, $properties ] = $this->getLocationsAndProperties( $row );

		return $this->buildLocationsForPage(
			$locations,
			$text,
			$this->getLocationIcon( $row ),
			$properties,
			$title
		);
	}

	/**
	 * @param ResultArray $resultArray
	 * @return string[] [string $title, string $text]
	 */
	private function getTitleAndText( ResultArray $resultArray ): array {
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
	 * @param ResultArray[] $row
	 * @return array{0: array<int, Location>, 1: string}
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
					$properties[$resultArray->getPrintRequest()->getCanonicalLabel()] = $this->handleResultProperty(
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

		$title = $object->getDataItem()->getTitle();

		if ( !( $title instanceof Title ) ) {
			return '';
		}

		if ( $this->showArticleLink() ) {
			if ( $this->linkAbsolute ) {
				$text = Html::element(
					'a',
					[ 'href' => $title->getFullUrl() ],
					$this->hideNamespace ? $object->getText() : $title->getFullText()
				);
			} else {
				if ( $this->hideNamespace ) {
					$text = $object->getShortHTMLText( smwfGetLinker() );
				} else {
					$text = $object->getLongHTMLText( smwfGetLinker() );
				}
			}
		} else {
			$text = $this->hideNamespace ? $object->getText() : $title->getFullText();
		}

		return '<b>' . $text . '</b>';
	}

	private function showArticleLink() {
		return $this->linkStyle !== 'none';
	}

	/**
	 * Handles a single property (SMWPrintRequest) to be displayed for a record (SMWDataValue).
	 */
	private function handleResultProperty( SMWDataValue $object, PrintRequest $printRequest ): string {
		if ( $this->hasTemplate() ) {
			if ( $object instanceof SMWWikiPageValue ) {
				return $object->getDataItem()->getTitle()->getPrefixedText();
			}

			return $object->getLongText( SMW_OUTPUT_WIKI, null );
		}

		$propertyName = $this->getPropertyName( $printRequest );
		return $propertyName . ( $propertyName === '' ? '' : ': ' ) . $this->getPropertyValue( $object );
	}

	private function getPropertyName( PrintRequest $printRequest ): string {
		if ( $this->headerStyle === 'hide' ) {
			return '';
		}

		if ( $this->linkAbsolute ) {
			$titleText = $printRequest->getText( null );
			$t = Title::newFromText( $titleText, SMW_NS_PROPERTY );

			if ( $t instanceof Title && $t->exists() ) {
				return Html::element(
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
	 * @param ResultArray[] $row
	 */
	private function getLocationIcon( array $row ): string {
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

	private function shouldGetActiveIconUrlFor( ?Title $title ) {
		global $wgTitle;

		return $title !== null
			&& isset( $this->activeIcon )
			&& is_object( $wgTitle )
			&& $wgTitle->equals( $title );
	}

	/**
	 * @param Location[] $locations
	 * @param string $text
	 * @param string $icon
	 * @param array $properties
	 * @param string $title
	 *
	 * @return Location[]
	 */
	private function buildLocationsForPage( array $locations, $text, $icon, array $properties, string $title ): array {
		if ( $properties !== [] && $text !== '' ) {
			$text .= $this->subjectSeparator;
		}

		foreach ( $locations as &$location ) {
			$location->setTitle( $this->getTitleOutput( $title ) );
			$location->setText( $text . $this->buildPopupText( $properties, $title, $location ) );
			$location->setIcon( trim( $icon ) );
		}

		return $locations;
	}

	private function buildPopupText( array $properties, string $title, Location $location ): string {
		if ( $this->hasTemplate() ) {
			return $this->getParser()->recursiveTagParseFully(
				$this->newTemplatedPopup()->getWikiText(
					$title,
					$this->getTitleOutput( $title ),
					$location->getCoordinates(),
					$properties
				)
			);
		}

		return implode( '<br />', $properties );
	}

	private function newTemplatedPopup(): TemplatedPopup {
		return new TemplatedPopup(
			$this->template,
			$this->userParam
		);
	}

	private function getTitleOutput( string $titleText ) {
		$title = Title::newFromText( $titleText );

		if ( $title === null ) {
			return '';
		}

		return $this->hideNamespace ? $title->getText() : $title->getFullText();
	}

	private function getParser(): \Parser {
		return MediaWikiServices::getInstance()->getParser();
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
