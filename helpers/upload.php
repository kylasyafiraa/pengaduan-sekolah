<?php

declare(strict_types=1);

function upload_image(array $file, string $subDir, array &$errors): ?string
{
    if (!isset($file['error']) || (int)$file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ((int)$file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Upload gambar gagal.';
        return null;
    }

    $maxBytes = 2 * 1024 * 1024;
    if (isset($file['size']) && (int)$file['size'] > $maxBytes) {
        $errors[] = 'Ukuran gambar maksimal 2MB.';
        return null;
    }

    $tmpName = (string)($file['tmp_name'] ?? '');
    if ($tmpName === '' || !is_uploaded_file($tmpName)) {
        $errors[] = 'File upload tidak valid.';
        return null;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($tmpName);

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    if (!isset($allowed[$mime])) {
        $errors[] = 'Tipe gambar harus JPG/PNG/WEBP.';
        return null;
    }

    $subDir = trim($subDir, '/');
    $baseDir = __DIR__ . '/../uploads';
    $targetDir = $baseDir . '/' . $subDir;

    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0775, true) && !is_dir($targetDir)) {
            $errors[] = 'Folder upload tidak bisa dibuat.';
            return null;
        }
    }

    $ext = $allowed[$mime];
    $random = bin2hex(random_bytes(12));
    $fileName = date('Ymd_His') . '_' . $random . '.' . $ext;
    $targetPath = $targetDir . '/' . $fileName;

    if (!move_uploaded_file($tmpName, $targetPath)) {
        $errors[] = 'Gagal menyimpan gambar.';
        return null;
    }

    return 'uploads/' . $subDir . '/' . $fileName;
}
