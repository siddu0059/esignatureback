<?php
namespace Esignature;

use Illuminate\Support\Facades\DB;
use Auth;
class ValidateEsignaturePackage {
    public function proceedSignature($cid,$userrole) {
        $esignature_info = DB::table('esignature_actor')->select('action_url','actor_status','actor_role','user_email','package_id','document_id')->where('contract_id',$cid)->orderBy('actor_role', 'DESC')->get()->toarray();
        $createCurlRequestObject = new CreateCurlRequest();
        $message = "";
        $download_url = "/testPdf/$cid";
        $action_url ="";
        if($userrole == 'tenant') {
            $sign_show = "true";
            if($esignature_info) {
                if($esignature_info[0]->actor_status == "Available") {
                    $sign_disable = "false";
                    $statusurl = env('ESIGNATURE')."packages/".$esignature_info[0]->package_id."/status";
                    $packagedata = $createCurlRequestObject->curlRequest($statusurl,env('ESIGNATURE_PASS'),"GET",null);
                    if(explode(",",$packagedata['response']['Stakeholders'][0]['ExternalStakeholderReference'])[0]=='tenant') {
                        $action_url = $packagedata['response']['Stakeholders'][0]['Actors'][0]['ActionUrl'];
                    }
                    else {
                        $action_url = $packagedata['response']['Stakeholders'][1]['Actors'][0]['ActionUrl'];
                    }
                }
                else {
                    $sign_disable = "true";
                    $message = "You already signed this contract";
                    if($esignature_info[0] ->actor_status != "Available") {
                        $download_url = "/download-signed-document/".$esignature_info[0]->package_id."/".$esignature_info[0]->document_id."/".$cid;
                    }
                }
            }
            else {
                $sign_disable = "false";
                $action_url = "/sign-contract/$cid";
            }
        }
        else {
            $sign_show = "false";
            if($esignature_info) {               
                if($esignature_info[0] ->actor_status != "Available") {
                    if($esignature_info[1] ->actor_status == "Available"){
                        $statusurl = env('ESIGNATURE')."packages/".$esignature_info[0]->package_id."/status";
                        $packagedata = $createCurlRequestObject->curlRequest($statusurl,env('ESIGNATURE_PASS'),"GET",null);
                        $sign_disable = "false";
                        if(explode(",",$packagedata['response']['Stakeholders'][0]['ExternalStakeholderReference'])!='tenant') {
                            $action_url = $packagedata['response']['Stakeholders'][0]['Actors'][0]['ActionUrl'];
                        }
                        else {
                            $action_url = $packagedata['response']['Stakeholders'][1]['Actors'][0]['ActionUrl'];
                        } 
                    }
                    else {
                        $sign_disable = "true";
                        $message = "You already signed this contract";
                        $download_url = "/download-signed-document/".$esignature_info[0]->package_id."/".$esignature_info[0]->document_id."/".$cid;
                    }
                }
                else {
                    $sign_disable = "true";
                    $message = "tenant did'nt signed to this contract yet";
                }
            }
            else {
                $sign_disable = "true";
            }
        }
        return [$sign_disable,$action_url,$message,$sign_show,$download_url];
    }
}
?>