<?php

namespace App\Services;

use TCPDF;

class MYPDF extends TCPDF
{

  //Page header
  public function Header()
  {
    // Logo
    $image_file = K_PATH_IMAGES . 'logo_example.jpg';
    $this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    // Set font
    $this->SetFont('helvetica', 'B', 20);
    // Title
    // $this->Cell(0, 15, '<hr>', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    $this->writeHTMLCell(0, 0, '', '', '<br><hr>', 0, 1, 0, true, '', true);
  }

  // Page footer
  public function Footer()
  {
    // Position at 15 mm from bottom
    $this->SetY(-22);
    // Set font
    $this->SetFont('helvetica', 'I', 8);
    // Page number
    $this->writeHTMLCell(0, 0, '', '', '<div style="text-align:center;">Page ' . $this->getAliasNumPage() . ' sur ' . $this->getAliasNbPages() . '</div><hr><div></div><div style="text-align:center">LOT MASSIRA RESIDENCE COSTA DEL SOL ETG 4 APP 21 - Mohammedia, Maroc - Tel: +212 (0) 520354177 - Fax: +212 (0) 622878187 <br> Email: contact@hsprint.ma - ICE 003176911000018 - RC 32107 - TP 39580811 - IF 53244421</div>', 0, 1, 0, true, '', true);
  }
}
