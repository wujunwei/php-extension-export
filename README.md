# php-extension-export
export php extension to php file
## How to use
```php
require "../vendor/autoload.php";
try {
    FirstW\Export\Extension::dump('swoole', './swoole/');
} catch (Exception $e) {
    echo 'fail:'.$e->getMessage();
}
```
