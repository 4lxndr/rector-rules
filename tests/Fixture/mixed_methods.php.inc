<?php

namespace Alxndr\Tests\Fixture;

class MixedMethods
{
    private $count;
    private $active;
    private $tag;

    public function calculate() {}
    public function addTag($tag) {}
    public function isActive() {}
    public function removeTag($tag) {}
    public function getCount() {}
    public function process() {}
    public function setActive($value) {}
    public function setCount($value) {}
    public function validate() {}
}

?>
-----
<?php

namespace Alxndr\Tests\Fixture;

class MixedMethods
{
    private $count;
    private $active;
    private $tag;

    public function getCount() {}
    public function setCount($value) {}
    public function isActive() {}
    public function setActive($value) {}
    public function addTag($tag) {}
    public function removeTag($tag) {}
    public function calculate() {}
    public function process() {}
    public function validate() {}
}

?>
