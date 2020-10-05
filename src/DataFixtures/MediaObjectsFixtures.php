<?php

namespace App\DataFixtures;

use App\Entity\MediaObject;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Faker\Provider\Image;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Create media objects fixtures.
 *
 * @author Ghaith Daly <daly.ghaith@gmail.com>
 */
class MediaObjectsFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $parameterBag;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder, ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function load(ObjectManager $manager)
    {
        $uploadDir = $this->parameterBag->get('kernel.project_dir') . '/public/media';

        // insert dummy media object not related to any user yet , useful for tests
        for ($i = 1; $i <= 10; $i++) {
            $mediaObject = (new MediaObject())->setFilePath(Image::image($uploadDir, 640, 480, 'people', false));

            $manager->persist($mediaObject);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
