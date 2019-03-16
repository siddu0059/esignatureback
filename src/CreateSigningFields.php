<?php
namespace Esignature;
interface ICreateSigningFields {
    function SigningFields();
}
class CreateSigningFields implements ICreateSigningFields {
    private $PageNumber,$Width,$Height,$Left,$Top;
    public function __construct($PageNumber,$Width,$Height,$Left,$Top,$Label,$MarkerOrFieldId) {
        $this->PageNumber = $PageNumber;
        $this->Width = $Width;
        $this->Height = $Height;
        $this->Left = $Left;
        $this->Top = $Top;
        $this->Label = $Label;
        $this->MarkerOrFieldId = $MarkerOrFieldId;
    }
    public function SigningFields() {
        $SigningFields =[
            "PageNumber"=>$this->PageNumber,
            "Width"=>$this->Width,
            "Height"=>$this->Height,
            "Left"=>$this->Left,
            "Top"=>$this->Top,
            "Label"=>$this->Label,
            "MarkerOrFieldId"=>$this->MarkerOrFieldId,            
        ];
        return $SigningFields;
    }
}
?>
