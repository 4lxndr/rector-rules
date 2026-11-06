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

        $infos = [];
        foreach ($node->stmts as $stmt) {
            $infos[\spl_object_id($stmt)] = $this->describe($stmt, $propertyIndex);
        }

        $sortedStmts = $node->stmts;
        \usort($sortedStmts, static fn (Node\Stmt $a, Node\Stmt $b): int => self::compare($infos[\spl_object_id($a)], $infos[\spl_object_id($b)]));

        if ($sortedStmts === $node->stmts) {
            return null;
        }

        $node->stmts = $sortedStmts;

        return $node;
    }

    /**
     * @param array<string, int> $propertyIndex
     *
     * @return array{magic: int|null, accessor: bool, index: int|null, type: int}|null null for a non-method
     */
    private function describe(Node\Stmt $stmt, array $propertyIndex): ?array
    {
        if (!$stmt instanceof ClassMethod) {
            return null;
        }

        $name = $this->getName($stmt);

        if (\str_starts_with($name, '__')) {
            return ['magic' => self::MAGIC_METHOD_ORDER[$name] ?? 999, 'accessor' => false, 'index' => null, 'type' => 0];
        }

        if (!\preg_match('/^(get|set|add|remove|is|has|can|should)([A-Z].*)$/', $name, $matches)) {
            return ['magic' => null, 'accessor' => false, 'index' => null, 'type' => 0];
        }

        return [
            'magic' => null,
            'accessor' => true,
            'index' => $propertyIndex[\lcfirst($matches[2])] ?? null,
            'type' => self::ACCESSOR_ORDER[$matches[1]],
        ];
    }

    /**
     * Non-methods first, then magic methods, then accessors in property order, then the rest.
     * An accessor of an unknown property compares equal to other accessors, as in 1.0.0.
     *
     * @param array{magic: int|null, accessor: bool, index: int|null, type: int}|null $a
     * @param array{magic: int|null, accessor: bool, index: int|null, type: int}|null $b
     */
    private static function compare(?array $a, ?array $b): int
    {
        if ($a === null || $b === null) {
            return ($b === null) <=> ($a === null);
        }

        if ($a['magic'] !== null || $b['magic'] !== null) {
            return ($a['magic'] ?? \PHP_INT_MAX) <=> ($b['magic'] ?? \PHP_INT_MAX);
        }

        if (!$a['accessor'] || !$b['accessor']) {
            return $b['accessor'] <=> $a['accessor'];
        }

        if ($a['index'] === null || $b['index'] === null) {
            return 0;
        }

        return [$a['index'], $a['type']] <=> [$b['index'], $b['type']];
    }

    /**
     * @param array{match?: callable} $configuration
     */
    public function configure(array $configuration): void
    {
        $this->matchCallback = $configuration['match'] ?? null;
    }
}
