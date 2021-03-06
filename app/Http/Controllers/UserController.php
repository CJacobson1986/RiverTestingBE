<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Image;
use Response;
use Purifier;
use Hash;
use Auth;
use JWTAuth;
use App\User;
use App\Admin;

class UserController extends Controller
{
  public function __construct() {
    $this->middleware('jwt.auth', ['only' => ['get', 'update', 'review']]);
  }

  # token -> user
  public function get() {
    $id = Auth::id();
    $user = User::find($id);
    if(empty($user)) {
      return Response::json(['error' => 'User does not exist', 'id' => $id]);
    }

    return Response::json([
      'user' => $user,
      'success' => 'User is logged in'
    ]);
  }

  # ?page=pageNum -> users
  public function index() {
    $users = User::orderBy('id', 'asc')->paginate(12);

    return Response::json(['users' => $users]);
  }

  # search_term -> users
  public function search($search_term) {
    $jobs = User::where('name', 'LIKE', "%$search_term%")->
      orWhere('location', 'LIKE', "%$search_term%")->get();

    return Response::json(['jobs' => $jobs]);
  }

  # id -> user
  public function show($id) {
    $user = User::find($id);
    if(empty($user)) {
      return Response::json(['error' => 'User does not exist', 'id' => $id]);
    }

    return Response::json(['user' => $user]);
  }

  # name, email, password -> user
  public function store(Request $request) {
    $rules = [
      'fullName' => 'required',
      'phoneNumber' => 'required',
      'email' => 'required',
      'password' => 'required',
      'role_id' => 'required',
      'address' => 'required',
      'certification' => 'required',
      'certificationId' => 'required'
    ];

    $validator = Validator::make(Purifier::clean($request->all()), $rules);
    if($validator->fails()) {
      return Response::json(['error' => 'Please fill out all fields.']);
    }

    $email = $request->input('email');
    $check_email = User::where('email', '=', $email)->first();
    if(!empty($check_email)) {
      return Response::json(['error' => 'That email is already taken.']);
    }

    $fullName =  $request->input('fullName');
    $phoneNumber = $request->input('phoneNumber');
    $role = $request->input('role_id');
    $address = $request->input('address');
    $certification = $request->input('certification');
    $certificationId = $request->input('certificationId');
    $password = $request->input('password');
    $password = Hash::make($password);

    $user = new User;
    $user->email = $email;
    $user->fullName = $fullName;
    $user->phoneNumber = $phoneNumber;
    $user->password = $password;
    $user->role_id = $role;
    $user->address = $address;
    $user->certification = $certification;
    $user->certificationId = $certificationId;
    $user->save();

    return Response::json([
      'success' => 'Thanks for signing up! When authorized you will be able to log-in.',
      'user' => $user
    ]);
  }

  # token, bio, phone, location -> user
  public function update(Request $request) {
    $rules = [
      'fullName' => 'required',
      'phoneNumber' => 'required',
      'testingLocation' => 'required',
      'address' => 'required',
      'certification' => 'required',
      'certificationId' => 'required',
      'pickUpDate' => 'required',
      'testingFrequency' => 'required',
    ];

    $validator = Validator::make(Purifier::clean($request->all()), $rules);
    if($validator->fails()) {
      return Response::json(['error' => 'Please fill out all fields.']);
    }

    $id = Auth::id();
    $user = User::find($id);
    if(empty($user)) {
      return Response::json(['error' => 'User does not exist', 'id' => $id]);
    }

    $fullName = $request->input('fullName');
    $phoneNumber = $request->input('phoneNumber');
    $testingLocation = $request->input('testingLocation');
    $address = $request->input('address');
    $certification = $request->input('certification');
    $certificationId = $request->input('certificationId');
    $pickUpDate = $request->input('pickUpDate');
    $testingFrequency = $request->input('testingFrequency');

    $user->fullName = $fullName;
    $user->phoneNumber = $phoneNumber;
    $user->testingLocation = $testingLocation;
    $user->address = $address;
    $user->certification = $certification;
    $user->certificationId = $certificationId;
    $user->pickUpDate = $pickUpDate;
    $user->testingFrequency = $testingFrequency;

    $user->save();

    return Response::json([
      'success' => 'Profile updated successfully!',
      'user' => $user
    ]);
  }

  # email, password -> token
  public function logIn(Request $request) {
    $rules = [
      'email' => 'required',
      'password' => 'required'
    ];

    $validator = Validator::make(Purifier::clean($request->all()), $rules);
    if($validator->fails()) {
      return Response::json(['error' => 'Please fill out all fields.']);
    }

    $email = $request->input('email');
    $password = $request->input('password');
    $credentials = compact('email', 'password');

    $token = JWTAuth::attempt($credentials);

    if ($token == false) {
      return Response::json(['error' => 'Wrong Email/Password']);
    }
    else {
      $user = User::find(Auth::id());
      return Response::json([
        'token' => $token,
        'success' => 'Logged in successfully.',
        'user' => $user
      ]);
    }
  }

  public function review(Request $request) {
    $rules = [
      'user_id' => 'required',
      'approved' => 'required'
    ];

    $validator = Validator::make(Purifier::clean($request->all()), $rules);
    if($validator->fails()) {
      return Response::json(['error' => 'Please fill out all fields.']);
    }

    $admin_id = Auth::id();
    $admin = !empty(Admin::where('user_id', '=', $admin_id)->first());

    if(!$admin) {
      return Response::json(['error' => 'You are not an admin']);
    }

    $user = User::find($request->input('user_id'));

    if(empty($user)) {
      return Response::json(['error' => 'User does not exist']);
    }

    $user->approved = $request->input('approved');
    $user->reviewed = 1;
    $user->save();

    return Response::json([
      'success' => 'User approval status changed',
      'user' => $user
    ]);
  }
}
