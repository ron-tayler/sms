<?php

namespace Engine;
/**
 * Response class
 * @package Engine
 * @author Ron_Tayler
 * @copyright 04.05.2021
 */
class Response {
	private static array $headers = array();
	private static int $level = 0;
	/** @var string|array */
	private static $output;

	/**
	 * Method addHeader - Добавление заголовков
	 * @param string $header
 	 */
	public static function addHeader($header) {
		self::$headers[] = $header;
	}
	
	/**
     * Method redirect - Перенаправление
	 * @param string $url
	 * @param int $status
 	 */
	public static function redirect($url, $status = 303) {
        if (!headers_sent())
            header('Location: ' . str_replace(array('&amp;', "\n", "\r"), array('&', '', ''), $url), true, $status);
        else {
            echo '<script type="text/javascript">';
            echo 'window.location.href="'.$url.'";';
            echo '</script>';
            echo '<noscript>';
            echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
            echo '</noscript>';
        }
		exit();
	}
	
	/**
	 * @param int $level
 	 */
	private static function setCompression($level) {
		self::$level = $level;
	}
	
	/**
	 * @return string|array
 	 */
	public static function getOutput() {
		return self::$output;
	}
	
	/**
	 * @param string|array $output
 	 */
	public static function setOutput($output) {
		self::$output = $output;
	}
	
	/**
	 * @param	string	$data
	 * @param	int		$level
	 * @return	string
 	 */
    private static function compress($data, $level = 0) {
		if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false)) {
			$encoding = 'gzip';
		}

		if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false)) {
			$encoding = 'x-gzip';
		}

		if (!isset($encoding) || ($level < -1 || $level > 9)) {
			return $data;
		}

		if (!extension_loaded('zlib') || ini_get('zlib.output_compression')) {
			return $data;
		}

		if (headers_sent()) {
			return $data;
		}

		if (connection_status()) {
			return $data;
		}

		self::addHeader('Content-Encoding: ' . $encoding);

		return gzencode($data, (int)$level);
	}
	
	/**
	 * output
     * Вывод данных пользователю
 	 */
    private static function output() {
		if (self::$output) {
			$output = self::$level ? self::compress(self::$output, self::$level) : self::$output;
			
			if (!headers_sent()) {
				foreach (self::$headers as $header) {
					header($header, true);
				}
			}
			echo $output;
		}
	}
}
