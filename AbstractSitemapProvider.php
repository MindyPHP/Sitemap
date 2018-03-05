<?php

declare(strict_types=1);

/*
 * Studio 107 (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Sitemap;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class AbstractSitemapProvider.
 */
abstract class AbstractSitemapProvider implements SitemapProviderInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    protected $router;

    /**
     * PageProvider constructor.
     *
     * @param UrlGeneratorInterface $router
     */
    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param string $hostWithScheme
     * @param $route
     * @param array $parameters
     *
     * @return string
     */
    protected function generateLoc($hostWithScheme, $route, $parameters = [])
    {
        if (null === $this->router) {
            throw new \RuntimeException('UrlGenerator interface is missing');
        }

        list($scheme, $host) = explode('://', $hostWithScheme);

        $this->router
            ->getContext()
            ->setHost($host)
            ->setScheme($scheme);

        return $this->router->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
