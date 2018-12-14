<?php
namespace Esignature;
interface ICreateStakeholders {
    function Stakeholders();
}
class CreateStakeholders implements ICreateStakeholders{
    private $Actors,$FirstName,$LastName,$EmailAddress,$Language,$BirthDate,$ExternalStakeholderReference;
    public function __construct($Actors,$FirstName,$LastName,$EmailAddress,$Language,$BirthDate,$ExternalStakeholderReference) {
        $this->Actors = $Actors;
        $this->FirstName = $FirstName;
        $this->LastName = $LastName;
        $this->EmailAddress = $EmailAddress;
        $this->Language = $Language;
        $this->BirthDate = $BirthDate;
        $this->ExternalStakeholderReference = $ExternalStakeholderReference;
    }
    public function Stakeholders() {
        $Stakeholders = [
            "Actors"=>[$this->Actors],
            "FirstName"=> $this->FirstName,
            "LastName"=> $this->LastName,
            "EmailAddress"=> $this->EmailAddress,
            "Language"=>$this->Language,
            "BirthDate"=>$this->BirthDate,
            "ExternalStakeholderReference"=> $this->ExternalStakeholderReference,
        ];
        return $Stakeholders;
    }
}
?>
