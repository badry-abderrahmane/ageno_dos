<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use TCPDF;

class MYPDF extends TCPDF
{

  //Page header
  public function Header()
  {
    // Set font
    $this->SetFont('helvetica', 'B', 20);
    // Title
    // $this->Cell(0, 15, '<hr>', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    $this->writeHTMLCell(0, 0, '', '', '<br><hr>', 0, 1, 0, true, '', true);
  }

  // Page footer
  public function Footer()
  {
    $organization = Auth::user()->organization;
    // Position at 15 mm from bottom
    $this->SetY(-22);
    // Set font
    $this->SetFont('helvetica', 'I', 8);
    // Page number
    $this->writeHTMLCell(0, 0, '', '', '<div style="text-align:center;">Page ' . $this->getAliasNumPage() . ' sur ' . $this->getAliasNbPages() . '</div><hr><div></div><div style="text-align:center">' . $organization->org_footer . '</div>', 0, 1, 0, true, '', true);
  }
}
