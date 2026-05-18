<?php
/**
 * Dashboard Controller
 */

class DashboardController
{
    private PolicyModel $policyModel;

    public function __construct()
    {
        $this->policyModel = new PolicyModel();
    }

    // GET /dashboard
    public function index(): void
    {
        AuthMiddleware::requireAuth();

        // Refresh statuses on every dashboard load (lightweight; can be moved to cron)
        $this->policyModel->refreshStatuses();

        $counts        = $this->policyModel->getCounts();
        $nearingCount  = $this->policyModel->getNearingRenewalCount(RENEWAL_WARN_DAYS);
        $nearingList   = $this->policyModel->getNearingRenewal(RENEWAL_WARN_DAYS);

        $pageTitle = 'Dashboard';
        include ROOT . '/views/dashboard/index.php';
    }
}
