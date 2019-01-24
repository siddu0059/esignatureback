<?php
namespace Esignature;
interface ICreateActors {
    function Actors();
}
class CreateActors  {
    private $Type,$OrderIndex,$PhoneNumber,$LegalNoticeCode,$LegalNoticeText,$RedirectUrl,$SendNotifications;
    public function __construct($Type,$OrderIndex,$PhoneNumber,$LegalNoticeCode,$LegalNoticeText,$SigningFields,$SigningTypes,$RedirectUrl,$SendNotifications) {
        $this->Type = $Type;
        $this->OrderIndex = $OrderIndex;
        $this->PhoneNumber = $PhoneNumber;
        $this->LegalNoticeCode = $LegalNoticeCode;
        $this->LegalNoticeText = $LegalNoticeText;
        $this->SigningFields = $SigningFields;
        $this->SigningTypes = $SigningTypes;
        $this->RedirectUrl = $RedirectUrl;
        $this->SendNotifications = $SendNotifications;
    }
    public function Actors($SigningFields,$SigningTypes,$userrole) {
        $Actors =[
            "Type"=>$this->Type,
            "OrderIndex" =>$this->OrderIndex,
            "PhoneNumber"=> $this->PhoneNumber,
            "LegalNoticeCode"=>$this->LegalNoticeCode,
            "LegalNoticeText"=> $this->LegalNoticeText,
            "SigningFields"=>[$SigningFields],
            "SigningTypes"=>$SigningTypes,
            "RedirectUrl"=>$this->RedirectUrl,
            "SendNotifications"=>$this->SendNotifications,
            "UserRoles"=>[$userrole],
        ];
        return $Actors;
    }
}
?>
