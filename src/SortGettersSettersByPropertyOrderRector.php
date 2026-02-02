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
    /**
     * @var callable|null
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

        // Check if a file matches the configured filter
        if ($this->matchCallback !== null) {
            $file = $this->file->getFilePath();
            if (!\call_user_func($this->matchCallback, $file)) {
                return null;
            }
        }

        // Collect the properties in the order of their appearance
        $properties = [];
        foreach ($node->getProperties() as $property) {
            foreach ($property->props as $prop) {
                $properties[] = $this->getName($prop);
            }
        }

        if ($properties === []) {
            return null;
        }

        // Clone the stmts array to avoid modifying in place
        $originalStmts = $node->stmts;
        $sortedStmts = [...$node->stmts];

        // Sort the methods: magic methods first, then getters/setters/adders/removers by property order, then other methods
        \usort($sortedStmts, function ($a, $b) use ($properties) {
            // Non-methods stay in their original position relative to each other
            if (!$a instanceof ClassMethod && !$b instanceof ClassMethod) {
                return 0;
            }

            // Methods come after non-methods
            if (!$a instanceof ClassMethod) {
                return -1;
            }

            if (!$b instanceof ClassMethod) {
                return 1;
            }

            $aName = $this->getName($a);
            $bName = $this->getName($b);

            $aIsMagic = $this->isMagicMethod($aName);
            $bIsMagic = $this->isMagicMethod($bName);

            // Magic methods always come first
            if ($aIsMagic && !$bIsMagic) {
                return -1;
            }

            if (!$aIsMagic && $bIsMagic) {
                return 1;
            }

            // Both are magic methods - sort by magic method order
            if ($aIsMagic && $bIsMagic) {
                return $this->getMagicMethodOrder($aName) <=> $this->getMagicMethodOrder($bName);
            }

            $aProperty = $this->extractPropertyFromMethodName($aName);
            $bProperty = $this->extractPropertyFromMethodName($bName);

            // Only sort getter/setter/adder/remover methods
            if (!$aProperty && !$bProperty) {
                return 0;
            }

            if (!$aProperty) {
                return 1;
            }

            if (!$bProperty) {
                return -1;
            }

            // Both methods belong to properties
            if (\in_array($aProperty, $properties, true) && \in_array($bProperty, $properties, true)) {
                // First sort by property order
                $aPropertyIndex = \array_search($aProperty, $properties, true);
                $bPropertyIndex = \array_search($bProperty, $properties, true);
                $propertyComparison = $aPropertyIndex <=> $bPropertyIndex;

                if ($propertyComparison !== 0) {
                    return $propertyComparison;
                }

                // Then sort by method type (get, set, add, remove)
                $methodOrder = ['get' => 1, 'set' => 2, 'add' => 3, 'remove' => 4];
                $aType = $this->extractMethodType($aName);
                $bType = $this->extractMethodType($bName);

                return ($methodOrder[$aType] ?? 5) <=> ($methodOrder[$bType] ?? 5);
            }

            return 0;
        });

        // Check if the order actually changed by comparing object IDs
        $originalIds = \array_map('spl_object_id', $originalStmts);
        $sortedIds = \array_map('spl_object_id', $sortedStmts);

        if ($originalIds === $sortedIds) {
            return null;
        }

        $node->stmts = $sortedStmts;

        return $node;
    }

    private function extractPropertyFromMethodName(string $methodName): ?string
    {
        if (\preg_match('/^(get|set|add|remove|is|has|can|should)([A-Z].*)$/', $methodName, $matches)) {
            return \lcfirst($matches[2]);
        }

        return null;
    }

    private function extractMethodType(string $methodName): ?string
    {
        if (\preg_match('/^(get|set|add|remove|is|has|can|should)/', $methodName, $matches)) {
            // Treat is/has/can/should as getter variants
            $type = $matches[1];

            return \in_array($type, ['is', 'has', 'can', 'should'], true) ? 'get' : $type;
        }

        return null;
    }

    private function isMagicMethod(string $methodName): bool
    {
        return \str_starts_with($methodName, '__');
    }

    private function getMagicMethodOrder(string $methodName): int
    {
        $magicMethodOrder = [
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

        return $magicMethodOrder[$methodName] ?? 999;
    }

    /**
     * @param array{match?: callable} $configuration
     */
    public function configure(array $configuration): void
    {
        $this->matchCallback = $configuration['match'] ?? null;
    }
}
