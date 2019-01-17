# Git hooks
##Installation
* ```composer require --dev msamec/git-hooks```
* create ```.php_cs.dist``` inside root of your project
##Documentation
This package will create git hooks that will trigger on git commit.
At the moment two tools are running [PHPStan](https://github.com/phpstan/phpstan) and [PHP CS FIXER](https://github.com/FriendsOfPHP/PHP-CS-Fixer).

#### PHPStan
Currently it will run using max level
#### PHP CS Fixer
It will run against configuration you have in your `.php_cs.dist` file
