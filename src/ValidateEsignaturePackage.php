<?php
namespace Esignature;
class ValidateEsignaturePackage {
    public function ValidateSigningFields($Width,$Height,$Left,$Top) {
        if($Top == null) {
            throw new Exception("Top cannot be empty");
        }
        else if($Left == null) {
            throw new Exception ("LastLeftName cannot be empty");
        }
        else if($Height == null) {
            throw new Exception ("Height cannot be empty");
        } 
        else if($Width == null) {
            throw new Exception ("Width cannot be empty");
        }
        else {
            return true;
        }
    }
    public function ValidateSigningType($SigningType) {
        if($SigningType == null) {
            throw new Exception ("SigningType cannot be empty");
        }
    }
    public function ValidateActor($Type,$PhoneNumber,$SigningType) {
        if($Type == null) {
            throw new Exception ("Type cannot be empty");
        }
        else {
            if($SigningType == 'smsotp') {
                if($PhoneNumber == null) {
                    throw new Exception ("PhoneNumber cannot be empty");
                }
                else {
                    return true;
                }
            }
            else {
                return true;
            }
        } 
    }
    public function ValidateStakeholder($FirstName,$LastName,$Language,$EmailAddress) {
        if($FirstName == null) {
            throw new Exception ("FirstName cannot be empty");
        }
        else if($LastName == null) {
            throw new Exception ("LastName cannot be empty");
        }
        else if($EmailAddress == null) {
            throw new Exception ("EmailAddress cannot be empty");
        }
        else  {
            if($Language == null) {
                $Language = "en";
            }
            return true;
        }
    }
    public function ValidatePackage ($DocumentPath,$Initiator,$DocumentLanguage,$DocumentName,$ExpiryTimestamp) {
        if($DocumentPath == null) {
            throw new Exception ("DocumentPath cannot be empty");
        }
        else if($ExpiryTimestamp == null) {
            throw new Exception ("ExpiryTimestamp cannot be empty");
        }
        else if($DocumentName == null) {
            throw new Exception ("DocumentName cannot be empty");
        }
        else if($Initiator == null) {
            throw new Exception ("Initiator cannot be empty");
        }
        else {
            if($DocumentLanguage == null) {
                $DocumentLanguage == "en";
            }
            return true;
        }
    }
}
?>