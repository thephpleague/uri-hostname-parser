<?php

declare(strict_types=1);

namespace League\Uri\PublicSuffix\Tests;

use League\Uri\PublicSuffix\Cache;
use League\Uri\PublicSuffix\CurlHttpClient;
use League\Uri\PublicSuffix\Manager;
use League\Uri\PublicSuffix\ManagerException;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class ManagerTest extends TestCase
{
    /**
     * @var Manager
     */
    protected $manager;
    protected $cachePool;
    protected $cacheDir;
    protected $root;
    protected $sourceUrl = 'https://publicsuffix.org/list/public_suffix_list.dat';

    public function setUp()
    {
        $this->root = vfsStream::setup('pdp');
        vfsStream::create(['cache' => []], $this->root);
        $this->cacheDir = vfsStream::url('pdp/cache');
        $this->cachePool = new Cache($this->cacheDir);
        $this->manager = new Manager($this->cachePool, new CurlHttpClient());
    }

    public function tearDown()
    {
        $this->manager = null;
        $this->cachePool = null;
        $this->cacheDir = null;
        $this->root = null;
    }

    public function testRefreshRules()
    {
        $previous = $this->manager->getRules();
        $this->assertTrue($this->manager->refreshRules($this->sourceUrl));
        $this->assertEquals($previous, $this->manager->getRules());
    }

    public function testRebuildRulesFromRemoveSource()
    {
        $previous = $this->manager->getRules($this->sourceUrl);
        $this->cachePool->clear(); //delete all local cache
        $list = $this->manager->getRules($this->sourceUrl);
        $this->assertEquals($previous, $this->manager->getRules($this->sourceUrl));
    }

    public function testGetRulesThrowsException()
    {
        $this->expectException(ManagerException::class);
        $this->manager->getRules('https://google.com');
    }
}
