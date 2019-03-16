<?php
namespace Esignature;
use DateTime;
class CreateCurlRequest {
    private $url;
    private $request;
    private $postfields;
    private $esignaturePass;
    private $responce;
    private $err;
    // public function __construct() {
    //     $this->url = $url;
    //     $this->esignaturePass = $esignaturePass;
    //     $this->request = $request;
    //     $this->postfields =$postfields;
    // }
    public function curlRequest($url,$esignaturePass,$request,$postfields) {
        $this->url = $url;
        
        $this->esignaturePass = $esignaturePass;
        $this->request = $request;
        $this->postfields =$postfields;
        $curl = curl_init();
        curl_setopt_array($curl, array (
        CURLOPT_URL => $this->url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $this->request,
        CURLOPT_POSTFIELDS => $this->postfields ,
        CURLOPT_HTTPHEADER => array(
            "Authorization: Basic ".$this->esignaturePass,
            "Content-Type: application/json",
            "cache-control: no-cache"
        ),
        ));
        $response = curl_exec($curl);
        $response = json_decode($response,true);
        $err = curl_error($curl);
        curl_close($curl);
        return array('response'=>$response,'err'=>$err);
    }
    public function downloadDocumnet($downloadUrl) {
        $curl = curl_init();
        curl_setopt_array($curl, array (
        CURLOPT_URL => $downloadUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            "Authorization: Basic ".env('ESIGNATURE_PASS'),
            "cache-control: no-cache"
        ),
        ));
        $data = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        return $data;
    }
    public function phonenumberValidation($phno){
        $CreatePackageUrl = env('ESIGNATURE')."packages/instant";
        $EsignaturePassword = env('ESIGNATURE_PASS');
        $CreatePackageMethod = "POST";
        $document = "SW5kaWE=";
        $stackholder = [["Actors"=>[["Type"=>"Signer","OrderIndex"=>1,"PhoneNumber"=>$phno,"SigningFields"=>[["PageNumber"=>1,"Width"=>150,"Height"=>100,"Left"=>80,"Top"=>650]],"SigningTypes"=>[["SigningType"=>"smsotp",]],"SendNotifications"=>false]],"FirstName"=>"F","LastName"=>"L","EmailAddress"=>"sample@gmail.com","Language"=>"en"]];
        $request =["Document"=>$document,"Initiator"=>env('EINITIATOR'),"DocumentLanguage"=>"en","DocumentName"=>"s","ExpiryTimestamp"=>date('Y-m-d\TH:m:s.000\Z',strtotime("+1 hours")),"Stakeholders"=> $stackholder];
        $JsonPakage = json_encode($request);
        $responce = $this ->curlRequest($CreatePackageUrl,$EsignaturePassword,$CreatePackageMethod,$JsonPakage);

        if(isset($responce['response']['PackageId'])) {
            return "true";
        }
        else if(isset($responce['err']) && $responce['err']!= '') {
            $phnoValidation = new CreateCurlRequest();
            $phnoValidation->phonenumberValidation($phno);
        }
        else {
            return "false";
        }
    }
}
?>