<?php

namespace Alxndr\Tests\Fixture;

class WithMagicMethods
{
    private $name;
    private $value;

    public function getName() {}
    public function __toString() {}
    public function setValue($val) {}
    public function getValue() {}
    public function __construct($name, $value) {}
    public function setName($name) {}
}

?>
-----
<?php

namespace Alxndr\Tests\Fixture;

class WithMagicMethods
{
    private $name;
    private $value;
    public function __construct($name, $value) {}
    public function __toString() {}
    public function getName() {}
    public function setName($name) {}
    public function getValue() {}
    public function setValue($val) {}
}

?>
