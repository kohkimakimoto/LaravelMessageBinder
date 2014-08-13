# Laravel Message Binder

A little hack for Illminate View to access flash messages easily.

You can use the `withMessages` method like a laravel builtin method `withErrors` in a general purpose.

```php
Route::get('register', function()
{
    return View::make('user.register');
});

Route::post('register', function()
{
    $rules = array(...);

    $validator = Validator::make(Input::all(), $rules);

    if ($validator->fails())
    {
        return Redirect::to('register')->withErrors($validator);
    }

    // You can use the `withMessages` method like a laravel builtin method `withErrors` in a general purpose.
    return Redirect::to('register')->withMessages(array('default' => 'Success!');
});
```

After redirection, you may utilize the automatically bound $messages variable in your view:

```
<?php echo $messages->first('default'); ?>
```

see also [laravel docs#validation](http://laravel.com/docs/validation#error-messages-and-views)

## Installation

Add dependency in `composer.json`

```json
"require": {
    "kohkimakimoto/laravel-message-binder": "dev-master"
}
```

Run `composer update` command.

```
$ composer update
```

Add `MessageBinderServiceProvider` to providers array in `app/config/app.php`

```php
'providers' => array(
    ...
    'Kohkimakimoto\MessageBinder\MessageBinderServiceProvider',
),
```

## LICENSE

The MIT License

## Author 

Kohki Makimoto <kohki.makimoto@gmail.com>
