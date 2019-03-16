<?php

namespace Esignature;

interface ICreateSigningType {
    function SigningType();
}

class CreateSigningType implements ICreateSigningType {
    private $SigningType,$MandatedSignerValidation;
    public function __construct($SigningTypes,$CommitmentTypes,$MandatedSignerValidation,$MandatedSignerIds) {
        $this->SigningTypes = $SigningTypes;
        $this->CommitmentTypes = $CommitmentTypes;
        $this->MandatedSignerValidation = $MandatedSignerValidation;
        $this->MandatedSignerIds = $MandatedSignerIds;
    }
    public function SigningType() {
        foreach ($this->SigningTypes as $key => $value) {
            $SigningType[$key] = [
                "SigningType"=>$value,
                "CommitmentTypes"=>$this->CommitmentTypes,
                "MandatedSignerValidation"=>$this->MandatedSignerValidation,
                "MandatedSignerIds"=>$this->MandatedSignerIds,
            ];
        }
        return $SigningType;
    }
}
?>
