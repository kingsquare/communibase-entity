<?php

namespace Communibase\Entity;

use Communibase\DataBag;

/**
 * Communibase E-mail
 *
 * @author Kingsquare (source@kingsquare.nl)
 * @copyright Copyright (c) Kingsquare BV (http://www.kingsquare.nl)
 */
class Email
{
    /**
     * @var DataBag
     */
    protected $dataBag;

    /**
     * @param array $emailAddressData
     */
    protected function __construct(array $emailAddressData)
    {
        if (empty($emailAddressData['type'])) {
            $emailAddressData['type'] = 'private';
        }
        $this->dataBag = DataBag::create();
        $this->dataBag->addEntityData('email', $emailAddressData);
    }

    /**
     * @return static
     */
    public static function create()
    {
        return new static([]);
    }

    /**
     * @param array|null $emailAddressData
     *
     * @return static
     */
    public static function fromEmailAddressData(array $emailAddressData = null)
    {
        if ($emailAddressData === null) {
            $emailAddressData = [];
        }
        return new static($emailAddressData);
    }

    /**
     * @param string $emailAddress
     *
     * @return static
     */
    public static function fromEmailAddress($emailAddress)
    {
        return static::fromEmailAddressData([
            'emailAddress' => $emailAddress,
        ]);
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return (string)$this->dataBag->get('email.emailAddress');
    }

    /**
     * @param string $emailAddress
     */
    public function setEmailAddress($emailAddress)
    {
        $this->dataBag->set('email.emailAddress', (string)$emailAddress);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return (string)$this->dataBag->get('email.type');
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->dataBag->set('email.type', (string)$type);
    }

    /**
     * @return array|null
     */
    public function getState()
    {
        if (empty($this->getEmailAddress())) {
            return null;
        }
        return $this->dataBag->getState('email');
    }
}
