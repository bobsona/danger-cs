<?php
/**
 * This file is part of the BannersBundle.
 *
 * (c) Nikolay Tumbalev <n.tumbalev@stenik.bg>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Stenik\BannersBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 *  Fixture for loading banners mapping data in database
 *
 * @package package
 * @author Nikolay Tumbalev <n.tumbalev@stenik.bg>
 */
class LoadBannersZonesData implements FixtureInterface
{
    /**
     * Array holding banners mapping information
     * @var array
     */
    private $bannerZones = array(
        'HOMEPAGE_1_HEADER' => 'Начална страница 1 горе - 300x250px',
        'HOMEPAGE_2_HEADER' => 'Начална страница 2 горе - 300x250px',
        'HOMEPAGE_3_HEADER' => 'Начална страница 3 горе - 300x250px',
        'HOMEPAGE_4_HEADER' => 'Начална страница 4 горе - 470x250px',
        'HOMEPAGE_5_HEADER' => 'Начална страница 5 горе - 470x250px',
        'HOMEPAGE_6_HEADER' => 'Начална страница 6 горе - 980x250px',
        'HOMEPAGE_MIDDLE' => 'Начална страница среден баннер - 160x600px',
        'HOMEPAGE_1_FOOTER' => 'Начална страница 1 долу - 300x250px',
        'HOMEPAGE_2_FOOTER' => 'Начална страница 2 долу - 300x250px',
        'HOMEPAGE_3_FOOTER' => 'Начална страница 3 долу - 300x250px',
        'HOMEPAGE_4_FOOTER' => 'Начална страница 4 долу - 470x250px',
        'HOMEPAGE_5_FOOTER' => 'Начална страница 5 долу - 470x250px',
        'HOMEPAGE_6_FOOTER' => 'Начална страница 6 долу - 980x250px',

        'NEWS_LISTING_1' => 'Новини листинг 1 - 300x250px',
        'NEWS_LISTING_2' => 'Новини листинг 2 - 300x250px',
        'NEWS_LISTING_3' => 'Новини листинг 3 - 300x250px',
        'NEWS_LISTING_4' => 'Новини листинг 4 - 470x250px',
        'NEWS_LISTING_5' => 'Новини листинг 5 - 470x250px',
        'NEWS_LISTING_6' => 'Новини листинг 6 - 980x250px',
        'NEWS_LISTING_MIDDLE' => 'Новини вертикален банер 160x600px',

        'NEWS_DETAILED_1' => 'Новини детайлна 1 - 300x250px',
        'NEWS_DETAILED_2' => 'Новини детайлна 2 - 300x250px',
        'NEWS_DETAILED_3' => 'Новини детайлна 3 - 300x250px',

        'PROMOTIONS_LISTING_1' => 'Промоции листинг 1 - 300x250px',
        'PROMOTIONS_LISTING_2' => 'Промоции листинг 2 - 300x250px',
        'PROMOTIONS_LISTING_3' => 'Промоции листинг 3 - 300x250px',
        'PROMOTIONS_LISTING_4' => 'Промоции листинг 4 - 470x250px',
        'PROMOTIONS_LISTING_5' => 'Промоции листинг 5 - 470x250px',
        'PROMOTIONS_LISTING_6' => 'Промоции листинг 6 - 980x250px',
        'PROMOTIONS_LISTING_MIDDLE' => 'Промоции вертикален банер 160x600px',

        'PROMOTIONS_DETAILED_1' => 'Промоции детайлна 1 - 300x250px',
        'PROMOTIONS_DETAILED_2' => 'Промоции детайлна 2 - 300x250px',
        'PROMOTIONS_DETAILED_3' => 'Промоции детайлна 3 - 300x250px',

        'PROGRAM_1'  => 'Програма 1 - 300x250px',
        'PROGRAM_2'  => 'Програма 2 - 300x250px',
        'PROGRAM_3'  => 'Програма 3 - 300x250px',
    );

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em)
    {
        $entityClass = '\Stenik\BannersBundle\Entity\BannerZone';
        $i = 1;
        foreach ($this->bannerZones as $key => $value) {
            $rsm = new ResultSetMapping();
            $rsm->addEntityResult($entityClass, 'bz');
            $rsm->addFieldResult('bz', 'id', 'id');
            $rsm->addFieldResult('bz', 'key', 'key');
            $rsm->addFieldResult('bz', 'value', 'value');

            $tableName = $em->getClassMetadata($entityClass)->getTableName();

            $query = $em->createNativeQuery(
                "SELECT bz.id, bz.key_code, bz.value
                FROM {$tableName} bz
                WHERE
                    bz.key_code = ?
                    OR bz.value = ?
                ",
                $rsm)
            ->setParameter(1, $key)
            ->setParameter(2, $value);

            if (count($query->getResult()) == 0) {
                $obj = new \Stenik\BannersBundle\Entity\BannerZone();
                $obj->setKey($key);
                $obj->setZones($i);
                $obj->setValue($value);
                $obj->setWidth(350);
                $obj->setHeight(350);
                $em->persist($obj);
                $i++;
            }
        }

        $em->flush();
    }
}
