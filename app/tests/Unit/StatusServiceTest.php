<?php

namespace App\Tests\Unit;

use App\Entity\StatusWave;
use App\Entity\User;
use App\Entity\Wave;
use App\Repository\StatusWaveRepository;
use App\Service\StatusService;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class StatusServiceTest extends TestCase
{
    private EntityManagerInterface&MockObject $entityManagerInterfaceMock;
    private StatusService $statusService;

    protected function setUp(): void
    {
        $this->entityManagerInterfaceMock = $this->createMock(EntityManagerInterface::class);
        $this->statusService = new StatusService($this->entityManagerInterfaceMock);
    }

    /**
     * @group unit
     */
    public function testSetValidStatus(): void
    {
        $entityMock = $this->createMock(Wave::class);

        $statusMock = $this->createMock(StatusWave::class);

        $repositoryMock = $this->createMock(StatusWaveRepository::class);
        $repositoryMock->expects($this->once())
            ->method("findOneBy")
            ->with(["const" => "TEST"])
            ->willReturn($statusMock);

        $this->entityManagerInterfaceMock->expects($this->once())
            ->method("getRepository")
            ->willReturn($repositoryMock);

        $this->statusService->setStatus($entityMock, StatusWave::class, "TEST");
    }

    /**
     * @group unit
     */
    public function testSetInvalidStatus(): void
    {
        $entityMock = $this->createMock(Wave::class);

        $statusRepositoryMock = $this->createMock(StatusWaveRepository::class);
        $statusRepositoryMock->expects($this->once())
            ->method("findOneBy")
            ->with(["const" => "TEST"])
            ->willReturn(null);

        $this->entityManagerInterfaceMock->expects($this->once())
            ->method("getRepository")
            ->willReturn($statusRepositoryMock);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("The requested status does not exist.");

        $this->statusService->setStatus($entityMock, StatusWave::class, "TEST");
    }

    /**
     * @group unit
     */
    public function testSetStatusOnEntityWithoutStatus(): void
    {
        $entityMock = $this->createMock(User::class);

        $statusMock = $this->createMock(StatusWave::class);

        $repositoryMock = $this->createMock(StatusWaveRepository::class);
        $repositoryMock->expects($this->once())
            ->method("findOneBy")
            ->with(["const" => "TEST"])
            ->willReturn($statusMock);

        $this->entityManagerInterfaceMock->expects($this->once())
            ->method("getRepository")
            ->willReturn($repositoryMock);

            $this->expectException(InvalidArgumentException::class);
            $this->expectExceptionMessage("The entity does not have a setStatus method.");

        $this->statusService->setStatus($entityMock, StatusWave::class, "TEST");
    }
}
