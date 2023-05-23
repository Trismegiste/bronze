<?php

/*
 * WeKissMedia
 */

namespace Trismegiste\Bronze\Entity;

use Trismegiste\Strangelove\MongoDb\Root;
use Trismegiste\Strangelove\MongoDb\RootImpl;

/**
 * Description of WikiPage
 *
 * @author flo
 */
class WikiPage implements Root
{

    use RootImpl;

    public string $title;
    public string $content;

}
