<?php
/**
 * This file is part of the ApplicationSonataUserBundle.
 *
 * (c) Nikolay Tumbalev <n.tumbalev@stenik.bg>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\UserBundle\Entity;

use Sonata\UserBundle\Entity\BaseUser as BaseUser;
use Doctrine\Common\Collections\ArrayCollection;
/**
 *  Entity holding translations of user's interests
 *
 * @package Application\Sonata\UserBundle
 * @author Nikolay Tumbalev <n.tumbalev@stenik.bg>
 */
class User extends BaseUser
{
    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $address;

    /**
     * @var string
     */
    protected $signedForNewsletter;


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
     * Gets the value of cinema.
     *
     * @return string
     */
    public function getCinema()
    {
        return $this->cinema;
    }

    /**
     * Sets the value of cinema.
     *
     * @param string $cinema the cinema
     *
     * @return self
     */
    public function setCinema($cinema)
    {
        $this->cinema = $cinema;

        return $this;
    }

    /**
     * Gets the value of city.
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Sets the value of city.
     *
     * @param string $city the city
     *
     * @return self
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Gets the value of address.
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Sets the value of address.
     *
     * @param string $address the address
     *
     * @return self
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }
    
    /**
     * Gets the value of signedForNewsletter.
     *
     * @return string
     */
    public function getSignedForNewsletter()
    {
        return $this->signedForNewsletter;
    }

    /**
     * Sets the value of signedForNewsletter.
     *
     * @param string $signedForNewsletter the signed for newsletter
     *
     * @return self
     */
    protected function setSignedForNewsletter($signedForNewsletter)
    {
        $this->signedForNewsletter = $signedForNewsletter;

        return $this;
    }
}
