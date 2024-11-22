<?php

declare(strict_types=1);

namespace App\Src\Services;

use App\Exception\InvalidOperationException;

/**
 * Class NavigationService
 */
final class NavigationService extends Service
{
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->ci->load->model('Navigation_model');
    }

    /**
     * Get level one navigation header
     *
     * @param int $companyId
     * @param int $userId
     * @param int $userType
     * @param int $companyType
     * @param int $isGroupUser
     * @return array
     */
    public function getNavigationHeaderLevelOne(
        int $companyId,
        int $userId,
        int $userType,
        int $companyType,
        int $isGroupUser
    ): array
    {
        $data = $this->ci->Navigation_model->getNavigationHeader(
            $companyId,
            $userId,
            $userType,
            $companyType,
            $isGroupUser
        );
        return array_filter($data, function($navigation) {
            return $navigation['levelNo'] == 0;
        });
    }

    /**
     * Get navigation header
     *
     * @param int $companyId
     * @param int $userId
     * @param int $userType
     * @param int $companyType
     * @param int $isGroupUser
     * @return array
     */
    public function getNavigationHeader(
        int $companyId,
        int $userId,
        int $userType,
        int $companyType,
        int $isGroupUser
    ): array
    {
        return $this->ci->Navigation_model->getNavigationHeader(
            $companyId,
            $userId,
            $userType,
            $companyType,
            $isGroupUser
        );
    }

    /**
     * Get navigation by id
     *
     * @param int $navigationId
     * @return array
     */
    public function getNavigationById(int $navigationId): array
    {
        return $this->ci->Navigation_model->getNavigationById($navigationId);
    }

    /**
     * Get all navigation
     *
     * @return array<int, mixed>
     */
    public function getAll(): array
    {
        return $this->ci->Navigation_model->getAll();
    }

    /**
     * Save navigation secondary description
     *
     * @param array<string, mixed> $data
     * @return void
     */
    public function saveNavigationSecondaryDescription(array $data): void
    {
        $result = $this->ci->Navigation_model->saveNavigationSecondaryDescription($data);
        if (!$result) {
            throw new InvalidOperationException('Update failed');
        }
    }
}