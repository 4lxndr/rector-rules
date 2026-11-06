<?php

declare(strict_types=1);

namespace Alxndr\Rector;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class SortGettersSettersByPropertyOrderRector extends AbstractRector implements ConfigurableRectorInterface
{
    private const ACCESSOR_ORDER = ['get' => 1, 'is' => 1, 'has' => 1, 'can' => 1, 'should' => 1, 'set' => 2, 'add' => 3, 'remove' => 4];

    private const MAGIC_METHOD_ORDER = [
        '__construct' => 1,
        '__destruct' => 2,
        '__toString' => 3,
        '__invoke' => 4,
        '__get' => 5,
        '__set' => 6,
        '__isset' => 7,
        '__unset' => 8,
        '__call' => 9,
        '__callStatic' => 10,
        '__clone' => 11,
        '__sleep' => 12,
        '__wakeup' => 13,
        '__serialize' => 14,
        '__unserialize' => 15,
        '__debugInfo' => 16,
    ];

    /**
     * @var callable(string): bool|null
     */
    private $matchCallback;

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Sorts getters (get/is/has/can/should), setters, adders, and removers by the order of properties, with magic methods first', [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    private $bProperty;
    private $aProperty;

    public function getBProperty() {}
    public function __toString() {}
    public function setBProperty() {}
    public function addBProperty() {}
    public function __construct() {}
    public function removeBProperty() {}
    public function getAProperty() {}
    public function setAProperty() {}
    public function addAProperty() {}
    public function removeAProperty() {}
}
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
class SomeClass
{
    private $bProperty;
    private $aProperty;

    public function __construct() {}
    public function __toString() {}
    public function getAProperty() {}
    public function setAProperty() {}
    public function addAProperty() {}
    public function removeAProperty() {}
    public function getBProperty() {}
    public function setBProperty() {}
    public function addBProperty() {}
    public function removeBProperty() {}
}
CODE_SAMPLE
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof Class_) {
            return null;
        }

        if ($this->matchCallback !== null && !($this->matchCallback)($this->getFile()->getFilePath())) {
            return null;
        }

        $propertyIndex = [];
        foreach ($node->getProperties() as $property) {
            foreach ($property->props as $prop) {
                $propertyIndex[$this->getName($prop)] ??= \count($propertyIndex);
            }
        }

        if ($propertyIndex === []) {
            return null;
        }

        $keys = [];
        foreach ($node->stmts as $position => $stmt) {
            $keys[$position] = [...$this->sortKey($stmt, $propertyIndex), $position];
        }

        \asort($keys);
        $positions = \array_keys($keys);

        if ($positions === \array_keys($node->stmts)) {
            return null;
        }

        $node->stmts = \array_map(static fn (int $position): Node\Stmt => $node->stmts[$position], $positions);

        return $node;
    }

    /**
     * Non-methods, magic methods, accessors of known properties, accessors of unknown properties, other methods.
     *
     * @param array<string, int> $propertyIndex
     *
     * @return array{int, int, int}
     */
    private function sortKey(Node\Stmt $stmt, array $propertyIndex): array
    {
        if (!$stmt instanceof ClassMethod) {
            return [0, 0, 0];
        }

        $name = $this->getName($stmt);

        if (\str_starts_with($name, '__')) {
            return [1, self::MAGIC_METHOD_ORDER[$name] ?? \PHP_INT_MAX, 0];
        }

        if (!\preg_match('/^(get|is|has|can|should|set|add|remove)([A-Z].*)$/', $name, $matches)) {
            return [4, 0, 0];
        }

        $index = $propertyIndex[\lcfirst($matches[2])] ?? null;

        return $index === null ? [3, 0, 0] : [2, $index, self::ACCESSOR_ORDER[$matches[1]]];
    }

    /**
     * @param array{match?: callable} $configuration
     */
    public function configure(array $configuration): void
    {
        $this->matchCallback = $configuration['match'] ?? null;
    }
}
