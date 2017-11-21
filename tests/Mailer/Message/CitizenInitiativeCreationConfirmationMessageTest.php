<?php

namespace Tests\AppBundle\Mailer\Message;

use AppBundle\CitizenInitiative\CitizenInitiativeCreatedEvent;
use AppBundle\Mailer\Message\CitizenInitiativeCreationConfirmationMessage;
use AppBundle\Mailer\Message\Message;
use AppBundle\Mailer\Message\MessageRecipient;

class CitizenInitiativeCreationConfirmationMessageTest extends AbstractEventMessageTest
{
    public function testCreate()
    {
        $adherent = $this->createAdherentMock('em@example.com', 'Émmanuel', 'Macron');
        $initiative = $this->createCitizenInitiativeMock('En Marche Lyon', '2017-02-01 15:30:00', '15 allées Paul Bocuse', '69006-69386', 'en-marche-lyon');

        $event = $this->createMock(CitizenInitiativeCreatedEvent::class);
        $event->expects(static::any())->method('getAuthor')->willReturn($adherent);
        $event->expects(static::any())->method('getCitizenInitiative')->willReturn($initiative);

        $message = CitizenInitiativeCreationConfirmationMessage::create($event);

        $this->assertInstanceOf(CitizenInitiativeCreationConfirmationMessage::class, $message);
        $this->assertInstanceOf(Message::class, $message);
        $this->assertCount(1, $message->getVars());
        $this->assertSame(
            [
                'IC_name' => 'En Marche Lyon',
            ],
            $message->getVars()
        );
        $this->assertCount(1, $message->getRecipients());

        $recipient = $message->getRecipient(0);

        $this->assertInstanceOf(MessageRecipient::class, $recipient);
        $this->assertSame('em@example.com', $recipient->getEmailAddress());
        $this->assertSame('Émmanuel', $recipient->getFullName());
        $this->assertSame(
            [
                'IC_name' => 'En Marche Lyon',
                'prenom' => 'Émmanuel',
            ],
            $recipient->getVars()
        );
    }
}
