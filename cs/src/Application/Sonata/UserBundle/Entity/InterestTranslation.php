<?php

/**
 * This file is part of the Extended SonataUserBundle.
 *
 * (c) Nikolay Tumbalev <n.tumbalev@stenik.bg>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\UserBundle\Entity;

/**
 *  Entity holding translations of user's interests
 *
 * @package Application\Sonata\UserBundle
 * @author Nikolay Tumbalev <n.tumbalev@stenik.bg>
 */
class InterestTranslation
{
    /**
     * @var integer $id
     */
    protected $id;

    /**
     * Association with Interest entity
     */
    protected $object;

    /**
     * Current system locale
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $title;

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the value of title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the value of title.
     *
     * @param string $title the title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Gets the value of locale.
     *
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Sets the value of locale.
     *
     * @param mixed $locale the locale
     *
     * @return self
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Gets the Association with Interest entity.
     *
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Sets the Association with Interest entity.
     *
     * @param mixed $object the object
     *
     * @return self
     */
    public function setObject($object)
    {
        $this->object = $object;

        return $this;
    }
}
