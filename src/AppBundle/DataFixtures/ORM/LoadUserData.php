<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Models\Administrator;
use AppBundle\Models\Campaign;
use AppBundle\Models\CampaignAdministrator;
use AppBundle\Models\Juror;
use AppBundle\Models\Identity;
use AppBundle\Models\Notation;
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

//            @todo NEED TO BE DONE !!!
//            const TO_BE_REVIEWED = 'to_be_reviewed';
//            const ACCEPTED = 'accepted';
//            const RESULTS_PUBLISHED = 'results_published';
//            const CLOSED = 'closed';

            // Create Campaigns TO BE REVIEWED for the user
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

                // for each campaign -> Assign a random number of juror

                $manager->persist($campaign);
            }

            // Create Campaigns ACCEPTED for the user
            for ($y = 0, $yMax = random_int(1, 4); $y < $yMax; $y++) {
                $campaign = $this->createCampaign(
                    $campaignAdministrator->getIdentity(),
                    $this->createNotation(),
                    true,
                    self::DT_FUTUR
                );

                for ($z = 0, $zMax = random_int(1, 5); $z < $zMax; $z++) {
                    $manager->persist($this->createJurorWithCampaign($campaign));
                }

                $campaign->approveCampaign();

                $manager->persist($campaign);
            }

            // Create Campaigns RESULTS_PUBLISHED for the user
            for ($y = 0, $yMax = random_int(1, 4); $y < $yMax; $y++) {
                $campaign = $this->createCampaign(
                    $campaignAdministrator->getIdentity(),
                    $this->createNotation(),
                    true,
                    self::DT_PAST
                );

                for ($z = 0, $zMax = random_int(1, 5); $z < $zMax; $z++) {
                    $manager->persist($this->createJurorWithCampaign($campaign));
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
                    true,
                    self::DT_PAST
                );

                for ($z = 0, $zMax = random_int(1, 5); $z < $zMax; $z++) {
                    /** @var Juror $juror */
                    $juror = $this->createUser('Juror');
                    $juror->addCampaign($campaign);
                }

                // @todo CREATE MARKS !!!

                $campaign->publishResults();

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
            $this->fakerGenerator->realText(100),
            $this->fakerGenerator->realText(250),
            $this->uuidGenerator->generateUuid(),
            $identity,
            $notation,
            $publicResults
        );

        return $campaign;
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
}