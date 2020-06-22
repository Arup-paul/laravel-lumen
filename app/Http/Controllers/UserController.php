<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use PDOException;

class UserController extends Controller {

    public function index(){

        $users = app('db')->table('users')->get();
        return response()->json($users);
    }

    public function create( Request $request ) {

        try {
            $this->validate( $request, [
                'fullname' => 'required',
                'username' => 'required|min:6',
                'email' => 'required|email',
                'password' => 'required|min:6',
            ] );
        } catch ( ValidationException $e ) {
            return response()->json( [
                'success' => false,
                'message' => $e->getMessage(),
            ], 442 );
        }

        try {
            $id = app( 'db' )->table( 'users' )->insertGetId( [
                'fullname' => trim( $request->input( 'fullname' ) ),
                'username' => strtolower( trim( $request->input( 'username' ) ) ),
                'email' => strtolower( trim( $request->input( 'email' ) ) ),
                'password' => app( 'hash' )->make( $request->input( 'password' ) ),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ] );

           $user = app('db')->table('users')->select('fullname','username','email')->where('id',$id)->first();

            return response()->json([
               'id' => $id,
               'fullname' => $user->fullname,
               'username' => $user->username,
               'email' => $user->email,
            ],201);
        } catch ( \PDOException $e ) {
            return response()->json( [
                'success' => false,
                'message' => $e->getMessage(),
            ], 400 );
        }

    }

}
