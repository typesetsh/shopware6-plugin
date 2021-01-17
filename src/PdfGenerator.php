<?php

declare(strict_types=1);

namespace Typesetsh\ShopwarePlatform;

use Shopware\Core\Checkout\Document\FileGenerator\FileTypes;
use Shopware\Core\Checkout\Document\FileGenerator\PdfGenerator as ShopwarePdfGenerator;
use Shopware\Core\Checkout\Document\GeneratedDocument;

class PdfGenerator extends ShopwarePdfGenerator
{
    public const FILE_EXTENSION = 'pdf';
    public const FILE_CONTENT_TYPE = 'application/pdf';

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var string
     */
    private $publicDir;

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
        $dir = $this->cacheDir.'/cache/typesetsh';

        $cache = new \typesetsh\Resource\Cache($dir);
        $cache->downloadLimit = 1024 * 1024 * 10;
        $cache->timeout = 10;

        $resolveUri = function (string $uri) use ($cache): string {
            if (strpos($uri, '//') === 0) {
                $uri = 'https:'.$uri;
            }

            if (strpos($uri, 'http://') === 0 || strpos($uri, 'https://') === 0) {
                $parsedUrl = parse_url($uri);
                if ($parsedUrl && file_exists($this->publicDir.$parsedUrl['path'])) {
                    $uri = $this->publicDir.$parsedUrl['path'];
                } else {
                    $uri = $cache->fetch($uri);
                }
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
