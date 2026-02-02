<?php

namespace Alxndr\Tests\Fixture;

class SomeClass
{
    private $bProperty;
    private $aProperty;

    public function getAProperty() {}
    public function removeAProperty($item) {}
    public function getBProperty() {}
    public function addBProperty($item) {}
    public function setAProperty($value) {}
    public function setBProperty($value) {}
    public function addAProperty($item) {}
    public function removeBProperty($item) {}
}

?>
-----
<?php

namespace Alxndr\Tests\Fixture;

class SomeClass
{
    private $bProperty;
    private $aProperty;
    public function getBProperty() {}
    public function setBProperty($value) {}
    public function addBProperty($item) {}
    public function removeBProperty($item) {}
    public function getAProperty() {}
    public function setAProperty($value) {}
    public function addAProperty($item) {}
    public function removeAProperty($item) {}
}

?>
