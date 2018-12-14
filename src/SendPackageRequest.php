<?php
    use Esignature\CreatePackage;
    
    #include('CreatePackage.php');
    use Esignature\CreateStakeholders;
    use Esignature\CreateActors;
    use Esignature\CreateSigningFields;
    use Esignature\CreateSigningType;
    #require( 'PdfToText-master/PdfToText.phpclass' );
    $Width = 150;
    $Height = 100;
    $Left = 360;
    $Top = 650;
    $SigningType = "smsotp";
    $MandatedSignerValidation = "";
    $Type = "Signer";
    $OrderIndex = 1;
    $PhoneNumber = "+918296616254";
    $LegalNoticeCode = "";
    $LegalNoticeText = "";
    $RedirectUrl = "";
    $SendNotifications = true; 
    $FirstName = "shanth";
    $LastName = "kumar";
    $EmailAddress = "shanth1997.sk@gmail.com";
    $Language = "en";
    $BirthDate = "2018-11-29T07:18:52.548Z";
    $ExternalStakeholderReference = "";
    $Initiator = "nagasiddeswara.infanion@gmail.com";
    $CallBackUrl = "https://www.google.com";
    $DocumentLanguage = "nl";
    $DocumentName = "shanth";
    $NotificationCallBackUrl = "";
    $DocumentGroupCode = "";
    $ExpiryTimestamp = "2019-01-23T12:34:00.000Z";
    $ExternalPackageReference = "";
    $ExternalPackageData = "";
    $F2FRedirectUrl = "";
    $ExternalDocumentReference = "";
    $TargetType = "";
    $PdfErrorHandling = "";
    $SigningTemplateCode = "";
    $CorrelationId = "";
    $DocumentPath = "/home/siddu/Download/demotest.php";
    #$DocumentPdf = new PdfToText($DocumentPath);
    $PageNumber = 30;//count($DocumentPdf->Pages);

    $SigningTypesObject = new CreateSigningType($SigningType,$MandatedSignerValidation);
    $SigningFieldsObject = new CreateSigningFields($PageNumber,$Width,$Height,$Left,$Top);
    $SigningTypes = $SigningTypesObject->SigningType();
    $SigningFields = $SigningFieldsObject->SigningFields();
    
    $ActorsObject = new CreateActors($Type,$OrderIndex,$PhoneNumber,$LegalNoticeCode,$LegalNoticeText,$SigningFields,$SigningTypes,$RedirectUrl,$SendNotifications,$DocumentPath);
    $Actors = $ActorsObject->Actors();

    $StakeholdersObject = new CreateStakeholders($Actors,$FirstName,$LastName,$EmailAddress,$Language,$BirthDate,$ExternalStakeholderReference);
    $Stakeholders = $StakeholdersObject->Stakeholders();
    
    $PackageObject = new CreatePackage($DocumentPath,$Initiator,$CallBackUrl,$DocumentLanguage,$DocumentName,$NotificationCallBackUrl,$DocumentGroupCode,$ExpiryTimestamp,$ExternalPackageReference,$ExternalPackageData,$F2FRedirectUrl,$ExternalDocumentReference,$Stakeholders,$TargetType,$PdfErrorHandling,$SigningTemplateCode,$CorrelationId);
    $Package = $PackageObject->Package();
    $JsonPakage = json_encode($Package);
    
    $curl = curl_init();
    curl_setopt_array($curl, array (
        CURLOPT_URL => "https://portaldemo14.connective.eu/webportalapi/v3/packages/instant",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $JsonPakage ,
        CURLOPT_HTTPHEADER => array(
          "Authorization: Basic UG9ydGFsVEE6c2laVWYlKHB9UHh3bWgwV1ZeJS8=",
          "Content-Type: application/json",
          "Postman-Token: 119af311-93aa-492f-a4c1-3bdd2a96389a",
          "cache-control: no-cache"
        ),
      ));
      $response = curl_exec($curl);
      $err = curl_error($curl);
      
      curl_close($curl);
      
      if ($err) {
        echo "cURL Error #:" . $err;
      } else {
        echo $response;
      }

?>
