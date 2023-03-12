# Bronze
## Code your Proof Of Concept with Swag

Survival kit for your proof of concepts & quick n dirty tests. Start a PHP project in 10 seconds flat. 
It feels like Symfony but without ANY config, cache, model, entity, database config, form class... Inspired by Silex (hence the name : Bronze).
No security at all, not intended for production **AT ALL**. It includes :
* PHP 8.1
* Symfony Kernel & HttpFoundation
* Symfony Forms with Validators
* Twig
* MongoDb
* PicoCSS
* AlpineJS

# Install

```bash
$ docker compose build && docker compose up
```

# Access to app

```bash
$ firefox http://127.0.0.1:8000/
```

# Hack n slash

## Run tests
```bash
$ docker exec -it bronze-symfony-1 vendor/bin/phpunit
```

## Run mongo shell
```bash
$ docker exec -it bronze-mongo-1 mongo
```

# Customize

## Edit ```index.php```

### Run a full business app with LCRUD :
```php
$app = new BusinessApp();

// database name : bronze
// entity name and collection name : bicycle

$app->crud('bronze', 'bicycle', function (FormBuilderInterface $builder) {
    return $builder
            ->add('brand', TextType::class)
            ->add('size', TextType::class)
            ->add('color', TextType::class)
            ->add('save', SubmitType::class)
            ->getForm();
});

$app->run();
```

This method adds 5 routes : 
* ```/bicycle``` for list
* ```/bicycle/new/create``` for creating a new bicycle
* ```/bicycle/1234/show``` for viewing bicycle 1234
* ```/bicycle/1234/edit``` for editing bicycle 1234
* ```/bicycle/1234/delete``` for deleting bicycle 1234

It uses a entity with magic accessors/mutators but you can use your own entity, your own form and your own routes.

### If your app doesn't need form nor CRUD
Homepage '/' with a template ```index.html.twig``` stored in ```./templates``` :
```php
$app = new \Trismegiste\Bronze\Core\WebApp();

$app->get('/', function () {
    return $this->render('index.html.twig', ['message' => 'Hi Mom !']);
});

$app->run();
```
### If your app does not need Twig
```php
$app = new Trismegiste\Bronze\Core\App();

$app->get('/', function () {
    return new \Symfony\Component\HttpFoundation\JsonResponse(['message' => 'Hi Mom !']);
});

$app->run();
```

All controllers are Closures but they are bound to feel like you're using a AbstractController from Symfony.

## Twig

Customize templates in ```./templates/bicycle``` for the entity name **bicycle** :
* list.html.twig
* show.html.twig
* form.html.twig
* create.html.twig
* edit.html.twig
* delete.html.twig

If one of these template does not exist, twig falls back to generic templates in ```./Resources/templates```
