<?php

namespace App\Libraries;

use TCPDF;
use TCPDF_STATIC;

class CustomTcpdf extends TCPDF
{
    /**
     * Stores the PDF byte range offset used during eSign.
     *
     * @var int|null
     */
    public $pdf_byte_range = null;

    /**
     * Enable TCPDF signing mode.
     *
     * This is the NGDRS/CakePHP-compatible method used to make TCPDF
     * create the /ByteRange and /Contents placeholders when the PDF
     * is closed.
     */
    public function my_set_sign(
        $signing_cert = '',
        $private_key = '',
        $private_key_password = '',
        $extracerts = '',
        $cert_type = 2,
        $info = array(),
        $approval = ''
    ) {
        $this->signature_max_length = 32768;
        $this->sign = true;

        ++$this->n;
        $this->sig_obj_id = $this->n;

        ++$this->n;

        $this->signature_data = array();

        $this->signature_data['signcert'] = $signing_cert;
        $this->signature_data['privkey'] = $private_key;
        $this->signature_data['password'] = $private_key_password;
        $this->signature_data['extracerts'] = $extracerts;
        $this->signature_data['cert_type'] = $cert_type;
        $this->signature_data['info'] = $info;
        $this->signature_data['approval'] = $approval;
    }

    /**
     * Return TCPDF's configured signature placeholder size.
     */
    public function getSignatureMaxLength(): int
    {
        return (int) $this->signature_max_length;
    }

    /**
     * Prepare the generated PDF for an external eSign service.
     *
     * IMPORTANT:
     * - TCPDF must be in signing mode before Close().
     * - We do NOT insert the real PKCS#7 signature here.
     * - The returned PDF retains TCPDF's /ByteRange and /Contents
     *   placeholders so PdfSignerService can calculate the hash and
     *   the external eSign response can later be inserted.
     *
     * @param string $name Output PDF path
     * @param string $dest Output destination, normally F
     * @return array{path:string, byte_range:array, contents_offset:int, contents_length:int}
     */
    public function prepareForExternalSigning(
        string $name,
        string $dest = 'F'
    ): array {
        /*
         * Force signing mode. This is required because TCPDF creates
         * the signature object/placeholders during document finalization.
         *
         * If my_set_sign() has already been called by EsignService,
         * this does not create another signature object.
         */
        if (!$this->sign) {
            $this->my_set_sign();
        }

        /*
         * Finish the PDF. TCPDF creates the signature object here.
         */
        if ($this->state < 3) {
            $this->Close();
        }

        /*
         * Get the final TCPDF buffer exactly as generated.
         */
        $pdfdoc = $this->getBuffer();

        if ($pdfdoc === '' || strlen($pdfdoc) < 100) {
            throw new \Exception(
                'TCPDF generated an empty or invalid PDF.'
            );
        }

        /*
         * TCPDF normally leaves one trailing newline. The original
         * NGDRS implementation removes it before ByteRange calculation.
         */
        if (substr($pdfdoc, -1) === "\n") {
            $pdfdoc = substr($pdfdoc, 0, -1);
        }

        /*
         * Verify that TCPDF actually generated its signing placeholders.
         * Do NOT manufacture a /ByteRange ourselves: the placeholder must
         * come from TCPDF so that the PDF object offsets remain correct.
         */
        $byteRangePlaceholder =
            TCPDF_STATIC::$byterange_string;

        $byteRangePosition =
            strpos($pdfdoc, $byteRangePlaceholder);

        if ($byteRangePosition === false) {
            throw new \Exception(
                'TCPDF did not generate the /ByteRange signature placeholder. '
                . 'Ensure my_set_sign() is called before the PDF is finalized.'
            );
        }

        /*
         * Locate /Contents. TCPDF's signature contents are represented as
         * a hexadecimal placeholder enclosed by < and >.
         */
        $contentsMarker = '/Contents <';

        $contentsPosition =
            strpos($pdfdoc, $contentsMarker);

        if ($contentsPosition === false) {
            /*
             * Some TCPDF versions may put whitespace between /Contents
             * and '<'. Accept both forms without changing the PDF.
             */
            $contentsPosition =
                preg_match(
                    '/\/Contents\s*</',
                    $pdfdoc,
                    $contentsMatch,
                    PREG_OFFSET_CAPTURE
                )
                ? $contentsMatch[0][1]
                : false;
        }

        if ($contentsPosition === false) {
            throw new \Exception(
                'TCPDF did not generate the /Contents signature placeholder.'
            );
        }

        /*
         * The first byte covered by the second ByteRange section is the
         * byte immediately after the complete /Contents hexadecimal
         * placeholder, including the '<' and '>' delimiters.
         *
         * TCPDF's own signature_max_length is the number of hexadecimal
         * characters reserved for the external PKCS#7 signature.
         */
        $contentsStart =
            strpos($pdfdoc, '<', $contentsPosition);

        if ($contentsStart === false) {
            throw new \Exception(
                'TCPDF /Contents opening delimiter not found.'
            );
        }
        /*
         * IMPORTANT: determine the real /Contents placeholder size from
         * the generated PDF. Do NOT rely on a manually changed
         * $this->signature_max_length value.
         */
        $contentsEnd =
            strpos(
                $pdfdoc,
                '>',
                $contentsStart + 1
            );

        if ($contentsEnd === false) {
            throw new \Exception(
                'TCPDF /Contents closing delimiter not found.'
            );
        }

        $contentsLength =
            $contentsEnd - $contentsStart + 1;

        /*
         * Calculate the ByteRange in the same way as the original
         * NGDRS customized TCPDF.
         */
        $byteRangeStringLength =
            strlen($byteRangePlaceholder);

        $byteRange = array(
            0,
            $byteRangePosition
            + $byteRangeStringLength
            + 10,
            0,
            0
        );

        /*
         * ByteRange[1] is the position of the '<' delimiter and
         * ByteRange[2] is the byte immediately after the '>' delimiter.
         * Use the actual placeholder found above.
         */
        $byteRange[1] = $contentsStart;

        $byteRange[2] =
            $contentsEnd + 1;

        $byteRange[3] =
            strlen($pdfdoc)
            - $byteRange[2];

        /*
         * Save the first ByteRange offset for PdfSignerService and
         * later signature insertion.
         */
        $this->pdf_byte_range = $byteRange[1];

        /*
         * Keep TCPDF's placeholder PDF unchanged. The external signer
         * needs the exact bytes produced by TCPDF.
         */
        $this->buffer = $pdfdoc;
        $this->bufferlen = strlen($pdfdoc);

        /*
         * Write the prepared PDF to disk.
         */
        $dest = strtoupper($dest);

        if (!in_array($dest, array('F', 'FI'), true)) {
            throw new \Exception(
                'prepareForExternalSigning() supports only F or FI destination.'
            );
        }

        $directory = dirname($name);

        if (!is_dir($directory)) {
            if (!mkdir($directory, 0777, true) && !is_dir($directory)) {
                throw new \Exception(
                    'Unable to create PDF output directory: ' . $directory
                );
            }
        }

        $handle =
            TCPDF_STATIC::fopenLocal($name, 'wb');

        if (!$handle) {
            throw new \Exception(
                'Unable to create prepared PDF: ' . $name
            );
        }

        fwrite(
            $handle,
            $pdfdoc,
            strlen($pdfdoc)
        );

        fclose($handle);

        if (!file_exists($name) || filesize($name) === 0) {
            throw new \Exception(
                'Failed to write prepared PDF: ' . $name
            );
        }

        /*
         * Final verification against the file on disk.
         */
        $writtenPdf =
            file_get_contents($name);

        if ($writtenPdf === false) {
            throw new \Exception(
                'Unable to read prepared PDF: ' . $name
            );
        }

        if (
            strpos(
                $writtenPdf,
                $byteRangePlaceholder
            ) === false
        ) {
            throw new \Exception(
                'Prepared PDF does not contain TCPDF /ByteRange placeholder.'
            );
        }

        if (
            !preg_match(
                '/\/Contents\s*</',
                $writtenPdf
            )
        ) {
            throw new \Exception(
                'Prepared PDF does not contain TCPDF /Contents signature placeholder.'
            );
        }

        return array(
            'path' => $name,
            'byte_range' => $byteRange,
            'contents_offset' => $contentsStart,
            'contents_length' => $contentsLength,
        );
    }

    /**
     * Calculate PDF ByteRange from a TCPDF-generated signing PDF.
     *
     * The PDF is expected to still contain TCPDF's original placeholder.
     */
    public function getPdfByteRange(string $pdfdoc): array
    {
        /* Already-prepared PDF: parse its numeric ByteRange. */
        if (
            preg_match(
                '/\/ByteRange\s*\[\s*0\s+(\d+)\s+(\d+)\s+(\d+)\s*\]/',
                $pdfdoc,
                $matches
            ) === 1
        ) {
            $byteRange = [
                0,
                (int) $matches[1],
                (int) $matches[2],
                (int) $matches[3],
            ];

            $this->pdf_byte_range = $byteRange[1];

            return $byteRange;
        }

        $byteRangePlaceholder =
            TCPDF_STATIC::$byterange_string;

        $position =
            strpos(
                $pdfdoc,
                $byteRangePlaceholder
            );

        if ($position === false) {
            throw new \Exception(
                'TCPDF ByteRange placeholder not found in PDF.'
            );
        }

        /* Find /Contents belonging to this signature dictionary. */
        $search = substr($pdfdoc, $position);
        $match = [];

        if (
            preg_match(
                '/\/Contents\s*</',
                $search,
                $match,
                PREG_OFFSET_CAPTURE
            ) !== 1
        ) {
            throw new \Exception(
                'TCPDF /Contents signature placeholder not found.'
            );
        }

        $contentsMarker =
            $position + (int) $match[0][1];

        $contentsStart =
            strpos(
                $pdfdoc,
                '<',
                $contentsMarker
            );

        $contentsEnd =
            $contentsStart === false
            ? false
            : strpos(
                $pdfdoc,
                '>',
                $contentsStart + 1
            );

        if ($contentsStart === false || $contentsEnd === false) {
            throw new \Exception(
                'Invalid TCPDF /Contents signature placeholder.'
            );
        }

        $byteRange = [
            0,
            $contentsStart,
            $contentsEnd + 1,
            strlen($pdfdoc) - ($contentsEnd + 1),
        ];

        $this->pdf_byte_range = $contentsStart;

        return $byteRange;
    }

    /**
     * Output PDF using the legacy NGDRS custom signing implementation.
     *
     * This method is retained for compatibility with code that calls
     * my_output() directly.
     */
    public function my_output(
        $name = 'doc.pdf',
        $dest = 'I',
        $pdf_file = null,
        $cer_value = null,
        $pkcs7_value = null,
        $signing = false,
        $pdf_byte_range = null
    ) {
        /*
         * Finish document if necessary.
         */
        if ($this->state < 3) {
            $this->Close();
        }

        /*
         * Normalize destination.
         */
        if (is_bool($dest)) {
            $dest = $dest ? 'D' : 'F';
        }

        $dest = strtoupper($dest);

        if ($dest[0] != 'F') {
            $name = preg_replace('/[\s]+/', '_', $name);
            $name = preg_replace(
                '/[^a-zA-Z0-9_\.-]/',
                '',
                $name
            );
        }

        if ($signing === true) {
            $this->sign = true;
        }

        if ($this->sign) {
            /*
             * Get the document content.
             */
            if ($pdf_file !== null) {
                $pdfdoc = $pdf_file;
            } else {
                $pdfdoc = $this->getBuffer();

                /*
                 * Remove final newline.
                 */
                if (substr($pdfdoc, -1) === "\n") {
                    $pdfdoc = substr($pdfdoc, 0, -1);
                }

                /*
                 * Define ByteRange.
                 */
                $byteRange =
                    $this->getPdfByteRange($pdfdoc);

                /*
                 * Remove the signature contents area only when creating
                 * the temporary document for actual PKCS#7 signing.
                 */
                $unsignedPdf =
                    substr(
                        $pdfdoc,
                        0,
                        $byteRange[1]
                    )
                    . substr(
                        $pdfdoc,
                        $byteRange[2]
                    );

                /*
                 * Replace TCPDF ByteRange placeholder with the actual
                 * ByteRange in the temporary signing document.
                 */
                $byteRangePlaceholder =
                    TCPDF_STATIC::$byterange_string;

                $byteRangeStringLength =
                    strlen($byteRangePlaceholder);

                $byteRangeString =
                    sprintf(
                        '/ByteRange[0 %u %u %u]',
                        $byteRange[1],
                        $byteRange[2],
                        $byteRange[3]
                    );

                $paddingLength =
                    $byteRangeStringLength
                    - strlen($byteRangeString);

                if ($paddingLength < 0) {
                    throw new \Exception(
                        'Calculated ByteRange is larger than TCPDF placeholder.'
                    );
                }

                $byteRangeString .=
                    str_repeat(' ', $paddingLength);

                $pdfdoc =
                    str_replace(
                        $byteRangePlaceholder,
                        $byteRangeString,
                        $unsignedPdf
                    );
            }

            /*
             * If an external PKCS#7 value is supplied, insert it into
             * the reserved /Contents area.
             */
            if ($cer_value !== null && $pkcs7_value !== null) {
                $signature =
                    base64_decode(
                        trim($pkcs7_value),
                        true
                    );

                if ($signature === false) {
                    throw new \Exception(
                        'Invalid base64 PKCS#7 signature.'
                    );
                }

                /*
                 * Apply TSA only if the inherited implementation provides
                 * this method and the external signature is intended for
                 * TSA processing.
                 */
                if (method_exists($this, 'applyTSA')) {
                    $signature =
                        $this->applyTSA($signature);
                }

                $signature =
                    current(
                        unpack('H*', $signature)
                    );

                // if (
                //     strlen($signature)
                //     > $this->signature_max_length
                // ) {
                //     throw new \Exception(
                //         'PKCS#7 signature is larger than TCPDF signature placeholder.'
                //     );
                // }

                /*
                 * The supplied value may be either the complete ByteRange
                 * array or just the Contents offset. Normalize it here.
                 */
                if (is_array($pdf_byte_range)) {
                    if (count($pdf_byte_range) !== 4) {
                        throw new \Exception(
                            'Invalid PDF ByteRange array.'
                        );
                    }

                    $insertOffset =
                        (int) $pdf_byte_range[1];
                } elseif ($pdf_byte_range !== null) {
                    $insertOffset =
                        (int) $pdf_byte_range;
                } else {
                    $insertOffset =
                        $this->pdf_byte_range;
                }

                if ($insertOffset === null) {
                    $range =
                        $this->getPdfByteRange($pdfdoc);

                    $insertOffset =
                        $range[1];
                }

                /* The offset must point exactly at '<'. */
                if (
                    $insertOffset < 0
                    || $insertOffset >= strlen($pdfdoc)
                    || !isset($pdfdoc[$insertOffset])
                    || $pdfdoc[$insertOffset] !== '<'
                ) {
                    throw new \Exception(
                        'PDF /Contents opening delimiter not found at ByteRange offset.'
                    );
                }

                $contentsEnd =
                    strpos(
                        $pdfdoc,
                        '>',
                        $insertOffset + 1
                    );

                if ($contentsEnd === false) {
                    throw new \Exception(
                        'PDF /Contents closing delimiter not found.'
                    );
                }

                $contentsHexLength =
                    $contentsEnd - $insertOffset - 1;

                if ($contentsHexLength <= 0) {
                    throw new \Exception(
                        'PDF /Contents placeholder is empty.'
                    );
                }
                error_log(
                    'TCPDF SIGNATURE PLACEHOLDER: ' .
                    $contentsHexLength .
                    ' | PKCS7 REQUIRED: ' .
                    strlen($signature)
                );

                if (strlen($signature) > $contentsHexLength) {

                    throw new \Exception(
                        'PKCS#7 signature is larger than TCPDF signature placeholder. ' .
                        'Required: ' . strlen($signature) .
                        ' hex characters; available: ' . $contentsHexLength
                    );
                }

                /* Keep the /Contents field exactly the same byte length. */
                $signature =
                    str_pad(
                        $signature,
                        $contentsHexLength,
                        '0'
                    );

                /*
                 * CRITICAL FIX:
                 * Replace the complete original <000...> placeholder.
                 * Do NOT insert <signature> in front of the old placeholder.
                 */
                $pdfdoc =
                    substr(
                        $pdfdoc,
                        0,
                        $insertOffset
                    )
                    . '<'
                    . $signature
                    . '>'
                    . substr(
                        $pdfdoc,
                        $contentsEnd + 1
                    );

                /*
                 * If this call received the raw TCPDF placeholder, finalize
                 * ByteRange now. This is important because PdfSignerService
                 * normally passes the prepared PDF as $pdf_file.
                 */
                $byteRangePlaceholder =
                    TCPDF_STATIC::$byterange_string;

                if (strpos($pdfdoc, $byteRangePlaceholder) !== false) {
                    $range =
                        $this->getPdfByteRange(
                            $pdfdoc
                        );

                    $byteRangeString =
                        sprintf(
                            '/ByteRange[0 %u %u %u]',
                            $range[1],
                            $range[2],
                            $range[3]
                        );

                    $paddingLength =
                        strlen($byteRangePlaceholder)
                        - strlen($byteRangeString);

                    if ($paddingLength < 0) {
                        throw new \Exception(
                            'Calculated ByteRange is larger than TCPDF placeholder.'
                        );
                    }

                    $byteRangeString .=
                        str_repeat(
                            ' ',
                            $paddingLength
                        );

                    $pdfdoc =
                        str_replace(
                            $byteRangePlaceholder,
                            $byteRangeString,
                            $pdfdoc,
                            $replaceCount
                        );

                    if ($replaceCount !== 1) {
                        throw new \Exception(
                            'Unable to finalize PDF ByteRange.'
                        );
                    }
                }

                $this->buffer =
                    $pdfdoc;
            } else {
                /*
                 * No external signature supplied: retain the original
                 * TCPDF signing PDF.
                 */
                $this->buffer =
                    $this->getBuffer();
            }

            $this->bufferlen =
                strlen($this->buffer);
        }

        /*
         * Handle output destination.
         */
        switch ($dest) {
            case 'I':
                if (ob_get_contents()) {
                    $this->Error(
                        'Some data has already been output, can\'t send PDF file'
                    );
                }

                if (php_sapi_name() != 'cli') {
                    header('Content-Type: application/pdf');
                    header(
                        'Content-Disposition: inline; filename="'
                        . basename($name)
                        . '"'
                    );

                    TCPDF_STATIC::sendOutputData(
                        $this->getBuffer(),
                        $this->bufferlen
                    );
                } else {
                    echo $this->getBuffer();
                }

                break;

            case 'D':
                if (ob_get_contents()) {
                    $this->Error(
                        'Some data has already been output, can\'t send PDF file'
                    );
                }

                header('Content-Type: application/pdf');
                header(
                    'Content-Disposition: attachment; filename="'
                    . basename($name)
                    . '"'
                );

                TCPDF_STATIC::sendOutputData(
                    $this->getBuffer(),
                    $this->bufferlen
                );

                break;

            case 'F':
            case 'FI':
            case 'FD':
                $handle =
                    TCPDF_STATIC::fopenLocal(
                        $name,
                        'wb'
                    );

                if (!$handle) {
                    $this->Error(
                        'Unable to create output file: ' . $name
                    );
                }

                fwrite(
                    $handle,
                    $this->getBuffer(),
                    $this->bufferlen
                );

                fclose($handle);

                if ($dest === 'FI' || $dest === 'FD') {
                    header('Content-Type: application/pdf');

                    header(
                        'Content-Disposition: '
                        . ($dest === 'FI'
                            ? 'inline'
                            : 'attachment')
                        . '; filename="'
                        . basename($name)
                        . '"'
                    );

                    TCPDF_STATIC::sendOutputData(
                        file_get_contents($name),
                        filesize($name)
                    );
                }

                break;

            case 'E':
                $retval =
                    'Content-Type: application/pdf;' . "\r\n";

                $retval .=
                    ' name="' . $name . '"' . "\r\n";

                $retval .=
                    'Content-Transfer-Encoding: base64' . "\r\n";

                $retval .=
                    'Content-Disposition: attachment;' . "\r\n";

                $retval .=
                    ' filename="' . $name . '"' . "\r\n\r\n";

                $retval .=
                    chunk_split(
                        base64_encode(
                            $this->getBuffer()
                        ),
                        76,
                        "\r\n"
                    );

                return $retval;

            case 'S':
                return $this->getBuffer();

            default:
                $this->Error(
                    'Incorrect output destination: ' . $dest
                );
        }

        return '';
    }

    /**
     * Split a PDF byte stream into indirect objects, safely skipping
     * over binary stream content so that byte sequences resembling
     * "N G obj" or "endobj" inside embedded fonts/images/compressed
     * streams can never be mistaken for real object boundaries.
     *
     * Returns an array of matches shaped like preg_match_all(...,
     * PREG_SET_ORDER) would for the pattern:
     *
     *   /(\d+)\s+(\d+)\s+obj\b(.*?)\bendobj/s
     *
     * i.e. each entry is [full, objNum, gen, body].
     */
    private function splitPdfObjectsRespectingStreams(string $pdf): array
    {
        $results = [];
        $len = strlen($pdf);
        $pos = 0;

        // Matches the start of an indirect object: "N G obj"
        $objStartPattern = '/(\d+)[ \t\r\n]+(\d+)[ \t\r\n]+obj\b/';

        while (
            preg_match(
                $objStartPattern,
                $pdf,
                $startMatch,
                PREG_OFFSET_CAPTURE,
                $pos
            ) === 1
        ) {
            $objNum = $startMatch[1][0];
            $genNum = $startMatch[2][0];
            $objHeaderStart = $startMatch[0][1];
            $bodyStart = $objHeaderStart + strlen($startMatch[0][0]);

            /*
             * Walk forward from bodyStart looking for "stream" or
             * "endobj", whichever comes first. If we hit "stream"
             * first, we must skip its binary payload using the
             * declared /Length before continuing to search for
             * "endobj" - a text search inside binary data is unsafe.
             */
            $cursor = $bodyStart;
            $objEnd = null;

            while (true) {
                $nextStream = strpos($pdf, 'stream', $cursor);
                $nextEndobj = strpos($pdf, 'endobj', $cursor);

                if ($nextEndobj === false) {
                    // Malformed / truncated PDF.
                    break 2;
                }

                if ($nextStream === false || $nextEndobj < $nextStream) {
                    /*
                     * No more streams before endobj (or no stream at
                     * all in this object) - this is the real end.
                     */
                    $objEnd = $nextEndobj;
                    break;
                }

                /*
                 * Found "stream" before "endobj". Determine the
                 * actual start of binary data: "stream" is followed
                 * by an optional \r then \n (per PDF spec), or just \n.
                 */
                $dataStart = $nextStream + strlen('stream');

                if (substr($pdf, $dataStart, 2) === "\r\n") {
                    $dataStart += 2;
                } elseif (substr($pdf, $dataStart, 1) === "\n") {
                    $dataStart += 1;
                }

                /*
                 * Find /Length for THIS object (search only within
                 * the dictionary text between bodyStart and nextStream,
                 * to avoid picking up an unrelated /Length elsewhere).
                 */
                $dictText = substr($pdf, $bodyStart, $nextStream - $bodyStart);

                $streamLength = null;

                if (
                    preg_match(
                        '/\/Length\s+(\d+)\s*(\d*\s*R)?/',
                        $dictText,
                        $lengthMatch
                    ) === 1
                ) {
                    /*
                     * If it's an indirect reference ("/Length 5 0 R"),
                     * we can't resolve it without a second pass. Fall
                     * back to literal "endstream" search in that case
                     * - rare in TCPDF output, which normally emits a
                     * direct integer /Length.
                     */
                    if (!empty($lengthMatch[2])) {
                        $streamLength = null;
                    } else {
                        $streamLength = (int) $lengthMatch[1];
                    }
                }

                if ($streamLength !== null) {
                    /*
                     * Skip exactly the declared binary length, then
                     * expect "endstream" shortly after (allow a small
                     * whitespace tolerance for encoding differences).
                     */
                    $expectedEndstream = strpos(
                        $pdf,
                        'endstream',
                        $dataStart + $streamLength
                    );

                    if (
                        $expectedEndstream !== false
                        && $expectedEndstream <= $dataStart + $streamLength + 32
                    ) {
                        $cursor = $expectedEndstream + strlen('endstream');
                        continue;
                    }
                }

                /*
                 * /Length was missing, indirect, or didn't line up
                 * (defensive fallback) - locate "endstream" literally
                 * and resume from just after it. This is still safer
                 * than the original approach because "stream" acts as
                 * a hard skip-ahead point past the bulk of the binary
                 * payload before any endobj search resumes.
                 */
                $endstreamPos = strpos($pdf, 'endstream', $dataStart);

                if ($endstreamPos === false) {
                    break 2; // Malformed PDF.
                }

                $cursor = $endstreamPos + strlen('endstream');
            }

            if ($objEnd === null) {
                break;
            }

            $body = substr($pdf, $bodyStart, $objEnd - $bodyStart);

            $results[] = [
                substr($pdf, $objHeaderStart, ($objEnd + strlen('endobj')) - $objHeaderStart),
                $objNum,
                $genNum,
                $body,
            ];

            $pos = $objEnd + strlen('endobj');

            if ($pos >= $len) {
                break;
            }
        }

        return $results;
    }

    /**
     * Prepare a NEW incremental PDF signature revision.
     *
     * This method never rewrites the previous PDF revision. It appends
     * a new signature field + signature dictionary and updates the page
     * and AcroForm through an incremental update.
     */
    public function prepareIncrementalExternalSigning(
        string $sourcePdf,
        string $outputPdf,
        string $fieldName,
        int $page = 1,
        float $x = 0,
        float $y = 0,
        float $width = 50,
        float $height = 20,
        string $partyName = '',
        ?string $signDateTime = null
    ): array {
        if (!is_file($sourcePdf)) {
            throw new \Exception('Source PDF not found: ' . $sourcePdf);
        }

        $pdf = file_get_contents($sourcePdf);

        if ($pdf === false || strlen($pdf) < 100 || substr($pdf, 0, 5) !== '%PDF-') {
            throw new \Exception('Invalid source PDF.');
        }

        $pageIndex = max(1, $page);

        /*
         * Find the requested page. For NGDRS PDFs the page object itself
         * contains "/Type /Page". We select the Nth page object.
         */
        // preg_match_all(
        //     '/(\d+)\s+(\d+)\s+obj\b(.*?)\bendobj/s',
        //     $pdf,
        //     $objects,
        //     PREG_SET_ORDER
        // );

        // $pageObjects = [];

        // foreach ($objects as $obj) {
        //     $body = $obj[3];

        //     if (
        //         preg_match('/\/Type\s*\/Page\b/', $body)
        //         && !preg_match('/\/Type\s*\/Pages\b/', $body)
        //     ) {
        //         $pageObjects[] = [
        //             'num' => (int) $obj[1],
        //             'gen' => (int) $obj[2],
        //             'body' => $body,
        //         ];
        //     }
        // }
        preg_match_all(
            '/(\d+)\s+(\d+)\s+obj\b(.*?)\bendobj/s',
            $pdf,
            $objects,
            PREG_SET_ORDER
        );
        $objects = $this->splitPdfObjectsRespectingStreams($pdf);
        $latestByNum = [];

        foreach ($objects as $obj) {
            $latestByNum[(int) $obj[1]] = [
                'num' => (int) $obj[1],
                'gen' => (int) $obj[2],
                'body' => $obj[3],
            ];
        }

        $pageObjects = [];

        foreach ($latestByNum as $entry) {
            if (
                preg_match('/\/Type\s*\/Page\b/', $entry['body'])
                && !preg_match('/\/Type\s*\/Pages\b/', $entry['body'])
            ) {
                $pageObjects[] = $entry;
            }
        }

        if (!isset($pageObjects[$pageIndex - 1])) {
            throw new \Exception(
                'Unable to locate PDF page ' . $pageIndex . '.'
            );
        }

        $pageObject = $pageObjects[$pageIndex - 1];
        $pageRef = $pageObject['num'] . ' ' . $pageObject['gen'] . ' R';

        /*
         * Find the document catalog (/Root).
         */
        if (
            preg_match_all(
                '/\/Root\s+(\d+)\s+(\d+)\s+R\b/',
                $pdf,
                $rootMatches
            ) !== false
            && !empty($rootMatches[1])
        ) {
            $rootNum = (int) end($rootMatches[1]);
            $rootGen = (int) end($rootMatches[2]);
        } else {
            throw new \Exception('PDF catalog /Root reference not found.');
        }

        $rootObject = $this->getIncrementalPdfObject(
            $pdf,
            $rootNum,
            $rootGen
        );

        if ($rootObject === null) {
            throw new \Exception('Unable to read PDF catalog object.');
        }

        /*
         * Determine the next free object number.
         */
        $maxObject = 0;

        foreach ($objects as $obj) {
            $maxObject = max($maxObject, (int) $obj[1]);
        }

        $sigObjectNum = ++$maxObject;
        $fontObjectNum = ++$maxObject;
        $appearanceObjectNum = ++$maxObject;
        $widgetObjectNum = ++$maxObject;
        $annotsObjectNum = ++$maxObject;

        /*
         * Signature field name must be unique in the document.
         */
        $safeFieldName = preg_replace(
            '/[^A-Za-z0-9_\-\.]/',
            '_',
            $fieldName
        );

        if ($safeFieldName === '') {
            $safeFieldName = 'Signature_' . $sigObjectNum;
        }

        /*
         * Create the signature dictionary. A fixed-width ByteRange
         * placeholder is used so replacing it never changes offsets.
         *
         * 32768 hex chars = 16384 bytes of PKCS#7 capacity.
         */
        $contentsHexLength = 32768;

        $byteRangePlaceholder =
            '[0000000000 0000000000 0000000000 0000000000]';

        $timestamp = gmdate('YmdHis') . 'Z';
        // error_log('SIGNATURE PLACEHOLDER LENGTH = ' . $contentsHexLength);
        $sigObject =
            $sigObjectNum . " 0 obj\n"
            . "<<\n"
            . "/Type /Sig\n"
            . "/Filter /Adobe.PPKLite\n"
            . "/SubFilter /adbe.pkcs7.detached\n"
            . "/ByteRange " . $byteRangePlaceholder . "\n"
            . "/Contents <"
            . str_repeat('0', $contentsHexLength)
            . ">\n"
            . "/M (D:" . $timestamp . ")\n"
            . "/Name (" . $this->escapePdfString($safeFieldName) . ")\n"
            . ">>\n"
            . "endobj\n";

        /*
         * Visible signature appearance.
         *
         * This text is part of the PDF revision BEFORE the PDF is hashed,
         * so it is covered by the external eSign signature.
         */
        $partyName = trim($partyName);

        if ($partyName === '') {
            $partyName = 'Authorized Party';
        }

        $signDateTime = $signDateTime ?: date('Y-m-d H:i:s');
        $timestampValue = strtotime($signDateTime);

        if ($timestampValue === false) {
            $timestampValue = time();
        }

        $displayDate = date('d/m/Y', $timestampValue);
        $displayTime = date('h:i:s A', $timestampValue);

        // $visibleLines = [
        //     // 'Digitally Signed by:',
        //     // $partyName,
        //     // 'Date: ' . $displayDate,
        //     // 'Time: ' . $displayTime,
        // ];

        $fontSize = max(4, min(7, $height * 0.22));
        // $lineHeight = $fontSize + 1.5;
        // $textY = $height - $fontSize - 1;

        $appearanceStream =
            "q\n"
            . "BT\n"
            . "/F1 " . $this->formatPdfNumber($fontSize) . " Tf\n"
            . "0 g\n";

        // foreach ($visibleLines as $line) {
        //     $line = function_exists('mb_substr')
        //         ? mb_substr($line, 0, 70)
        //         : substr($line, 0, 70);

        //     $appearanceStream .=
        //         "1 " . $this->formatPdfNumber($textY) . " Td\n"
        //         . "(" . $this->escapePdfString($line) . ") Tj\n";

        //     $textY -= $lineHeight;
        // }

        $appearanceStream .=
            "ET\n"
            . "Q\n";

        /*
         * Create a dedicated Helvetica font object.
         * Do NOT assume an existing object such as 4 0 R is a font.
         */
        $fontObject =
            $fontObjectNum . " 0 obj\n"
            . "<<\n"
            . "/Type /Font\n"
            . "/Subtype /Type1\n"
            . "/BaseFont /Helvetica\n"
            . "/Encoding /WinAnsiEncoding\n"
            . ">>\n"
            . "endobj\n";

        $appearanceObject =
            $appearanceObjectNum . " 0 obj\n"
            . "<<\n"
            . "/Type /XObject\n"
            . "/Subtype /Form\n"
            . "/FormType 1\n"
            . "/BBox [0 0 "
            . $this->formatPdfNumber($width) . " "
            . $this->formatPdfNumber($height)
            . "]\n"
            . "/Resources << /Font << /F1 "
            . $fontObjectNum
            . " 0 R >> >>\n"
            . "/Length " . strlen($appearanceStream) . "\n"
            . ">>\n"
            . "stream\n"
            . $appearanceStream
            . "endstream\n"
            . "endobj\n";

        /*
         * Widget annotation / signature field.
         */
        // $widgetObject =
        //     $widgetObjectNum . " 0 obj\n"
        //     . "<<\n"
        //     . "/Type /Annot\n"
        //     . "/Subtype /Widget\n"
        //     . "/FT /Sig\n"
        //     . "/T (" . $this->escapePdfString($safeFieldName) . ")\n"
        //     . "/V " . $sigObjectNum . " 0 R\n"
        //     . "/P " . $pageRef . "\n"
        //     . "/Rect ["
        //     . $this->formatPdfNumber($x) . " "
        //     . $this->formatPdfNumber($y) . " "
        //     . $this->formatPdfNumber($x + $width) . " "
        //     . $this->formatPdfNumber($y + $height)
        //     . "]\n"
        //     . "/F 4\n"
        //     . "/AP << /N " . $appearanceObjectNum . " 0 R >>\n"
        //     . ">>\n"
        //     . "endobj\n";
        $widgetObject =
            $widgetObjectNum . " 0 obj\n"
            . "<<\n"
            . "/Type /Annot\n"
            . "/Subtype /Widget\n"
            . "/FT /Sig\n"
            . "/T (" . $this->escapePdfString($safeFieldName) . ")\n"
            . "/TU (" . $this->escapePdfString(
                    'Signed by ' . $partyName . ' on ' . $displayDate . ' ' . $displayTime
                ) . ")\n"
            . "/V " . $sigObjectNum . " 0 R\n"
            . "/P " . $pageRef . "\n"
            . "/Rect ["
            . $this->formatPdfNumber($x) . " "
            . $this->formatPdfNumber($y) . " "
            . $this->formatPdfNumber($x + $width) . " "
            . $this->formatPdfNumber($y + $height)
            . "]\n"
            . "/F 4\n"
            . "/AP << /N " . $appearanceObjectNum . " 0 R >>\n"
            . ">>\n"
            . "endobj\n";

        /*
         * Preserve existing page annotations.
         *
         * We create a NEW Annots array object and point the updated page
         * revision at it. Existing annotation references are retained.
         */
        $oldAnnotsRefs = [];

        if (
            preg_match(
                '/\/Annots\s*\[(.*?)\]/s',
                $pageObject['body'],
                $annotsMatch
            )
        ) {
            preg_match_all(
                '/(\d+)\s+(\d+)\s+R/',
                $annotsMatch[1],
                $annotsRefs
            );

            foreach ($annotsRefs[0] as $ref) {
                $oldAnnotsRefs[] = trim($ref);
            }
        } elseif (
            preg_match(
                '/\/Annots\s+(\d+)\s+(\d+)\s+R\b/',
                $pageObject['body'],
                $annotsRefMatch
            )
        ) {
            $oldAnnotsObjectNum = (int) $annotsRefMatch[1];
            $oldAnnotsObjectGen = (int) $annotsRefMatch[2];

            $oldAnnotsObject = $this->getIncrementalPdfObject(
                $pdf,
                $oldAnnotsObjectNum,
                $oldAnnotsObjectGen
            );

            if (
                $oldAnnotsObject !== null
                && preg_match(
                    '/\[(.*?)\]/s',
                    $oldAnnotsObject['body'],
                    $arrayMatch
                )
            ) {
                preg_match_all(
                    '/(\d+)\s+(\d+)\s+R/',
                    $arrayMatch[1],
                    $annotsRefs
                );

                foreach ($annotsRefs[0] as $ref) {
                    $oldAnnotsRefs[] = trim($ref);
                }
            }
        }

        $oldAnnotsRefs[] = $widgetObjectNum . " 0 R";

        $annotsObject =
            $annotsObjectNum . " 0 obj\n"
            . "["
            . implode(' ', $oldAnnotsRefs)
            . "]\n"
            . "endobj\n";

        /*
         * Update the page dictionary.
         */
        // $updatedPageBody = $pageObject['body'];

        // $updatedPageBody =
        //     preg_replace(
        //         '/\/Annots\s*\[(.*?)\]/s',
        //         '/Annots ' . $annotsObjectNum . ' 0 R',
        //         $updatedPageBody,
        //         1,
        //         $pageAnnotsReplaced
        //     );

        // if ($pageAnnotsReplaced === 0) {
        //     $updatedPageBody =
        //         preg_replace(
        //             '/>>\s*$/s',
        //             '/Annots ' . $annotsObjectNum . ' 0 R' . "\n>>",
        //             $updatedPageBody,
        //             1
        //         );
        // }
        $updatedPageBody = $pageObject['body'];

        $updatedPageBody =
            preg_replace(
                '/\/Annots\s*\[(.*?)\]/s',
                '/Annots ' . $annotsObjectNum . ' 0 R',
                $updatedPageBody,
                1,
                $pageAnnotsReplaced
            );

        if ($pageAnnotsReplaced === 0) {
            $updatedPageBody =
                preg_replace(
                    '/\/Annots\s+\d+\s+\d+\s+R\b/',
                    '/Annots ' . $annotsObjectNum . ' 0 R',
                    $updatedPageBody,
                    1,
                    $pageAnnotsReplaced
                );
        }

        if ($pageAnnotsReplaced === 0) {
            $updatedPageBody =
                preg_replace(
                    '/>>\s*$/s',
                    '/Annots ' . $annotsObjectNum . ' 0 R' . "\n>>",
                    $updatedPageBody,
                    1
                );
        }

        $updatedPageObject =
            $pageObject['num'] . ' ' . $pageObject['gen'] . " obj\n"
            . $updatedPageBody
            . "\nendobj\n";

        /*
         * Add/update the signature field in AcroForm.
         */
        $acroFormObjectNum = null;
        $acroFormObjectGen = 0;
        $acroFormBody = null;

        if (
            preg_match(
                '/\/AcroForm\s+(\d+)\s+(\d+)\s+R\b/',
                $rootObject['body'],
                $acroRef
            )
        ) {
            $acroFormObjectNum = (int) $acroRef[1];
            $acroFormObjectGen = (int) $acroRef[2];

            $acroObject = $this->getIncrementalPdfObject(
                $pdf,
                $acroFormObjectNum,
                $acroFormObjectGen
            );

            if ($acroObject !== null) {
                $acroFormBody = $acroObject['body'];
            }
        }

        if ($acroFormBody !== null) {
            $fieldRefs = [];

            if (
                preg_match(
                    '/\/Fields\s*\[(.*?)\]/s',
                    $acroFormBody,
                    $fieldsMatch
                )
            ) {
                preg_match_all(
                    '/(\d+)\s+(\d+)\s+R/',
                    $fieldsMatch[1],
                    $fieldMatches
                );

                foreach ($fieldMatches[0] as $ref) {
                    $fieldRefs[] = trim($ref);
                }

                $newFields =
                    '/Fields ['
                    . implode(' ', $fieldRefs)
                    . ' ' . $widgetObjectNum . ' 0 R]';

                $acroFormBody =
                    preg_replace(
                        '/\/Fields\s*\[(.*?)\]/s',
                        $newFields,
                        $acroFormBody,
                        1
                    );
            } else {
                $acroFormBody =
                    preg_replace(
                        '/>>\s*$/s',
                        '/Fields [' . $widgetObjectNum . ' 0 R]' . "\n>>",
                        $acroFormBody,
                        1
                    );
            }

            $updatedAcroFormObject =
                $acroFormObjectNum . ' ' . $acroFormObjectGen . " obj\n"
                . $acroFormBody
                . "\nendobj\n";
        } else {
            $acroFormObjectNum = ++$maxObject;

            $updatedAcroFormObject =
                $acroFormObjectNum . " 0 obj\n"
                . "<<\n"
                . "/Fields [" . $widgetObjectNum . " 0 R]\n"
                . "/SigFlags 3\n"
                . ">>\n"
                . "endobj\n";

            /*
             * Add /AcroForm to the catalog.
             */
            $rootBody = $rootObject['body'];

            $rootBody =
                preg_replace(
                    '/>>\s*$/s',
                    '/AcroForm ' . $acroFormObjectNum . " 0 R\n>>",
                    $rootBody,
                    1
                );

            $updatedRootObject =
                $rootNum . ' ' . $rootGen . " obj\n"
                . $rootBody
                . "\nendobj\n";
        }

        /*
         * If AcroForm already existed, root does not need another update.
         * If it did not exist, update the catalog as well.
         */
        $oldLength = strlen($pdf);
        $increment = '';
        $offsets = [];

        $appendObject = function (string $object) use (&$increment, &$offsets) {
            if (
                preg_match(
                    '/^(\d+)\s+(\d+)\s+obj\b/',
                    $object,
                    $m
                )
            ) {
                $offsets[] = [
                    'num' => (int) $m[1],
                    'gen' => (int) $m[2],
                ];
            }

            $increment .= $object;
        };

        $appendObject($sigObject);
        $appendObject($fontObject);
        $appendObject($appearanceObject);
        $appendObject($widgetObject);
        $appendObject($annotsObject);
        $appendObject($updatedPageObject);

        if ($acroFormBody !== null) {
            $appendObject($updatedAcroFormObject);
        } else {
            $appendObject($updatedAcroFormObject);
            $appendObject($updatedRootObject);
        }

        /*
         * The signature dictionary must be located in the final appended
         * revision. Calculate its exact Contents position after appending.
         */
        $prepared = $pdf . $increment;

        $sigContentsPattern =
            '/(\d+)\s+0\s+obj\b.*?\/ByteRange\s*\[0000000000 0000000000 0000000000 0000000000\]\s*'
            . '\/Contents\s*<([0]+)>/s';

        if (
            preg_match(
                $sigContentsPattern,
                $prepared,
                $sigMatch,
                PREG_OFFSET_CAPTURE
            ) !== 1
        ) {
            throw new \Exception(
                'Unable to locate incremental signature placeholder.'
            );
        }

        $contentsHex = $sigMatch[2][0];
        $contentsStart =
            (int) $sigMatch[2][1] - 1;

        $contentsEnd =
            $contentsStart + 1 + strlen($contentsHex);

        /*
         * Find the exact ByteRange placeholder belonging to this signature.
         */
        $byteRangeNeedle =
            '[0000000000 0000000000 0000000000 0000000000]';

        $byteRangeStart =
            strrpos(
                substr($prepared, 0, $contentsStart),
                $byteRangeNeedle
            );

        if ($byteRangeStart === false) {
            throw new \Exception(
                'Unable to locate incremental ByteRange placeholder.'
            );
        }

        $byteRange =
            [
                0,
                $contentsStart,
                $contentsEnd + 1,
                strlen($prepared) - ($contentsEnd + 1),
            ];

        $byteRangeString =
            sprintf(
                '[%010d %010d %010d %010d]',
                $byteRange[0],
                $byteRange[1],
                $byteRange[2],
                $byteRange[3]
            );

        if (strlen($byteRangeString) !== strlen($byteRangeNeedle)) {
            throw new \Exception('Invalid incremental ByteRange length.');
        }

        $prepared =
            substr($prepared, 0, $byteRangeStart)
            . $byteRangeString
            . substr(
                $prepared,
                $byteRangeStart + strlen($byteRangeNeedle)
            );

        /*
         * Add the incremental xref and trailer.
         */
        $xrefOffset = strlen($prepared);
        $xref = "xref\n";

        usort(
            $offsets,
            static function ($a, $b) {
                return $a['num'] <=> $b['num'];
            }
        );

        /*
         * Recalculate offsets in the FINAL document.
         */
        $finalOffsets = [];

        foreach ($offsets as $entry) {
            $needle =
                $entry['num'] . ' ' . $entry['gen'] . " obj\n";

            $pos =
                strrpos($prepared, $needle);

            if ($pos === false) {
                throw new \Exception(
                    'Unable to calculate xref offset for object '
                    . $entry['num']
                );
            }

            $finalOffsets[$entry['num']] = [
                'num' => $entry['num'],
                'gen' => $entry['gen'],
                'offset' => $pos,
            ];
        }

        $groups = [];
        $numbers = array_keys($finalOffsets);
        sort($numbers, SORT_NUMERIC);

        foreach ($numbers as $num) {
            if (empty($groups)) {
                $groups[] = [$num, $num];
                continue;
            }

            $last = count($groups) - 1;

            if ($num === $groups[$last][1] + 1) {
                $groups[$last][1] = $num;
            } else {
                $groups[] = [$num, $num];
            }
        }

        foreach ($groups as $group) {
            $start = $group[0];
            $count = $group[1] - $group[0] + 1;

            $xref .= $start . ' ' . $count . "\n";

            for ($i = $start; $i <= $group[1]; $i++) {
                $entry = $finalOffsets[$i];

                $xref .= sprintf(
                    "%010d %05d n \n",
                    $entry['offset'],
                    $entry['gen']
                );
            }
        }

        $previousXref = null;

        if (
            preg_match_all(
                '/startxref\s+(\d+)\s+%%EOF/s',
                $pdf,
                $startXrefs
            )
            && !empty($startXrefs[1])
        ) {
            $previousXref =
                (int) end($startXrefs[1]);
        }

        if ($previousXref === null) {
            throw new \Exception(
                'Previous PDF startxref not found.'
            );
        }

        $maxObjectNumber =
            max(
                $maxObject,
                max(array_keys($finalOffsets))
            );

        $trailer =
            "trailer\n"
            . "<<\n"
            . "/Size " . ($maxObjectNumber + 1) . "\n"
            . "/Root " . $rootNum . ' ' . $rootGen . " R\n"
            . "/Prev " . $previousXref . "\n"
            . ">>\n"
            . "startxref\n"
            . $xrefOffset . "\n"
            . "%%EOF\n";

        $prepared .= $xref . $trailer;

        /*
         * The xref offset itself is after the signature ByteRange and
         * therefore belongs to ByteRange[3]. Recalculate ByteRange after
         * adding xref/trailer, then replace the same fixed-width values.
         */
        $finalContentsStart = $contentsStart;
        $finalContentsEnd = $contentsEnd;

        $finalByteRange = [
            0,
            $finalContentsStart,
            $finalContentsEnd + 1,
            strlen($prepared) - ($finalContentsEnd + 1),
        ];

        $finalByteRangeString =
            sprintf(
                '[%010d %010d %010d %010d]',
                $finalByteRange[0],
                $finalByteRange[1],
                $finalByteRange[2],
                $finalByteRange[3]
            );

        $currentRange =
            substr(
                $prepared,
                $byteRangeStart,
                strlen($byteRangeNeedle)
            );

        if (
            preg_match(
                '/^\[\d{10} \d{10} \d{10} \d{10}\]$/',
                $currentRange
            ) !== 1
        ) {
            throw new \Exception(
                'Incremental ByteRange placeholder was corrupted.'
            );
        }

        $prepared =
            substr($prepared, 0, $byteRangeStart)
            . $finalByteRangeString
            . substr(
                $prepared,
                $byteRangeStart + strlen($byteRangeNeedle)
            );

        /*
         * Write prepared PDF.
         */
        $directory = dirname($outputPdf);

        if (!is_dir($directory)) {
            if (
                !mkdir($directory, 0777, true)
                && !is_dir($directory)
            ) {
                throw new \Exception(
                    'Unable to create output directory: ' . $directory
                );
            }
        }

        if (file_put_contents($outputPdf, $prepared) === false) {
            throw new \Exception(
                'Unable to write prepared incremental PDF.'
            );
        }

        return [
            'path' => $outputPdf,
            'byte_range' => $finalByteRange,
            'contents_offset' => $finalContentsStart,
            'contents_length' => strlen($contentsHex) + 2,
            'field_name' => $safeFieldName,
        ];
    }

    /**
     * Insert an external PKCS#7 signature into the LAST incremental
     * signature placeholder without changing any other PDF bytes.
     */
    public function applyIncrementalExternalSignature(
        string $preparedPdf,
        string $signedPdf,
        string $pkcs7
    ): array {
        if (!is_file($preparedPdf)) {
            throw new \Exception(
                'Prepared incremental PDF not found: ' . $preparedPdf
            );
        }

        $pdf = file_get_contents($preparedPdf);

        if ($pdf === false || strlen($pdf) < 100) {
            throw new \Exception('Unable to read prepared incremental PDF.');
        }

        $signature = base64_decode(trim($pkcs7), true);

        if ($signature === false) {
            throw new \Exception('Invalid base64 PKCS#7 signature.');
        }

        $signatureHex =
            strtoupper(bin2hex($signature));

        /*
         * Locate the LAST signature ByteRange. Because this is an
         * incremental update, the new signature is at the end of the PDF.
         */
        if (
            preg_match_all(
                '/\/ByteRange\s*\[\s*(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s*\]/',
                $pdf,
                $rangeMatches,
                PREG_OFFSET_CAPTURE
            ) < 1
        ) {
            throw new \Exception(
                'No PDF signature ByteRange found.'
            );
        }

        $lastRangeIndex =
            count($rangeMatches[0]) - 1;

        $contentsSearchStart =
            (int) $rangeMatches[0][$lastRangeIndex][1];

        if (
            preg_match(
                '/\/Contents\s*<([0-9A-Fa-f]+)>/',
                substr($pdf, $contentsSearchStart),
                $contentsMatch,
                PREG_OFFSET_CAPTURE
            ) !== 1
        ) {
            throw new \Exception(
                'Incremental signature /Contents placeholder not found.'
            );
        }

        $contentsHex =
            $contentsMatch[1][0];

        $contentsHexStart =
            $contentsSearchStart
            + (int) $contentsMatch[1][1];

        $contentsHexLength =
            strlen($contentsHex);

        if ($contentsHexLength <= 0) {
            throw new \Exception(
                'Incremental /Contents placeholder is empty.'
            );
        }

        if (strlen($signatureHex) > $contentsHexLength) {
            throw new \Exception(
                'PKCS#7 signature is larger than incremental placeholder. '
                . 'Required: ' . strlen($signatureHex)
                . ' hex characters; available: ' . $contentsHexLength
            );
        }

        /*
         * Keep the exact same byte length.
         */
        $replacement =
            str_pad(
                $signatureHex,
                $contentsHexLength,
                '0'
            );

        $pdf =
            substr($pdf, 0, $contentsHexStart)
            . $replacement
            . substr(
                $pdf,
                $contentsHexStart + $contentsHexLength
            );

        if (file_put_contents($signedPdf, $pdf) === false) {
            throw new \Exception(
                'Unable to write signed incremental PDF.'
            );
        }

        return [
            'path' => $signedPdf,
            'byte_range' => [
                (int) $rangeMatches[1][$lastRangeIndex][0],
                (int) $rangeMatches[2][$lastRangeIndex][0],
                (int) $rangeMatches[3][$lastRangeIndex][0],
                (int) $rangeMatches[4][$lastRangeIndex][0],
            ],
            'signature_hex_length' => strlen($signatureHex),
            'contents_hex_length' => $contentsHexLength,
        ];
    }

    /**
     * Read one PDF indirect object.
     */
    // private function getIncrementalPdfObject(
    //     string $pdf,
    //     int $objectNumber,
    //     int $generation = 0
    // ): ?array {
    //     $pattern =
    //         '/'
    //         . preg_quote((string) $objectNumber, '/')
    //         . '\s+'
    //         . preg_quote((string) $generation, '/')
    //         . '\s+obj\b(.*?)\bendobj\s*/s';

    //     if (
    //         preg_match(
    //             $pattern,
    //             $pdf,
    //             $match,
    //             PREG_OFFSET_CAPTURE
    //         ) !== 1
    //     ) {
    //         /*
    //          * A later incremental revision may contain the same object
    //          * number. Return the LAST definition, not the first one.
    //          */
    //         preg_match_all(
    //             $pattern,
    //             $pdf,
    //             $matches,
    //             PREG_SET_ORDER
    //         );

    //         if (empty($matches)) {
    //             return null;
    //         }

    //         $match = end($matches);

    //         return [
    //             'num' => $objectNumber,
    //             'gen' => $generation,
    //             'body' => $match[1],
    //         ];
    //     }

    //     preg_match_all(
    //         $pattern,
    //         $pdf,
    //         $matches,
    //         PREG_SET_ORDER
    //     );

    //     if (empty($matches)) {
    //         return null;
    //     }

    //     $match = end($matches);

    //     return [
    //         'num' => $objectNumber,
    //         'gen' => $generation,
    //         'body' => $match[1],
    //     ];
    // }
    private function getIncrementalPdfObject(
        string $pdf,
        int $objectNumber,
        int $generation = 0
    ): ?array {
        $objects = $this->splitPdfObjectsRespectingStreams($pdf);

        $found = null;

        foreach ($objects as $obj) {
            if ((int) $obj[1] === $objectNumber && (int) $obj[2] === $generation) {
                // Keep the LAST definition (later revision wins).
                $found = [
                    'num' => $objectNumber,
                    'gen' => $generation,
                    'body' => $obj[3],
                ];
            }
        }

        return $found;
    }

    /**
     * Escape a value used inside a PDF literal string.
     */
    private function escapePdfString(string $value): string
    {
        return str_replace(
            ['\\', '(', ')', "\r", "\n"],
            ['\\\\', '\(', '\)', '', ''],
            $value
        );
    }

    /**
     * Format PDF coordinate values without unnecessary decimals.
     */
    private function formatPdfNumber(float $value): string
    {
        if (abs($value - round($value)) < 0.000001) {
            return (string) (int) round($value);
        }

        return rtrim(
            rtrim(
                number_format($value, 4, '.', ''),
                '0'
            ),
            '.'
        );
    }


}
