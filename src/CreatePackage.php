<?php
namespace Esignature; 
interface ICreatePackage { 
    function Package();
}
class CreatePackage implements ICreatePackage {
    private $PackageInfo,$Stakeholders;
    public function __construct($PackageInfo,$Stakeholders) {
        $this->PackageInfo = $PackageInfo;
        $this->Stakeholders = $Stakeholders;
    }
    public function Package() {
        $Document = $this->PackageInfo['PdfData'];
        $Package = [
            "Document"=>$Document,
            "Initiator"=>$this->PackageInfo['Initiator'],
            "CallBackUrl"=>$this->PackageInfo['CallBackUrl'],
            "DocumentLanguage"=>$this->PackageInfo['DocumentLanguage'],
            "DocumentName"=>$this->PackageInfo['DocumentName'],
            "NotificationCallBackUrl"=>$this->PackageInfo['NotificationCallBackUrl'],
            "DocumentGroupCode"=>$this->PackageInfo['DocumentGroupCode'],
            "ExpiryTimestamp"=>$this->PackageInfo['ExpiryTimestamp'],
            "ExternalPackageReference"=>$this->PackageInfo['ExternalPackageReference'],
            "ExternalPackageData"=>$this->PackageInfo['ExternalPackageData'],
            "F2FRedirectUrl"=>$this->PackageInfo['F2FRedirectUrl'],
            "ExternalDocumentReference"=>$this->PackageInfo['ExternalDocumentReference'],
            "Stakeholders"=>$this->Stakeholders,
            "TargetType" =>$this->PackageInfo['TargetType'],
            "PdfErrorHandling" =>$this->PackageInfo['PdfErrorHandling'],
            "SigningTemplateCode" =>$this->PackageInfo['SigningTemplateCode'],
            "CorrelationId" =>$this->PackageInfo['CorrelationId'],
        ];
        return $Package;
    }
}

?>
