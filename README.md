Uri Hostname Parser
=======

This package contains a lightweight domain parser using the [Public Suffix List ICANN section](http://publicsuffix.org/) based on the excellent work by [Jeremy Kendall](https://github.com/jeremykendall/php-domain-parser/).

**GOALS**

- public suffix
- registrable domain
- sub domain

**NON GOALS**

- validate the hostname

If you need to validate your hostname please refers to the other [URI](https://github.com/thephpleague/uri-parser) [packages](https://github.com/thephpleague/uri-components)

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

```
$ composer require league/uri-hostname-parser
```

Documentation
--------

```php
use League\Uri\PublicSuffix\Cache;
use League\Uri\PublicSuffix\CurlHttpClient;
use League\Uri\PublicSuffix\Manager;

require 'vendor/autoload.php';

$manager = new Manager(new Cache(), new CurlHttpClient());
$rules = $manager->getRules();
$host = 'www.bbc.co.uk';
$domain = $rules->resolve($host);
//$domain is a League\Uri\PublicSuffix\Domain;

$domain->getPublicSuffix(); //returns 'co.uk'
$domain->getRegistrableDomain(); //returns 'bbc.co.cuk'
$domain->getSubDomain(); //returns 'www'
$domain->isValid(); //return true
```

This package only uses the ICANN section of the Public Suffix List. For more information regarding the different Public Suffix List section, please refer to [Public Suffix List](http://publicsuffix.org/) website.


Contributing
-------

Contributions are welcome and will be fully credited. Please see [CONTRIBUTING](.github/CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

Testing
-------

`Uri Hostname Parser` has a [PHPUnit](https://phpunit.de) test suite and a coding style compliance test suite using [PHP CS Fixer](http://cs.sensiolabs.org/). To run the tests, run the following command from the project folder.

``` bash
$ composer test
```

Security
-------

If you discover any security related issues, please email nyamsprod@gmail.com instead of using the issue tracker.

Credits
-------

- [ignace nyamagana butera](https://github.com/nyamsprod)
- [All Contributors](https://github.com/thephpleague/uri/contributors)

License
-------

The MIT License (MIT). Please see [License File](LICENSE) for more information.

Attribution
-------

The HTTP adapter interface and the cURL HTTP adapter were inspired by (er,
lifted from) Will Durand's excellent
[Geocoder](https://github.com/willdurand/Geocoder) project.  His MIT license and
copyright notice are below.

```
Copyright (c) 2011-2013 William Durand <william.durand1@gmail.com>

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is furnished
to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
```

Portions of the PublicSuffixListManager and the DomainParser are derivative
works of the PHP
[registered-domain-libs](https://github.com/usrflo/registered-domain-libs).
Those parts of this codebase are heavily commented, and I've included a copy of
the Apache Software Foundation License 2.0 in this project.