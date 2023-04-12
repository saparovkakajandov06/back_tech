<?php

namespace App\Services\Admin;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PaymentMethodIconsService
{
    const DEFAULT_ICON = 'cardRF.svg';

    const TMP_DIR_NAME = 'tmp';
    const UPLOADS_DIR_NAME = 'uploads';
    const FILE_TYPE = 'svg';

    protected string $iconsBaseDir;

    protected string $iconsStorageDir;

    protected string $tmpDir;

    protected string $uploadsDir;

    public function __construct(string $iconsBaseDir)
    {
        $this->iconsBaseDir = $iconsBaseDir;
        $this->iconsStorageDir = 'public/' . $this->iconsBaseDir;

        $this->tmpDir = $this->iconsStorageDir . '/' . self::TMP_DIR_NAME;
        $this->uploadsDir = $this->iconsStorageDir . '/' . self::UPLOADS_DIR_NAME;
    }

    public function uploadIcon(UploadedFile $icon): string
    {
        $uuid = uniqid('pm-');

        $fileName = $uuid . '.' . self::FILE_TYPE;
        $filePath = $this->tmpDir . '/' . $fileName;

        if(!Storage::exists($this->tmpDir)) {
            Storage::makeDirectory($this->tmpDir, 0775);
        }

        //Rewrite file if exists
        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
        }

        $icon->storeAs($this->tmpDir, $fileName);

        return $uuid;
    }

    public function saveIcon(string $uuid, int $methodId): string
    {
        $tmpFilePath = $this->tmpDir . '/' . $uuid . '.' . self::FILE_TYPE;
        $uploadFilePath = $this->uploadsDir . '/' . $methodId . '.' . self::FILE_TYPE;

        if(!Storage::exists($this->uploadsDir)) {
            Storage::makeDirectory($this->uploadsDir, 0775);
        }

        //Rewrite file if exists
        if (Storage::exists($uploadFilePath)) {
            Storage::delete($uploadFilePath);
        }

        if(!Storage::move($tmpFilePath, $uploadFilePath)){
            throw new \RuntimeException('Tmp file move error');
        }

        return self::UPLOADS_DIR_NAME . '/' . $methodId . '.' . self::FILE_TYPE;
    }

    public function clearTmp(): void
    {
        $files = Storage::files($this->tmpDir);

        foreach ($files as $file){
            if(str_contains($file, '.svg')){
                Storage::delete($file);
            }
        }
    }

    public function hasTmpIcon(string $uuid): bool
    {
        return Storage::exists($this->tmpDir . '/' . $uuid . '.' . self::FILE_TYPE);
    }

    public function buildTmpIconPath($uuid): string
    {
        return self::TMP_DIR_NAME . '/' . $uuid . '.' . self::FILE_TYPE;
    }

    public function defaultIcon(): string
    {
        return self::DEFAULT_ICON;
    }
}
