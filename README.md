MyAnimeList Parsers
==========
Example Code of Manga Parser:
```php
<?php

require_once 'mangaParser.php';

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
