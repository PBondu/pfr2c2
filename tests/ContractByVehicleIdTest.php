<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Repository\ContractRepository;
use App\Repository\BillingRepository;
use App\Service\MongoDBService;
use App\Service\UserRequestProvider;
use App\Controller\SearchController;

class ContractByVehicleIdTest extends TestCase
{
    private $contractRepository;
    private $billingRepository;
    private $mongoDBService;
    private $userRequestProvider;

    protected function setUp(): void
    {
        $this->contractRepository = $this->createMock(ContractRepository::class);
        $this->billingRepository = $this->createMock(BillingRepository::class);
        $this->mongoDBService = $this->createMock(MongoDBService::class);
        $this->userRequestProvider = $this->createMock(UserRequestProvider::class);
    }

    public function testContractFoundByVehicleId(): void
    {
        $searchClass = new SearchController(
            $this->mongoDBService,
            $this->contractRepository,
            $this->billingRepository,
            $this->userRequestProvider
        );

        $vehicleId = 1;
        $contractFound = [(object)['vehicle_uid' => $vehicleId]];

        $this->contractRepository->method('findBy')
            ->with(['vehicle_uid' => $vehicleId])
            ->willReturn($contractFound);

        $functionResult = $searchClass->searchContractByVehicleId($vehicleId);

        $this->assertNotEmpty($functionResult, 'Le tableau est vide.');
        $this->assertSame($contractFound, $functionResult, 'Le contrat renvoyé par la fonction n\'est pas correct');
    }

    public function testContractNotFoundByVehicleId(): void
    {
        $searchClass = new SearchController(
            $this->mongoDBService,
            $this->contractRepository,
            $this->billingRepository,
            $this->userRequestProvider
        );

        $vehicleId = 9999999999;
        $contractNotFound = [];

        $this->contractRepository->method('findBy')
            ->with(['vehicle_uid' => $vehicleId])
            ->willReturn($contractNotFound);

        $functionResult = $searchClass->searchContractByVehicleId($vehicleId);

        $this->assertEmpty($functionResult, 'Le tableau n\'est pas vide.');
        $this->assertSame($contractNotFound, $functionResult, 'Le contrat renvoyé par la fonction n\'est pas correct');
    }
}