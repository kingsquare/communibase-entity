<?php

declare(strict_types=1);

namespace Communibase\Tests\Entity;

use Communibase\Entity\Email;
use PHPUnit\Framework\TestCase;

/**
 * @author Kingsquare (source@kingsquare.nl)
 * @copyright Copyright (c) Kingsquare BV (http://www.kingsquare.nl)
 */
class EmailTest extends TestCase
{

    public function test_it_can_get_various_properties(): void
    {
        $data = [
            'emailAddress' => 'info@kingsquare.nl',
            'type' => 'private',
        ];
        $email = Email::fromEmailAddressData($data);
        self::assertSame($data['emailAddress'], $email->getEmailAddress());
        self::assertSame($data['type'], $email->getType());
    }

    public function test_with_only_emailAddress_string_as_input(): void
    {
        $input = 'info@kingsquare.nl';
        $email = Email::fromEmailAddress($input);
        self::assertSame($input, $email->getEmailAddress());
    }

    public function test_with_empty_input(): void
    {
        $email = Email::fromEmailAddressData();
        self::assertSame('', $email->getEmailAddress());
    }

    public function test_setters(): void
    {
        $email = Email::fromEmailAddressData();
        self::assertNull($email->getState());

        $email->setEmailAddress('info@kingsquare.nl');
        self::assertSame('info@kingsquare.nl', $email->getEmailAddress());

        $email->setType('work');
        self::assertSame('work', $email->getType());

        self::assertSame(
            [
                'type' => 'work',
                'emailAddress' => 'info@kingsquare.nl',
            ],
            $email->getState()
        );
    }

}
