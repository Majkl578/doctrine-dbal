<?php

declare(strict_types=1);

namespace Doctrine\DBAL\Types;

use Doctrine\DBAL\DBALException;

final class TypeRegistry
{
    /**
     * Map of already instantiated type objects. One instance per type (flyweight).
     * @var TypeInterface[]
     */
    private $typeInstances = [];

    /**
     * Map of defined types and their classes
     * @var string[]
     */
    private $typesMap;

    /**
     * Factory method to create type instances.
     * Type instances are implemented as flyweights.
     *
     * @param string $name The name of the type (as returned by getName()).
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getType(string $name) : TypeInterface
    {
        if ( ! isset($this->typeInstances[$name])) {
            if ( ! isset($this->typesMap[$name])) {
                throw DBALException::unknownColumnType($name);
            }
            $this->typeInstances[$name] = new $this->typesMap[$name]();
        }

        return $this->typeInstances[$name];
    }

    /**
     * Adds a custom type to the type map.
     *
     * @param string $name      The name of the type. This should correspond to what getName() returns.
     * @param string $className The class name of the custom type.
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function addType(string $name, string $className) : void
    {
        if (isset($this->typesMap[$name])) {
            throw DBALException::typeExists($name);
        }

        $this->typesMap[$name] = $className;
    }

    /**
     * Checks if exists support for a type.
     *
     * @param string $name The name of the type.
     *
     * @return bool TRUE if type is supported; FALSE otherwise.
     */
    public function hasType(string $name) : bool
    {
        return isset($this->typesMap[$name]);
    }

    /**
     * Overrides an already defined type to use a different implementation.
     *
     * @param string $name
     * @param string $className
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function overrideType(string $name, string $className) : void
    {
        if ( ! isset($this->typesMap[$name])) {
            throw DBALException::typeNotFound($name);
        }

        if (isset($this->typeInstances[$name])) {
            unset($this->typeInstances[$name]);
        }

        $this->typesMap[$name] = $className;
    }

    /**
     * Gets the types array map which holds all registered types and the corresponding
     * type class
     *
     * @return string[]
     */
    public function getTypesMap() : array
    {
        return $this->typesMap;
    }

    /**
     * @throws DBALException
     */
    public function lookupName(TypeInterface $type) : string
    {
        $name = array_search($type, $this->typeInstances, true);

        if ($name !== false) {
            return $name;
        }

        $name = array_search(get_class($type), $this->typesMap, true);

        if ($this->typesMap !== false) {
            return $name;
        }

        throw DBALException::typeNotFound($type);
    }
}
