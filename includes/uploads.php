<?php

const FAM_MAX_UPLOAD_BYTES = 5 * 1024 * 1024; // 5 MB

const FAM_UPLOAD_MIME_MAP = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
];

function fam_upload_error_message(int $code): string
{
    return match ($code) {
        UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Image is too large.',
        UPLOAD_ERR_PARTIAL => 'Image upload was interrupted — please try again.',
        UPLOAD_ERR_NO_TMP_DIR, UPLOAD_ERR_CANT_WRITE, UPLOAD_ERR_EXTENSION => 'Could not save the uploaded image.',
        default => 'Image upload failed.',
    };
}

/**
 * @return array{path: ?string, error: ?string}
 */
function fam_handle_image_upload(string $fieldName): array
{
    $file = $_FILES[$fieldName] ?? null;

    if ($file === null || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['path' => null, 'error' => null];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['path' => null, 'error' => fam_upload_error_message($file['error'])];
    }

    if ($file['size'] <= 0 || $file['size'] > FAM_MAX_UPLOAD_BYTES) {
        return ['path' => null, 'error' => 'Image must be smaller than 5 MB.'];
    }

    if (!is_uploaded_file($file['tmp_name'])) {
        return ['path' => null, 'error' => 'Image upload failed.'];
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $ext = FAM_UPLOAD_MIME_MAP[$mime] ?? null;
    if ($ext === null) {
        return ['path' => null, 'error' => 'Only JPEG, PNG, or WebP images are allowed.'];
    }

    $src = match ($ext) {
        'jpg' => @imagecreatefromjpeg($file['tmp_name']),
        'png' => @imagecreatefrompng($file['tmp_name']),
        'webp' => @imagecreatefromwebp($file['tmp_name']),
    };
    if ($src === false) {
        return ['path' => null, 'error' => 'The uploaded file is not a valid image.'];
    }

    if ($ext === 'png' || $ext === 'webp') {
        imagealphablending($src, false);
        imagesavealpha($src, true);
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
    $dest = __DIR__ . '/../uploads/' . $filename;

    $written = match ($ext) {
        'jpg' => imagejpeg($src, $dest, 85),
        'png' => imagepng($src, $dest, 6),
        'webp' => imagewebp($src, $dest, 85),
    };
    imagedestroy($src);

    if (!$written) {
        return ['path' => null, 'error' => 'Could not save the uploaded image.'];
    }

    return ['path' => 'uploads/' . $filename, 'error' => null];
}

function fam_cleanup_old_upload(?string $old, ?string $new): void
{
    if ($old === null || $old === $new) {
        return;
    }
    if (!preg_match('#^uploads/[a-f0-9]{32}\.(jpg|png|webp)$#', $old)) {
        return;
    }
    @unlink(__DIR__ . '/../' . $old);
}
