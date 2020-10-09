<?php

declare(strict_types=1);

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

    protected function __construct(array $emailAddressData = [])
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
        return new static();
    }

    /**
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
     * @return static
     */
    public static function fromEmailAddress(string $emailAddress)
    {
        return static::fromEmailAddressData([
            'emailAddress' => $emailAddress,
        ]);
    }

    public function getEmailAddress(): string
    {
        return (string)$this->dataBag->get('email.emailAddress');
    }

    public function setEmailAddress(string $emailAddress): void
    {
        $this->dataBag->set('email.emailAddress', $emailAddress);
    }

    public function getType(): string
    {
        return (string)$this->dataBag->get('email.type');
    }

    public function setType(string $type): void
    {
        $this->dataBag->set('email.type', $type);
    }

    public function getState(): ?array
    {
        if (empty($this->getEmailAddress())) {
            return null;
        }
        return $this->dataBag->getState('email');
    }
}
