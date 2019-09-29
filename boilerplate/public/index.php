<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_WARNING | E_PARSE);

/**
 * Use Composer’s autoloading. This is mandatory as µ doesn’t work without it
 * as well as you won’t be able to access any of its functionality.
 *
 * @see https://getcomposer.org/doc/01-basic-usage.md#autoloading
 */
require __DIR__ . '/../vendor/autoload.php';


use function µ\config;
use function µ\router;
use function µ\template;

// Start a session
session_start();

/**
 * Load a config files
 */
config()->append(__DIR__ . '/../configs/micro-framework.yaml');
config()->append(__DIR__ . '/../configs/xman.yaml');

/**
 * Add global data/functions that should be available in all templates.
 */
template()->addData([
    'paths' => config()->get('µ.paths')
]);


/**
 * Register routes…
 */

// Download routes
router()->get('/downloads/modx/latest/', function(){
  xman\helper\getCurrentModxVersionDownload();
});

// Login route (unauthenticated)
router()->addRoute(['GET','POST'], '/login/', function () {
    // Redirect user if already logged in
    if (xman\user\isLoggedIn()) {
        xman\helper\redirect('/instances/');

        return true;
    }

    // Check login if POST was submitted
    $loginResponse = "";
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $loginResponse = xman\user\login($_POST['username'], $_POST['password']);

        if ($loginResponse === true) {
            xman\helper\redirect('/instances/');
        }
    }

    // Render login template
    echo template()->render('login', array(
    'error' => $loginResponse
  ));

    return true;
});

// All authenticated routes
router()->addGroup('/', function ($r) {
    // Redirect user to login if user is not logged in and is not on a allowed unauthenticated route
    $allowedUnauthRoutes = array(
      '/login/',
      '/downloads/modx/latest/',
    );

    if (!xman\user\isLoggedIn() && !in_array(xman\helper\getRoute(), $allowedUnauthRoutes)) {
        xman\helper\redirect('/login/');

        return false;
    }

    if (xman\helper\getRoute() === "/") {
        xman\helper\redirect('/instances/');
    }

    // Logout
    router()->get('logout/', function () {
        xman\user\logout();
        xman\helper\redirect('/login');
    });

    // Instance list
    router()->get('instances/', function () {
        echo template()->render('instances/list', ['instances' => xman\instance\getAll("", config()->get('xman.instances.list_limit')), 'totalInstanceCount' => xman\instance\getCount()]);

        return true;
    });
    router()->post('instances/', function () {
        $search = strip_tags($_POST['search']);
        echo template()->render('instances/list', ['instances' => xman\instance\getAll($search, config()->get('xman.instances.list_limit')), 'totalInstanceCount' => xman\instance\getCount()]);

        return true;
    });

    // Instance detail page
    router()->get('instances/{id:\d+}/', function ($id) {
        echo template()->render('instances/detail', ['instance' => xman\instance\get($id)]);
    });

    // Instance create page
    router()->get('instances/create/', function () {
        echo template()->render('instances/create');
    });
    router()->post('instances/create/', function () {
        $response = xman\instance\create();
        echo template()->render('instances/create', $response);
    });

    // Instance edit page
    router()->get('instances/edit/{id:\d+}/', function ($id) {
        if (!xman\instance\get($id)) {
            echo template()->render('instances/edit', ['error' => "Instance with the ID: '{$id}' not found"]);
        } else {
            echo template()->render('instances/edit', ['instance' => xman\instance\get($id)]);
        }
    });
    router()->post('instances/edit/{id:\d+}/', function ($id) {
        $response = xman\instance\update($id);
        echo template()->render('instances/edit', $response);
    });

    // Instance update
    router()->get('instances/update/{id:\d+}/', function ($id){
      try {
        $update = \xman\instance\updateModx($id);
        echo template()->render('instances/update', $update);
      } catch (Exception $e){
        die($e->getMessage());
      }
    });

    // Instance clear log
    router()->get('instances/clearlog/{id:\d+}/', function($id) {
      xman\log\clear($id);

      xman\helper\redirect("/instances/{$id}/");
    });

    // Instance delete
    router()->get('instances/delete/{id:\d+}/', function ($id) {
        if (!xman\instance\delete($id)) {
            echo template()->render('instances/delete', ['error' => "An error occured whilst deleting instance with the ID: '{$id}'"]);
        } else {
            echo template()->render('instances/delete', ['success' => "Instance with the ID: '{$id}' deleted successfully"]);
        }
    });

    // User list
    router()->get('users/', function () {
        echo template()->render('users/list', ['users' => xman\user\getAll(), 'totalUserCount' => xman\user\getCount()]);

        return true;
    });

    // User create page
    router()->get('users/create/', function () {
        echo template()->render('users/create');
    });
    router()->post('users/create/', function () {
        $response = xman\user\create();
        echo template()->render('users/create', $response);
    });

    // User edit page
    router()->get('users/edit/{id:\d+}/', function ($id) {
        if (!xman\user\get($id)) {
            echo template()->render('users/edit', ['error' => "User with the ID: '{$id}' not found"]);
        } else {
            echo template()->render('users/edit', ['user' => xman\user\get($id)]);
        }
    });
    router()->post('users/edit/{id:\d+}/', function ($id) {
        $response = xman\user\update($id);
        echo template()->render('users/edit', $response);
    });

    // User delete
    router()->get('users/delete/{id:\d+}/', function ($id) {
        if (!xman\user\delete($id)) {
            echo template()->render('users/delete', ['error' => "An error occured whilst deleting user with the ID: '{$id}'"]);
        } else {
            echo template()->render('users/delete', ['success' => "User with the ID: '{$id}' deleted successfully"]);
        }
    });
});

/**
 * Dispatch the request and retrieve the response status code.
 */
list($statusCode) = router()->dispatch();

/**
 * A really basic handler function for 404 errors. Can be used anywhere
 * in any context. For example if a given route was matched but the
 * provided data was malicious.
 *
 * @param array $data Additional template data.
 */
function handle404(array $data = [])
{
    http_response_code(404);

    echo template()->render('404', $data);
}


if ($statusCode === 404) {
    handle404();
}
