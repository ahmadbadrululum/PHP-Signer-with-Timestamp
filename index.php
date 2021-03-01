<?php
require_once('vendor/autoload.php');


$filename = '/var/www/layanan-integrasi/storage/app/public/signature/data-pemohon/belum-ttd/certificate-9082572946011386fc30bc0051503586.pdf';
$tsUrl = 'https://freetsa.org/tsr';   // A RFC3161 conform Timestamp server

$tsUsername = NULL; // The username (if required)
$tsPassword = '';   // The password (if required)

// create a Http writer
$writer = new SetaPDF_Core_Writer_File('pdf/signer-timestamp.pdf');
// load document by filename
$document = SetaPDF_Core_Document::loadByFilename($filename, $writer);

// create a signer instance for the document
$signer = new SetaPDF_Signer($document);

// set the reserved space for the final signature to a higher amount because of the timestamp signature and certificate
$signer->setSignatureContentLength(20000);

// set some signature properties
$signer->setReason("Kementerian Komunikasi dan Informatika");
$signer->setLocation('kominfo.go.id');
$signer->setContactInfo('021-34830963');

// create an instance
$module = new SetaPDF_Signer_Signature_Module_Cms();

// set the sign certificate
$module->setCertificate(file_get_contents('/var/www/layanan-integrasi/storage/app/public/signature/signature_file-baba-dummypub.pem'));
// set the private key for the sign certificate
$module->setPrivateKey([file_get_contents('/var/www/layanan-integrasi/storage/app/public/signature/signature_file-baba-dummykey.pem'), '12345678']);

// create an instance of a time stamp module
$tsModule = new SetaPDF_Signer_Timestamp_Module_Rfc3161_Curl($tsUrl);

// if you have to authenticate with username and password...
if (isset($tsUsername)) {
    $tsModule->setCurlOption(CURLOPT_USERPWD, $tsUsername . ':' . $tsPassword);
}
// Attach the module to the signer
$signer->setTimestampModule($tsModule);

$signer->sign($module);