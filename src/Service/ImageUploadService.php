<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

final class ImageUploadService
{
    public const DIRECTORY_TEAM = 'team';
    public const DIRECTORY_FACILITY = 'facility';

    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
        private readonly SluggerInterface $slugger,
        private readonly Filesystem $filesystem,
    ) {
    }

    public function upload(UploadedFile $file, string $directory, string $nameHint = 'image'): string
    {
        $uploadDir = $this->getUploadDirectory($directory);
        $this->filesystem->mkdir($uploadDir);

        $extension = $file->guessExtension() ?? $file->getClientOriginalExtension() ?? 'bin';
        $safeName = (string) $this->slugger->slug($nameHint);
        $filename = sprintf('%s-%s.%s', $safeName, bin2hex(random_bytes(4)), $extension);

        $file->move($uploadDir, $filename);

        return sprintf('uploads/%s/%s', $directory, $filename);
    }

    public function deleteIfExists(?string $relativePath): void
    {
        if ($relativePath === null || $relativePath === '') {
            return;
        }

        if (!str_starts_with($relativePath, 'uploads/')) {
            return;
        }

        $absolutePath = $this->projectDir.'/public/'.$relativePath;

        if ($this->filesystem->exists($absolutePath)) {
            $this->filesystem->remove($absolutePath);
        }
    }

    private function getUploadDirectory(string $directory): string
    {
        return $this->projectDir.'/public/uploads/'.$directory;
    }
}
