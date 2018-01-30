<?php

declare(strict_types=1);

namespace League\Uri\Tests;

use League\Uri\PublicSuffix\Domain;
use PHPUnit\Framework\TestCase;

class DomainTest extends TestCase
{
    /**
     * @dataProvider invalidRegistrableDomainProvider
     *
     * @param string $domain
     * @param string $publicSuffix
     */
    public function testRegistrableDomainIsNullWithFoundDomain(string $domain, $publicSuffix)
    {
        $domain = new Domain($domain, $publicSuffix, true);
        $this->assertNull($domain->getRegistrableDomain());
        $this->assertNull($domain->getSubDomain());
    }

    public function invalidRegistrableDomainProvider()
    {
        return [
            'domain and suffix are the same' => ['co.uk', 'co.uk'],
            'domain has no labels' => ['faketld', 'faketld'],
            'public suffix is null' => ['faketld', null],
            'public suffix is invalid' => ['_b%C3%A9bé.be-', 'be-'],
        ];
    }

    public function testDomainInternalPhpMethod()
    {
        $domain = new Domain('www.ulb.ac.be', 'ac.be', true);
        $generateDomain = eval('return '.var_export($domain, true).';');
        $this->assertInternalType('array', $domain->__debugInfo());
        $this->assertEquals($domain, $generateDomain);
    }
}
