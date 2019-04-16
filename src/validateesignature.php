<?php
namespace Esignature;

use Illuminate\Support\Facades\DB;
use Auth;
use Illuminate\Support\Arr;
class validateesignature {
    private $sign_disable = "";
    private $action_url = "";
    private $download_url = "";
    private $esignature_message = "";
    private $sign_show = "";
    private $signed = "";
    private $pending = "";
    private $actorstatus = "";
    private $packagestatus = "";
    public function proceedSignature($cid,$userrole,$useremail,$userid = '') {
    $this ->contarct_status = DB::table('contracts as c')->join('status as s', 's.id', '=', 'c.status')->where('unique_key', $cid)->select('c.status', 's.name')->get()->toarray();
        
        if ($userrole == "tenant") {
            if($this ->contarct_status[0]->status == 3 || $this ->contarct_status[0]->status == 9 || $this ->contarct_status[0]->status == 13) {
                self::getvalues($cid,$userrole,$useremail);
            }
            else {
                self::cantSign($cid);
            }
        } 
        else if($userrole == "property owner"){
            if($this ->contarct_status[0]->status == 9 || $this ->contarct_status[0]->status == 13) {
                self::getvalues($cid,$userrole,$useremail);
            }
            else {
                self::cantSign($cid);
            }
        }
        else {
            if($this ->contarct_status[0]->status == 9 || $this ->contarct_status[0]->status == 13) {
                self::getvalues($cid,$userrole,$useremail);
            }
            else {
                self::cantSign($cid);
            }
        }
        return [$this ->sign_disable,$this ->action_url,$this ->download_url,$this ->esignature_message,$this ->sign_show,$this ->signed,$this ->pending,$this ->actorstatus,$this ->packagestatus];

    }

    public function getvalues($cid,$userrole,$useremail) {
        $esignature_package_info = DB::table('esignature_package')->select('package_id','package_status', 'document_id')->where('contract_id', $cid)->get()->toarray();
        if(count($esignature_package_info) == 0) {
            $this ->sign_disable = "false";
            $this ->sign_show = "true";
            $this ->action_url = "";
            $this ->download_url = "/download/unsigned-document/".$cid;
            $this ->esignature_message = "Signature is pending from all the parties";
            $this ->actorstatus = "Available";
            $this ->packagestatus = "Pending";
        }
        elseif($this ->contarct_status[0]->status == 13 || $esignature_package_info[0]->package_status == 'Finished') {
            $esignature_info = DB::table('esignature_actor as ea')->join('esignature_package as ep', 'ea.package_id', 'ep.package_id')->select('ea.actor_role', 'ea.actor_status', 'ep.package_id', 'ep.document_id', 'ep.download_url', 'ep.contract_id', 'ep.package_status')->where('ea.actor_mail', $useremail)->where('ep.contract_id', $cid)->get()->toarray();
            $this ->sign_disable = "true";
            $this ->sign_show = "false";
            $this ->download_url = $download_url = "/download/signed-document/".$esignature_package_info[0]->package_id."/".$esignature_package_info[0]->document_id."/".$cid;
            $this ->sign_show = "false";
            $this ->esignature_message = "Signed by all parties";
            $this ->actorstatus = "SIGNED";
            $this ->packagestatus = "Finished";
        }
        else {
            $esignature_info = DB::table('esignature_actor as ea')->join('esignature_package as ep', 'ea.package_id', 'ep.package_id')->select('ea.actor_role', 'ea.actor_status', 'ep.package_id', 'ep.document_id', 'ep.download_url', 'ep.contract_id', 'ep.package_status')->where('ea.actor_mail', $useremail)->where('ep.contract_id', $cid)->get()->toarray();
            $actorstatus = $esignature_info[0]->actor_status;
            if($actorstatus == "Available") {
                $users = self::eSignatureUsers($cid);
                $this ->sign_disable = "false";
                $this ->sign_show = "true";
                $this ->action_url = "";
                $this ->signed = $users['signed'];
                $this ->pending = $users['pending'];
                $this ->esignature_message = $users['esignature_message'];
                $this ->download_url = "/download/unsigned-document/".$cid;
                $this ->actorstatus = "Available";
                $this ->packagestatus = "Pending";

            }
            else {
                $users = self::eSignatureUsers($cid);
                $this ->sign_disable = "true";
                $this ->sign_show = "false";
                $this ->signed = $users['signed'];
                $this ->pending = $users['pending'];
                $this ->esignature_message = $users['esignature_message'];
                $this ->download_url = "/download/unsigned-document/".$cid;
                $this ->actorstatus = "SIGNED";
                $this ->packagestatus = "Pending";
            }
        }
    } 
    
    public function cantSign($cid) {
        $this ->sign_disable = "true";
        $this ->sign_show = "false";
        $this ->download_url = "/download/unsigned-document/".$cid;
    }

    public function eSignatureUsers($cid) {
        $actors = DB::table('credentials as c')->join('esignature_actor as ea','c.id','ea.credentials_id')->join('esignature_package as ep','ep.package_id','ea.package_id')->select('c.first_name','c.last_name','c.title','ea.actor_status')->where('ep.contract_id',$cid)->get()->toArray();
        $signed = "";
        $pending = "";
        $signed_users = [];
        $pending_users = [];
        foreach ($actors as $key => $value) {
            if($value->actor_status == "Available") {
                $pending_users[] = $value;
            }
            else {
                $signed_users[] = $value;
            }
        }
        foreach ($signed_users as $key => $value) {
            $signed = $signed.$value->title." ".$value->first_name." ".$value->last_name;
            if($key < count($signed_users)-1) {
                $signed = $signed.", ";
            }
        }
        foreach ($pending_users as $key => $value) {
            $pending = $pending.$value->title." ".$value->first_name." ".$value->last_name;
            if($key < count($pending_users)-1) {
                $pending = $pending.", ";
            }
        }
        if(count($signed_users) == 0) {
            return ["signed" => "","pending" => "",'esignature_message' => "Signature is pending from all parties"];
        }
        elseif(count($pending_users) == 0) {
            return ["signed" => "","pending" => "",'esignature_message' => "Signed by all parties"];
        }
        else {
            return ["signed" => $signed,"pending" => $pending,'esignature_message' => ""];
        }
    }

    /*
    public function getApiUrl($esignature_info,$useremail,$cid) {
        $response = ['success' => 0, 'url' => '', 'message' => '', 'type' => 1];
        if(count($esignature_info) == 0) {
            // $action_url = "/get-connective-sign-url/$cid";
            // return $action_url;
            $action_url = "/get-connective-sign-url/$cid";
            $response['url'] = $action_url;
            $response['success'] = 1;
            return $response;
        }
        else {
            $statusurl = env('ESIGNATURE')."packages/".$esignature_info[0]->package_id."/status";
            $createCurlRequestObject = new CreateCurlRequest();
            $packagedata = $createCurlRequestObject->curlRequest($statusurl, env('ESIGNATURE_PASS'), "GET", null);
            if(isset($packagedata['response']['Stakeholders'])) {
                foreach ($packagedata['response']['Stakeholders'] as $key => $value) {
                    $externalReference = explode(",", $value['ExternalStakeholderReference']);
                    if (base64_decode($externalReference[1]) == $useremail && $externalReference[2] == $cid) {
                        $action_url = $value['Actors'][0]['ActionUrl'];
                        if (isset($userid) && $userid != '') {
                            redirect()->to($action_url)->send();
                        }
                        else {
                            return $action_url;
                        }
                        $response['url'] = $action_url;
                        $response['success'] = 1;
                        $response['type'] = 2;
                        return $response;
                    }
                }
            }
            else {
                $response['success'] = 0;
                $response['message'] = t('Unable to receive response from connective server, please try again');
                return $response;
                if (isset($userid) && $userid != '') {
                    echo "time out";
                }
                return "timeout";
            }
        }
            
    } */
    public function getApiUrl($esignature_info,$useremail,$cid) {
        $response = ['success' => 0, 'url' => '', 'message' => '', 'type' => 1];
        if(count($esignature_info) == 0) {
            // $action_url = "/get-connective-sign-url/$cid";
            // return $action_url;
            $action_url = "/get-connective-sign-url/$cid";
            $response['url'] = $action_url;
            $response['success'] = 1;
            return $response;
        }
        else {
            $statusurl = env('ESIGNATURE')."packages/".$esignature_info[0]->package_id."/status";
            $createCurlRequestObject = new CreateCurlRequest();
            $packagedata = $createCurlRequestObject->curlRequest($statusurl, env('ESIGNATURE_PASS'), "GET", null);
            if(isset($packagedata['response']['Stakeholders'])) {
                foreach ($packagedata['response']['Stakeholders'] as $key => $value) {
                    $externalReference = explode(",", $value['ExternalStakeholderReference']);
                    if (base64_decode($externalReference[1]) == $useremail && $externalReference[2] == $cid) {
                        $action_url = $value['Actors'][0]['ActionUrl'];
                        /*if (isset($userid) && $userid != '') {
                            redirect()->to($action_url)->send();
                        }
                        else {
                            return $action_url;
                        }*/
                        $response['url'] = $action_url;
                        $response['success'] = 1;
                        $response['type'] = 2;
                        return $response;
                    }
                }
            }
            else {
                $response['success'] = 0;
                $response['message'] = t('Unable to receive response from connective server, please try again');
                return $response;
                /*if (isset($userid) && $userid != '') {
                    echo "time out";
                }
                return "timeout";
                */
            }
        }
            
    }
}
?>
