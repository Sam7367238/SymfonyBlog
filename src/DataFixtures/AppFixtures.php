<?php

namespace App\DataFixtures;

use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $post = new Post();
        $post->setTitle('[1] Post Title');
        $post->setContent('Post content.');
        $manager->persist($post);

        $post = new Post();
        $post->setTitle('[2] Post Title');
        $post->setContent('Post content.');
        $manager->persist($post);

        $manager->flush();
    }
}
