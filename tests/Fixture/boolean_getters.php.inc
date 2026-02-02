<?php

namespace Alxndr\Tests\Fixture;

class BooleanGettersClass
{
    private $enabled;
    private $permission;
    private $editable;
    private $edit;
    private $valid;
    private $validate;

    public function shouldValidate() {}
    public function setEnabled($value) {}
    public function hasPermission($name) {}
    public function isEditable() {}
    public function canEdit() {}
    public function setValid($value) {}
    public function isEnabled() {}
    public function isValid() {}
}

?>
-----
<?php

namespace Alxndr\Tests\Fixture;

class BooleanGettersClass
{
    private $enabled;
    private $permission;
    private $editable;
    private $edit;
    private $valid;
    private $validate;

    public function isEnabled() {}
    public function setEnabled($value) {}
    public function hasPermission($name) {}
    public function isEditable() {}
    public function canEdit() {}
    public function isValid() {}
    public function setValid($value) {}
    public function shouldValidate() {}
}

?>
