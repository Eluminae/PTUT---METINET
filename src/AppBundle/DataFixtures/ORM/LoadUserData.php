<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Models\Administrator;
use AppBundle\Models\Campaign;
use AppBundle\Models\CampaignAdministrator;
use AppBundle\Models\Candidate;
use AppBundle\Models\Juror;
use AppBundle\Models\Identity;
use AppBundle\Models\Mark;
use AppBundle\Models\Notation;
use AppBundle\Models\Realisation;
use AppBundle\Models\UtcDate;
use AppBundle\Services\UuidGenerator;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData implements FixtureInterface, ContainerAwareInterface
{
    /** @var ContainerInterface */
    private $container;

    /** @var UuidGenerator */
    private $uuidGenerator;

    /** @var Factory */
    private $fakerGenerator;

    /** @var array */
    private $password;

    const DT_ACTIVE = 'active';

    const DT_PAST = 'past';

    const DT_FUTUR = 'futur';

    /**
     * LoadUserData constructor.
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function __construct()
    {
        $this->fakerGenerator = Factory::create('fr_FR');
    }

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        // Construct -> Can not be put in constructor because of the container
        $this->uuidGenerator = $this->container->get('app.uuid.generator');
        $this->password = $this->container->get('app.services.user_registerer')->encodePasswordFromPlain('admin');

        // Create User Administrator
        for ($i = 0; $i < 10; $i++) {
            $administrator = $this->createUser('Administrator');

            $manager->persist($administrator);
        }

        // Create User CampaignAdmin
        for ($i = 0; $i < 10; $i++) {
            /** @var CampaignAdministrator $campaignAdministrator */
            $campaignAdministrator = $this->createUser('CampaignAdministrator');

            // Create Campaigns TO BE REVIEWED for the user with Jurors
            for ($y = 0, $yMax = random_int(1, 4); $y < $yMax; $y++) {
                $campaign = $this->createCampaign(
                    $campaignAdministrator->getIdentity(),
                    $this->createNotation(),
                    true,
                    self::DT_ACTIVE
                );

                for ($z = 0, $zMax = random_int(1, 5); $z < $zMax; $z++) {
                    $manager->persist($this->createJurorWithCampaign($campaign));
                }

                $manager->persist($campaign);
            }

            // Create Campaigns ACCEPTED for the user with Realisations
            for ($y = 0, $yMax = random_int(1, 4); $y < $yMax; $y++) {
                $campaign = $this->createCampaign(
                    $campaignAdministrator->getIdentity(),
                    $this->createNotation(),
                    true,
                    self::DT_ACTIVE
                );

                for ($z = 0, $zMax = random_int(1, 5); $z < $zMax; $z++) {
                    $manager->persist($this->createJurorWithCampaign($campaign));
                }

                $campaign->approveCampaign();

                for ($w = 0, $wMax = random_int(1, 15); $w < $wMax; $w++) {
                    $realisation = $this->createRealisation($campaign);
                    $manager->persist($realisation);
                }

                $manager->persist($campaign);
            }

            // Create Campaigns RESULTS_PUBLISHED for the user with Realisations and Marks
            for ($y = 0, $yMax = random_int(1, 4); $y < $yMax; $y++) {
                $campaign = $this->createCampaign(
                    $campaignAdministrator->getIdentity(),
                    $this->createNotation(),
                    true,
                    self::DT_PAST
                );

                $campaign->approveCampaign();



                for ($z = 0, $zMax = random_int(1, 5); $z < $zMax; $z++) {
                    $manager->persist($this->createJurorWithCampaign($campaign));
                }

                for ($w = 0, $wMax = random_int(1, 15); $w < $wMax; $w++) {
                    $realisation = $this->createRealisation($campaign);
                    // create mark
                    $this->createMark($campaign, $jur)
                    $manager->
                    $manager->persist($realisation);
                }


                // @todo CREATE MARKS !!!

                $campaign->publishResults();

                $manager->persist($campaign);
            }

            // Create Campaigns CLOSED for the user
            for ($y = 0, $yMax = random_int(1, 5); $y < $yMax; $y++) {
                $campaign = $this->createCampaign(
                    $campaignAdministrator->getIdentity(),
                    $this->createNotation(),
                    false,
                    self::DT_PAST
                );

                for ($z = 0, $zMax = random_int(1, 5); $z < $zMax; $z++) {
                    $manager->persist($this->createJurorWithCampaign($campaign));
                }

                $campaign->close();

                // @todo CREATE MARKS !!! -> No so important now if they are not display in any view

                $manager->persist($campaign);
            }

            $manager->persist($campaignAdministrator);
        }
        $manager->flush();
    }

    /**
     * @param string $type
     *
     * @return mixed
     */
    private function createUser(string $type)
    {
        $userObject = 'AppBundle\Models\\'.$type;

        return new $userObject(
            $this->uuidGenerator->generateUuid(),
            $this->createIdentity(),
            $this->password['encodedPassword'],
            $this->password['salt'],
            $this->getRoleFromType($type)
        );
    }

    private function createJurorWithCampaign(Campaign $campaign)
    {
        /** @var Juror $juror */
        $juror = $this->createUser('Juror');
        $juror->addCampaign($campaign);

        return $juror;
    }

    private function getRoleFromType(string $type)
    {
        $role = 'ROLE_USER';
        switch ($type) {
            case 'Administrator':
                $role = 'ROLE_ADMIN';
                break;
            case 'CampaignAdministrator':
                $role = 'ROLE_CAMPAIGN_ADMIN';
                break;
            case 'Juror':
                $role = 'ROLE_JUROR';
        }

        return $role;
    }

    private function createCandidate()
    {
        return new Candidate(
            $this->uuidGenerator->generateUuid(),
            $this->createIdentity()
        );
    }

    /**
     * @return Identity
     */
    private function createIdentity()
    {
        return new Identity(
            $this->uuidGenerator->generateUuid(),
            $this->fakerGenerator->lastName,
            $this->fakerGenerator->firstName,
            $this->fakerGenerator->email,
            $this->fakerGenerator->company
        );
    }

    /**
     * @param Identity $identity
     * @param Notation $notation
     * @param bool     $publicResults
     * @param string   $status
     *
     * @return Campaign
     */
    private function createCampaign(
        Identity $identity,
        Notation $notation,
        bool $publicResults,
        string $status
    ) {
        $dates = $this->getRandomDateTimePeriod($status);

        $campaign = new Campaign(
            $this->uuidGenerator->generateUuid(),
            new UtcDate(
                $this->uuidGenerator->generateUuid(),
                $dates['endDate']
            ),
            new UtcDate(
                $this->uuidGenerator->generateUuid(),
                $dates['beginningDate']
            ),
            $this->fakerGenerator->realText(50),
            $this->fakerGenerator->realText(250),
            $this->uuidGenerator->generateUuid(),
            $identity,
            $notation,
            $publicResults
        );

        return $campaign;
    }

    private function createRealisation(Campaign $campaign)
    {
        $candidates = [];
        for ($i = 0, $iMax = random_int(1, 5); $i < $iMax; $i++) {
            $candidates[] = $this->createCandidate();
        }

        return new Realisation(
            $this->uuidGenerator->generateUuid(),
            $this->getDateTimeBeforeClosing($campaign->getEndDate()->getDate()),
            $this->fakerGenerator->realText(50),
            $campaign,
            $candidates
        );
    }

    /**
     * @return Notation
     */
    private function createNotation()
    {
        $randomChoice = random_int(1, 2);
        if ($randomChoice === 1) {
            $type = Notation::RANKING;
        } else {
            $type = Notation::NUMBER;
        }

        return new Notation(
            $this->uuidGenerator->generateUuid(),
            $type,
            $this->fakerGenerator->numberBetween(5, 300)
        );
    }

    private function createMark(Campaign $campaign, Identity $jurorIdentity, Realisation $realisation) {
        $notation = $campaign->getNotation();

        if ($notation === Notation::RANKING) {
            $value = random_int(1, 100);
        } else {
            $value = random_int(0, $notation->getMarkTypeNumber());
        }

        return new Mark(
            $this->uuidGenerator->generateUuid(),
            $this->getDateTimeBeforeOfAfterClosing($campaign->getEndDate()->getDate(), false),
            $value,
            $jurorIdentity,
            $realisation
        );
    }

    private function getRandomDateTimePeriod(string $state)
    {
        $dates = [];
        $dateTime = new \DateTimeImmutable();
        $shortDateInterval = new \DateInterval('P'.$this->fakerGenerator->numberBetween(0, 300).'D');
        $longDateInterval = new \DateInterval('P'.$this->fakerGenerator->numberBetween(300, 1000).'D');

        switch ($state) {
            case self::DT_ACTIVE:
                $dates['beginningDate'] = $dateTime->sub($shortDateInterval);
                $dates['endDate'] = $dateTime->add($shortDateInterval);
                break;

            case self::DT_PAST:
                $dates['beginningDate'] = $dateTime->sub($longDateInterval);
                $dates['endDate'] = $dateTime->sub($shortDateInterval);
                break;

            case self::DT_FUTUR:
                $dates['beginningDate'] = $dateTime->add($shortDateInterval);
                $dates['endDate'] = $dateTime->add($longDateInterval);
                break;
        }

        return $dates;
    }

    private function getDateTimeBeforeOfAfterClosing(\DateTimeImmutable $dateTimeBeforeClosing, bool $before = true)
    {
        $oneDayInterval = new \DateInterval('P1D');

        if ($before) {
            return $dateTimeBeforeClosing->sub($oneDayInterval);
        }

        return $dateTimeBeforeClosing->add($oneDayInterval);
    }
}