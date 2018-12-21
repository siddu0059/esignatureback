<?php
  namespace Esignature;
  use Esignature\CreatePackage;
  use Esignature\CreateStakeholders;
  use Esignature\CreateActors;
  use Esignature\CreateSigningFields;
  use Esignature\CreateSigningType;
  use Esignature\ValidateEsignaturePackage;
  $vendorDir = dirname(dirname(__FILE__));
  $baseDir = dirname($vendorDir);
  
  require($vendorDir . "/src/PdfToText-master/PdfToText.phpclass");
  class SendPackageRequest {
    private $Width,$Height,$Left,$Top,$MandatedSignerValidation,$Type,$OrderIndex,$LegalNoticeCode,$LegalNoticeText,$RedirectUrl,$SendNotifications,$FirstName,$LastName;
    private $EmailAddress,$Language,$BirthDate,$ExternalStakeholderReference,$Initiator,$CallBackUrl,$DocumentLanguage,$DocumentName,$NotificationCallBackUrl,$DocumentGroupCode;
    private $ExpiryTimestamp,$ExternalPackageReference,$ExternalPackageData,$F2FRedirectUrl,$ExternalDocumentReference,$TargetType,$PdfErrorHandling,$SigningTemplateCode,$CorrelationId,$DocumentPath;
    private $SigningTypesObject,$SigningFieldsObject,$SigningTypes,$SigningFields,$ActorsObject,$Actors,$StakeholdersObject,$Stakeholders,$PackageObject,$Package,$PageNumber,$JsonPakage;
    
    public function __construct($SigningInfo,$UserInfo,$ContractInfo,$cid,$documentInfo) {
      $this -> Width = $SigningInfo['width'];
      $this -> Height = $SigningInfo['height'];
      $this -> Left = $SigningInfo['left'];
      $this -> Top = $SigningInfo['top'];
      $this -> SigningType = $SigningInfo['signingtype'];
      $this -> MandatedSignerValidation = "";
      $this -> PhoneNumber = $UserInfo[0]->phcode.$UserInfo[0]->phno;
      $this -> Type = "Signer";
      $this -> OrderIndex = 1;
      $this -> LegalNoticeCode = "";
      $this -> LegalNoticeText = "";
      $this -> RedirectUrl = "http://d4c1f8c4.ngrok.io";
      $this -> SendNotifications = true; 
      $this -> FirstName = $UserInfo[0]->first_name;
      $this -> LastName = $UserInfo[0]->last_name;
      $this -> EmailAddress = $UserInfo[0]->email;
      $this -> Language = $UserInfo[0]->language;
      $this -> BirthDate = "";
      $this -> ExternalStakeholderReference = "";
      $this -> Initiator = "nagasiddeswara.infanion@gmail.com";
      $this -> CallBackUrl = "https://d4c1f8c4.ngrok.io/nl/signed-document";
      $this -> DocumentLanguage = $UserInfo[0]->language;
      $this -> DocumentName = $documentInfo['fileName'];
      $this -> NotificationCallBackUrl = "";
      $this -> DocumentGroupCode = "";
      $this -> ExpiryTimestamp = $ContractInfo[0]->contract_end_date;
      $this -> ExternalPackageReference = "";
      $this -> ExternalPackageData = "";
      $this -> F2FRedirectUrl = "";
      $this -> ExternalDocumentReference = "";
      $this -> TargetType = "";
      $this -> PdfErrorHandling = "";
      $this -> SigningTemplateCode = "";
      $this -> CorrelationId = "";
      $this -> DocumentPath = $documentInfo['s3FilePath'];
      $this -> DocumentPdf = new \PdfToText($this ->DocumentPath);
      $this -> PageNumber = count($this->DocumentPdf->Pages);
    }
    public function createObjects() {
      $ValidateEsignaturePackageObject = new ValidateEsignaturePackage();
      $ValidateEsignaturePackageObject -> ValidateSigningFields($this->Width,$this->Height,$this->Left,$this->Top);
      $ValidateEsignaturePackageObject -> ValidateSigningType($this->SigningType);
      $ValidateEsignaturePackageObject -> ValidateStakeholder($this->FirstName,$this->LastName,$this->Language,$this->EmailAddress);
      $ValidateEsignaturePackageObject -> ValidatePackage ($this->DocumentPath,$this->Initiator,$this->DocumentLanguage,$this->DocumentName,$this->ExpiryTimestamp);
      $this ->SigningTypesObject = new CreateSigningType($this->SigningType, $this->MandatedSignerValidation);
      $this ->SigningFieldsObject = new CreateSigningFields($this->PageNumber, $this->Width, $this ->Height,$this ->Left,$this ->Top);
      $this ->SigningTypes = $this ->SigningTypesObject->SigningType();
      $this ->SigningFields = $this ->SigningFieldsObject->SigningFields();
      $this ->ActorsObject = new CreateActors($this ->Type,$this->OrderIndex,$this ->PhoneNumber,$this ->LegalNoticeCode,$this ->LegalNoticeText,$this ->SigningFields,$this ->SigningTypes,$this ->RedirectUrl,$this ->SendNotifications,$this ->DocumentPath);
      $this ->Actors = $this ->ActorsObject->Actors();
      $this ->StakeholdersObject = new CreateStakeholders($this ->Actors,$this ->FirstName,$this ->LastName,$this ->EmailAddress,$this ->Language,$this ->BirthDate,$this ->ExternalStakeholderReference);
      $this ->Stakeholders = $this ->StakeholdersObject->Stakeholders();        
      $this ->PackageObject = new CreatePackage($this ->DocumentPath,$this ->Initiator,$this ->CallBackUrl,$this ->DocumentLanguage,$this ->DocumentName,$this ->NotificationCallBackUrl,$this ->DocumentGroupCode,$this ->ExpiryTimestamp,$this ->ExternalPackageReference,$this ->ExternalPackageData,$this ->F2FRedirectUrl,$this ->ExternalDocumentReference,$this ->Stakeholders,$this ->TargetType,$this ->PdfErrorHandling,$this ->SigningTemplateCode,$this ->CorrelationId);
      $this ->Package = $this->PackageObject->Package();
      
    }
    
    public function sendJsonObject($esignature,$esignature_pass) {
      $this->JsonPakage = json_encode($this->Package);
      $curl = curl_init();
      curl_setopt_array($curl, array (
        CURLOPT_URL => $esignature . "packages/instant",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $this->JsonPakage ,
        CURLOPT_HTTPHEADER => array(
          "Authorization: Basic ".$esignature_pass,
          "Content-Type: application/json",
          "cache-control: no-cache"
        ),
      ));
      $response = curl_exec($curl);
      $err = curl_error($curl);
      curl_close($curl);
      if ($err) {
        echo "CURL Error #:" . $err;
      } else {
        $response = json_decode($response,true);
        if(!empty($response['Errors'])) {
          echo "<h1>".$response['Errors'][0]['Message']."</h1>";
          exit;
        }
        print_r($response);
        redirect()->to($response['Stakeholders'][0]['Actors'][0]['ActionUrl'])->send();
      }
    }
  }
?>