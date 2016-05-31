use \Milon\Barcode\DNS1D;

 

	echo '<img src="' . DNS2D::getBarcodePNG("4445645656", "QRCODE",3,33) . '" alt="barcode"   />';
 echo DNS2D::getBarcodeHTML("4445645656", "QRCODE");