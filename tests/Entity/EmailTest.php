<?php

namespace Communibase\Tests\Entity;

use Communibase\Entity\Address;
use Communibase\Entity\Email;
use PHPUnit\Framework\TestCase;

/**
 * @author Kingsquare (source@kingsquare.nl)
 * @copyright Copyright (c) Kingsquare BV (http://www.kingsquare.nl)
 */
class EmailTest extends TestCase
{

    public function test_it_can_get_various_properties()
    {
        $data = [
            'emailAddress' => 'info@kingsquare.nl',
            'type' => 'private',
        ];
        $email = Email::fromEmailAddressData($data);
        $this->assertSame($data['emailAddress'], $email->getEmailAddress());
        $this->assertSame($data['type'], $email->getType());
    }

    public function test_with_only_emailAddress_string_as_input()
    {
        $input = 'info@kingsquare.nl';
        $email = Email::fromEmailAddress($input);
        $this->assertSame($input, $email->getEmailAddress());
    }

    public function test_with_empty_input()
    {
        $email = Email::fromEmailAddressData();
        $this->assertSame('', $email->getEmailAddress());
    }

    public function test_setters()
    {
        $email = Email::fromEmailAddressData();
        $this->assertNull($email->getState());

        $email->setEmailAddress('info@kingsquare.nl');
        $this->assertSame('info@kingsquare.nl', $email->getEmailAddress());

        $email->setType('work');
        $this->assertSame('work', $email->getType());

        $this->assertSame([
            'type' => 'work',
            'emailAddress' => 'info@kingsquare.nl',
        ], $email->getState());
    }

}
