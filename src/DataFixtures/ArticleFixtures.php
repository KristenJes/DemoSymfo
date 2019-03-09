<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use \Faker\Factory;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\User;

class ArticleFixtures extends Fixture
{

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $users = array();
        // Créer 15 Utilisateur
        for($i = 1; $i <= 15; $i++){
            $user = new User();
            $user->setEmail($faker->email())
                 ->setUsername($faker->userName())
                 ->setPassword($this->encoder->encodePassword($user, $faker->password()));

            $manager->persist($user);
            $users[] = $user;
        }

        // Créer 3 Catégories fakées
        for($i = 1; $i <= 3; $i++){
            $category = new Category();
            $category->setTitle($faker->sentence())
                     ->setDescription($faker->paragraph());

            $manager->persist($category);

            // Créer entre 4 et 8 articles
            for($j = 1; $j <= mt_rand(4, 8); $j++){
                $article = new Article();

                $content = '<p>' . join($faker->paragraphs(5), '</p><p>') . '</p>';
                $article->setTitle($faker->sentence())
                        ->setContent($content)
                        ->setImage($faker->imageUrl())
                        ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                        ->setCategory($category);
    
                $manager->persist($article);

                
                // Créer entre 4 et 10 commentaires
                for($k = 1; $k <= mt_rand(4, 10); $k++){
                    $comment = new Comment();

                    $content = '<p>' . join($faker->paragraphs(2), '</p><p>') . '</p>';
                    $days = (new \DateTime())->diff($article->getCreatedAt())->days;
                    $comment->setAuthor($users[array_rand($users)])
                            ->setContent($content)
                            ->setCreatedAt($faker->dateTimeBetween('-' . $days . ' days'))
                            ->setArticle($article);
        
                    $manager->persist($comment);
                    
                }
            }

        }

        $manager->flush();
    }
}
