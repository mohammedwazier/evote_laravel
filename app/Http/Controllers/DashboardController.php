<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Jobs\SendingEmail;

use Hash;
use Ramsey\Uuid\Uuid;

use Carbon\Carbon;
use Session;

use App\Models\AuthUser;
use App\Models\Elections;
use App\Models\Voter;
use App\Models\Ballot;
use App\Models\ShortLink;

use DB;

class DashboardController extends Controller
{

    public function generateUUID(){
        return sprintf('%04x%04x%04x%04x%04x%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        // 16 bits for "time_mid"
        mt_rand(0, 0xffff),
        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand(0, 0x0fff) | 0x4000,
        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand(0, 0x3fff) | 0x8000,
        // 48 bits for "node"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    public function saveLink($links){
        $link = new ShortLink;
        $link->link = $links;
        $link->code = $this->generateUUID($link);
        $link->save();
        return route('redirect', ['code' => $link->code]);
    }

    public function redirect(Request $req, $code){
        $find = ShortLink::where('code', $code)->first();
        if($find){
            return redirect($find->link);
        }else{
            return redirect()->route('homepage.login')->with(['message' => 'No Links available', 'icon' => 'success']);
        }
    }

    protected $key;
    public function __construct(){
        $this->key = "0983u4ijoklmergAER";
    }
    public function index(){
        $election = [];
        if(Session::get('user')->is_manager === '1'){
            $election = Elections::where('created_by', Session::get('user')->id)->get();
        }
        return view('pages.dashboard', ['election' => $election]);
    }

    public function enc($text){
        $plaintext = $text;
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $this->key, $options=OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $this->key, $as_binary=true);
        $ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );
        return $ciphertext;
    }

    public function dec($ciphertext){
        $c = base64_decode($ciphertext);
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len=32);
        $ciphertext_raw = substr($c, $ivlen+$sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $this->key, $options=OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $this->key, $as_binary=true);
        if (hash_equals($hmac, $calcmac))//PHP 5.6+ timing attack safe comparison
        {
            // echo $original_plaintext."\n";
            return $original_plaintext;
        }
    }

    public function createElection(){
        $user = AuthUser::where('verified', 'true')->get();
        return view('pages.election_create', ['user' => $user]);
    }

    public function storeElection(Request $req){
        $election = new Elections;
        $voters = explode("\n", htmlspecialchars($req->voters));
        $managers = explode("\n", htmlspecialchars($req->manager));
        foreach($voters as $key => $val){
            $voters[$key] = str_replace(array("\n", "\r"), '', $val);
        }

        foreach($managers as $key => $val){
            $managers[$key] = str_replace(array("\n", "\r"), '', $val);
        }
        $uuid = Uuid::uuid1();
        $ballot_model = [
            'key' => $uuid,
            'answer' => $req->question
        ];
        $counter = [];

        foreach($req->question as $key => $val){
            $counter[htmlspecialchars($val)] = 0;
        }

        // $voter = new Voter;
        // foreach

        $election->title = htmlspecialchars($req->title);
        $election->ballot_model = json_encode($ballot_model);
        $election->voters = json_encode($voters);
        $election->managers = json_encode($managers);
        $election->vote_email = htmlspecialchars($req->voteForm);
        $election->voted_email = htmlspecialchars($req->voteSuccess);
        $election->email_sender = env('MAIL_FROM_ADDRESS');
        $election->private_key = $uuid."_".md5($election->voters)."_".md5($election->managers);
        $election->public_key = $this->enc($election->private_key);
        // $election->dec = $this->dec($election->public_key);
        $election->counters = json_encode($counter);
        $election->closed = 0;
        $election->is_active = 0;
        $election->deadline = $req->electionPeriod;
        $election->created_by = Session::get('user')->id;
        $election->not_voted_email = "";
        $election->modified_by = "";
        $election->updated_at = null;

        DB::beginTransaction();

        try{
            $election->save();
            foreach($voters as $key => $val){
                $voter = new Voter;
                $voter->voter_uuid = Uuid::uuid1();
                $voter->election_id = $election->public_key;
                $voter->email = $val;
                $voter->voted = 'false';
                $voter->created_at = Carbon::now('Asia/Jakarta');
                $voter->save();

                $ballot = new Ballot;
                $ballot->election_id = $election->public_key;
                $ballot->ballot_content = json_encode($req->question);
                $ballot->assigned = 0;
                $ballot->voted = 0;
                $ballot->result = json_encode([]);
                $ballot->ballot_uuid = "Ballot-0000".($key + 1);
                $ballot->signature = $this->enc($ballot->election_id."_".$voter->voter_uuid);
                $ballot->save();

                // Shorten the Link
                $voteLink = $this->saveLink(route('dashboard.vote.vote', ['id' => base64_encode($uuid), 'key' => base64_encode($ballot->signature)]));
                $ballotLink = $this->saveLink(route('ballot.details', ['id' => base64_encode($uuid), 'ballot_uid' => $ballot->ballot_uuid]));
                $resultLink = $this->saveLink(route('election.result', ['id' => base64_encode($uuid)]));
                // Sending Email
                $body = $election->vote_email;
                $body = str_replace("{=title}", $election->title, $body);
                $body = str_replace("{=link}", $voteLink, $body);
                $body = str_replace("{=link_ballots}", $ballotLink, $body);
                $body = str_replace("{=link_results}", $resultLink, $body);
                $dataEmail = [
                    "email" => $voter->email,
                    "title" => $election->title,
                    "body" => $body
                ];
                dispatch(new SendingEmail($dataEmail));
            }

            DB::commit();
            return redirect()->route('dashboard.index')->with(['message' => 'Success Create Election', 'icon' => 'success']);

        }catch(\Exception $e){
            dd($e);
            DB::rollback();
            return redirect()->back()->with(['message' => 'Failed Create Election', 'icon' => 'error']);
        }

    }

    public function voteVoted(Request $req, $id, $key){
        $id = base64_decode($id);
        $key = base64_decode($key);

        $signature = explode("_", $this->dec($key));

        try{

            $voter = Voter::where('voter_uuid', $signature[1])->firstOrFail();
            $user = AuthUser::where('email', $voter->email)->firstOrFail();
            $ballot = Ballot::where('signature', $key)->firstOrFail();
            $election = Elections::where(['public_key' => $ballot->election_id])->firstOrFail();
            if($election->is_active === '0'){
                return redirect()->route('homepage.login')->with(['message' => 'Election has not been started', 'icon' => 'info']);
            }
            if($election->closed === "1"){
                // Ke Ballot
                $id_election = base64_encode(explode("_", $election->private_key)[0]);
                return redirect()->route('ballot.details', ['id' => $id_election, 'ballot_uid' => $ballot->ballot_uuid]);
            }

            if($ballot->assigned === "1" || $ballot->voted === '1' || $voter->voted === 'true'){
                // Kembali ke Ballot
                $id_election = base64_encode(explode("_", $election->private_key)[0]);
                return redirect()->route('ballot.details', ['id' => $id_election, 'ballot_uid' => $ballot->ballot_uuid]);
            }

            $data = [
                'title' => $election->title,
                'email' => $voter->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'pilihan' => $ballot->ballot_content,
                'ballot_uuid' => $ballot->ballot_uuid,
                'ballot_signature' => base64_encode($ballot->signature),
                'election_model' => $election->ballot_model,
                'election_public_key' => base64_encode($election->public_key),
                'election_public_key_dec' => $this->dec($election->public_key)
            ];

            return view('pages.vote', ['data' => $data]);
        }catch(\Exception $e){
            return redirect()->route('homepage.login');

        }

    }

    public function voteVotedStore(Request $req, $id, $key, $blt_key){
        // dd($id);
        $id_key = md5($id);
        $value = $req[$id_key];
        DB::beginTransaction();
        try{
            $election = Elections::where('private_key', 'LIKE', "%{$id}%")->first();
            $voter = Voter::where('election_id', $election->public_key)->first();
            $ballot = Ballot::where('election_id', $election->public_key)->first();

            $elCount = json_decode($election->counters, true);
            $elCount[$value]+=1;
            $voter->voted = 'true';
            $ballot->assigned = '1';
            $ballot->voted = '1';
            $ballot->voted_on = Carbon::now('Asia/Jakarta');
            $ballot->result = json_encode([$value]);
            $election->counters = json_encode($elCount);

            $voter->save();
            $ballot->save();
            $election->save();

            DB::commit();
            $id_election = base64_encode(explode("_", $election->private_key)[0]);
            return redirect()->route('ballot.details', ['id' => $id_election, 'ballot_uid' => $ballot->ballot_uuid]);
        }catch(\Exception $e){
            DB::rollback();
        }
    }

    public function editElection(Request $re, $id){
        $id_bal = base64_decode($id);
        $election = Elections::where('private_key', 'LIKE', "%{$id_bal}%")->first();
        if($election){
            dd($election);
        }else{
            return redirect()->back()->with(['message' => "Election Not Found", 'icon' => 'error']);
        }
    }

    public function deleteElection(Request $req, $id){
        $id_bal = base64_decode($id);
        try{
            $election = Elections::where('private_key', 'LIKE', "%{$id_bal}%")->first();
            $ballot = Ballot::where('election_id', $election->public_key)->get();
            foreach($ballot as $key => $val){
                $sign = explode("_",$this->dec($val->signature));
                $voter = Voter::where('voter_uuid', $sign[1])->first();
                // Delete Voter & Ballot
                $val->delete();
                $voter->delete();
            }
            $election->delete();
            return redirect()->back()->with(['message' => 'Success Delete Election', 'icon' => 'success']);
        }catch(\Exception $e){
            return redirect()->back()->with(['message' => 'Election Not Found', 'icon' => 'error']);
        }
        // dd($election);
    }

    public function startElection(Request $req, $id){
        $id_bal = base64_decode($id);
        $election = Elections::where('private_key', 'LIKE', "%{$id_bal}%")->first();
        if($election){
            $election->is_active = "1";
            $election->save();
            return redirect()->back()->with(['message' => "Success Starting Election {$election->title}", 'icon' => 'success']);
        }else{
            return redirect()->back()->with(['message' => "Election Not Found", 'icon' => 'error']);
        }
    }

    public function closeElection(Request $req, $id){
        $id_bal = base64_decode($id);
        // dd($id_bal);
        $election = Elections::where('private_key', 'LIKE', "%{$id_bal}%")->first();
        if($election){
            $election->closed = "1";
            $election->save();
            return redirect()->back()->with(['message' => "Election Closed {$election->title}", 'icon' => 'info']);
        }else{
            return redirect()->back()->with(['message' => "Election Not Found", 'icon' => 'error']);
        }
    }

    public function viewBallot(Request $req, $id){
        $id_bal = base64_decode($id);
        $election = Elections::where('private_key', 'LIKE', "%{$id_bal}%")->first();
        if($election){
            $ballot = Ballot::where('election_id', $election->public_key)->get();
            $usedBallot = 0;
            foreach($ballot as $val){
                if($val->assigned === '1' && $val->voted === '1'){
                    $usedBallot+=1;
                }
            }
            $dec_data = base64_encode(explode("_", $this->dec($election->public_key))[0]);
            // dd($dec_data);
            return view('pages.ballots', ['election' => $election, 'ballot' => $ballot, 'ballot_used' => $usedBallot, 'id' => $id, 'dec_data' => $dec_data]);
        }else{
            return redirect()->route('homepage.login')->with(['message' => 'Ballot Not Found', 'icon' => 'error']);
        }
    }

    public function detailBallot(Request $req, $id, $ballot_uid){
        // dd([$id, $ballot_uid]);
        $id_bal = base64_decode($id);
        $election = Elections::where('private_key', 'LIKE', "%{$id_bal}%")->first();
        if($election->is_active === '0'){
            return redirect()->back()->with(['message' => 'Election are not Started', 'icon' => 'info']);
        }
        // Cek ballot punya sendiri
        if($election){
            $ballot = Ballot::where(['election_id' => $election->public_key, 'ballot_uuid' => $ballot_uid])->first();
            $sign = explode("_",$this->dec($ballot->signature));
            $voter = Voter::where('voter_uuid', $sign[1])->first();
            if($ballot->voted === '1' && $voter->voted === 'true'){
                $user = AuthUser::where('email', $voter->email)->firstOrFail();
                // dd('lkjasdlkjasd');
                // dd([$election, $ballot, $voter]);
                $data = [
                    'title' => $election->title,
                    // 'email' => $voter->email,
                    // 'first_name' => $user->first_name,
                    // 'last_name' => $user->last_name,
                    'pilihan' => $ballot->ballot_content,
                    'answer' => $ballot->result,
                    'ballot_uuid' => $ballot->ballot_uuid,
                    'ballot_signature' => base64_encode($ballot->signature),
                    // 'election_model' => $election->ballot_model,
                    'election_public_key' => base64_encode($election->public_key),
                    'election_public_key_dec' => $this->dec($election->public_key),
                    'voted_on' => $ballot->voted_on,
                ];
                return view('pages.ballots_detail', ['data' => $data]);
            }else{
                if($election->closed === '1'){
                    // To List Result
                    return redirect()->route('election.result', ['id' => $id])->with(['message' => 'Election is Closed', 'icon' => 'warning']);
                }else{
                    return redirect()->route('dashboard.vote.vote', ['id' => $id, 'key' => base64_encode($ballot->signature)]);
                }
                // return redirect()->back()->with(['message' => "Have not vote yet", 'icon' => 'info']);
            }
        }else{
            return redirect()->route('homepage.login')->with(['message' => 'Ballot Not Found', 'icon' => 'error']);
        }
    }

    public function electionResult(Request $req, $id){
        $id_bal = base64_decode($id);
        $election = Elections::where('private_key', 'LIKE', "%{$id_bal}%")->first();
        $ballot = Ballot::where(['election_id' => $election->public_key])->get();
        $question = json_decode($election->ballot_model)->answer;
        $counter = json_decode($election->counters);
        $countData = (Array)$counter;
        // dd($countData);
        foreach($countData as $key => $val){
            $countData[$key] = 0;
        }
        // dd($countData);
        foreach($ballot as $val){
            $val->result = json_decode($val->result);
            if(count($val->result) > 0){
                $countData[$val->result[0]] += 1;
            }

        }
        $counter = (Array)$counter;
        if(count($counter) === count($countData)){
            $data = [
                'title' => $election->title,
                'counted' => $countData,
                'election_public_key' => base64_encode($election->public_key),
                'election_public_key_dec' => $this->dec($election->public_key),
            ];
            return view('pages.election_result', ['data' => $data]);
        }else{
            return redirect()->back()->with(['message' => 'Election Fraud', 'icon' => 'error']);
        }
        // dd([$countData, $counter]);
    }
}
