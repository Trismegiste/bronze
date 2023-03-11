# Bronze
## Code your Proof Of Concept with Swag

Survival kit for your proof of concepts & quick n dirty tests. Start a PHP project in 10 seconds flat. No security, not for production :
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

# Restart the project

```bash
$ rm -rf .git
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
```

This method adds 5 routes : 
* ```/bicycle``` for list
* ```/bicycle/new/create``` for creating a new bicycle
* ```/bicycle/1234/show``` for viewing bicycle 1234
* ```/bicycle/1234/edit``` for editing bicycle 1234
* ```/bicycle/1234/delete``` for deleting bicycle 1234

It uses a entity with magic accessors/mutators but you can use your own entity, your own form and your own routes.

## Twig

Customize templates in ```./templates/bicycle``` for the entity name **bicycle** :
* list.html.twig
* show.html.twig
* form.html.twig
* create.html.twig
* edit.html.twig
* delete.html.twig

If one of these template does not exist, twig falls back to generic templates in ```./Resources/templates```
