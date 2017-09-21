# Lethaba DI
This is a simple DI container, inspired by (read, shamelessly copied from) [Pimple 1.x](https://github.com/silexphp/Pimple/tree/1.1)

## Usage

Instantiate your container

`$container = new DIContainer();`

you can optionally pass an array of initial parameters at construction time

`$container = new DIContainer(['parameter' => 'value']);`

### Defining Parameters and Services

You're able to add simple values, objects, and functions by binding them to the container

`$container->bind('serviceName' => $service);`

You can then access the service by invoking the container with the service's name

`$serviceResult = $container('serviceName');`

### Protecting Parameters

If you want to store an invokable object as a parameter, you have to bind it to the container using the protect() method.
The container will try to run any invokable by default during service resolution. Protecting the parameter means that you're guaranteed to get back what you put in.

`$container->protect('protectedParameter', function () { return 'will run outside container';});`

### Defining Shared Services

Shared services return the same instance every time the service is resolved. To define a shared service, simply bind it to the container with the bindSingleton method.

```
$container->bindSingleton('serviceName', function ($c) {
   return new someSharedObject($c('someParameter')); 
});
```

### Modifying Existing Services

You're able to modify the behaviour of an existing service by using the extend() method.
Service extensions should be invokable with two arguments, first, the value returned by the existing service, and second, the container instance.

Here is an example of defining a service that returns a string, and then extending it to modify the string.

```
    $container = new DIContainer();
    $container->bind('service', function ($c) {
        return "inside";
    });

    $container->extend('service', function ($innerResult, $c) {
      return "outside-" . $innerResult . "-outside";
    });

    $output = $container('service'); //will contain 'outside-inside-outside'
```

