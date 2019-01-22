<?php

namespace Esignature;

interface ICreateSigningType {
    function SigningType();
}

class CreateSigningType implements ICreateSigningType {
    private $SigningType,$MandatedSignerValidation;
    public function __construct($SigningType,$MandatedSignerValidation) {
        $this->SigningType = $SigningType;
        $this->MandatedSignerValidation = $MandatedSignerValidation;
    }
    public function SigningType() {
        $SigningType1 = [
            "SigningType"=>'manual',
            "CommitmentTypes"=>[],
            "MandatedSignerValidation"=>$this->MandatedSignerValidation,
            "MandatedSignerIds"=>[],
        ];
        $SigningType2 = [
            "SigningType"=>'mailotp',
            "CommitmentTypes"=>[],
            "MandatedSignerValidation"=>$this->MandatedSignerValidation,
            "MandatedSignerIds"=>[],
        ];
        $SigningType3 = [
            "SigningType"=>"smsotp",
            "CommitmentTypes"=>[],
            "MandatedSignerValidation"=>$this->MandatedSignerValidation,
            "MandatedSignerIds"=>[],
        ];
        $SigningType4 = [
            "SigningType"=>"beid",
            "CommitmentTypes"=>[],
            "MandatedSignerValidation"=>$this->MandatedSignerValidation,
            "MandatedSignerIds"=>[],
        ];
        $SigningTypes = [$SigningType1,$SigningType2,$SigningType3,$SigningType4];
        return $SigningTypes;
    }
}
?>
