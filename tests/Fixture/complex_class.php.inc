<?php

namespace Alxndr\Tests\Fixture;

class ComplexClass
{
    private $status;
    private $enabled;
    private $permission;
    private $edit;
    private $item;
    private $metadata;

    public function getItem() {}
    public function businessMethod() {}
    public function setStatus($value) {}
    public function __toString() {}
    public function hasPermission($name) {}
    public function addItem($item) {}
    public function isEnabled() {}
    public function __construct() {}
    public function removePermission($name) {}
    public function canEdit() {}
    public function setEnabled($value) {}
    public function getStatus() {}
    public function addPermission($name) {}
    public function removeItem($item) {}
    public function getMetadata() {}
    public function setMetadata($data) {}
    public function anotherBusinessMethod() {}
}

?>
-----
<?php

namespace Alxndr\Tests\Fixture;

class ComplexClass
{
    private $status;
    private $enabled;
    private $permission;
    private $edit;
    private $item;
    private $metadata;
    public function __construct() {}
    public function __toString() {}
    public function getStatus() {}
    public function setStatus($value) {}
    public function isEnabled() {}
    public function setEnabled($value) {}
    public function hasPermission($name) {}
    public function addPermission($name) {}
    public function removePermission($name) {}
    public function canEdit() {}
    public function getItem() {}
    public function addItem($item) {}
    public function removeItem($item) {}
    public function getMetadata() {}
    public function setMetadata($data) {}
    public function businessMethod() {}
    public function anotherBusinessMethod() {}
}

?>
