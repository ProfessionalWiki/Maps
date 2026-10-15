<?php

declare( strict_types = 1 );

namespace Maps\Tests\Util;

use MediaWiki\Context\RequestContext;
use MediaWiki\Linker\LinkTarget;
use MediaWiki\MediaWikiServices;
use MediaWiki\Parser\Parser;
use MediaWiki\Parser\ParserOptions;
use MediaWiki\Title\Title;
use MediaWiki\User\User;
use Wikimedia\ScopedCallback;

/**
 * Stands in for extensions that restrict, in the template callbacks of the parser options, which pages a render
 * may include, and note on the render's output the pages they left out.
 */
class RefusingParserOptions {

	/**
	 * Extension data listing the pages a render left out
	 */
	public const REFUSED_PAGES = 'maps-test-refused-pages';

	public static function refusing( string ...$refusedPageNames ): ParserOptions {
		$refuses = static function ( LinkTarget $page, $parser ) use ( $refusedPageNames ): bool {
			$pageName = Title::newFromLinkTarget( $page )->getPrefixedText();

			if ( !in_array( $pageName, $refusedPageNames, true ) ) {
				return false;
			}

			if ( $parser instanceof Parser ) {
				$parser->getOutput()->appendExtensionData( self::REFUSED_PAGES, $pageName );
			}

			return true;
		};

		$options = ParserOptions::newFromAnon();
		$fetchTemplate = $options->getTemplateCallback();
		$fetchRevision = $options->getCurrentRevisionRecordCallback();

		$options->setTemplateCallback(
			static function ( LinkTarget $page, $parser = false ) use ( $refuses, $fetchTemplate ): array {
				if ( $refuses( $page, $parser ) ) {
					return [ 'text' => false, 'deps' => [] ];
				}

				return $fetchTemplate( $page, $parser );
			}
		);

		$options->setCurrentRevisionRecordCallback(
			static function ( LinkTarget $page, $parser = null ) use ( $refuses, $fetchRevision ) {
				if ( $refuses( $page, $parser ) ) {
					return false;
				}

				return $fetchRevision( $page, $parser );
			}
		);

		return $options;
	}

	/**
	 * Runs the action while the default parser options refuse every page to anonymous readers. The request comes from
	 * a registered user meanwhile, so that only a read as an anonymous reader is refused.
	 */
	public static function whileDefaultsRefuseAnonymousReaders( callable $action ): void {
		$context = RequestContext::getMain();
		$requestUser = $context->getUser();
		$context->setUser( User::newSystemUser( 'Maps test user', [ 'steal' => true ] ) );

		$refusingAnonymousReaders = MediaWikiServices::getInstance()->getHookContainer()->scopedRegister(
			'ParserOptionsRegister',
			static function ( array &$defaults ): void {
				$fetchRevision = $defaults['currentRevisionRecordCallback'];
				$defaults['currentRevisionRecordCallback'] = static fn ( LinkTarget $page, Parser $parser ) =>
					$parser->getOptions()->getUserIdentity()->isRegistered() ? $fetchRevision( $page, $parser ) : false;
			}
		);
		ParserOptions::clearStaticCache();

		try {
			$action();
		} finally {
			ScopedCallback::consume( $refusingAnonymousReaders );
			ParserOptions::clearStaticCache();
			$context->setUser( $requestUser );
		}
	}

}
