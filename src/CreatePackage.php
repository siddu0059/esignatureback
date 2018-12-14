<?php
namespace Esignature; 
interface ICreatePackage { 
    function Package();
}
class CreatePackage implements ICreatePackage {
    private $DocumentPath,$Initiator,$CallBackUrl,$DocumentLanguage,$DocumentName,$NotificationCallBackUrl,$DocumentGroupCode,$ExpiryTimestamp,$ExternalPackageReference,$ExternalPackageData,$F2FRedirectUrl,$ExternalDocumentReference,$Stakeholders,$TargetType,$PdfErrorHandling,$SigningTemplateCode,$CorrelationId;
    public function __construct($DocumentPath,$Initiator,$CallBackUrl,$DocumentLanguage,$DocumentName,$NotificationCallBackUrl,$DocumentGroupCode,$ExpiryTimestamp,$ExternalPackageReference,$ExternalPackageData,$F2FRedirectUrl,$ExternalDocumentReference,$Stakeholders,$TargetType,$PdfErrorHandling,$SigningTemplateCode,$CorrelationId) {
        $this->DocumentPath = $DocumentPath;
        $this->Initiator = $Initiator;
        $this->CallBackUrl = $CallBackUrl;
        $this->DocumentLanguage = $DocumentLanguage;
        $this->DocumentName = $DocumentName;
        $this->NotificationCallBackUrl = $NotificationCallBackUrl;
        $this->DocumentGroupCode = $DocumentGroupCode;
        $this->ExpiryTimestamp = $ExpiryTimestamp;
        $this->ExternalPackageReference = $ExternalPackageReference;
        $this->ExternalPackageData = $ExternalPackageData;
        $this->F2FRedirectUrl = $F2FRedirectUrl;
        $this->ExternalDocumentReference = $ExternalDocumentReference;
        $this->Stakeholders = $Stakeholders;
        $this->TargetType = $TargetType;
        $this->PdfErrorHandling = $PdfErrorHandling;
        $this->SigningTemplateCode = $SigningTemplateCode;
        $this->CorrelationId = $CorrelationId;
    }
    public function Package() {
        $Document = base64_encode(file_get_contents($this->DocumentPath));
        $Package = [
            "Document"=>$Document,
            "Initiator"=>$this->Initiator,
            "CallBackUrl"=>$this->CallBackUrl,
            "DocumentLanguage"=>$this->DocumentLanguage,
            "DocumentName"=>$this->DocumentName,
            "NotificationCallBackUrl"=>$this->NotificationCallBackUrl,
            "DocumentGroupCode"=>$this->DocumentGroupCode,
            "ExpiryTimestamp"=>$this->ExpiryTimestamp,
            "ExternalPackageReference"=>$this->ExternalPackageReference,
            "ExternalPackageData"=>$this->ExternalPackageData,
            "F2FRedirectUrl"=>$this->F2FRedirectUrl,
            "ExternalDocumentReference"=>$this->ExternalDocumentReference,
            "Stakeholders"=>[$this->Stakeholders],
            "TargetType" =>$this->TargetType,
            "PdfErrorHandling" =>$this->PdfErrorHandling,
            "SigningTemplateCode" =>$this->SigningTemplateCode,
            "CorrelationId" =>$this->CorrelationId,
        ];
        return $Package;
    }
}

?>
