<?php

namespace App\Services\Actions\Esign;

use App\Libraries\CustomTcpdf;

class PdfSignerService
{
    /**
     * Get the ByteRange from a prepared PDF.
     *
     * ByteRange format
     *
     * [
     *     0,
     *     length of first signed section,
     *     start of second signed section,
     *     length of second signed section
     * ]
     *
     * @param string $pdfContent
     * @return array
     */
    public function getByteRange(
        string $pdfContent
    ): array {

        $pdf = new CustomTcpdf(
            PDF_PAGE_ORIENTATION,
            PDF_UNIT,
            PDF_PAGE_FORMAT,
            true,
            'UTF-8',
            false
        );

        return $pdf->getPdfByteRange(
            $pdfContent
        );
    }


    /**
     * Get the signature placeholder size used by
     * CustomTcpdf.
     *
     * @return int
     */
    private function getSignatureMaxLength(): int
    {
        $pdf = new CustomTcpdf(
            PDF_PAGE_ORIENTATION,
            PDF_UNIT,
            PDF_PAGE_FORMAT,
            true,
            'UTF-8',
            false
        );

        return $pdf->getSignatureMaxLength();
    }


    /**
     * Get the actual data that needs to be hashed
     * for the PDF signature.
     *
     * @param string $pdfContent
     * @param array $byteRange
     * @return string
     */
    public function getSigningData(
        string $pdfContent,
        array $byteRange
    ): string {

        if (count($byteRange) !== 4) {
            throw new \Exception(
                'Invalid PDF ByteRange.'
            );
        }

        /*
         * Validate all ByteRange values.
         */
        foreach ($byteRange as $value) {
            if (!is_int($value) || $value < 0) {
                throw new \Exception(
                    'Invalid PDF ByteRange values.'
                );
            }
        }

        /*
         * First signed section.
         */
        $firstPart =
            substr(
                $pdfContent,
                $byteRange[0],
                $byteRange[1]
            );

        /*
         * Second signed section.
         */
        $secondPart =
            substr(
                $pdfContent,
                $byteRange[2],
                $byteRange[3]
            );

        if (
            $firstPart === false ||
            $secondPart === false
        ) {
            throw new \Exception(
                'Unable to extract PDF signing data.'
            );
        }

        return
            $firstPart .
            $secondPart;
    }

    public function prepareIncrementalSignature(
        string $sourcePdf,
        string $outputPdf,
        string $fieldName,
        int $page = 1,
        float $x = 15,
        float $y = 15,
        float $width = 180,
        float $height = 40,
        string $partyName = '',
        ?string $signDateTime = null
    ): array {
        $pdf = new CustomTcpdf(
            PDF_PAGE_ORIENTATION,
            PDF_UNIT,
            PDF_PAGE_FORMAT,
            true,
            'UTF-8',
            false
        );

        return $pdf->prepareIncrementalExternalSigning(
            $sourcePdf,
            $outputPdf,
            $fieldName,
            $page,
            $x,
            $y,
            $width,
            $height,
            $partyName,
            $signDateTime
        );
    }

    public function applyIncrementalSignature(
        string $preparedPdf,
        string $signedPdf,
        string $pkcs7
    ): array {
        $pdf = new CustomTcpdf(
            PDF_PAGE_ORIENTATION,
            PDF_UNIT,
            PDF_PAGE_FORMAT,
            true,
            'UTF-8',
            false
        );

        return $pdf->applyIncrementalExternalSignature(
            $preparedPdf,
            $signedPdf,
            $pkcs7
        );
    }



    /**
     * Apply the external eSign PKCS#7 signature
     * to the prepared PDF.
     *
     * IMPORTANT:
     *
     * $pdfByteRange is the COMPLETE ByteRange array:
     *
     * [
     *     0,
     *     length1,
     *     offset2,
     *     length2
     * ]
     *
     * CustomTcpdf::my_output(), however, expects
     * only the signature insertion offset.
     *
     * Therefore we pass $pdfByteRange[1].
     *
     * @param string $preparedPdf
     * @param string $signedPdf
     * @param string $certificate
     * @param string $pkcs7
     * @param array $pdfByteRange
     * @return string
     */


    public function applyExternalSignature(
        string $preparedPdf,
        string $signedPdf,
        string $certificate,
        string $pkcs7,
        array $pdfByteRange
    ): string {

        /*
         * ---------------------------------------------------------
         * Validate prepared PDF
         * ---------------------------------------------------------
         */

        if (!file_exists($preparedPdf)) {
            throw new \Exception(
                'Prepared PDF not found: ' .
                $preparedPdf
            );
        }

        $pdfContent =
            file_get_contents(
                $preparedPdf
            );

        if ($pdfContent === false) {
            throw new \Exception(
                'Unable to read prepared PDF.'
            );
        }


        /*
         * ---------------------------------------------------------
         * Validate ByteRange
         * ---------------------------------------------------------
         */

        if (count($pdfByteRange) !== 4) {
            throw new \Exception(
                'Invalid PDF ByteRange.'
            );
        }

        foreach ($pdfByteRange as $value) {
            if (!is_int($value) || $value < 0) {
                throw new \Exception(
                    'Invalid PDF ByteRange values.'
                );
            }
        }

        /*
         * Expected structure:
         *
         * [0, length1, offset2, length2]
         */
        if ($pdfByteRange[0] !== 0) {
            throw new \Exception(
                'Invalid PDF ByteRange first value.'
            );
        }

        /*
         * The signature insertion point used by
         * CustomTcpdf is ByteRange[1].
         */
        $signatureOffset =
            $pdfByteRange[1];


        if (
            $signatureOffset <= 0 ||
            $signatureOffset >= strlen($pdfContent)
        ) {
            throw new \Exception(
                'Invalid PDF signature byte range offset.'
            );
        }


        /*
         * ---------------------------------------------------------
         * Validate PKCS#7
         * ---------------------------------------------------------
         */

        if (empty($pkcs7)) {
            throw new \Exception(
                'PKCS#7 signature is empty.'
            );
        }


        /*
         * ---------------------------------------------------------
         * Validate certificate
         * ---------------------------------------------------------
         */

        if (empty($certificate)) {
            throw new \Exception(
                'Signing certificate is empty.'
            );
        }


        /*
         * ---------------------------------------------------------
         * Make sure this is still the prepared TCPDF
         * ---------------------------------------------------------
         */

        // if (
        //     strpos(
        //         $pdfContent,
        //         TCPDF_STATIC::$byterange_string
        //     ) === false
        // ) {

        //     /*
        //      * Depending on the preparation stage,
        //      * the ByteRange placeholder may already have
        //      * been replaced.
        //      *
        //      * In that case we still allow signing as long
        //      * as the /Contents placeholder exists.
        //      */
        // }


        /*
         * ---------------------------------------------------------
         * Make sure the /Contents placeholder exists.
         * ---------------------------------------------------------
         */

        if (
            strpos(
                $pdfContent,
                '/Contents<'
            ) === false
        ) {
            throw new \Exception(
                'PDF signature /Contents placeholder not found.'
            );
        }


        /*
         * ---------------------------------------------------------
         * Create CustomTcpdf instance.
         * ---------------------------------------------------------
         *
         * We are NOT generating a new PDF.
         * The existing prepared PDF is supplied to
         * my_output().
         */

        $pdf =
            new CustomTcpdf(
                PDF_PAGE_ORIENTATION,
                PDF_UNIT,
                PDF_PAGE_FORMAT,
                true,
                'UTF-8',
                false
            );


        /*
         * ---------------------------------------------------------
         * Apply external PKCS#7 signature.
         * ---------------------------------------------------------
         *
         * IMPORTANT:
         *
         * my_output() expects:
         *
         * $pdf_byte_range = integer
         *
         * NOT:
         *
         * [0, length1, offset2, length2]
         *
         * Therefore pass only ByteRange[1].
         */

        $pdf->my_output(
            $signedPdf,
            'F',
            $pdfContent,
            $certificate,
            $pkcs7,
            true,
            $signatureOffset
        );


        /*
         * ---------------------------------------------------------
         * Verify output
         * ---------------------------------------------------------
         */

        if (!file_exists($signedPdf)) {
            throw new \Exception(
                'Failed to create signed PDF.'
            );
        }

        if (filesize($signedPdf) <= 0) {
            throw new \Exception(
                'Signed PDF is empty.'
            );
        }


        /*
         * ---------------------------------------------------------
         * Verify resulting PDF
         * ---------------------------------------------------------
         */

        $signedContent =
            file_get_contents(
                $signedPdf
            );

        if ($signedContent === false) {
            throw new \Exception(
                'Unable to read signed PDF.'
            );
        }

        /*
         * The final PDF should no longer contain the
         * original TCPDF ByteRange placeholder.
         */
        // if (
        //     strpos(
        //         $signedContent,
        //         TCPDF_STATIC::$byterange_string
        //     ) !== false
        // ) {
        //     throw new \Exception(
        //         'Signed PDF still contains the TCPDF ByteRange placeholder.'
        //     );
        // }

        /*
         * Verify that a /Contents value exists.
         */
        if (
            strpos(
                $signedContent,
                '/Contents<'
            ) === false
        ) {
            throw new \Exception(
                'Signed PDF does not contain /Contents.'
            );
        }

        return $signedPdf;
    }
}