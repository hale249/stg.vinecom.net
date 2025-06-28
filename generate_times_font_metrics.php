<?php
require 'vendor/autoload.php';

use Dompdf\FontLib\Font;
use Dompdf\FontLib\Adapter\File as FontFile;

$fontDir = __DIR__ . '/storage/fonts/';
$fontFile = $fontDir . 'times-new-roman.ttf';
$fontName = 'times-new-roman';

if (!file_exists($fontFile)) {
    die("Font file not found: $fontFile\n");
}

// Load the font
$font = Font::load($fontFile);
$font->saveAdobeFontMetrics($fontDir . $fontName . '.ufm');
$font->saveFontMetrics($fontDir . $fontName . '.php');

echo "Font metrics generated for Times New Roman!\n"; 