# jsend-response

Implementation of the [JSend](https://labs.omniti.com/labs/jsend) standard JSON response.

## Usage

```php
// Success
$jsend = new JSend();
$jsend->success();

// Fail
$jsend = new JSend();
$jsend->fail("Invalid username");

// Error with optional code
$jsend = new JSend();
$jsend->error("Unable to communicate with database")->addCode(10);

// Transform Exception
$jsend = new JSend();
try {
    doWork();
} catch (SomeExeption $exception) {
    $result = $jsend->errorException($exception)->getJson();
}

// Example of use in Slim framework 
$app = new \Slim\App();

$container = $app->getContainer();

$container['jsend'] = function() {
    return new \JSend\JSend();
};

$app->get('/jsend',  function (Request $request, Response $response, array $args) {
    $result = $this->get('jsend')->success();
    return $response->withJson((string) $result);
});
```

Also check the [unit test](./tests/JSendTest.php).

## Development

Run unit tests:
    
    $ composer run-tests
    
Run phpcs, phpmd and phpcpd:

    $ composer coding-standard
