<?php

/*
 * Bronze - Make your Proof of Concept with Swag
 */

namespace Trismegiste\Bronze;

/**
 * Description of Human
 *
 * @author flo
 */
class Human implements \Trismegiste\Strangelove\MongoDb\Root
{

    use \Trismegiste\Strangelove\MongoDb\RootImpl;

    public string $firstname;

}
