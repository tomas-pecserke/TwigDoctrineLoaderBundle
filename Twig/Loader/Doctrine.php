<?php

/*
 * (c) Tomas Pecserke <tomas@pecserke.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pecserke\Bundle\TwigDoctrineLoaderBundle\Twig\Loader;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ODM\MongoDB\DocumentRepository as MongoDBDocumentRepository;
use Doctrine\ORM\EntityRepository;
use Pecserke\Bundle\TwigDoctrineLoaderBundle\Model\Template;

/**
 * Loads template from database using Doctrine 2.
 *
 * @author Tomas Pecserke <tomas@pecserke.eu>
 */
class Doctrine implements \Twig_LoaderInterface, \Twig_ExistsLoaderInterface
{
    /**
     * @var ObjectRepository
     */
    protected $templateRepository;

    /**
     * @var string
     */
    protected $cacheKeyPrefix;

    /**
     * @param ObjectRepository $templateRepository
     * @param string $cacheKeyPrefix
     */
    public function __construct(ObjectRepository $templateRepository, $cacheKeyPrefix)
    {
        $this->templateRepository = $templateRepository;
        $this->cacheKeyPrefix = $cacheKeyPrefix;
    }

    /**
     * {@inheritdoc}
     */
    public function exists($name)
    {
        $repo = $this->templateRepository;

        if (class_exists('Doctrine\ORM\EntityRepository') && $repo instanceof EntityRepository) {
            $qb = $repo->createQueryBuilder('t');
            $qb
                ->select('count(t)')
                ->where($qb->expr()->eq('t.name', ':name'))
                ->setParameter('name', $name)
            ;

            return $qb->getQuery()->getSingleScalarResult() !== 0;
        }

        if (class_exists('Doctrine\ODM\MongoDB\DocumentRepository') && $repo instanceof MongoDBDocumentRepository) {
            $qb = $repo->createQueryBuilder()->field('name')->equals($name);

            return $qb->getQuery()->execute()->count() !== 0;
        }

        return $this->templateRepository->findOneBy(array('name' => $name)) !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function getSource($name)
    {
        return $this->getTemplate($name)->getSource();
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKey($name)
    {
        return $this->cacheKeyPrefix . $name;
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh($name, $time)
    {
        return $this->getTemplate($name)->getModifiedAt()->getTimestamp() <= $time;
    }

    /**
     * @param string $name
     * @return Template
     * @throws \Twig_Error_Loader
     */
    protected function getTemplate($name)
    {
        $template = $this->templateRepository->findOneBy(array('name' => $name));

        if ($template === null) {
            throw new \Twig_Error_Loader(sprintf('Unable to find template "%s".', $name));
        }

        return $template;
    }
}