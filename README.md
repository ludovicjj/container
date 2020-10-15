# Conteneur de dépendance

Création d'un conteneur d'injection de dépendance implémentant l'interfaces du PSR11 
et utilisant l'API de reflection de PHP.

## Capacité du conteneur

* Créer une instance d'une classe sans dépendance
* Créer une instance d'une classe avec dépendance(s)
* Créer une instance d'une classe avec dépendance(s) sur plusieurs niveaux de profondeur
* Créer une instance d'une classe à partir d'une interface
* Gestion des dépendances de type scalaire optionnel ou non optionnel

## Interface

Pour instancier une classe à partir d'une interface, il faudra indiquer à quelle
classe est rattachée cette interface. 
Sinon le conteneur renverra une exception de type ```NotFoundExceptionInterface``` (PSR11).

Pour associer une interface à une classe, il faudra procéder de la façon suivante :

```
$container = new container();

// Association Interface/Class
$container->addAlias(FooInterface::class, Foo::class);

// Création de l'instance
$container->get(FooInterface::class);
```

La methode ```addAlias()``` utilise le modèle Fluent.

## Paramètre

Pour instancier une classe avec des paramètres non optionnels, il faudra indiquer la valeur de
chaque paramètre. 
Sinon le conteneur renverra une exception de type ```NotFoundExceptionInterface``` (PSR11).

Pour associer une valeur à un paramètre, il faudra procéder de la façon suivante :

```
$container = new container();

// Association paramètre/valeur
$container
    ->addParameter('name', 'John')
    ->addParameter('surname', 'Doe');

// Création de l'instance
$container->get(Bar::class);
```

La methode ```addParameter()``` utilise le modèle Fluent.

## Tests

Lancer les tests :

```
vendor/bin/phpunit
```

Lancer les tests avec couverture de code :

```
vendor/bin/phpunit --coverage-html=coverage/
```