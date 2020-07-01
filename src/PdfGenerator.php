<?php

declare(strict_types=1);

namespace typesetsh\Shopware6;

use Dompdf\Dompdf;
use Dompdf\Options;
use League\Flysystem\FilesystemInterface;
use Shopware\Core\Checkout\Document\FileGenerator\FileGeneratorInterface;
use Shopware\Core\Checkout\Document\FileGenerator\FileTypes;
use Shopware\Core\Checkout\Document\GeneratedDocument;

class PdfGenerator implements FileGeneratorInterface
{
    public const FILE_EXTENSION = 'pdf';
    public const FILE_CONTENT_TYPE = 'application/pdf';

    /**
     * @var string
     */
    private $shopwarePath;

    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * @var string
     */
    private $cacheDir;
    /**
     * @var string
     */
    private $publicDir;


    /**
     * @param string $cacheDir
     * @param string $publicDir
     */
    public function __construct(string $cacheDir, string $publicDir)
    {
        $this->cacheDir = $cacheDir;
        $this->publicDir = $publicDir;
    }

    public function supports(): string
    {
        return FileTypes::PDF;
    }

    public function getExtension(): string
    {
        return self::FILE_EXTENSION;
    }

    public function getContentType(): string
    {
        return self::FILE_CONTENT_TYPE;
    }

    public function generate(GeneratedDocument $generatedDocument): string
    {
        $dir = $this->cacheDir . '/cache/typesetsh';

        $cache = new \typesetsh\Resource\Cache($dir);
        $cache->downloadLimit = 1024 * 1024 * 10;
        $cache->timeout = 10;

        $resolveUri = function(string $uri) use ($cache): string {
            if (strpos($uri, '//') === 0) {
                $uri = 'https:'.$uri;
            }
            if (strpos($uri, 'http://') === 0 || strpos($uri, 'https://') === 0) {
                return $cache->fetch($uri);
            }

            $uri = realpath($uri);
            if (strpos($uri, $this->publicDir) === 0) {
                return $uri;
            }

            return '';
        };

        $html = $generatedDocument->getHtml();

        try {
            $pdf = \typesetsh\createPdf($html, $resolveUri);

            return $pdf->asString();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
