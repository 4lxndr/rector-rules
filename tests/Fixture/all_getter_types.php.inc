<?php

namespace Alxndr\Tests\Fixture;

class AllGetterTypes
{
    private $name;
    private $enabled;
    private $permission;
    private $execute;
    private $validate;

    public function shouldValidate() {}
    public function hasPermission() {}
    public function getName() {}
    public function canExecute() {}
    public function isEnabled() {}
}

?>
-----
<?php

namespace Alxndr\Tests\Fixture;

class AllGetterTypes
{
    private $name;
    private $enabled;
    private $permission;
    private $execute;
    private $validate;
    public function getName() {}
    public function isEnabled() {}
    public function hasPermission() {}
    public function canExecute() {}
    public function shouldValidate() {}
}

?>
