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
        $SigningType = [
            "SigningType"=>$this->SigningType,
            "CommitmentTypes"=>[],
            "MandatedSignerValidation"=>$this->MandatedSignerValidation,
            "MandatedSignerIds"=>[],
        ];
        return $SigningType;
    }
}
?>
