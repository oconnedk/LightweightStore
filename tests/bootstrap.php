<?php
/**
 * Created by PhpStorm.
 * User: oconnedk
 * Date: 09/03/17
 * Time: 22:21
 */
if (!($loader = @include __DIR__ . '/../vendor/autoload.php')) {
    die(<<<'EOT'
You must set up the project dependencies, run the following commands:
wget http://getcomposer.org/composer.phar
php composer.phar install
EOT
    );
}