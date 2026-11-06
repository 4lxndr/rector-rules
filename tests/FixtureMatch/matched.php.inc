<?php

namespace Alxndr\Tests\FixtureMatch;

class Matched
{
    private $b;
    private $a;

    public function getA() {}
    public function getB() {}
}

?>
-----
<?php

namespace Alxndr\Tests\FixtureMatch;

class Matched
{
    private $b;
    private $a;
    public function getB() {}
    public function getA() {}
}

?>
