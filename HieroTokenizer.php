<?php
/**
 * Copyright (C) 2004 Guillaume Blanchard (Aoineko)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 */

/**
 * Hieroglyphs tokenizer
 */
class HieroTokenizer {
	private static $delimiters = false;
	private static $tokenDelimiters;
	private static $singleChars;

	private $text;
	private $blocks = false;
	private $currentBlock;
	private $token;

	/**
	 * Constructor
	 *
	 * @param $text string:
	 */
	public function __construct( $text ) {
		$this->text = $text;
		self::initStatic();
	}

	private static function initStatic() {
		if ( self::$delimiters ) {
			return;
		}

		self::$delimiters = array_flip( array( ' ', '-', "\t", "\n", "\r" ) );
		self::$tokenDelimiters = array_flip( array( '*', ':', '(', ')' ) );
		self::$singleChars = array_flip( array( '!' ) );
	}

	/**
	 * Split text into blocks, then split blocks into items
	 *
	 * @return array: tokenized text
	 */
	public function tokenize() {
		if ( $this->blocks !== false ) {
			return $this->blocks;
		}

		$this->blocks = array();
		$this->currentBlock = array();
		$this->token = '';

		$text = preg_replace( '/\\<!--.*?--\\>/s', '', $this->text ); // remove HTML comments

		for ( $i = 0; $i < strlen( $text ); $i++ ) {
			$char = $text[$i];

			if ( isset( self::$delimiters[$char] ) ) {
				$this->newBlock();
			} elseif ( isset( self::$singleChars[$char] ) ) {
				$this->singleCharBlock( $char );
			} elseif ( $char == '.' ) {
				$this->dot();
			} elseif ( isset( self::$tokenDelimiters[$char] ) ) {
				$this->newToken( $char );
			} else {
				$this->char( $char );
			}
		}

		$this->newBlock(); // flush stuff being processed

		return $this->blocks;
	}

	/**
	 * Handles a block delimiter
	 */
	private function newBlock() {
		$this->newToken();
		if ( $this->currentBlock ) {
			$this->blocks[] = $this->currentBlock;
			$this->currentBlock = array();
		}
	}

	/**
	 * Flushes current token, optionally adds another one
	 *
	 * @param $token Mixed: token to add or false
	 */
	private function newToken( $token = false ) {
		if ( $this->token !== '' ) {
			$this->currentBlock[] = $this->token;
			$this->token = '';
		}
		if ( $token !== false ) {
			$this->currentBlock[] = $token;
		}
	}

	/**
	 * Adds a block consisting of one character
	 *
	 * @param $char string: block character
	 */
	private function singleCharBlock( $char ) {
		$this->newBlock();
		$this->blocks[] = array( $char );
	}

	/**
	 * Handles void blocks represented by dots
	 */
	private function dot() {
		if ( $this->token == '.' ) {
			$this->token = '..';
			$this->newBlock();
		} else {
			$this->newBlock();
			$this->token = '.';
		}
	}

	/**
	 * Adds a miscellaneous character to current token
	 *
	 * @param $char string: character to add
	 */
	private function char( $char ) {
		if ( $this->token == '.' ) {
			$this->newBlock();
			$this->token = $char;
		} else {
			$this->token .= $char;
		}
	}
}
