<?php namespace Bomoko\Lethaba;

use Psr\Container;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Bomoko\Lethaba\Exception\ContainerEntryNotFoundException;

class DIContainer implements Container\ContainerInterface
{

    protected $values = [];

    /**
     * DIContainer constructor.
     *
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        $this->values = $values;
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this**
     *   identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new ContainerEntryNotFoundException(sprintf("The key '%s' has not been bound in the container",
              $id));
        }

        //anything that's callable, gets called
        if (is_callable($this->values[$id])) {
            return $this->values[$id]($this);
        }

        return $this->values[$id];
    }

    /**
     * Returns true if the container can return an entry for the given
     * identifier. Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw
     * an exception. It does however mean that `get($id)` will not throw a
     * `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id)
    {
        return key_exists($id, $this->values);
    }

    /**
     * Invoking the object with an argument will retrieve an entry of the
     * container by its identifier and return it.
     *
     * @param $id
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this**
     *   identifier.
     *
     * @return mixed
     */
    public function __invoke($id)
    {
        return $this->get($id);
    }

    /**
     * Adds a parameter or service into the container.
     *
     * @param $id
     * @param $value
     *
     * @return void
     */
    public function bind($id, $value)
    {
        $this->values[$id] = $value;
    }

    /**
     * Given a service, this will run that service once, and return its result
     * on all subsequent calls.
     *
     * @param $id
     * @param $value
     *
     * @return void
     */
    public function bindSingleton($id, $value)
    {
        if (!is_callable($value)) {
            throw new \InvalidArgumentException("Singletons have to be invokable");
        }

        $this->values[$id] = function ($c) use ($value) {
            static $singleton;

            if (is_null($singleton)) {
                $singleton = $value($c);
            }
            return $singleton;
        };
    }

    /**
     * Given a value to store in the container, it will return that value
     * unmolested
     * (i.e. if passing a callable, the callable will be returned un-invoked).
     *
     * @param $id
     * @param $value
     *
     * @return void
     */
    public function protect($id, $value)
    {
        $this->values[$id] = function ($c) use ($value) {
            return $value;
        };
    }

    /**
     * Given a service that already
     *
     * @param $id
     * @param $value
     *
     * @return void
     */
    public function extend($id, $value)
    {
        if (!$this->has($id)) {
            throw new ContainerEntryNotFoundException(sprintf("The key '%s' has not been bound in the container",
              $id));
        }

        if (!is_callable($this->values[$id])) {
            throw new \InvalidArgumentException(sprintf("%s has to be invokable in order to extend it",
              $id));
        }

        if (!is_callable($value)) {
            throw new \InvalidArgumentException("Service extensions have to be invokable");
        }

        $extendedService = $this->values[$id];

        $this->values[$id] = function ($c) use ($extendedService, $value) {
            return $value($extendedService($c), $c);
        };
    }

}