# Startup

```sh
composer serve
```

# Config
## Sensible
Remove `.example` extension of config files in `config/sensible` directory and fill in details.

## Others
In `config/`:
- Routes are registered in `routes.php`
- Storages are registered in `storage.php`


# Test
```sh
composer test

# Passing parameters to phpunit
composer test -- --filter RequestTest

composer test -- --testsuite Feature
composer test -- --testsuite Integration
composer test -- --testsuite Unit --filter Config
```

