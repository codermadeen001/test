<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\AppUser;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Str;

class AppUserController extends Controller
{
    
    public function account_creation(Request $request)
    {
        $user=AppUser::where("email", $request->email)->first();
        if($user){
            return response()->json(["success" => false, "message" => "Account exists!"]);
        }
    
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:app_users',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            $validatorerrors = $validator->errors();
            return response()->json(["success" => false, "message" => "$validatorerrors"]);
        }

       
        $save=AppUser::create([
                "email" => $request->email,
                'password' => Hash::make($request->password),
            ]);

            
       
    
        if ($save){

            $userEmail = $request->email;
     
             $fromEmail = 'syeundainnocent@gmail.com';  
             $fromPassword =  "vwuergurzyjucjmc";  
     
             $mail = new PHPMailer(true);
     
             try {
                 // SMTP configuration
                 $mail->isSMTP();
                 $mail->Host = 'smtp.gmail.com';   
                 $mail->SMTPAuth = true;
                 $mail->Username = $fromEmail;    
                 $mail->Password = $fromPassword; 
                 $mail->SMTPSecure = 'tls';     
                 $mail->Port = 587;               
     
                 // Email settings
                 $mail->setFrom($fromEmail, 'Wardrobe Management System');
                 $mail->addAddress($userEmail);   
     
                 $mail->Subject = ' Welcome to Wardrobe Management System ';
                 $mail->Body = 

                 "Greetings,\n\nYour account with Wardrobe Management System has been successfully created.\n\nThank you for choosing us, hope you will enjoy our services.\n\n";

                 $mail->send();
             } catch (Exception $e) {
                
                 return response()->json(["success" => false,  "message"=>"mail not send"]);
             }
            return response()->json(["success" => true, "message" => "Account created successfully"]);
    } else {
        return response()->json(["success" => false, "message" => "Unsuccessful signup"]);
    }
    return response()->json(["success" => true, "message" => "Account created successfully"]);
    }

    







    
   

    public function login(Request $request)
    {
        $credentials=$request->only("email","password");
    
            $user=AppUser::where("email", $credentials["email"])->first();
            if(!$user){
                return response()->json(["success"=>false, "message"=>"Invalid credentials!"]);
            }
        
            $auth=($user && Hash::check($credentials["password"], $user->password));

           if($auth){
                $token = $user->createToken('authToken')->plainTextToken;
                return response()->json(["success"=>true,"token" => $token ]);
            }else{
                return response()->json(["success"=>false, "message"=>"Invalid credentials!"]);
            }

           
            

    }




    public function password_reset(Request $request)
    {
        $email=$request->email;
        $user=AppUser::where("email", $email)->first();
        if($user){
            $password=Str::random(5);
            $hashedPassword=Hash::make($password);
            $user->password=$hashedPassword;
            $user->save();
            
            $userEmail = $email;
            $fromEmail = 'syeundainnocent@gmail.com'; 
            $fromPassword =  "vwuergurzyjucjmc"; 
    
            $mail = new PHPMailer(true);
    
            try {
                // SMTP configuration
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';   
                $mail->SMTPAuth = true;
                $mail->Username = $fromEmail;    
                $mail->Password = $fromPassword; 
                $mail->SMTPSecure = 'tls';       
                $mail->Port = 587;               
                // Email settings
                $mail->setFrom($fromEmail, 'Wardrobe Management System');
                $mail->addAddress($userEmail);   
    
                $mail->Subject = 'Password Reset ';
                $mail->Body ="Password for Wardrobe Management System,\n has been reset to ".$password;
                $mail->send();
            } catch (Exception $e) {
                return response()->json(["success" => false,  "message"=>"mail not send"]);
            }

            return response()->json(["success"=>true, "message" =>"New password has been send to your email"]);
        }else{
            return response()->json(["success"=>false, "message" =>"no account found"]);
        }
       
    }




    
    public function google_login(Request $request)
    {
        if($request->userEmail==NULL||$request->userEmail==''||$request->userEmail=='undefined'){
             return response()->json(["success"=>false,"message"=>"invalid Email" ]);
        }
        $credentials=$request->only("userName", "userEmail", "userImgUrl");
         $user=AppUser::where("email", $credentials["userEmail"])->first();
         //return response()->json(["success"=>false,"message"=>"Account is suspended" ]);

      if($user){
        
            $token = $user->createToken('authToken')->plainTextToken;
            
            return response()->json(["success"=>true,"isAdmin"=>false,"message"=>"Login successful",  "token" =>  $token]); 
      }else{
            $password= Str::random(32);
            $user =AppUser::create([
                "name" => $credentials["userName"],
                "email" => $credentials["userEmail"],
                "profile_picture" => $credentials["userImgUrl"],
                'password' => Hash::make($password),
            ]);

                $userEmail = $credentials["userEmail"];
                $name=$credentials["userName"];
         
                 // Email sending logic
                 $fromEmail = 'syeundainnocent@gmail.com';  // Replace with your Gmail address
                 $fromPassword =  "vwuergurzyjucjmc";  // Replace with your Gmail app password
         
                 $mail = new PHPMailer(true);
         
                 try {
                    // SMTP configuration
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';   
                    $mail->SMTPAuth = true;
                    $mail->Username = $fromEmail;    
                    $mail->Password = $fromPassword; 
                    $mail->SMTPSecure = 'tls';     
                    $mail->Port = 587;               
        
                    // Email settings
                    $mail->setFrom($fromEmail, 'Wardrobe Management System');
                    $mail->addAddress($userEmail);   // Recipient's email
        
                    $mail->Subject = ' Welcome to Wardrobe Management System ';
                    $mail->Body = 
   "Greetings,\n\nYour account with Wardrobe Management System has been successfully created.\n\nThank you for choosing us, hope you will enjoy our services.\n\n";

                    $mail->send();
                } catch (Exception $e) {
                   
                    return response()->json(["success" => false,  "message"=>"mail not send"]);
                }
                $token = $user->createToken('authToken')->plainTextToken;
            return response()->json(["success" => true, "message" => "Account created successfully",'token'=>$token]);
       } 
          
    }


    public function logout(Request $request)
    {
        // Revoke the user's token
        $user = Auth::guard('sanctum')->user();
        
        $user->tokens()->delete();
        return response()->json(["success" => true, "message" => "Logged out"]);
       
    }







    
}


