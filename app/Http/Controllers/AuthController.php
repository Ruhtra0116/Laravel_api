<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    //register user
    public function register(Request $request){

        //validate fields
        $attrs = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed'
        ]);

        //create user
        $user = User::create([
            'name' => $attrs['name'],
            'email' => $attrs['email'],
            'password' => bcrypt($attrs['password'])
        ]);

        //return user n token in response
        return response([
            'user' => $user,
            'token' => $user->createToken('secret')->plainTextToken
        ], 200);
    }

    //login user
    public function login(Request $request){

        //validate fields
        $attrs = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        // attempt login
        if(!Auth::attempt($attrs))
        {
            return response([
                'message' => 'Invalid credentials.'
            ], 403);
        }

        //return user & token in response
        return response([
            'user' => auth()->user(),
            'token' => auth()->user()->createToken('secret')->plainTextToken
        ], 200);
    }

     // logout user
     public function logout()
     {
         auth()->user()->tokens()->delete();
         return response([
             'message' => 'Logout success.'
         ], 200);
     }

     // get user details
    public function user()
    {
        return response([
            'user' => auth()->user()
        ], 200);
    }

    // update user
    // public function update(Request $request)
    // {
    //     $attrs = $request->validate([
    //         'name' => 'required|string'
    //     ]);

        
    //     $image = $this->saveImage($request->image, 'image');

    //     auth()->user()->update([
    //         'name' => $attrs['name'],
    //         'image' => $image
    //     ]);

    //     return response([
    //         'message' => 'User updated.',
    //         'user' => auth()->user()
    //     ], 200);
    // }

    


    public function index(): View
    {
        return view('imageUpload');
    }

    public function image(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'nullable|image|mimes:jpg,png,bmp',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation fails',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = auth()->user();

        if ($request->hasFile('image')) {
            if ($user->image) {
                $old_path = public_path('images') . $user->image;
                if (File::exists($old_path)) {
                    File::delete($old_path);
                }
            }

            $image = $request->file('image');
            $image_name = time() . '.' . $image->getClientOriginalExtension();

            $image->move(public_path('images'), $image_name);
        } else {
            $image_name = $user->image;
        }

        $user->update([
            'image' => $image_name,
        ]);

        return response()->json([
            'message' => 'Profile successfully updated',
            'user' => $user,
       ], 200);
    }

//     public function store(Request $request)
// {
//     $request->validate([
//         'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
//     ]);
    
//     $imageName = time().'.'.$request->image->extension();  
     
//     $request->image->move(public_path('images'), $imageName);

//     // Assuming you want to store the image path or filename in the database
//     auth()->user()->update(['image' => 'images/'.$imageName]);

//     return response([ 
//         'success'=> 'You have successfully uploaded the image.',
//         'image' => $imageName  
//     ]);
                    
// }



    
}
