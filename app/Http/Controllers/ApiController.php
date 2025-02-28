<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Position;
use App\Models\Token;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Tinify\Tinify;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function getToken()
    {
        // Generate a token (for simplicity, using a random string here)
        $tokenString = bin2hex(random_bytes(32));

        // Save the token to the database
        $token = new Token();
        $token->token = hash('sha256', $tokenString);
        $token->used = 0;
        $token->save();

        return response()->json(['success' => true, 'token' => $tokenString]);
    }

    public function registerUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:60',
            'email' => 'required|email|max:100',
            'phone' => 'required|regex:/^[\+]{0,1}380([0-9]{9})$/',
            'position_id' => 'required|integer|exists:\App\Models\Position,id',
            'photo' => 'required|image|mimes:jpeg,jpg|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'fails' => $validator->errors()], 422);
        }

        // Check if phone or email already exists
        if (User::where('phone', $request->phone)->exists() || User::where('email', $request->email)->exists()) {
            return response()->json(['success' => false, 'message' => 'User with this phone or email already exist'], 409);
        }

        // Save the user
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->position_id = $request->position_id;

        // Handle the photo upload
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            // Crop the image to 70x70px
            $image = Image::read($photo)->crop(70, 70, 0, 0, position: 'center');

            $sourceData=$image->toJpeg();
            Tinify::setKey(env('TINIFY_KEY'));
            $photoPath='images/'.Str::random() . '.' . $photo->getClientOriginalExtension();
            $resultData = \Tinify\fromBuffer($sourceData)->toBuffer();
            
            Storage::disk('public')->put(
                $photoPath,
                $resultData
            );
            
            $user->photo = $photoPath;
        }

        $user->save();

        return response()->json(['success' => true,'user_id'=>$user->id, 'message' => 'New user successfully registered']);
    }

    public function getUsers(Request $request)
    {
        $page = $request->query('page', 1);
        $count = $request->query('count', 5);

        $users = User::paginate($count, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'page' => $users->currentPage(),
            'total_pages' => $users->lastPage(),
            'total_users' => $users->total(),
            'count' => $users->perPage(),
            'links' => [
                'next_url' => $users->nextPageUrl(),
                'prev_url' => $users->previousPageUrl()
            ],
            'users' => $users->items()
        ]);
    }

    public function getUserById($id)
    {
        if (!is_numeric($id) || intval($id) != $id) {
            return response()->json([
                'success' => false,
                'message' => 'The user with the requested id does not exist',
                'fails' => [
                    'userId' => ['The user must be an integer.']
                ]
            ], 400);
        }
        $user = User::find($id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        return response()->json(['success' => true, 'user' => $user]);
    }

    public function getPositions()
    {
        $positions = Position::all();

        if ($positions->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Positions not found'], 404);
        }

        return response()->json(['success' => true, 'positions' => $positions]);
    }
}
