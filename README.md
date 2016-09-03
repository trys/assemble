# assemble
Event app

Create `app/config.php` with the following:

```php
<?php

const DEFAULT_URL = 'xxxxxx';
const DEFAULT_TOKEN = 'xxxxxx';
const DEFAULT_PATH = '/';

?>
```

Run `php composer.phar install`, `npm install`, then `gulp serve`.

This assumes you have PHP 5.4+ installed. The PHP gulp task references the PHP location:

```javascript
{
	bin: '/usr/bin/php',
	ini: '/etc/php5/cli/php.ini',
}
```