<?php

namespace App\Module\Collection;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\ClosureExpressionVisitor;
use Doctrine\Common\Collections\Selectable;
use InvalidArgumentException;

abstract class ObjectCollection implements \Countable, \IteratorAggregate, Selectable
{
    private $items = [];

    abstract protected static function getItemType(): string;

    /**
     * @param mixed[] $items
     *
     * @throws InvalidArgumentException
     */
    public function __construct(iterable $items = [])
    {
        self::assertItemsInstanceOf($items, static::getItemType());

        $this->setItems($items);
    }

    /**
     * @param mixed $item
     *
     * @throws InvalidArgumentException
     */
    final public function add($item): self
    {
        self::assertItemInstanceOf($item, static::getItemType());

        $this->items[] = $item;

        return $this;
    }

    final public function merge(self $collection): self
    {
        $items = $this->getItems();

        foreach ($collection as $item) {
            $items[] = $item;
        }

        return new static($items);
    }

    final public function slice(int $offset, ?int $length = null): self
    {
        return new static(array_slice($this->getItems(), $offset, $length));
    }

    /**
     * @return mixed[]
     */
    final public function map(callable $mapper): iterable
    {
        return array_map($mapper, $this->getItems());
    }

    final public function has(object $neededObject): bool
    {
        return (bool) $this->find(static function ($object) use ($neededObject) {
            return $object === $neededObject;
        });
    }

    final public function equals(self $collection): bool
    {
        foreach ($collection as $item) {
            if (!$this->has($item)) {
                return false;
            }
        }

        return $this->count() === $collection->count();
    }

    final public function remove(object $objectForRemoving): self
    {
        return $this->filter(static function ($item) use ($objectForRemoving) {
            return $item !== $objectForRemoving;
        });
    }

    final public function getIterator(): iterable
    {
        return new \ArrayIterator($this->getItems());
    }

    final public function count(): int
    {
        return count($this->getItems());
    }

    final protected function filter(callable $filter): self
    {
        return new static(array_values(array_filter($this->getItems(), $filter)));
    }

    final protected function find(callable $expression): ?object
    {
        foreach ($this->getItems() as $item) {
            if ($expression($item)) {
                return $item;
            }
        }

        return null;
    }

    final protected function getFirstItem(): ?object
    {
        $firstItem = current($this->getItems());

        return $firstItem ?: null;
    }

    final protected function getLastItem(): ?object
    {
        $items = $this->getItems();
        $lastItem = end($items);

        return $lastItem ?: null;
    }

    /**
     * @param mixed[] $items
     */
    private function setItems(iterable $items): void
    {
        $this->items = $items instanceof \Traversable ? iterator_to_array($items) : $items;
    }

    /**
     * @return mixed[]
     */
    final protected function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param mixed[] $items
     *
     * @throws InvalidArgumentException
     */
    private static function assertItemsInstanceOf(iterable $items, string $expectedItemType): void
    {
        foreach ($items as $item) {
            self::assertItemInstanceOf($item, $expectedItemType);
        }
    }

    /**
     * @param mixed $item
     *
     * @throws InvalidArgumentException
     */
    private static function assertItemInstanceOf($item, string $expectedItemType): void
    {
        if ($item instanceof $expectedItemType) {
            return;
        }

        throw new InvalidArgumentException(sprintf(
            'Collection should has only items of "%s".',
            $expectedItemType
        ));
    }

    public function isEmpty(): bool
    {
        return empty($this->getItems());
    }

    /**
     * {@inheritDoc}
     *
     * @return self
     */
    public function matching(Criteria $criteria)
    {
        $expression = $criteria->getWhereExpression();
        $filtered = $this->items;

        if ($expression) {
            $visitor = new ClosureExpressionVisitor();
            $filter = $visitor->dispatch($expression);
            $filtered = array_filter($filtered, $filter);
        }

        $orderings = $criteria->getOrderings();

        if ($orderings) {
            $next = null;

            foreach (array_reverse($orderings) as $field => $ordering) {
                $next = ClosureExpressionVisitor::sortByField($field, $ordering === Criteria::DESC ? -1 : 1, $next);
            }

            uasort($filtered, $next);
        }

        $offset = $criteria->getFirstResult();
        $length = $criteria->getMaxResults();

        if ($offset || $length) {
            $filtered = array_slice($filtered, (int) $offset, $length);
        }

        return new static($filtered);
    }
}
