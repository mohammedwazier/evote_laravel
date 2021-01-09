<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\SendingEmail;

use Hash;
use Ramsey\Uuid\Uuid;

use Carbon\Carbon;
use Session;

use App\Models\AuthUser;


class HomepageController extends Controller
{
    public function index(){
        return view('pages.login');
    }

    public function loginProcess(Request $req){
        $check = AuthUser::where('email', htmlspecialchars($req->email))->first();
        if($check){
            if(Hash::check($req->password, $check->password)){
                if($check->verified === 'false'){
                    return redirect()->back()->with(['message' => 'Please verify your account', 'icon' => 'error']);
                }else{
                    Session::put('user', $check);
                    return redirect()->route('dashboard.index');
                }
            }else{
                return redirect()->back()->with(['message' => 'Wrong Password', 'icon' => 'error']);
            }
        }else{
            return redirect()->back()->with(['message' => 'Account not Found', 'icon' => 'error']);
        }
    }

    public function register(){
        return view('pages.register');
    }

    public function registerProcess(Request $req){
        $uuid1 = Uuid::uuid1();
        $check = AuthUser::where('email', htmlspecialchars($req->email))->get();
        if(count($check) > 0){
            return redirect()->route('homepage.login')->with(['message' => 'Your email has been registered', 'icon' => 'warning']);
        }
        $user = new AuthUser;

        $user->first_name = htmlspecialchars($req->firstname);
        $user->last_name = htmlspecialchars($req->lastname);
        $user->email = htmlspecialchars($req->email);
        $user->password = Hash::make($req->password);
        if($req->manager){
            $user->is_manager = 1;
        }else{
            $user->is_manager = 0;
        }
        $user->created_at = Carbon::now('Asia/Jakarta');

        $user->registration_key = $uuid1->toString();
        $raw = $user->registration_key.$user->email.$user->created_at;
        $user->registration_password_key = md5($raw);

        $title = "Registration Evote";
        $encode = route('homepage.verification', ['key' => base64_encode($raw)]);
        $body = "Link to Verification Registration : {$encode}";

        $data = [
            "email" => $req->email,
            "title" => $title,
            "body" => $body
        ];
        dispatch(new SendingEmail($data));
        $user->save();
        return redirect()->route('homepage.login')->with(['message' => 'Please check your email, verification link was send to your email', 'icon' => 'success']);
    }

    public function verification(Request $req, $key){
        $decode = base64_decode($key);
        $md = md5($decode);

        $user = AuthUser::where('registration_password_key', $md)->first();
        if($user){
            $user->verified = 'true';
            $user->updated_at = Carbon::now('Asia/Jakarta');
            $raw = md5($user->registration_key.$user->email.$user->updated_at);
            $user->registration_password_key = $raw;
            $user->save();
            return redirect()->route('homepage.login')->with(['message' => 'Success Verification your Account, please Login to use Application', 'icon' => 'success']);
        }else{
            return redirect()->route('homepage.login')->with(['message' => 'Invalid Verification Link', 'icon' => 'error']);
        }
    }

    public function logout(){
        Session::flush();
        return redirect()->route('homepage.login');
    }
}
