<?php

namespace Tests\AppBundle\CitizenProject;

use AppBundle\Address\NullableAddress;
use AppBundle\CitizenProject\CitizenProjectUpdateCommand;
use AppBundle\Entity\CitizenProjectCategory;
use AppBundle\Entity\CitizenProjectSkill;
use AppBundle\Entity\Committee;
use AppBundle\Entity\NullablePostAddress;
use AppBundle\Entity\CitizenProject;
use Doctrine\Common\Collections\Collection;
use libphonenumber\PhoneNumber;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class CitizenProjectCommandTest extends TestCase
{
    const CREATOR_UUID = '0214e826-0116-4caa-a635-3f6f51a86750';

    public function testCreateCitizenProjectCommandFromCitizenProject()
    {
        $name = 'Projet citoyen à Lyon 1er';
        $subtitle = 'Le projet citoyen à Lyon 1er';
        $uuid = CitizenProject::createUuid($name);
        $citizenProjectCategory = $this->createMock(CitizenProjectCategory::class);
        $committee = $this->createMock(Committee::class);
        $assistanceNeeded = false;
        $assistanceContent = null;
        $problemDescription = 'Problem description';
        $proposedSolution = 'Proposed solution';
        $requiredMeans = 'Required means';
        $skill = $this->createMock(CitizenProjectSkill::class);

        $citizenProject = new CitizenProject(
            $uuid,
            Uuid::fromString(self::CREATOR_UUID),
            $name,
            $subtitle,
            $citizenProjectCategory,
            [$committee],
            $assistanceNeeded,
            $assistanceContent,
            $problemDescription,
            $proposedSolution,
            $requiredMeans,
            (new PhoneNumber())->setCountryCode('FR')->setNationalNumber('0407080502'),
            NullablePostAddress::createFrenchAddress('2 Rue de la République', '69001-69381'),
            '69001-en-marche-lyon'
        );
        $citizenProject->setSkills([$skill]);

        $citizenProjectUpdateCommand = CitizenProjectUpdateCommand::createFromCitizenProject($citizenProject);

        $this->assertInstanceOf(CitizenProjectUpdateCommand::class, $citizenProjectUpdateCommand);
        $this->assertSame($uuid, $citizenProjectUpdateCommand->getCitizenProjectUuid());
        $this->assertSame($citizenProject, $citizenProjectUpdateCommand->getCitizenProject());
        $this->assertSame($name, $citizenProjectUpdateCommand->name);
        $this->assertSame($citizenProjectCategory, $citizenProjectUpdateCommand->getCategory());
        $this->assertCount(1, $citizenProjectUpdateCommand->getCommitteeSupports()->toArray());
        $this->assertSame($assistanceNeeded, $citizenProjectUpdateCommand->isAssistanceNeeded());
        $this->assertSame($assistanceContent, $citizenProjectUpdateCommand->getAssistanceContent());
        $this->assertSame($problemDescription, $citizenProjectUpdateCommand->getProblemDescription());
        $this->assertSame($proposedSolution, $citizenProjectUpdateCommand->getProposedSolution());
        $this->assertSame($requiredMeans, $citizenProjectUpdateCommand->getRequiredMeans());
        $this->assertInstanceOf(Collection::class, $citizenProject->getSkills());
        $this->assertCount(1, $citizenProjectUpdateCommand->getSkills());
        $this->assertInstanceOf(NullableAddress::class, $citizenProjectUpdateCommand->getAddress());
    }
}
