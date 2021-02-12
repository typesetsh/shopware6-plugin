<?php

declare(strict_types=1);

namespace Typesetsh\ShopwarePlatform;

use Shopware\Core\Checkout\Document\FileGenerator\FileTypes;
use Shopware\Core\Checkout\Document\FileGenerator\PdfGenerator as ShopwarePdfGenerator;
use Shopware\Core\Checkout\Document\GeneratedDocument;
use Typesetsh;

class PdfGenerator extends ShopwarePdfGenerator
{
    public const FILE_EXTENSION = 'pdf';
    public const FILE_CONTENT_TYPE = 'application/pdf';

    /** @var int */
    public $timeout = 10;

    /** @var int */
    public $downloadLimit = 1024 * 1024 * 10;

    /** @var Typesetsh\UriResolver */
    private $uriResolver;

    public function __construct(string $cacheDir, string $publicDir)
    {
        $schemes = [];
        $schemes['data'] = new Typesetsh\UriResolver\Data($cacheDir);
        $schemes['file'] = new Typesetsh\UriResolver\File([$publicDir]);
        $schemes['http'] =
        $schemes['https'] = new Typesetsh\UriResolver\Http($cacheDir, $this->timeout, $this->downloadLimit);

        $this->uriResolver = new Typesetsh\UriResolver($schemes);
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
        $html = $generatedDocument->getHtml();

        try {
            $pdf = Typesetsh\createPdf($html, $this->uriResolver);

            return $pdf->asString();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
