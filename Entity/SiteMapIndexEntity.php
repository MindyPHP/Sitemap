<?php

declare(strict_types=1);

/*
 * Studio 107 (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Sitemap\Entity;

use Mindy\Sitemap\Collection\SiteMapCollection;

/**
 * Class SiteMapIndexEntity.
 */
class SiteMapIndexEntity extends AbstractEntity
{
    /**
     * @var SiteMapCollection
     */
    protected $siteMapCollection;

    public function __construct()
    {
        $this->siteMapCollection = new SiteMapCollection();
    }

    /**
     * @param SiteMapEntity $siteMapEntity
     *
     * @return $this
     */
    public function addSiteMap(SiteMapEntity $siteMapEntity)
    {
        $this->siteMapCollection->attach($siteMapEntity);

        return $this;
    }

    /**
     * @return SiteMapCollection
     */
    public function getSiteMapCollection()
    {
        return $this->siteMapCollection;
    }

    /**
     * @return string
     */
    public function getXml()
    {
        $siteMapIndexText = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
        $siteMapIndexText .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($this->siteMapCollection as $siteMapEntity) {
            $siteMapIndexText .= '<sitemap>';
            $siteMapIndexText .= '<loc>'.$siteMapEntity->getLoc().'</loc>';
            $siteMapIndexText .= '<lastmod>'.$siteMapEntity->getLastmod()->format('c').'</lastmod>';
            $siteMapIndexText .= '</sitemap>';
        }
        $siteMapIndexText .= '</sitemapindex>';

        return $siteMapIndexText;
    }
}
