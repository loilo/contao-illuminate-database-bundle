# Illuminate Database for Contao
[![Tests](https://badgen.net/github/checks/loilo/contao-illuminate-database-bundle/master)](https://github.com/loilo/contao-illuminate-database-bundle/actions)
[![Version on packagist.org](https://badgen.net/packagist/v/loilo/contao-illuminate-database-bundle)](https://packagist.org/packages/loilo/contao-illuminate-database-bundle)

Use Laravel's [Illuminate Database](https://laravel.com/docs/queries) abstraction in Contao with support for Contao models.

## Installation
```bash
composer require loilo/contao-illuminate-database-bundle
```

## Usage
### Getting Started
Get the `db()` helper function:

```php
use function Loilo\ContaoIlluminateDatabaseBundle\Database\db;
```

Calling the `db()` function creates a new [Laravel query builder](https://laravel.com/docs/queries) instance.

### Basic Queries
This is how we'd fetch ID and name of the earliest admin of the Contao installation:

```php
$row = db()
  ->select('id', 'name')
  ->from('user')
  ->where('admin', '1')
  ->orderBy('dateAdded')
  ->first();
```

> Note how the `tl_` prefix is automatically prepended to table names, so we actually read from `tl_user`.

The above is just a very basic example. To get an idea of what's possible with this API, consult the [Laravel docs](https://laravel.com/docs/queries).

### Fetching Models
In addition to Laravel's built-in methods, the query builder of this package exposes an additional `asModel()` method.

Using it inside a query builder chain will instruct the `get()`, `first()`, `find()` and `cursor()` methods to return Contao models instead of plain database records.

To explain this based on the example above:

```php
$user = db()
  ->from('user')
  ->asModel() // <- notice this line
  ->where('admin', '1')
  ->orderBy('dateAdded')
  ->first();

// $user will be an instance of \UserModel
```

### Customizing Connections
The `db()` function takes an optional argument which may override keys from the default connection configuration [passed to Laravel's connection manager](https://laravel.com/api/5.8/Illuminate/Database/Capsule/Manager.html#method_addConnection):

```php
// Set an empty prefix to use the "tl_user" table
db([ 'prefix' => '' ])->from('tl_user')->first();
```
