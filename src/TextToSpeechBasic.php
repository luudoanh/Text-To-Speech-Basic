<?php

namespace App;

/**
 * This is a class that allows you to convert plain text into audio (mp3) via Google Translate speech (EN)
 * Đây là một lớp đơn giản giúp bạn có thể chuyển đổi văn bản thành file audio (mp3) thông qua Google Translate (VI)
 * các bạn thể sử dụng lại class này cho các dự án khác của mình. Mình mong là nó sẽ hửu ích cho bạn.
 * 
 */
class TextToSpeechBasic {
	/**
	 * @author 		Luu Doanh
	 * @copyright 	Luu Doanh (C) 2019
	 * @link 		https://github.com/luudoanh/Text-To-Speech-Basic
	 */
	
	/**
	 * Declare a constant to divide the text
	 * @var int
	 */
	const Number = 200;
	
	/**
	 * raw http header
	 */
	const Context = array(
		'https' => array(
			'method' => "GET",
			'header' => "accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3\r\n" .
			"accept-language: vi-VN,vi;q=0.9,fr-FR;q=0.8,fr;q=0.7,en-US;q=0.6,en;q=0.5\r\n".
			"user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.90 Safari/537.36\r\n"
		)
	);

	/**
	 * url google translate
	 * @var string
	 */
	const url = 'https://translate.google.com.vn/translate_tts';
	
	/**
	 * The directory save audio files when processing is complete
	 * @var string
	 */
	private static $save_folder = '';

	/**
	 * Array contains listArrayCharacters characters.
	 * @var array
	 */
	private $listArrayCharacters = array('.', ',', '?', '!', ':');

	/**
	 * url with parameters
	 * @var string
	 */
	private $raw_url = '';

	/**
	 * Array contains uri parameters
	 * @var array
	 */
	
	private $_config = array(
		'tl' => 'vi',
		'ie' => 'UTF-8',
		'client' => 'tw-ob'
	);

	/**
	 * Array contains files list audio
	 * @var array
	 */
	private $fileList = [];

	var $text = '';

	/**
	 * Set input parameters
	 * @param  array  $config [description]
	 * @return [type]         [description]
	 */
	public function _setting($config = array()) {
		foreach ($config as $key => $value) {
			if (isset($key)) {
				$this->_config[$key] = $value;
			}
		}
		return $this;
	}

	/**
	 * Set url complete
	 * @param Content text to speech
	 */
	private function set_uri($content) {
		$this->raw_url = self::url.'?ie='.$this->_config['ie'].'&tl='.$this->_config['tl'].'&client='.$this->_config['client'].'&q='.urlencode($content);
		return $this;

	}

	/**
	 * Get list file mp3 in save_folder
	 * @param  Source folder file audio
	 * @param  Type file (dedault .mp3)
	 * @return Array contains file list
	 */
	private function fromData() {
		if (!is_dir(self::$save_folder)) {
			return false;
		}
		$this->fileList = [];
		if ($handle = opendir(self::$save_folder)) {
			while ($entry = readdir($handle)) {
				if (preg_match('/part\_[\d]+\.mp3$/i', $entry)) {
					$this->fileList[] = $entry;
				}
			}
			closedir($handle);
			natsort($this->fileList);
			return $this->fileList;
		} else {
			return false;
		}
	}
	
	/**
	 * Merge audio files
	 * @param string $name_full
	 */
	private function Merge_Audio($name_full = 'Full_') {
		if (empty($this->fromData())) {
			return false;
		}
		$this->text = null;
		foreach ($this->fromData() as $key => $value) {
			$this->text .= file_get_contents(self::$save_folder.'/'.$value);
			unlink(self::$save_folder.'/'.$value);
		}
		$result = file_put_contents(self::$save_folder.'/'.$name_full.time().'.mp3', $this->text, FILE_APPEND);
		if ($result) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Save file audios
	 * @param  integer $part
	 * @return true
	 */
	private function save_file($path,$name='part_', $part=1) {
		foreach ($this->fromFile($path) as $key => $value) {
			$url = $this->set_uri($value)->raw_url;
			$result = copy($url, self::$save_folder.'/'.$name.$part.'.mp3', stream_context_create(self::Context));
			//sleep(1); //Sometimes we should go to sleep!
			$part++;
		}
		return true;
	}

	/**
	 * Get content file text
	 * @param  path file
	 * @return array contains words
	 */
	private function fromFile($path, $newArray = array(), $data = array()) {
		if (empty($this->listArrayCharacters)) return false;
		$str = file_get_contents($path);
		$str = preg_replace('/\n|\r/', '', $str);
		while (true) {
			if (strlen($str) < self::Number) {
				array_push($newArray, $str);
				break;
			}
			$strs = substr($str, 0, self::Number);
			$data = array();
			foreach ($this->listArrayCharacters as $key => $value) {
				$data[] = abs(strrpos($strs, $value));
			}
			$limit = max($data);
			if ($limit == 0) { //If there is no valid listArrayCharacters.
				$data[] = abs(strrpos($strs, ' ')); //then we use spaces to break sentences.
				$limit = max($data);
			}
			$result = substr($strs, 0, $limit);
			array_push($newArray, $result);
			$str = substr($str, ($limit+1), strlen($str));
		}
		return $newArray;
	}

	/**
	 * Function text to speech
	 * @param path file text
	 * @param New folder to save the final file
	 */
	public function TTS($path, $save_folder) {
		if (!is_string($path)) {
			return false;
		}
		if (!is_dir($save_folder)) {
			mkdir($save_folder, '0755');
		}
		self::$save_folder = $save_folder;
		if (!empty($this->fromFile($path))) {
			if ($this->save_file($path)) {
				$result = $this->Merge_Audio();
				if ($result) {
					return 'Successful!';
				} else {
					return false;
				}
			}

		}
	}

	/**
	 * Fucntion reset
	 * @return null
	 */
	private function reset() {
		$this->text = null;
		$this->fileList = [];
	}

	/**
	 * Reset script
	 */
	public function __construct() {
		$this->reset();
	}
}