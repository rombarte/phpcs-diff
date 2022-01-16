# rombarte/phpcs-diff
## Run phpcs and compare result with last commit changes only.

* Library written in pure PHP (version 7.4)
* Library currently in **development** stage
* Require **phpcs** and **git**
* MIT license

### Pre-release testing:

Add repository to composer.json:

```json
  "repositories": [
    {
      "type": "git",
      "url": "https://github.com/rombarte/phpcs-diff.git"
    }
  ],
```

Install library:

```shell
COMPOSER_MEMORY_LIMIT=-1 /usr/bin/composer require rombarte/phpcs-diff:0.0.1-alpha
```

Run command (from your project):
```shell
./vendor/bin/phpcs-diff
```

### Running tests:

Clone this project and run tests:
```shell
./vendor/bin/phpunit
```
