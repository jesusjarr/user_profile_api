<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\User;

class UsersController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // config logs
        $logFile = 'user_controller.log';
        Log::useDailyFiles(storage_path().'/logs/'.$logFile);
    }

    /**
     * Create new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // the variables are received
        $this->user = $request->all();
        
        // validation is performed
        $validator = Validator::make($this->user, [
            'name'      => 'required',
            'email'     => 'required|email|unique:users',
            'Image'     => 'required'
        ]);
    
        // validation fails
        if ($validator->fails()) {
            // reply message
            $result = [
                'code'      => 500,
                'message'   => $validator->errors()->all()
            ];
            
            // register log
            Log::error('    users.store     '.$result['code'].'     '.json_encode($validator->errors()->all()));
        } else {
            // create new user instance 
            $user = new User;
            
            // include info user
            $user->name     =   $this->user['name']; 
            $user->email    =   $this->user['email'];
            $user->Image    =   $this->user['Image'];
            
            // register a user
            if ($user->save()) {
                // reply message
                $result = [
                    'code'      => 200,
                    'message'   => 'User successfully registered'
                ];
                
                // register log
                Log::info('     users.store     '.$result['code'].'     '.json_encode($result));
            } else {
                // reply message
                $result = [
                    'code'      => 500,
                    'message'   => 'Failed to register user'
                ];
                
                // register log
                Log::error('    users.store     '.$result['code'].'     '.json_encode($result));
            }
        }
        
        // send the result now
        return response()->json($result, $result['code']);
    }

    /**
     * Consult a user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // user search is performed
        $this->user = User::find($id);
        
        // validate that the user exists
        if (!is_null($this->user)) {
            // register log
            Log::info('     users.show      200     '.json_encode($this->user));
            
            // send the result now
            return response()->json($this->user, 200);
        } else {
            // reply message
            $result = [
                'code'      => 404,
                'message'   => 'User not found'
            ];  
            
            // register log
            Log::warning('  users.show      '.$result['code'].'     '.json_encode($result));
            
            // send the result now
            return response()->json($result, $result['code']);
        }
    }

    /**
     * Update user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // user search is performed
        $user = User::find($id);
            
        // validate that the user exists
        if (!is_null($user)) {
                
            // the variables are received
            $this->user = $request->all();
            
            // initialize rule variable
            $rules = [];
            
            // creating rules
            if (isset($this->user['name']) && !is_null($this->user['name']) && ($user->name != $this->user['name']))        $rules['name']  = 'required';
            if (isset($this->user['email']) && !is_null($this->user['email']) && ($user->email != $this->user['email']))    $rules['email'] = 'required|email|unique:users';
            if (isset($this->user['Image']) && !is_null($this->user['Image']) && ($user->Image != $this->user['Image']))    $rules['Image'] = 'required';
            
            // validation is performed
            $validator = Validator::make($this->user, $rules);
    
            // validation falis
            if ($validator->fails()) {
                // Reply message
                $result = [
                    'code'      => 500,
                    'message'   => $validator->errors()->all()
                ];
                
                // register log
                Log::error('    users.update    '.$result['code'].'     '.json_encode($validator->errors()->all()));
            } else {
                
                // include info user
                if (isset($this->user['name']) && !is_null($this->user['name']))    $user->name     =   $this->user['name']; 
                if (isset($this->user['email']) && !is_null($this->user['email']))  $user->email    =   $this->user['email'];
                if (isset($this->user['Image']) && !is_null($this->user['Image']))  $user->Image    =   $this->user['Image'];
                
                // update a user
                if (!is_null($user) && $user->save()) {
                    // reply message
                    $result = [
                        'code'      => 200,
                        'message'   => 'User successfully modified'
                    ];
                    
                    // register log
                    Log::info('     users.update    '.$result['code'].'     '.json_encode($result));
                } else {
                    // reply message
                    $result = [
                        'code'      => 500,
                        'message'   => 'Failed to update user'
                    ];
                    
                    // register log
                    Log::error('    users.update    '.$result['code'].'     '.json_encode($result));
                }
            }
        
        } else {
            // reply message
            $result = [
                'code'      => 404,
                'message'   => 'User not found'
            ]; 
            
            // register log
            Log::warning('  users.update    '.$result['code'].'     '.json_encode($result));
        }
        
        // send the result now
        return response()->json($result, $result['code']);
    }

    /**
     * Delete user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // user search is performed
        $this->user = User::find($id); 
        
        // validate that the user exists
        if (!is_null($this->user)) {
            // we delete the user
            if ($this->user->delete()) {
                // reply message
                $result = [
                    'code'      => 200,
                    'message'   => 'User successfully deleted'
                ];
                
                // register log
                Log::info('     users.destroy   '.$result['code'].'     '.json_encode($result));
            } else {
                // reply message
                $result = [
                    'code'      => 500,
                    'message'   => 'Failed to delete user'
                ];
                
                // register log
                Log::error('        users.destroy   '.$result['code'].'     '.json_encode($result));
            }
        } else {
            // reply message
            $result = [
                'code'      => 404,
                'message'   => 'User not found'
            ];  
            
            // register log
            Log::warning('  users.destroy   '.$result['code'].'     '.json_encode($result));
        }
        
        // send the result now
        return response()->json($result, $result['code']);
    }
    
    /**
     * Upload image a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function uploadImage(Request $request, $id)
    {
        // user search is performed
        $this->user = User::find($id);
            
        // validate that the user exists
        if (!is_null($this->user)) {
            
            // the variables are received
            $user = $request->all();
            
            // validation is performed
            $validator = Validator::make($user, [
                'Image'     => 'required|mimes:png,jpg,jpeg'
            ]);
        
            // validation fails
            if ($validator->fails()) {
                // reply message
                $result = [
                    'code'      => 500,
                    'message'   => $validator->errors()->all()
                ];
                
                // register log
                Log::error('    users.upload    '.$result['code'].'     '.json_encode($result));
            } else {
                // create name image
                $imageName = $this->user->id . '.' . date('YmdHis') . '.' .
                $request->file('Image')->getClientOriginalExtension();
                
                // save image
                $request->file('Image')->move(
                    base_path() . '/public/images/', $imageName
                );
                
                // url image
                $this->user->Image = url('images/'.$imageName); 
                
                // update a user
                if ($this->user->save()) {
                    // reply message
                    $result = [
                        'code'      => 200,
                        'message'   => 'Uploaded successfully the image'
                    ];
                    
                    // register log
                    Log::info('     users.upload    '.$result['code'].'     '.json_encode($result));
                } else {
                    // reply message
                    $result = [
                        'code'      => 500,
                        'message'   => 'Failed uploading image'
                    ];
                    
                    // register log
                    Log::error('    users.upload    '.$result['code'].'     '.json_encode($result));
                }
            }
        } else {
            // reply message
            $result = [
                'code'      => 404,
                'message'   => 'User not found'
            ];  
            
            // register log
            Log::warning('  users.upload    '.$result['code'].'     '.json_encode($result));
        }
        
        // send the result now
        return response()->json($result, $result['code']);
    }
}
