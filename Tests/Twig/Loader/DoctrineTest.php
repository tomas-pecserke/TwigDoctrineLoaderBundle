<?php

/*
 * (c) Tomas Pecserke <tomas@pecserke.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pecserke\Bundle\TwigDoctrineLoaderBundle\Tests\Twig\Loader;

use Doctrine\ORM\Query\Expr;
use Pecserke\Bundle\TwigDoctrineLoaderBundle\Model\Template;
use Pecserke\Bundle\TwigDoctrineLoaderBundle\Twig\Loader\Doctrine;

class DoctrineTest extends \PHPUnit_Framework_TestCase
{
    public function testExists()
    {
        $repository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $loader = new Doctrine($repository, '');

        $template = new Template();
        $name = 'template_name';
        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(array('name' => $name))
            ->will($this->returnValue($template))
        ;

        $this->assertTrue($loader->exists($name));
    }

    public function testExistsFalse()
    {
        $repository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $loader = new Doctrine($repository, '');

        $name = 'template_name';
        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(array('name' => $name))
            ->will($this->returnValue(null))
        ;

        $this->assertFalse($loader->exists($name));
    }

    public function testExistsEntityManager()
    {
        $repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $loader = new Doctrine($repository, '');

        $count = 1;
        $query = $this->getMockBuilder('Doctrine\ORM\AbstractQuery')
            ->disableOriginalConstructor()
            ->setMethods(array('getSingleScalarResult'))
            ->getMockForAbstractClass()
        ;
        $query->expects($this->once())->method('getSingleScalarResult')->will($this->returnValue($count));
        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->setMethods(array('add', 'expr', 'getQuery', 'setParameter'))
            ->getMock()
        ;
        $qb->expects($this->any())->method('add')->will($this->returnSelf());
        $qb->expects($this->any())->method('expr')->will($this->returnValue(new Expr()));
        $qb->expects($this->once())->method('getQuery')->will($this->returnValue($query));
        $repository->expects($this->once())->method('createQueryBuilder')->will($this->returnValue($qb));

        $this->assertTrue($loader->exists('template_name'));
    }

    public function testExistsEntityManagerFalse()
    {
        $repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $loader = new Doctrine($repository, '');

        $count = 0;
        $query = $this->getMockBuilder('Doctrine\ORM\AbstractQuery')
            ->disableOriginalConstructor()
            ->setMethods(array('getSingleScalarResult'))
            ->getMockForAbstractClass()
        ;
        $query->expects($this->once())->method('getSingleScalarResult')->will($this->returnValue($count));
        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->setMethods(array('add', 'expr', 'getQuery', 'setParameter'))
            ->getMock()
        ;
        $qb->expects($this->any())->method('add')->will($this->returnSelf());
        $qb->expects($this->any())->method('expr')->will($this->returnValue(new Expr()));
        $qb->expects($this->once())->method('getQuery')->will($this->returnValue($query));
        $repository->expects($this->once())->method('createQueryBuilder')->will($this->returnValue($qb));

        $this->assertFalse($loader->exists('template_name'));
    }

    public function testExistsDocumentManager()
    {
        $repository = $this->getMockBuilder('Doctrine\ODM\MongoDB\DocumentRepository')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $loader = new Doctrine($repository, '');

        $count = 1;
        $query = $this->getMockBuilder('Doctrine\ODM\MongoDB\Query\Query')
            ->disableOriginalConstructor()
            ->setMethods(array('execute'))
            ->getMock()
        ;
        $query->expects($this->once())->method('execute')->will($this->returnValue(new \SplFixedArray($count)));
        $qb = $this->getMockBuilder('Doctrine\ODM\MongoDB\Query\Builder')
            ->disableOriginalConstructor()
            ->setMethods(array('field', 'equals', 'getQuery'))
            ->getMock()
        ;
        $qb->expects($this->any())->method('field')->will($this->returnSelf());
        $qb->expects($this->any())->method('equals')->will($this->returnSelf());
        $qb->expects($this->once())->method('getQuery')->will($this->returnValue($query));
        $repository->expects($this->once())->method('createQueryBuilder')->will($this->returnValue($qb));

        $this->assertTrue($loader->exists('template_name'));
    }

    public function testExistsDocumentManagerFalse()
    {
        $repository = $this->getMockBuilder('Doctrine\ODM\MongoDB\DocumentRepository')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $loader = new Doctrine($repository, '');

        $count = 0;
        $query = $this->getMockBuilder('Doctrine\ODM\MongoDB\Query\Query')
            ->disableOriginalConstructor()
            ->setMethods(array('execute'))
            ->getMock()
        ;
        $query->expects($this->once())->method('execute')->will($this->returnValue(new \SplFixedArray($count)));
        $qb = $this->getMockBuilder('Doctrine\ODM\MongoDB\Query\Builder')
            ->disableOriginalConstructor()
            ->setMethods(array('field', 'equals', 'getQuery'))
            ->getMock()
        ;
        $qb->expects($this->any())->method('field')->will($this->returnSelf());
        $qb->expects($this->any())->method('equals')->will($this->returnSelf());
        $qb->expects($this->once())->method('getQuery')->will($this->returnValue($query));
        $repository->expects($this->once())->method('createQueryBuilder')->will($this->returnValue($qb));

        $this->assertFalse($loader->exists('template_name'));
    }

    public function testGetSource()
    {
        $repository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $loader = new Doctrine($repository, '');

        $source = 'template source';
        $template = (new Template())->setSource($source);
        $name = 'template_name';
        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(array('name' => $name))
            ->will($this->returnValue($template))
        ;

        $this->assertEquals($source, $loader->getSource($name));
    }

    /**
     * @expectedException \Twig_Error_Loader
     * @expectedExceptionMessage Unable to find template "template_name".
     */
    public function testGetSourceNotExists()
    {
        $repository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $loader = new Doctrine($repository, '');

        $name = 'template_name';
        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(array('name' => $name))
            ->will($this->returnValue(null))
        ;

        $loader->getSource($name);
    }

    public function testGetCacheKey()
    {
        $cacheKey = 'cache_key.';
        $repository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $loader = new Doctrine($repository, $cacheKey);

        $name = 'template_name';
        $this->assertEquals($cacheKey . $name, $loader->getCacheKey($name));
    }

    public function testIsFresh()
    {
        $repository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $loader = new Doctrine($repository, '');

        $template = new Template();
        $template->onModified();
        $name = 'template_name';
        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(array('name' => $name))
            ->will($this->returnValue($template))
        ;

        $this->assertTrue($loader->isFresh($name, time()));
    }

    public function testIsFreshFalse()
    {
        $repository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $loader = new Doctrine($repository, '');

        $template = new Template();
        $template->onModified();
        $name = 'template_name';
        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(array('name' => $name))
            ->will($this->returnValue($template))
        ;

        $this->assertFalse($loader->isFresh($name, time() - 10));
    }

    /**
     * @expectedException \Twig_Error_Loader
     * @expectedExceptionMessage Unable to find template "template_name".
     */
    public function testIsFreshNotExists()
    {
        $repository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $loader = new Doctrine($repository, '');

        $name = 'template_name';
        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(array('name' => $name))
            ->will($this->returnValue(null))
        ;

        $loader->isFresh($name, time());
    }
}
