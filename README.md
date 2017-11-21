Uri Hostname Parser
=======

This package contains a lightweight domain parser using the [Public Suffix List (PSL) ICANN section](http://publicsuffix.org/) based on the excellent work by [Jeremy Kendall](https://github.com/jeremykendall/php-domain-parser/).

**GOALS**

Detects:

- public suffix
- registrable domain
- sub domain

**NON GOALS**

Validates:

- the hostname
- cookie headers and value

**ALTERNATIVES**

If you need to validate

- your hostname please refers to the other [URI](https://github.com/thephpleague/uri-parser) [packages](https://github.com/thephpleague/uri-components)
- cookie headers please refers to [PHP Domain Parser](https://github.com/jeremykendall/php-domain-parser/).

System Requirements
-------

You need:

- **PHP >= 7.0** but the latest stable version of PHP is recommended
- the `mbstring` extension
- the `intl` extension
- the `curl` extension

Dependencies
-------

- [PSR-16](http://www.php-fig.org/psr/psr-16/)

Installation
--------

~~~
$ composer require league/uri-hostname-parser
~~~

Usage
--------

~~~php
use League\Uri\PublicSuffix\Cache;
use League\Uri\PublicSuffix\CurlHttpClient;
use League\Uri\PublicSuffix\ICANNSectionManager;

require 'vendor/autoload.php';

$manager = new ICANNSectionManager(new Cache(), new CurlHttpClient());
$icann_rules = $manager->getRules();
$domain = $icann_rules->resolve('www.bbc.co.uk');
$domain->getPublicSuffix();      //returns 'co.uk'
$domain->getRegistrableDomain(); //returns 'bbc.co.uk'
$domain->getSubDomain();         //returns 'www'
$domain->isValid();              //returns true
~~~

This package only uses the ICANN section of the PSL. For more information regarding the different PSL section, please refer to [Public Suffix List](http://publicsuffix.org/) website.

Documentation
-------

### ICANNSectionManager

~~~php
<?php

namespace League\Uri\PublicSuffix;

use Psr\SimpleCache\CacheInterface;

final class ICANNSectionManager
{
    const PSL_URL = 'https://raw.githubusercontent.com/publicsuffix/list/master/public_suffix_list.dat';
    public function __construct(CacheInterface $cache, HttpClient $http)
    public function getRules(string $source_url = self::PSL_URL): Rules
    public function refreshRules(string $source_url = self::PSL_URL): bool
}
~~~

The class obtains, writes, caches, and returns PHP representations of the PSL ICANN section rules.

#### ICANNSectionManager::__construct

To work as intended, the `ICANNSectionManager` constructor requires:

- a [PSR-16](http://www.php-fig.org/psr/psr-16/) Cache object to store the retrieved rules using a basic HTTP client.

- a `HttpClient` interface which exposes the `HttpClient::getContent` method which expects a string URL representation has its sole argument and returns the body from the given URL resource as a string.  
If an error occurs while retrieving such body a `HttpClientException` is thrown.

~~~php
<?php

namespace League\Uri\PublicSuffix;

interface HttpClient
{
    /**
     * Returns the content fetched from a given URL.
     *
     * @param string $url
     *
     * @throws HttpClientException If an errors occurs while fetching the content from a given URL
     *
     * @return string Retrieved content
     */
    public function getContent(string $url): string;
}
~~~

For advance usages you are free to use your own cache and/or http implementation. By default and out of the box, the package uses:

- a file cache PSR-16 implementation based on the excellent [FileCache](https://github.com/kodus/file-cache) which **caches the local copy for a maximum of 7 days**.
- a HTTP client based on the cURL extension.

#### ICANNSectionManager::getRules

~~~php
<?php

public function getRules(string $source_url = self::PSL_URL): Rules
~~~

This method returns a `League\Uri\PublicSuffix\Rules` object which is instantiated with the PSL ICANN Section rules.

`ICANNSectionManager::getRules` takes an optional `$source_url` argument to specify the PSL ICANN Section source URL. If no local cache exists for the submitted source URL, the method will first call `ICANNSectionManager::refreshRules` to update its local cache prior to instantiate and return the `Rules` object.  
On error, the method throws an `League\Uri\PublicSuffix\Exception`.

~~~php
use League\Uri\PublicSuffix\Cache;
use League\Uri\PublicSuffix\CurlHttpClient;
use League\Uri\PublicSuffix\ICANNSectionManager;

$manager = new ICANNSectionManager(new Cache(), new CurlHttpClient());
$icann_rules = $manager->getRules('https://publicsuffix.org/list/public_suffix_list.dat');
$icann_rules->resolve('www.bébé.be');
~~~

#### ICANNSectionManager::refreshRules

This method enables refreshing your local copy of the PSL ICANN Section stored with your [PSR-16](http://www.php-fig.org/psr/psr-16/) Cache and retrieved using the Http Client. By default the method will use the `ICANNSectionManager::PSL_URL` as the source URL but you are free to substitute this URL with your own.  
The method returns a boolean value which is `true` on success.

~~~php
use League\Uri\PublicSuffix\Cache;
use League\Uri\PublicSuffix\CurlHttpClient;
use League\Uri\PublicSuffix\ICANNSectionManager;

$manager = new ICANNSectionManager(new Cache(), new CurlHttpClient());
$manager->refreshRules('https://publicsuffix.org/list/public_suffix_list.dat');
~~~

#### Automatic Updates

It is important to always have an up to date PSL ICANN Section. In order to do so the library comes bundle with an auto-update mechanism using the script located in the `bin` directory

~~~bash
$ php ./bin/update-psl
~~~

This script assumes that your are using the Cache and HTTP Client implementations bundle with the package. If you prefer using your own implementation you should copy and then update its code to reflect your requirements.

Below I'm using the `ICANNSectionManager` with the Symfony Cache component. Of course you can add more setups depending on your usage.

*Be sure to adapt the following code to your own framework/situation. The following code is given as an example without warranty of it working out of the box.*

~~~php
use League\Uri\PublicSuffix\CurlHttpClient;
use League\Uri\PublicSuffix\ICANNSectionManager;
use Symfony\Component\Cache\Simple\PDOCache;

$symfonyCache = new PDOCache($pdo, 'league-psl-icann', 86400);
$manager = new ICANNSectionManager($symfonyCache, new CurlHttpClient());
$manager->refreshRules();
~~~

In any case, you should setup a cron to regularly update your local cache.

### Rules and Domain

~~~php
<?php

namespace League\Uri\PublicSuffix;

final class Rules
{
    public function __construct(array $rules)
    public function resolve(string $host): Domain
}
~~~

The `League\Uri\PublicSuffix\Rules` class resolves the submitted host against the parsed rules from the PSL. This is done using the `Rules::resolve` method which returns a `League\Uri\PublicSuffix\Domain` object.

~~~php
final class Domain
{
    public function __construct(?string $domain = null,?string $publicSuffix = null, bool $isValid = false);
    public function getDomain(): ?string
    public function getPublicSuffix(): ?string
    public function getRegistrableDomain(): ?string
    public function getSubDomain(); ?string
    public function isValid(): bool;
}
~~~

The `Domain` getters method always return normalized value according to the host status against the PSL rules.

*OF NOTE: `Domain::isValid` status depends on the PSL rules used. For the same hostname, depending on the rules used a hostname public suffix may be valid or not. Since this package only deals with the ICANN Section rules, the validity will be tested only against said rules.*

**WARNING: Some people use the PSL to determine what is a valid domain name and what isn't. This is dangerous, particularly in these days where new gTLDs are arriving at a rapid pace, if your software does not regularly receive PSL updates, because it will erroneously think new gTLDs are not valid. The DNS is the proper source for this innormalizeion. If you must use it for this purpose, please do not bake static copies of the PSL into your software with no update mechanism.**

Contributing
-------

Contributions are welcome and will be fully credited. Please see [CONTRIBUTING](.github/CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

Testing
-------

`Uri Hostname Parser` has a [PHPUnit](https://phpunit.de) test suite and a coding style compliance test suite using [PHP CS Fixer](http://cs.sensiolabs.org/). To run the tests, run the following command from the project folder.

~~~ bash
$ composer test
~~~

Security
-------

If you discover any security related issues, please email nyamsprod@gmail.com instead of using the issue tracker.

Credits
-------

- [Ignace Nyamagana Butera](https://github.com/nyamsprod)
- [Jeremy Kendall](https://github.com/jeremykendall/)
- [All Contributors](https://github.com/thephpleague/uri-hostname-parser/contributors)

License
-------

The MIT License (MIT). Please see [License File](LICENSE) for more information.

Attribution
-------

This work is based on a Fork of [PHP Domain Parser](https://github.com/jeremykendall/php-domain-parser/)