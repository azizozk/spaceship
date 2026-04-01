<?php

namespace App\DataFixtures;

use App\Factory\PuduAccountFactory;
use App\Factory\PuduAccountLogFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $admin = UserFactory::createOne([
            'email' => 'admin@mail.com',
            'password' => 'password',
        ]);

        $puduAccount = PuduAccountFactory::createOne([
            'owners' => [$admin],
        ]);
        PuduAccountLogFactory::createMany(100, [
            'puduAccount' => $puduAccount,
            'body' => [
                'test-body-key' => 'test-body-value',
            ],
            'responseCode' => 200,
            'responseBody' => [
                'test-response-body-key' => 'test-response-body-value',
            ]
        ]);

        $manager->flush();
    }
}
