<?php
/**
 * Upload Service
 * Handles secure file validation, storage, and deletion.
 */

class UploadService
{
    private string $uploadDir;

    public function __construct()
    {
        $this->uploadDir = UPLOAD_DIR;

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    /**
     * Process and store an uploaded file.
     *
     * @param  array  $file  $_FILES element
     * @return array  ['stored_name', 'file_path', 'mime_type', 'file_size', 'original_name']
     * @throws RuntimeException on validation failure
     */
    public function store(array $file): array
    {
        // ── Validate upload error ─────────────────────────────
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException($this->uploadErrorMessage($file['error']));
        }

        // ── Validate file size ────────────────────────────────
        if ($file['size'] > MAX_FILE_SIZE) {
            $max = MAX_FILE_SIZE / 1024 / 1024;
            throw new RuntimeException("File exceeds the maximum allowed size of {$max} MB.");
        }

        // ── Validate extension ────────────────────────────────
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ALLOWED_EXTENSIONS, true)) {
            throw new RuntimeException('File type not allowed. Accepted: PDF, JPG, PNG.');
        }

        // ── Validate MIME via finfo (server-side, not client header) ──
        $finfo    = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if (!in_array($mimeType, ALLOWED_MIME_TYPES, true)) {
            throw new RuntimeException('File MIME type is not permitted.');
        }

        // ── Generate unique stored name ───────────────────────
        $storedName = bin2hex(random_bytes(16)) . '.' . $ext;
        $filePath   = $this->uploadDir . $storedName;

        // ── Move file ─────────────────────────────────────────
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            throw new RuntimeException('Failed to save the uploaded file.');
        }

        return [
            'original_name' => $file['name'],
            'stored_name'   => $storedName,
            'file_path'     => $filePath,
            'mime_type'     => $mimeType,
            'file_size'     => $file['size'],
        ];
    }

    /**
     * Delete a stored file from disk.
     */
    public function delete(string $storedName): bool
    {
        $path = $this->uploadDir . $storedName;
        if (file_exists($path)) {
            return unlink($path);
        }
        return false;
    }

    private function uploadErrorMessage(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'The file is too large.',
            UPLOAD_ERR_PARTIAL  => 'The file was only partially uploaded.',
            UPLOAD_ERR_NO_FILE  => 'No file was uploaded.',
            default             => 'An unknown upload error occurred.',
        };
    }
}
