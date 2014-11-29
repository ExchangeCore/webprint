<?php
namespace exchangecore\webprint\src\barcodes;

class code39
{
    /**
     * Code 39 format 2 specifications
     */
    const BAR_WIDE = "11";
    const BAR_NARROW = "10";
    const SPACE_WIDE = "00";
    const SPACE_NARROW = "01";

    const WIDE_BAR_RATIO = 3;

    const TEXT_POS_NONE = 'none';
    const TEXT_POS_BELOW = 'below';

    /**
     * Character to encoding mapping
     * @var array $codes
     */
    protected $codes = [
        '0' => [
            'code'  => '101001101101',
            'check' => 0,
        ],
        '1' => [
            'code'  => '110100101011',
            'check' => 1,
        ],
        '2' => [
            'code'  => '101100101011',
            'check' => 2,
        ],
        '3' => [
            'code'  => '110110010101',
            'check' => 3,
        ],
        '4' => [
            'code'  => '101001101011',
            'check' => 4,
        ],
        '5' => [
            'code'  => '110100110101',
            'check' => 5,
        ],
        '6' => [
            'code'  => '101100110101',
            'check' => 6,
        ],
        '7' => [
            'code'  => '101001011011',
            'check' => 7,
        ],
        '8' => [
            'code'  => '110100101101',
            'check' => 8,
        ],
        '9' => [
            'code'  => '101100101101',
            'check' => 9,
        ],
        'A' => [
            'code'  => '110101001011',
            'check' => 10,
        ],
        'B' => [
            'code'  => '101101001011',
            'check' => 11,
        ],
        'C' => [
            'code'  => '110110100101',
            'check' => 12,
        ],
        'D' => [
            'code'  => '101011001011',
            'check' => 13,
        ],
        'E' => [
            'code'  => '110101100101',
            'check' => 14,
        ],
        'F' => [
            'code'  => '101101100101',
            'check' => 15,
        ],
        'G' => [
            'code'  => '101010011011',
            'check' => 16,
        ],
        'H' => [
            'code'  => '110101001101',
            'check' => 17,
        ],
        'I' => [
            'code'  => '101101001101',
            'check' => 18,
        ],
        'J' => [
            'code'  => '101011001101',
            'check' => 19,
        ],
        'K' => [
            'code'  => '110101010011',
            'check' => 20,
        ],
        'L' => [
            'code'  => '101101010011',
            'check' => 21,
        ],
        'M' => [
            'code'  => '110110101001',
            'check' => 22,
        ],
        'N' => [
            'code'  => '101011010011',
            'check' => 23,
        ],
        'O' => [
            'code'  => '110101101001',
            'check' => 24,
        ],
        'P' => [
            'code'  => '101101101001',
            'check' => 25,
        ],
        'Q' => [
            'code'  => '101010110011',
            'check' => 26,
        ],
        'R' => [
            'code'  => '110101011001',
            'check' => 27,
        ],
        'S' => [
            'code'  => '101101011001',
            'check' => 28,
        ],
        'T' => [
            'code'  => '101011011001',
            'check' => 29,
        ],
        'U' => [
            'code'  => '110010101011',
            'check' => 30,
        ],
        'V' => [
            'code'  => '100110101011',
            'check' => 31,
        ],
        'W' => [
            'code'  => '110011010101',
            'check' => 32,
        ],
        'X' => [
            'code'  => '100101101011',
            'check' => 33,
        ],
        'Y' => [
            'code'  => '110010110101',
            'check' => 34,
        ],
        'Z' => [
            'code'  => '100110110101',
            'check' => 35,
        ],
        '-' => [
            'code'  => '100101011011',
            'check' => 36,
        ],
        '.' => [
            'code'  => '110010101101',
            'check' => 37,
        ],
        ' ' => [
            'code'  => '100110101101',
            'check' => 38,
        ],
        '$' => [
            'code'  => '100100100101',
            'check' => 39,
        ],
        '/' => [
            'code'  => '100100101001',
            'check' => 40,
        ],
        '+' => [
            'code'  => '100101001001',
            'check' => 41,
        ],
        '%' => [
            'code'  => '101001001001',
            'check' => 42,
        ],
        '*' => [
            'code'  => '100101101101',
            'check' => 0,
        ],
    ];

    protected $barWidth = 3;
    protected $barcodeColor = array(0, 0, 0);
    protected $barcodeHeight = 80;
    protected $barcodeTextPosition = self::TEXT_POS_NONE;
    protected $barcodeTextSize = 8;

    /**
     * Width of thin bars in barcode
     * @param int $barWidth
     */
    public function setBarWidth($barWidth)
    {
        $this->barWidth = $barWidth;
    }

    /**
     * The height of the barcode
     * @param int $barcodeHeight
     */
    public function setBarcodeHeight($barcodeHeight)
    {
        $this->barcodeHeight = $barcodeHeight;
    }

    /**
     * Determines the position of the barcode text, use constants self::TEXT_POS_*
     * @param string $barcodeTextPosition
     */
    public function setBarcodeTextPosition($barcodeTextPosition)
    {
        $this->barcodeTextPosition = $barcodeTextPosition;
    }

    /**
     * Barcode text size
     * @param int $barcodeTextSize
     */
    public function setBarcodeTextSize($barcodeTextSize)
    {
        $this->barcodeTextSize = $barcodeTextSize;
    }

    /**
     * Draw the barcode and return it as an image resource
     * @param string $string The string to create a barcode for
     * @return resource|bool Returns the image resource or false if the image could not be created
     */
    public function draw($string) {
        if(!function_exists("imagegif")) {
            return false;
        }

        $i = 0;
        $bars = array();
        $barcodeText = '';
        $position = 0;
        while($i < strlen($string)) {
            $char = $string[$i++];
            if(isset($this->codes[$char])) {
                $barcodeText .= $char;
                $code = $this->codes[$char];

                $b = 0;
                while($b < strlen($code['code'])) {
                    if($b == strlen($code['code']) -1) {
                        $barEncoding = $code['code'][$b] . '0';
                    } else {
                        $barEncoding = $code['code'][$b] . $code['code'][$b + 1];
                    }
                    if($barEncoding === self::BAR_WIDE || $barEncoding === self::SPACE_WIDE) {
                        $width = $this->barWidth * self::WIDE_BAR_RATIO;
                        $b++;
                    } else {
                        $width = $this->barWidth;
                    }
                    if($barEncoding === self::BAR_WIDE || $barEncoding === self::BAR_NARROW) {
                        $bars[] = [$position, $position + $width];
                    }
                    $position += $width + $this->barWidth;
                    $b++;
                }
            }
        }
        ob_start();
        $img = imagecreatetruecolor($position, $this->barcodeHeight);
        imagefilledrectangle($img, 0, 0, $position, $this->barcodeHeight, 0xFFFFFF);
        $barColor = imagecolorallocate($img, 0, 0, 0);
        foreach($bars AS $bar) {
            imagefilledrectangle($img, $bar[0], 0, $bar[1], $this->barcodeHeight, $barColor);
        }
        imagegif($img);
        $imageString = base64_encode(ob_get_contents());
        imagedestroy($img);
        ob_end_clean();

        // check if using barcode text
       /* if($this->barcode_text) {
            // set barcode text box
            $barcode_text_h = 10 + $this->barcode_padding;
            imagefilledrectangle($img, $this->barcode_padding, $this->barcode_height - $this->barcode_padding - $barcode_text_h,
                $bc_w - $this->barcode_padding, $this->barcode_height - $this->barcode_padding, $_fff);

            // set barcode text font params
            $font_size = $this->barcode_text_size;
            $font_w = imagefontwidth($font_size);
            $font_h = imagefontheight($font_size);

            // set text position
            $txt_w = $font_w * strlen($barcode_string);
            $pos_center = ceil((($bc_w - $this->barcode_padding) - $txt_w) / 2);

            // set text color
            $txt_color = imagecolorallocate($img, 0, 255, 255);

            // draw barcod text
            imagestring($img, $font_size, $pos_center, $this->barcode_height - $barcode_text_h - 2,
                $barcode_string, $_000);
        }*/

        return $imageString;
    }

} 