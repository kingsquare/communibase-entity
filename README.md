# Communibase Entities

[![Build Status](https://travis-ci.org/kingsquare/communibase-entity.svg?branch=master)](https://travis-ci.org/kingsquare/communibase-entity)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/kingsquare/communibase-entity/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/kingsquare/communibase-entity/?branch=master)

Some basic reusable CB Entities:

- CommunibaseId
- CommunibaseIdCollection
- DocumentReference
- Address
- Email
- PhoneNumber

PhoneNumber uses a setter which parses the the given string to countryCode, areaCode en subscriberNumber internally.

The DocumentReference object that allows for creating the document references within communibase in a more sane contract:
```php
// create a reference that points to an emailAddress of a Person on another entity (i.e. Membership)
$entityType = 'Person';
$entityId = CommunibaseId::fromValidString($someCommunibaseId);

// or this could be some entity which returns the communibaseId object on calling $entity->getId()

$resultingArray = DocumentReference::create($entityType, $entityId)->toEmail($email);
```

The resulting datastructure (`$resultArray`) can then be used as a property on some other entity which then points to the $email on the `Person`-document. (thus creating a reference)
