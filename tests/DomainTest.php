<?php

declare(strict_types=1);

namespace League\Uri\PublicSuffix\Tests;

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
        ];
    }
}
