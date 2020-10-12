<?php

namespace App\DataFixtures;

use App\Entity\MediaObject;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\VerificationRequest;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Faker\Provider\Image;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Create user fixtures.
 *
 * @author Ghaith Daly <daly.ghaith@gmail.com>
 */
class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $userPasswordEncoder;

    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $parameterBag;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder, ParameterBagInterface $parameterBag)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->parameterBag = $parameterBag;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        if (!file_exists($uploadDir = $this->parameterBag->get('kernel.project_dir') . '/public/media')) {
            mkdir($uploadDir);
        }

        // insert dummy unverified users with verification request
        for ($i = 1; $i <= 5; $i++) {
            $user = new User();

            $user
                ->setFirstname($faker->firstName)
                ->setLastname($faker->lastName)
                ->setEmail(sprintf("user%d@mail.com", $i))
                ->setPassword($this->userPasswordEncoder->encodePassword($user, '12345678'));

            $mediaObject = (new MediaObject())->setFilePath(Image::image($uploadDir, 640, 480, 'people', false));

            $verificationRequest = new VerificationRequest();
            $verificationRequest
                ->setMessage("Hello am {$user->getFirstname()}.")
                ->setDate(new DateTimeImmutable())
                ->setImage($mediaObject)
                ->setUser($user);

            // decline user 5
            if (5 == $i) {
                $verificationRequest->setStatus(VerificationRequest::STATUS_VERIFICATION_DECLINED);
            }
            
            $manager->persist($user);
            $manager->persist($mediaObject);
            $manager->persist($verificationRequest);
        }

        $manager->flush();
        $manager->clear();

        // insert dummy verified bloggers + posts
        for ($i = 1; $i <= 5; $i++) {
            $blogger = new User();

            $blogger
                ->setFirstname($faker->firstName)
                ->setLastname($faker->lastName)
                ->setEmail(sprintf("blogger%d@mail.com", $i))
                ->setPassword($this->userPasswordEncoder->encodePassword($blogger, '12345678'))
                ->setRoles(['ROLE_BLOGGER']);

            $mediaObject = (new MediaObject())->setFilePath(Image::image($uploadDir, 640, 480, 'people', false));

            $verificationRequest = new VerificationRequest();
            $verificationRequest
                ->setMessage("Hello am {$blogger->getFirstname()}.")
                ->setImage($mediaObject)
                ->setUser($blogger)
                ->setStatus(VerificationRequest::STATUS_VERIFICATION_APPROVED);

            for ($j = 1; $j <= 5; $j++) {
                $post = (new Post())
                    ->setTitle($faker->sentence)
                    ->setContent($faker->text)
                    ->setUser($blogger);

                $manager->persist($post);
            }

            $manager->persist($blogger);
            $manager->persist($mediaObject);
            $manager->persist($verificationRequest);
        }

        // insert dummy unverified user with no verification request attached

        $user = new User();

        $user
            ->setFirstname($faker->firstName)
            ->setLastname($faker->lastName)
            ->setEmail("user-with-no-verification-request@mail.com")
            ->setPassword($this->userPasswordEncoder->encodePassword($user, '12345678'));

        $mediaObject = (new MediaObject())->setFilePath(Image::image($uploadDir, 640, 480, 'people', false));

        $manager->persist($user);
        $manager->persist($mediaObject);

        $manager->flush();
    }
}
