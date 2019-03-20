<?php
namespace Esignature;

use Illuminate\Support\Facades\DB;
use Auth;
class ValidateEsignaturePackage {
<<<<<<< HEAD
    public function proceedSignature($cid,$userrole,$useremail) {
=======
    public function proceedSignature($cid,$userrole,$useremail,$userid = '') {
>>>>>>> 296fcc12dc87de5464af9a162be72667905dd4e2
        if ($userrole == "tenant" || $userrole == "property owner") {
            $createCurlRequestObject = new CreateCurlRequest();
            $esignature_info = DB::table('esignature_actor as ea')->join('esignature_package as ep', 'ea.package_id', 'ep.package_id')->select('ea.actor_role', 'ea.actor_status', 'ep.package_id', 'ep.document_id', 'ep.download_url', 'ep.contract_id', 'ep.package_status')->where('ea.actor_mail', $useremail)->where('ep.contract_id', $cid)->get()->toarray();
            $contarct_status = DB::table('contracts')->where('unique_key',$cid)->select('status')->get()->toarray();
            $sign_disable = "";
            $action_url = "";
<<<<<<< HEAD
            $download_url = "/testPdf/$cid";
            $sign_show = "true";
            $esignature_message = "";
=======
            $download_url = "/download/unsigned-document/".$cid;
            $sign_show = "true";
            $esignature_message = "";
            $signed = "";
            $pending = "";
>>>>>>> 296fcc12dc87de5464af9a162be72667905dd4e2
            
            if($contarct_status[0]->status == 2 && $userrole == 'property owner') {
                $sign_disable = "";
                $action_url = "";
<<<<<<< HEAD
                $download_url = "/testPdf/$cid";
                $sign_show = "";
                $esignature_message = "";
                return [$sign_disable,$action_url,$download_url,$esignature_message,$sign_show];
=======
                $download_url = "/download/unsigned-document/".$cid;
                $sign_show = "";
                $esignature_message = "";
                return [$sign_disable,$action_url,$download_url,$esignature_message,$sign_show,$signed,$pending];
>>>>>>> 296fcc12dc87de5464af9a162be72667905dd4e2
            }
            if($contarct_status[0]->status == 10) {
                $sign_disable = "";
                $action_url = "";
<<<<<<< HEAD
                $download_url = "/testPdf/$cid";
                $sign_show = "";
                $esignature_message = "";
                return [$sign_disable,$action_url,$download_url,$esignature_message,$sign_show];
=======
                $download_url = "/download/unsigned-document/".$cid;
                $sign_show = "";
                $esignature_message = "";
                return [$sign_disable,$action_url,$download_url,$esignature_message,$sign_show,$signed,$pending];
>>>>>>> 296fcc12dc87de5464af9a162be72667905dd4e2
            }
            if($contarct_status[0]->status == 3 && $userrole == 'property owner') {
                $sign_disable = "true";
                $action_url = "";
<<<<<<< HEAD
                $download_url = "/testPdf/$cid";
                $sign_show = "false";
                $esignature_message = "Pending approval from tenant";
                return [$sign_disable,$action_url,$download_url,$esignature_message,$sign_show];
=======
                $download_url = "/download/unsigned-document/".$cid;
                $sign_show = "false";
                $esignature_message = "Pending approval from tenant";
                return [$sign_disable,$action_url,$download_url,$esignature_message,$sign_show,$signed,$pending];
>>>>>>> 296fcc12dc87de5464af9a162be72667905dd4e2
            }
            if (empty($esignature_info)) {
                $sign_disable = "false";
                $action_url = "/sign-contract/$cid";
                $esignature_message = "Signature is pending from all the parties";
<<<<<<< HEAD
=======
                if (isset($userid) && $userid != '') {
                    redirect()->to(env('APP_URL') . "/sign-contract/$cid?userrole=$userrole&email=$useremail&userid=$userid")->send();
                }
>>>>>>> 296fcc12dc87de5464af9a162be72667905dd4e2
            } 
            else {
                $signed_users = DB::table('esignature_actor as ea')->join('esignature_package as ep', 'ea.package_id', 'ep.package_id')->select('ea.fname')->where('ea.actor_status', "SIGNED")->where('ep.contract_id', $cid)->get()->toarray();
                $pending_users = DB::table('esignature_actor as ea')->join('esignature_package as ep', 'ea.package_id', 'ep.package_id')->select('ea.fname')->where('ea.actor_status', "Available")->where('ep.contract_id', $cid)->get()->toarray();
<<<<<<< HEAD
=======
                
>>>>>>> 296fcc12dc87de5464af9a162be72667905dd4e2
                if ($esignature_info[0]->actor_status == "Available") {
                    $statusurl = env('ESIGNATURE')."packages/".$esignature_info[0]->package_id."/status";
                    $packagedata = $createCurlRequestObject->curlRequest($statusurl, env('ESIGNATURE_PASS'), "GET", null);
                    foreach ($packagedata['response']['Stakeholders'] as $key => $value) {
                        $externalReference = explode(",", $value['ExternalStakeholderReference']);
                        if ($externalReference[1] == $useremail && $externalReference[2] == $cid) {
                            $sign_disable = "false";
                            $action_url = $value['Actors'][0]['ActionUrl'];
<<<<<<< HEAD
=======
                            if (isset($userid) && $userid != '') {
                                redirect()->to($action_url)->send();
                            }
>>>>>>> 296fcc12dc87de5464af9a162be72667905dd4e2
                            if(empty($signed_users)) {
                                $esignature_message = "Signature is pending from all the parties";
                            }
                            else {
<<<<<<< HEAD
                                $esignature_message = "Signed By - ";
                                foreach ($signed_users as $key => $value) {
                                    $esignature_message = $esignature_message." ".$value->fname;
                                }
                                $esignature_message = $esignature_message."\n Pending From- ";
                                foreach ($pending_users as $key => $value) {
                                    $esignature_message = $esignature_message." ".$value->fname;
=======
                                foreach ($signed_users as $key => $value) {
                                    $signed = $signed.$value->fname;
                                    if($key < count($signed_users)-1) {
                                        $signed = $signed.", ";
                                    }
                                }
                                foreach ($pending_users as $key => $value) {
                                    $pending = $pending.$value->fname;
                                    if($key < count($pending_users)-1) {
                                        $pending = $pending.", ";
                                    }
>>>>>>> 296fcc12dc87de5464af9a162be72667905dd4e2
                                }
                            }
                        }
                    }
                }
                else {
                    if ($esignature_info[0]->package_status == "Finished") {
                        $sign_disable = "true";
                        $download_url = "/download/signed-document/".$esignature_info[0]->package_id."/".$esignature_info[0]->document_id."/".$cid;
<<<<<<< HEAD
                        $esignature_message = " Signed By all the parties";
                        $sign_show = "false";
                    } else {
                        $sign_disable = "true";
                        $download_url = "/testPdf/$cid";
=======
                        $esignature_message = "Signed By all the parties";
                        $sign_show = "false";
                    } else {
                        $sign_disable = "true";
                        $download_url = "/download/unsigned-document/".$cid;
>>>>>>> 296fcc12dc87de5464af9a162be72667905dd4e2
                        $sign_show = "true";
                        if(empty($signed_users)) {
                            $esignature_message = "Signature is pending from all the parties";
                        }
                        else {
<<<<<<< HEAD
                            $esignature_message = "Signed By - ";
                            foreach ($signed_users as $key => $value) {
                                $esignature_message = $esignature_message." ".$value->fname;
                            }
                            $esignature_message = $esignature_message."\n".t("Pending From-");
                            foreach ($pending_users as $key => $value) {
                                $esignature_message = $esignature_message." ".$value->fname;
=======
                            foreach ($signed_users as $key => $value) {
                                $signed = $signed.$value->fname;
                                if($key < count($signed_users)-1) {
                                    $signed = $signed.", ";
                                }
                            }
                            foreach ($pending_users as $key => $value) {
                                $pending = $pending.$value->fname;
                                if($key < count($pending_users)-1) {
                                    $pending = $pending.", ";
                                }
>>>>>>> 296fcc12dc87de5464af9a162be72667905dd4e2
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
<<<<<<< HEAD
                $download_url = "/testPdf/$cid";
=======
                $download_url = "download/unsigned-document/".$cid;
>>>>>>> 296fcc12dc87de5464af9a162be72667905dd4e2
                $esignature_message = "Signature is Pending from all the parties";
                $sign_show = "false";
            }
            else {
                $signed_users = DB::table('esignature_actor as ea')->join('esignature_package as ep', 'ea.package_id', 'ep.package_id')->select('ea.fname')->where('ea.actor_status', "Available")->where('ep.contract_id', $cid)->get()->toarray();
                $pending_users = DB::table('esignature_actor as ea')->join('esignature_package as ep', 'ea.package_id', 'ep.package_id')->select('ea.fname')->where('ea.actor_status', "SIGNED")->where('ep.contract_id', $cid)->get()->toarray();
<<<<<<< HEAD
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
=======
                
                
                $sign_disable = "true";
                $action_url = "";
                $download_url = "download/unsigned-document/".$cid;
                foreach ($signed_users as $key => $value) {
                    $signed = $signed.$value->fname;
                    if($key < count($signed_users)-1) {
                        $signed = $signed.", ";
                    }
                }
                foreach ($pending_users as $key => $value) {
                    $pending = $pending.$value->fname;
                    if($key < count($pending_users)-1) {
                        $pending = $pending.", ";
                    }
>>>>>>> 296fcc12dc87de5464af9a162be72667905dd4e2
                }
                $sign_show = "false";
            }
            
        }
<<<<<<< HEAD
        return [$sign_disable,$action_url,$download_url,$esignature_message,$sign_show];
=======
        return [$sign_disable,$action_url,$download_url,$esignature_message,$sign_show,$signed,$pending];
>>>>>>> 296fcc12dc87de5464af9a162be72667905dd4e2
    }
}
?>