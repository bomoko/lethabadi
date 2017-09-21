<?php namespace Bomoko\Lethaba;

class DIContainer
{

    protected $values = [];

    public function __construct(array $values = [])
    {
        $this->values = $values;
    }

    public function __invoke($key)
    {
        if (!key_exists($key, $this->values)) {
            throw new \InvalidArgumentException(sprintf("The key '%s' has not been bound in the container",
              $key));
        }

        //anything that's callable, gets called
        if (is_callable($this->values[$key])) {
            return $this->values[$key]($this);
        }

        return $this->values[$key];
    }

    public function bind($key, $value)
    {
        $this->values[$key] = $value;
    }

    public function bindSingleton($key, $value)
    {
        if (!is_callable($value)) {
            throw new \InvalidArgumentException("Singletons have to be invokable");
        }

        $this->values[$key] = function ($c) use ($value) {
            static $singleton;

            if(is_null($singleton)) {
                $singleton = $value($c);
            }
            return $singleton;
        };
    }
}