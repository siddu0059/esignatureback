<?php
namespace Esignature;

use Illuminate\Support\Facades\DB;
use Auth;
class ValidateEsignaturePackage {
    public function proceedSignature($cid,$userrole,$useremail) {
        if ($userrole == "tenant" || $userrole == "property owner") {
            $createCurlRequestObject = new CreateCurlRequest();
            $esignature_info = DB::table('esignature_actor as ea')->join('esignature_package as ep', 'ea.package_id', 'ep.package_id')->select('ea.actor_role', 'ea.actor_status', 'ep.package_id', 'ep.document_id', 'ep.download_url', 'ep.contract_id', 'ep.package_status')->where('ea.actor_mail', $useremail)->where('ep.contract_id', $cid)->get()->toarray();
            $contarct_status = DB::table('contracts')->where('unique_key',$cid)->select('status')->get()->toarray();
            $sign_disable = "";
            $action_url = "";
            $download_url = "/testPdf/$cid";
            $sign_show = "true";
            $esignature_message = "";
            
            if($contarct_status[0]->status == 2 && $userrole == 'property owner') {
                $sign_disable = "";
                $action_url = "";
                $download_url = "/testPdf/$cid";
                $sign_show = "";
                $esignature_message = "";
                return [$sign_disable,$action_url,$download_url,$esignature_message,$sign_show];
            }
            if($contarct_status[0]->status == 10) {
                $sign_disable = "";
                $action_url = "";
                $download_url = "/testPdf/$cid";
                $sign_show = "";
                $esignature_message = "";
                return [$sign_disable,$action_url,$download_url,$esignature_message,$sign_show];
            }
            if($contarct_status[0]->status == 3 && $userrole == 'property owner') {
                $sign_disable = "true";
                $action_url = "";
                $download_url = "/testPdf/$cid";
                $sign_show = "false";
                $esignature_message = "Pending approval from tenant";
                return [$sign_disable,$action_url,$download_url,$esignature_message,$sign_show];
            }
            if (empty($esignature_info)) {
                $sign_disable = "false";
                $action_url = "/sign-contract/$cid";
                $esignature_message = "Signature is pending from all the parties";
            } 
            else {
                $signed_users = DB::table('esignature_actor as ea')->join('esignature_package as ep', 'ea.package_id', 'ep.package_id')->select('ea.fname')->where('ea.actor_status', "SIGNED")->where('ep.contract_id', $cid)->get()->toarray();
                $pending_users = DB::table('esignature_actor as ea')->join('esignature_package as ep', 'ea.package_id', 'ep.package_id')->select('ea.fname')->where('ea.actor_status', "Available")->where('ep.contract_id', $cid)->get()->toarray();
                if ($esignature_info[0]->actor_status == "Available") {
                    $statusurl = env('ESIGNATURE')."packages/".$esignature_info[0]->package_id."/status";
                    $packagedata = $createCurlRequestObject->curlRequest($statusurl, env('ESIGNATURE_PASS'), "GET", null);
                    foreach ($packagedata['response']['Stakeholders'] as $key => $value) {
                        $externalReference = explode(",", $value['ExternalStakeholderReference']);
                        if ($externalReference[1] == $useremail && $externalReference[2] == $cid) {
                            $sign_disable = "false";
                            $action_url = $value['Actors'][0]['ActionUrl'];
                            if(empty($signed_users)) {
                                $esignature_message = "Signature is pending from all the parties";
                            }
                            else {
                                $esignature_message = "Signed By - ";
                                foreach ($signed_users as $key => $value) {
                                    $esignature_message = $esignature_message." ".$value->fname;
                                }
                                $esignature_message = $esignature_message."\n Pending From- ";
                                foreach ($pending_users as $key => $value) {
                                    $esignature_message = $esignature_message." ".$value->fname;
                                }
                            }
                        }
                    }
                }
                else {
                    if ($esignature_info[0]->package_status == "Finished") {
                        $sign_disable = "true";
                        $download_url = "/download/signed-document/".$esignature_info[0]->package_id."/".$esignature_info[0]->document_id."/".$cid;
                        $esignature_message = " Signed By all the parties";
                        $sign_show = "false";
                    } else {
                        $sign_disable = "true";
                        $download_url = "/testPdf/$cid";
                        $sign_show = "true";
                        if(empty($signed_users)) {
                            $esignature_message = "Signature is pending from all the parties";
                        }
                        else {
                            $esignature_message = "Signed By - ";
                            foreach ($signed_users as $key => $value) {
                                $esignature_message = $esignature_message." ".$value->fname;
                            }
                            $esignature_message = $esignature_message."\n".t("Pending From-");
                            foreach ($pending_users as $key => $value) {
                                $esignature_message = $esignature_message." ".$value->fname;
                            }
                        }
                    }
                }
            }
        }
        else {
            $esignature_info = DB::table('esignature_actor as ea')->join('esignature_package as ep', 'ea.package_id', 'ep.package_id')->select('ea.actor_role', 'ea.actor_status', 'ep.package_id', 'ep.document_id', 'ep.download_url', 'ep.contract_id', 'ep.package_status')->where('ea.actor_mail', $useremail)->where('ep.contract_id', $cid)->get()->toarray();
            if(empty($esignature_info)) {
                $sign_disable = "true";
                $action_url = "";
                $download_url = "/testPdf/$cid";
                $esignature_message = "Signature is Pending from all the parties";
                $sign_show = "false";
            }
            else {
                $signed_users = DB::table('esignature_actor as ea')->join('esignature_package as ep', 'ea.package_id', 'ep.package_id')->select('ea.fname')->where('ea.actor_status', "Available")->where('ep.contract_id', $cid)->get()->toarray();
                $pending_users = DB::table('esignature_actor as ea')->join('esignature_package as ep', 'ea.package_id', 'ep.package_id')->select('ea.fname')->where('ea.actor_status', "SIGNED")->where('ep.contract_id', $cid)->get()->toarray();
                $sign_disable = "true";
                $action_url = "";
                $download_url = "/testPdf/$cid";
                $esignature_message = "Signed By - ";
                foreach ($signed_users as $key => $value) {
                    $esignature_message = $esignature_message." ".$value->fname;
                }
                $esignature_message = $esignature_message."<br> Pending By- ";
                foreach ($pending_users as $key => $value) {
                    $esignature_message = $esignature_message." ".$value->fname;
                }
                $sign_show = "false";
            }
            
        }
        return [$sign_disable,$action_url,$download_url,$esignature_message,$sign_show];
    }
}
?>