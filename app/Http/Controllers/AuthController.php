<?php

namespace App\Http\Controllers;

use App\User;
use App\Group;
use App\Contact;
use App\Attachment;
use App\Survey;
use App\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use DB;
use Image;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function register(Request $req)
    {
        $rules = [
            'name' => 'required|string',
            'email' => 'required|string|unique:users',
            'password' => 'required|string|min:6'
        ];
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $pass = substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyz"), 0, 6);

        $user = User::create([
            'name' => $req->name,
            'email' => $req->email,
            'password' => Hash::make($req->password),
            'code' => $pass,
        ]);
        if ($user) {
            $token = $user->createToken('Personal Access Token')->plainTextToken;
            $response = ['Done' => true, 'user' => $user, 'token' => $token];
            return response()->json($response, 200);
        } else {
            $response = ['Done' => False];
            return response()->json($response, 500);
        }
        // $token = $user->createToken('Personal Access Token')->plainTextToken;
        // $response = ['user' => $user, 'token' => $token];
        // return response()->json($response, 200);
    }

    public function login(Request $req)
    {
        $rules = [
            'email' => 'required',
            'password' => 'required|string'
        ];
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $user = User::where('email', $req->email)->first();
        if ($user && Hash::check($req->password, $user->password)) {
            $token = $user->createToken('Personal Access Token')->plainTextToken;
            $response = ['message' => true,'user' => $user, 'token' => $token];
            return response()->json($response, 200);
        }
        $response = ['message' => false];
        return response()->json($response, 400);
    }


   ///////////////////////// groups //////////////////////////

   public function groups(Request $req){

    $rules = [
        'name' => 'required',
        'user_id' => 'required'
    ];
    $validator = Validator::make($req->all(), $rules);
    if ($validator->fails()) {
        return response()->json($validator->errors(), 400);
    }
    $group = new Group;
    $group->name = $req->name;
    $group->user_id = $req->user_id;
    $result = $group->save();

    if ($result) {
        return response()->json($group);
    } else {
        return response()->json(["Result" => 'Failed to create data'], 500);
    }
    }
        // public function getgroups(){
        //     return Group::all();
        // }

        public function getgroups($id=null){
            return $id?Group::find($id):Group::all();
        }


        public function updateGroup(Request $request, $id) {
        $group = Group::find($id);
        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }
        $group->name = $request->name;
        $group->user_id = $request->user_id;
        $group->save();

        return response()->json(['message' => 'Group updated successfully', 'group' => $group]);
    }

        public function deleteGroup($id){
            
        $group = Group::find($id);

        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        $group->delete();

        return response()->json(['message' => 'Group deleted successfully']);
    }

      

      public function getSingleGroup($user_id, $id = null){
         
        if ($id) {
            $group = Group::where('user_id', $user_id)
                      ->where('id', $id)
                      ->first();

          if (!$group) {
            return response()->json(['error' => 'Group not found'], 404);
         }

        return response()->json($group);
    } else {
        $groups = Group::where('user_id', $user_id)->get();
        return response()->json($groups);
    }
 }

 public function deleteSingleGroup($user_id, $id){
            
    if ($id) {
        $group = Group::where('user_id', $user_id)
                  ->where('id', $id)
                  ->first();

      if (!$group) {
        return response()->json(['error' => 'Group not found'], 404);
     }

    $group->delete();

    return response()->json(['message' => 'Group deleted successfully']);
}
 }

 public function updateSingleGroup(Request $request, $user_id, $id ) {
    if ($id) {
        $group = Group::where('user_id', $user_id)
                  ->where('id', $id)
                  ->first();

        if (!$group) {
            return response()->json(['error' => 'Group not found'], 404);
        }

        $group->name = $request->name;
        $group->user_id = $user_id; // Use the provided $user_id, not $request->user_id
        $group->save();

        return response()->json(['message' => 'Group updated successfully', 'group' => $group]);
    }
}

///////////////////////// groups //////////////////////////



/////////////////////////// contacts ///////////////////////////

        public function contacts(Request $req){

            // $user = Auth::user();
            // if (!$user) {
            //     return response()->json(['message' => 'Unauthenticated'], 401);
            // }
            $rules = [
                'name' => 'required',
                'email' => 'required',
                'group_id' => 'required',
                'user_id' => 'required'
            ];
            $validator = Validator::make($req->all(), $rules);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }
            $contact = new Contact;
            $contact->name = $req->name;
            $contact->email = $req->email;
            $contact->user_id = $req->user_id;
            $contact->group_id = $req->group_id;
            $result = $contact->save();
        
            if ($result) {
                return response()->json($contact);
            } else {
                return response()->json(["Result" => 'Failed to create data'], 500);
            }
        }

        public function getcontacts($id=null){
            return $id?Contact::find($id):Contact::all();
        }

        public function deleteContact($id){
            
            $contact = Contact::find($id);
    
            if (!$contact) {
                return response()->json(['message' => 'Contact not found'], 404);
            }
    
            $contact->delete();
    
            return response()->json(['message' => 'Contact deleted successfully']);
        }

        public function updateContact(Request $req, $id) {
            
            $contact = Contact::find($id);
            if (!$contact) {
                return response()->json(['message' => 'Contact not found'], 404);
            }
            $contact->name = $req->name;
            $contact->email = $req->email;
            $contact->user_id = $req->user_id;
            $contact->group_id = $req->group_id;
            $contact->save();
    
            return response()->json(['message' => 'Contact updated successfully', 'contact' => $contact]);
        }

        public function getSingleContact($user_id, $id = null){
            if ($id) {
                $contact = Contact::where('user_id', $user_id)
                              ->where('id', $id)
                              ->first();
        
                if (!$contact) {
                    return response()->json(['error' => 'Contact not found'], 404);
                }
        
                return response()->json($contact);
            } else {
                $contacts = Contact::where('user_id', $user_id)->get();
                return response()->json($contacts);
            }
        }

        public function deleteSingleContact($user_id, $id ){
            
            if ($id) {
                $contact = Contact::where('user_id', $user_id)
                          ->where('id', $id)
                          ->first();
        
              if (!$contact) {
                return response()->json(['error' => 'Contact not found'], 404);
             }
        
            $contact->delete();
        
            return response()->json(['message' => 'Contact deleted successfully']);
        }
         }

         public function updateSingleContact(Request $request, $user_id, $id ) {
            if ($id) {
                $contact = Contact::where('user_id', $user_id)
                          ->where('id', $id)
                          ->first();
        
                if (!$contact) {
                    return response()->json(['error' => 'Group not found'], 404);
                }
        
                $contact->name = $request->name;
                $contact->user_id = $user_id; // Use the provided $user_id, not $request->user_id
                $contact->save();
        
                return response()->json(['message' => 'Contact updated successfully', 'contact' => $contact]);
            }
        }
        
/////////////////////////// contacts ///////////////////////////



/////////////////////////// <attachments> ///////////////////////////

public function multi_attachment_upload(Request $request)
{
    $validator = Validator::make($request->all(), [
        'survey_id' => 'required',
        'file' => 'required|array',
        'file.*' => 'required|mimes:doc,docx,pdf,txt,csv,xml',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 400);
    }

    $attachments = [];

    if ($request->hasFile('file')) {
        foreach ($request->file('file') as $file) {
            $path = $file->store('public/files');
            $name = $file->getClientOriginalName();
            $surveyId = $request->input('survey_id');

            $attachment = new Attachment();
            $attachment->name = $name;
            $attachment->file = $path;
            $attachment->survey_id = $surveyId;
            $attachment->save();

            $attachments[] = $attachment;
        }
    }

    return response()->json([
        'success' => true,
        'message' => 'Files successfully uploaded',
        'attachments' => $attachments
    ]);
}


public function upload(Request $request)
{
    $validator = Validator::make($request->all(), [ 
        'survey_id' => 'required',
        'file' => 'required|mimes:doc,docx,pdf,txt,csv,xml|max:2048',
    ]);   

    if ($validator->fails()) {            
        return response()->json(['error' => $validator->errors()], 400);                        
    }  

    if ($file = $request->file('file')) {

        $path = $file->store('public/files');
        $name = $file->getClientOriginalName();
        $surveyId = $request->input('survey_id');
        
        $attachment = new Attachment();
        $attachment->name = $name;
        $attachment->file = $path;
        $attachment->survey_id = $surveyId;
        $attachment->save();
          
        return response()->json([
            'success' => true,
            'message' => 'File successfully uploaded',
            'attachment' => $attachment
        ]);
    }
    
    return response()->json([
        'error' => 'File not found in the request',
    ], 400);
}

public function attachment($surveyId)
{
    $attachments = Attachment::where('survey_id', $surveyId)->get();

    foreach ($attachments as $attachment) {
        $attachment->file = asset('storage/files/' . basename($attachment->file));
    }

    return response()->json(['data' => $attachments]);
}
// public function attachment()
// {
//     $attachments = Attachment::all();

//     foreach ($attachments as $attachment) {
//         $attachment->file = asset('storage/files/' . basename($attachment->file));
//     }

//     return response()->json(['data' => $attachments]);
// }

/////////////////////////// </attachments> ///////////////////////////



/////////////////////////// <participants> ///////////////////////////


public function participants(Request $request)
{
    $validator = Validator::make($request->all(), [
        'contact_id' => 'required',
        'survey_id' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 400);
    }

    $participant = new Participant();
    $participant->contact_id = $request->contact_id;
    $participant->survey_id = $request->survey_id;
    $participant->save();

    return response()->json([
        'success' => true,
        'message' => 'Data successfully inserted',
        'participant' => $participant
    ]);
}


public function deleteParticipant($id){
            
    $participant = Participant::find($id);

    if (!$participant) {
        return response()->json(['message' => 'Participant not found'], 404);
    }

    $participant->delete();

    return response()->json(['message' => 'Participant deleted successfully']);
}

// public function getparticipants()
// {
//     $participant = Participant::all();

//     return response()->json(['data' => $participant]);
// }
public function getParticipants($surveyId)
{
    $participants = Participant::where('survey_id', $surveyId)->get();

    return response()->json(['data' => $participants]);
}

public function deleteSingleParticipant($survey_id, $id){
            
    if ($id) {
        $participant = Participant::where('survey_id', $survey_id)
                  ->where('id', $id)
                  ->first();

      if (!$participant) {
        return response()->json(['error' => 'Participant not found'], 404);
     }

    $participant->delete();

    return response()->json(['message' => 'Participant deleted successfully']);
}
 }

/////////////////////////// </participants> ///////////////////////////




/////////////////////////// <surveys> ////////////////////////////////

public function create_survey(Request $request)
{
    $validator = Validator::make($request->all(), [
        'title' => 'required',
        'type' => 'required',
        'description' => 'required',
        'user_id' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 400);
    }

    $survey = new Survey();
    $survey->title = $request->title;
    $survey->type = $request->type;
    $survey->description = $request->description;
    $survey->user_id = $request->user_id;
    $survey->save();

    return response()->json([
        'success' => true,
        'message' => 'Data successfully inserted',
        'survey' => $survey
    ]);
}

public function getSingleSurvey($user_id, $id = null){
         
    if ($id) {
        $survey = Survey::where('user_id', $user_id)
                  ->where('id', $id)
                  ->first();

         if (!$survey) {
            return response()->json(['error' => 'Survey not found'], 404);
         }

        return response()->json($survey);
      } else {
           $surveys = Survey::where('user_id', $user_id)->get();
           return response()->json($surveys);
      }
 }

/////////////////////////// </surveys> ////////////////////////////////




////////////////////////////////////<users>////////////////////////////////////

  public function getusers(){
            $users = User::all();
            return response()->json($users);
        }

     
////////////////////////////////////</users>////////////////////////////////////
}