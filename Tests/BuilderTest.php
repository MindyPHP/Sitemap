<?php

declare(strict_types=1);

/*
 * Studio 107 (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Sitemap\Tests;

use Mindy\Sitemap\Builder;
use Mindy\Sitemap\Entity\LocationEntity;
use Mindy\Sitemap\SitemapProviderInterface;
use PHPUnit\Framework\TestCase;

class TestSitemapProvider implements SitemapProviderInterface
{
    /**
     * @param string $hostWithScheme
     *
     * @return \Generator
     */
    public function build($hostWithScheme)
    {
        yield (new LocationEntity())
            ->setLocation(sprintf('%s/hello-world.html', $hostWithScheme));

        yield (new LocationEntity())
            ->setLocation(sprintf('%s/about.html', $hostWithScheme));
    }
}

class BuilderTest extends TestCase
{
    public function tearDown()
    {
        $this->clean();
    }

    protected function clean()
    {
        foreach (glob(__DIR__.'/var/*') as $file) {
            unlink($file);
        }
        rmdir(__DIR__.'/var');
    }

    public function testBuild()
    {
        $builder = new Builder('https://example.com', __DIR__.'/var');
        $this->assertSame([__DIR__.'/var/sitemap.xml'], $builder->build());
        $this->assertSame(0, count($builder->fetchEntities()));

        $builder->addProvider(new TestSitemapProvider());
        $this->assertSame(2, count($builder->fetchEntities()));

        $this->assertSame([
            __DIR__.'/var/sitemap-0.xml',
            __DIR__.'/var/sitemap-1.xml',
            __DIR__.'/var/sitemap.xml',
        ], $builder->build(1));

        $this->clean();
        $this->assertFalse(is_dir(__DIR__.'/var'));
        $builder->build();
        $this->assertTrue(is_dir(__DIR__.'/var'));
    }
}
