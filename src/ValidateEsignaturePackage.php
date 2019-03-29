<?php
namespace Esignature;

use Illuminate\Support\Facades\DB;
use Auth;
use Illuminate\Support\Arr;
class ValidateEsignaturePackage {
    public function proceedSignature($cid,$userrole,$useremail,$userid = '') {
        if ($userrole == "tenant" || $userrole == "property owner") {
            $createCurlRequestObject = new CreateCurlRequest();
            $esignature_info = DB::table('esignature_actor as ea')->join('esignature_package as ep', 'ea.package_id', 'ep.package_id')->select('ea.actor_role', 'ea.actor_status', 'ep.package_id', 'ep.document_id', 'ep.download_url', 'ep.contract_id', 'ep.package_status')->where('ea.actor_mail', $useremail)->where('ep.contract_id', $cid)->get()->toarray();
            $contarct_status = DB::table('contracts as c')->join('status as s','s.id','=','c.status')->where('unique_key',$cid)->select('c.status','s.name')->get()->toarray();
            $sign_disable = "";
            $action_url = "";
            $download_url = "/download/unsigned-document/".$cid;
            $sign_show = "true";
            $esignature_message = "";
            $signed = "";
            $pending = "";
            $packagestatus = "Pending";
           
            if($contarct_status[0]->status ==2 || $contarct_status[0]->status ==4 || $contarct_status[0]->status ==5 || $contarct_status[0]->status ==6 || $contarct_status[0]->status == 8) {
                $sign_show = "false";
                $sign_disable = "true";
                $download_url = "/download/unsigned-document/".$cid;
                $action_url = "";
                $esignature_message = "";
                $signed = "";
                $pending = "";
                return [$sign_disable,$action_url,$download_url,$esignature_message,$sign_show,$signed,$pending];
            }
            if($contarct_status[0]->status ==3 && $userrole = 'tenant') {
                if (empty($esignature_info)) {
                    $actorstatus = "Available";
                    $sign_disable = "false";
                    $action_url = "/sign-contract/$cid";
                    $esignature_message = "Signature is pending from all the parties";
                    if (isset($userid) && $userid != '') {
                        redirect()->to(env('APP_URL') . "/sign-contract/$cid?userrole=$userrole&email=$useremail&userid=$userid")->send();
                    }
                } 
                else {
                    $users = DB::table('credentials as c')->join('esignature_actor as ea','c.id','ea.credentials_id')->join('esignature_package as ep','ep.package_id','ea.package_id')->select('c.first_name','c,last_name','c.title')->where('ep.contract_id',$cid);
                    $actorstatus = $esignature_info[0]->actor_status;
                    $result = DB::table('credentials as c')->join('esignature_actor as ea','c.id','ea.credentials_id')->join('esignature_package as ep','ep.package_id','ea.package_id')->select('c.first_name','c.last_name','c.title','ea.actor_status')->where('ep.contract_id',$cid)->get()->toArray();
                    $i = 0;
                    $j = 0;
                    foreach ($result as $key => $value) {
                        if($value->actor_status == "Available") {
                            $pending_users[$i++] = $value;
                        }
                        else {
                            $signed_users[$j++] = $value;
                        }
                    }
                    if ($esignature_info[0]->actor_status == "Available") {
                        $statusurl = env('ESIGNATURE')."packages/".$esignature_info[0]->package_id."/status";
                        $packagedata = $createCurlRequestObject->curlRequest($statusurl, env('ESIGNATURE_PASS'), "GET", null);
                        foreach ($packagedata['response']['Stakeholders'] as $key => $value) {
                            $externalReference = explode(",", $value['ExternalStakeholderReference']);
                            if (base64_decode($externalReference[1]) == $useremail && $externalReference[2] == $cid) {
                                $sign_disable = "false";
                                $action_url = $value['Actors'][0]['ActionUrl'];
                                if (isset($userid) && $userid != '') {
                                    redirect()->to($action_url)->send();
                                }
                                if(empty($signed_users)) {
                                    $esignature_message = "Signature is pending from all the parties";
                                }
                                else {
                                    foreach ($signed_users as $key => $value) {
                                        $signed = $signed.$value->title.$value->first_name." ".$value->last_name;
                                        if($key < count($signed_users)-1) {
                                            $signed = $signed.", ";
                                        }
                                    }
                                    foreach ($pending_users as $key => $value) {
                                        $pending = $pending.$value->title.$value->first_name." ".$value->last_name;
                                        if($key < count($pending_users)-1) {
                                            $pending = $pending.", ";
                                        }
                                    }
                                }
                            }
                        }
                    }
                    else {
                        if (isset($userid) && $userid != '') {
                            $statusurl = env('ESIGNATURE')."packages/".$esignature_info[0]->package_id."/status";
                            $packagedata = $createCurlRequestObject->curlRequest($statusurl, env('ESIGNATURE_PASS'), "GET", null);
                            foreach ($packagedata['response']['Stakeholders'] as $key => $value) {
                                $externalReference = explode(",", $value['ExternalStakeholderReference']);
                                if (base64_decode($externalReference[1]) == $useremail && $externalReference[2] == $cid) {
                                    $action_url = $value['Actors'][0]['ActionUrl'];
                                    if (isset($userid) && $userid != '') {
                                        redirect()->to($action_url)->send();
                                    }
                                }
                            }
                        }
                        if ($esignature_info[0]->package_status == "Finished") {
                            $sign_disable = "true";
                            $download_url = "/download/signed-document/".$esignature_info[0]->package_id."/".$esignature_info[0]->document_id."/".$cid;
                            $esignature_message = "Signed By all the parties";
                            $sign_show = "false";
                            $packagestatus = "Finished";
                        } else {
                            $sign_disable = "true";
                            $download_url = "/download/unsigned-document/".$cid;
                            $sign_show = "true";
                            if(empty($signed_users)) {
                                $esignature_message = "Signature is pending from all the parties";
                            }
                            else {
                                foreach ($signed_users as $key => $value) {
                                    $signed = $signed.$value->title.$value->first_name." ".$value->last_name;
                                    if($key < count($signed_users)-1) {
                                        $signed = $signed.", ";
                                    }
                                }
                                foreach ($pending_users as $key => $value) {
                                    $pending = $pending.$value->title.$value->first_name." ".$value->last_name;
                                    if($key < count($pending_users)-1) {
                                        $pending = $pending.", ";
                                    }
                                }
                            }
                        }
                    }
                }                
                return [$sign_disable,$action_url,$download_url,$esignature_message,$sign_show,$signed,$pending,$actorstatus,$packagestatus];
            }
            else {
                if (empty($esignature_info)) {
                    $actorstatus = "Available";
                    $sign_disable = "false";
                    $action_url = "/sign-contract/$cid";
                    $esignature_message = "Signature is pending from all the parties";
                    if (isset($userid) && $userid != '') {
                        redirect()->to(env('APP_URL') . "/sign-contract/$cid?userrole=$userrole&email=$useremail&userid=$userid")->send();
                    }
                }
                else {
                    $actorstatus = $esignature_info[0]->actor_status;
                    $result = DB::table('credentials as c')->join('esignature_actor as ea','c.id','ea.credentials_id')->join('esignature_package as ep','ep.package_id','ea.package_id')->select('c.first_name','c.last_name','c.title','ea.actor_status')->where('ep.contract_id',$cid)->get()->toArray();
                    $i = 0;
                    $j = 0;
                    foreach ($result as $key => $value) {
                        if($value->actor_status == "Available") {
                            $pending_users[$i++] = $value;
                        }
                        else {
                            $signed_users[$j++] = $value;
                        }
                    }
                    if ($esignature_info[0]->actor_status == "Available") {
                        $statusurl = env('ESIGNATURE')."packages/".$esignature_info[0]->package_id."/status";
                        $packagedata = $createCurlRequestObject->curlRequest($statusurl, env('ESIGNATURE_PASS'), "GET", null);
                        foreach ($packagedata['response']['Stakeholders'] as $key => $value) {
                            $externalReference = explode(",", $value['ExternalStakeholderReference']);
                            if (base64_decode($externalReference[1]) == $useremail && $externalReference[2] == $cid) {
                                $sign_disable = "false";
                                $action_url = $value['Actors'][0]['ActionUrl'];
                                if (isset($userid) && $userid != '') {
                                    redirect()->to($action_url)->send();
                                }
                                if(empty($signed_users)) {
                                    $esignature_message = "Signature is pending from all the parties";
                                }
                                else {
                                    foreach ($signed_users as $key => $value) {
                                        $signed = $signed.$value->title.$value->first_name." ".$value->last_name;
                                        if($key < count($signed_users)-1) {
                                            $signed = $signed.", ";
                                        }
                                    }
                                    foreach ($pending_users as $key => $value) {
                                        $pending = $pending.$value->title.$value->first_name." ".$value->last_name;
                                        if($key < count($pending_users)-1) {
                                            $pending = $pending.", ";
                                        }
                                    }
                                }
                            }
                        }
                    }
                    else {
                        if (isset($userid) && $userid != '') {
                            $statusurl = env('ESIGNATURE')."packages/".$esignature_info[0]->package_id."/status";
                            $packagedata = $createCurlRequestObject->curlRequest($statusurl, env('ESIGNATURE_PASS'), "GET", null);
                            foreach ($packagedata['response']['Stakeholders'] as $key => $value) {
                                $externalReference = explode(",", $value['ExternalStakeholderReference']);
                                if (base64_decode($externalReference[1]) == $useremail && $externalReference[2] == $cid) {
                                    $action_url = $value['Actors'][0]['ActionUrl'];
                                    if (isset($userid) && $userid != '') {
                                        redirect()->to($action_url)->send();
                                    }
                                }
                            }
                        }
                        if ($esignature_info[0]->package_status == "Finished") {
                            $sign_disable = "true";
                            $download_url = "/download/signed-document/".$esignature_info[0]->package_id."/".$esignature_info[0]->document_id."/".$cid;
                            $esignature_message = "Signed By all the parties";
                            $sign_show = "false";
                            $packagestatus = "Finished";
                        } else {
                            $sign_disable = "true";
                            $download_url = "/download/unsigned-document/".$cid;
                            $sign_show = "true";
                            if(empty($signed_users)) {
                                $esignature_message = "Signature is pending from all the parties";
                            }
                            else {
                                foreach ($signed_users as $key => $value) {
                                    $signed = $signed.$value->title.$value->first_name." ".$value->last_name;
                                    if($key < count($signed_users)-1) {
                                        $signed = $signed.", ";
                                    }
                                }
                                foreach ($pending_users as $key => $value) {
                                    $pending = $pending.$value->title.$value->first_name." ".$value->last_name;
                                    if($key < count($pending_users)-1) {
                                        $pending = $pending.", ";
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return [$sign_disable,$action_url,$download_url,$esignature_message,$sign_show,$signed,$pending,$actorstatus,$packagestatus];
        }
        else {
            $esignature_info = DB::table('esignature_actor as ea')->join('esignature_package as ep', 'ea.package_id', 'ep.package_id')->select('ea.actor_role', 'ea.actor_status', 'ep.package_id', 'ep.document_id', 'ep.download_url', 'ep.contract_id', 'ep.package_status')->where('ea.actor_mail', $useremail)->where('ep.contract_id', $cid)->get()->toarray();
            if(empty($esignature_info)) {
                $sign_disable = "true";
                $action_url = "";
                $download_url = "download/unsigned-document/".$cid;
                $esignature_message = "Signature is Pending from all the parties";
                $sign_show = "false";
                $packagestatus = "Finished";
            }
            else {
                $result = DB::table('credentials as c')->join('esignature_actor as ea','c.id','ea.credentials_id')->join('esignature_package as ep','ep.package_id','ea.package_id')->select('c.first_name','c.last_name','c.title','ea.actor_status')->where('ep.contract_id',$cid)->get()->toArray();
                $i = 0;
                $j = 0;
                foreach ($result as $key => $value) {
                    if($value->actor_status == "Available") {
                        $pending_users[$i++] = $value;
                    }
                    else {
                        $signed_users[$j++] = $value;
                    }
                }
                $sign_disable = "true";
                $action_url = "";
                $download_url = "download/unsigned-document/".$cid;
                foreach ($signed_users as $key => $value) {
                    $signed = $signed.$value->title.$value->first_name." ".$value->last_name;
                    if($key < count($signed_users)-1) {
                        $signed = $signed.", ";
                    }
                }
                foreach ($pending_users as $key => $value) {
                    $pending = $pending.$value->title.$value->first_name." ".$value->last_name;
                    if($key < count($pending_users)-1) {
                        $pending = $pending.", ";
                    }
                }
                $sign_show = "false";
            }
            
        }
        return [$sign_disable,$action_url,$download_url,$esignature_message,$sign_show,$signed,$pending,$actorstatus,$packagestatus];
    }
}
?>
