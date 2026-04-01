<?php

namespace App\MessageHandler;

use App\Entity\RobotGroup;
use App\Entity\RobotInGroup;
use App\Message\SyncRobotMessage;
use App\Repository\PuduAccountRepository;
use App\Repository\RobotGroupRepository;
use App\Repository\RobotInGroupRepository;
use App\Service\Pudu\PuduApiClientFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SyncRobotMessageHandler
{
    public function __construct(
        private readonly PuduAccountRepository $puduAccountRepository,
        private readonly PuduApiClientFactory $puduApiClientFactory,
        private readonly RobotGroupRepository $robotGroupRepository,
        private readonly RobotInGroupRepository $robotInGroupRepository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(SyncRobotMessage $message): void
    {
        $puduAccount = $this->puduAccountRepository->find($message->puduAccountId);
        $api = $this->puduApiClientFactory->createFromAccount($puduAccount);
        $groups = $api->getBoundRobotGroups();

        foreach ($groups->groups as $groupDto) {
            $robotGroup = $this->robotGroupRepository->findOneBy([
                'puduAccount' => $puduAccount,
                'puduGroupId' => $groupDto->groupId,
            ]) ?? new RobotGroup();

            $robotGroup
                ->setPuduAccount($puduAccount)
                ->setPuduGroupId($groupDto->groupId)
                ->setGroupName($groupDto->groupName)
                ->setPuduShopId($groupDto->shopId)
                ->setPuduShopName($groupDto->shopName);

            $this->em->persist($robotGroup);

            $robots = $api->getRobotsInGroup($groupDto->groupId);

            foreach ($robots->robots as $robotDto) {
                $robotInGroup = $this->robotInGroupRepository->findOneBy([
                    'robotGroup' => $robotGroup,
                    'sn' => $robotDto->sn,
                ]) ?? new RobotInGroup();

                $robotInGroup
                    ->setRobotGroup($robotGroup)
                    ->setSn($robotDto->sn)
                    ->setMac($robotDto->mac)
                    ->setRobotName($robotDto->robotName)
                    ->setPuduShopId($robotDto->shopId)
                    ->setPuduShopName($robotDto->shopName);

                $this->em->persist($robotInGroup);
            }
        }

        $this->em->flush();
    }
}
