<?php

declare( strict_types = 1 );

namespace Maps\Tests\Util;

use MediaWiki\Parser\ParserOutput;
use MediaWiki\Parser\ParserOutputLinkTypes;
use MediaWiki\Title\Title;

class RecordedLinks {

	/**
	 * @return string[]
	 */
	public static function templatesOf( ParserOutput $output ): array {
		return self::pageNames( $output->getLinkList( ParserOutputLinkTypes::TEMPLATE ) );
	}

	/**
	 * @return string[]
	 */
	public static function linksOf( ParserOutput $output ): array {
		return self::pageNames( $output->getLinkList( ParserOutputLinkTypes::LOCAL ) );
	}

	/**
	 * @return string[]
	 */
	private static function pageNames( array $links ): array {
		return array_map(
			static fn ( array $link ): string => Title::newFromLinkTarget( $link['link'] )->getPrefixedText(),
			$links
		);
	}

}
