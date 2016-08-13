Symfony Dependency Injection Magic
====================================

This bundle aims to reduce usage of service locator in Symfony's Controllers by providing you a way to inject services
using [double dispatch](https://en.wikipedia.org/wiki/Double_dispatch). If you don't know why this may be usefull,
[see below](#why-do-i-may-need-it)


***Note that this is currently just poof-of-concept implementation.*** 

## Installation

TODO

## Why do I may need it?

First of all, lets be clear. It's OK to use [service locator is controllers](http://davedevelopment.co.uk/2016/06/01/service-locators-have-their-place.html)
since we should't care about coupling in controllers since it already tightly coupled to framework.

But service locator usage has it's onw big disatwantage: implicit dependency management. 
You can't use all this cool refactoring tools that IDE provide you without some plugins.

## Controllers as Service

There is already pretty good solution for this problem - [DunglasActionBundle](https://github.com/dunglas/DunglasActionBundle).
This solution allows you to register your POPO controllers as services automatically and use the power of autowiring.

The problem is the idea of "[controllers as services](http://symfony.com/doc/current/controller/service.html)".
This requires dev to have relatively small controllers to reduce amount of dependencies for controllers. 
As a solution for this particular problem ActionBundle provide you "Actions". But this leads to even more problem, 
now developers have enough space to put logic in it instead of making it thin.

I needed solution that forces developers to:

 - Make controller as thin as possible
 - Simplify life when you add service per action (something like use-cases in Uncle Bob's clean architecture)
 
So here it is:

```php
/**
 * @Route("/users")
 * @Method("POST")
 */
public function registerUser(RegisterUserRequest $request, RegisterUserAction $action, UserDetailsResponder $responder)
{
   // Action is just a service which contains
   // orcestration logic and do all the stuff
   //
   // Request - this is DTO which already contains valid data
   // https://github.com/fesor/request-objects
   $user = $action($request);
   
   // Just calling Doctrine to flush changes
   $this->flushChanges();
   
   // Responder - this is just small service with responsibility
   // of providing HTTP responses. This allows to DRY stuff.    
   return $responder($user);
}
```

As you can see we have how pretty thin controller action. And since we separated concerns on performing request action
and preparing response we are now flexible as never before!

## Limitations

** This bundle doesn't provide you autowiring of method calls! **

All services should be registered in container, no runtime autowiring will be provided. If you are to lazy to register
services manually, well... you coudl add compile pass which will register all classes in specific directory as services.
This is much more flexible solution.

## Contribution

Feel free to contribute to this project!

## License

This bundle is under the MIT license