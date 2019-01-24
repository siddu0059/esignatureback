<?php
  namespace Esignature;
  use Esignature\CreatePackage;
  use Esignature\CreateStakeholders;
  use Esignature\CreateActors;
  use Esignature\CreateSigningFields;
  use Esignature\CreateSigningType;
  use Esignature\ValidateEsignaturePackage;
  use Esignature\PackageInfo;
  use Esignature\CreateCurlRequest;
  use Illuminate\Support\Facades\DB;
  use Config;
  use Auth;
  $vendorDir = dirname(dirname(__FILE__));
  $baseDir = dirname($vendorDir);
  
  require($vendorDir . "/src/PdfToText-master/PdfToText.phpclass");
  class SendPackageRequest {
    public function __construct($SigningInfoFirst,$SigningInfoSecond,$documentPath,$UsersInfo,$cid) {
      
      $this->SigningInfoFirst = $SigningInfoFirst;
      $this->SigningInfoSecond = $SigningInfoSecond;
      $this->documentPath = $documentPath;
      $this->UsersInfo = $UsersInfo;
      $this->cid = $cid;
    }

    public function createObjects() {
      $TenantPhoneNumber = '+' . $this->UsersInfo['credentials']['tenantDetails'][0]['phcode'].$this->UsersInfo['credentials']['tenantDetails'][0]['phno'];
      $TenantType = "Signer";
      $TenantOrderIndex = 0;
      $TenantLegalNoticeCode = "";
      $TenantLegalNoticeText = "";
      $TenantRedirectUrl = "http://daredevil.local/upadte-sigining-status-tenant/$this->cid";
      $TenantSendNotifications = true;
      $TenantFirstName = $this->UsersInfo['credentials']['tenantDetails'][0]['first_name'];
      $TenantLastName = $this->UsersInfo['credentials']['tenantDetails'][0]['last_name'];
      $TenantEmailAddress = $this->UsersInfo['credentials']['tenantDetails'][0]['email'];
      $TenantLanguage = Config::get('app.locale');
      $TenantBirthDate = "";
      $TenantExternalStakeholderReference = "tenant,".$TenantEmailAddress;
      $TenantSigningType = "manual";

      $OwnerPhoneNumber = '+' . $this->UsersInfo['credentials']['ownerDetails'][0]['phcode'].$this->UsersInfo['credentials']['tenantDetails'][0]['phno'];
      $OwnerType = "Signer";
      $OwnerOrderIndex = 1;
      $OwnerLegalNoticeCode = "";
      $OwnerLegalNoticeText = "";
      $OwnerRedirectUrl = "http://daredevil.local/upadte-sigining-status-owner/$this->cid";
      $OwnerSendNotifications = true; 
      $OwnerFirstName = $this->UsersInfo['credentials']['ownerDetails'][0]['first_name'];
      $OwnerLastName = $this->UsersInfo['credentials']['ownerDetails'][0]['last_name'];
      $OwnerEmailAddress = $this->UsersInfo['credentials']['ownerDetails'][0]['email'];
      $OwnerLanguage = Config::get('app.locale');
      $OwnerBirthDate = "";
      $OwnerExternalStakeholderReference = "owner,".$OwnerEmailAddress;
      $OwnerSigningType = "manual";

      $MandatedSignerValidation = "";
      $Initiator = env('EINITIATOR'); //"nagasiddeswara.infanion@gmail.com";
      $CallBackUrl = env('APP_URL') . "/signed-document";
      $DocumentLanguage = Config::get('app.locale');
      $DocumentName = "contract";
      $NotificationCallBackUrl = "";
      $DocumentGroupCode = "";
      $ExpiryTimestamp = '2022-01-03';
      $ExternalPackageReference = "";
      $ExternalPackageData = "";
      $F2FRedirectUrl = "";
      $ExternalDocumentReference = "";
      $TargetType = "";
      $PdfErrorHandling = "";
      $SigningTemplateCode = "";
      $CorrelationId = "";
      $DocumentPath = $this->documentPath;
      $DocumentPdf = new \PdfToText($DocumentPath);
      $PageNumber = count($DocumentPdf->Pages);
 
      $WidthFirst = $this->SigningInfoFirst['width'];
      $HeightFirst = $this->SigningInfoFirst['height'];
      $TopFirst = $this->SigningInfoFirst['top'];
      $LeftFirst = $this->SigningInfoFirst['left'];
      $SignTypeFirst = $this->SigningInfoFirst['signingtype'];

      $WidthSecond = $this->SigningInfoSecond['width'];
      $HeightSecond = $this->SigningInfoSecond['height'];
      $TopSecond = $this->SigningInfoSecond['top'];
      $LeftSecond = $this->SigningInfoSecond['left'];
      $SignTypeSecond = $this->SigningInfoSecond['signingtype'];

      $SigningFieldsObjectFist = new CreateSigningFields($PageNumber,$WidthFirst,$HeightFirst,$LeftFirst,$TopFirst);
      $SigningFieldsObjectSecond = new CreateSigningFields($PageNumber,$WidthSecond,$HeightSecond,$LeftSecond,$TopSecond);
      $SigningFieldsFirst = $SigningFieldsObjectFist ->SigningFields();
      $SigningFieldsSecond = $SigningFieldsObjectSecond ->SigningFields();

      $SigningTypesObjectFirst = new CreateSigningType($TenantSigningType, $MandatedSignerValidation);
      $SigningTypesObjectSecond = new CreateSigningType($OwnerSigningType, $MandatedSignerValidation);
      $SigningTypesFirst = $SigningTypesObjectFirst->SigningType();
      $SigningTypesSecond = $SigningTypesObjectSecond->SigningType();

      $ActorsObjectFirst = new CreateActors($TenantType,$TenantOrderIndex,$TenantPhoneNumber,$TenantLegalNoticeCode,$TenantLegalNoticeText,$SigningFieldsFirst,$SigningTypesFirst,$TenantRedirectUrl,$TenantSendNotifications);
      $ActorsObjectSecond = new CreateActors($OwnerType,$OwnerOrderIndex,$OwnerPhoneNumber,$OwnerLegalNoticeCode,$OwnerLegalNoticeText,$SigningTypesSecond,$SigningTypesSecond,$OwnerRedirectUrl,$OwnerSendNotifications);
      $ActorsFirst = $ActorsObjectFirst ->Actors($SigningFieldsFirst,$SigningTypesFirst,"tenant");
      $ActorsSecond = $ActorsObjectSecond ->Actors($SigningFieldsSecond,$SigningTypesSecond,"owner");

      $StakeholdersObjectFirst = new CreateStakeholders($ActorsFirst,$TenantFirstName,$TenantLastName,$TenantEmailAddress,$TenantLanguage,$TenantBirthDate,$TenantExternalStakeholderReference);
      $StakeholdersObjectSecond = new CreateStakeholders($ActorsSecond,$OwnerFirstName,$OwnerLastName,$OwnerEmailAddress,$OwnerLanguage,$OwnerBirthDate,$OwnerExternalStakeholderReference);
      $StakeholderFirst = $StakeholdersObjectFirst ->Stakeholders();
      $StakeholderSecond = $StakeholdersObjectSecond ->Stakeholders();

      $PackageObject = new CreatePackage($DocumentPath,$Initiator,$CallBackUrl,$DocumentLanguage,$DocumentName,$NotificationCallBackUrl,$DocumentGroupCode,$ExpiryTimestamp,$ExternalPackageReference,$ExternalPackageData,$F2FRedirectUrl,$ExternalDocumentReference,$StakeholderFirst,$StakeholderSecond,$TargetType,$PdfErrorHandling,$SigningTemplateCode,$CorrelationId);
      $Package = $PackageObject->Package();
      $JsonPakage = json_encode($Package);
      $CreatePackageUrl = env('ESIGNATURE')."packages/instant";
      $EsignaturePassword = env('ESIGNATURE_PASS');
      $CreatePackageMethod = "POST";
      $createCurlRequestObject = new CreateCurlRequest();
      //$createCurlRequestObject ->phonenumberValidation("+9182916254");
      $curlResponse = $createCurlRequestObject ->curlRequest($CreatePackageUrl,$EsignaturePassword,$CreatePackageMethod,$JsonPakage);
      unlink($this->documentPath);
      return $curlResponse;
    }
  }
?>
