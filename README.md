# Custom Rector Rules

This project contains custom Rector rules for PHP code refactoring.

## Rules

### SortGettersSettersByPropertyOrderRector

Sorts accessor methods by the order of properties in the class, with magic methods first.

This rule organizes methods in the following order:
1. Magic methods (e.g., `__construct`, `__toString`) come first
2. Accessor methods are sorted by their corresponding property order in the class
3. For each property, accessors are ordered as: getter, setter, adder, remover
4. Other methods (business logic) remain at the end in their relative order

Supported accessor method prefixes:
- **Getters**: `get*`, `is*`, `has*`, `can*`, `should*`
- **Setters**: `set*`
- **Adders**: `add*`
- **Removers**: `remove*`

**Example:**

```php
// Before
class SomeClass
{
    private $status;
    private $enabled;
    private $permission;
    private $edit;
    private $item;

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
}

// After
class SomeClass
{
    private $status;
    private $enabled;
    private $permission;
    private $edit;
    private $item;

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
    public function businessMethod() {}
}
```
