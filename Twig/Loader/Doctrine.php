<?php
namespace Pecserke\Bundle\TwigDoctrineLoaderBundle\Twig\Loader;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Pecserke\Bundle\TwigDoctrineLoaderBundle\Model\Template;

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
        if (class_exists('Doctrine\ORM\EntityRepository') && $this->templateRepository instanceof EntityRepository) {
            /* @var QueryBuilder $qb */
            $qb = $this->templateRepository->createQueryBuilder('t');
            $qb
                ->select('count(t)')
                ->where($qb->expr()->eq('t.name', ':name'))
                ->setParameter('name', $name)
            ;

            return $qb->getQuery()->getSingleScalarResult() !== 0;
        }

        if (class_exists('Doctrine\ODM\MongoDB\DocumentRepository') && $this->templateRepository instanceof DocumentRepository) {
            /* @var Builder $qb */
            $qb = $this->templateRepository->createQueryBuilder()->field('name')->equals($name);

            return $qb->getQuery()->execute()->count() !== 0;
        }

        return $this->templateRepository->findOneByName($name) !== null;
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
        return $this->cacheKeyPrefix . $this->getTemplate($name)->getName();
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
        $template = $this->templateRepository->findOneByName($name);

        if ($template === null) {
            throw new \Twig_Error_Loader(sprintf('Unable to find template "%s".', $name));
        }

        return $template;
    }
}