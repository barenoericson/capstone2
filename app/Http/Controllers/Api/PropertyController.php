<?php

namespace App\Http\Controllers\Api;

use App\Models\Property;
use App\Models\PropertyPhoto;
use App\Models\PropertyPanorama;
use App\Models\Agent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PropertyController extends Controller
{
    /**
     * Get all properties with pagination
     */
    public function index(Request $request)
    {
        try {
            $properties = Property::with([
                'agent.user',
                'photos' => function ($q) {
                    $q->orderBy('is_cover', 'desc')
                      ->orderBy('sort_order', 'asc');
                }
            ])
            ->where('status', 'available')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

            return response()->json([
                'success' => true,
                'data' => $properties,
            ]);
        } catch (\Exception $e) {
            \Log::error('PropertyController@index error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load properties',
            ], 500);
        }
    }

    /**
     * ✅ GET SINGLE PROPERTY - THIS IS WHAT EDIT NEEDS
     */
    public function show($id)
    {
        try {
            // ✅ IMPORTANT: Eager load agent.user and photos
            $property = Property::with([
                'agent.user',
                'photos' => function ($q) {
                    $q->orderBy('is_cover', 'desc')
                      ->orderBy('sort_order', 'asc');
                },
                'panoramas' => function ($q) {
                    $q->orderBy('sort_order', 'asc');
                },
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $property,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('PropertyController@show error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load property',
            ], 500);
        }
    }

    /**
     * Get agent's properties
     */
    public function agentProperties(Request $request)
    {
        try {
            // Check if user has agent profile
            $agent = auth()->user()->agent;
            if (!$agent) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not an agent',
                ], 403);
            }

            $query = Property::query()
                ->with([
                    'agent.user',
                    'photos' => function ($q) {
                        $q->orderBy('is_cover', 'desc')
                          ->orderBy('sort_order', 'asc');
                    }
                ])
                ->where('agent_id', $agent->id)
                ->orderBy('created_at', 'desc');

            $properties = $query->paginate(12);

            return response()->json([
                'success' => true,
                'data' => $properties,
            ]);
        } catch (\Exception $e) {
            \Log::error('PropertyController@agentProperties error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load properties',
            ], 500);
        }
    }

    /**
     * Create new property
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string',
                'description' => 'nullable|string',
                'address' => 'required|string',
                'city' => 'required|string',
                'province' => 'required|string',
                'postal_code' => 'nullable|string',
                'country' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'bedrooms' => 'required|integer|min:0',
                'bathrooms' => 'required|integer|min:0',
                'floor_area' => 'nullable|numeric',
                'lot_area' => 'nullable|numeric',
                'property_type' => 'required|string',
                'status' => 'nullable|string|in:available,sold',
            ]);

            $agent = auth()->user()->agent;
            if (!$agent) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not an agent',
                ], 403);
            }

            $validated['agent_id'] = $agent->id;
            $validated['status'] = $validated['status'] ?? 'available';

            $property = Property::create($validated);

            // AI property analysis — flag suspicious listings
            try {
                $gemini = app(\App\Services\GeminiService::class);
                $result = $gemini->analyzeProperty([
                    'title'         => $property->title,
                    'description'   => $property->description,
                    'price'         => $property->price,
                    'city'          => $property->city,
                    'province'      => $property->province,
                    'property_type' => $property->property_type,
                    'bedrooms'      => $property->bedrooms,
                    'bathrooms'     => $property->bathrooms,
                    'floor_area'    => $property->floor_area,
                    'lot_size'      => $property->lot_size,
                ]);

                if ($result['is_suspicious'] && $result['confidence'] >= 0.70) {
                    \App\Models\PropertyFlag::create([
                        'property_id'    => $property->id,
                        'flag_reason'    => $result['flag_reason'],
                        'ai_raw_response' => $result['raw_response'],
                        'ai_confidence'  => $result['confidence'] * 100,
                        'status'         => \App\Models\PropertyFlag::STATUS_PENDING,
                        'flagged_at'     => now(),
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('AI property analysis failed silently: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Property created successfully',
                'data' => $property,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('PropertyController@store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create property',
            ], 500);
        }
    }

    /**
     * Update property
     */
    public function update(Request $request, $id)
    {
        try {
            $property = Property::findOrFail($id);

            // Check authorization
            $agent = auth()->user()->agent;
            if (!$agent || $property->agent_id !== $agent->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $validated = $request->validate([
                'title' => 'required|string',
                'description' => 'nullable|string',
                'address' => 'required|string',
                'city' => 'required|string',
                'province' => 'required|string',
                'postal_code' => 'nullable|string',
                'country' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'bedrooms' => 'required|integer|min:0',
                'bathrooms' => 'required|integer|min:0',
                'floor_area' => 'nullable|numeric',
                'lot_area' => 'nullable|numeric',
                'property_type' => 'required|string',
                'status' => 'nullable|string|in:available,sold',
            ]);

            $property->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Property updated successfully',
                'data' => $property,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found',
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('PropertyController@update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update property',
            ], 500);
        }
    }

    /**
     * Delete property
     */
    public function destroy($id)
    {
        try {
            $property = Property::findOrFail($id);

            // Check authorization
            $agent = auth()->user()->agent;
            if (!$agent || $property->agent_id !== $agent->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $property->delete();

            return response()->json([
                'success' => true,
                'message' => 'Property deleted successfully',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('PropertyController@destroy error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete property',
            ], 500);
        }
    }

    /**
     * Update property status only
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $property = Property::findOrFail($id);

            $agent = auth()->user()->agent;
            if (!$agent || $property->agent_id !== $agent->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $validated = $request->validate([
                'status' => 'required|string|in:available,sold',
            ]);

            $property->update(['status' => $validated['status']]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'data' => $property,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found',
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('PropertyController@updateStatus error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status',
            ], 500);
        }
    }

    /**
     * ✅ UPLOAD PHOTOS FOR A PROPERTY
     */
    public function uploadPhotos(Request $request, $id)
    {
        try {
            $property = Property::findOrFail($id);
            $agent = auth()->user()->agent;

            // Check authorization
            if (!$agent || $property->agent_id !== $agent->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            // Validate photos
            $validated = $request->validate([
                'photos.*' => 'required|image|max:10240', // Max 10MB
            ]);

            $uploadedPhotos = [];

            if ($request->hasFile('photos')) {
                $sortOrder = $property->photos()->max('sort_order') ?? 0;

                foreach ($request->file('photos') as $photo) {
                    try {
                        // Store photo in storage/app/public/properties/{id}/
                        $path = $photo->store('properties/' . $id, 'public');
                        $photoUrl = \Storage::url($path);

                        // Create photo record using PropertyPhoto model
                        $photoRecord = PropertyPhoto::create([
                            'property_id' => $property->id,
                            'photo_path' => $path,
                            'photo_url' => $photoUrl,
                            'is_cover' => false,
                            'approval_status' => 'approved',
                            'sort_order' => ++$sortOrder,
                        ]);

                        $uploadedPhotos[] = $photoRecord;
                    } catch (\Exception $e) {
                        \Log::error('Error uploading individual photo: ' . $e->getMessage());
                        continue;
                    }
                }
            }

            if (empty($uploadedPhotos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No photos uploaded',
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => count($uploadedPhotos) . ' photo(s) uploaded successfully',
                'data' => $uploadedPhotos,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found',
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('PropertyController@uploadPhotos error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload photos',
            ], 500);
        }
    }

    /**
     * ✅ DELETE A PHOTO
     */
    public function deletePhoto($photoId)
    {
        try {
            $photo = PropertyPhoto::findOrFail($photoId);
            $property = $photo->property;
            $agent = auth()->user()->agent;

            // Check authorization
            if (!$agent || $property->agent_id !== $agent->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            // Delete file from storage
            if ($photo->photo_path && \Storage::disk('public')->exists($photo->photo_path)) {
                \Storage::disk('public')->delete($photo->photo_path);
            }

            // Delete database record
            $photo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Photo deleted successfully',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Photo not found',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('PropertyController@deletePhoto error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete photo',
            ], 500);
        }
    }

    /**
     * ✅ SET COVER PHOTO
     */
    public function setCoverPhoto($photoId)
    {
        try {
            $photo = PropertyPhoto::findOrFail($photoId);
            $property = $photo->property;
            $agent = auth()->user()->agent;

            // Check authorization
            if (!$agent || $property->agent_id !== $agent->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            // Remove cover from all other photos in this property
            PropertyPhoto::where('property_id', $property->id)
                ->where('id', '!=', $photo->id)
                ->update(['is_cover' => false]);

            // Set this photo as cover
            $photo->update(['is_cover' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Cover photo updated successfully',
                'data' => $photo,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Photo not found',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('PropertyController@setCoverPhoto error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cover photo',
            ], 500);
        }
    }

    /**
     * Increment view count
     */
    public function incrementViews($id)
    {
        try {
            $property = Property::findOrFail($id);
            $property->increment('view_count');

            return response()->json([
                'success' => true,
                'message' => 'View count updated',
            ]);
        } catch (\Exception $e) {
            \Log::error('PropertyController@incrementViews error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update view count',
            ], 500);
        }
    }

    /**
     * Get saved properties for a buyer
     */
    public function getSavedProperties(Request $request)
    {
        try {
            $user = auth()->user();
            $savedProperties = $user->savedProperties()
                ->with([
                    'agent.user',
                    'photos' => function ($q) {
                        $q->orderBy('is_cover', 'desc')
                          ->orderBy('sort_order', 'asc');
                    }
                ])
                ->paginate(12);

            return response()->json([
                'success' => true,
                'data' => $savedProperties,
            ]);
        } catch (\Exception $e) {
            \Log::error('PropertyController@getSavedProperties error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load saved properties',
            ], 500);
        }
    }

    /**
     * Save a property for a buyer
     */
    public function saveProperty($id)
    {
        try {
            $property = Property::findOrFail($id);
            $user = auth()->user();

            // Toggle save (if already saved, unsave it)
            $user->savedProperties()->toggle($id);

            return response()->json([
                'success' => true,
                'message' => 'Property saved',
            ]);
        } catch (\Exception $e) {
            \Log::error('PropertyController@saveProperty error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save property',
            ], 500);
        }
    }

    // ============================================================
    // 360° PANORAMA METHODS
    // ============================================================

    /**
     * Upload 360° panoramic images for a property
     */
    public function uploadPanoramas(Request $request, $id)
    {
        try {
            $property = Property::findOrFail($id);
            $agent = auth()->user()->agent;

            if (!$agent || $property->agent_id !== $agent->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $request->validate([
                'panoramas'   => 'required|array|min:1',
                'panoramas.*' => 'required|image|max:20480',
                'room_names'  => 'required|array|min:1',
                'room_names.*' => 'required|string|max:100',
            ]);

            $uploadedPanoramas = [];
            $sortOrder = $property->panoramas()->max('sort_order') ?? 0;
            $roomNames = $request->input('room_names', []);

            foreach ($request->file('panoramas') as $index => $file) {
                try {
                    $path = $file->store('properties/' . $id . '/panoramas', 'public');
                    $imageUrl = \Storage::url($path);
                    $roomName = $roomNames[$index] ?? 'Room ' . ($sortOrder + 1);

                    $panorama = PropertyPanorama::create([
                        'property_id' => $property->id,
                        'image_path'  => $path,
                        'image_url'   => $imageUrl,
                        'room_name'   => $roomName,
                        'sort_order'  => ++$sortOrder,
                    ]);

                    $uploadedPanoramas[] = $panorama;
                } catch (\Exception $e) {
                    \Log::error('Error uploading panorama: ' . $e->getMessage());
                    continue;
                }
            }

            if (empty($uploadedPanoramas)) {
                return response()->json(['success' => false, 'message' => 'No panoramas uploaded'], 400);
            }

            $this->generateAutoHotspots($property);

            return response()->json([
                'success' => true,
                'message' => count($uploadedPanoramas) . ' panorama(s) uploaded successfully',
                'data'    => $uploadedPanoramas,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('PropertyController@uploadPanoramas error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload panoramas'], 500);
        }
    }

    /**
     * Delete a 360° panoramic image
     */
    public function deletePanorama($panoramaId)
    {
        try {
            $panorama = PropertyPanorama::findOrFail($panoramaId);
            $property = $panorama->property;
            $agent = auth()->user()->agent;

            if (!$agent || $property->agent_id !== $agent->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            if ($panorama->image_path && \Storage::disk('public')->exists($panorama->image_path)) {
                \Storage::disk('public')->delete($panorama->image_path);
            }

            $panorama->delete();

            $this->generateAutoHotspots($property);

            return response()->json(['success' => true, 'message' => 'Panorama deleted successfully']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Panorama not found'], 404);
        } catch (\Exception $e) {
            \Log::error('PropertyController@deletePanorama error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete panorama'], 500);
        }
    }

    /**
     * Auto-generate hotspots linking adjacent panoramas
     */
    private function generateAutoHotspots(Property $property): void
    {
        $panoramas = $property->panoramas()->orderBy('sort_order')->get();

        if ($panoramas->count() < 2) {
            foreach ($panoramas as $p) {
                $p->update(['hotspots' => null]);
            }
            return;
        }

        foreach ($panoramas as $index => $panorama) {
            $hotspots = [];

            if ($index > 0) {
                $prev = $panoramas[$index - 1];
                $hotspots[] = [
                    'id'      => 'hs-prev-' . $prev->id,
                    'pitch'   => -5,
                    'yaw'     => -90,
                    'type'    => 'scene',
                    'text'    => $prev->room_name,
                    'sceneId' => 'scene-' . $prev->id,
                ];
            }

            if ($index < $panoramas->count() - 1) {
                $next = $panoramas[$index + 1];
                $hotspots[] = [
                    'id'      => 'hs-next-' . $next->id,
                    'pitch'   => -5,
                    'yaw'     => 90,
                    'type'    => 'scene',
                    'text'    => $next->room_name,
                    'sceneId' => 'scene-' . $next->id,
                ];
            }

            $panorama->update(['hotspots' => $hotspots]);
        }
    }
}