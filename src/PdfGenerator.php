<?php

declare(strict_types=1);

namespace Typesetsh\ShopwarePlatform;

use Shopware\Core\Checkout\Document;
use Typesetsh\PdfBundle;

class PdfGenerator extends Document\FileGenerator\PdfGenerator
{
    public const FILE_EXTENSION = 'pdf';
    public const FILE_CONTENT_TYPE = 'application/pdf';

    /** @var PdfBundle\PdfGenerator */
    private $pdfGenerator;

    public function __construct(PdfBundle\PdfGenerator $pdfGenerator)
    {
        $this->pdfGenerator = $pdfGenerator;
    }

    public function supports(): string
    {
        return Document\FileGenerator\FileTypes::PDF;
    }

    public function getExtension(): string
    {
        return self::FILE_EXTENSION;
    }

    public function getContentType(): string
    {
        return self::FILE_CONTENT_TYPE;
    }

    public function generate(Document\GeneratedDocument $generatedDocument): string
    {
        $html = $generatedDocument->getHtml();
        $result = $this->pdfGenerator->render($html);

        return $result->asString();
    }
}
