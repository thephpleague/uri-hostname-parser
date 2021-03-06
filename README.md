Uri Hostname Parser
=======

# This package is EOL since 2018-02-16

**This repository was a temporary workaround for**

- **[PHP Domain Parser v3.0](https://github.com/jeremykendall/php-domain-parser/releases/tag/3.0.0)**
- **used by [League URI v5](https://github.com/thephpleague/uri/releases/tag/5.2.0).**

**You should instead use:**

- **[The latest League URI package](https://github.com/thephpleague/uri/releases).**
- **[The latest PHP Domain Parser](https://github.com/jeremykendall/php-domain-parser/releases).**

[![Build Status](https://img.shields.io/travis/thephpleague/uri-hostname-parser/master.svg?style=flat-square)](https://travis-ci.org/thephpleague/uri-hostname-parser)
[![Latest Version](https://img.shields.io/github/release/thephpleague/uri-hostname-parser.svg?style=flat-square)](https://github.com/thephpleague/uri-hostname-parser/releases)

This package contains a lightweight domain parser using the [Public Suffix List (PSL) ICANN section](http://publicsuffix.org/) based on the excellent work by [Jeremy Kendall](https://github.com/jeremykendall/php-domain-parser/).

**WARNING: Some people use the PSL to determine what is a valid domain name and what isn't. This is dangerous, particularly in these days where new gTLDs are arriving at a rapid pace, if your software does not regularly receive PSL updates, because it will erroneously think new gTLDs are not valid. The DNS is the proper source for this in normal condition. If you must use it for this purpose, please do not bake static copies of the PSL into your software with no update mechanism.**

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

Documentation
--------

Full documentation can be found at [uri.thephpleague.com](https://uri.thephpleague.com).

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
