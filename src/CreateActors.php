<?php
namespace Esignature;
interface ICreateActors {
    function Actors();
}
class CreateActors  {
    private $Type,$OrderIndex,$PhoneNumber,$LegalNoticeCode,$LegalNoticeText,$SigningFields,$SigningTypes,$RedirectUrl,$SendNotifications,$UserRoles;
    public function __construct($Type,$OrderIndex,$PhoneNumber,$LegalNoticeCode,$LegalNoticeText,$SigningFields,$SigningTypes,$RedirectUrl,$SendNotifications,$UserRoles) {
        $this->Type = $Type;
        $this->OrderIndex = $OrderIndex;
        $this->PhoneNumber = $PhoneNumber;
        $this->LegalNoticeCode = $LegalNoticeCode;
        $this->LegalNoticeText = $LegalNoticeText;
        $this->SigningFields = $SigningFields;
        $this->SigningTypes = $SigningTypes;
        $this->RedirectUrl = $RedirectUrl;
        $this->SendNotifications = $SendNotifications;
        $this->UserRoles = $UserRoles;
    }
    public function Actors() {
        $Actors =[
            "Type"=>$this->Type,
            "OrderIndex" =>$this->OrderIndex,
            "PhoneNumber"=> $this->PhoneNumber,
            "LegalNoticeCode"=>$this->LegalNoticeCode,
            "LegalNoticeText"=> $this->LegalNoticeText,
            "SigningFields"=>[$this->SigningFields],
            "SigningTypes"=>$this->SigningTypes,
            "RedirectUrl"=>$this->RedirectUrl,
            "SendNotifications"=>$this->SendNotifications,
            "UserRoles"=>$this->UserRoles,
        ];
        return $Actors;
    }
}
?>
