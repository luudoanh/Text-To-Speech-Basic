# Text To Speech Basic
 - This is a basic class that allows you to convert text content into audio (mp3) files, via a google translate. (EN).
 - Đây là một lớp đơn giản giúp bạn có thể chuyển đổi nội dung của file văn bản (text) thành file âm thanh (mp3), thông qua goiogle translate (VI).

## Installation
### With Composer
```
$ composer require cookie/text-to-speech
```
```php
<?php

require 'TextToSpeechBasic.php';

use App\TextToSpeechBasic;
$tts = new TextToSpeechBasic();
$tts->_config = array(
	'tl' => 'en', // Example: en => English, vi => Vietnamese
	'ie' => 'UTF-8', // The language character set you want to convert.
	'client' => 'tw-ob' // Constant
);
// $file_path path to text file.
// $folder_save folder save audio file
echo $tts->TTS($file_path, $folder_save);
// return  Successful! is completed
```

### Without Composer

If you don't use composer. Download the Text To Speech latest release and put the contents of the ZIP archive into a directory in your project. Then do the code above.
## Support Language

I'm not sure, but it works well with English and Vietnamese