<?php

require dirname(__DIR__) . '/vendor/autoload.php';

$file = dirname(__DIR__) . '/src/Resources/pictures/sample.png';

$png = new Trismegiste\Bronze\Tools\PngReader(new \SplFileInfo($file));

var_dump($png->getTextChunk());
