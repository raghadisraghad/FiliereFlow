<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'sex' => 'nullable|in:male,female,other',
            'type' => 'required|in:student,teacher,admin',
            'profile_picture' => 'nullable|string', // base64 image
            'remove_picture' => 'nullable|in:0,1'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $validated = $validator->validated();
        
        // Update user data
        $user->name = $validated['name'];
        $user->last_name = $validated['last_name'] ?? $user->last_name;
        $user->email = $validated['email'];
        $user->sex = $validated['sex'] ?? $user->sex;
        $user->type = $validated['type'];
        
        // Handle profile picture
        if (isset($validated['remove_picture']) && $validated['remove_picture'] == '1') {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
                $user->profile_photo_path = null;
            }
        } elseif (!empty($validated['profile_picture'])) {
            $imageData = $validated['profile_picture'];
            
            // Detect type automatically from base64 header
            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $matches)) {
                $imageType = strtolower($matches[1]); // png, jpg, jpeg, gif, etc.
                
                // Normalize common extensions
                if ($imageType === 'jpeg') $imageType = 'jpg';
                
                // Remove the data prefix
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
                $decodedImage = base64_decode($imageData);
                
                if ($decodedImage === false) {
                    return response()->json(['errors' => ['profile_picture' => ['Invalid image data']]], 422);
                }
                
                // Save with correct extension
                $filename = 'user_' . $user->id . '_' . time() . '.' . $imageType;
                $path = 'profile-photos/' . $filename;
                
                if ($user->profile_photo_path) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }
                
                Storage::disk('public')->put($path, $decodedImage);
                $user->profile_photo_path = $path;
                
                if (!Storage::disk('public')->exists($path)) {
                    return response()->json(['errors' => ['profile_picture' => ['Failed to save image']]], 422);
                }
            } else {
                return response()->json(['errors' => ['profile_picture' => ['Invalid image format']]], 422);
            }
        }
        
        $user->save();
        
        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }

    
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $user = Auth::user();
        
        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'errors' => [
                    'current_password' => ['Current password is incorrect']
                ]
            ], 422);
        }
        
        // Update password
        $user->password = $request->password; // Will be hashed by mutator
        $user->save();
        
        return response()->json([
            'message' => 'Password updated successfully'
        ]);
    }
    
    public function deleteAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
            'confirm_delete' => 'required|accepted'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $user = Auth::user();
        
        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'errors' => [
                    'password' => ['Password is incorrect']
                ]
            ], 422);
        }
        
        // Delete profile picture if exists
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }
        
        // Logout user
        Auth::logout();
        
        // Delete user (this will cascade to student record if exists)
        $user->delete();
        
        return response()->json([
            'message' => 'Account deleted successfully'
        ]);
    }
}