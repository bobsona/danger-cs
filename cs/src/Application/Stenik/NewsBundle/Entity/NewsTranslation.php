<?php 

namespace Application\Stenik\NewsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Stenik\NewsBundle\Entity\NewsTranslation as BaseNewsTranslation;

/**
 * Stenik\NewsBundle\Entity\NewsTranslation.php
 *
 * @ORM\Entity
 * @ORM\Table(name="news_i18n", uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_idx", columns={
 *     "locale", "object_id"
 *   })}
 * )
 */
class NewsTranslation extends BaseNewsTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="Application\Stenik\NewsBundle\Entity\News", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;
}