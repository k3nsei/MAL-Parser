MyAnimeList Parsers
==========
Example Code of Manga Parser:
```php
<?php

require_once 'mangaParser.php';

$html = shell_exec("casperjs mal.casper.js \"http://myanimelist.net/manga/11" 2>&1 &");
$pareser = new MLNG\MAL\Manga\Parser($html);
$data = $parser->getAll();

?>
 ```
 
 Example Code of People Parser:
```php
<?php

require_once 'peopleParser.php';
$pareser = new MLNG\MAL\People\Parser($html);

?>
 ```
