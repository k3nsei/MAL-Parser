MyAnimeList Parsers
==========
Example Code of Manga Parser:
```php
<?php

use MLNG\MAL\Manga\Parser as MangaParser;

// Turn off all error reporting
error_reporting(0);
// Report all PHP errors
//error_reporting(E_ALL);

date_default_timezone_set('Europe/Warsaw');

require_once './mangaParser.php';

$id = 1;

$response = shell_exec("casperjs mal.casper.js \"http://myanimelist.net/manga/" . $id . "\" 2>&1 &");
$response = trim($response);

try {
 $parser = new MangaParser($response);
 $data = $parser->getAll();
 
 var_dump($data);
} catch (Exception $e) {
		echo 'no_found ' . $id;
}
 ```
 
 Example Code of People Parser:
```php
<?php

use MLNG\MAL\People\Parser as PeopleParser;

// Turn off all error reporting
error_reporting(0);
// Report all PHP errors
//error_reporting(E_ALL);

date_default_timezone_set('Europe/Warsaw');

require_once './peopleParser.php';

$id = 1;

$response = shell_exec("casperjs mal.casper.js \"http://myanimelist.net/people/" . $id . "\" 2>&1 &");
$response = trim($response);

try {
 $parser = new PeopleParser($response);
 $data = $parser->getAll();
 
 var_dump($data);
} catch (Exception $e) {
		echo 'no_found ' . $id;
}
 ```
