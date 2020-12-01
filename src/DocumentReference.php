<?php

declare(strict_types=1);

namespace Communibase;

use Communibase\Entity\Address;
use Communibase\Entity\Email;
use Communibase\Entity\PhoneNumber;
use Communibase\Exception\InvalidEntityException;

class DocumentReference
{

    /**
     * @var string
     */
    private $rootDocumentIdEntity;
    /**
     * @var CommunibaseId
     */
    private $rootDocumentId;

    private function __construct()
    {
    }

    /**
     * @throws InvalidEntityException
     */
    public static function create(string $entityType, CommunibaseId $id): DocumentReference
    {
        $instance = new self;

        if (ucfirst($entityType) !== $entityType) {
            throw new InvalidEntityException('Non well formatted EntityType given (' . $entityType . ')');
        }

        $instance->rootDocumentIdEntity = $entityType;
        $instance->rootDocumentId = $id;
        return $instance;
    }

    public function toEmail(Email $email): array
    {
        return $this->toArray('emailAddresses', $email->getId());
    }

    public function toAddress(Address $address): array
    {
        return $this->toArray('addresses', $address->getId());
    }

    public function toPhoneNumber(PhoneNumber $phoneNumber): array
    {
        return $this->toArray('phoneNumbers', $phoneNumber->getId());
    }

    public function toEntity(): array
    {
        return $this->toArray();
    }

    private function toArray(string $field = null, CommunibaseId $id = null): array
    {
        if (empty($this->rootDocumentIdEntity) || $this->rootDocumentId->isEmpty()) {
            return [];
        }

        $result = [
            'documentReference' => [
                'rootDocumentId' => $this->rootDocumentId->toString(),
                'rootDocumentEntityType' => $this->rootDocumentIdEntity,
            ],
        ];
        if ($field !== null && $id !== null && !$id->isEmpty()) {
            $result['path'] = [
                [
                    'field' => $field,
                    'objectId' => $id->toString(),
                ],
            ];
        }
        return $result;
    }

}
