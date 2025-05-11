<?php

namespace DreamTeam\Base\Listeners;

use DreamTeam\AdminUser\Services\Interfaces\AdminUserServiceInterface;

class RefreshCountAdminMenuListener
{
    private AdminUserServiceInterface $adminUserService;

    /**
     * Create the event listener.
     */
    public function __construct(AdminUserServiceInterface $adminUserService)
    {
        $this->adminUserService = $adminUserService;
    }

    /**
     * Handle the event.
     */
    public function handle(): void
    {
        $cache = cache();
        $adminUsers = $this->adminUserService->getMultipleWithFromConditions([], [], 'id', 'ASC');
        if (setting('cache_admin_menu_enable', true)) {
            foreach ($adminUsers as $user) {
                $cacheKey = md5('cache-dashboard-menu-' . $user->id);
                $cache->forget($cacheKey);
            }
        }
    }
}
