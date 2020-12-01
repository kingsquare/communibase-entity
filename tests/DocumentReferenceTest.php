<?php

declare(strict_types=1);

namespace Communibase\Tests;

use Communibase\CommunibaseId;
use Communibase\DocumentReference;
use Communibase\Exception\InvalidEntityException;
use PHPUnit\Framework\TestCase;

/**
 * @author Kingsquare (source@kingsquare.nl)
 * @copyright Copyright (c) Kingsquare BV (http://www.kingsquare.nl)
 */
class DocumentReferenceTest extends TestCase
{

    public function test_it_can_be_created(): void
    {
        DocumentReference::create('Person', CommunibaseId::create());
    }

    public function test_it_will_throw_exception_when_wrong_casing_is_passed(): void
    {
        $this->expectException(InvalidEntityException::class);
        DocumentReference::create('person', CommunibaseId::create());
    }
}
