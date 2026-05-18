<?php
/**
 * Document Controller
 */

class DocumentController
{
    private DocumentModel $docModel;
    private PolicyModel   $policyModel;
    private UploadService $uploadService;

    public function __construct()
    {
        $this->docModel      = new DocumentModel();
        $this->policyModel   = new PolicyModel();
        $this->uploadService = new UploadService();
    }

    // POST /documents/store/{policyId}
    public function store(?string $policyId): void
    {
        AuthMiddleware::requireRole('admin', 'policy_officer');
        AuthMiddleware::verifyCsrf();

        $policy = $this->fetchPolicyOr404((int) $policyId);

        if (empty($_FILES['document']) || $_FILES['document']['error'] === UPLOAD_ERR_NO_FILE) {
            $_SESSION['flash_error'] = 'Please select a file to upload.';
            header('Location: ' . BASE_URL . '/policies/show/' . $policy['id']);
            exit;
        }

        try {
            $stored = $this->uploadService->store($_FILES['document']);

            $this->docModel->create([
                'policy_id'     => $policy['id'],
                'original_name' => $stored['original_name'],
                'stored_name'   => $stored['stored_name'],
                'file_path'     => $stored['file_path'],
                'mime_type'     => $stored['mime_type'],
                'file_size'     => $stored['file_size'],
                'uploaded_by'   => AuthMiddleware::currentUserId(),
            ]);

            $_SESSION['flash_success'] = 'Document uploaded successfully.';
        } catch (RuntimeException $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        header('Location: ' . BASE_URL . '/policies/show/' . $policy['id']);
        exit;
    }

    // POST /documents/destroy/{id}
    public function destroy(?string $id): void
    {
        AuthMiddleware::requireRole('admin', 'policy_officer');
        AuthMiddleware::verifyCsrf();

        $doc = $this->docModel->findById((int) $id);
        if (!$doc) {
            $_SESSION['flash_error'] = 'Document not found.';
            header('Location: ' . BASE_URL . '/policies/index');
            exit;
        }

        $policyId = $doc['policy_id'];
        $deleted  = $this->docModel->delete((int) $id);

        if ($deleted) {
            $this->uploadService->delete($deleted['stored_name']);
            $_SESSION['flash_success'] = 'Document deleted.';
        } else {
            $_SESSION['flash_error'] = 'Failed to delete document.';
        }

        header('Location: ' . BASE_URL . '/policies/show/' . $policyId);
        exit;
    }

    // GET /documents/download/{id}
    public function download(?string $id): void
    {
        AuthMiddleware::requireAuth();

        $doc = $this->docModel->findById((int) $id);
        if (!$doc || !file_exists($doc['file_path'])) {
            http_response_code(404);
            die('File not found.');
        }

        header('Content-Description: File Transfer');
        header('Content-Type: ' . $doc['mime_type']);
        header('Content-Disposition: attachment; filename="' . addslashes($doc['original_name']) . '"');
        header('Content-Length: ' . filesize($doc['file_path']));
        header('Pragma: public');
        readfile($doc['file_path']);
        exit;
    }

    private function fetchPolicyOr404(int $id): array
    {
        $policy = $this->policyModel->findById($id);
        if (!$policy) {
            http_response_code(404);
            include ROOT . '/views/partials/404.php';
            exit;
        }
        return $policy;
    }
}
