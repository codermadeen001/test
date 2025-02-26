<?php

namespace App\Http\Controllers;
use App\Models\ClothItem;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ClothItemController extends Controller
{
     public function __construct()
 {
        $this->middleware('auth:sanctum'); 
    }   

    public function accountDetails(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json(["success" => false, 'message' => 'Unauthenticated']);
        } else {
            return response()->json(["success" => true, 'message' => 'authenticated', 'data' => $user]);
        }
    }

    public function my_cloth(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $cloth = ClothItem::where('email', $user->email)->get();
        return response()->json(['success' => true, 'data' => $cloth]);
    }

    public function clothSearch(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json(["success" => false, 'message' => 'Unauthenticated']);
        }

        $query = $request->get('query');
        $cloth = ClothItem::where('email', $user->email)
                          ->where(function($q) use ($query) {
                              $q->where('name', 'like', "%$query%")
                              ->orWhere('category', ' like',"%$query%")
                                ->orWhere('description', 'like', "%$query%");
                          })
                          ->get();
        
        return response()->json(['success' => true, 'data' => $cloth]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::guard('sanctum')->user();
        
        // Handle file upload
        $filePath = $request->file('photo')->store('public/photos');
        $fileName = basename($filePath);

        $clothItem = ClothItem::create([
            "email" => $user->email,
            'name' => $request->name,
            "description" => $request->description,
            'category' => $request->category,
            "image_link" => $fileName,
            "favorite" => false
        ]);
    
        return response()->json([
            "success" => true, 
            "message" => $request->name." Uploaded!",
            "data" => $clothItem
        ]);
    }

    public function update(Request $request, string $id)
    {
        $user = Auth::guard('sanctum')->user();
        $cloth = ClothItem::findOrFail($id);

        if ($cloth->email !== $user->email) {
            return response()->json([
                "success" => false, 
                "message" => "You don't have permission to update this item"
            ], 403);
        }
        
        $dataToUpdate = [];
    
        $rules = [];
        
        if (!empty($request->name) && $request->name !== "undefined") {
            $rules['name'] = 'required|string';
            $dataToUpdate['name'] = $request->name;
        }
    
        if (!empty($request->category) && $request->category !== "undefined") {
            $rules['category'] = 'required|string';
            $dataToUpdate['category'] = $request->category;
        }
        
        if (!empty($request->description) && $request->description !== "undefined") {
            $rules['description'] = 'required|string';
            $dataToUpdate['description'] = $request->description;
        }


        if ($request->hasFile('photo')) {
            $rules['photo'] = 'required|image|mimes:jpeg,png,jpg,gif|max:2048';
            
            // Handle file upload
            $filePath = $request->file('photo')->store('public/photos');
            $fileName = basename($filePath);

            if ($cloth->image_link) {
                Storage::delete('public/photos/' . $cloth->image_link);
            }
            
            $dataToUpdate['image_link'] = $fileName;
        }

        if (empty($dataToUpdate)) {
            return response()->json(["success" => false, "message" => "No valid fields to update"]);
        }
    

        $validator = Validator::make($request->only(array_keys($dataToUpdate)), $rules);
    
        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->errors()]);
        }
    

        $cloth->update($dataToUpdate);
    
        return response()->json([
            "success" => true, 
            "message" => "Data updated successfully", 
            "updated" => array_keys($dataToUpdate),
            "data" => $cloth
        ]);
    }

    public function like(string $id)
    {
        $user = Auth::guard('sanctum')->user();
        $clothItem = ClothItem::findOrFail($id);

        if ($clothItem->email !== $user->email) {
            return response()->json([
                "success" => false, 
                "message" => "You don't have permission to like this item"
            ], 403);
        }
        

        $clothItem->favorite = !$clothItem->favorite;
        $clothItem->save();
        
        $message = $clothItem->favorite ? 
            $clothItem->name . " added to favorites" : 
            $clothItem->name . " removed from favorites";

        return response()->json([
            "success" => true, 
            "message" => $message,
            "data" => $clothItem
        ]);
    }

    public function destroy(string $id)
    {
        $user = Auth::guard('sanctum')->user();
        $clothItem = ClothItem::findOrFail($id);
        
       
        if ($clothItem->email !== $user->email) {
            return response()->json([
                "success" => false, 
                "message" => "You don't have permission to delete this item"
            ], 403);
        }
        
        $name = $clothItem->name;
        
 
        if ($clothItem->image_link) {
            Storage::delete('public/photos/' . $clothItem->image_link);
        }
        
        if ($clothItem->delete()) {
            return response()->json(["success" => true, "message" => $name . " deleted"]);
        } else {
            return response()->json(["success" => false, "message" => "Unable to delete " . $name]);
        }
    }
}



