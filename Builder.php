<?php

declare(strict_types=1);

/*
 * Studio 107 (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Sitemap;

use Exception;
use Mindy\Sitemap\Entity\SiteMapEntity;
use Mindy\Sitemap\Entity\SiteMapIndexEntity;

/**
 * Class Builder.
 */
class Builder
{
    /**
     * @var SitemapProviderInterface[]
     */
    protected $providers = [];
    /**
     * @var string
     */
    protected $hostWithScheme;
    /**
     * @var string
     */
    protected $path;

    /**
     * Builder constructor.
     *
     * @param string $hostWithScheme
     * @param string $path
     */
    public function __construct(string $hostWithScheme, string $path)
    {
        $this->hostWithScheme = $hostWithScheme;
        $this->path = $path;
    }

    /**
     * @param SitemapProviderInterface $provider
     */
    public function addProvider(SitemapProviderInterface $provider)
    {
        $this->providers[] = $provider;
    }

    /**
     * @param string $filePath
     * @param string $fileContent
     *
     * @throws Exception
     *
     * @return bool|int
     */
    public function saveFile($filePath, $fileContent)
    {
        if (false === is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0755);
        }

        return file_put_contents($filePath, $fileContent);
    }

    /**
     * @param $path
     * @param array $entities
     *
     * @return SiteMapEntity
     */
    public function saveSitemap($path, array $entities)
    {
        $sitemap = new SiteMapEntity();
        foreach ($entities as $location) {
            $sitemap->setLastmod(new \DateTime());
            $sitemap->addLocation($location);
        }

        $this->saveFile($path, $sitemap->getXml());

        return $sitemap;
    }

    /**
     * @return array
     */
    public function fetchEntities(): array
    {
        $entities = [];
        foreach ($this->providers as $provider) {
            foreach ($provider->build($this->hostWithScheme) as $location) {
                $entities[] = $location;
            }
        }

        return $entities;
    }

    /**
     * @param int $limit
     *
     * @return array
     */
    public function build(int $limit = 50000)
    {
        $sitemaps = [];

        $entities = $this->fetchEntities();

        if (count($entities) > $limit) {
            $sitemapIndex = new SiteMapIndexEntity();

            foreach (array_chunk($entities, $limit) as $i => $chunk) {
                $sitemap = $this->saveSitemap(sprintf('%s/sitemap-%s.xml', $this->path, $i), $chunk);
                $sitemaps[] = $loc = sprintf('%s/sitemap-%s.xml', rtrim($this->hostWithScheme, '/'), $i);
                $sitemap->setLoc($loc);

                $sitemapIndex->addSiteMap($sitemap);
            }

            $this->saveFile(sprintf('%s/sitemap.xml', rtrim($this->path, '/')), $sitemapIndex->getXml());
        } else {
            $this->saveSitemap(sprintf('%s/sitemap.xml', rtrim($this->path, '/')), $entities);
        }

        $sitemaps[] = sprintf('%s/sitemap.xml', rtrim($this->path, '/'));

        return $sitemaps;
    }
}
