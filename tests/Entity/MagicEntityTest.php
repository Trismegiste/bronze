<?php

/*
 * Bronze - Make your Proof of Concept with Swag
 */

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\ObjectIdInterface;
use PHPUnit\Framework\TestCase;
use Trismegiste\Bronze\Entity\MagicEntity;

class MagicEntityTest extends TestCase
{

    protected MagicEntity $sut;

    protected function setUp(): void
    {
        $this->sut = new MagicEntity();
    }

    public function testEmpty()
    {
        $this->assertNull($this->sut->yoloShouldBeNull);
    }

    public function testSetter()
    {
        $this->sut->attribute = 123;
        $this->assertEquals(123, $this->sut->attribute);
    }

    public function testIsset()
    {
        $this->assertFalse(isset($this->sut->yolo));
        $this->sut->yolo = 123;
        $this->assertTrue(isset($this->sut->yolo));
    }

    public function testSerialize()
    {
        $this->sut->_id = new ObjectId();
        $this->sut->yolo = 123;
        $dump = json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($this->sut)), true);
        $this->assertEquals(123, $dump['yolo']);
        $this->assertEquals(base64_encode(MagicEntity::class), $dump['__pclass']['$binary']);

        return $dump;
    }

    /** @depends testSerialize */
    public function testUnserialize(array $dump)
    {
        $obj = \MongoDB\BSON\toPHP(\MongoDB\BSON\fromJSON(json_encode($dump)));
        $this->assertInstanceOf(MagicEntity::class, $obj);
        $this->assertEquals(123, $obj->yolo);
        $this->assertInstanceOf(ObjectIdInterface::class, $obj->getPk());
    }

    public function testAttrbutes()
    {
        $this->sut->yolo = 123;
        $this->assertContains('yolo', $this->sut->getAttributes());
    }

}
